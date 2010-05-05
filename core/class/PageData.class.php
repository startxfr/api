<?php
/*#########################################################################
#
#   name :       PageData.inc
#   desc :	 Class Page
#   categorie :  Page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for geting detailed information about a given page
 * This class actually only support DB input format but should
 * provide other storage tools such as XML
 */
class PageData {
    /** Page ID. */
    var $id;
    /** Language to use. */
    var $lang;
    /** where you can find array describing this page. */
    var $Data;
    /** XML description of the page. */
    var $DataXML;
    /** Suffix use in page table. */
    var $DBSuffix;
    /** Cache interface. */
    var $cache;


    /**
     * Constructor.
     * Define requested variable
     * Page ID can also be set here by giving $idpage
     * Set absURI, translatable, lang and langExt
     */
    function __construct($idpage="",$lang='') {
	$this->id 		= $idpage;
	if ($lang == '') {
	    $this->lang = $GLOBALS['LANGUE']['default'];
	}
	else {
	    $this->lang = $lang;
	}
	$this->cache = new PageXMLCache($this->lang);
    }

    /**
     * Select the given page ID.
     */
    function SelectID($idpage) {
	$this->id = $idpage;
    }

    /**
     * Process page Data search and return array or XML as requested.
     */
    function Process() {
	if($this->id == "") {
	    Logg::loggerError('PageData::Process() ~ aucun identifiant de page n\'as été trouvé','',__FILE__.'@'.__LINE__);
	}
	else {
	    $this->cache->setCacheFile($this->id);
	    return $this->cache->Process();
	}
    }

}

?>
