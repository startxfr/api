<?php
/*#########################################################################
#
#   name :       Module Header/Footer d'affichage du lien credit du site
#   desc :       Affiche le lien credit du site
#   categorie :  ModulesHF
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


$moduleHF[$i]['name']		= "Credit";
$moduleHF[$i]['position']	= "toolF";
$moduleHF[$i]['actif']		= TRUE;
$moduleHF[$i]['order']		= 2;
$moduleHF[$i]['class']		= "ModuleHFCredit";

/**
 * Class containing method for generating module content
 */
class ModuleHFCredit {
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
	if($_SESSION['user']['fullnom'] != '')
	    return '<li><a href="#" title="Crédits du logiciel" onclick="zuno.contextBox.open(this,\'DisplayCredits\');">Informations</a></li>';
	else
	    return '';
    }
}

?>
