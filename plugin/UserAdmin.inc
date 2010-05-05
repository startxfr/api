<?php
/*#########################################################################
#
#   name :       ACTUALITE
#   desc :       user central module
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * UserAdmin.
 * Class used for generating news portlets
 */
class UserAdminPortlet {
    /** Sort orderField field */
    var $orderField;
    /** Sort order direction */
    var $orderDirection;
    /** Selected news ID */
    var $selectID;
    /** Message used for administration process. */
    var $message;
    /** Lang used for this portlet. */
    var $Lang;
    /** Html output. */
    var $output;

    /**
     * Constructor.
     * @param channel define channel used for this object
     */
    function __construct($lang = '') {
	$this->orderField	= 'droit';
	$this->orderDirection	= 'ASC';
	if ($lang == '') {
	    $this->Lang = $GLOBALS['LANGUE']['default'];
	}
    }

    /**
     * Set News to select.
     * @id News ID to select in this instance
     */
    function SetSelected($id) {
	$this->selectID = $id;
    }

    /**
     * Set Message to display in admin portlet.
     * @message Content of the message
     */
    function SetMessage($message) {
	$this->message = "<span class='important'>".$message."</span>";
    }

    /**
     * Set News order for a list.
     * @field Filed to use for ordering
     * @direction Direction of this ordering
     */
    function SetOrder($field, $direction = 'ASC') {
	$this->orderField = $field;
	$this->orderDirection = $direction;
    }

    /**
     * Type is a shortcut to configure various kind of transformation.
     * @type Type to use for configuring this instance
     * @data array with data given from the form
     */
    function Type($type = '', $data = '') {
	if($type == 'CREATE') {
	    $tmp['selectciv']	= HtmlForm::addSelectCiv('civ', $data['civ']);
	    $tmp['selectlang']	= HtmlForm::addSelectLang('lang', $data['lang'] );
	    $bddtmp			= new Bdd();
	    $bddtmp->makeRequeteAuto('droit');
	    $listeDroit		= $bddtmp->process();
	    $tmp['selectdroit']	= '<select name="droit"><option value ="5"> Choisissez un droit </option>';
	    foreach ($listeDroit as $key => $val)
		$tmp['selectdroit'] .= '<option value ="'.$val['id_dt'].'">'.$val['nom_dt'].'</option>';
	    $tmp['selectdroit'] .= '</select>';

	    $tmp['message'] = $this->message;
	    $tmp['nom']	= $data['nom'];
	    $tmp['prenom']	= $data['prenom'];
	    $tmp['mail']	= $data['mail'];
	    $tmp['login']	= $data['login'];
	    $tmp['image']	= $data['image'];
	    $tmp['pwd'] 	= "";
	    $tmp['actif'] 	= "checked";
	    $tmp['nonactif']= "";

	    $BoxName  = 'UserCreate';
	    $tmp['FormName'] 	  = 'Form'.$BoxName;
	    $tmp['PortletName'] = 'Portlet'.$BoxName;
	    $BoxTitre = 'Création d\'un utilisateur';
	    $BoxCorps = templating('user/create', $tmp);
	    $BoxPied  = '<a href="javascript:history.back()">'.imageTag('../img/prospec/cancel.png','Effacer','middle').' Annuler</a>
		   			 <a href="javascript:document.'.$tmp['FormName'].'.submit()">'.imageTag('../img/prospec/record.png','Enregistrer','middle').' Enregistrer</a>';
	    $this->output = generateZBox($BoxTitre,$BoxTitre,$BoxCorps,$BoxPied,$BoxName);
	}
	elseif($type == 'MODIF') {
	    $tmp['selectciv'] = HtmlForm::addSelectCiv('civ', $data['civ']);
	    $tmp['selectlang'] = HtmlForm::addSelectLang('lang', $data['lang'] );

	    $bddtmp = new Bdd();
	    $bddtmp->makeRequeteAuto('droit');
	    $listeDroit = $bddtmp->process();
	    $tmp['selectdroit'] = '<select name="droit"><option value ="5"> Choisissez un droit </option>';
	    foreach ($listeDroit as $key => $val) {
		if ($val['id_dt'] == $data['droit'])
		    $checked = ' selected="selected"';
		else  $checked = '';
		$tmp['selectdroit'] .= '<option value ="'.$val['id_dt'].'"'.$checked.'>'.$val['nom_dt'].'</option>';
	    }
	    $tmp['selectdroit'] .= '</select>';

	    $tmp['message'] = $this->message;

	    $tmp['nom'] 	= $data['nom'];
	    $tmp['prenom']	= $data['prenom'];
	    $tmp['mail']	= $data['mail'];
	    $tmp['login']	= $data['login'];
	    if ($data['actif'] == '1')
		$tmp['actif'] = "checked";
	    else  $tmp['nonactif'] = "checked";

	    if ($data['image'] != '') {
		$tmp['image']  = '<br/><img src="../'.'admin/droit/'.$data['image'].'" name="image" alt="apercu"/><input type="checkbox" name="img_del" value="1"/>';
		$tmp['imguri'] = $data['image'];
	    }
	    else  $tmp['image'] = "";

	    $BoxName  = 'UserCreateModif';
	    $tmp['FormName'] 	  = 'Form'.$BoxName;
	    $tmp['PortletName'] = 'Portlet'.$BoxName;
	    $BoxTitre = 'Modification de l\'utilisateur';
	    $BoxCorps = templating('user/modif', $tmp);
	    $BoxPied  = '<a href="javascript:history.back()">'.imageTag('../img/prospec/cancel.png','Effacer','middle').' Annuler</a>
		   			 <a href="javascript:document.'.$tmp['FormName'].'.submit()">'.imageTag('../img/prospec/record.png','Enregistrer','middle').' Enregistrer</a>';
	    $this->output = generateZBox($BoxTitre,$BoxTitre,$BoxCorps,$BoxPied,$BoxName);
	}
	elseif($type == 'MANAGE') {
	    // requete pour trouver les utilisateurs
	    $bddtmp = new Bdd();
	    // selectionner les utilisateur activé ou pas
	    if (( $data['actif'] == '0')and($data['actif'] == '1'))
		$etat['actif'] = $data['actif'];
	    // selectionner les utilisateurs ayant le profil demandé
	    if ( $data['droit'] != '')
		$etat['droit'] = $data['droit'];
	    // trier les colonnes si demandé
	    if (is_array($etat))
		$command = ' AND ';
	    else  $command = ' WHERE ';
	    if ($this->orderField != '')
		$order = $command.' droit = id_dt ORDER BY '.$this->orderField." ".$this->orderDirection.', nom ASC';
	    else  $order = $command.' droit = id_dt';

	    $tables = array("user","droit");
	    $bddtmp->makeRequeteAuto($tables, $etat, $order);
	    $res = $bddtmp->process();

	    // remplissage du tableau
	    if ( is_array($res) ) {
		$j=1;
		foreach ($res as $key => $val) {
		    $val['altern'] = ($j++ % 2);
		    if ( $val['actif'] == 1 )
			$val['actif'] = "<img src=\"../img/valid.png\" name=\"actif\" alt=\"actif\" title=\"actif\">";
		    else  $val['actif'] = "";
		    $tmptab .= templating('user/manage.row', $val);
		}
	    }

	    // preparation du tableau et ajout à la page
	    $tab['liste'] = $tmptab ;
	    $BoxName  = 'UserManage';
	    $tab['FormName'] 	  = 'Form'.$BoxName;
	    $tab['PortletName'] = 'Portlet'.$BoxName;
	    $BoxTitre = 'Gestion des utilisateurs';
	    $BoxCorps = templating('user/manage', $tab);
	    $BoxPied  = '';
	    $this->output .= generateZBox($BoxTitre,$BoxTitre,$BoxCorps,$BoxPied,$BoxName);


	}
	elseif($type == 'MANAGEFORM') {
	    $bddtmp = new Bdd();
	    $bddtmp->makeRequeteAuto('droit');
	    $listeDroit = $bddtmp->process();
	    $tmp['selectdroit'] = '<select name="droit"><option value =""> Choisissez un droit </option>';
	    foreach ($listeDroit as $key => $val) {
		if ($val['id_dt'] == $data['droit'])
		    $checked = ' selected="selected"';
		else  $checked = '';
		$tmp['selectdroit'] .= '<option value ="'.$val['id_dt'].'"'.$checked.'>'.$val['nom_dt'].'</option>';
	    }
	    $tmp['selectdroit'] .= '</select>';

	    if($data['actif'] == '0' or $data['actif'] == '1' or $data['droit'] != '')
		$BoxState = 'open';
	    else	$BoxState = 'close';
	    $BoxName  = 'UserSearch';
	    $tmp['FormName'] 	  = 'Form'.$BoxName;
	    $tmp['PortletName'] = 'Portlet'.$BoxName;
	    $BoxTitre = 'Rechercher un utilisateur';
	    $BoxCorps = templating('user/manage.querybox', $tmp);
	    $BoxPied  = '<a href="javascript:document.'.$tmp['FormName'].'.reset()">'.imageTag('../img/prospec/cancel.png','Effacer','middle').' Réinitialiser</a>
		   			 <a href="javascript:document.'.$tmp['FormName'].'.submit()">'.imageTag('../img/prospec/voir.png','Rechercher','middle').' Lancer la recherche</a>';
	    $this->output = generateZBox($BoxTitre,$BoxTitre,$BoxCorps,$BoxPied,$BoxName,$BoxState);
	}
	elseif($type == 'DELETE') {
	    $usertable = "user";
	    $bddtmp = new Bdd();
	    $bddtmp->makeRequeteSelect($usertable, 'login', $data['id']);
	    $res = $bddtmp->process();

	    $tmp['selectlang'] = $GLOBALS["LANGUE_".$_SESSION["language"]][$res[0]['lang']];
	    $tmp['message'] = $this->message;

	    $tmp['civ'] = $res[0]['civ'];
	    $tmp['nom'] = $res[0]['nom'];
	    $tmp['prenom'] = $res[0]['prenom'];
	    $tmp['mail'] = $res[0]['mail'];
	    $tmp['login'] = $res[0]['login'];
	    $tmp['droit'] = $res[0]['droit'];	//ca se passe au dessus qd il aura des droits
	    if ($res[0]['actif'] == '1')
		$tmp['actif'] = "actif";
	    else  $tmp['actif'] = "inactif";
	    if ($res[0]['image'] != '')
		$tmp['image'] = "<img src=\"../img/".'admin/droit/'.$res[0]['image']."\" name=".$res[0]['image']." alt=\"apercu\"/> <input type=\"hidden\" name=\"img\" value=\"".$GLOBALS['REP']['appli'].'admin/droit/'.$res[0]['image']."\">";
	    else  $tmp['image'] = "<i>aucune image</i>";

	    $BoxName  = 'UserDelete';
	    $tmp['FormName'] 	  = 'Form'.$BoxName;
	    $tmp['PortletName'] = 'Portlet'.$BoxName;
	    $BoxTitre = 'Suppression de l\'utilisateur';
	    $BoxCorps = templating('user/delete', $tmp);
	    $BoxPied  = '<a href="javascript:history.back()">'.imageTag('../img/prospec/cancel.png','Effacer','middle').' Annuler</a>
		   			 <a href="javascript:document.'.$tmp['FormName'].'.submit()">'.imageTag('../img/valid.png','Confirmer','middle').' Confirmer</a>';
	    $this->output = generateZBox($BoxTitre,$BoxTitre,$BoxCorps,$BoxPied,$BoxName);
	}
    }

    /**
     * @return Portlet content to place in everywhere
     */
    function Process() {
	return $this->output;
    }

}





/**
 * UserAdmin.
 * News toolkit for news management and processing
 */
class UserAdminToolkit {
    /**
     * DBRecord.
     * @data array with data to update or insert news info
     */
    function DBRecord($data) {
	$dbConnexion = new Bdd();
	//on prepare les vars
	$in['nom']              = $data['nom'];
	$in['prenom']           = $data['prenom'];
	$in['mail']             = $data['mail'];
	$in['actif']            = $data['actif'];
	$in['lang']             = $data['lang'];
	$in['login']            = $data['login'];
	$in['civ']         	= $data['civ'];

	if ($data['image'] != '') {
	    $in['image']	= $data['image'];
	}
	if ($data['pass'] != '') {
	    $in['pwd'] 	= md5($data['pass']);
	}
	if ($data['droit'] == '') {
	    $in['droit'] 	= 10;
	}
	else {
	    $in['droit'] = $data['droit'];
	}

	$liste['login'] = $data['login'];
	$dbConnexion->MakeRequeteAuto('user',$liste);
	$result = $dbConnexion->process();
	if ($result[0]['login'] == '') {
	    $dbConnexion->makeRequeteInsert('user',$in);
	}
	else {
	    $dbConnexion->makeRequeteUpdate('user','login',$data['login'],$in);
	}
	$dbConnexion->process();
    }


    /**
     * DBDelete.
     * @id string ID of the newsletter to remove from database
     */
    function DBDelete($id = '', $img = '') {
	if($id != '') {
	    $in['login']= $id;
	    $dbConnexion = new Bdd();
	    $dbConnexion->makeRequeteDelete('user',$in);
	    $dbConnexion->process();

	    # delete image
	    if ( $img != '' ) {
		unlink($img);
	    }
	}
    }
}
?>
