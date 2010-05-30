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
include ('inc/conf.inc');		// Declare global variables from config files
include ('inc/core.inc');		// Load core library
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext();
$PC->GetVarContext();
$PC->GetChannelContext();
$PC->GetPageContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,'',false);

//Si annultion de la session, on affche un message
if(strlen($PC->rcvG['token']) == 32) {
    loadPlugin(array('ZModels/TokenModel'));
    $token = new TokenModel();
    $data = $token->getInfos($PC->rcvG['token']);
    if($data[1][0]['used_token'] == 0) {
        $token->used($PC->rcvG['token']);
        $AuthTest = new SessionUser($PC->channel);
        $AuthTest->GetDBUser($data[1][0]['user_token']);
        $AuthTest->CreateSession($data[1][0]['user_token'], false);

        header('Location:'.substr($data[1][0]['action_token'],1));
        exit;
    }

}
if($PC->rcvG["mess"] == 'delsess') {
    $message = $GLOBALS['Tx4Lg']['LoginErrorExpire'];
}
elseif($PC->rcvG["mess"] == 'badsess') {
    $message = $GLOBALS['Tx4Lg']['LoginErrorSession'];
}
elseif(count($PC->rcvP) == 0) {

}
else {
    if($PC->rcvP["login"] == '') {
        $authentification = 'NO_LOGIN';
    }
    elseif($PC->rcvP["pwd"] == '') {
        $authentification = 'NO_PWD';
    }
    elseif(($PC->rcvP["login"] != '')
            and($PC->rcvP["pwd"] != '')) {
        $AuthTest = new SessionUser($PC->channel);
        $authentification = $AuthTest->TestUser($PC->rcvP["login"],$PC->rcvP["pwd"]);
    }

    if($authentification == 'OK') {
        $AuthTest->CreateSession($PC->rcvP["login"], true, $PC->rcvP['to']);
    }
    elseif($authentification == 'BAD_LOGIN') {
        $message = $GLOBALS['Tx4Lg']['LoginErrorBadLog'];
    }
    elseif($authentification == 'BAD_RIGHT') {
        $message = $GLOBALS['Tx4Lg']['LoginErrorNoRight'];
    }
    elseif($authentification == 'INACTIVE_USER') {
        $message = $GLOBALS['Tx4Lg']['LoginErrorInactive'];
    }
    elseif($authentification == 'BAD_PWD') {
        $message = $GLOBALS['Tx4Lg']['LoginErrorBadPw'];
    }
    elseif($authentification == 'NO_PWD') {
        $message = $GLOBALS['Tx4Lg']['LoginErrorBadMDP'];
    }
    elseif($authentification == 'NO_LOGIN') {
        $message = $GLOBALS['Tx4Lg']['LoginErrorBadID'];
    }
}

$input['login']		= $PC->rcvG["login"];

if(isset($message) and $message != '')
    $input['message']	= "<span class='important'>".$message."</span>";
else $input['message'] = '';

$input['to'] = ($PC->rcvG['from'] != "") ? $PC->rcvG['from'] : $PC->rcvP['to'];
// We add content and Process display
$out->AddBodyContent("");
$out->AddBodyContent(templating('Login',$input));
$out->Process();
?>
