<?php
/*#########################################################################
#
#   name :       USER
#   desc :       User central module
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


/**
 * User Toolkit
 * Toolkit for displaying user informations
 */
class UserToolkit {

    /**
     * generate user info Portlet
     * @id		ID of selected user
     * @type	kind of output display
     */
    function UserPortlet($id,$type = 'SIMPLE') {
	$orderField	= 'Login';
	$orderDirection	= 'ASC';

	$bddtmp	= new Bdd();
	$table  = array('user','droit');
	$search = array('login'=>$id,'droit'=>'`id_dt');
	$bddtmp->makeRequeteAuto($table,$search);
	$usertmp= $bddtmp->process();
	$user   = $usertmp[0];
	if($user['login'] != '') {
	    if($user['image'] != '') {
		$user['image'] = imageTag("../".'admin/droit/'.$user['image'],
			'utilisateur '.$user['nom'].' '.$user['prenom'],
			'right');
	    }
	    $user['language'] = $GLOBALS['LANGUE_'.$_SESSION["language"]][$user['lang']];

	    if($type == 'SIMPLE')
		$corps = templating('user/Portlet.Simple',$user);
	    $titre = "Information sur l'utilisateur ".$user['login'];
	    return generateZBox($titre, $titre, $corps,'','UserViewBox','');
	}
	else {
	    return "<span class=\"important\">".$GLOBALS['Tx4Lg']['UserNoProfileFound']."</span>";
	}
    }
}
?>
