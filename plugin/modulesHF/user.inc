<?php
/*#########################################################################
#
#   name :       Module Header/Footer d'affichage de l'utilisateur
#   desc :       Affiche un liens avec le nom du profil
#   categorie :  ModulesHF
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


$moduleHF[$i]['name']		= "User";
$moduleHF[$i]['position']	= "toolH";
$moduleHF[$i]['actif']		= TRUE;
$moduleHF[$i]['order']		= 2;
$moduleHF[$i]['class']		= "ModuleHFUser";

/**
 * Class containing method for generating module content
 */
class ModuleHFUser {
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
	if($this->channel == 'normal') {
	    if(isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['fullnom'] != '')
		$out .= '<li class="moduleUser-right'.$_SESSION['user']['right'].'"><a href="#" onclick="zuno.popup.open(\'User.php\',\'type=popup\',\'480\',\'220\');" title="'.$_SESSION['user']['fullnom'].'">'.imageTag($GLOBALS['REP']['img'].'admin/droit/'.$_SESSION['user']['right'].".".'png',$_SESSION['user']['rightDesc'],'','','BoutonConnect').$_SESSION['user']['fullnom'].'</a></li>';
	}
	else {
	    $addadmin = "../";
	    if(isset($_SESSION) and array_key_exists('user',$_SESSION) and $_SESSION['user']['fullnom'] != '')
		$out .= '<li class="moduleUser-right'.$_SESSION['user']['right'].'"><a href="#" onclick="zuno.popup.open(\''.$addadmin.'User.php\',\'type=popup\',\'480\',\'220\');" title="'.$_SESSION['user']['fullnom'].'">'.imageTag($addadmin.$GLOBALS['REP']['img'].'admin/droit/'.$_SESSION['user']['right'].".".'png',$_SESSION['user']['rightDesc'],'','','BoutonConnect').$_SESSION['user']['fullnom'].'</a></li>';
	}
	return $out;
    }
}

?>
