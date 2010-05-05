<?php
/*#########################################################################
#
#   name :       PageXMLCache.inc
#   desc :	 Class Page
#   categorie :  Page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

class PageXMLCache {
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
    var $docID;
    /** file URI from root project to cache file. */
    var $fileURI;
    /** CacheXML class used for this object. */
    var $XmlCache;
    /** Language to use for this cache. */
    var $Lang;

    /** Constructor. */
    function __construct($lang = '') {
	$this->table 		= 'page';
	$this->tableSuffix 	= '_pg';
	$this->translatable 	= array('nom','desc','header');
	$this->datable 		= array('modif_date'=>'shortdetail','create_date'=>'simple','stat_date'=>'simple');
	if ($lang == '') {
	    $this->Lang = $GLOBALS['LANGUE']['default'];
	}
	else {
	    $this->Lang = $lang;
	}
	$this->XmlCache 	= new XmlCache();
	$this->cacheName= $this->table.'.'.$this->docID.'.'.$this->Lang;
    }

    /** Set another language to use. */
    function setLang($lang) {
	if ($lang != '') {
	    $this->Lang = $lang;
	    $this->cacheName= $this->table.'.'.$this->docID.'.'.$this->Lang;
	    $this->XmlCache->switchName($this->cacheName);
	    $this->fileURI	 = $this->XmlCache->GetFileURI();
	}
    }

    /** set ID selected. */
    function setCacheFile($id = '') {
	if ($id != '') {
	    $this->docID = $id;
	    $this->cacheName= $this->table.'.'.$this->docID.'.'.$this->Lang;
	}
	$this->XmlCache->switchName($this->cacheName);
	$this->fileURI	 = $this->XmlCache->GetFileURI();
    }


    function createXml() {
	$dbConnexion = new Bdd();
	$newCacheContent = new Xml();
	$resultat = array();
	if ($this->cacheName != '') {
	    $newCacheContent->getXmlFile($GLOBALS['REP']['cache_template'].$this->table.'.detail.xml');
	    $dbConnexion->makeRequeteSelect($this->table, 'id'.$this->tableSuffix, $this->docID);
	    $result = $dbConnexion->process();
	    $lapage = $result[0];
	    $lapage = stripslashs($lapage);
	    $lapageContent = $lapage["content".$this->tableSuffix];
	    foreach($lapage as $idlp => $vallp) {
		$lapage[$idlp] = SXhtmlentities($vallp, ENT_QUOTES);
	    }
	    $lapage["content".$this->tableSuffix] = stripslashs($lapageContent);

	    if($lapage["page".$this->tableSuffix] == '') {
		$lapage["page".$this->tableSuffix] = "page.php?id=".$lapage["id".$this->tableSuffix];
	    }
	    // Debut de l'analyse des variables
	    if($lapage['img_menu'.$this->tableSuffix] != '') {
		$lapage['img_menu'.$this->tableSuffix] = $GLOBALS['PAGE']['REP_pagemenu'].$lapage['img_menu'.$this->tableSuffix];
	    }
	    if($lapage['img'.$this->tableSuffix] != '') {
		$lapage['img'.$this->tableSuffix] = $GLOBALS['PAGE']['REP_page'].$lapage['img'.$this->tableSuffix];
	    }
	    //on traite les dates
	    if(count($this->datable) > 0) {
		foreach($this->datable as $idd => $format) {
		    $lapage[$idd.$this->tableSuffix] = DateUniv2Human($lapage[$idd.$this->tableSuffix],$format);
		}
	    }

	    //On traite les champs traduisibles
	    if($this->Lang != $GLOBALS['LANGUE']['default']) {
		if(count($this->translatable) > 0) {
		    foreach($this->translatable as $kkk => $name) {
			$nametrad = $name.$this->tableSuffix.'_'.$this->Lang;
			if($lapage[$nametrad] != '') {
			    $lapage[$name.$this->tableSuffix] = $lapage[$nametrad];
			}
		    }
		}
	    }
	    //on traduit le contenu
	    if ($this->Lang != $GLOBALS['LANGUE']['default']) {
		if(($lapage['content'.$this->tableSuffix.'_'.$this->Lang] == '')and
			($GLOBALS['PAGE']['LANG_TxtNoTrans'])) {
		    Language::LoadInit($this->Lang);
		    $lapage['content'.$this->tableSuffix] = stripslashs("<span class=\"important\">".$GLOBALS['Tx4Lg']['PageErrorTransl']."</span><br/>".$lapage['content'.$this->tableSuffix]);
		    Language::LoadInit($_SESSION["language"]);
		}
		else {
		    $lapage['content'.$this->tableSuffix] = stripslashs($lapage['content'.$this->tableSuffix.'_'.$this->Lang]);
		}
	    }
	    $lapage['content'.$this->tableSuffix.'_entities'] = htmlspecialchars($lapage['content'.$this->tableSuffix]);

	    // Get info for parent page
	    if($lapage['parent'.$this->tableSuffix] != '') {
		$CacheParent = new Xml();
		$CacheParent->getXmlFile($GLOBALS['REP']['cache_template'].$this->table.'.detail.child.xml');

		$var['id'.$this->tableSuffix]	= $lapage['parent'.$this->tableSuffix];
		$dbConnexion->makeRequeteAuto('page',$var);
		$resultparenttmp = $dbConnexion->process();
		$resultparent = $resultparenttmp[0];
		foreach($resultparent as $idlp => $vallp) {
		    $resultparent[$idlp] = SXhtmlentities($vallp, ENT_QUOTES);
		}

		if($resultparent["page".$this->tableSuffix] == '') {
		    $resultparent["page".$this->tableSuffix] = "page.php?id=".$resultparent["id".$this->tableSuffix];
		}
		// Debut de l'analyse des variables
		if($resultparent['img_menu'.$this->tableSuffix] != '') {
		    $resultparent['img_menu'.$this->tableSuffix] = $GLOBALS['PAGE']['REP_pagemenu'].$resultparent['img_menu'.$this->tableSuffix];
		}
		//On traite les champs traduisibles
		if($this->Lang != $GLOBALS['LANGUE']['default']) {
		    if(count($this->translatable) > 0) {
			foreach($this->translatable as $kkk => $name) {
			    $nametrad = $name.$this->tableSuffix.'_'.$this->Lang;
			    if($resultparent[$nametrad] != '') {
				$resultparent[$name.$this->tableSuffix] = $resultat[$nametrad];
			    }
			}
		    }
		}
		$CacheParent->preProcess($resultparent);
		$outttmp = $CacheParent->Process();
		$lapage['parent'] = "<parent>\n\t\t\t".$outttmp."\n\t\t\t</parent>\n\t\t\t";
	    }
	    else {
		$lapage['parent'] = "";
	    }

	    // Get sub-page data
	    $var1['parent'.$this->tableSuffix] = $lapage['id'.$this->tableSuffix];
	    $dbConnexion->makeRequeteAuto('page',$var1,' ORDER BY order_pg, nom_pg ASC');
	    $resultatchilddb = $dbConnexion->process();
	    if(count($resultatchilddb) > 0) {
		$CacheChild = new Xml();
		$CacheChild->getXmlFile($GLOBALS['REP']['cache_template'].$this->table.'.detail.child.xml');

		foreach($resultatchilddb as $kk => $resultatchild1) {
		    foreach($resultatchild1 as $idlp => $vallp) {
			$resultatchild[$idlp] = SXhtmlentities($vallp, ENT_QUOTES);
		    }
		    if($resultatchild["page".$this->tableSuffix] == '') {
			$resultatchild["page".$this->tableSuffix] = "page.php?id=".$resultatchild["id".$this->tableSuffix];
		    }
		    // Debut de l'analyse des variables
		    if($resultatchild['img_menu'.$this->tableSuffix] != '') {
			$resultatchild['img_menu'.$this->tableSuffix] = $GLOBALS['PAGE']['REP_pagemenu'].$resultatchild['img_menu'.$this->tableSuffix];
		    }
		    //On traite les champs traduisibles
		    if($this->Lang != $GLOBALS['LANGUE']['default']) {
			if(count($this->translatable) > 0) {
			    foreach($this->translatable as $kkk => $name) {
				$nametrad = $name.$this->tableSuffix.'_'.$this->Lang;
				if($resultatchild[$nametrad] != '') {
				    $resultatchild[$name.$this->tableSuffix] = $resultatchild[$nametrad];
				}
			    }
			}
		    }
		    $CacheChild->preProcess($resultatchild);
		    $lapage['enfant'] .= $CacheChild->Process();
		    $CacheChild->cleanXml();
		    $CacheChild->getXmlFile($GLOBALS['REP']['cache_template'].$this->table.'.detail.child.xml');
		}
	    }
	    else {
		$lapage['enfant'] = "";
	    }
	    $resultat = $lapage;
	    $resultat["language"] = $this->Lang;
	    $newCacheContent->preProcess($resultat);
	    $out = $newCacheContent->Process();

	    $this->XmlCache->flushMe();
	    $out = "<?xml version='".$GLOBALS['CACHEXML']['version']."' encoding='".$GLOBALS['CACHEXML']['encoding']."'?>".$out;
	    File_Add2File($GLOBALS['REP']['appli'].$this->fileURI,$out);
	}
	else {
	    Logg::loggerError('PageXMLCache::createXml() ~ pas de nom pour le fichier de cache','',__FILE__.'@'.__LINE__);
	}
    }

    /**
     * Process cache search and return cache URI from root dir of the project.
     * @return URI from root directory of the project
     */
    function Process() {
	if ($this->XmlCache->doesCacheExist()) {
	    return $this->fileURI;
	}
	else {
	    $masterLang = $this->Lang;
	    $listesupported = explode(",",$GLOBALS['LANGUE']['supported']);
	    foreach ($listesupported as $val) {
		if ($val != $masterLang) {
		    $this->setLang($val);
		    $this->createXml();
		}
	    }
	    $this->setLang($masterLang);
	    $this->createXml();
	    return $this->fileURI;
	}
    }
}

?>
