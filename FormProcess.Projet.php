<?php
/*#########################################################################
#
#   name :       FormProcess.Projet.php
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
loadPlugin(array('ZunoCore','ZView/ProspecView','ZModels/ContactModel'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetVarContext();
$PC->GetChannelContext();
$PC->GetSessionContext();
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
if ($PC->rcvP['action'] == "modif") {
    $id_proj= $PC->rcvP['id_proj'];
    $id_cont= $PC->rcvP['contact_proj'];
    $action = $PC->rcvP['action'];
}
elseif ($PC->rcvP['action'] == "new") {
    $action = $PC->rcvP['action'];
    $id_cont= $PC->rcvP['contact_proj'];
}
elseif ($PC->rcvP['action'] == "newapp") {
    $action = $PC->rcvP['action'];
    $id_cont= $PC->rcvP['contact_proj'];
    $id_app = $PC->rcvP['appel_proj'];
}

$PC->rcvP['actif_proj'] = ($PC->rcvP['actif_proj'] == '1') ? 1 : 0;
if ($PC->rcvP['echeance_proj'] != '')
    $Decheance_proj = "'".DateHuman2Univ($PC->rcvP['echeance_proj'])."'";
if ($PC->rcvP['rdv_proj'] != '')
    $Drdv_proj = "'".DateHuman2Univ($PC->rcvP['rdv_proj'])."'";

//############################################################################
//		RECEPTION DES VARABLES
//############################################################################
$model = new projetModel();
$datedetect = date("Ymd");
if ($action == 'modif') {
    $PC->rcvP['rdv_proj'] = $Drdv_proj;
    $PC->rcvP['echeance_proj'] = $Decheance_proj;
    $model->update($PC->rcvP, $id_proj);
}

if (($action == 'new')or($action == 'newadd')) {
    $PC->rcvP['rdv_proj'] = $Drdv_proj;
    $PC->rcvP['echeance_proj'] = $Decheance_proj;
    $PC->rcvP['detect_proj'] = $datedetect;
    $model->insert($PC->rcvP);
    $id_proj = $model->getLastId();
}

if ($popup == 'on')
    echo "<HTML><HEAD></HEAD><body onLoad=\"zuno.popup.close();\"></body>";
else header("Location: ".$_SERVER["HTTP_REFERER"]);
exit();
?>
