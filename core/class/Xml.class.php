<?php
/*#########################################################################
#
#   name :       Xml.inc
#   desc :       library for XML processing
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for Creating, acquire or manipulate XML document
 */
class Xml {
    /** XML content. */
    var $xml;
    /** Array of key to use for preProcess. */
    var $postIn;
    /** Array of content to use for preProcess. */
    var $postOut;

    /**
     * Constructor.
     */
    function __construct() {
	$this->xml 	= '';
    }

    /**
     * Get XML content from a file.
     */
    function getXmlFile($file = '') {
	if($file{0} == '/') {
	    $filepath = $file;
	}
	else {
	    $filepath = $GLOBALS['REP']['appli'].$file;
	}

	if (!($fp = fopen($filepath,"r"))) {
	    Logg::loggerError('XML::getXmlFile() ~ impossible d\'ouvrir le fichier '.$filepath,'',__FILE__.'@'.__LINE__);
	}
	$data = fread($fp, filesize($filepath));
	fclose($fp);
	$this->xml 	= $data;
    }

    /**
     * Add XML content to the xml document.
     */
    function addXml($content = '', $addheader = FALSE) {
	if($addheader) {
	    $content = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>".$this->xml.$content;
	    $this->xml = '';
	}
	$this->xml 	.= $content;
    }

    /**
     * reset XML content.
     */
    function cleanXml() {
	$this->xml 	= '';
    }

    /**
     * Record XML into a file.
     */
    function recordXmlFile($filepath = '') {
	if($filepath == '') {
	    $string = time().rand();
	    $FilePrefix = substr(md5($string), 0, 12);
	    $FileName = $GLOBALS['REP']['tmp'].$FilePrefix.'.xml';
	    $FilePath = $GLOBALS['REP']['appli'].$FileName;
	    File_Add2File($FilePath,$this->xml);
	    return $FileName;
	}
	else {
	    File_Add2File($filepath,$this->xml);
	    return $filepath;
	}
    }

    /**
     * Configure templating on the XML content after generating it
     */
    function preProcess($data) {
	if(is_array ($data)) {
	    unset($this->postIn);
	    unset($this->postOut);
	    foreach ($data as $in => $out) {
		$this->postIn[] = "/\#\#\#\[".$in."\]\#\#\#/";
		$this->postOut[] = $out;
	    }
	}
	else {
	    Logg::loggerError('XML::preProcess() ~ Les données fournient doivent être sous forme de tableau',$data,__FILE__.'@'.__LINE__);
	}
    }

    /**
     * Apply templating on the XML content after generating it
     */
    function doPreProcess() {
	if( is_array($this->postIn) and is_array($this->postOut)) {
	    $sortie = preg_replace($this->postIn, $this->postOut, $this->xml);
	    $this->xml = $sortie;
	}
    }

    /**
     * Process Creation of the XML or just return $this->xml.
     */
    function Process($option = '') {
	$this->doPreProcess();
	return $this->xml;
    }
}


/**
 * Convert SimpleXMLElement object to ISO array
 * Copyright Daniel FAIVRE 2005 - www.geomaticien.com
 * Copyleft GPL license
 *
 * Modifications by Tobias Wantzen
 */
function simplexml2ISOarray($xml) {
    if (get_class($xml) == 'SimpleXMLElement') {
	$attributes = $xml->attributes();
	foreach($attributes as $k=>$v) {
	    if ($v) $a[$k] = (string) $v;
	}
	$x = $xml;
	$xml = get_object_vars($xml);
    }
    if (is_array($xml)) {
	if (count($xml) == 0) return (string) $x; // for CDATA
	foreach($xml as $key=>$value) {
	    $r[$key] = simplexml2ISOarray($value);
	    if (!is_array( $r[$key])) $r[$key] = $r[$key];
	}
	if (isset($a)) $r['@'] = $a;    // Attributes
	return $r;
    }
    return (string) $xml;
}
?>
