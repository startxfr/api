




<?php
/*#########################################################################
#
#   name :       Xsl.inc
#   desc :       library for XSL processing
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for Creating, acquire or manipulate XSL document
 */
class Xsl {
    /** XSL content. */
    var $xsl;
    /** Array of key to use for preProcess. */
    var $postIn;
    /** Array of content to use for preProcess. */
    var $postOut;

    /**
     * Constructor.
     */
    function __construct() {
	$this->xsl 	= '';
    }

    /**
     * Get XSL content from a file.
     */
    function getXslFile($file = '') {
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

	if (!($fp = fopen($filepath,"r"))) {
	    Logg::loggerError('XSL::getXslFile() ~ Mauvais fichier XSL '.$filepath,'',__FILE__.'@'.__LINE__);
	}
	$data = fread($fp, filesize($filepath));
	fclose($fp);
	$this->xsl 	= $data;
    }

    /**
     * Add XSL content to the xsl document.
     */
    function addXsl($content = '', $addheader = FALSE) {
	if($addheader) {
	    $content = $this->xsl.$content;
	    $this->xsl = '';
	}
	$this->xsl 	.= $content;
    }

    /**
     * reset XSL content.
     */
    function cleanXsl() {
	$this->xsl 	= '';
    }

    /**
     * Record XSL into a file.
     */
    function recordXslFile($filepath = '') {
	if($filepath == '') {
	    $string = time().rand();
	    $FilePrefix = substr(md5($string), 0, 12);
	    $FileName = $GLOBALS['REP']['tmp'].$FilePrefix.'.xsl';
	    $FilePath = $GLOBALS['REP']['appli'].$FileName;
	    File_Add2File($FilePath,$this->xsl);
	    return $FilePath;
	}
	else {
	    File_Add2File($filepath,$this->xsl);
	    return $filepath;
	}
    }

    /**
     * Configure templating on the XSL content after generating it
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
	    Logg::loggerError('CORE::XSL::preProcess() ~ l\'entrée n\'est pas un tableau',$data,__FILE__.'@'.__LINE__);
	}
    }

    /**
     * Apply templating on the XSL content after generating it
     */
    function doPreProcess() {
	if( is_array($this->postIn) and is_array($this->postOut)) {
	    $sortie = preg_replace($this->postIn, $this->postOut, $this->xsl);
	    $this->xsl = $sortie;
	}
    }

    /**
     * Process Creation of the XSL or just return $this->xsl.
     */
    function Process($option = '') {
	$this->doPreProcess();
	return $this->xsl;
    }
}

?>
