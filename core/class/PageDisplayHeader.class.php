<?php
/*#########################################################################
#
#   name :       PageDisplayHeader.php
#   desc :       Class PageDisplayHeader
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for generating HTML header fot this page according to the given channel
 */
class PageDisplayHeader {
    /** Page title. */
    var $titre;
    /** Page meta tags. */
    var $metainfo = array();
    /** Page description. */
    var $description;
    /** Channel for this page. */
    var $channel;
    /** CSS Theme used. */
    var $cssTheme;
    /** List of CSS Stylesheet. */
    var $css;
    /** List of JS script page. */
    var $js;
    /** List on onload function. */
    var $onload;
    /** List on unonload function. */
    var $onunload;

    /**
     * Constructor.
     * Configure default HTML Header according to
     * channel configuration
     */
    function __construct($channel='normal') {
	$this->channel	= $channel;
	$this->titre	= 'Bienvenue sur '.$GLOBALS['PROJET']['nom'];
	$this->AddDescription($GLOBALS['PAGE_HEADER']['description']);
	$this->AddMetaTag('keyword',$GLOBALS['PAGE_HEADER']['keyword']);
	$this->AddMetaTag('mail',$GLOBALS['PROJET']['mail']);
	$this->AddMetaTag('version',$GLOBALS['PROJET']['version']);
	$this->AddMetaTag('copyright',$GLOBALS['PROJET']['copyright']);
	$this->AddMetaTag('Author',$GLOBALS['PROJET']['auteur']);
	$this->AddMetaTag('contact_name',$GLOBALS['PROJET']['contact']);

	if (!isset($_SESSION) or $_SESSION["language"] == '')
	    $_SESSION["language"] = $GLOBALS["LANGUE"]["default"];
	$this->AddMetaTag('Content-Language',$_SESSION["language"]);

	if ($GLOBALS['CHANNEL_'.$this->channel]['CSSTheme'] == '')
	    $this->cssTheme = 'default';
	else  $this->cssTheme = $GLOBALS['CHANNEL_'.$this->channel]['CSSTheme'];

	$this->AddCSS($this->cssTheme.'/init.css');
	$this->AddCSS($this->cssTheme.'/style.channel.'.$this->channel.'.css');
	$this->AddCSS($this->cssTheme.'/style.print.css','print');
	$this->initAjax();
	$this->initCalendar();
	$this->AddJS($this->cssTheme.'/init.js');
	$this->AddJS($this->cssTheme.'/tooltip.js');
	$InfoClient = GetClientBrowserInfo();
    }

    /**
     * Set title of the page
     */
    function SetTitle($titre = '') {
	$this->titre = $GLOBALS['PROJET']['nom']." : ".$titre;
    }

    /**
     * Add a description to the page description meta-tag
     */
    function AddDescription($desc) {
	$this->description .= $desc.', ';
    }

    /**
     * Add a MetaTag to the page description
     */
    function AddMetaTag($key, $val = '') {
	if(!array_key_exists($key,$this->metainfo)) $this->metainfo[$key] = '';
	$this->metainfo[$key] .= $val;
    }

    /**
     * Generate Mata tags regarding previous setting
     */
    function generateMetaInfo() {
	$this->AddMetaTag('description',$this->description);
	$tmp = "\n\t\t\t<meta http-equiv=\"Content-language\" content=\"".$_SESSION["language"]."\"/>";
	foreach($this->metainfo as $name => $info)
	    $tmp .= "\n\t\t\t<meta name=\"".$name."\" content=\"".$info."\"/>";
	return $tmp;
    }

    /**
     * Add a CSS to the list of Stylesheet to load
     */
    function AddCSS($style, $type='all') {
	if (isset($style))
	    $this->css[$type][] = $style;
    }

    /**
     * Add a JavaScript page to the list of script to load
     */
    function AddJS($newJs) {
	if (isset($newJs))
	    $this->js[] = $newJs;
    }

    /**
     * Process the list of Stylesheet to load and return result
     */
    function generateCssTags() {
	$tmp = "";
	foreach( $this->css as $media => $css) {
	    $addMedia = ($media != 'all') ? " media=\"$media\"" : '';
	    $tmp  .= "\n\t\t\t<style type=\"text/css\"$addMedia>";
	    foreach ( $css as $cssFile )
		$tmp .= "\n\t\t\t\t@import url(\"".getStaticUrl('Jss').$cssFile."\");";
	    $tmp .= "\n\t\t\t</style>";
	}
	$tmp .= $this->generateUserCss();
	return $tmp;
    }

    /**
     * Process the list of Stylesheet to load and return result
     */
    function generateUserCss() {
	$tmp = "";
	if(isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['id'] != '') {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $bddtmp->makeRequeteSelect('user','login',$_SESSION['user']['id']);
	    $result = $bddtmp->process();
	    if($result[0]['viewportSize'] != '') {
		$size = explode('x',$result[0]['viewportSize']);
		$tmp  .= "\n\t\t\t<style type=\"text/css\" media=\"screen\">";
		$tmp .= "\n\t\t\t\t".'#ZunoWorkspace { min-height: '.$size[1].'px;width: '.$size[0].'px; }';
		$tmp .= "\n\t\t\t</style>";
	    }
	}
	return $tmp;
    }

    /**
     * Process the list of JavaScript page to load and return result
     */
    function generateJsTags() {
	$tmp = "";
	foreach( $this->js as $javas ) {
	    $tmp .= "\n\t\t\t<script language=\"JavaScript\" type=\"text/javascript\" src=\"";
	    $tmp .= getStaticUrl('Jss').$javas."\"></script>";
	}
	return $tmp;
    }

    /**
     * If a calendar is required, load appropriate
     * JavaScript and CSS stylesheet
     */
    function initCalendar() {
	$this->AddCSS("JSCal2-1.0/css/jscal2.css");
	$this->AddCSS("JSCal2-1.0/css/border-radius.css");
	$this->AddCSS("JSCal2-1.0/css/reduce-spacing.css");
	$this->AddJS("JSCal2-1.0/js/jscal2.js");
	$this->AddJS("JSCal2-1.0/js/lang/".$_SESSION["language"].".js");
    }


    /**
     * If a calendar is required, load appropriate
     * JavaScript and CSS stylesheet
     */
    function initAjax() {
	$this->AddJS("script.aculo/js/prototype.js");
	$this->AddJS("script.aculo/js/scriptaculous.js");
	$this->AddJS("script.aculo/js/resizable.js");
	$this->AddJS("script.aculo/js/unittest.js");
	$this->AddJS("script.aculo/js/menu.js");
	$this->AddJS("script.aculo/js/accordion.js");
	$this->AddJS("prototip/js/prototip.js");
	$this->AddCSS("prototip/css/prototip.css");
    }


    /**
     * Add OnLoad Javascript command for this page
     */
    function AddOnload($load) {
	if ( isset($load) )
	    $this->onload[] = $load;
    }

    /**
     * Add OnLoad Javascript command for this page
     */
    function AddOnunload($unload) {
	if ( isset($unload) )
	    $this->onunload[] = $unload;
    }

    /**
     * Generate OnLoad Javascript command according to previous
     * configuration
     */
    function generateOnLoad() {
	$tmp = "";
	if (isset($this->onload)) {
	    $tmp .= " onLoad=\"";
	    foreach( $this->onload as $load )
		$tmp .= $load.";";
	    $tmp .= "\" ";
	}
	if (isset($this->onunload)) {
	    $tmp .= " onUnload=\"";
	    foreach( $this->onunload as $load )
		$tmp .= $load.";";
	    $tmp .= "\" ";
	}
	return $tmp;
    }

    /**
     * Process the Header generation according to the configuration
     * @return Complete HTML Header
     */
    function Process() {
	$output['titre']  = $this->titre;
	$output['meta']	= $this->generateMetaInfo();
	$output['css'] 	= zunoRSS::generateHeaderLinkCommon($this->channel).
		$this->generateCssTags();
	$output['js'] 	= $this->generateJsTags();
	$output['onload'] = $this->generateOnLoad();
	return templating($GLOBALS['CHANNEL_'.$this->channel]['FramesetTmpDir'].'Header',$output);
    }

}

?>