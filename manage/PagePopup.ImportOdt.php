<?php
/*#########################################################################
#
#   name :       PagePopupImportOdt.php
#   desc :       Popup for inserting content from ODT files
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');               // Declare global variables from config files
include ('../inc/core.inc');               // Load core library
loadPlugin(array('PageAdmin'));

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$sortie  = new PageAdminPortlet($_SESSION["language"]);

if (($PC->rcvP['action'] == 'Importer')and($PC->rcvG['id'] != '')) {
    //Analyse du droit d'accès requit
    if($PC->rcvF['odt']['error'] == 0) {
	if(FileGetExtention($PC->rcvF['odt']['name']) == "odt") {
	    $ZIPname 	= $PC->rcvF['odt']['name'];
	    $TmpPath	= $GLOBALS['REP']['tmp'].
		    'OdtImport'.
		    substr(md5(rand(1000,9999)+rand(1000,9999)),0,8).
		    '/';
	    @rm($GLOBALS['REP']['appli'].$TmpPath);
	    mkdir($GLOBALS['REP']['appli'].$TmpPath);

	    loadPlugin(array('pclzip'));
	    ini_set('memory_limit',60000000);
	    ini_set('max_execution_time',180);
	    $odt = new PclZip($PC->rcvF['odt']['tmp_name']);


	    if ($odt->extract(PCLZIP_OPT_PATH,$GLOBALS['REP']['appli'].$TmpPath) == 0) {
		$sortie->SetMessage($odt->errorInfo(true));
		$content = $sortie->Type("IMPORTODT");
	    }
	    else {
		$PhotoStockTmp = $GLOBALS['REP']['appli'].$TmpPath."Pictures/";
		$images = FileDirectoryDetail($PhotoStockTmp);
		if (is_array($images)) {
		    $PhotoStock = $GLOBALS['REP']['appli'].$GLOBALS['PAGE']['REP_phototeque'].$PC->rcvG['id']."/";
		    $PhotoStockURI = $GLOBALS['PAGE']['REP_phototeque'].$PC->rcvG['id']."/";
		    if(!is_dir($PhotoStock)) {
			mkdir($PhotoStock);
		    }
		    foreach($images as $key => $img) {
			copy($PhotoStockTmp.$img['nom'],$PhotoStock.$img['nom']);
		    }
		}
		$xml = $GLOBALS['REP']['appli'].$TmpPath."content.xml";
		$data['PhotoStock'] = "http://".$GLOBALS['URL']['appli'].$PhotoStockURI;

		$output = new Xml2Xsl();
		$output->xslFile("admin/PageOdt2Xhtml.xsl");
		$output->xmlFile($xml);
		$output->xslParameter($data);
		$Thumb 	= "<img src='../".$TmpPath."Thumbnails/thumbnail.png'/>";
		$xhtml  = stripslashs($output->Process());

		$data['file'] = $PC->rcvF['odt']['name'];
		$data['xmlFile'] = $xml;
		$data['Thumb'] = $Thumb;
		$data['content'] = $xhtml;
		$content = $sortie->Type("IMPORTODTConfirm",$data);
	    }
	}
	else {
	    $sortie->SetMessage($GLOBALS['Tx4Lg']['PageErrorDocNotODT']);
	    $content = $sortie->Type("IMPORTODT");
	}
    }
    else {
	$sortie->SetMessage($GLOBALS['Tx4Lg']['FileUploadError_'.$PC->rcvF['odt']['error']]);
	$content = $sortie->Type("IMPORTODT");
    }
}
elseif (($PC->rcvP['action'] == 'Confirmer')and($PC->rcvG['id'] != '')) {
    $output = new Xml2Xsl();
    $output->xslFile("admin/PageOdt2Xhtml.xsl");
    $output->xmlFile($PC->rcvP['xmlFile']);
    $output->xslParameter($PC->rcvP);
    $xhtml  = stripslashs($output->Process());

    $modifData['editor'] = $xhtml;
    $modifData['id_pg'] = $PC->rcvG['id'];

    PageAdminToolkit::DBRecord($modifData);
    echo "<html><body><script language=\"javascript\">window.location.reload();zuno.popup.close();</script></body></html>";
}
else {
    $content = $sortie->Type("IMPORTODT");
}


/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->DoNavBar = FALSE;
echo $out->DisplayHeader();
echo $out->DisplayBodyContent();
echo $out->CreateDebug();
?>
