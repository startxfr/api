<?php
/*#########################################################################
#
#   name :       page.delete.php
#   desc :       Delete a existing page
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
loadPlugin(array('PageAdmin'));

/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*-----------------------------------------------------------------------*/

$portlet = new PageAdminPortlet();



/////////////////////////////////

if ($PC->rcvP['bouton'] == 'Annuler') {
    header("Location: PageManage.php ");
}
elseif ($PC->rcvP['action'] == 'delete') {
    //analyse des données renvoyées
    if($PC->rcvP['id'] != '') {
	PageAdminToolkit::DBDelete($PC->rcvP['id'], $PC->rcvP['channel']);
	header("Location: PageManage.php ");
    }
    else {
	header("Location: PageManage.php ");
    }
}
elseif ($PC->rcvG['id'] != '') {
    $portlet->SetSelected($PC->rcvG['id']);
    $portlet->Type('DELETE');
    $content = $portlet->process();
}
else {
    header("Location: PageManage.php ");
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();


?>
