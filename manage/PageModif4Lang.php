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


if ($PC->rcvP['action'] == 'modif')
{
	//Analyse des champs obligatoires en fonction du contexte de langue
	if ($PC->rcvP['language'] != $GLOBALS['LANGUE']['default'])
	{
		if($PC->rcvP['nom_pg'] == '')		{ $mess_err1 .= $GLOBALS['Tx4Lg']['PageErrorNom'];}
		if($PC->rcvP['header_pg'] == '')	{ $mess_err1 .= $GLOBALS['Tx4Lg']['PageErrorHeader'];}
	}

	if($mess_err1 == '')
	{
		$langactive = $_SESSION["language"];
		$_SESSION["language"] = $PC->rcvP['language'];
		//on modifie
		PageAdminToolkit::DBRecord($PC->rcvP,FALSE,TRUE);
		$_SESSION["language"] = $langactive;
		echo "<html><body><script language=\"javascript\">window.location.reload();zuno.popup.close();</script></body></html>";
	}

	$sortie = new PageAdminPortlet($_SESSION["language"]);
	$sortie->SetMessage($mess_err1);
	$sortie->SetSelected($PC->rcvP['id_pg']);
	$sortie->Type("MODIFCONTENT");
}
elseif(($PC->rcvG['language'] != '')and($PC->rcvG['id'] != ''))
{
	$sortie = new PageAdminPortlet($PC->rcvG['language']);
	$sortie->SetSelected($PC->rcvG['id']);
	$sortie->Type("MODIFCONTENT");
}
else
{
	echo "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie->process());
echo $out->DisplayHeader();
echo $out->DisplayBodyContent();
echo $out->CreateDebug();
?>
