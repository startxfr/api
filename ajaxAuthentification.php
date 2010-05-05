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

//Si annultion de la session, on affiche un message
if($PC->rcvG["mess"] == 'delsess')		{ $message = $GLOBALS['Tx4Lg']['LoginErrorExpire'];}
elseif($PC->rcvG["mess"] == 'badsess')	{ $message = $GLOBALS['Tx4Lg']['LoginErrorSession'];}

if($PC->rcvP['action'] == 'doLogin')
{
	if($PC->rcvP['login'] == '')		$authentification = 'NO_LOGIN';
	elseif($PC->rcvP['pwd'] == '')	$authentification = 'NO_PWD';
	elseif(($PC->rcvP['login'] != '')
	       and($PC->rcvP['pwd'] != '')) { $AuthTest = new SessionUser($PC->channel);
						  	  $authentification = $AuthTest->TestUser($PC->rcvP["login"],$PC->rcvP["pwd"]); }

	if($authentification == 'OK') 				$AuthTest->CreateSession($PC->rcvP["login"],false);
	elseif($authentification == 'BAD_LOGIN')	$message = $GLOBALS['Tx4Lg']['LoginErrorBadLog'];
	elseif($authentification == 'BAD_RIGHT')	$message = $GLOBALS['Tx4Lg']['LoginErrorNoRight'];
	elseif($authentification == 'INACTIVE_USER')$message = $GLOBALS['Tx4Lg']['LoginErrorInactive'];
	elseif($authentification == 'BAD_PWD')		$message = $GLOBALS['Tx4Lg']['LoginErrorBadPw'];
	elseif($authentification == 'NO_PWD')		$message = $GLOBALS['Tx4Lg']['LoginErrorBadMDP'];
	elseif($authentification == 'NO_LOGIN')		$message = $GLOBALS['Tx4Lg']['LoginErrorBadID'];
}
elseif($PC->rcvP['action'] == 'doLogout')
{
	$Session = new Session();
	$Session->CatchSession();
	if($PC->rcvP['doSave'] == 'true')
		$Session->recordSessionData();
	$Session->Deconnect();
}


if($message != '') {
	$return['code'] = false;
	$return['mess'] = $message;
}
else {
	$return['code'] = true;
	$return['mess'] = '';
}


header('Content-Type: text/javascript; charset: UTF-8');
header('X-JSON: '.json_encode($return));
?>