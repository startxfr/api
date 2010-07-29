<?php
/*#########################################################################
#
#   name :       Fournisseur.php
#   desc :       Display page content
#   categorie :  produit
#   ID :  	 $Id: Produit.php 2814 2009-06-29 14:54:25Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/FournisseurView','ZView/ProduitView', 'ZModels/ContactModel'));
loadPlugin(array('ZControl/GeneralControl'));
loadPlugin(array('ZControl/DevisControl'));

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

if($PC->rcvG['id_fourn'] != '') {
    $sortie = viewFiche($PC->rcvG['id_fourn'], 'produit', '', 'fourn', 'web');
}
elseif($PC->rcvP['action'] == 'modifFournisseur') {
    aiJeLeDroit('produit', 15, 'web');
    $sql = new ProduitModel();
    $PC->rcvP['remise_fourn'] = prepareNombreTraitement($PC->rcvP['remise_fourn']);
    if(!array_key_exists('actif', $PC->rcvP))
	$PC->rcvP['actif'] = 0;

    $rs = $sql->updateFournisseur($PC->rcvP, 'non', $PC->rcvP['idFournisseur']);
    if($rs[0]) {
	echo viewFiche($PC->rcvP['idFournisseur'], 'produit', 'interneInfos', 'fourn', 'web', true, 'Enregistré');
	exit;
    }
    else {
	echo '<erreur>error</erreur><span class="important" style="text-align:center;">Une erreur a eu lieu lors de l\'insertion !</span>';
	exit;
    }

}
elseif($PC->rcvP['action'] == 'addFournisseur') {
    aiJeLeDroit('produit', 20, 'web');
    if(!array_key_exists('nom_ent', $PC->rcvP) or $PC->rcvP['nom_ent'] == '') {
	echo '<erreur>error</erreur><span class="important" style="text-align:center;">Veuillez indiquer une entreprise comme fournisseur.</span>';
	exit;
    }
    elseif(!array_key_exists('cp_ent', $PC->rcvP) or $PC->rcvP['cp_ent'] == '') {
	echo '<erreur>error</erreur><span class="important" style="text-align:center;">Veuillez indiquer un code postal pour cette entreprise.</span>';
	exit;
    }
    elseif($PC->rcvP['nom_cont'] == "" or $PC->rcvP['prenom_cont'] == "") {
	echo '<erreur>error</erreur><span class="important" style="text-align:center;">Veuillez indiquer un contact pour cette entreprise.</span>';
	exit;
    }
    else {
	$sql = new contactEntrepriseModel();
	$PC->rcvP['type_ent'] = 5;
	$PC->rcvP['nom_ent'] = strtoupper($PC->rcvP['nom_ent']);
	$sql->insert($PC->rcvP);
	$PC->rcvP['entreprise_cont'] = $sql->getLastId();
	$sql = new contactParticulierModel();
	$sql->insert($PC->rcvP);
	$PC->rcvP['contactComm_fourn'] = $sql->getLastId();
	$sql = new produitModel();
	$PC->rcvP['entreprise_fourn'] = $PC->rcvP['entreprise_cont'];
	$PC->rcvP['actif'] = 1;
	$cp = substr($PC->rcvP['cp_ent'],0,2);
	$nom = strtoupper(substr($PC->rcvP['nom_ent'],0,2));
	$id = $nom.$cp;
	$deja = $sql->getFournisseurByID($id);
	$k = 1;
	while(array_key_exists('0',$deja[1])) {
	    $nom = strtoupper(substr($PC->rcvP['nomEnt'],$k,2));
	    $k++;
	    $id = $nom.$cp;
	    $deja = $sql->getFournisseurByID($id);
	}
	$PC->rcvP['id_fourn'] = $id;
	$rs = $sql->insertFournisseur($PC->rcvP);
	if($rs[0]) {
	    echo viewFiche($id, 'produit', 'afterCreate', 'fourn', 'web', true, 'Enregistré');
	    exit;
	}
	else {
	    echo '<erreur>error</erreur><span class="important" style="text-align:center;">Une erreur est survenue lors de l\'insertion de ce fournisseur.</span>';
	    exit;

	}
    }

}
elseif($PC->rcvG['action'] == 'changeDatas') {

    $view = new FournisseurView();
    echo $view->popupActionProd();

    exit;
}
elseif($PC->rcvP['action'] == 'changeDatasProdFourn') {
    aiJeLeDroit('produit', 15, 'web');
    if($PC->rcvP['idFournisseur'] == '') {
	echo '<erreur>erreurPopup</erreur>Un problème est survenu, impossible de faire la modification !';
	exit;

    }
    if(!is_array($PC->rcvP['prod'])) {
	echo '<erreur>erreurPopup</erreur>Vous n\'avez pas sélectionné de produit !';
	exit;
    }
    $sql = new produitModel();
    $sql->updateProduitFournisseurMasse($PC->rcvP['idFournisseur'], $PC->rcvP['newPF'], $PC->rcvP['newRF'], $PC->rcvP['prod']);
    echo viewFiche($PC->rcvP['idFournisseur'], 'produit', 'afterChangeProd', 'fourn', 'web', true, 'Enregistré');
    exit;
}
else {
    aiJeLeDroit('produit', 20, 'web');
    $sql = new ProduitModel();
    $datas['entreprise'] = $sql->getEntreprisePotentiels();
    $datas['pays'] = $sql->getPays();
    $view = new FournisseurView();
    $sortie = $view->creer($datas);
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
