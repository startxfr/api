<?php
/*#########################################################################
#
#   name :       Login.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
	include ('../inc/conf.inc');		// Declare global variables from config files
	include ('../inc/core.inc');		// Load core library
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('gnose');
$PC->GetVarContext();
$PC->GetChannelContext();
$PC->GetPageContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,'',false);

//Si annultion de la session, on affche un message
if($PC->rcvG["mess"] == 'delsess')		{ $message = $GLOBALS['Tx4Lg']['LoginErrorExpire'];}
elseif($PC->rcvG["mess"] == 'badsess')	{ $message = $GLOBALS['Tx4Lg']['LoginErrorSession'];}
else
	{
	if($PC->rcvP["login"] == '')		{ $authentification = 'NO_LOGIN';}
	elseif($PC->rcvP["pwd"] == '')		{ $authentification = 'NO_PWD';}
	elseif(($PC->rcvP["login"] != '')
	and($PC->rcvP["pwd"] != ''))		{ $AuthTest = new SessionUser($PC->channel);
						  $authentification = $AuthTest->TestUser($PC->rcvP["login"],$PC->rcvP["pwd"]); }

	if($authentification == 'OK')		{ $AuthTest->CreateSession($PC->rcvP["login"]); }
	elseif($authentification == 'BAD_LOGIN'){ $message = $GLOBALS['Tx4Lg']['LoginErrorBadLog']; }
	elseif($authentification == 'BAD_RIGHT'){ $message = $GLOBALS['Tx4Lg']['LoginErrorNoRight'];}
	elseif($authentification == 'INACTIVE_USER'){ $message = $GLOBALS['Tx4Lg']['LoginErrorInactive'];}
	elseif($authentification == 'BAD_PWD')	{ $message = $GLOBALS['Tx4Lg']['LoginErrorBadPw'];  }
	elseif($authentification == 'NO_PWD')	{ $message = $GLOBALS['Tx4Lg']['LoginErrorBadMDP']; }
	elseif($authentification == 'NO_LOGIN')	{ $message = $GLOBALS['Tx4Lg']['LoginErrorBadID'];  }
	}

$input['login']		= $PC->rcvG["login"];

if($message != '')	$input['message']	= "<span class='important'>".$message."</span>";
else 				$input['message'] = '';

// We add content and Process display
$out->AddBodyContent("");
$out->AddBodyContent(templating('Login',$input));
$out->Process();
?>
