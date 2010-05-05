<?php
/*#########################################################################
#
#   name :       FormProcess.ScanSearch.php
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
//print_r($PC->rcvP);exit;
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
if ($PC->rcvP['Code'] != "") {
    $secondCode = substr($PC->rcvP['Code'],3);
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("SELECT id_fact FROM facture WHERE  id_fact = '".((int) $PC->rcvP['Code'])."' or   id_fact = '".((int) $secondCode)."'");
    $fact = $bddtmp->process();
    if(count($fact) > 0)
	header("Location: facturier/Facture.php?id_fact=".$fact[0]['id_fact']);
    else {
	$bddtmp->makeRequeteFree("SELECT id_cmd FROM commande WHERE  id_cmd = '".$PC->rcvP['Code']."'");
	$cmd = $bddtmp->process();
	if(count($cmd) > 0)
	    header("Location: pegase/Commande.php?id_cmd=".$PC->rcvP['Code']);
	else {
	    $bddtmp->makeRequeteFree("SELECT id_dev FROM devis WHERE  id_dev = '".$PC->rcvP['Code']."'");
	    $dev = $bddtmp->process();
	    if(count($dev) > 0)
		header("Location: draco/Devis.php?id_dev=".$PC->rcvP['Code']);
	    else {
		$bddtmp->makeRequeteFree("SELECT id_aff FROM affaire WHERE  id_aff = '".$PC->rcvP['Code']."'");
		$aff = $bddtmp->process();
		if(count($aff) > 0)
		     header("Location: draco/Affaire.php?id_aff=".$PC->rcvP['Code']);
		else header("Location: ".$_SERVER["HTTP_REFERER"]);
	    }
	}

    }

}
else header("Location: ".$_SERVER["HTTP_REFERER"]);
?>