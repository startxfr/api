<?php
/*#########################################################################
#
#   name :       Recherche.php
#   desc :       Prospection list for sxprospec
#   categorie :  prospec page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZunoCore','ZView/ProspecView','ZView/ContactView'));

/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$message = '';
aiJeLeDroit('contact', 05, 'web');
if ($PC->rcvP['action'] == 'search') {
    $_SESSION['prospec']['search']['recherche'] = $PC->rcvP['recherche'];
    $_SESSION['prospec']['search']['type']	= $PC->rcvP['type'];
    $req = new contactEntrepriseModel();
    $cloud = new CloudModel();
    $tableau = 'classic';
    switch($PC->rcvP['type']) {
	case 'entreprise' :
	    $cloud->addToCloud($PC->rcvP['recherche'], 'entreprise');
	    $total = $req->getDataForSearchEntWeb($PC->rcvP['recherche'], 'ALL');
	    $total = $total[1][0]['counter'];
	    $PC->rcvP['limit'] = ($PC->rcvP['limit'] == 'ALL') ? $total : $PC->rcvP['limit'];
	    $result = $req->getDataForSearchEntWeb($PC->rcvP['recherche'], $PC->rcvP['limit'], $PC->rcvP['from'], $PC->rcvP['order'], $PC->rcvP['orderSens']);
	    $tableau = 'ent';
	    break;
	case 'contact' :
	    $cloud->addToCloud($PC->rcvP['recherche'], 'contact');
	    $total = $req->getDataForSearchContWeb($PC->rcvP['recherche'], 'ALL');
	    $total = $total[1][0]['counter'];
	    $PC->rcvP['limit'] = ($PC->rcvP['limit'] == 'ALL') ? $total : $PC->rcvP['limit'];
	    $result = $req->getDataForSearchContWeb($PC->rcvP['recherche'], $PC->rcvP['limit'], $PC->rcvP['from'], $PC->rcvP['order'], $PC->rcvP['orderSens']);
	    break;
	case 'appel' :
	    $total = $req->getDataForSearchAppWeb($PC->rcvP['recherche'], 'ALL');
	    $total = $total[1][0]['counter'];
	    $PC->rcvP['limit'] = ($PC->rcvP['limit'] == 'ALL') ? $total : $PC->rcvP['limit'];
	    $result = $req->getDataForSearchAppWeb($PC->rcvP['recherche'], $PC->rcvP['limit'], $PC->rcvP['from'], $PC->rcvP['order'], $PC->rcvP['orderSens']);
	    break;
	default:
	    $total = $req->getDataForSearchGlobal($PC->rcvP['recherche'], 'ALL','0', $PC->rcvP['order'], $PC->rcvP['orderSens']);
	    $total = $total[1][0]['counter'];
	    $PC->rcvP['limit'] = ($PC->rcvP['limit'] == 'ALL') ? $total : $PC->rcvP['limit'];
	    $result = $req->getDataForSearchGlobal($PC->rcvP['recherche'], $PC->rcvP['limit'], $PC->rcvP['from'], $PC->rcvP['order'], $PC->rcvP['orderSens']);
	    break;
    }

    $datas['total'] = $total;
    $datas['data'] = $result[1];
    $datas['from'] = $PC->rcvP['from'];
    $datas['limit'] = $PC->rcvP['limit'];
    $datas['order'] = $PC->rcvP['order'];
    $datas['orderSens'] = $PC->rcvP['orderSens'];
    $view = new contactView();
    echo $view->searchResult($datas,'result', $tableau);
    exit;
}
elseif($PC->rcvP['action'] == 'exportTableur') {
    $req = new contactParticulierModel();
    $result = $req->getDataForExportTableur($PC->rcvP['selectC']);
    $gnose = new contactGnose();
    $file = $gnose->ContactExportTableurConverter($result[1],$PC->rcvP['exportType']);
    PushFileToBrowser($file);
    exit;
}
elseif ($PC->rcvP['action'] == 'groupedAction') {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $action = $PC->rcvP['groupedAction'];

    if (is_array($PC->rcvP['selectC']) and count($PC->rcvP['selectC']) > 0) {
        $contactListString = '';
        foreach ($PC->rcvP['selectC'] as $k => $contact)
            $contactListString .= ', \''.$contact.'\'';
        $contactListString = substr($contactListString,1);
    }
    if (is_array($PC->rcvP['selectE']) and count($PC->rcvP['selectE']) > 0) {
        $entrepriseListString = '';
        foreach ($PC->rcvP['selectE'] as $k => $entreprise)
            $entrepriseListString .= ', \''.$entreprise.'\'';
        $entrepriseListString = substr($entrepriseListString,1);
    }

    if($action == 'changeAttributeC') {
	$req = "SELECT contact.id_cont, contact.nom_cont FROM contact
		WHERE id_cont IN (".$contactListString.")
		GROUP BY id_cont ORDER BY id_cont ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
        if(is_array($res) and count($res) > 0) {
            foreach($res as $k => $contact) {
                contactParticulierModel::changeAttributeInDB($contact['id_cont'],$PC->rcvP);
                $message.= "Contact ".$contact['nom_cont']." modifié\n";
            }
            $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
        }
        else $message = "<span class=\"importantblue\">Aucune des contacts séléctionnés ne peut être re-initialisé</span>";
    }
    elseif($action == 'changeAttributeE') {
	$req = "SELECT entreprise.id_ent, entreprise.nom_ent FROM entreprise
		WHERE id_ent IN (".$contactListString.")
		GROUP BY id_ent ORDER BY id_ent ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
        if(is_array($res) and count($res) > 0) {
            foreach($res as $k => $entreprise) {
                contactEntrepriseModel::changeAttributeInDB($entreprise['id_ent'],$PC->rcvP);
                $message.= "Entreprise ".$entreprise['nom_ent']." modifiée\n";
            }
            $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
        }
        else $message = "<span class=\"importantblue\">Aucune des entreprises séléctionnées ne peut être modifié</span>";
    }
    elseif($action == 'deleteC') {
	$req = "SELECT contact.id_cont, contact.nom_cont FROM contact
		LEFT JOIN affaire ON affaire.contact_aff = contact.id_cont
		LEFT JOIN devis ON (devis.contact_achat_dev = contact.id_cont OR devis.contact_dev = contact.id_cont)
		LEFT JOIN commande ON (commande.contact_achat_cmd = contact.id_cont OR commande.contact_cmd = contact.id_cont)
		LEFT JOIN facture ON (facture.contact_achat_fact = contact.id_cont OR facture.contact_fact = contact.id_cont)
		LEFT JOIN facture_fournisseur ON facture_fournisseur.contact_factfourn = contact.id_cont
		LEFT JOIN fournisseur ON (fournisseur.contactComm_fourn = contact.id_cont OR fournisseur.ContactADV_fourn = contact.id_cont OR fournisseur.contactFact_fourn = contact.id_cont)
		LEFT JOIN historique_payline ON historique_payline.contact_hp = contact.id_cont
		LEFT JOIN appel ON appel.contact_app = contact.id_cont
		WHERE id_cont IN (".$contactListString.")
		AND affaire.id_aff IS NULL
		AND devis.id_dev IS NULL
		AND commande.id_cmd IS NULL
		AND facture.id_fact IS NULL
		AND facture_fournisseur.id_factfourn IS NULL
		AND fournisseur.id_fourn IS NULL
		AND historique_payline.id_hp IS NULL
		AND appel.id_app IS NULL
		GROUP BY id_cont ORDER BY id_cont ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
        if(is_array($res) and count($res) > 0) {
            foreach($res as $k => $contact) {
                contactParticulierModel::deleteInDB($contact['id_cont'],$PC->rcvP);
                $message.= "Contact ".$contact['nom_cont']." supprimé \n";
            }
            $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
        }
        else $message = "<span class=\"importantblue\">Aucune des contacts séléctionnés ne peut être supprimé</span>";
    }
    elseif($action == 'deleteE') {
	$req = "SELECT entreprise.id_ent, entreprise.nom_ent FROM entreprise
		LEFT JOIN affaire ON affaire.entreprise_aff = entreprise.id_ent
		LEFT JOIN devis ON devis.entreprise_dev = entreprise.id_ent
		LEFT JOIN commande ON commande.entreprise_cmd = entreprise.id_ent
		LEFT JOIN facture ON facture.entreprise_fact = entreprise.id_ent
		LEFT JOIN facture_fournisseur ON facture_fournisseur.entreprise_factfourn = entreprise.id_ent
		LEFT JOIN fournisseur ON fournisseur.entreprise_fourn = entreprise.id_ent
		LEFT JOIN contact ON contact.entreprise_cont = entreprise.id_ent
		WHERE id_ent IN (".$entrepriseListString.")
		AND affaire.id_aff IS NULL
		AND devis.id_dev IS NULL
		AND commande.id_cmd IS NULL
		AND facture.id_fact IS NULL
		AND facture_fournisseur.id_factfourn IS NULL
		AND fournisseur.id_fourn IS NULL
		AND contact.id_cont IS NULL
		GROUP BY id_ent ORDER BY id_ent ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
        if(is_array($res) and count($res) > 0) {
            foreach($res as $k => $entreprise) {
                contactEntrepriseModel::deleteInDB($entreprise['id_ent'],$PC->rcvP);
                $message.= "Entreprise ".$entreprise['nom_ent']." supprimée \n";
            }
            $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
        }
        else $message = "<span class=\"importantblue\">Aucune des entreprises séléctionnées ne peut être supprimée</span>";
    }
    $message = $message.'<script type="text/javascript">setTimeout("window.location.href = \'Recherche.php\';",1000);</script>';
}
else {
    $req = new contactEntrepriseModel();
    $total = $req->getDataForSearchEntWeb('', 'ALL');
    $total = $total[1][0]['counter'];
    $datas['total'] = $total;
    $result = $req->getDataForSearchEntWeb('', '30');
    $result = $result[1];
    $datas['data'] = $result;
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $datas['type'] = 'entreprise';
    $view = new contactView();
    $sortie = $view->searchResult($datas, '', 'ent');
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>
