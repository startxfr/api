<?php
/*#########################################################################
#
#   name :       PageCreate.php
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


if ($PC->rcvP['action'] != '') {
    // partie action
    if ($PC->rcvP['action'] == 'create') {
	//Analyse de l'ID
	if($PC->rcvP['id_pg'] == '') {
	    $mess_err1 .= $GLOBALS['Tx4Lg']['PageErrorID'];
	}
	else {
	    $PC->rcvP['id_pg'] = FileCleanFileName($PC->rcvP['id_pg']);
	    $in['id_pg']= $PC->rcvP['id_pg'];
	    $dbConnexion = new Bdd();
	    $dbConnexion->makeRequeteAuto('ref_page',$in);
	    $testID = $dbConnexion->process();
	    if($testID[0]['id_pg'] != '') {
		$mess_err1 .= $GLOBALS['Tx4Lg']['PageErrorIDExist'];
	    }
	}
	//Analyse de la page php
	if($PC->rcvP['page_pg'] != '') {
	    $PC->rcvP['page_pg'] = FileCleanFileName($PC->rcvP['page_pg']);
	    $in['page_pg']= $PC->rcvP['page_pg'];
	    $dbConnexion = new Bdd();
	    $dbConnexion->makeRequeteAuto('ref_page',$in);
	    $testPage = $dbConnexion->process();
	    if($testPage[0]['id_pg'] != '') {
		$mess_err1 .= $GLOBALS['Tx4Lg']['PageErrorPageExist'];
	    }
	}
	//Analyse du droit d'accès requit
	if($PC->rcvP['usedroit'] == 'no') {
	    $PC->rcvP['droit_pg'] = '';
	}
	else {
	    if($PC->rcvP['droit_pg'] == '') {
		$PC->rcvP['droit_pg'] = '';
	    }
	}

	$PC->rcvP['parent_pg'] = $PC->rcvP[$PC->rcvP['channel_pg'].'_parent_pg'];

	//Analyse des champs obligatoires en fonction du contexte de langue
	if ($_SESSION["language"] == $GLOBALS['LANGUE']['default']) {
	    if($PC->rcvP['nom_pg'] == '') {
		$mess_err1 .= $GLOBALS['Tx4Lg']['PageErrorNom'];
	    }
	    if($PC->rcvP['header_pg'] == '') {
		$mess_err1 .= $GLOBALS['Tx4Lg']['PageErrorHeader'];
	    }
	}
	//on gère les upload
	if($PC->rcvF['img_pg']['tmp_name'] != '') {
	    if(FileMoveUploaded($PC->rcvF['img_pg']['tmp_name'],
	    $PC->rcvF['img_pg']['name'],
	    $GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_page'])) {
		$PC->rcvP['img_pg'] = $PC->rcvF['img_pg']['name'];
	    }
	    else {
		$PC->rcvP['img_pg'] = '';
	    }
	}
	else {
	    $PC->rcvP['img_pg'] = '';
	}
	if($PC->rcvF['img_menu_pg']['tmp_name'] != '' && $PC->rcvF['img_menu_pg']['tmp_name'] != $PC->rcvF['img_pg']['tmp_name']) {
	    if(FileMoveUploaded($PC->rcvF['img_menu_pg']['tmp_name'],
	    $PC->rcvF['img_menu_pg']['name'],
	    $GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_pagemenu'])) {
		$PC->rcvP['img_menu_pg'] = $PC->rcvF['img_menu_pg']['name'];
	    }
	    else {
		$PC->rcvP['img_menu_pg'] = '';
	    }
	}
	elseif ($PC->rcvF['img_menu_pg']['tmp_name'] != '' && $PC->rcvF['img_menu_pg']['tmp_name'] == $PC->rcvF['img_pg']['tmp_name']) {
	    copy($GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_page'].$PC->rcvF['img_pg']['name'], $GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_pagemenu'].$PC->rcvF['img_menu_pg']['name']);
	    $PC->rcvP['img_menu_pg'] = $PC->rcvF['img_menu_pg']['name'];
	}
	else {
	    $PC->rcvP['img_menu_pg'] = '';
	}

	if($mess_err1 == '') {
	    PageAdminToolkit::DBRecord($PC->rcvP, TRUE);
	    header("Location: PageManage.php ");
	}
	else {
	    $sortie = new PageAdminPortlet($_SESSION["language"]);
	    $sortie->SetMessage($mess_err1);
	    $sortie->Type("CREATE", $PC->rcvP);
	}
    }
    else {
	$sortie = new PageAdminPortlet($_SESSION["language"]);
	$sortie->Type("CREATE");
    }
}
else {
    $sortie = new PageAdminPortlet($_SESSION["language"]);
    $sortie->Type("CREATE", $PC->rcvG);
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie->process());
$out->Process();

?>
