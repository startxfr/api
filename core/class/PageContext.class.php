<?php
/*#########################################################################
#
#   name :       PageContext.inc
#   desc :	 Class Page
#   categorie :  PageContext
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for geting a complete context for this page
 * This class is provide to get all informations relevant to CHANNEL, Incoming
 * VAR, SESSION and PAGE DATA information.
 * Execute acess control regarding to channel page and session information.
 * If this requirement fail, user is redirected, else the script continue
 */
class PageContext {
    /** Channel ID to use for this Context. */
    var $channel;
    /** Channel Context. */
    var $channelData;
    /** incoming GET data. */
    var $rcvG;
    /** incoming POST data. */
    var $rcvP;
    /** incoming FILE data. */
    var $rcvF;
    /** where you can find array describing this page. */
    var $Data;
    /** URI to XML description of the page. */
    var $cacheXML;
    /** ID of the page. */
    var $id;
    /** Page Class connector. */
    var $PageClass;

    /**
     * Constructor.
     * This set the channel to use for this context
     */
    function __construct($channel = 'normal') {
	$this->channel = $channel;
	$GLOBALS['currentChannel'] = $channel;
	Language::LoadInit();
    }

    /**
     * Process analyse of the VAR context
     */
    function GetVarContext() {
	$IncVar = new PageContextVar();
	$this->rcvG	= $IncVar->rcvG;
	$this->rcvP	= $IncVar->rcvP;
	$this->rcvF	= $IncVar->rcvF;
    }

    /**
     * Process analyse of the CHANNEL context
     */
    function GetChannelContext() {
	Logg::loggerNotice('PageContext::getChannelContext() ~ informations sur le channel '.$this->channel,'',__FILE__.'@'.__LINE__);
	$this->channelData = $GLOBALS['CHANNEL_'.$this->channel];
    }

    /**
     * Process analyse of the PAGE context
     */
    function GetPageContext($lang = "") {
	$this->PageClass = new PageContextData($this->channel);
	if($lang != '') {
	    $this->PageClass->changeLang($lang);
	}
	if(($this->id == '')or($lang != '')) {
	    $this->cacheXML	= $this->PageClass->Process();
	    $this->Data	= $this->PageClass->Data;
	    $this->id	= $this->PageClass->id;
	}
    }

    /**
     * Process analyse of the SESSION context
     */
    function GetSessionContext($right = '', $doRedirect = true) {
	$Sess = new PageContextSession($this->channel);
	// if the given channel is private, required authenticated session
	if(($this->channelData['private'] == 'TRUE')and($this->Data['page_pg'] != 'Login.php')) {
	    $Sess->NeedSessionUser();
	}
	// if the given channel has to be secure, required HTTPS protocol
	if($this->channelData['requireSSL'] == 'TRUE') {
	    $Sess->NeedSecure = TRUE;
	}
	// if we force specific right requirement
	if($right != '') {
	    $Sess->NeedRightSession($right);
	    $Sess->NeedSessionUser();
	}
	else {
	    // if the given page need specific right
	    if($this->Data['droit_pg'] != '') {
		$Sess->NeedRightSession($this->Data['droit_pg']);
		$Sess->NeedSessionUser();
	    }
	}
	return $Sess->Process('',$doRedirect);
    }

    /**
     * Process analyse of the complete page context
     * this mean SESSION, VAR, CHANNEL and PAGE context
     */
    function GetFullContext() {
	Logg::loggerNotice('PageContext::GetFullContext() ~ Récupération du context complet de la page','',__FILE__.'@'.__LINE__);
	// Var context
	$this->GetVarContext();
	// Channel context
	$this->GetChannelContext();
	// Page context
	$this->GetPageContext();
	// Session context
	$this->GetSessionContext();

	// gestion des changement de langue
	if($_SESSION["language"] != $GLOBALS['LANGUE']['default']) {
	    $changeLang = $_SESSION["language"];
	}
	elseif(isset($this->rcvG["lang"])) {
	    $changeLang = $this->rcvG["lang"];
	}
	//On switch la langue si demandé
	if(isset($changeLang)) {
	    Language::LanguageSwitch($changeLang);
	    $this->GetPageContext($changeLang);
	}
    }
}
?>
