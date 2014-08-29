<?php
/*#########################################################################
#
#   name :       Gnose
#   desc :       Gnose central module
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * GnosewcXMLCache.
 * Cache management for XML News description
 * This class is a complete interface for test, acquire, create, and re-generate
 * XML cache file used for XSLT tranformation
 */
class GnosewcXMLCache {
    /** ID of actual SVN Pool used. */
    var $svnPoolID;
    /** SVN Pool context for this cache. */
    var $svnPool;
    /** SVN Directory to analyse for this cache. */
    var $svnDirectory;
    /** SVN file type for this cache. */
    var $infoType;
    /** Name of the cache file. */
    var $cacheName;
    /** file URI from root project to cache file. */
    var $fileURI;
    /** CacheXML class used for this object. */
    var $XmlCache;
    /** Language to use for this cache. */
    var $Lang;
    /** Temporary SVN INFO used for generating XML tree. */
    var $SVNInfo;
    /** Temporary SVN PROP list used for generating XML tree. */
    var $SVNPropList;
    /** Temporary SVN PROP list used for generating XML tree. */
    var $PropListParsed;
    /** Temporary SVN LOG XML inserted after file tree. */
    var $SVNLog;
    /** Temporary Analysed Base path used for generating XML tree. */
    var $basePath;


    /**
     * Set several configuration parameter like table to attack, table suffix,
     * translatable and datable field to transform,...
     * @lang Lang to use for this XML generation
     */
    function __construct($directory = "",$infoType = "",$svnpool = '') {
	set_time_limit(3600);
	ini_set('memory_limit','150M');
	$this->switchSVNPool($directory,$svnpool,$infoType);
	$this->XmlCache = new XmlCache($this->cacheName);
	$this->fileURI	 = $this->XmlCache->GetFileURI();
	$this->Lang = $_SESSION['language'];
    }

    /**
     * Switch SVN pool used
     * @pool New used SVN pool
     */
    function switchSVNPool($directory = '',$pool = '',$infoType = "") {
	if ($pool == '') {
	    $pool = 1;
	}
	if ($directory == '') {
	    $directory	= "WORK";
	}
	$this->svnDirectory	= $directory;
	$this->svnPoolID	= $pool;
	$this->cacheName	= 'WC'.$this->svnPoolID.".".$this->svnDirectory;
	$this->svnPool 		= $GLOBALS['SVN_Pool'.$this->svnPoolID];
	$this->infoType	 	= $infoType;
	if (is_object($this->XmlCache)) {
	    $this->XmlCache->switchName($this->cacheName);
	}
    }

    /**
     * Generate XML cache file relative to this instance configuration.
     * This fonction should be used only via the Process() function who check for
     * an existing cache file
     */
    function createXml() {
	//On Log et on defini les variables de contexte
	$root_cache_name = $this->cacheName;
	$this->basePath = $GLOBALS['SVN_Pool'.$this->svnPoolID]['WorkCopy'].$GLOBALS['SVN_Pool'.$this->svnPoolID]['WorkDir'].$this->svnDirectory;
	//On récupére les info de la WC
	if(($this->infoType == '')
		or($this->infoType == 'Tree')
		or($this->infoType == 'Info')
		or($this->infoType == 'Prop')) {
	    $this->SVNInfo = libSvn::SVNInfo('',$this->svnDirectory,TRUE,$this->svnPoolID);
	}
	//print_r($this->SVNInfo);

	//ON PREPARE LE FICHIER DE CACHE DE L'ARBORESCENCE (Tree)
	if(($this->infoType == '')or($this->infoType == 'Tree')) {
	    $this->cacheName = $root_cache_name.".Tree";
	    $this->XmlCache->switchName($this->cacheName);
	    $this->fileURI	 = $this->XmlCache->GetFileURI();
	    if(is_array($this->SVNInfo)) {
		$resultat 		= $this->SVNInfo['ROOT'];
		$resultat["enfant"]	= $this->CreateXMLTreeChild("[ROOT]");
		$resultat["Name"]	= $this->svnDirectory;

		$out = "<directory root='".$this->basePath."' name='".$resultat["Name"]."'>".$resultat["enfant"]."\n</directory>";
		$out = "\n<gnoseTree lang=\"".$this->Lang
			."\" xmlns:gnose=\"gnose\""
			."\" xmlns:svn=\"http://subversion.tigris.org/"
			."\">\n".$out."\n</gnoseTree>";
	    }
	    $this->XmlCache->flushMe();
	    $out = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>".$out;
	    File_Add2File($GLOBALS['REP']['appli'].$this->fileURI,$out);
	    unset($out);
	}

	//ON PREPARE LE FICHIER DE CACHE DES INFO (Info)
	if(($this->infoType == '')or($this->infoType == 'Info')) {
	    $this->cacheName = $root_cache_name.".Info";
	    $this->XmlCache->switchName($this->cacheName);
	    $this->fileURI	 = $this->XmlCache->GetFileURI();
	    if(is_array($this->SVNInfo) > 0) {
		$resultat 		= $this->SVNInfo['ROOT'];
		$resultat["enfant"]	= $this->CreateXMLInfoChild("[ROOT]");
		$resultat["Name"]	= $this->svnDirectory;

		$out = "<directory root='".$this->basePath."' name='".$resultat["Name"]."'>".$resultat["enfant"]."\n</directory>";
		$out = "\n<gnoseInfo lang=\"".$this->Lang
			."\" xmlns:gnose=\"gnose\""
			."\" xmlns:svn=\"http://subversion.tigris.org/"
			."\">\n".$out."\n</gnoseInfo>";
	    }
	    $this->XmlCache->flushMe();
	    $out = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>".$out;
	    File_Add2File($GLOBALS['REP']['appli'].$this->fileURI,$out);
	    unset($out);
	}

	//ON PREPARE LE FICHIER DE CACHE DES PROPRIETES (Prop)
	if(($this->infoType == '')or($this->infoType == 'Prop')) {
	    $this->SVNPropList = libSvn::SVNPropList('',$this->svnDirectory,TRUE,$this->svnPoolID);
	    $this->cacheName = $root_cache_name.".Prop";
	    $this->XmlCache->switchName($this->cacheName);
	    $this->fileURI	 = $this->XmlCache->GetFileURI();
	    if(is_array($this->SVNInfo)) {
		$this->CreateXMLPropChild("[ROOT]");
		if(is_array($this->PropListParsed)) {
		    foreach($this->PropListParsed as $PropID => $PropFollow) {
			foreach($PropFollow as $PropVal => $FileFollow) {
			    $out .= "\n<prop name=\"".$PropID."\" val=\"".$PropVal."\">";
			    foreach($FileFollow as $File => $File1) {
				$out .= "\n\t<file id=\"".$File."\"/>";
			    }
			    $out .= "\n</prop>";
			}
		    }
		}


		$out = "<directory root='".$this->basePath."' name='".$resultat["Name"]."'>".$out."\n</directory>";
		$out = "\n<gnoseProp lang=\"".$this->Lang
			."\" xmlns:gnose=\"gnose\""
			."\" xmlns:svn=\"http://subversion.tigris.org/"
			."\">\n".$out."\n</gnoseProp>";
	    }
	    $this->XmlCache->flushMe();
	    $out = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>".$out;
	    File_Add2File($GLOBALS['REP']['appli'].$this->fileURI,$out);
	    unset($out);
	}

	//ON PREPARE LE FICHIER DE CACHE HISTORIQUE (History)
	if(($this->infoType == '')or($this->infoType == 'History')) {
	    $this->cacheName = $root_cache_name.".History";
	    $this->XmlCache->switchName($this->cacheName);
	    $this->fileURI	 = $this->XmlCache->GetFileURI();
	    $SVNLog = libSvn::SVNLog('xml',$this->svnDirectory,$this->svnPoolID);
	    $this->XmlCache->flushMe();
	    $out = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>".
		    "\n<gnoseHistory lang=\"".$this->Lang."\">\n".$SVNLog."\n</gnoseHistory>";
	    File_Add2File($GLOBALS['REP']['appli'].$this->fileURI,$out);
	    unset($SVNLog);
	}

	unset($this->SVNInfo);
	unset($this->SVNPropList);
	unset($out);
	$this->cacheName = $root_cache_name.".Tree";
	$this->XmlCache->switchName($this->cacheName);
	$this->fileURI	 = $this->XmlCache->GetFileURI();
    }


    /**
     * Create XML submenu content called by CreateXML()
     */
    function CreateXMLTreeChild($ParentNode) {
	$SVNInfoCopy = null;
	eval("\$SVNInfoCopy = \$this->SVNInfo$ParentNode;");
	if(is_array($SVNInfoCopy)) {
	    foreach($SVNInfoCopy as $nodeID => $NodeVal) {
		if($nodeID == "ROOT") {
		    return $this->CreateXMLTreeChild($ParentNode."['".$nodeID."']");
		}
		elseif(is_array($NodeVal)) {
		    $resultat 	= $NodeVal;
		    if($NodeVal['Node_Kind'] == "directory") {
			$resultat["enfant"]		= $this->CreateXMLTreeChild($ParentNode."['".$nodeID."']");
			$nt = explode("/",$NodeVal['Path']);
			$cn = count($nt)-1;
			$resultat["Name"]		= $nt[$cn];
			$out .= "\n<dir id=\"".$resultat["Path"]."\" name=\"".$resultat["Name"]."\">".$resultat["enfant"]."\n</dir>";
		    }
		    else {
			$out .= "\n\t<file id=\"".$resultat["Path"]."\" name=\"".$resultat["Name"]."\"/>";
		    }
		}
	    }
	    unset($SVNInfoCopy);
	    return $out;
	}
    }

    /**
     * Create XML submenu content called by CreateXML()
     */
    function CreateXMLInfoChild($ParentNode) {
	$SVNInfoCopy = null;
	eval("\$SVNInfoCopy = \$this->SVNInfo$ParentNode;");
	if(is_array($SVNInfoCopy) > 0) {
	    foreach($SVNInfoCopy as $nodeID => $NodeVal) {
		if($nodeID == "ROOT") {
		    return $this->CreateXMLInfoChild($ParentNode."['".$nodeID."']");
		}
		elseif(is_array($NodeVal)) {
		    $resultat 			= $NodeVal;
		    $resultat["Last_Changed_Date"]  = libSvn::SVNDateSvn2human($resultat["Last_Changed_Date"],'shortdetail');
		    if($NodeVal['Node_Kind'] == "directory") {
			$resultat["enfant"]	= $this->CreateXMLInfoChild($ParentNode."['".$nodeID."']");
			$out .= "\n<dir id=\"".$resultat["Path"]."\">".
				"\n\t<size octet=\"".$resultat["Size"]."\" human=\"".$resultat["FormatedSize"]."\"/>".
				"\n\t<modif date=\"".$resultat["Last_Changed_Date"]."\" rev=\"".$resultat["Last_Changed_Rev"]."\" user=\"".$resultat["Last_Changed_Author"]."\"/>".
				"</dir>".$resultat["enfant"];
		    }
		    else {
			$out .= "\n<file id=\"".$resultat["Path"]."\">".
				"\n\t<size octet=\"".$resultat["Size"]."\" human=\"".$resultat["FormatedSize"]."\"/>".
				"\n\t<modif date=\"".$resultat["Last_Changed_Date"]."\" rev=\"".$resultat["Last_Changed_Rev"]."\" user=\"".$resultat["Last_Changed_Author"]."\"/>".
				"</file>";
		    }
		}
	    }
	    unset($SVNInfoCopy);
	    return $out;
	}
    }

    /**
     * Create XML submenu content called by CreateXML()
     */
    function CreateXMLPropChild($ParentNode) {
	$SVNPropCopy = $SVNInfoCopy = null;
	eval("\$SVNInfoCopy = \$this->SVNInfo$ParentNode;");
	eval("\$SVNPropCopy = \$this->SVNPropList$ParentNode;");
	if(is_array($SVNInfoCopy) > 0) {
	    foreach($SVNInfoCopy as $nodeID => $NodeVal) {
		if($nodeID == "ROOT") {
		    $this->CreateXMLPropChild($ParentNode."['".$nodeID."']");
		}
		elseif(is_array($NodeVal)) {
		    if($NodeVal['Node_Kind'] == "directory") {
			$this->CreateXMLPropChild($ParentNode."['".$nodeID."']");
		    }
		    if(count($SVNPropCopy[$nodeID]) > 0) {
			foreach($SVNPropCopy[$nodeID] as $idprop => $propval) {
			    if(!is_array($propval)) {
				$this->PropListParsed[$idprop][$propval][$NodeVal['Path']] = $NodeVal['Path'];
			    }
			}
		    }
		}
	    }
	}
    }


    /**
     * Process cache search and return cache URI from root dir of the project.
     * If no cahce file is found, generate it and return this URI
     * @return URI from root directory of the project
     */
    function Process() {
	if ($this->infoType == '') {
	    $root_name = $this->cacheName;
	    $cacheName = $root_name.".Tree";
	    $this->XmlCache->switchName($cacheName);
	    $this->fileURI = $this->XmlCache->GetFileURI();
	    if (!$this->XmlCache->doesCacheExist()) {
		$this->createXml();
	    }
	    return $GLOBALS['REP']['tmp'].$root_name;
	}
	else {
	    $cacheName = $this->cacheName.".".$this->infoType;
	    $this->XmlCache->switchName($cacheName);
	    $this->fileURI = $this->XmlCache->GetFileURI();
	    if (!$this->XmlCache->doesCacheExist()) {
		$this->createXml();
	    }
	    return $this->fileURI;
	}
    }
}
?>
