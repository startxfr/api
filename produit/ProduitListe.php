<?php
/*#########################################################################
#
#   name :       ListeDevis.php
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
loadPlugin(array('ZunoCore','ZView/ProduitView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('produit');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('produit', 05, 'web');
if($PC->rcvP['action'] == 'searchProduit') {
    if($PC->rcvP['id_prod'] != '')
	$data['id_prod'] = $PC->rcvP['id_prod'];
    if($PC->rcvP['nom_prod'] != '')
	$data['nom_prod'] = $PC->rcvP['nom_prod'];
    if($PC->rcvP['famille_prod'] != '')
	$data['famille_prod'] = $PC->rcvP['famille_prod'];
    if($PC->rcvP['fournisseur_prod'] != '')
	$data['fournisseur_id'] = $PC->rcvP['fournisseur_prod'];
    if($PC->rcvP['stillAvailable_prod'] != '')
	$data['stillAvailable_prod'] = $PC->rcvP['stillAvailable_prod'];

    if($PC->rcvP['pxmin_prod'] != '')
	$data['pxmin_prod'] = $PC->rcvP['pxmin_prod'];
    if($PC->rcvP['pxmax_prod'] != '')
	$data['pxmax_prod'] = $PC->rcvP['pxmax_prod'];
    // Ajout d'un hook pour SXA
    sxaFilterVar4SearchProduit($PC->rcvP,$data);
    if($PC->rcvP['ordre_prod'] != '')
	$ordre = 'ORDER BY '.$PC->rcvP['ordre_prod'];
    if($PC->rcvP['limit'] != '')
	$datas['limit'] = $PC->rcvP['limit'];
    else
	$datas['limit'] = '30';
    if($PC->rcvP['from'] != '')
	$datas['from'] = $PC->rcvP['from'];
    else
	$datas['from'] = '0';
    $req = new produitModel();
    $result = $req->getDataForSearchProduitWeb('', $datas['from'], 'ALL', $ordre, $data);
    $datas['total'] = $result[1][0]['counter'];
    if($datas['total'] == '')
	$datas['total'] = '0';
    if($datas['limit'] == 'ALL') {
	$datas['limit'] = $datas['total'];
	$datas['from'] = 0;
    }
    $result = $req->getDataForSearchProduitWeb('', $datas['from'], $datas['limit'], $ordre, $data);
    $datas['data'] = $result[1];
    $view = new produitView();
    echo $view->searchResult($datas,$PC->rcvP['result']);
    exit;
}
elseif ($PC->rcvP['action'] == 'groupedAction') {
    $bddtmp = new ProduitModel();
    $list = $PC->rcvP['select'];
    $action = $PC->rcvP['groupedAction'];

    if (count($list) > 0) {
	$prodListString = '';
	foreach ($list as $k => $prod)
	    $prodListString .= ', \''.$prod.'\'';
	$prodListString = substr($prodListString,1);
    }

    if($action == 'desactivate') {
	$req = "SELECT produit.id_prod FROM produit
		WHERE id_prod IN (".$prodListString.")
		GROUP BY id_prod ORDER BY id_prod ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		produitModel::markDesactivateProduitInDB($prod['id_prod'],$PC->rcvP);
		$message.= "Produit ".$prod['id_prod']." marqué comme désactivé\n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' produits désactivés', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des produits séléctionnés ne peut être marqué comme désactivé</span>";
    }
    elseif($action == 'activate') {
	$req = "SELECT produit.id_prod FROM produit
		WHERE id_prod IN (".$prodListString.")
		GROUP BY id_prod ORDER BY id_prod ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		produitModel::markActivateProduitInDB($prod['id_prod'],$PC->rcvP);
		$message.= "Produit ".$prod['id_prod']." marqué comme activé\n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' produits activés', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des produits séléctionnés ne peut être marqué comme activé</span>";
    }
    elseif($action == 'fournisseur') {
	$req = "SELECT produit.id_prod FROM produit
		WHERE id_prod IN (".$prodListString.")
		GROUP BY id_prod ORDER BY id_prod ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		produitModel::changeFournisseurProduitProduitInDB($prod['id_prod'],$PC->rcvP['fournisseur'],$PC->rcvP);
		$message.= "Modification des informations du produit ".$prod['id_prod']." fourni par le fournisseur ".$PC->rcvP['fournisseur']."\n";
	    }
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des produits séléctionnés ne peut être marqué comme activé</span>";
    }
    elseif($action == 'delete') {
	$req = "SELECT produit.id_prod FROM produit
		LEFT JOIN devis_produit ON devis_produit.id_produit = produit.id_prod
		LEFT JOIN commande_produit ON commande_produit.id_produit = produit.id_prod
		LEFT JOIN facture_produit ON facture_produit.id_produit = produit.id_prod
		WHERE id_prod IN (".$prodListString.")
		AND devis_produit.id_produit IS NULL
		AND commande_produit.id_produit IS NULL
		AND facture_produit.id_produit IS NULL
		GROUP BY id_prod ORDER BY id_prod ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		produitModel::markDeleteProduitInDB($prod['id_prod'],$PC->rcvP);
		$message.= "Produit ".$prod['id_prod']." supprimé\n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' produits supprimés', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des produits séléctionnés ne peut être supprimé</span>";
    }
    elseif($action == 'changeAttribute') {
	$req = "SELECT produit.id_prod FROM produit
		WHERE id_prod IN (".$prodListString.")
		GROUP BY id_prod ORDER BY id_prod ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		produitModel::changeAttributeProduitInDB($prod['id_prod'],$PC->rcvP);
		$message.= "changement des attributs du produit ".$prod['id_prod']."\n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' produits modifiés', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des produits séléctionnés ne peuvent être modifié</span>";
    }
    $message = $message.'<script type="text/javascript">setTimeout("window.location.href = \'ProduitListe.php\';",1000);</script>';
}
else {
    if($PC->rcvG['fourn'] != '') {
	$data['fournisseur_id'] = $PC->rcvG['fourn'];
	$data['stillAvailable_prod'] = 1;
    }
    else
	$data['stillAvailable_prod'] = 1;
    $req = new produitModel();
    $total = $req->getDataForSearchProduitWeb('', '0', 'ALL', 'ORDER BY nom_prod', $data);
    $total = $total[1][0]['counter'];
    $datas['total'] = $total;
    $result = $req->getDataForSearchProduitWeb('', '0', '30', 'ORDER BY nom_prod', $data);
    $result = $result[1];
    $datas['data'] = $result;
    $datas['form']['fournisseur_prod'] = $data['fournisseur_id'];
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $datas['ordre']['nom_prod'] = 'Par nom';
    $datas['ordre']['famille_prod'] = 'Par famille';
    $datas['ordre']['prix_prod'] = 'Par prix';
    $datas['ordre']['stock_prod'] = 'Par stock';
    $datas['famille'] = $req->getAllFamille();
    $temp = $req->getAllFournisseur();
    if($temp[0] and is_array($temp[1]))
	foreach($temp[1] as $v) {
	    $datas['fournisseur'][$v['id_fourn']] = $v['id_fourn'].' - '.$v['nom_ent'].' ('.$v['cp_ent'].')';
	}
    $view = new produitView();
    $sortie = $view->searchResult($datas, '');
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>
