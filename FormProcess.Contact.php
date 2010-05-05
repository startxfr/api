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
    ($PC->rcvP['action'] == "modifpop")or
    ($PC->rcvP['action'] == "new")or
    ($PC->rcvP['action'] == "newpop")or
    ($PC->rcvP['action'] == "move"))
{
	$id_cont = $PC->rcvP['id_cont'];
	$action = $PC->rcvP['action'];
	$bddtmp->makeRequeteFree("SELECT * FROM contact WHERE id_cont = '".$id_cont."'");
	$cont = $bddtmp->process();
	$ent_cont = $cont[0]['entreprise_cont'];
}
elseif (($PC->rcvG['action'] == "supp"))
{
	$id_cont = $PC->rcvG['id_cont'];
	$action = $PC->rcvG['action'];
	$bddtmp->makeRequeteFree("SELECT * FROM contact WHERE id_cont = '".$id_cont."'");
	$cont = $bddtmp->process();
	$ent_cont = $cont[0]['entreprise_cont'];
}
else	$action = 'new';

if (($PC->rcvP['nom_cont'] == '')and($PC->rcvP['fonction_cont'] == ''))
	$erreur = 'pas_nom_fonction';
if (isset($PC->rcvP['entreprise_cont']))
	$var_recv['entreprise_cont'] = $PC->rcvP['entreprise_cont'];
if ($PC->rcvP['relactive_cont'] == '0')
	$var_recv['relactive_cont'] = '0';
else	$var_recv['relactive_cont'] = '1';
if (isset($PC->rcvP['civ_cont']))
	$var_recv['civ_cont'] = $PC->rcvP['civ_cont'];
if (isset($PC->rcvP['nom_cont']))
	$var_recv['nom_cont'] = $PC->rcvP['nom_cont'];
if (isset($PC->rcvP['prenom_cont']))
	$var_recv['prenom_cont'] = $PC->rcvP['prenom_cont'];
if (isset($PC->rcvP['fonction_cont']))
	$var_recv['fonction_cont'] = $PC->rcvP['fonction_cont'];
if (isset($PC->rcvP['tel_cont']))
	$var_recv['tel_cont'] = $PC->rcvP['tel_cont'];
if (isset($PC->rcvP['fax_cont']))
	$var_recv['fax_cont'] = $PC->rcvP['fax_cont'];
if (isset($PC->rcvP['mob_cont']))
	$var_recv['mob_cont'] = $PC->rcvP['mob_cont'];
if (isset($PC->rcvP['mail_cont']))
	$var_recv['mail_cont'] = $PC->rcvP['mail_cont'];
if (isset($PC->rcvP['comm_cont']))
	$var_recv['comm_cont'] = $PC->rcvP['comm_cont'];

if (($action == 'modif')or($action == 'modifpop'))
{
	$req = "UPDATE contact SET ";
	foreach ($var_recv as $key => $value)
		$req .= $key." = '".$value."', ";
	$bddtmp->makeRequeteFree($req."id_cont = '".$id_cont."' WHERE id_cont = '".$id_cont."'");
	$bddtmp->process();
}

if (($action == 'new')or($action == 'newpop'))
{
	foreach ($var_recv as $key => $value)
	{
		$req_tete  .= $key.", ";
		$req_corps .= "'".$value."', ";
	}
	$bddtmp->makeRequeteFree("INSERT INTO contact (".$req_tete." id_cont) VALUES (".$req_corps." '');");
	$bddtmp->process();
	$bddtmp->makeRequeteFree("SELECT id_cont FROM contact ORDER BY id_cont DESC LIMIT 1");
	$res = $bddtmp->process();
	$id_cont = $res[0]['id_cont'];
	if ($action == 'new')
		$_SERVER["HTTP_REFERER"] = 'prospec/Contact.php?id_cont='.$id_cont;
}

if ($action == 'supp')
{
	$bddtmp->makeRequeteFree("DELETE FROM appel WHERE contact_app = '".$id_cont."';");
	$bddtmp->process();
	$bddtmp->makeRequeteFree("DELETE FROM contact WHERE id_cont = '".$id_cont."';");
	$bddtmp->process();
}

if ($action == 'move')
{
	$bddtmp->makeRequeteFree("UPDATE contact SET entreprise_cont = ".$PC->rcvP['entreprise_cont']." WHERE id_cont = '".$id_cont."';");
	$bddtmp->process();
}

if (($action == 'modifpop')or
    ($action == 'newpop')or
    ($action == 'supp')or
    ($action == 'move'))
{
	echo "<HTML><HEAD>
	     </HEAD><body onLoad=\"zuno.popup.close();window.location='prospec/fiche.php?id_ent=".$ent_cont."';\"></body>";
}
else
{
	header("Location: ".$_SERVER["HTTP_REFERER"]);
	exit();
}
?>
