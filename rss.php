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

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext();
$PC->GetVarContext();
$PC->GetSessionContext();

//var_dump($PC);

$rss = new zunoRSS();
if($PC->rcvG['type'] == 'my')
    if($_SESSION['user']['id'] != '')
	$rss->generateMyRss($_SESSION['user']['id']);
    else $rss->generateUnauthorizedRss();
elseif($PC->rcvG['type'] == 'client')
    if($rss->checkClientToken($PC->rcvG['token']))
	$rss->generateClientRss($PC->rcvG['token']);
    else $rss->generateUnauthorizedRss();
else $rss->generatePublicRss();
$rss->display();

?>
