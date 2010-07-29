<?php
/*#########################################################################
#
#   name :       FileInfo.php
#   desc :       enter Gnose personal main page
#   categorie :  page
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
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
//print_r($PC);
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
if($PC->rcvG['id'] != '') {
    if ($PC->rcvG['pid'] != '') {
	if ($PC->rcvP['action'] == 'confirm') {
	    // supression
	    PageAdminToolkit::PageDBRemoveDocument($PC->rcvG['id'],$PC->rcvG['pid']);
	    echo "<html><body><script language=\"javascript\">window.location.href = '".$_SESSION['reftmp']."';zuno.popup.close();</script></body></html>";
	    unset($_SESSION['reftmp']);
	}
	else {
	    $_SESSION['reftmp'] = $_SERVER['HTTP_REFERER'];
	    $xml->cache = new PageXMLCache($SESSION['language']);
	    $xml->cache->setCacheFile($PC->rcvG['pid']);
	    $xml = $xml->cache->Process();

	    //définition des parametre a passer a la feuille XSL
	    $varinXSL['id']	= $PC->rcvG['id'];
	    $output = new Xml2Xsl();
	    $output->xslFile("admin/PageAdminDeleteFile.xsl");
	    $output->xmlFile($xml);
	    $output->xslParameter($varinXSL);
	    $sortie = stripslashs($output->Process());

	    $out->AddBodyContent($sortie);
	    echo $out->DisplayHeader();
	    echo $out->DisplayBodyContent();
	}
    }
    else {
	echo "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";
    }
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/
$out->CreateDebug();
?>






