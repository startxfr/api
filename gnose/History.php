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
loadPlugin(array('GnosewcXMLCache'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('gnose');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
//On récupère le cache XML
$toto	= new GnosewcXMLCache('WORK','History');
$XML	= $toto->process();
$toto	= new GnosewcXMLCache('WORK','History');
$XML	= $toto->process();
//On charge le processeur XSL
$output = new Xml2Xsl();

if ($PC->rcvG['rev'] != '') {
    $varinXSL['Rev'] = $PC->rcvG['rev'];
    $output->xslFile("GNOSE/History.xsl");
    $output->xmlFile($XML);
    $output->xslParameter($varinXSL);
    $sortie = stripslashs($output->Process());
    if ($sortie == '') {
	//modifie le fichier de cache utilisé
	$toto->switchSVNPool('PERSO','',"History");
	$XML	= $toto->process();
	$output->xmlFile($XML);
	$sortie = $output->Process();
	if ($sortie == '') {
	    //modifie le fichier de cache utilisé
	    $toto->switchSVNPool('ARCHIVES','',"History");
	    $XML	= $toto->process();
	    $output->xmlFile($XML);
	    $sortie = $output->Process();
	}
    }
}
else {
    //On crée le XSL
    $output->xslFile("GNOSE/HistoryList.xsl");
    $output->xmlFile($XML);
    $sortie = stripslashs($output->Process());
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/
$out->AddBodyContent($sortie);
$out->Process();
?>