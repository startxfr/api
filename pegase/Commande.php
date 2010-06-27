<?php
/*#########################################################################
#
#   name :       page.php
#   desc :       Display page content
#   categorie :  page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore', 'ZView/CommandeView', 'Send/Send', 'ZModels/ActualiteModel'));
loadPlugin(array('ZControl/GeneralControl'));

// Whe get the page context
$PC = new PageContext('pegase');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
if($PC->rcvG['action'] == 'actuCommande') {
    aiJeLeDroit('actualite', 10, 'web');
    $sql = new actualiteModel();
    $result = $sql->getData4Commande($PC->rcvG['id_cmd']);
    $view = new commandeView();
    echo $view->popupActu($result[1]);
    exit;
}
elseif($PC->rcvG['id_cmd'] != '') {
    $sortie = viewFiche($PC->rcvG['id_cmd'], 'commande', '', 'non', 'web', true);
}
elseif($PC->rcvG['id_commande'] != '' and $PC->rcvG['action'] == 'supp') {
    aiJeLeDroit('commande', 30, 'web');
	$titre = "Suppression de la commande ".$PC->rcvG['id_commande'];
	$corps 	= '<span class="importantblue">Confirmer la suppression</span>';
	$pied 	= '<a href="javascript:zuno.popup.close();">'.imageTag('../img/prospec/cancel.png','Effacer','middle').' Annuler</a>
		   <a href="../pegase/Commande.php?action=suppconfirm&id_commande='.$PC->rcvG['id_commande'].'">'.imageTag('../img/prospec/confirm.png','Effacer','middle').'Confirmer</a>';
	echo generateZBox($titre, $titre, $corps,$pied,'CommandeBox','');
}
elseif ($PC->rcvG['id_commande'] != '' and $PC->rcvG['action'] == 'suppconfirm') {
    $bddtmp = new CommandeModel();
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$PC->rcvG['id_commande'],array("status_cmd"=>"2"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'delete');
    $out = new PageDisplayHeader();
    echo $out->Process();
    echo "<script language=\"javascript\">location.href= 'CommandeListe.php';</script>";
    exit;
}
elseif ($PC->rcvG['action'] == 'VoirBDCC' or
	$PC->rcvG['action'] == 'VoirBDCF' or
	$PC->rcvG['action'] == 'VoirBDL' or
	$PC->rcvG['action'] == 'VoirRI') {
    $bddtmp->makeRequeteSelect('affaire','id_aff',substr($PC->rcvG['id_commande'],0,-5));
    $aff = $bddtmp->process();
    $aff = $aff[0];
    $PathTo  = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$aff['dir_aff'];
    if ($PC->rcvG['action'] == 'VoirBDCC')		$Doc = $PC->rcvG['id_commande'].'C.pdf';
    elseif ($PC->rcvG['action'] == 'VoirBDCF')		$Doc = $PC->rcvG['id_commande'].'F-'.$PC->rcvG['fourn'].'.pdf';
    elseif ($PC->rcvG['action'] == 'VoirBDL')		$Doc = substr($PC->rcvG['id_commande'],0,-1).'L.pdf';
    elseif ($PC->rcvG['action'] == 'VoirRI')		$Doc = "RapportIntervention.".$PC->rcvG['id_commande'].'.pdf';
    PushFileToBrowser($PathTo.$Doc,$Doc);
}
elseif($PC->rcvP['action'] == 'addCmd') {
    aiJeLeDroit('commande', 20, 'web');
    $sql = new commandeModel();
    $view = new commandeView();
    $dev = $sql->getDataFromDevis($PC->rcvP['id_dev']);
    $dev = $dev[1][0];
    $FHT = 0;
    foreach($PC->rcvP['id_produit'] as $k => $v) {
        if($PC->rcvP['quantite_cmd'][$k] == 0 or $PC->rcvP['quantite_cmd'][0] == '' or $PC->rcvP['fournisseur'][$k] == '')
            $PC->rcvP['fournisseur'][$k] = null;
        $produit[$k]['id_produit'] = $v;
        $produit[$k]['desc'] = $PC->rcvP['desc'][$k];
        $produit[$k]['fournisseur'] = $PC->rcvP['fournisseur'][$k];
        $produit[$k]['quantite'] = prepareNombreTraitement($PC->rcvP['quantite'][$k]);
        $produit[$k]['quantite_cmd'] = ($PC->rcvP['quantite_cmd'][$k]) ? prepareNombreTraitement($PC->rcvP['quantite_cmd'][$k]) : 0;
        $produit[$k]['remise'] = prepareNombreTraitement($PC->rcvP['remise'][$k]);
        $produit[$k]['remiseF'] = ($PC->rcvP['remiseF'][$k]) ? prepareNombreTraitement($PC->rcvP['remiseF'][$k]) : 0;
        $produit[$k]['prix'] = prepareNombreTraitement($PC->rcvP['prix'][$k]);
        $produit[$k]['prixF'] = ($PC->rcvP['prixF'][$k] != '') ? prepareNombreTraitement($PC->rcvP['prixF'][$k]) : 0;
        $FHT += $produit[$k]['prixF']*$produit[$k]['quantite_cmd']*(1-$produit[$k]['remiseF']/100);
    }
    $data['id_cmd'] = $PC->rcvP['id_dev'].'BC';
    $data['devis_cmd'] = $PC->rcvP['id_dev'];
    $data['titre_cmd'] = ($PC->rcvP['titre_cmd'] != '') ? $PC->rcvP['titre_cmd'] : $dev['titre_dev'];
    $data['commercial_cmd'] = $_SESSION['user']['id'];
    $data['sommeHT_cmd'] = $dev['sommeHT_dev'];
    $data['sommeFHT_cmd'] = $FHT;
    $data['modereglement_cmd'] = $PC->rcvP['modereglement_cmd'];
    $data['condireglement_cmd'] = $PC->rcvP['condireglement_cmd'];
    $data['BDCclient_cmd'] = ($PC->rcvP['BDCclient_cmd'] != '') ? $PC->rcvP['BDCclient_cmd'] : $dev['BDCclient_dev'];
    $data['entreprise_cmd'] = $dev['entreprise_dev'];
    $data['contact_cmd'] = $dev['contact_dev'];
    $data['contact_achat_cmd'] = $dev['contact_achat_dev'];
    $data['nomdelivery_cmd'] = $dev['nomdelivery_dev'];
    $data['adressedelivery_cmd'] = $dev['adressedelivery_dev'];
    $data['adresse1delivery_cmd'] = $dev['adresse1delivery_dev'];
    $data['villedelivery_cmd'] = $dev['villedelivery_dev'];
    $data['cpdelivery_cmd'] = $dev['cpdelivery_dev'];
    $data['paysdelivery_cmd'] = $dev['paysdelivery_dev'];
    $data['maildelivery_cmd'] = $dev['maildelivery_dev'];
    $data['complementdelivery_cmd'] = $dev['complementdelivery_dev'];
    $data['tva_cmd'] = $dev['tva_dev'];

    $result = $sql->insert($data, 'cloner', $produit);
    if($result[0]) {
        $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);        
        $bddtmp->makeRequeteUpdate('devis', 'id_dev', $data['devis_cmd'], array('status_dev' => '6'));
        $bddtmp->process();
        echo $view->popupCommande($PC->rcvP, 'ok', '<img src="../img/ajax-loader.gif" alt="loading" onload="redirectCommande(\''.$data['id_cmd'].'\');"');
        exit;
    }
    echo $view->popupCommande($PC->rcvP, 'erreurCmd', 'Erreur insertion '.$result[1]);
    exit;
}
elseif($PC->rcvG['action'] == 'addContCmd') {
    aiJeLeDroit('contact', 20, 'web');
    loadPlugin(array('ZView/ContactView'));
    $view = new contactView();
    $data['entreprise_cont'] = $PC->rcvG['entreprise'];
    $data['idRetour'] = $PC->rcvG['idRetour'];
    $data['idChamp'] = $PC->rcvG['idChamp'];
    $data['from'] = 'commande';
    echo $view->popupCont($data);
    exit;
}
elseif($PC->rcvP['action'] == 'modifCmd') {
    aiJeLeDroit('commande', 15, 'web');
    $sql = new commandeModel();
    $result = $sql->update($PC->rcvP, $PC->rcvP['id_cmd']);
    echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'interneInfos', 'non', 'web', true, 'Sauvegardé');
    exit;
}
elseif($PC->rcvP['action'] == 'modifProd') {
    aiJeLeDroit('commande', 15, 'web');
    $data['id_commande'] = $PC->rcvP['id_cmd'];
    $data['id'] = $PC->rcvP['enmodif'];
    $data['prixF'] = (is_numeric(prepareNombreTraitement($PC->rcvP['prixF'][$PC->rcvP['enmodif']]))) ? prepareNombreTraitement($PC->rcvP['prixF'][$PC->rcvP['enmodif']]) : '0';
    $data['remiseF'] = (is_numeric(prepareNombreTraitement($PC->rcvP['remiseF'][$PC->rcvP['enmodif']]))) ? prepareNombreTraitement($PC->rcvP['remiseF'][$PC->rcvP['enmodif']]) : '0';
    $data['quantite_cmd'] = (is_numeric(prepareNombreTraitement($PC->rcvP['quantite_cmd'][$PC->rcvP['enmodif']]))) ? prepareNombreTraitement($PC->rcvP['quantite_cmd'][$PC->rcvP['enmodif']]) : '0';
    $data['quantite'] = (is_numeric(prepareNombreTraitement($PC->rcvP['quantite'][$PC->rcvP['enmodif']]))) ? prepareNombreTraitement($PC->rcvP['quantite'][$PC->rcvP['enmodif']]) : '0';
    $data['prix'] = (is_numeric(prepareNombreTraitement($PC->rcvP['prix'][$PC->rcvP['enmodif']]))) ? prepareNombreTraitement($PC->rcvP['prix'][$PC->rcvP['enmodif']]) : '0';
    $data['remise'] = (is_numeric(prepareNombreTraitement($PC->rcvP['remise'][$PC->rcvP['enmodif']]))) ? prepareNombreTraitement($PC->rcvP['remise'][$PC->rcvP['enmodif']]) : '0';

    $data['fournisseur'] = $PC->rcvP['fournisseur'][$PC->rcvP['enmodif']];
    $info = new commandeModel();
    $result=$info->updateProduits($data);
    if($result[0]) {
        echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'interneProduit', 'non', 'web', true, 'Le produit a été modifié');
        exit;
    }
}
elseif($PC->rcvP['action'] == 'Valid') {
    aiJeLeDroit('commande', 13, 'web');
    $bdd = new commandeModel();
    $data['status_cmd'] = '2';
    $data['BDCclient_cmd'] = $PC->rcvP['BDCclient_cmd'];
    $data['modereglement_cmd'] = $PC->rcvP['modereglement_cmd'];
    $data['condireglement_cmd'] = $PC->rcvP['condireglement_cmd'];
    $result = $bdd->update($data, $PC->rcvP['id_cmd']);
    echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'Traitement', 'non', 'web', true, 'Cette commande a été validée.');
    exit;
}
elseif($PC->rcvP['action'] == 'GenererBDCC') {
    aiJeLeDroit('commande', 62, 'web');
    $gnose = new commandeGnose();
    $bdd = new commandeModel();
    $result = $bdd->getDataFromID($PC->rcvP['id_cmd']);
    $datas['data'] = $result[1][0];
    $datas["data"]['tauxTVA_ent'] = $datas["data"]['tva_cmd'];
    $datas['pays'] =  $bdd->getPays();
    $datas['user'] = $bdd->getUser($PC->rcvP['id_cmd']);
    $datas['produit'] = $bdd->getAllFournisseursFromID($PC->rcvP['id_cmd']);
    $Doc = $gnose->CommandeGenerateBDC($datas);
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
}
elseif($PC->rcvP['action'] == 'GenererBDL') {
    aiJeLeDroit('commande', 62, 'web');
    $gnose = new commandeGnose();
    $bdd = new commandeModel();
    $result = $bdd->getDataFromID($PC->rcvP['id_cmd']);
    $datas['data'] = $result[1][0];
    $datas["data"]['tauxTVA_ent'] = $datas["data"]['tva_cmd'];
    $datas['pays'] =  $bdd->getPays();
    $datas['user'] = $bdd->getUser($PC->rcvP['id_cmd']);
    $datas['produit'] = $bdd->getAllFournisseursFromID($PC->rcvP['id_cmd']);
    $Doc = $gnose->CommandeGenerateBDC($datas, 'pdf', 'BDL');
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
}
elseif($PC->rcvP['action'] == 'GenererRI') {
    aiJeLeDroit('commande', 62, 'web');
    $gnose = new commandeGnose();
    $bdd = new commandeModel();
    $result = $bdd->getDataFromID($PC->rcvP['id_cmd']);
    $datas['data'] = $result[1][0];
    $datas["data"]['tauxTVA_ent'] = $datas["data"]['tva_cmd'];
    $datas['pays'] =  $bdd->getPays();
    $datas['user'] = $bdd->getUser($PC->rcvP['id_cmd']);
    $Doc = $gnose->CommandeGenerateBDC($datas, 'pdf', 'RI');
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
}
elseif(substr($PC->rcvP['action'],0,11) == 'GenererBDCF') {
    aiJeLeDroit('commande', 62, 'web');
    $fourn = substr($PC->rcvP['action'], 12,4);
    $gnose = new commandeGnose();
    $bdd = new commandeModel();
    $result = $bdd->getDataFromID($PC->rcvP['id_cmd']);
    $datas['data'] = $result[1][0];
    $datas["data"]['tauxTVA_ent'] = $datas["data"]['tva_cmd'];
    $datas['pays'] =  $bdd->getPays();
    $datas['user'] = $bdd->getUser($PC->rcvP['id_cmd']);
    $bdd = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bdd->makeRequeteFree("SELECT * FROM fournisseur f LEFT JOIN entreprise e ON e.id_ent = f.entreprise_fourn LEFT JOIN contact c ON c.id_cont=f.contactComm_fourn where id_fourn = '".$fourn."'");
    $datas['fournisseur'] = $bdd->process2();
    $datas['fournisseur'] = $datas['fournisseur'][1][0];
    $bdd->makeRequeteFree("Select * from commande_produit cp left join produit p ON p.id_prod = cp.id_produit where cp.fournisseur = '".$fourn."' AND cp.id_commande = '".$PC->rcvP['id_cmd']."'");
    $datas['produit'] = $bdd->process2();
    $datas['produit'] = $datas['produit'][1];
    $Doc = $gnose->CommandeGenerateBDCF($datas);
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
}
elseif(($PC->rcvP['action'] == 'GenererDocs') or($PC->rcvP['action'] == 'RecordSend') or ($PC->rcvP['action'] == 'Send')) {
    aiJeLeDroit('commande', 62, 'web');
    $bdd = new commandeModel();
    if($PC->rcvP['action'] == 'GenererDocs' or $PC->rcvP['action'] == 'RecordSend') {
        aiJeLeDroit('commande', 60, 'web');
        $gnose = new commandeGnose();
	$datas = $bdd->getFullDataFromID($PC->rcvP['id_cmd']);
        $bdd->makeRequeteFree("Select fournisseur from commande_produit where id_commande = '".$PC->rcvP['id_cmd']."'");
        $fourn = $bdd->process2();
        $statusCmd = $datas['data']['status_cmd'];
        $datas['pays'] =  $bdd->getPays();
        $datas['user'] = $bdd->getUser($PC->rcvP['id_cmd']);

	if(is_array($fourn[1])) {
            foreach($fourn[1] as $v) {
                if(is_null($v['fournisseur']))
                    continue;
                elseif(array_key_exists('BCF'.$v['fournisseur'], $PC->rcvP)) {
                    $bdd->makeRequeteFree("SELECT * FROM fournisseur f LEFT JOIN entreprise e ON e.id_ent = f.entreprise_fourn LEFT JOIN contact c ON c.id_cont=f.contactComm_fourn where id_fourn = '".$v['fournisseur']."'");
                    $datas['fournisseur'] = $bdd->process2();
                    $datas['fournisseur'] = $datas['fournisseur'][1][0];
                    $bdd->makeRequeteFree("Select * from commande_produit cp left join produit p ON p.id_prod = cp.id_produit where cp.fournisseur = '".$v['fournisseur']."' AND cp.id_commande = '".$PC->rcvP['id_cmd']."'");
                    $datas['produit'] = $bdd->process2();
                    $datas['produit'] = $datas['produit'][1];
                    $dooc = $gnose->CommandeGenerateBDCF($datas, $PC->rcvP['OutputExt']);
                    if(is_string($dooc))
                        $Doc[] = $dooc;
                }
            }
        }
        $datas['produit'] = $bdd->getAllFournisseursFromID($PC->rcvP['id_cmd']);
        if(array_key_exists('GetDocBDC', $PC->rcvP)) {
            $ddd = $gnose->CommandeGenerateBDC($datas, $PC->rcvP['OutputExt']);
            if($ddd != false)
		$Doc[] = $ddd;
        }
        if(array_key_exists('GetDocBDL', $PC->rcvP)) {
            $ddd = $gnose->CommandeGenerateBDC($datas, $PC->rcvP['OutputExt'], 'BDL');
            if($ddd != false)
		$Doc[] = $ddd;
        }
        if(array_key_exists('GetDocRI', $PC->rcvP)) {
            $ddd = $gnose->CommandeGenerateBDC($datas, $PC->rcvP['OutputExt'], 'RI');
            if($ddd != false)
		$Doc[] = $ddd;
        }
	if(count($Doc) > 0) {
	    $result = $gnose->CommandeSaveDocInGnose($Doc,$datas['data']);
	    if($result !== true) {
		echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'Traitement', 'non', 'web', true, $result);
		exit;
	    }
	}
	else {
	    echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'Traitement', 'non', 'web', true, 'Impossible de générer le document.');
	    exit;
	}
    }
    if($PC->rcvP['action'] == 'Send') {
        foreach($PC->rcvP as $k => $v) {
            if($k == 'GetDocBDC')
                $Doc[] = 'BC.'.substr($PC->rcvP['id_cmd'],0,9).'.pdf';
            if($k == 'GetDocBDL')
                $Doc[] = 'BDL.'.substr($PC->rcvP['id_cmd'],0,9).'.pdf';
            if($k == 'GetDocRI')
                $Doc[] = 'RapportIntervention.'.substr($PC->rcvP['id_cmd'],0,9).'.pdf';
            if(substr($k,0,3) == 'BCF')
                $Doc[] = 'BCF.'.substr($PC->rcvP['id_cmd'],0,9).'-'.substr($k,3,4).'.pdf';
        }
    }

    if($PC->rcvP['action'] == 'GenererDocs') {
        if($statusCmd < 3)
            $result = $bdd->update(array('status_cmd' => '3'), $PC->rcvP['id_cmd']);
        
        echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'Traitement', 'non', 'web', true, 'Les documents ont été générés et sauvegardés.');
        exit;
    }
    elseif($PC->rcvP['action'] == 'RecordSend' or $PC->rcvP['action'] == 'Send') {
        if($statusCmd < 4)
            $result = $bdd->update(array('status_cmd' => '4'), $PC->rcvP['id_cmd']);
        $PC->rcvP['id'] = $PC->rcvP['id_cmd'];
        $PC->rcvP['partie'] = 'commande';
        $PC->rcvP['expediteur'] = $_SESSION['user']['id'];
        if($PC->rcvP['fichier'] != '' or $PC->rcvP['fichier'] != null)
            $Doc[] = $PC->rcvP['fichier'];
        $PC->rcvP['fichier'] = $Doc;
        $sender = new Sender($PC->rcvP);
        $result = $sender->send();
        if($result[0]) {
            $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
            $bddtmp->makeRequeteFree("SELECT id_cmd, entreprise_cmd, contact_cmd FROM commande WHERE  id_cmd = '".$PC->rcvP['id_cmd']."'");
            $lignes = $bddtmp->process();
            $dev = $lignes[0];
            switch($PC->rcvP['typeE']) {
                case 'fax' : $arrivee = " au numéro suivant : ".$PC->rcvP['fax'].".";
                    break;
                case 'courrier' : $arrivee = ".";
                    break;
                default : $arrivee = " à l'adresse suivante : ".$PC->rcvP['mail'].".";
            }           
            $bddtmp->makeRequeteUpdate('devis',"id_dev",substr($dev['id_cmd'],0,9),array("status_dev"=>"6"));
            $bddtmp->process();
            $bddtmp->makeRequeteUpdate('affaire',"id_aff",substr($dev['id_cmd'],0,6),array("status_aff"=>"9"));
            $bddtmp->process();
            $bddtmp->makeRequeteUpdate('commande',"id_cmd",$dev['id_cmd'],array("status_cmd"=>"4"));
            $bddtmp->process();
            echo $result[1];
        }
        else
            echo "Erreur lors de l'envoi";
        exit;
    }
}
elseif($PC->rcvP['action'] == 'VoirRI') {
    aiJeLeDroit('commande', 62, 'web');
    $Doc = 'RapportIntervention.'.substr($PC->rcvP['id_cmd'],0,9).'.'.$PC->rcvP['format'];
    PushFileToBrowser($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$PC->rcvP['dir_aff'].$Doc, $Doc);
}
elseif($PC->rcvP['action'] == 'VoirBC') {
    aiJeLeDroit('commande', 62, 'web');
    $Doc = 'BC.'.substr($PC->rcvP['id_cmd'],0,9).'.'.$PC->rcvP['format'];
    PushFileToBrowser($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$PC->rcvP['dir_aff'].$Doc, $Doc);
}
elseif($PC->rcvP['action'] == 'VoirBDL') {
    aiJeLeDroit('commande', 62, 'web');
    $Doc = 'BDL.'.substr($PC->rcvP['id_cmd'],0,9).'.'.$PC->rcvP['format'];
    PushFileToBrowser($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$PC->rcvP['dir_aff'].$Doc, $Doc);
}
elseif(substr($PC->rcvP['action'],0,7) == 'VoirBCF') {
    aiJeLeDroit('commande', 62, 'web');
    $Doc = 'BCF.'.substr($PC->rcvP['id_cmd'],0,9).'-'.substr($PC->rcvP['action'],7,4).'.'.$PC->rcvP['format'];
    PushFileToBrowser($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$PC->rcvP['dir_aff'].$Doc, $Doc);
}
elseif($PC->rcvP['action'] == "traitement") {
    aiJeLeDroit('commande', 13, 'web');
    $bdd = new commandeModel();
    $result = $bdd->update(array('status_cmd' => '6'), $PC->rcvP['id_cmd']);
    if($result[0]) {
        echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'Traitement', 'non', 'web', true, 'La commande a été traitée.');
    }
    else {
        echo "Problème lors du traitement de la commande.";
    }
    exit;
}
elseif($PC->rcvP['action'] == "expedie") {
    aiJeLeDroit('commande', 13, 'web');
    $bdd = new commandeModel();
    $result = $bdd->update(array('status_cmd' => '7'), $PC->rcvP['id_cmd']);
    if($result[0]) {
        echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'Traitement', 'non', 'web', true, 'La commande a été expédiée au client.');
    }
    else {
        echo "Problème lors de la modification du status.";
    }
    exit;
}
elseif($PC->rcvP['action'] == "reception") {
    aiJeLeDroit('commande', 13, 'web');
    $bdd = new commandeModel();
    $result = $bdd->update(array('status_cmd' => '8'), $PC->rcvP['id_cmd']);
    if($result[0]) {
        echo viewFiche($PC->rcvP['id_cmd'], 'commande', 'Traitement', 'non', 'web', true, 'La commande a été réceptionnée par le client.');
    }
    else {
        echo "Problème lors de la modification du status.";
    }
    exit;
}
elseif($PC->rcvG['action'] == "fact") {
    aiJeLeDroit('commande', 13, 'web');
    $view = new commandeView();
    echo $view->popupValidFacturation($PC->rcvG['id_commande']);
    exit;
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
