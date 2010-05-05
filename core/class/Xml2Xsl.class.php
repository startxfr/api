<?php
/*#########################################################################
#
#   name :       xml2xsl.inc
#   desc :       library for XML processing
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for transforming XML document into HTML document using an XSL
 * stylesheet
 */
class Xml2Xsl {
    /** XML file used for transformation. */
    var $xml;
    /** XML file path used for this transformation. */
    var $xmlFilePath;
    /** XSL file used for transformation. */
    var $xsl;
    /** XSL file path used for this transformation. */
    var $xslFilePath;
    /** XSL parameters to use. */
    var $xslparam;
    /** XSL processor. */
    var $proc;
    /** Array of key to use for postProcess. */
    var $postIn;
    /** Array of content to use for postProcess. */
    var $postOut;

    /**
     * Constructor.
     */
    function __construct() {
	$this->xml		= new DomDocument($GLOBALS['CACHEXML']['version'],$GLOBALS['CACHEXML']['encoding']);
	$this->dynXml		= FALSE;
	$this->xsl		= new DomDocument($GLOBALS['CACHEXML']['version'],$GLOBALS['CACHEXML']['encoding']);
	$this->proc 		= new XsltProcessor();
	//$this->proc->registerPhpFunctions();
	$this->xslparam		= '';
    }

    /**
     * XML comming from a file.
     */
    function xmlFile($file = '') {
	if($file{0} == '/') {
	    $filepath = $file;
	}
	else {
	    $filepath = $GLOBALS['REP']['appli'].$file;
	}
	unset($this->xml);
	$this->xml	= new DomDocument($GLOBALS['CACHEXML']['version'],$GLOBALS['CACHEXML']['encoding']);
	$this->xml->load($filepath);
	$this->xmlFilePath = $filepath;
    }

    /**
     * XML comming from content.
     */
    function xmlContent($content = '') {
	if($content == '') {
	    Logg::loggerError('Xml2Xsl::XmlContent() ~ Pas de chaine XML fournie','',__FILE__.'@'.__LINE__);
	}
	else {
	    $this->xml->loadXML($content);
	    $this->xmlFilePath = "VIRTUAL XML";
	}
    }

    /**
     * Force remove of XML file after process
     */
    function setDynXml() {
	$this->dynXml	= TRUE;
    }

    /**
     * If the XSL is given by a file.
     */
    function xslFile($file = '') {
	if($file{0} == '/') {
	    $filepath = $file;
	}
	else {
	    $filepath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['xsl']."defaut/".$file;
	    if ($_SESSION["language"] != $GLOBALS['LANGUE']['default']) {
		$url_testLang = $GLOBALS['REP']['appli'].$GLOBALS['REP']['xsl'].$_SESSION["language"]."/".$file;
		if (@$fp = fopen($url_testLang, "r")) {
		    $filepath = $url_testLang;
		}
	    }
	}
	unset($this->xsl);
	$this->xsl	= new DomDocument($GLOBALS['CACHEXML']['version'],$GLOBALS['CACHEXML']['encoding']);
	$this->xsl->load($filepath);
	$this->xslFilePath = $filepath;
    }

    /**
     * XSL comming from content.
     */
    function xslContent($content = '') {
	if($content == '') {
	    Logg::loggerError('Xml2Xsl::XslContent() ~ Pas de chaine XSL fournie','',__FILE__.'@'.__LINE__);
	}
	else {
	    $this->xsl->loadXML($content);
	    $this->xslFilePath = "VIRTUAL XSL";
	}
    }

    /**
     * XSL Parameters to use.
     */
    function xslParameter($param = '') {
	if($param == '') {
	    Logg::loggerError('Xml2Xsl::xslParameter() ~ pas de paramètres XSL fournis','',__FILE__.'@'.__LINE__);
	}
	elseif(!is_array($param)) {
	    Logg::loggerError('Xml2Xsl::xslParameter() ~ les paramètres ne sont pas sous forme de tableau',$param,__FILE__.'@'.__LINE__);
	}
	else {
	    foreach ($param as $key => $val) {
		$this->xslparam[$key] = $val;
	    }
	}
    }

    /**
     * Process templating on the XML/XSL result
     * before outputing content
     */
    function postProcess($data) {
	if(is_array ($data)) {
	    $input = array();
	    $output= array();
	    foreach ($data as $in => $out) {
		$this->postIn[] = "/\#\#\#\[".$in."\]\#\#\#/";
		$this->postOut[] = $out;
	    }
	}
    }

    /**
     * Process transformation according to actual configuration.
     */
    function Process() {
	$this->proc->importStylesheet($this->xsl);
	if(is_array($this->xslparam)) {
	    $logParameter = " [ PARAM: ";
	    foreach ($this->xslparam as $key => $val) {
		$this->proc->setParameter('',$key,$val);
		$logParameter .= $key.":".$val."; ";
	    }
	    $logParameter .= "]";
	}

	//echo "<br/>".$this->xslFilePath;
	$generate_time_begin = microtime(true);
	$output = $this->proc->transformToDoc($this->xml)->saveXML();
	$generate_time_end = microtime(true);
	$time = $generate_time_end-$generate_time_begin;
	$output = trim(str_replace("<?xml version=\"".$GLOBALS['CACHEXML']['version']."\" encoding=\"".$GLOBALS['CACHEXML']['encoding']."\" standalone=\"yes\"?>", "",$output));
	if ($GLOBALS['LOG']['DisplayDebug']) {
	    $GLOBALS['LogXsltProcess'][] = $this->xslFilePath.$logParameter." => ". $this->xmlFilePath;
	    $GLOBALS['LogXsltProcessTime'] = $GLOBALS['LogXsltProcessTime']+$time;
	}

	if($this->dynXml) {
	    unlink($this->xmlFilePath);
	}

	if(is_array($this->postIn) and is_array($this->postOut)) {
	    $sortie = preg_replace($this->postIn, $this->postOut, $output);
	    $output = $sortie;
	}
	return html_entity_decode($output);
    }
}

?>
