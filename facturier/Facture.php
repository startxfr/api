<?php
/*#########################################################################
#
#   name :       Facture.php
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
loadPlugin(array('ZunoCore','ZView/FactureView', 'Send/Send','ZModels/ActualiteModel', 'ZModels/RenouvellementModel', 'ZModels/TransactionModel', 'ZModels/PaylineModel'));
loadPlugin(array('ZControl/DevisControl', 'ZControl/FactureControl', 'ZControl/GeneralControl', 'Payline/Payline'));

// Whe get the page context
$PC = new PageContext('facturier');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$info = new factureModel();
if($PC->rcvG['id_fact'] != '') {
    $sortie = viewFiche($PC->rcvG['id_fact'], $info->getType($PC->rcvG['id_fact']), '', 'non', 'web', true);
}
elseif($PC->rcvG['action'] == 'cloner') {
    aiJeLeDroit($info->getType($PC->rcvG['fact']), 25, 'web');

    $result = $info->getDataFromID($PC->rcvG['fact']);
    $id = $info->getLastId();
    $id ++;
    $prod=$info->getProduitsFromID($PC->rcvG['fact']);
    $result[1][0]['id_fact'] = $id;
    $result[1][0]['status_fact'] = '1';
    unset($result[1][0]['ren_fact']);

    $resultat = $info->insert($result[1][0], 'cloner', $prod[1]);
    if($resultat[0]) {
        header('Location:Facture.php?id_fact='.$id);
        exit;
    }
}
elseif($PC->rcvP['action'] == 'avoirFromFact') {
    aiJeLeDroit('avoir', 20, 'web');
    $result = $info->getDataFromID($PC->rcvP['id_fact']);
    $id = $info->getLastId();
    $id ++;
    $prod=$info->getProduitsFromID($PC->rcvP['id_fact']);
    $result[1][0]['id_fact'] = $id;
    $result[1][0]['status_fact'] = '1';
    $result[1][0]['type_fact'] = 'Avoir';
    $result[1][0]['sommeHT_fact'] = prepareNombreTraitement((-1)*abs($result[1][0]['sommeHT_fact']));
    $resultat = $info->insert($result[1][0], 'toAvoir', $prod[1]);
    if($resultat[0]) {
        echo viewFiche($id, 'avoir', 'ficheComplete', 'non', 'web', true, 'L\'avoir a bien été enregistré');
        exit;
    }
    else {
        echo viewFiche($PC->rcvP['id_fact'], 'facture', 'ficheComplete', 'non', 'web', true, 'Un problème est survenu durant la génération de l\'avoir');
        exit;
    }
}
elseif($PC->rcvP['action'] == 'modifFacture') {
    $type = $info->getType($PC->rcvP['id_fact']);
    aiJeLeDroit($type, 15, 'web');

    if($PC->rcvP['actif_ren'] == 1) {
        if($PC->rcvP['ren_fact'] == "") {
            $ren['type_ren'] = 'facture';
            $ren['idChamp_ren'] = $PC->rcvP['id_fact'];
            $ren['actif_ren'] = 1;
            $ren['periode_ren'] = $PC->rcvP['periode_ren'];
            $ren['mail_ren'] = $PC->rcvP['mail_ren'];
            $ren['statusChamp_ren'] = $PC->rcvP['statusChamp_ren'];
            $ren['fin_ren'] = substr($PC->rcvP['fin_ren'], 6,4).substr($PC->rcvP['fin_ren'], 3,2).substr($PC->rcvP['fin_ren'],0,2);
            $renModel = new RenouvellementModel();
            $renModel->insert($ren);
            $PC->rcvP['ren_fact'] = $renModel->getLastId();
        }
        else {
            $ren['type_ren'] = 'facture';
            $ren['idChamp_ren'] = $PC->rcvP['id_fact'];
            $ren['actif_ren'] = 1;
            $ren['periode_ren'] = $PC->rcvP['periode_ren'];
            $ren['mail_ren'] = $PC->rcvP['mail_ren'];
            $ren['statusChamp_ren'] = $PC->rcvP['statusChamp_ren'];
            $ren['fin_ren'] = substr($PC->rcvP['fin_ren'], 6,4).substr($PC->rcvP['fin_ren'], 3,2).substr($PC->rcvP['fin_ren'],0,2);
            $renModel = new RenouvellementModel();
            $renModel->update($PC->rcvP['ren_fact'], $ren);
        }
    }
    else {
        if($PC->rcvP['ren_fact'] != "") {
            $renModel = new RenouvellementModel();
            $renModel->desactiver($PC->rcvP['ren_fact']);
        }
    }
    $result = $info->update($PC->rcvP, $PC->rcvP['id_fact']);
    echo viewFiche($PC->rcvP['id_fact'], $type, 'interneInfos', 'non', 'web', true, 'Sauvegardé');
    exit;
}
elseif($PC->rcvP['action'] == 'addProduit') {
    $type = $info->getType($PC->rcvP['id_facture']);
    aiJeLeDroit($type, 15, 'web');
    if($PC->rcvP['id_produit'] == '')
        $PC->rcvP['id_produit'] = $PC->rcvP['produit'];
    $control = produitControl::control($PC->rcvP);//Je controle les données
    if($control[0]) {
        $idp = array($PC->rcvP['id_produit']);
        $data['id_produit']=FileCleanFileName($idp[0], 'SVN_PROP');
        $data['quantite']=($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '') ? 1 : prepareNombreTraitement($PC->rcvP['quantite']);
        $data['remise']=($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '') ? 0 : prepareNombreTraitement($PC->rcvP['remise']);
        $data['id_facture']=$PC->rcvP['id_facture'];
        $temp=$info->getInfoProduits($data['id_produit']);
        $data['desc']=($PC->rcvP['desc'] == NULL || $PC->rcvP['desc'] == '') ? $temp[1][0]['nom_prod'] : $PC->rcvP['desc'];
        $data['prix']= ($PC->rcvP['prix'] == NULL || $PC->rcvP['prix'] == '') ? $temp[1][0]['prix_prod'] : prepareNombreTraitement($PC->rcvP['prix']);
        if($type == 'avoir')
            $data['prix'] = (-1)*abs($data['prix']);
        $result = $info->insertProduits($data);//Je fais l'insertion dans la BDD
        if($result[0]) {
            echo viewFiche($PC->rcvP['id_facture'], $type, 'interneProduit', 'non', 'web', true, 'Sauvegardé');
        }
        else {
            $test = ereg("Duplicate",$result[1],$erreur);
            if ($test == 9 ) {
                $mess = "Ce produit est déjà présent dans la facture en cours.";
            }
            else {
                $mess = "Une erreur est survenue";
            }
            echo viewFiche($PC->rcvP['id_facture'], $type, 'interneProduit', 'non', 'web', true, $mess);
        }
    }
    else {
        echo viewFiche($PC->rcvP['id_facture'], $type, 'interneProduit', 'non', 'web', true, $control[1]);
    }
    exit;
}
elseif($PC->rcvP['action'] == 'suppProduit') {
    $type = $info->getType($PC->rcvP['id_facture']);
    aiJeLeDroit($type, 15, 'web');
    $data['id_facture'] = $PC->rcvP['id_facture'];
    $data['id'] = $PC->rcvP['produitAModifier'];

    $result = $info->deleteProduits($data);//J'effectue la suppression du produit de la BDD.
    if($result[0]) {
        $result = $info->getProduitsFromID($PC->rcvP['id_facture']);
        $sommeHT = 0;
        foreach($result[1] as $v) {
            $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
        }
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont from facture f left join entreprise e on e.id_ent=f.entreprise_fact left join contact c on c.id_cont=f.contact_fact where id_fact='".$PC->rcvP['id_facture']."';");
        $infoprod=$sqlConn->process2();
        $sommeHT = formatCurencyDatabase($sommeHT);
        $sqlConn->makeRequeteFree("update facture set sommeHT_fact='".$sommeHT."' WHERE id_fact = '".$PC->rcvP['id_facture']."'");
        $temp = $sqlConn->process2();
    }
    echo viewFiche($PC->rcvP['id_facture'], $type, 'interneProduit', 'non', 'web', true, 'Le produit a été enlevé de la facture');
    exit;
}
elseif($PC->rcvP['action'] == 'modifProduit') {
    $type = $info->getType($PC->rcvP['id_facture']);
    aiJeLeDroit($type, 15, 'web');

    $data['id'] = $PC->rcvP['enmodif'];
    $data['id_produit']=FileCleanFileName($PC->rcvP['id_produit'], 'SVN_PROP');
    $data['quantite']=($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '' || !is_numeric(prepareNombreTraitement($PC->rcvP['quantite']))) ? 1 : prepareNombreTraitement($PC->rcvP['quantite']);
    $data['remise']=($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '' || !is_numeric(prepareNombreTraitement($PC->rcvP['remise']))) ? 0 : prepareNombreTraitement($PC->rcvP['remise']);
    $data['id_facture']=$PC->rcvP['id_facture'];
    $temp=$info->getInfoProduits($data['id_produit']);
    $data['desc']=($PC->rcvP['desc'] == NULL || $PC->rcvP['desc'] == '') ? $temp[1][0]['nom_prod'] : $PC->rcvP['desc'];
    $data['prix']= ($PC->rcvP['prix'] == NULL || $PC->rcvP['prix'] == '' || !is_numeric(prepareNombreTraitement($PC->rcvP['prix']))) ? $temp[1][0]['prix_prod'] : prepareNombreTraitement($PC->rcvP['prix']);
    if($type == 'avoir')
        $data['prix'] = (-1)*abs($data['prix']);
    $result=$info->updateProduits($data);
    if($result[0]) {
        $result = $info->getProduitsFromID($PC->rcvP['id_facture']);
        $sommeHT = 0;
        foreach($result[1] as $v) {
            $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
        }//On génère le total à entrer dans la BDD devis.
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont from facture f left join entreprise e on e.id_ent=f.entreprise_fact left join contact c on c.id_cont=f.contact_fact where id_fact='".$PC->rcvP['id_facture']."';");
        $infoprod=$sqlConn->process2();
        $sommeHT = formatCurencyDatabase($sommeHT);
        $sqlConn->makeRequeteFree("update facture set sommeHT_fact='".$sommeHT."' WHERE id_fact = '".$PC->rcvP['id_facture']."'");
        $temp = $sqlConn->process2();
        echo viewFiche($PC->rcvP['id_facture'], $type, 'interneProduit', 'non', 'web', true, 'Le produit a été modifié');
        exit;
    }
}
elseif (($PC->rcvP['action'] == "Voir") or ($PC->rcvP['action'] == "Record") or ($PC->rcvP['action'] == "RecordSend") or ($PC->rcvP['action'] == "Send")) {
    $type = $info->getType($PC->rcvP['id_fact']);
    aiJeLeDroit($type, 62, 'web');
    $gnose = new factureGnose();
    $id_fact = $PC->rcvP['id_fact'];
    if($PC->rcvP['action'] != 'Send') {
        $Doc = $gnose->FactureGenerateDocument($id_fact,$PC->rcvP['OutputExt'],$PC->rcvP['Cannevas']);
        if(!is_string($Doc)) {
            echo viewFiche($PC->rcvP['id_fact'], 'facture', 'interneTraitement', 'non', 'web', true, 'Impossible de générer le document.');
            exit;
        }
    }
    if ($PC->rcvP['action'] == 'Voir') {
        PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
    }
    if (($PC->rcvP['action'] == 'Record')or($PC->rcvP['action'] == 'RecordSend')) {
        aiJeLeDroit($type, 60, 'web');
        $PC->rcvP['MessageRecord'] = "Changement de la '.$type.' ".$id_fact." par ".$_SESSION['user']['id'];

        $dev = $info->getDataFromID($id_fact);
        $dev = $dev[1][0];

        $save = $gnose->FactureSaveDocInGnose($Doc,$PC->rcvP['id_fact'],$PC->rcvP['MessageRecord']);


        $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        if($dev['status_aff'] < 13 and $type == 'facture') {
            $inActualiteRec['status_aff'] = '13';
            $bddtmp->makeRequeteUpdate('affaire','id_aff',$dev['id_aff'],array('status_aff'=>$inActualiteRec['status_aff']));
            $bddtmp->process();
        }
        if($dev['status_dev'] < 3 and $type == 'facture') {
            $inActualiteRec['status_fact'] = '3';
            $info->update($inActualiteRec, $dev['id_fact']);
        }

        if($PC->rcvP['action'] == 'Record') {
            echo viewFiche($PC->rcvP['id_fact'], $type, 'interneTraitement', 'non', 'web', true, 'Enregistrée');
            exit;
        }

    }
    if (($PC->rcvP['action'] == 'RecordSend')or($PC->rcvP['action'] == 'Send')) {
        if($PC->rcvP['action'] == 'RecordSend') {
            $PC->rcvP['fichier'] = $Doc;
        }
        $PC->rcvP['id'] = $PC->rcvP['id_fact'];
        $PC->rcvP['partie'] = 'facture';
        $PC->rcvP['from'] = $_SESSION['user']['mail'];
        $PC->rcvP['expediteur'] = $_SESSION['user']['fullnom'];
        $send = new Sender($PC->rcvP);
        $result = $send->send($_SESSION['user']['mail']);
        if($result[0]) {
            $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
            $bddtmp->makeRequeteFree("SELECT * FROM facture WHERE  id_fact = '".$id_fact."'");
            $lignes = $bddtmp->process();
            $dev = $lignes[0];
            switch($PC->rcvP['typeE']) {
                case 'fax' : $arrivee = " au numéro suivant : ".$PC->rcvP['fax'].".";
                    break;
                case 'courrier' : $arrivee = ".";
                    break;
                default : $arrivee = " à l'adresse suivante : ".$PC->rcvP['mail'].".";
            }
            $bddtmp->makeRequeteUpdate('devis',"id_dev",substr($dev['commande_fact'],0,9),array("status_dev"=>"6"));
            $bddtmp->process();
            $bddtmp->makeRequeteUpdate('affaire',"id_aff",substr($dev['commande_fact'],0,6),array("status_aff"=>"15"));
            $bddtmp->process();
            $bddtmp->makeRequeteUpdate('commande',"id_cmd",$dev['commande_fact'],array("status_cmd"=>"9"));
            $bddtmp->process();
	    if($dev['status_fact'] <= 3) {
		$info->update(array("status_fact"=>"4"), $id_fact);
		$info->update(array("status_fact"=>"5"), $id_fact);
	    }
	    elseif($dev['status_fact'] == 4)
		$info->update(array("status_fact"=>"5"), $id_fact);
            echo viewFiche($PC->rcvP['id_fact'], $type, 'Traitement', 'non', 'web', true, 'Document envoyé');

        }
        else
            echo $result[1];
        exit;
    }
}
elseif($PC->rcvG['action'] == 'actuFacture') {
    aiJeLeDroit('actualite', 10, 'web');
    $sql = new actualiteModel();
    $result = $sql->getData4Facture($PC->rcvG['id_facture']);
    $view = new factureView();
    echo $view->popupActu($result[1]);
    exit;
}

elseif($PC->rcvP['action'] == "addFactFromCmd") {
    aiJeLeDroit('facture', 20, 'web');
    loadPlugin(array('ZModels/CommandeModel'));
    $id = $info->GetLastId();
    $id ++;
    $commande = new commandeModel();
    $aijecommande = $commande->getDataFromID($PC->rcvP['id_cmd']);
    $produit = $commande->getProduitsFromID($PC->rcvP['id_cmd']);
    $produit = $produit[1];
    $data['id_fact'] = $id;
    $data['commande_fact'] = $aijecommande[1][0]['id_cmd'];
    $data['titre_fact'] = $aijecommande[1][0]['titre_cmd'];
    $data['commercial_fact'] = $_SESSION['user']['id'];
    $data['sommeHT_fact'] = $aijecommande[1][0]['sommeHT_cmd'];
    $data['modereglement_fact'] = $aijecommande[1][0]['modereglement_cmd'];
    $data['condireglement_fact'] = $aijecommande[1][0]['condireglement_cmd'];
    $data['BDCclient_fact'] = $aijecommande[1][0]['BDCclient_cmd'];
    $data['entreprise_fact'] = $aijecommande[1][0]['entreprise_cmd'];
    $data['contact_fact'] = $aijecommande[1][0]['contact_cmd'];
    $data['contact_achat_fact'] = $aijecommande[1][0]['contact_achat_cmd'];
    $data['nomentreprise_fact'] = $aijecommande[1][0]['nomdelivery_cmd'];
    $data['add1_fact'] = $aijecommande[1][0]['adressedelivery_cmd'];
    $data['add2_fact'] = $aijecommande[1][0]['adresse1delivery_cmd'];
    $data['ville_fact'] = $aijecommande[1][0]['villedelivery_cmd'];
    $data['cp_fact'] = $aijecommande[1][0]['cpdelivery_cmd'];
    $data['pays_fact'] = $aijecommande[1][0]['paysdelivery_cmd'];
    $data['tauxTVA_fact'] = $aijecommande[1][0]['tva_cmd'];
    $data['commentaire_fact'] = $aijecommande[1][0]['commentaire_cmd'];
    $data['maildelivery_fact'] = $aijecommande[1][0]['maildelivery_cmd'];
    $data['complementdelivery_fact'] = $aijecommande[1][0]['complementdelivery_cmd'];
    $data['type_fact'] = 'Facture';
    $result = $info->insert($data, 'cloner', $produit);
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("UPDATE entreprise SET type_ent = '4' WHERE id_ent = ".$data['entreprise_fact']." AND type_ent < '4' ; ");
    $bddtmp->process2();
    if($result[0]) {
        $bddtmp->makeRequeteFree("UPDATE commande SET status_cmd = '9' WHERE id_cmd = '".$PC->rcvP['id_cmd']."' ; ");
        $result = $bddtmp->process2();
        $sortie = viewFiche($data['id_fact'], 'facture', '', 'non', 'web', true);
    }
    else {
        echo "Un problème est survenu, la facture n'a pas été créée.";
        exit;
    }
}
elseif($PC->rcvP['action'] == "Zieuter") {
    aiJeLeDroit('facture', 62, 'web');
    $Doc = 'Facture.'.$PC->rcvP["id_fact"].'.'.$PC->rcvP['format'];
    PushFileToBrowser($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].$Doc, $Doc);

}
elseif ($PC->rcvG['action'] == 'VoirFact') {
	$PathTo  = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'];
	$bddtmp->makeRequeteFree("SELECT file_fact FROM facture WHERE id_fact = '".$PC->rcvG['id_fact']."'");
	$facture = $bddtmp->process();
	$facture = $facture[0];
	PushFileToBrowser($PathTo.$facture['file_fact'],$facture['file_fact']);
}
elseif($PC->rcvG['action'] == 'addContFact') {
    aiJeLeDroit('contact', 20, 'web');
    loadPlugin(array('ZView/ContactView'));
    $view = new contactView();
    $dev = new factureModel();
    $data['listePays'] = $dev->getPays();
    $data['entreprise_cont'] = $PC->rcvG['entreprise'];
    $data['idRetour'] = $PC->rcvG['idRetour'];
    $data['from'] = 'facture';
    $data['idChamp'] = $PC->rcvG['idChamp'];
    echo $view->popupCont($data);
    exit;
}
elseif($PC->rcvG['action'] == 'addEnt') {
    aiJeLeDroit('contact', 20, 'web');
    loadPlugin(array('ZView/ContactView'));
    $view = new contactView();
    $dev = new factureModel();
    $data['listePays'] = $dev->getPays();
    $data['from'] = 'facture';
    $sql = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $sql->makeRequeteFree("SELECT * FROM ref_typeentreprise ");
    $result = $sql->process2();
    if(is_array($result[1])) {
        foreach($result[1] as $v) {
            $data['types'][$v['id_tyent']] = $v['nom_tyent'];
        }
    }
    echo $view->popupEnt($data);
    exit;
}
elseif($PC->rcvG['action'] == 'popupCB') {
    $rs = $info->getContactsForCB($PC->rcvG['facture']);
    if($rs[0]) {
        $view = new factureView();
        echo $view->popupCB($rs[1], $PC->rcvG['facture']);
    }
    exit;
}
elseif($PC->rcvP['action'] == 'payerCB') {
    $model = new Payline();
    $montant = $info->getMontantTTC($PC->rcvP['id_fact']);

    $model->setCodeAction("101");
    $model->setReference("Fact".$PC->rcvP['id_fact']);
    $model->setMontant($montant);

    if($PC->rcvP['id_cont'] != "" and $PC->rcvP['wallet'] != "" and $PC->rcvP['modified'] == 'false') {
        $model->setContact($PC->rcvP['id_cont'], true);
        $result = $model->doWalletPayement();
    }
    elseif($PC->rcvP['id_cont'] != "" and $PC->rcvP['wallet'] != "" and $PC->rcvP['modified'] == 'true') {
        $model->setContact($PC->rcvP['id_cont'], true);
        $model->setNom($PC->rcvP['nom_cont']);
        $model->setPrenom($PC->rcvP['prenom_cont']);
        $model->setCarte($PC->rcvP['finCarte_cont']);
        $model->setDateCarte($PC->rcvP['dateCarte_cont']);
        $model->setCvvCarte($PC->rcvP['cvvCarte_cont']);
        if($PC->rcvP['save_cont'] == "1") {
            $model->saveCarteDatas();
            $model->updateWallet();
            $result = $model->doWalletPayement();
        }
        else {
            $result = $model->doPayement();
        }
    }
    elseif($PC->rcvP['id_cont'] != "" and $PC->rcvP['wallet'] == "") {
        $model->setContact($PC->rcvP['id_cont'], false);
        $model->setNom($PC->rcvP['nom_cont']);
        $model->setPrenom($PC->rcvP['prenom_cont']);
        $model->setCarte($PC->rcvP['finCarte_cont']);
        $model->setDateCarte($PC->rcvP['dateCarte_cont']);
        $model->setCvvCarte($PC->rcvP['cvvCarte_cont']);
        if($PC->rcvP['save_cont'] == "1") {
            $wallet = substr($PC->rcvP['nom_cont'], 0, 5).substr($PC->rcvP['cvvCarte_cont'],0,1).substr($PC->rcvP['prenom_cont'], 0, 2).substr($PC->rcvP['finCarte_cont'], strlen($PC->rcvP['finCarte_cont'])-4,2);
            $model->setWallet($wallet);
            $result = $model->createWallet();
            if($result[0] == 1){
                $model->saveCarteDatas();
                $result = $model->doWalletPayement();
            }
        }
        else {
            $result = $model->doPayement();
        }
    }
    else {
        $model->setNom($PC->rcvP['nom_cont']);
        $model->setPrenom($PC->rcvP['prenom_cont']);
        $model->setCarte($PC->rcvP['finCarte_cont']);
        $model->setDateCarte($PC->rcvP['dateCarte_cont']);
        $model->setCvvCarte($PC->rcvP['cvvCarte_cont']);
        $result = $model->doPayement();
    }
    if($result[0] == 1) {

        $pm = new PaylineModel();
        $data['payline_trans'] = $pm->getLastId();
        $data['contact_trans'] = $PC->rcvP['id_cont'];
        $data['nom_trans'] = $PC->rcvP['nom_cont'];
        $data['prenom_trans'] = $PC->rcvP['prenom_cont'];
        $data['facture_trans'] = $PC->rcvP['id_fact'];
        $data['devise_trans'] = 978;
        $data['montant_trans'] = $montant;
        $trans = new TransactionModel();
        $trans->insert($data);

        $payline = $pm->getDatasFromId($data['payline_trans']);
        $payline = $payline[1][0];

        $cleanFrom = array();
        $cleanTo = array();

        $f = $info->getDataFromID($PC->rcvP['id_fact']);
        loadPlugin(array('docGenerator'));

        foreach ($a = docGeneratorGetZunoConfInfo() as $in => $out) {
            $cleanFrom[] = "{".$in."}";
            $cleanTo[] = $out;
        }
        $cleanFrom[] = "{civ_cont}";
        $cleanTo[] = $PC->rcvP['civ_cont'];
        $cleanFrom[] = "{prenom_cont}";
        $cleanTo[] = $PC->rcvP['prenom_cont'];
        $cleanFrom[] = "{nom_cont}";
        $cleanTo[] = $PC->rcvP['nom_cont'];
        $cleanFrom[] = "{id_fact}";
        $cleanTo[] = $PC->rcvP['id_fact'];
        $cleanFrom[] = "{nom_fact}";
        $cleanTo[] = $f[1][0]['titre_fact'];
        $cleanFrom[] = "{commercial_fullnom}";
        $cleanTo[] = $_SESSION['user']['fullnom'];
        $cleanFrom[] = "{payline_trans}";
        $cleanTo[] = $payline['transaction_hp'];

        $key = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.texte'].'_mailPayement.txt';


        if($PC->rcvP['email_client'] == 1 and $PC->rcvP['mail_cont'] != "") {
            $array['id'] = $PC->rcvP['id_fact'];
            $array['partie'] = "facture";
            $array['typeE'] = "mail";
            $array['typeEmail'] = "html";
            $array['message'] = trim(ProcessTemplating($key,$cleanFrom,$cleanTo));
            $array['sujet'] = "Règlement de la facture ".$PC->rcvP['id_fact'];
            $array['expediteur'] = $GLOBALS['zunoClientCoordonnee']['mail'];
            $array['mail'] = $PC->rcvP['mail_cont'];
            $array['fichier'] = $f[1][0]['file_fact'];
            $sender = new Sender($array);
            $test = $sender->send();
            if($test[0])
                $mess = "Un e-mail de confirmation a été envoyé à votre client sur ".$PC->rcvP['mail_cont'];
            $array = array();
        }
        else
            $mess = "";

        $cc = $info->getUsersOfRight("2");

        $array['id'] = $PC->rcvP['id_fact'];
        $array['partie'] = "facture";
        $array['typeE'] = "mail";
        $array['typeEmail'] = "html";
        $array['message'] = "Vous avez effectué une demande de payement d'un montant de ".prepareNombreAffichage($montant)." €.<br />".
                "Votre client ".$PC->rcvP['prenom_cont']." ".$PC->rcvP['nom_cont']." a bien été prélevé.<br />".
                "Ce payement est lié à la facture ".$PC->rcvP['id_fact']." et a été effectué ce jour, le ".date('d')." ".date('M')." ".date('Y')."<br />".$mess;
        $array['sujet'] = "Débit client sur Carte Bancaire";
        $array['expediteur'] = 'ZUNO';
        $array['mail'] = $_SESSION['user']['mail'];
        $array['cc'] = $cc[1];
        $sender = new Sender($array);
        $sender->send();


    }
    echo viewFiche($PC->rcvP['id_fact'], 'facture', 'interneTraitement', 'non', 'web', true, $result[1]);
    exit;

}
elseif($PC->rcvG['action'] == "popupSupp") {
    $view = new factureView();
    echo $view->popupConfirmSupp($PC->rcvG['facture']);
    exit;
}
elseif($PC->rcvP['action'] == "doSupp") {
    $info->delete($PC->rcvP['facture']);
    header('Location:FactureListe.php?mess=supp');
    exit;
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
