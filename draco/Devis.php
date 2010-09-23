<?php

/* #########################################################################
  #
  #   name :       Devis.php
  #   desc :       Display page content
  #   categorie :  devis
  #   ID :  	 $Id: Devis.php 2814 2009-06-29 14:54:25Z nm $
  #
  #   copyright:   See licence.txt for this script licence
  ######################################################################### */

/* ------------------------------------------------------------------------+
  | FRAMEWORK LOADING
  +------------------------------------------------------------------------ */
include ('../inc/conf.inc'); // Declare global variables from config files
include ('../inc/core.inc'); // Load core library
loadPlugin(array('ZunoCore', 'ZView/DevisView', 'ZView/ContactView', 'Send/Send', 'ZModels/ActualiteModel', 'ZModels/RenouvellementModel', 'ZModels/AffaireModel'));
loadPlugin(array('ZControl/DevisControl'));
loadPlugin(array('ZControl/GeneralControl'));

// Whe get the page context
$PC = new PageContext('draco');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data, $PC->cacheXML);
/* ------------------------------------------------------------------------+
  | MODULE PROCESSING
  +------------------------------------------------------------------------ */
$model = new devisModel();
if ($PC->rcvG['action'] == "ZieuterPDF") {
    aiJeLeDroit('devis', 62, 'web');
    $bddAff = new affaireModel();
    $name = $GLOBALS['ZunoDevis']['file.suffixe'] . $PC->rcvG['id_devis'] . '.pdf';
    $Path = $bddAff->getAffaireDirectoryPathById(substr($PC->rcvG['id_devis'], 0, 6));
    PushFileToBrowser($Path . $name, $name);
    exit;
} elseif ($PC->rcvG['id_dev'] != '') {
    $sortie = viewFiche($PC->rcvG['id_dev'], 'devis', '', 'non', 'web', true);
} elseif ($PC->rcvG['action'] == 'cloner') {
    aiJeLeDroit('devis', 25, 'web');
    $result = $model->getDataFromID($PC->rcvG['dev']);
    $id = $model->createId($result[1][0]['affaire_dev']);
    $prod = $model->getProduitsFromID($PC->rcvG['dev']);
    $result[1][0]['id_dev'] = $id;
    $result[1][0]['status_dev'] = '1';
    $resultat = $model->insert($result[1][0], 'cloner', $prod[1]);
    if ($resultat[0]) {
	header('Location:Devis.php?id_dev=' . $id);
	exit;
    }
} elseif ($PC->rcvP['action'] == 'modifDevis') {
    aiJeLeDroit('devis', 15, 'web');
    $PC->rcvP['tva_dev'] = prepareNombreTraitement($PC->rcvP['tva_dev']);
    if ($PC->rcvP['actif_ren'] == 1) {
	if ($PC->rcvP['ren_dev'] == "") {
	    $ren['type_ren'] = 'devis';
	    $ren['idChamp_ren'] = $PC->rcvP['id_dev'];
	    $ren['actif_ren'] = 1;
	    $ren['periode_ren'] = $PC->rcvP['periode_ren'];
	    $ren['mail_ren'] = $PC->rcvP['mail_ren'];
	    $ren['statusChamp_ren'] = $PC->rcvP['statusChamp_ren'];
	    $ren['fin_ren'] = substr($PC->rcvP['fin_ren'], 6, 4) . substr($PC->rcvP['fin_ren'], 3, 2) . substr($PC->rcvP['fin_ren'], 0, 2);
	    $renModel = new RenouvellementModel();
	    $renModel->insert($ren);
	    $PC->rcvP['ren_dev'] = $renModel->getLastId();
	} else {
	    $ren['type_ren'] = 'devis';
	    $ren['idChamp_ren'] = $PC->rcvP['id_dev'];
	    $ren['actif_ren'] = 1;
	    $ren['periode_ren'] = $PC->rcvP['periode_ren'];
	    $ren['mail_ren'] = $PC->rcvP['mail_ren'];
	    $ren['statusChamp_ren'] = $PC->rcvP['statusChamp_ren'];
	    $ren['fin_ren'] = substr($PC->rcvP['fin_ren'], 6, 4) . substr($PC->rcvP['fin_ren'], 3, 2) . substr($PC->rcvP['fin_ren'], 0, 2);
	    $renModel = new RenouvellementModel();
	    $renModel->update($PC->rcvP['ren_dev'], $ren);
	}
    } else {
	if ($PC->rcvP['ren_dev'] != "") {
	    $renModel = new RenouvellementModel();
	    $renModel->desactiver($PC->rcvP['ren_dev']);
	}
    }
    if ($PC->rcvP['contact_dev'] == '') {
	echo '<erreur>error</erreur>Un contact technique est obligatoire !';
	exit;
    }
    $result = $model->update($PC->rcvP, $PC->rcvP['id_dev']);
    echo viewFiche($PC->rcvP['id_dev'], 'devis', 'interneInfos', 'non', 'web', true, 'Sauvegardé');
    exit;
} elseif ($PC->rcvP['action'] == 'addProduit') {
    aiJeLeDroit('devis', 15, 'web');
    prepareNombreTraitement($PC->rcvP['prix']);
    prepareNombreTraitement($PC->rcvP['remise']);
    prepareNombreTraitement($PC->rcvP['quantite']);
    if ($PC->rcvP['id_produit'] == '')
	$PC->rcvP['id_produit'] = $PC->rcvP['produit'];
    $control = produitControl::control($PC->rcvP); //Je controle les données
    if ($control[0]) {
	$idp = array($PC->rcvP['id_produit']);
	$data['id_produit'] = FileCleanFileName($idp[0], 'SVN_PROP');
	$data['quantite'] = ($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '') ? 1 : $PC->rcvP['quantite'];
	$data['remise'] = ($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '') ? 0 : $PC->rcvP['remise'];
	$data['id_devis'] = $PC->rcvP['id_devis'];
	$temp = $model->getInfoProduits($data['id_produit']);
	$data['desc'] = ($PC->rcvP['desc'] == NULL || $PC->rcvP['desc'] == '') ? $temp[1][0]['nom_prod'] : $PC->rcvP['desc'];
	$data['prix'] = ($PC->rcvP['prix'] == NULL || $PC->rcvP['prix'] == '') ? $temp[1][0]['prix_prod'] : $PC->rcvP['prix'];
	$result = $model->insertProduits($data); //Je fais l'insertion dans la BDD
	if ($result[0]) {
	    echo viewFiche($PC->rcvP['id_devis'], 'devis', 'interneProduit', 'non', 'web', true, 'Sauvegardé');
	} else {
	    $test = ereg("Duplicate", $result[1], $erreur);
	    if ($test == 9) {
		$mess = "Ce produit est déjà présent dans le devis en cours.";
	    } else {
		$mess = "Une erreur est survenue";
	    }
	    echo viewFiche($PC->rcvP['id_devis'], 'devis', 'interneProduit', 'non', 'web', true, $mess);
	}
    } else {
	echo viewFiche($PC->rcvP['id_devis'], 'devis', 'interneProduit', 'non', 'web', true, $control[1]);
    }
    exit;
} elseif ($PC->rcvP['action'] == 'suppProduit') {
    aiJeLeDroit('devis', 15, 'web');
    $data['id_devis'] = $PC->rcvP['id_devis'];
    $data['id'] = $PC->rcvP['produitAModifier'];

    $result = $model->deleteProduits($data); //J'effectue la suppression du produit de la BDD.
    if ($result[0]) {
	echo viewFiche($PC->rcvP['id_devis'], 'devis', 'interneProduit', 'non', 'web', true, 'Le produit a été enlevé du devis');
    } else {
	echo viewFiche($PC->rcvP['id_devis'], 'devis', 'interneProduit', 'non', 'web', true, 'Une erreur est survenue');
    }

    exit;
} elseif ($PC->rcvP['action'] == 'modifProduit') {
    aiJeLeDroit('devis', 15, 'web');

    $data['id'] = $PC->rcvP['enmodif'];
    $data['id_produit'] = FileCleanFileName($PC->rcvP['id_produit'], 'SVN_PROP');
    $data['quantite'] = ($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '' ) ? 1 : prepareNombreTraitement($PC->rcvP['quantite']);
    $data['remise'] = ($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '') ? 0 : prepareNombreTraitement($PC->rcvP['remise']);
    $data['id_devis'] = $PC->rcvP['id_devis'];
    $temp = $model->getInfoProduits($data['id_produit']);
    $data['desc'] = ($PC->rcvP['desc'] == NULL || $PC->rcvP['desc'] == '') ? $temp[1][0]['nom_prod'] : $PC->rcvP['desc'];
    $data['prix'] = ($PC->rcvP['prix'] == NULL || $PC->rcvP['prix'] == '' ) ? $temp[1][0]['prix_prod'] : prepareNombreTraitement($PC->rcvP['prix']);
    $result = $model->updateProduits($data);
    if ($result[0]) {
	echo viewFiche($PC->rcvP['id_devis'], 'devis', 'interneProduit', 'non', 'web', true, 'Le produit a été modifié');
	exit;
    }
} elseif (($PC->rcvP['action'] == "Voir") or ($PC->rcvP['action'] == "Record") or ($PC->rcvP['action'] == "RecordSend") or ($PC->rcvP['action'] == "Send")) {
    aiJeLeDroit('devis', 62, 'web');
    $gnose = new devisGnose();
    $id_dev = $PC->rcvP['id_dev'];
    if ($PC->rcvP['action'] != 'Send') {
	$Doc = $gnose->DevisGenerateDocument($id_dev, $PC->rcvP['OutputExt'], $PC->rcvP['Cannevas']);
	if (!is_string($Doc)) {
	    echo viewFiche($PC->rcvP['id_dev'], 'devis', 'interneTraitement', 'non', 'web', true, 'Impossible de générer le document.');
	    exit;
	}
    }
    if ($PC->rcvP['action'] == 'Voir') {
	PushFileToBrowser($GLOBALS['REP']['appli'] . $GLOBALS['REP']['tmp'] . $Doc, $Doc);
    }
    if (($PC->rcvP['action'] == 'Record') or ($PC->rcvP['action'] == 'RecordSend')) {
	aiJeLeDroit('devis', 60, 'web');
	$PC->rcvP['MessageRecord'] = "Changement du devis " . $id_dev . " par " . $_SESSION['user']['id'];

	$dev = $model->getDataFromID($id_dev);
	$dev = $dev[1][0];
	$affaire = ($dev['id_aff'] != '') ? $dev['id_aff'] : substr($id_dev, 0, 6);
	$save = $gnose->DevisSaveDocInGnose($GLOBALS['REP']['appli'] . $GLOBALS['REP']['tmp'] . $Doc, $Doc, $affaire, $PC->rcvP['MessageRecord']);

	if ($dev['status_aff'] < 3) {
	    $inActualiteRec['status_aff'] = '3';
	    $model->makeRequeteUpdate('affaire', 'id_aff', $affaire, array('status_aff' => $inActualiteRec['status_aff']));
	    $model->process();
	}
	if ($dev['status_dev'] < 3) {
	    $inActualiteRec['status_dev'] = '3';
	    $model->update($inActualiteRec, $dev['id_dev']);
	}

	if ($PC->rcvP['action'] == 'Record') {
	    echo viewFiche($PC->rcvP['id_dev'], 'devis', 'interneTraitement', 'non', 'web', true, 'Enregistré');
	    exit;
	}
    }
    if (($PC->rcvP['action'] == 'RecordSend') or ($PC->rcvP['action'] == 'Send')) {
	if ($PC->rcvP['action'] == 'RecordSend') {
	    $PC->rcvP['fichier'] = $Doc;
	}
	$PC->rcvP['id'] = $PC->rcvP['id_dev'];
	$PC->rcvP['partie'] = 'devis';
	$PC->rcvP['from'] = $_SESSION['user']['mail'];
	$PC->rcvP['expediteur'] = $_SESSION['user']['fullnom'];
	$send = new Sender($PC->rcvP);
	$result = $send->send($_SESSION['user']['mail']);
	if ($result[0]) {
	    $model->makeRequeteFree("SELECT * FROM devis WHERE  id_dev = '" . $id_dev . "'");
	    $lignes = $model->process();
	    $dev = $lignes[0];
	    switch ($PC->rcvP['typeE']) {
		case 'fax' : $arrivee = " au numéro suivant : " . $PC->rcvP['fax'] . ".";
		    break;
		case 'courrier' : $arrivee = ".";
		    break;
		default : $arrivee = " à l'adresse suivante : " . $PC->rcvP['mail'] . ".";
	    }
	    $model->update(array("status_dev" => "4"), $id_dev, true, 'From : ' . $PC->rcvP['from'] . "\nTo : " . $PC->rcvP['mail']);

	    $affaire = substr($id_dev, 0, 6);
	    $model->makeRequeteUpdate('affaire', "id_aff", $affaire, array("status_aff" => "4"));
	    $model->process();
	    echo viewFiche($PC->rcvP['id_dev'], 'devis', 'interneTraitement', 'non', 'web', true, 'Envoyé');
	    exit;
	} else {
	    echo "Erreur : " . $result[1];
	    exit;
	}
    }
} elseif ($PC->rcvG['action'] == 'Perdu') {
    $view = new devisView();
    echo $view->popupPerdu($PC->rcvG['devis']);
    exit;
} elseif ($PC->rcvP['action'] == 'Perdu') {
    aiJeLeDroit('devis', 13, 'web');
    $data['status_dev'] = '5';
    $result = $model->update($data, $PC->rcvP['id_dev'], false);
    $model->addActualite($PC->rcvP['id_dev'], 'perdu');

    if ($PC->rcvP['close_aff'] == "1" or $PC->rcvP['archive_aff'] == "1") {
	$data['status_aff'] = 6;
	$data['actif_aff'] = 0;
	$aff = new affaireModel();
	$aff->update($data, substr($PC->rcvP['id_dev'], 0, 6));
    }
    if ($PC->rcvP['archive_aff'] == "1") {
	affaireModel::archivateAffaireInDB(substr($PC->rcvP['id_dev'], 0, 6));
    }

    echo viewFiche($PC->rcvP['id_dev'], 'devis', 'Traitement', 'non', 'web', true, 'Ce devis vient d\'être marqué comme perdu.');
    exit;
} elseif ($PC->rcvG['action'] == 'actuDevis') {
    aiJeLeDroit('actualite', 10, 'web');
    $sql = new actualiteModel();
    $result = $sql->getData4Devis($PC->rcvG['id_devis']);
    $view = new devisView();
    echo $view->popupActu($result[1]);
    exit;
} elseif ($PC->rcvG['action'] == 'addContDev') {
    aiJeLeDroit('contact', 20, 'web');
    loadPlugin(array('ZView/ContactView'));
    $view = new contactView();
    $data['listePays'] = $model->getPays();
    $data['entreprise_cont'] = $PC->rcvG['entreprise'];
    $data['idRetour'] = $PC->rcvG['idRetour'];
    $data['from'] = 'devis';
    $data['idChamp'] = ($PC->rcvG['idChamp'] != null) ? $PC->rcvG['idChamp'] : $PC->rcvG['idRetour'];
    echo $view->popupCont($data);
    exit;
} elseif ($PC->rcvG['action'] == 'addEnt') {
    aiJeLeDroit('contact', 20, 'web');
    loadPlugin(array('ZView/ContactView'));
    $view = new contactView();
    $data['listePays'] = $model->getPays();
    $data['from'] = 'devis';
    $sql = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $sql->makeRequeteFree("SELECT * FROM ref_typeentreprise ");
    $result = $sql->process2();
    if (is_array($result[1])) {
	foreach ($result[1] as $v) {
	    $data['types'][$v['id_tyent']] = $v['nom_tyent'];
	}
    }
    echo $view->popupEnt($data);
    exit;
} elseif ($PC->rcvG['action'] == 'valid') {
    aiJeLeDroit('devis', 13, 'web');
    loadPlugin(array('ZView/CommandeView'));
    $view = new commandeView();
    $data['id_dev'] = $PC->rcvG['id_devis'];
    $sql = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $sql->makeRequeteFree("select * from devis_produit dp left join produit p on p.id_prod=dp.id_produit left join produit_fournisseur pf on pf.produit_id = dp.id_produit left join fournisseur ON fournisseur.id_fourn = pf.fournisseur_id left join entreprise e ON e.id_ent = fournisseur.entreprise_fourn where dp.id_devis = '" . trim($data['id_dev']) . "' order by id_produit ASC ;");
    $result = $sql->process2();
    $old = null;
    $increment = 0;
    foreach ($result[1] as $v) {
	if ($v['id_produit'] != $old) {
	    if ($v['id_fourn'] != null) {
		$v['fournisseurs'][$v['id_fourn']] = $v['nom_ent'] . ' (' . $v['cp_ent'] . ')';
		$v['PF'][$v['id_fourn']] = $v['prixF'];
		$v['RF'][$v['id_fourn']] = $v['remiseF'];
	    }
	    $data['produits'][$increment] = $v;
	    $old = $v['id_produit'];
	    $increment++;
	    continue;
	} else {
	    $data['produits'][$increment - 1]['fournisseurs'][$v['id_fourn']] = $v['nom_ent'] . ' (' . $v['cp_ent'] . ')';
	    $data['produits'][$increment - 1]['PF'][$v['id_fourn']] = formatCurencyDisplay($v['prixF'], 2, '');
	    $data['produits'][$increment - 1]['RF'][$v['id_fourn']] = formatCurencyDisplay($v['remiseF'], 2, '');
	    continue;
	}
    }
    echo $view->popupCommande($data);
    exit;
} elseif ($PC->rcvG['action'] == 'supp' and $PC->rcvG['id_devis'] != '') {
    aiJeLeDroit('devis', 30, 'web');
    $titre = "Suppression du devis " . $PC->rcvG['id_devis'];
    $corps = '<span class="importantblue">Confirmer la suppression</span>';
    $pied = '<a href="javascript:zuno.popup.close();">' . imageTag('../img/prospec/cancel.png', 'Effacer', 'middle') . ' Annuler</a>
		   <a href="../draco/Devis.php?action=suppconfirm&id_devis=' . $PC->rcvG['id_devis'] . '">' . imageTag('../img/prospec/confirm.png', 'Effacer', 'middle') . 'Confirmer</a>';
    echo generateZBox($titre, $titre, $corps, $pied, 'DevisBox', '');
    exit;
} elseif ($PC->rcvG['action'] == 'suppconfirm' and $PC->rcvG['id_devis'] != '') {
    $model = new DevisModel();
    $model->makeRequeteUpdate('devis', "id_dev", $PC->rcvG['id_devis'], array("status_dev" => "2"));
    $model->process();
    $model->addActualite($PC->rcvG['id_devis'], 'delete');
    $out = new PageDisplayHeader();
    echo $out->Process();
    echo "<script language=\"javascript\">location.href = 'DevisListe.php';</script>";
    exit;
} elseif ($PC->rcvG['action'] == 'pdf') {
    $bddAff = new affaireModel();
    $name = $GLOBALS['ZunoDevis']['file.suffixe'] . $PC->rcvG['id_devis'] . '.pdf';
    $Path = $bddAff->getAffaireDirectoryPathById(substr($PC->rcvG['id_devis'], 0, 6));
    PushFileToBrowser($path . $name, $name, 'application/pdf');
    exit;
} elseif ($PC->rcvG['action'] == 'clone') {
    $id_dev = $PC->rcvG['id_devis'];
    if ($PC->rcvG['renew'] == "yes") {
	$oldAffaire = substr($id_dev, 0, 6);
	$model->makeRequeteFree("SELECT * FROM affaire WHERE id_aff = '" . $oldAffaire . "'");
	$res = $model->process();
	if (count($res) > 0) {
	    $inputDB = $res[0];
	}
	$inputDB['id_aff'] = $affaire = affaireModel::affaireGenerateID();
	$inputDB['status_aff'] = "1";
	$inputDB['actif_aff'] = "1";
	unset($inputDB['projet_aff']);
	$inputDB['echeance_aff'] = "";
	$inputDB['budget_aff'] = "";
	$inputDB['titre_aff'] .= " RENOUVELLEMENT";
	$inputDB['modif_aff'] = DateTimestamp2Univ('');
	$inputDB['detect_aff'] = DateTimestamp2Univ('');
	$inputDB['commercial_aff'] = $_SESSION['user']['id'];
	affaireModel::createNewAffaireInDB($inputDB);
    } else {
	$affaire = substr($id_dev, 0, 6);
    }
    $model->makeRequeteFree("SELECT * FROM devis WHERE id_dev = '" . $id_dev . "'");
    $res = $model->process();
    if (count($res) > 0) {
	$var_recv = $res[0];
	$var_recv['id_dev'] = devisModel::DevisGenerateID($affaire);
	$var_recv['status_dev'] = "1";
	$var_recv['commercial_dev'] = $_SESSION['user']['id'];
	$var_recv['daterecord_dev'] = DateTimestamp2Univ('');
	$var_recv['datemodif_dev'] = DateTimestamp2Univ('');
	// on insert le devis
	$model->makeRequeteInsert('devis', $var_recv);
	$model->process();
	// On copie les ligne du devis
	$model->makeRequeteFree("SELECT * FROM devis_produit WHERE id_devis = '" . $id_dev . "'");
	$res = $model->process();
	if (count($res) > 0) {
	    foreach ($res as $key => $produit) {
		$produit['id_devis'] = $var_recv['id_dev'];
		// on insert le devis
		$model->makeRequeteInsert('devis_produit', $produit);
		$model->process();
	    }
	}
	// on insert l'actualité
	$model->addActualite($var_recv['id_dev'], 'clone', '', '', $id_dev);
	// On laisse une trace dans l'histo de l'affaire
	$model->makeRequeteUpdate('affaire', "id_aff", $affaire, array("status_aff" => "3"));
	$model->process();
	$location = "draco/Devis.php?id_dev=" . $var_recv['id_dev'];
	// si clone de renew, on mets a jour l'entrée de renouvelement
	if ($PC->rcvG["renew"] == "yes") {
	    $model->makeRequeteUpdate('devis_renew', "id_devis", $id_dev, array("devisgenere" => $var_recv['id_dev']));
	    $model->process();
	}
    }
} elseif ($PC->rcvP['action'] == "Zieuter") {
    aiJeLeDroit('devis', 62, 'web');
    $bddAff = new affaireModel();
    $name = $GLOBALS['ZunoDevis']['file.suffixe'] . $PC->rcvP['id_dev'] . '.' . $PC->rcvP['format'];
    $Path = $bddAff->getAffaireDirectoryPathById(substr($PC->rcvP['id_dev'], 0, 6));
    PushFileToBrowser($Path . $name, $name);
    exit;
}
/* ------------------------------------------------------------------------+
  | DISPLAY PROCESSING
  +------------------------------------------------------------------------ */

$out->AddBodyContent($sortie);
$out->Process();
?>