<?php
/*#########################################################################
#
#   name :       profil.create.php
#   desc :       interface to manage users
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('inc/conf.inc');	// Declare global variables from config files
include ('inc/core.inc');	// Load core library
loadPlugin(array('User'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext();
$PC->GetFullContext();

/*-----------------------------------------------------------------------*/
if($PC->rcvG['id'] != '')
	$id = $PC->rcvG['id'];
elseif($PC->rcvP['id'] != '')
	$id = $PC->rcvP['id'];
elseif($_SESSION["user"]["id"] != '')
	$id = $_SESSION["user"]["id"];
else  $id = "";

if (($PC->rcvP['type'] == 'popup')or
    ($PC->rcvG['type'] == 'popup'))
{
	if ($id != '')
		echo UserToolkit::UserPortlet($id);
	else  echo "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";
}
else {
	if ($id != '') {
		$out = new PageDisplay($PC->channel);
		$out->headerHTML->initCalendar();
		$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
		$out->AddBodyContent(UserToolkit::UserPortlet($id));
		$out->Process();
	}
	else  echo "<html><body><script language=\"javascript\">history.back();</script></body></html>";
}
?>