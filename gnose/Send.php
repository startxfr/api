<?php
/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZView/DocumentView', 'Send/Send', 'ZunoRenduHTML'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('gnose');
$PC->GetSessionContext();
$PC->GetVarContext();

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if($PC->rcvP['action'] == 'send') {
    if(array_key_exists('path', $PC->rcvP)) {
        $path = explode("/", $PC->rcvP['path']);
        $PC->rcvP['fichier'] = $path[count($path)-1];
        $PC->rcvP['partie'] = "send";
        $PC->rcvP['dir_aff'] = $path[count($path)-2]."/";
        $PC->rcvP['path'] = substr($PC->rcvP['path'], 0, strrpos($PC->rcvP['path'],"/"))."/";
    }
    if($PC->rcvP['mail'] == "")
            $PC->rcvP['mail'] = $PC->rcvP['mailaff'];
    $send = new Sender($PC->rcvP);
    $rs = $send->send();
    if($rs[0] == 1)
	 echo '<span class="importantgreen">'.$rs[1].'</span>';
    else echo '<span class="important">'.$rs[1].'</span>';
    exit;
}
else{
    $data['path'] = $PC->rcvG['file'];
    $path = explode("/",$PC->rcvG['file']);
    $data['fichier'] = $path[count($path)-1];
    $view = new documentViewRepertoire();
    echo $view->popupSendMail("email", $data);
    exit;
}
?>
