<?php
/*#########################################################################
#
#   name :       Module Header/Footer d'affichage du form scancode Zuno
#   desc :       Affiche le champs pour le scancode Zuno
#   categorie :  ModulesHF
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


$moduleHF[$i]['name']		= "ZunoScanCode";
$moduleHF[$i]['position']	= "toolF";
$moduleHF[$i]['actif']		= TRUE;
$moduleHF[$i]['order']		= 6;
$moduleHF[$i]['class']		= "ModuleHFZunoScanCode";

/**
 * Class containing method for generating module content
 */
class ModuleHFZunoScanCode {
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
	if(isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['fullnom'] != '')
	    $out .= '<li id="ScanLeCode">
				    <form name="ScanSearch" method="post" action="'.$prefix.'FormProcess.ScanSearch.php">
					<input type="text" name="Code" id="ScanSearchCode" value="" size="6"/>
					<img src="../img/searchGo.png" id="ScanSearchCodeGo" title="lancer la recherche"/>
				    </form>
				</li>
				<script type="text/javascript">
				    $(\'ScanSearchCode\').onchange = function() { document.ScanSearch.submit(); }
				    $(\'ScanSearchCode\').onfocus = function() { new Effect.Scale(\'ScanSearchCode\',300, { duration: 0.3, scaleY : false, scaleContent : false }); }
				    $(\'ScanSearchCode\').onblur = function() { new Effect.Scale(\'ScanSearchCode\',33, { duration: 0.3, scaleY : false, scaleContent : false }); }
				    $(\'ScanSearchCodeGo\').onchange = function() { document.ScanSearch.submit(); }
				    $(\'ScanSearchCodeGo\').onmouseover = function() { $(\'ScanSearchCodeGo\').style.cursor = \'pointer\'; }
				    $(\'ScanSearchCodeGo\').onmouseout = function() { $(\'ScanSearchCodeGo\').style.cursor = \'auto\'; }
				</script>';

	return $out;
    }
}

?>
