<?php
/*#########################################################################
#
#   name :       Page.php
#   desc :	 Class Page
#   categorie :  page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


/**
 * Class for managing page construction and output
 */
class PageDisplay {
    /** HTML Header Object. */
    var $headerHTML;
    /** Set to TRUE if HTML Header already sent. */
    var $headerHTMLSent;
    /** Header Object. */
    var $header;
    /** Body Content. */
    var $bodyContent;
    /** Footer Object. */
    var $footer;
    /** Debug informations if required. */
    var $Debug;
    /** Channel ID to use for this Context. */
    var $Channel;
    /** Language to use for this context. */
    var $Lang;
    /** Array with module to place in header and footer. */
    var $ModulesHF;
    /** Id of the current page. */
    var $PageId;
    /** Create Navigation Bar */
    var $DoNavBar;
    /** Titre de la page */
    var $title;
    /** Titre de la page */
    var $displayTitle;

    /**
     * Constructor.
     * initialize page display and set channel to use
     */
    function __construct($channel='normal') {
	$this->Channel	  = $channel;
	$this->setDisplayTitle();
	// HTML Header object
	// On s'assure qu'il y aura au moins le suffixe de page avec SetTitle()
	$this->headerHTML = new PageDisplayHeader($this->Channel);
	$this->headerHTMLSent = FALSE;
	$this->headerHTML->SetTitle();
	// Header object
	$this->header	  = new PageDisplayBodyHeader($this->Channel);
	// Body content
	//$this->setBodyContentTemplate();
	// Footer object
	$this->footer	  = new PageDisplayBodyFooter($this->Channel);

	if(($_SESSION["language"] != $GLOBALS['LANGUE']['default'])and($_SESSION["language"] != '')) {
	    $this->Lang = $_SESSION["language"];
	}
	else {
	    $this->Lang = $GLOBALS['LANGUE']['default'];
	}

	$this->DoNavBar = $GLOBALS['CHANNEL_'.$this->Channel]['DisplayNavBar'];
    }


    /**
     * Do some basic configuration for a given page
     * set header title, menu selection, description in header,...
     */
    function ConfigureWithPageData($pageData, $pageXML = '',$dislayTitle = true) {
	if ((is_array($pageData))and($pageData['id_pg'] != '')) {
	    $this->PageId = $pageData['id_pg'];
	    $this->GenerateModulesHF($pageData);
	    $this->headerHTML->SetTitle($pageData['header_pg']);
	    if($dislayTitle) $this->setTitle($pageData['header_pg'],$pageData['img_pg']);
	    $this->headerHTML->AddDescription($pageData['desc_pg']);
	    $this->header->PageSelect($pageData);
	    $this->setBodyContentTemplate($pageData['frameset_pg']);
	    if ($pageXML != '') {
		if ($pageData['style_pg'] == '') {
		    $pageData['style_pg'] = "defaut";
		}
		$output = new Xml2Xsl();
		$output->xslFile("BodyContent.Page.".$pageData['style_pg'].".xsl");
		$output->xmlFile($pageXML);
		$param['root_path'] = ($this->Channel != 'normal' ? '../' : '');
		$param['droit'] =  (isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['right'] != '') ? $_SESSION['user']['right'] : '';
		$output->xslParameter($param);
		$this->AddBodyContent($output->Process());
	    }
	}
	else {
	    Logg::loggerError('PageDisplay::ConfigureWithPageData() ~ impossible de traiter la déscription de la page ',$pageData,__FILE__.'@'.__LINE__);
	}
    }

    /**
     * GenerateTools.
     * Create toolbox according given type
     */
    function GenerateModulesHF($pageData) {
	$eval = '';
	$i 		= 0;
	$moduleHF 	= array();
	$dh		= opendir($GLOBALS['REP']['appli'].$GLOBALS['REP']['moduleHF']);
	while(false !== ($filename = readdir($dh))) {
	    if (strstr($filename,$GLOBALS['EXT']['plugin'])) {
		include($GLOBALS['REP']['appli'].$GLOBALS['REP']['moduleHF'].$filename);
		if($moduleHF[$i]['actif']) {
		    $funcName  = "\$eval = new ";
		    $funcName .= $moduleHF[$i]['class'];
		    $funcName .= " (\"";
		    $funcName .= $this->Channel."\",\"".$pageData;
		    $funcName .= "\");";
		    $funcName;
		    eval($funcName);
		    if (method_exists($eval,'DisplayModule')) {
			$moduleHF[$i]['out'] = $eval->DisplayModule();
		    }
		    $moduleOrder[$i] = $moduleHF[$i]['order'];
		}
		$i++;
	    }
	}
	closedir($dh);
	asort($moduleOrder);
	$output = array();
	foreach ($moduleOrder as $key => $val)
	    if(array_key_exists($moduleHF[$key]['position'],$output))
		$output[$moduleHF[$key]['position']] .= $moduleHF[$key]['out'];
	    else $output[$moduleHF[$key]['position']]  = $moduleHF[$key]['out'];

	$this->ModulesHF = $output;
    }


    /**
     * Process HTML Header creation and return result
     */
    function DisplayHeader() {
	return $this->headerHTML->Process();
	$this->headerHTMLSent = TRUE;
    }

    /**
     * Process BODY Header creation and return result
     */
    function DisplayBodyHeader() {
	if($this->DoNavBar) {
	    $xml 	= new XMLCache_Menu($this->Channel,$this->Lang);
	    $output = new Xml2Xsl();
	    $output->xslFile("NavBar.xsl");
	    $output->xmlFile($xml->Process());
	    $param['droit'] =  (isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['right'] != '') ? $_SESSION['user']['right'] : '';
	    $param['select'] = $this->PageId;
	    $output->xslParameter($param);
	    $this->ModulesHF['NavBar'] = $output->Process();
	}
	else {
	    $this->ModulesHF['NavBar'] = "";
	}
	return $this->header->Process($this->ModulesHF);
    }

    /**
     * Set Body Content template to use for this page
     */
    function setTitle($text = '',$img = '',$changeDocTitle = false) {
	$this->title['text'] = $text;
	if($img != '')
	    $this->title['img'] = $img;
	if($changeDocTitle)
	    $this->headerHTML->SetTitle($text);

	$imgPath = ($this->Channel == 'normal') ? '' : '../';
	$imgTag = (array_key_exists('img', $this->title) and $this->title['img'] != '') ? '<img alt="'.$this->title['text'].'" name="'.$this->title['text'].'" src="'.$imgPath.$this->title['img'].'"/>' : '';
	$this->title['tag'] = '<h1>'.$imgTag.$this->title['text'].'</h1>';
    }

    /**
     * Set Body Content template to use for this page
     */
    function setDisplayTitle($bool = true) {
	$this->displayTitle = $bool;
    }

    /**
     * Set Body Content template to use for this page
     */
    function setBodyContentTemplate($template = '') {
	if ($template == '') {
	    $this->bodyContentTemplate = 'BodyContent';
	}
	else {
	    $this->bodyContentTemplate = $template;
	}
    }

    /**
     * add body content row to place in the template
     */
    function AddBodyContent($content,$row = '') {
	if ($row == '') {
	    $this->bodyContent[] = $content.afficherMessages();
	}
	else {
	    $this->bodyContent[$row] = $content.afficherMessages();
	}
    }

    /**
     * Process BODY Content creation and return result
     */
    function DisplayBodyContent() {
	if($this->DoNavBar) {
	    $xml 	= new XMLCache_Menu($this->Channel,$this->Lang);
	    $output = new Xml2Xsl();
	    $output->xslFile("NavBar.xsl");
	    $output->xmlFile($xml->Process());
	    $param['droit'] =  (isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['right'] != '') ? $_SESSION['user']['right'] : '';
	    $param['select'] = $this->PageId;
	    $output->xslParameter($param);
	    $this->bodyContent['NavBar'] = $output->Process();
	}
	else {
	    $this->bodyContent['NavBar'] = "";
	}
	$this->bodyContent['Title'] = ($this->displayTitle) ? $this->title['tag'] : '';
	if((is_array($this->bodyContent))and($this->bodyContentTemplate != '')) {
	    return templating($GLOBALS['CHANNEL_'.$this->Channel]['FramesetTmpDir'].$this->bodyContentTemplate,$this->bodyContent);
	}
    }

    /**
     * Process BODY Footer creation and return result
     */
    function DisplayBodyFooter() {
	return $this->footer->Process($this->ModulesHF);
    }

    /**
     * Process HTML Footer creation and return result
     */
    function DisplayFooter($output = array()) {
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$out['filActu'] = '';
	if(array_key_exists('user', $_SESSION)) {
	    $req = "SELECT id, titre, type FROM actualite WHERE isVisibleFilActu = '1' ORDER BY id DESC LIMIT 0,20";
	    $bddtmp->makeRequeteFree($req);
	    $result = $bddtmp->process2();
	    if($result[0]) {
		if($this->Channel == 'normal')
		    $suffixe = '';
		else
		    $suffixe = '../';
		foreach($result[1] as $v)
		    $actu[] = '<a  href="#" onclick="return zuno.filActu.popupActu('.$v['id'].')"><img src=\''.$suffixe.'img/actualite/'.strtolower($v['type']).'.png\' alt=\''.$v['type'].'\'/> '.str_replace("\n", ' ',$v['titre']).'</a>';
	    }
	    else
		$actu[] = '';
	    if(is_array($actu))
		foreach ($actu as $k => $v)
		    $out['filActu'] .= 'zuno.filActu.addMessage(\''.str_replace("'","\'",$v).'\');'."\n\t";
	}
	$out['channel_name'] = $this->Channel;

	$output = array_merge($out,$output);
	return templating($GLOBALS['CHANNEL_'.$this->Channel]['FramesetTmpDir'].'Footer',$output);
    }


    /**
     * Process complete Page completion according
     * to previous configurations of the 5 Parts
     * HEADER, BODYHEADER, BODYCONTENT, BODYFOOTER
     * and FOOTER
     */
    function Process() {
	header('Content-Type: text/html; charset='.$GLOBALS['PROJET']['pageOutputEncoding']);

	if (!$this->headerHTMLSent) {
	    echo $this->DisplayHeader();
	}
	echo $this->DisplayBodyHeader();
	echo $this->DisplayBodyContent();
	echo $this->DisplayBodyFooter();
	echo $this->DisplayFooter();
	$this->CreateDebug();
    }

    function CreateDebug() {
	if ($GLOBALS['LOG']['DisplayDebug']) {
	    Logg::displayDebug();
	}
    }


}


?>
