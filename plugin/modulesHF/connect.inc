<?php
/*#########################################################################
#
#   name :       Module Header/Footer de connexion
#   desc :       Affiche un bouton pour se connecter/deconnecter de l'appli
#   categorie :  ModulesHF
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


$moduleHF[$i]['name']		= "Connexion";
$moduleHF[$i]['position']	= "toolH";
$moduleHF[$i]['actif']		= TRUE;
$moduleHF[$i]['order']		= 1;
$moduleHF[$i]['class']		= "ModuleHFConnect";

/**
 * Class containing method for generating module content
 */
class ModuleHFConnect {
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
	if(isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['fullnom'] != '')
	    $out .= '<li id="logout"><a onclick="zuno.contextBox.open(this,\'DisplayAuthentificationDisconnect\');" title="Logout" href="#"><img title="'.$GLOBALS['Tx4Lg']['Disconnect'].'" src="'.getStaticUrl('img').'deconnect.png"/>'.$GLOBALS['Tx4Lg']['Disconnect'].'</a></li>';
	else $out .= '<li id="login"><a onclick="zuno.contextBox.open(this,\'DisplayAuthentificationConnect\');" title="Login" href="#"><img title="'.$GLOBALS['Tx4Lg']['Connect'].'" src="'.getStaticUrl('img').'connect.png"/>'.$GLOBALS['Tx4Lg']['Connect'].'</a></li>';
	return $out;
    }
}

?>
