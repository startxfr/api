<?php
/*#########################################################################
#
#   name :       UserManage.php
#   desc :       Manage user
#   categorie :  management page
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('UserAdmin'));

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
/**************************************************************************/

$portlet = new UserAdminPortlet();

// affichage du resultat de la reponse
if ($PC->rcvP['action'] == 'search') {
    $_SESSION['tmpusersearch'] = $PC->rcvP;
    $portlet->Type('MANAGEFORM', $PC->rcvP);
    $portlet->Type('MANAGE', $PC->rcvP);
    $content = $portlet->process();
}
elseif ($PC->rcvG['action'] == 'tri') {
    // trier les colonnes si demandé
    if ($PC->rcvG['order'] != '')
	$portlet->setorder($PC->rcvG['order']);
    $portlet->Type('MANAGEFORM', $_SESSION['tmpusersearch']);
    $portlet->Type('MANAGE', $_SESSION['tmpusersearch']);
    $content = $portlet->process();
}
else {
    unset($_SESSION['tmpusersearch']);
    $portlet->Type('MANAGEFORM', $PC->rcvP);
    $portlet->Type('MANAGE',$_SESSION['tmpusersearch']);
    $content = $portlet->process();
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();



?>
