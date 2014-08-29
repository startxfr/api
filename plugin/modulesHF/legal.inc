<?php
/*#########################################################################
#
#   name :       Module Header/Footer d'affichage du lien info legales
#   desc :       Affiche le lien vers la popup d'info legale
#   categorie :  ModulesHF
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


$moduleHF[$i]['name']		= "Legal";
$moduleHF[$i]['position']	= "toolF";
$moduleHF[$i]['actif']		= TRUE;
$moduleHF[$i]['order']		= 1;
$moduleHF[$i]['class']		= "ModuleHFLegal";

/**
 * Class containing method for generating module content
 */
class ModuleHFLegal {
    // Page information context
    var $page;
    // Channel
    var $channel;

    /**
     * Constructor.
     */
    function __construct($channel,$data) {
	$this->data	= $data;
	$this->channel	= $channel;
    }

    /**
     * Display module content for page header
     */
    function DisplayModule() {
	return '<li><a href="#" title="Informations légales" onclick="zuno.contextBox.open(this,\'DisplayLegal\');">&copy; '.$GLOBALS['PROJET']['copyright'].'</a></li><li>|</li>';
    }
}

?>