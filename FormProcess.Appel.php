<?php
/*#########################################################################
#
#   name :       FormProcess.Appel.php
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
$model = new appelModel();
if (($PC->rcvP['action'] == "modif")or
    ($PC->rcvP['action'] == "modifpop")) {
	$id_app = $PC->rcvP['id_app'];
	$action = $PC->rcvP['action'];
}
elseif (($PC->rcvP['action'] == "new")or
	($PC->rcvP['action'] == "newpop"))
	$action = $PC->rcvP['action'];
elseif ($PC->rcvG['action'] == "supp") {
	$id_app = $PC->rcvG['id_app'];
	$action = $PC->rcvG['action'];
}
else  $action = 'new';


if ($PC->rcvP['rappel_app'] == '')
	$erreur = 'sans_date_relance';
if ($PC->rcvP['appel_app'] == '')
	$erreur = 'sans_date_relance';
if ($PC->rcvP['rappel_app'] != '')
	$PC->rcvP['rappel_app'] = DateHuman2Univ($PC->rcvP['rappel_app']);
if ($PC->rcvP['appel_app'] != '')
	$PC->rcvP['appel_app'] = DateHuman2Univ($PC->rcvP['appel_app']);
if ($PC->rcvP['premiercont_app'] == '1')
	$PC->rcvP['premiercont_app'] = '1';
else  $PC->rcvP['premiercont_app'] = '0';


$dateapp = date("Ymd");
if (($action == 'modif')or($action == 'modifpop')) {
	$model->update($PC->rcvP, $id_app);
}
if (($action == 'new')or($action == 'newpop')) {
	$PC->rcvP['appel_app'] = $dateapp;
	$PC->rcvP['utilisateur_app'] = $_SESSION['user']['id'];
	$model->insert($PC->rcvP);
	$id_app = $model->getLastId();
}

if (($action == 'modifpop')or($action == 'newpop')or($action == 'supp'))
	echo "<script language=\"javascript\">zuno.popup.close();window.opener.history.go(0);</script>";
else  header("Location: ".$_SERVER["HTTP_REFERER"]);

?>