<?php
/*#########################################################################
#
#   name :       Menu.inc
#   desc :       Class Menu
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

class Menu {
    /** XML Content. */
    var $xml;
    /** XSL file to use for transformation. */
    var $xsl;
    /** Parameters to use for XSL. */
    var $XslParam;
    /** Parameters to use for XSL Post processing. */
    var $XslParamPost;
    /** Page ID to use for selection. */
    var $PageSelect;
    /** Language to use for translated data for this menu. */
    var $Lang;
    /** Right limitation for this menu. Default set . */
    var $Droit;
    /** Channel to use for this menu. */
    var $channel;
    /** Cache interface for XML menu. */
    var $cache;

    /**
     * Constructor.
     */
    function __construct($channel='') {
	$this->channel	= $channel;
	if(($_SESSION["language"] != $GLOBALS['LANGUE']['default'])and($_SESSION["language"] != ''))
	    $this->Lang = $_SESSION["language"];
	else    $this->Lang = $GLOBALS['LANGUE']['default'];
	if($_SESSION['user']['right'] != '')
	    $this->Droit = $_SESSION['user']['right'];
	else    $this->Droit = 100;
	$this->cache	= new XMLCache_Menu($this->channel,$this->Lang);
    }

    /**
     * Set page to select.
     */
    function SetSelected($pageid) {
	$this->PageSelect = $pageid;
    }

    /**
     * Select XSL file to use for transformation.
     */
    function Xsl($xsl) {
	$this->xsl = $xsl;
    }

    /**
     * Type is a shortcut to configure various kind of menu.
     */
    function Type($type) {
	$this->cache->setFile($this->channel,$this->Lang);
	$this->xml = $this->cache->Process();

	$varin['root_path'] = ($this->channel != 'normal' ? '../' : '');
	//Changement pour la specificité du menu dynamique GNOSE
	if($type == 'header') {
	    $this->cache->processAllChannel($this->Lang);
	    $this->Xsl("BodyHeaderMenu.xsl");
	    $varin['droit'] = $this->Droit;
	    $varin['tmpPath'] = "../../tmp/";
	    $varin['tmpMenuPrefix'] = "menu.";
	    $varin['tmpMenuSuffix'] = ".cache.xml";
	    $varin['selectedChannel'] = $this->channel;
	    $varin['lang'] = $this->Lang;
	    $this->XslParam = $varin;
	    $this->xml = $GLOBALS['REP']['appli']."conf/permanent/channel.xml";
	}
	else {
	    $this->Xsl("normal/BodyHeaderMenu.xsl");
	}
    }

    /**
     * Process CreateXML() if not already done. Then check for XSL stylesheet
     * and process transformation
     */
    function Process($OutType = '') {
	if($this->xml == '') {
	    Logg::loggerError('Menu::Process() ~ Aucun fichier XML à traiter','',__FILE__.'@'.__LINE__);
	}
	if($this->xsl == '') {
	    Logg::loggerError('Menu::Process() ~ Aucun fichier XSL de traitement','',__FILE__.'@'.__LINE__);
	}
	if($OutType == 'XML') {
	    // take care to set header('Content-type: application/xml'); before output document
	    $XML = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>\n";
	    $XML.= "<?xml-stylesheet type=\"text/xsl\" href=\"".$this->xsl."\"?>\n";
	    $XML.= $this->xml;
	    return $XML;
	}
	else {
	    $output = new Xml2Xsl();
	    $output->xslFile($this->xsl);
	    $output->xmlFile($this->xml);
	    if(is_array($this->XslParam)) {
		$output->xslParameter($this->XslParam);
	    }
	    if(is_array($this->XslParamPost)) {
		$output->postProcess($this->XslParamPost);
	    }
	    $result = $output->Process();
	    $result = str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>",'',$result);
	    $result = str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".strtolower($GLOBALS['CACHEXML']['encoding'])."'?>",'',$result);
	    $result = str_replace("<?xml version='".$GLOBALS['CACHEXML']['version']."'?>",'',$result);
	    return $result;
	}
    }
}

?>
