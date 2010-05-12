<?php
/*#########################################################################
#
#   name :       MenuXMLCache.inc
#   desc :	 Class Page
#   categorie :  Page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

class XMLCache_Menu {
    /** Table. */
    var $table;
    /** Suffix used for this table. */
    var $tableSuffix;
    /** List of row to translate if translation available. */
    var $translatable;
    /** List of date row (key) to transform with DateUniv2Human() with a given type (val). */
    var $datable;
    /** Name of the cache file. */
    var $cacheName;
    /** Specific ID for the given. */
    var $selectedID;
    /** file URI from root project to cache file. */
    var $fileURI;
    /** CacheXML class used for this object. */
    var $XmlCache;
    /** Language to use for this cache. */
    var $Lang;
    /** Channel to use for this menu. */
    var $channel;
    /** menuDepth of this menu. */
    var $menuDepth;

    /** Constructor. */
    function __construct($channel='normal',$lang='') {
	$this->table 		= 'ref_page';
	$this->channel 		= $channel;
	$this->availableChannel = array('normal','prospec','draco', 'produit','pegase','gnose','facturier','admin','iPhone');
	$this->tableSuffix 	= '_pg';
	$this->menuDepth 	= 2;
	$this->translatable 	= array('nom','desc','header');
	$this->datable 		= array('modif_date'=>'shortdetail','create'=>'simple','stat_date'=>'simple');
	if ($lang == '') {
	    $this->Lang = $GLOBALS['LANGUE']['default'];
	}
	else {
	    $this->Lang = $lang;
	}
	$this->XmlCache 	= new XmlCache();
	$this->cacheName= 'menu.'.$this->channel.'.'.$this->Lang;
	$this->XmlCache->switchName($this->cacheName);
	$this->fileURI	 = $this->XmlCache->GetFileURI();
    }

    /** Set another language to use. */
    function setFile($channel = '', $lang = '') {
	if ($lang != '') {
	    $this->Lang = $lang;
	}
	if ($channel != '') {
	    $this->channel = $channel;
	}
	$this->cacheName= 'menu.'.$this->channel.'.'.$this->Lang;
	$this->XmlCache->switchName($this->cacheName);
	$this->fileURI	 = $this->XmlCache->GetFileURI();
    }

    function createXml() {
	$dbConnexion = new Bdd();
	$newCacheContent = new Xml();

	$varsql['channel_pg'] = $this->channel;
	$dbConnexion->makeRequeteAuto($this->table,$varsql,'ORDER BY order_pg, nom_pg ASC');
	$HResult = $dbConnexion->process();
	if(count($HResult) > 0) {
	    foreach($HResult as $kk => $hie) {
		foreach($hie as $idlp => $vallp) {
		    $hiecleaned[$idlp] = SXhtmlentities($vallp, ENT_QUOTES);
		}

		$HierarchyTree[$hie["id".$this->tableSuffix]] = $hie["parent".$this->tableSuffix];
		$HierarchyData[$hie["id".$this->tableSuffix]] = $hiecleaned;
	    }

	    foreach($HierarchyTree as $id => $parent) {
		if($parent == "") {
		    $newCacheContent->cleanXml();
		    $newCacheContent->getXmlFile($GLOBALS['REP']['cache_template'].'menu.xml');

		    $enfant = $this->CreateXMLSearchChild($HierarchyTree,$HierarchyData,$id,$this->menuDepth,$this->menuDepth);
		    // Debut de l'analyse des variables
		    if($HierarchyData[$id]["page".$this->tableSuffix] == '') {
			$HierarchyData[$id]["page".$this->tableSuffix] = "page.php?id=".$HierarchyData[$id]["id".$this->tableSuffix];
		    }
		    if($HierarchyData[$id]['img_menu'.$this->tableSuffix] != '') {
			$HierarchyData[$id]['img_menu'.$this->tableSuffix] = $GLOBALS['PAGE']['REP_pagemenu'].$HierarchyData[$id]['img_menu'.$this->tableSuffix];
		    }
		    if($HierarchyData[$id]['img'.$this->tableSuffix] != '') {
			$HierarchyData[$id]['img'.$this->tableSuffix] = $GLOBALS['PAGE']['REP_page'].$HierarchyData[$id]['img'.$this->tableSuffix];
		    }
		    //on traite les dates
		    if(count($this->datable) > 0) {
			foreach($this->datable as $idd => $format) {
			    $HierarchyData[$id][$idd.$this->tableSuffix] = DateUniv2Human($HierarchyData[$id][$idd.$this->tableSuffix],$format);
			}
		    }
		    //On traite les champs traduisibles
		    if($this->Lang != $GLOBALS['LANGUE']['default']) {
			if(count($this->translatable) > 0) {
			    foreach($this->translatable as $kkk => $name) {
				$nametrad = $name.$this->tableSuffix.'_'.$this->Lang;
				if($HierarchyData[$id][$nametrad] != '') {
				    $HierarchyData[$id][$name.$this->tableSuffix] = $HierarchyData[$id][$nametrad];
				}
			    }
			}
		    }
		    $HierarchyData[$id]["nom".$this->tableSuffix]= addslashes($HierarchyData[$id]["nom".$this->tableSuffix]);
		    $HierarchyData[$id]["header".$this->tableSuffix]= addslashes($HierarchyData[$id]["header".$this->tableSuffix]);
		    $HierarchyData[$id]["desc".$this->tableSuffix]= addslashes($HierarchyData[$id]["desc".$this->tableSuffix]);
		    //$HierarchyData[$id]["content".$this->tableSuffix]= strip_tags($HierarchyData[$id]["content".$this->tableSuffix]);
		    $resultat = $HierarchyData[$id];
		    $resultat["enfant"] = $enfant;
		    $newCacheContent->preProcess($resultat);
		    $out .= $newCacheContent->Process();
		}
	    }
	    $out = "\n<menutree lang=\"".$this->Lang."\">\n".$out."\n</menutree>";
	}
	$this->XmlCache->flushMe();
	$out = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>".$out;
	File_Add2File($GLOBALS['REP']['appli'].$this->fileURI,$out);//,TRUE); pour eviter les ajout recursifs dans le fichier XML
    }

    /**
     * Create XML submenu content called by CreateXML()
     */
    function CreateXMLSearchChild($HierarchyTree,$HierarchyData,$GivenParentPage = "",$limit = "",$beginLimit = "6") {
	if ($limit == 0) {
	    return '';
	}
	else {
	    $newCacheContent = new Xml();
	    if($limit != "") {
		$limit = $limit-1;
		$limdiff = $beginLimit-$limit;
		$espaceur = str_repeat("\t", $limdiff);
		$espaceur = "\n\t\t".$espaceur;
	    }
	    foreach ($HierarchyTree as $id => $parent) {
		if($parent == $GivenParentPage) {
		    $newCacheContent->cleanXml();
		    $newCacheContent->getXmlFile($GLOBALS['REP']['cache_template'].'menu.xml');

		    $enfant = $this->CreateXMLSearchChild($HierarchyTree,$HierarchyData,$id,$this->menuDepth,$this->menuDepth);
		    // Debut de l'analyse des variables
		    if($HierarchyData[$id]["page".$this->tableSuffix] == '') {
			$HierarchyData[$id]["page".$this->tableSuffix] = "page.php?id=".$HierarchyData[$id]["id".$this->tableSuffix];
		    }
		    if($HierarchyData[$id]['img_menu'.$this->tableSuffix] != '') {
			$HierarchyData[$id]['img_menu'.$this->tableSuffix] = $GLOBALS['PAGE']['REP_pagemenu'].$HierarchyData[$id]['img_menu'.$this->tableSuffix];
		    }
		    if($HierarchyData[$id]['img'.$this->tableSuffix] != '') {
			$HierarchyData[$id]['img'.$this->tableSuffix] = $GLOBALS['PAGE']['REP_page'].$HierarchyData[$id]['img'.$this->tableSuffix];
		    }
		    //on traite les dates
		    if(count($this->datable) > 0) {
			foreach($this->datable as $idd => $format) {
			    $HierarchyData[$id][$idd.$this->tableSuffix] = DateUniv2Human($HierarchyData[$id][$idd.$this->tableSuffix],$format);
			}
		    }
		    //On traite les champs traduisibles
		    if($this->Lang != $GLOBALS['LANGUE']['default']) {
			if(count($this->translatable) > 0) {
			    foreach($this->translatable as $kkk => $name) {
				$nametrad = $name.$this->tableSuffix.'_'.$this->Lang;
				if($HierarchyData[$id][$nametrad] != '') {
				    $HierarchyData[$id][$name.$this->tableSuffix] = $HierarchyData[$id][$nametrad];
				}
			    }
			}
		    }
		    $HierarchyData[$id]["nom".$this->tableSuffix]= addslashes($HierarchyData[$id]["nom".$this->tableSuffix]);
		    $HierarchyData[$id]["header".$this->tableSuffix]= addslashes($HierarchyData[$id]["header".$this->tableSuffix]);
		    $HierarchyData[$id]["desc".$this->tableSuffix]= addslashes($HierarchyData[$id]["desc".$this->tableSuffix]);
		    //$HierarchyData[$id]["content_pg"]= strip_tags($HierarchyData[$id]["content_pg"]);
		    $resultat = $HierarchyData[$id];
		    $resultat["enfant"] = $enfant;
		    $newCacheContent->preProcess($resultat);
		    $out .= $newCacheContent->Process();
		}
	    }
	    if ($out == '') {
		return '';
	    }
	    else {
		return "\n<submenu>".$out."\n</submenu>";
	    }
	}
    }



    /** Process cache search for all available channels
     * @return last channel URI
     */
    function processAllChannel($lang = '') {
	if ($lang != '') $this->Lang = $lang;
	foreach($this->availableChannel as $channel) {
	    $this->setFile($channel,$this->Lang);
	    $xml = $this->Process();
	}
	return $xml;
    }


    /**
     * Process cache search and return cache URI
     * @return URI from root directory of the project
     */
    function Process() {
	if ($this->XmlCache->doesCacheExist()) {
	    return $this->fileURI;
	}
	else {
	    $this->createXml();
	    return $this->fileURI;
	}
    }
}

?>
