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

/*-----------------------------------------------------------------------*/

$portlet = new UserAdminPortlet();


if ($PC->rcvP['valider'] == 'save') {
    //Analyse de la civilité
    if($PC->rcvP['civ'] == '') {
	$PC->rcvP['civ'] = 'Mr';
    }
    //Analyse du LOGIN
    if($PC->rcvP['login'] == '') {
	$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorID'];
    }
    elseif($PC->rcvP['login'] != FileCleanFileName($PC->rcvP['login'])) {
	$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorBadID'];
    }
    else {
	$bddtmp 	= new Bdd();
	$etat['login']	= $PC->rcvP['login'];
	$bddtmp->makeRequeteAuto("user", $etat);
	$res = $bddtmp->process();
	if ($res[0]['login'] != '') {
	    $mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorIDExist'];
	}
    }
    //Analyse du nom
    if($PC->rcvP['nom'] == '') {
	$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorNom'];
    }
    //Analyse du droit
    if($PC->rcvP['droit'] == '0') {
	$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorDroitAdmin'];
    }
    //Analyse du prenom
    if($PC->rcvP['prenom'] == '') {
	$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorPrenom'];
    }
    //Analyse du mot de passe
    if($PC->rcvP['pass'] == '') {
	$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorPw3'];
    }
    else {
	if($PC->rcvP['pass1'] != $PC->rcvP['pass']) {
	    $mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorPw2'];
	}
	elseif($PC->rcvP['pass1'] == '') {
	    $mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorPw1'];
	}
    }
    //Analyse du mail
    if($PC->rcvP['mail'] == '') {
	$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorMail'];
    }
    //Analyse de la langue
    if($PC->rcvP['lang'] == '') {
	$PC->rcvP['lang'] = $_SESSION["language"];
    }
    //Analyse de l'image
    if($PC->rcvF['image']['tmp_name'] != '') {
	if(FileMoveUploaded($PC->rcvF['image']['tmp_name'],
	$PC->rcvF['image']['name'],
	$GLOBALS['REP']['appli'].'admin/droit/')) {
	    $PC->rcvP['image'] = $PC->rcvF['image']['name'];
	}
	else {
	    $PC->rcvP['image'] = '';
	}
    }
    else {
	$PC->rcvP['image'] = '';
    }

    if ($mess_err1 == '') {
	UserAdminToolkit::DBRecord($PC->rcvP);
	header("Location: UserManage.php");
    }
    else {
	$portlet->setMessage($mess_err1);
	$portlet->Type("CREATE", $PC->rcvP);
	$content = $portlet->process();
    }
}
else {
    $portlet->Type('CREATE');
    $content = $portlet->process();
}



/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();

?>
