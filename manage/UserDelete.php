<?php
/*#########################################################################
#
#   name :       UserManage.php
#   desc :       interface to manage users
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
loadPlugin(array('UserAdmin'));

/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*-----------------------------------------------------------------------*/

$portlet = new UserAdminPortlet();


// affichage du resultat de la reponse
if (($PC->rcvP['action'] == 'remove')and($PC->rcvP['id'] != '')) {
    $portlet->SetMessage('Est-vous sur de vouloir supprimer définitivement cet utilisateur ?');
    $portlet->Type('DELETE', $PC->rcvP);
    $content = $portlet->process();
}
elseif (($PC->rcvP['valider'] == 'remove') and ($PC->rcvP['bouton'] == 'Confirmer')) {
    UserAdminToolkit::DBDelete($PC->rcvP['login'], $PC->rcvP['img']);
    header("Location: UserManage.php ");
}
else  header("Location: UserManage.php ");

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();

?>
