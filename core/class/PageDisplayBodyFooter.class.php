<?php
/*#########################################################################
#
#   name :       PageDisplayBodyFooter.php
#   desc :       Class PageDisplayBodyFooter
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

class PageDisplayBodyFooter {
    //variables
    var $menu;		// page menu
    var $Template;		// template to use for output
    var $channel;		// template to use for output

    // Constructeur
    function __construct($channel='normal') {
	$this->channel = $channel;
	$this->Template = $GLOBALS['CHANNEL_'.$this->channel]['FramesetTmpDir'].'BodyFooter';
    }

    // generate html output
    function Process($content) {
	loadPlugin('docGenerator');
	docGeneratorAddZunoConfInfoBeforeTemplating($content);
	$content = array_merge($GLOBALS['PROJET'],$GLOBALS['zunoWebService'],$content);
	return templating($this->Template,$content);
    }

}

?>
