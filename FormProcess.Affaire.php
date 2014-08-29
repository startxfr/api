<?php
/*#########################################################################
#
#   name :       FormProcess.Affaire.php
#   desc :       Prospection list for sxprospec
#   categorie :  prospec page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('inc/conf.inc');		// Declare global variables from config files
include ('inc/core.inc');		// Load core library
loadPlugin(array('ZunoCore','ZView/AffaireView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('draco');
$PC->GetVarContext();
$PC->GetChannelContext();
$PC->GetSessionContext();

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
if ($PC->rcvP['action'] == "modif") {
    $id_aff= $PC->rcvP['id_aff'];
    $id_cont= $PC->rcvP['contact_aff'];
    $action = $PC->rcvP['action'];
}
elseif ($PC->rcvP['action'] == "new") {
    $action = $PC->rcvP['action'];
    $id_cont= $PC->rcvP['contact_aff'];
}
elseif ($PC->rcvP['action'] == "newapp") {
    $action = $PC->rcvP['action'];
    $id_cont= $PC->rcvP['contact_aff'];
    $id_app = $PC->rcvP['appel_aff'];
}
elseif ($PC->rcvG['action'] == "archive") {
    $action = $PC->rcvG['action'];
    $id_aff= $PC->rcvG['id_aff'];
}
elseif ($PC->rcvP['action'] == "doArchivate") {
    $action = $PC->rcvP['action'];
}
else {
    $erreur = '0';
}

if (isset($PC->rcvP['titre_aff'])) {
    if ($PC->rcvP['titre_aff'] != '')
	$var_recv['titre_aff'] = $PC->rcvP['titre_aff'];
    else
	$erreur = '1';
}

if (isset($PC->rcvP['contact_aff']))
    $var_recv['contact_aff'] = $PC->rcvP['contact_aff'];
if ($PC->rcvP['actif_aff'] == '1')
    $var_recv['actif_aff'] = '1';
else	$var_recv['actif_aff'] = '0';
if (isset($PC->rcvP['status_aff']))
    $var_recv['status_aff'] = $PC->rcvP['status_aff'];
if (isset($PC->rcvP['titre_aff']))
    $var_recv['titre_aff'] = $PC->rcvP['titre_aff'];
if ($PC->rcvP['echeance_aff'] == '')
    $Decheance_aff = 'NULL';
else {
    $jour = substr($PC->rcvP['echeance_aff'],0,2);
    $mois = substr($PC->rcvP['echeance_aff'],3,2);
    $annee = substr($PC->rcvP['echeance_aff'],6,4);
    $Decheance_aff = $annee.'-'.$mois.'-'.$jour.' 23:59';
}
if (isset($PC->rcvP['budget_aff']))
    $var_recv['budget_aff'] = $PC->rcvP['budget_aff'];
if (isset($PC->rcvP['decid_aff']))
    $var_recv['decid_aff'] = $PC->rcvP['decid_aff'];
if (isset($PC->rcvP['comm_aff']))
    $var_recv['comm_aff'] = $PC->rcvP['comm_aff'];
if (isset($PC->rcvP['desc_aff']))
    $var_recv['desc_aff'] = $PC->rcvP['desc_aff'];
if (isset($PC->rcvP['typeproj_aff']))
    $var_recv['typeproj_aff'] = $PC->rcvP['typeproj_aff'];
if (isset($PC->rcvP['entreprise_aff']))
    $var_recv['entreprise_aff'] = $PC->rcvP['entreprise_aff'];


$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$datedetect = date("Ymd");
if ($action == 'doArchivate') {
    if (count($PC->rcvP['affList']) > 0)
	foreach ($PC->rcvP['affList'] as $k => $aff) affaireModel::archivateAffaireInDB($aff);
    $location = 'draco/ListeAffaires.php';
}

if ($action == 'move') {
    $req = "SELECT * FROM contact WHERE id_cont = '".$id_cont."'";
    $bddtmp->makeRequeteFree($req);
    $res = $bddtmp->process();
    $cont = $res[0];
    $ent_cont_avant = $cont['entreprise_cont'];
    $req = "UPDATE contact SET entreprise_cont = ".$new_ent_cont." WHERE id_cont = '".$id_cont."';";
    $bddtmp->makeRequeteFree($req);
    $bddtmp->process();
    $ent_cont = $ent_cont_avant;
}


$location = $_SERVER["HTTP_REFERER"];
if ($action == 'modif') {
    $req = "UPDATE affaire SET ";
    foreach ($var_recv as $key => $value) {
	if ($value == '') {
	    $value = 'NULL';
	}
	else {
	    $value = "'".$value."'";
	}
	$req .= $key." = ".$value.", ";
    }
    $req .= "echeance_aff = '".$Decheance_aff."' ";
    $req .= " WHERE id_aff = '".$id_aff."'";
    $bddtmp->makeRequeteFree($req);
    $bddtmp->process2();
}

if ($action == 'archive') {
    affaireModel::archivateAffaireInDB($id_aff);
    $location = "draco/Affaire.php?id_aff=".$id_aff;
}

if (($action == 'new')or($action == 'newadd')) {
    $var_recv['id_aff'] = affaireModel::affaireGenerateID();
    affaireModel::createNewAffaireInDB($var_recv);
    $location = "draco/Affaire.php?id_aff=".$var_recv['id_aff'];
}

header("Location: ".$location);
exit();

?>
