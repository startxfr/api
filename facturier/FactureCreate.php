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
loadPlugin(array('ZunoCore','ZView/FactureView','ZModels/CommandeModel','ZModels/ContactModel', 'ZModels/DevisModel', 'ZControl/FactureControl'));

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
aiJeLeDroit('facture', 20, 'web');
if($PC->rcvP['action'] == 'addFacture') {

    $control = factureControl::controlAddWeb($PC->rcvP);
    if(!$control[0]) {
	$view = new factureView();
	$sortie = $view->creerFacture('', $control[1], $PC->rcvP);
    }
    else {
	$data['contact_fact'] = $PC->rcvP['contact_fact'];
	$data['contact_achat_fact'] = $PC->rcvP['contact_achat_fact'];
	$info = new factureModel();
	if($PC->rcvP['commande_fact'] != "null" and $PC->rcvP['entreprise_fact'] != '') {
	    $cmd = $info->getEntrepriseData($PC->rcvP['commande_fact']);
	    $cmd = $cmd[1][0];
	    $devisM = new commandeModel();
	    $produit = $devisM->getProduitsFromID($PC->rcvP['commande_fact']);
	    $produit = $produit[1];
	    if($PC->rcvP['type'] == 'Avoir')
		$data['sommeHT_fact'] = (-1)*abs($cmd['sommeHT_cmd']);
	    else
		$data['sommeHT_fact'] = $cmd['sommeHT_cmd'];
	    $data['modereglement_fact'] = $cmd['modereglement_cmd'];
	    $data['condireglement_fact'] = $cmd['condireglement_cmd'];
	    $data['entreprise_fact'] = $cmd['entreprise_cmd'];
	    $data['contact_fact'] = $cmd['contact_cmd'];
	    $data['contact_achat_fact'] = $cmd['contact_achat_cmd'];
	    $data['nomentreprise_fact'] = $cmd['nomdelivery_cmd'];
	    $data['tauxTVA_fact'] = $cmd['tva_cmd'];
	}
	elseif($PC->rcvP['entreprise_fact'] != '') {
	    $ent = new contactEntrepriseModel();
	    $id = $ent->getDataFromID($PC->rcvP['entreprise_fact']);
	    $data['entreprise_fact'] = $PC->rcvP['entreprise_fact'];
	    $data['nomentreprise_fact'] = $id[1][0]['nom_ent'];
	    $data['tauxTVA_fact'] = $id[1][0]['tauxTVA_ent'];
	}
	else {
	    $ent = new contactParticulierModel();
	    $id = $ent->getDataFromID($PC->rcvP['contact_achat_fact']);
	    $data['nomentreprise_fact'] = $id[1][0]['civ_cont'].' '.$id[1][0]['prenom_cont'].' '.$id[1][0]['nom_cont'];
	    $data['tauxTVA_fact'] = $GLOBALS['zunoClientStatut']['tauxTVA'];
	}

	$id = $info->GetLastId();
	$id ++;
	$data['id_fact'] = $id;
	$data['commande_fact'] = $PC->rcvP['commande_fact'];
	$data['titre_fact'] = $PC->rcvP['titre_fact'];
	$data['commercial_fact'] = $_SESSION['user']['id'];
	$data['add1_fact'] = $PC->rcvP['add1_fact'];
	$data['add2_fact'] = $PC->rcvP['add2_fact'];
	$data['ville_fact'] = $PC->rcvP['ville_fact'];
	$data['cp_fact'] = $PC->rcvP['cp_fact'];
	$data['pays_fact'] = $PC->rcvP['pays_fact'];
	$data['type_fact'] = 'Facture';
	if($PC->rcvP['commande_fact'] != "null" and $PC->rcvP['entreprise_fact'] != '')
	    $result = ($PC->rcvP['type'] == 'Avoir') ? $info->insert($data, 'toAvoir', $produit) : $info->insert($data, 'cloner', $produit);
	else $result = $info->insert($data);
	if($PC->rcvP['entreprise_fact'] != '') {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $bddtmp->makeRequeteFree("UPDATE entreprise SET type_ent = '3' WHERE id_ent = ".$data['entreprise_fact']." AND type_ent < '3' ; ");
	    $bddtmp->process2();
	}
	if($result[0]) {
	    header('Location:Facture.php?id_fact='.$id);
	    exit;
	}
	else {
	    $view = new factureView();
	    $view->creerFacture();
	}
    }
}
elseif($PC->rcvP['action'] == 'createFromDevis') {
    aiJeLeDroit('commande', 20, 'web');
    $info = new commandeModel();
    $aijecommande = $info->getDataFromID($PC->rcvP['devis']."BC");
    if($aijecommande[1][0]['id_cmd'] == null || $aijecommande[1][0]['id_cmd'] == '') {
	$dev = $info->getEntrepriseData($PC->rcvP['devis']);
	$dev = $dev[1][0];
	$devisM = new devisModel();
	$produit = $devisM->getProduitsFromID($PC->rcvP['devis']);
	$produit = $produit[1];
	$FHT = 0;
	$data['id_cmd'] = $PC->rcvP['devis'].'BC';
	$data['devis_cmd'] = $PC->rcvP['devis'];
	$data['titre_cmd'] = $PC->rcvP['titre_cmd'];
	$data['BDCclient_cmd'] = $PC->rcvP['BDCclient_cmd'];
	$data['commercial_cmd'] = $_SESSION['user']['id'];
	$data['sommeHT_cmd'] = $dev['sommeHT_dev'];
	$data['sommeFHT_cmd'] = $FHT;
	$data['modereglement_cmd'] = $PC->rcvP['modereglement_cmd'];
	$data['condireglement_cmd'] = $PC->rcvP['condireglement_cmd'];
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
	$data['status_cmd'] = 9;

	$result = $info->insert($data, 'cloner', $produit);
	$aijecommande[1][0] = $data;
    }
    $produit = $info->getProduitsFromID($aijecommande[1][0]['id_cmd']);
    $produit = $produit[1];
    $facture = new factureModel();
    $id = $facture->GetLastId();
    $id ++;
    $data['id_fact'] = $id;
    $data['commande_fact'] = $aijecommande[1][0]['id_cmd'];
    $data['titre_fact'] = $aijecommande[1][0]['titre_cmd'];
    $data['commercial_fact'] = $_SESSION['user']['id'];
    $data['sommeHT_fact'] = $aijecommande[1][0]['sommeHT_cmd'];
    $data['modereglement_fact'] = $aijecommande[1][0]['modereglement_fact'];
    $data['condireglement_fact'] = $aijecommande[1][0]['condireglement_fact'];
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
    $data['type_fact'] = 'Facture';
    $result2 = $facture->insert($data, 'cloner', $produit);
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("UPDATE entreprise SET type_ent = '3' WHERE id_ent = ".$data['entreprise_fact']." AND type_ent < '3' ; ");
    $bddtmp->process2();
    $bddtmp->makeRequeteFree("UPDATE devis SET status_dev = '6' WHERE id_dev = ".$PC->rcvP['devis']." ");
    $bddtmp->process2();
    if($result2[0]) {
	echo '<redirection>../facturier/Facture.php?id_fact='.$data['id_fact'].'</redirection>';
	exit;
    }
}
else {
    $view = new factureView();
    if($PC->rcvG['type'] == 'avoir')
	$sortie = $view->creerFacture('avoir');
    else $sortie = $view->creerFacture();
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
