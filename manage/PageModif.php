<?php
/*#########################################################################
#
#   name :       PageModif.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('PageAdmin'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if ($PC->rcvP['action'] == 'modif') {
    //Analyse du droit d'accès requit
    if($PC->rcvP['usedroit'] == 'no')
	$PC->rcvP['droit_pg'] = '';
    else {
	if($PC->rcvP['droit_pg'] == '')
	    unset($PC->rcvP['droit_pg']);
    }

    $PC->rcvP['parent_pg'] = $PC->rcvP[$PC->rcvP['channel_pg'].'_parent_pg'];

    //on gère les upload
    if($PC->rcvF['img_pg']['tmp_name'] != '') {
	$newname 	= FileCleanFileName($PC->rcvF['img_pg']['name']);
	if(FileMoveUploaded($PC->rcvF['img_pg']['tmp_name'],
	$newname,
	$GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_page']))
	    $PC->rcvP['img_pg'] = $newname;
	else $PC->rcvP['img_pg'] = '';
    }
    if($PC->rcvF['img_menu_pg']['tmp_name'] != '') {
	$newname 	= FileCleanFileName($PC->rcvF['img_menu_pg']['name']);
	if(FileMoveUploaded($PC->rcvF['img_menu_pg']['tmp_name'],
	$newname,
	$GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_pagemenu']))
	    $PC->rcvP['img_menu_pg'] = $newname;
	else $PC->rcvP['img_menu_pg'] = '';
    }
    if($PC->rcvF['file_doc']['tmp_name'] != '') {
	$newname 	= FileCleanFileName($PC->rcvF['file_doc']['name']);
	if(FileMoveUploaded($PC->rcvF['file_doc']['tmp_name'],
	$newname,
	$GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_doc'])) {
	    $PC->rcvP['file_doc'] = $newname;
	    $PC->rcvP['page_doc'] = $PC->rcvP['id_pg'];
	    if($PC->rcvP['nom_doc'] == '')
		$PC->rcvP['nom_doc'] = $PC->rcvF['file_doc']['name'];
	    if($PC->rcvP['order_doc'] == '')
		$PC->rcvP['order_doc'] = '10';
	    PageAdminToolkit::DBRecordDocument($PC->rcvP,TRUE);
	    PageAdminToolkit::DBRecord($PC->rcvP);
	}
    }
    if($mess_err1 == '') {
	//on modifie
	PageAdminToolkit::DBRecord($PC->rcvP);
	header("Location: PageManage.php ");
    }

    $sortie = new PageAdminPortlet($_SESSION["language"]);
    $sortie->SetMessage($mess_err1);
    $sortie->SetSelected($PC->rcvP['id_pg']);
    $sortie->Type("MODIF");
}
elseif($PC->rcvP['id_pg'] != '' and $PC->rcvP['action'] == 'modifContent') {
    if($PC->rcvP['nom_pg'] == '')
	$message = 'merci de saisir un nom de page<br/>';
    if($PC->rcvP['header_pg'] == '')
	$message .= 'merci de saisir un titre de page<br/>';
    $sortie = new PageAdminPortlet($_SESSION["language"]);
    if($message == '')
	PageAdminToolkit::DBRecord($PC->rcvP);
    else $sortie->SetMessage($message);
    $sortie->SetSelected($PC->rcvP['id_pg']);
    $sortie->Type("MODIF");
}
elseif($PC->rcvG['id'] != '' and $PC->rcvG['action'] == 'publish') {
    $toto['id_pg'] = $PC->rcvG['id'];
    $toto['actif_pg'] = "1";
    PageAdminToolkit::DBRecord($toto);
    header("Location: PageManage.php ");
}
elseif($PC->rcvG['id'] != '') {
    $sortie = new PageAdminPortlet($_SESSION["language"]);
    $sortie->SetSelected($PC->rcvG['id']);
    $sortie->Type("MODIF");
}
else {
    header("Location: PageManage.php ");
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie->process());
$out->Process();

?>
