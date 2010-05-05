<?php
/*#########################################################################
#
#   name :       XmlCache.inc
#   desc :       library for XML form creation
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class provided to make an xml cache from a data base
 */
class XmlCache {
    /** Name of the cache file. */
    var $cacheName;
    /** file URI from root project to cache file. */
    var $fileURI;

    /** Constructor. */
    function __construct($name = '') {
	$this->cacheName = $name;
	$this->fileURI = $this->GetFileURI();
    }

    /** set name. */
    function switchName($name) {
	$this->cacheName = $name;
	$this->fileURI = $this->GetFileURI();
    }

    /**
     * Create cache fileURI according to cache name.
     * @return cache fileURI and path from root project
     */
    function GetFileURI() {
	return $GLOBALS['REP']['tmp'].$this->cacheName.$GLOBALS['EXT']['cache'];
    }

    /** Delete all xml cache files. */
    function flushCache() {
	Logg::loggerInfo('XmlCache::flushCache() ~ Nettoyage du cache XML','',__FILE__.'@'.__LINE__);
	rm($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp']."*");
    }

    /** Delete the xml cache file of the present instance. */
    function flushMe() {
	if($this->doesCacheExist()) {
	    Logg::loggerInfo('XmlCache::flushMe() ~ nettoyage du fichier de cache '.$this->fileURI,'',__FILE__.'@'.__LINE__);
	    unlink($GLOBALS['REP']['appli'].$this->fileURI);
	}
    }

    /** Return True if the cache file already exist. */
    function doesCacheExist() {
	return file_exists($GLOBALS['REP']['appli'].$this->fileURI);
    }

    function process() {
	if ( $this->doesCacheExist() ) {
	    return $this->fileURI;
	}
	else {
	    $this->createXml();
	    return $this->fileURI;
	}
    }
}

?>
