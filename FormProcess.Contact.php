<?php
/*#########################################################################
#
#   name :       FormProcess.Contact.php
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

$model = new contactParticulierModel();
if (($PC->rcvP['action'] == "modif")or
	($PC->rcvP['action'] == "modifpop")or
	($PC->rcvP['action'] == "new")or
	($PC->rcvP['action'] == "newpop")or
	($PC->rcvP['action'] == "move")) {
    $id_cont = $PC->rcvP['id_cont'];
    $action = $PC->rcvP['action'];
    $cont = $model->getDataFromID($id_cont);
    $ent_cont = $cont[1][0]['entreprise_cont'];
}
elseif (($PC->rcvG['action'] == "supp")) {
    $id_cont = $PC->rcvG['id_cont'];
    $action = $PC->rcvG['action'];
    $cont = $model->getDataFromID($id_cont);
    $ent_cont = $cont[1][0]['entreprise_cont'];
}
else	$action = 'new';

if (($PC->rcvP['nom_cont'] == '')and($PC->rcvP['fonction_cont'] == ''))
    $erreur = 'pas_nom_fonction';
if ($PC->rcvP['relactive_cont'] != '0')
    $PC->rcvP['relactive_cont'] = '1';

if (($action == 'modif')or($action == 'modifpop')) {
    $model->update($PC->rcvP, $id_cont);
}
elseif (($action == 'new')or($action == 'newpop')) {
    $model->insert($PC->rcvP);
    $id_cont = $model->getLastId();
    if ($action == 'new')
	$_SERVER["HTTP_REFERER"] = 'prospec/Contact.php?id_cont='.$id_cont;
}
elseif ($action == 'supp') {
    $model->deleteAllAppels($id_cont);
    $model->delete($id_cont);
}
elseif ($action == 'move') {
    $model->update(array('entreprise_cont'=>$PC->rcvP['entreprise_cont']), $id_cont);
}

if (($action == 'modifpop')or
	($action == 'newpop')or
	($action == 'supp')or
	($action == 'move'))
    echo "<HTML><HEAD>
	     </HEAD><body onLoad=\"zuno.popup.close();window.location='prospec/fiche.php?id_ent=".$ent_cont."';\"></body>";
else header("Location: ".$_SERVER["HTTP_REFERER"]);
exit();
?>
