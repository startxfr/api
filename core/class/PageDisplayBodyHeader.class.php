<?php
/*#########################################################################
#
#   name :       PageDisplayBodyHeader.php
#   desc :       Class PageDisplayBodyHeader
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for generating Page Content Header according to the given channel
 * This page header define top bar of the page, the site menu and a special
 * toolbar witch can be use for shortcut or spécial link such as language,
 * user info or anithing else
 */
class PageDisplayBodyHeader {
    /** Channel to use for this Body Header. */
    var $channel;
    /** Toolbox content. */
    var $tools;
    /** Page pageSelecteded inthe menu. */
    var $pageSelected;
    /** Page menu content. */
    var $menuContent;

    /**
     * Constructor.
     * Configure default Body Header according to
     * channel configuration
     */
    function __construct($channel='normal') {
	$this->channel = $channel;
    }

    /**
     * PageSelect.
     * Configure pageSelect var and set menu pageSelectedion
     */
    function PageSelect($pageselected) {
	if($pageselected != '') {
	    $this->pageSelected = $pageselected;
	}
    }

    /**
     * GenerateMenu.
     * Create menu according to previous configuration
     * using Menu Class
     */
    function GenerateMenu() {
	$menu = new Menu($this->channel);
	$menu->Type('header');
	$menu->SetSelected($this->pageSelected);
	$this->menuContent = $menu->Process();
    }

    // generate html output
    function Process($content) {
	if(!array_key_exists('menuH',$content)) $content['menuH'] = '';
	if(!array_key_exists('toolH',$content)) $content['toolH'] = '';
	if($this->menuContent == '') {
	    $this->GenerateMenu();
	}

	$content['menuH'] .= $this->menuContent;
	$content['toolH'] .= '';
	return templating($GLOBALS['CHANNEL_'.$this->channel]['FramesetTmpDir'].'BodyHeader',$content);
    }

}

?>
