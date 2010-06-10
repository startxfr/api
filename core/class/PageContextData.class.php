<?php
/*#########################################################################
#
#   name :       PageContextData.inc
#   desc :	 Class Page
#   categorie :  PageContext
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for geting detailed information about requested page
 * This class is provide to get all informations relevant to the given
 * page and put them in the page context environement
 */
class PageContextData {
    /** Channel to use for this Context. */
    var $channel;
    /** Page ID. */
    var $id;
    /** XML Cached filed URI. */
    var $cacheXML;
    /** Language to use. */
    var $lang;
    /** Array with synthetic page data for context process. */
    var $Data;
    /** Array with result from Database. */
    var $SQLOutputPage;


    /**
     * Constructor.
     * Define id for this context according to given id
     * or by using PageGetID() method
     */
    function __construct($channel='normal',$lang='') {
	$this->channel = $channel;
	if ($lang == '') {
	    $this->lang = $GLOBALS['LANGUE']['default'];
	}
	else {
	    $this->lang = $lang;
	}
	$this->id = $this->PageGetID();

    }

    /**
     * changeLang.
     * Change general language
     */
    function changeLang($lang) {
	if ($lang == '') {
	    $this->lang = $GLOBALS['LANGUE']['default'];
	}
	else {
	    $this->lang = $lang;
	}
    }

    /**
     * Find the page ID by analysing request URI and Script name
     */
    function PageGetID() {
	// Analyse requested page and script
	$result = explode("/", $_SERVER["SCRIPT_NAME"]);
	$nbres = count ($result);
	$nbres--;
	$result1 = explode("/", $_SERVER["REQUEST_URI"]);
	$nbres1 = count ($result1);
	$nbres1--;
	$uriref = $result[$nbres];
	$uriref1 = $result1[$nbres1];

	$DBconnection = new Bdd();
	if(($uriref == 'page.php')or($uriref == 'popup.php')or($uriref == 'test.php')) {
	    $searchvar['id_pg'] = $_GET['id'];
	    $sql = $DBconnection->makeRequeteAuto('ref_page',$searchvar);
	    $tmpresult = $DBconnection->process();
	    if($tmpresult[0]['id_pg'] == '') {
		$lid = "NotFound";
		Logg::loggerError('PageContextData::PageGetID() ~ Aucun identifiant de page n\'as pu être trouvé',$sql,__FILE__.'@'.__LINE__);
	    }
	    else {
		$this->SQLOutputPage = $tmpresult[0];
		$lid = $_GET['id'];
	    }
	}
	else {
	    $var['page_pg'] 	= $uriref1;
	    $var['channel_pg']	= $this->channel;
	    $DBconnection->makeRequeteAuto('ref_page',$var);
	    $resultat = $DBconnection->process();
	    //Si pas de result à partir du REQUEST_URI, on test avec SCRIPT_NAME
	    if(count($resultat) == 0) {
		$var['page_pg'] 	= $uriref;
		$var['channel_pg'] 	= $this->channel;
		$DBconnection->makeRequeteAuto('ref_page',$var);
		$resultat1 = $DBconnection->process();
		if(count($resultat1) > 0) {
		    $this->SQLOutputPage = $resultat1[0];
		    $lid = $resultat1[0]['id_pg'];
		}
		else {
		    $lid = "NotFound";
		    Logg::loggerError('PageContextData::PageGetID() ~ Aucun identifiant de page n\'as pu être trouvé','',__FILE__.'@'.__LINE__);
		}
	    }
	    else {
		$this->SQLOutputPage = $resultat[0];
		$lid = $resultat[0]['id_pg'];
	    }
	}
	return $lid;
    }

    function getData() {
	Logg::loggerNotice('PageContextData::getData() ~ Recherche des informations pour la page '.$this->id,'',__FILE__.'@'.__LINE__);
	$resultat = array();
	$translatable 	= array('nom_pg','desc_pg','header_pg');
	$datable 	= array('modif_date_pg'=>'shortdetail','create_date_pg'=>'simple');

	if($this->SQLOutputPage == '') {
	    $dbConnexion = new Bdd();
	    $dbConnexion->makeRequeteSelect('ref_page', 'id_pg', $this->id);
	    $result = $dbConnexion->process();
	    $this->SQLOutputPage = $result[0];
	}
	$childpage = $this->SQLOutputPage;

	if($childpage["page_pg"] == '') {
	    $childpage["page_pg"] = "page.php?id=".$childpage["id_pg"];
	}

	//On traite la traduction des ï¿½lï¿½ments
	if ($this->lang != $GLOBALS['LANGUE']['default']) {
	    if($childpage["nom_pg_".$this->lang] != '') {
		$childpage["nom_pg"] = $childpage["nom_pg_".$this->lang];
	    }
	    if($childpage["desc_pg_".$this->lang] != '') {
		$childpage["desc_pg"] = $childpage["desc_pg_".$this->lang];
	    }
	    //on traduit le contenu
	    if(($childpage['content_pg_'.$this->lang] == '')
		    and($childpage['content_pg'] != '')
		    and($GLOBALS['PAGE']['LANG_TxtNoTrans'])) {
		$childpage['content_pg'] = "<span class=\"important\">".
			$GLOBALS['Tx4Lg']['PageErrorTransl'].
			"</span>";
		$childpage['content_pg'];
	    }
	    else {
		$childpage['content_pg'] = $childpage['content_pg_'.$this->lang];
	    }
	}

	// Debut de l'analyse des variables
	if($childpage['img_menu_pg'] != '') {
	    $childpage['img_menu_pg'] = $GLOBALS['PAGE']['REP_pagemenu'].$childpage['img_menu_pg'];
	}
	if($childpage['img_pg'] != '') {
	    $childpage['img_pg'] = $GLOBALS['PAGE']['REP_page'].$childpage['img_pg'];
	}
	//on traite les dates
	if(count($datable) > 0) {
	    foreach($datable as $idd => $format) {
		$childpage[$idd] = DateUniv2Human($childpage[$idd],$format);
	    }
	}
	//On traite les champs traduisibles
	if($this->lang != $GLOBALS['LANGUE']['default']) {
	    if(count($this->translatable) > 0) {
		foreach($this->translatable as $kkk => $name) {
		    $nametrad = $name.'_'.$this->lang;
		    if($resultat[$nametrad] != '') {
			$childpage[$name] = $resultat[$nametrad];
		    }
		}
	    }
	}

	return $childpage;
    }

    /**
     * Process page Data search and return array or XML as requested.
     * whatever the return requested, $this->Data and $this->DataXML
     * will be set
     */
    function Process() {
	$process = new PageData($this->id,$this->lang);
	$this->cacheXML = $process->Process();
	$this->Data = $this->getData();
	return $this->cacheXML;
    }
}
?>
