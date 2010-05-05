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


if ($PC->rcvP['valider'] == 'save')
{
	//Analyse de la civilité
	if($PC->rcvP['civ'] == '')
		$PC->rcvP['civ'] = 'Mr';
	//Analyse du LOGIN
	if($PC->rcvP['login'] == '')
		$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorID'];
	//Analyse du nom
	if($PC->rcvP['nom'] == '')
		$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorNom'];
	//Analyse du mot de passe
	if($PC->rcvP['pass'] != '') {
		if($PC->rcvP['pass1'] != $PC->rcvP['pass'])
			$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorPw2'];
		elseif($PC->rcvP['pass1'] == '')
			$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorPw1'];
	}
	//Analyse du mail
	if($PC->rcvP['mail'] == '')
		$mess_err1 .= $GLOBALS['Tx4Lg']['UserErrorMail'];
	//Analyse de la langue
	if($PC->rcvP['lang'] == '')
		$PC->rcvP['lang'] = $_SESSION["language"];
	//Analyse de l'image
	if($PC->rcvF['image']['tmp_name'] != '')
	{
		$ext = FileGetExtention($PC->rcvF['image']['name']);
		if(FileMoveUploaded($PC->rcvF['image']['tmp_name'],
				    $PC->rcvP['login'].".".$ext,
				    $GLOBALS['REP']['appli'].'admin/droit/')) {
			$PC->rcvP['image'] = $PC->rcvP['login'].".".$ext;
		}
	}
	if($PC->rcvP['img_del'] == '1')
		$PC->rcvP['image'] = '';

	if ($mess_err1 == '') {
		UserAdminToolkit::DBRecord($PC->rcvP);
		header("Location: UserManage.php");
	}
	else {
		$PC->rcvP['image'] = $PC->rcvP['imguri'];
		$portlet->setMessage($mess_err1);
		$portlet->Type('MODIF',$PC->rcvP);
		$content = $portlet->process();
	}
}
elseif ($PC->rcvP['id'] != '')
{
	$bddtmp = new Bdd();
	$bddtmp->makeRequeteSelect('user','login',$PC->rcvP['id']);
	$res = $bddtmp->process();
	$portlet->Type('MODIF',$res[0]);
	$content = $portlet->process();
}
else  header("Location: UserManage.php ");


/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();

?>
