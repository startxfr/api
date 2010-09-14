<?php
/*#########################################################################
#
#   name :       index.php
#   desc :       Homepage
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

$uinf = GetClientBrowserInfo();
if($uinf[0] == 'iPod' or $uinf[0] == 'iPad' or $uinf[0] == 'iPhone') {
    header("Location: ".$GLOBALS['CHANNEL_iPhone']['path']);
    exit;
}
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext();
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
$sortie = "";
if(is_array($_SESSION) and array_key_exists('user',$_SESSION)) {
    header("Location: Bureau.php");
}
else header("Location: Login.php");

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/
$out->AddBodyContent($sortie);
$out->Process();
?>
