<?php
/*#########################################################################
#
#   name :       Login.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id: Login.php 1915 2008-12-13 01:46:04Z cl $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('inc/conf.inc');		// Declare global variables from config files
include ('inc/core.inc');		// Load core library

$PC = new PageContext();
$PC->GetVarContext();
$PC->GetChannelContext();

$return['code'] = false;
$return['mess'] = 'Erreur';
if($PC->rcvP['action'] == 'doSaveZBoxState') {
    $PC->GetSessionContext();
    if($PC->rcvP['zboxid'] != '' and $PC->rcvP['state'] != '')
	$_SESSION['ZBoxState'][$PC->rcvP['zboxid']] = $PC->rcvP['state'];
    exit;
}

header('Content-Type: text/html; charset: UTF-8');
header('X-JSON: '.json_encode($return));
?>