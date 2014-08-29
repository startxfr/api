<?php
/*#########################################################################
#
#   name :       Module Header/Footer d'affichage du form scancode Zuno
#   desc :       Affiche le champs pour le scancode Zuno
#   categorie :  ModulesHF
#   ID :  	 $Id: ZunoScanCode.inc 3220 2009-10-13 23:03:51Z cl $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


$moduleHF[$i]['name']		= "ZunoRapportBug";
$moduleHF[$i]['position']	= "toolF";
$moduleHF[$i]['actif']		= TRUE;
$moduleHF[$i]['order']		= 5;
$moduleHF[$i]['class']		= "ModuleHFZunoRapportBug";

/**
 * Class containing method for generating module content
 */
class ModuleHFZunoRapportBug {
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
	$out = '';
	$prefix = ($this->channel != 'normal') ? '../' : './';
	if($_SESSION['user']['fullnom'] != '')
	    $out .= '<li>|</li><li><a name="rapportBug" title="Soummettre un rapport de bug" onclick="zuno.contextBox.open(this,\'DisplayRapportBug\');">RapportBug</a></li>';
	return $out;
    }
}

?>
