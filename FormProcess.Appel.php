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
loadPlugin(array('ZunoCore','ZView/ProspecView'));
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
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
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
	$req = "SELECT contact_app,appel_app FROM appel WHERE id_app = '".$id_app."'";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	$app = $res[0];
	$contact_app = $app['contact_app'];
}
else  $action = 'new';


if ($PC->rcvP['rappel_app'] == '')
	$erreur = 'sans_date_relance';
if ($PC->rcvP['appel_app'] == '')
	$erreur = 'sans_date_relance';
if (isset($PC->rcvP['contact_app'])) {
	$var_recv['contact_app'] = $PC->rcvP['contact_app'];
	$contact_app = $PC->rcvP['contact_app'];
}
if (isset($PC->rcvP['rappel_app'])) {
	if ($PC->rcvP['rappel_app'] == '')
		$var_recv['rappel_app'] = '';
	else  $var_recv['rappel_app'] = DateHuman2Univ($PC->rcvP['rappel_app']);
}
if (isset($PC->rcvP['appel_app'])) {
	if ($PC->rcvP['appel_app'] == '')
		$var_recv['appel_app'] = '';
	else  $var_recv['appel_app'] = DateHuman2Univ($PC->rcvP['appel_app']);
}
if (isset($PC->rcvP['comm_app']))
	$var_recv['comm_app'] = $PC->rcvP['comm_app'];
if (isset($PC->rcvP['affaire_app']))
	$var_recv['affaire_app'] = $PC->rcvP['affaire_app'];
if ($PC->rcvP['premiercont_app'] == '1')
	$var_recv['premiercont_app'] = '1';
else  $var_recv['premiercont_app'] = '0';


$dateapp = date("Ymd");
if (($action == 'modif')or($action == 'modifpop')) {
	$req = "UPDATE appel SET ";
	foreach ($var_recv as $key => $value)
		$req .= $key." = '".$value."', ";
	$req .= "id_app = '".$id_app."' WHERE id_app = '".$id_app."'";
	$bddtmp->makeRequeteFree($req);
	$bddtmp->process();
}

if (($action == 'new')or($action == 'newpop')) {
	foreach ($var_recv as $key => $value) {
		$req_tete  .= $key.", ";
		$req_corps .= "'".$value."', ";
	}
	$req = "INSERT INTO appel (".$req_tete." id_app, appel_app, utilisateur_app ) VALUES (".$req_corps." '','".$dateapp."','".$_SESSION['user']['id']."');";
	$bddtmp->makeRequeteFree($req);
	$bddtmp->process();
	$bddtmp->makeRequeteFree("SELECT id_app FROM appel WHERE appel_app = '".$dateapp."' ORDER BY id_app DESC LIMIT 1");
	$res = $bddtmp->process();
	$id_app = $res[0]['id_app'];
}

if (($action == 'modifpop')or($action == 'newpop')or($action == 'supp'))
	echo "<script language=\"javascript\">zuno.popup.close();window.opener.history.go(0);</script>";
else  header("Location: ".$_SERVER["HTTP_REFERER"]);

?>