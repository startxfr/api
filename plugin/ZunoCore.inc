<?php
/*#########################################################################
#
#   name :       Prospec
#   desc :       Prospec Channel module
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function  FicheBlockContact($id_cont,$id_ent,$opt = "") {
    $cont = array();
    $size_comm = 60;
    $height_comm  = 4;
    if($id_cont != '') {
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT * FROM contact WHERE id_cont = '".$id_cont."'");
	$res = $bddtmp->process();
	$contact = $res[0];
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','id_cont','','','',$id_cont,'');
	$tag['suffixe'] = "modif_contact";
	if ($opt == 'modifpop') {
	    $size_comm  = 40;
	    $height_comm  = 7;
	}
    }
    elseif($opt == 'newpop') {
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','entreprise_cont','','','',$id_ent,'');
	$size_comm  = 40;
	$height_comm  = 7;
	$tag['suffixe'] = "new_contact1";
    }
    elseif($opt == 'delete') {
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','entreprise_cont','','','',$id_ent,'');
	$tag['suffixe'] = "new_contact1";
    }
    else {
	$opt = "new";
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','entreprise_cont','','','',$id_ent,'');
	$tag['suffixe'] = "new_contact1";
    }

    $tag['nom'] 	= inputTag('text','nom_cont','nextciv','', 20,$contact['nom_cont'],' onblur="ToUpCase(this)"');
    $tag['prenom']  = inputTag('text','prenom_cont','','', 20,$contact['prenom_cont'],'');
    $tag['fonction']= HtmlForm::addAutoSelect('fonction_cont','ref_fonction',$contact['fonction_cont'],'','','',$GLOBALS['PropsecConf']['DBPool']);
    $tag['LD'] 	= inputTag('text','tel_cont','','', 12,$contact['tel_cont'],'onblur="testPhone(this,true)"');
    $tag['fax'] 	= inputTag('text','fax_cont','','', 12,$contact['fax_cont'],'onblur="testPhone(this,true)"');
    $tag['mob'] 	= inputTag('text','mob_cont','','', 12,$contact['mob_cont'],'onblur="testPhone(this,true)"');
    if (($contact['mail_cont'] != '')and($id_cont != '')) {
	$mailCSS = 'icon';
	$tag['link_mail'] = linkTag("#\" onclick=\"return zuno.popup.open('../prospec/PopupSendMail.php','id_cont=".$id_cont."',750,650,'','','resize','SendMail');",imageTag('../img/prospec/SendMail.png','envoi','middle'));
    }
    else {
	$mailCSS = $tag['link_mail'] = '';
    }
    $tag['mail'] 	= inputTag('text','mail_cont',$mailCSS,'', 25,$contact['mail_cont'],'onblur="testEmail(this,true);"');
    $tag['comment'] 	= textareaTag('comm_cont',$contact['comm_cont'],'',$size_comm,$height_comm,'');
    $tag['civ'] 	= HtmlForm::addSelectCiv('civ_cont',$contact['civ_cont']);
    if ($contact['relactive_cont'] == '0') {
	$option = 'checked';
    }
    else {
	$option = '';
    }
    $tag['relance'] 	= inputTag('checkbox','relactive_cont','','','','0',$option);

    $corps = templating('prospec/fiche.ContactDetail',$tag);

    if(($opt == 'modif')or($opt == 'modifpop')) {
	$titre 	= imageTag('../img/prospec/contact.png','contact').' Informations sur '.$contact['civ_cont'].' '.$contact['prenom_cont'].' '.$contact['nom_cont'];
	$option = '';
	$pied 	= '<a href="javascript:document.'.$tag['suffixe'].'.reset()">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
			   <a href="javascript:document.'.$tag['suffixe'].'.submit()">'.imageTag('../img/prospec/record.png','','middle').' Modifier la fiche de '.$contact['civ_cont']." ".$contact['prenom_cont']." ".$contact['nom_cont'].'</a>';
	if ($_SESSION['user']['right'] <= $GLOBALS['PropsecConf']['right.deleteContact']) {
	    $pied .= '<a href="#" onclick="zuno.popup.open(\'PopupContact.php\',\'id_cont='.$id_cont.'&action=supp\',550,250,\'\',\'\',\'resize\',\'suppcont\');">'.imageTag('../img/prospec/delete.png','','middle').' Supprimer le contact</a>';
	}
    }
    elseif($opt == 'delete') {
	$DeleteOK = TRUE;
	$bddtmp->makeRequeteFree("SELECT COUNT(*) FROM appel WHERE contact_app = '".$id_cont."'");
	$tmp = $bddtmp->process();
	if((int) $tmp[0]['COUNT(*)'] == 1) {
	    $outTxt .= $tmp[0]['COUNT(*)']." appel sera supprimé<br/>";
	}
	if((int) $tmp[0]['COUNT(*)'] > 1) {
	    $outTxt .= $tmp[0]['COUNT(*)']." appels seront supprimés<br/>";
	}
	$bddtmp->makeRequeteFree("SELECT COUNT(*) FROM contact,projet WHERE contact_proj = '".$id_cont."'");
	$tmp = $bddtmp->process();
	if((int) $tmp[0]['COUNT(*)'] > 0) {
	    $outTxt = "Suppression impossible, ".$tmp[0]['COUNT(*)']." projet(s) associé(s).<br/>";
	    $DeleteOK = FALSE;
	}
	$bddtmp->makeRequeteFree("SELECT COUNT(*) FROM affaire WHERE contact_aff = '".$id_cont."'");
	$tmp = $bddtmp->process();
	if((int) $tmp[0]['COUNT(*)'] > 0) {
	    $outTxt = "Suppression impossible, ".$tmp[0]['COUNT(*)']." affaire(s) associée(s).<br/>";
	    $DeleteOK = FALSE;
	}
	$bddtmp->makeRequeteFree("SELECT COUNT(*) FROM facture WHERE contact_fact = '".$id_cont."'");
	$tmp = $bddtmp->process();
	if((int) $tmp[0]['COUNT(*)'] > 0) {
	    $outTxt = "Suppression impossible, ".$tmp[0]['COUNT(*)']." facture(s) associée(s).<br/>";
	    $DeleteOK = FALSE;
	}
	$corps = "<span class='important'>".$outTxt."</span>";

	$titre  = imageTag('../img/prospec/contact.delete.png','delete')." Supprimer la fiche de ".$contact['civ_cont'].' '.$contact['prenom_cont'].' '.$contact['nom_cont'];
	if($DeleteOK) {
	    $pied 	= '<a href="../FormProcess.Contact.php?action=supp&id_cont='.$id_cont.'">'.imageTag('../img/prospec/confirm.png','','middle').' Confirmer</a>
				   <a href="javascript:zuno.popup.close();">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>';
	}
    }
    elseif($opt == 'move') {
	$titre_cont = imageTag('../img/prospec/contact.move.png','déplacer')." Deplacer la fiche de ".$contact['civ_cont'].' '.$contact['prenom_cont'].' '.$contact['nom_cont'];
	$tag['groupe'] = HtmlForm::addAutoSelect('entreprise_cont','entreprise', $cont['entreprise_cont'],'','','',$GLOBALS['PropsecConf']['DBPool']);
	$tag['action'] = inputTag('hidden','action','','','','move','');
	$tag['action'].= inputTag('hidden','id_cont','','','',$id_cont,'');
	$corps = templating('prospec/fiche.ContactMove',$tag);
	$pied 	= '<a href="javascript:document.move_contact.reset()">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
			   <a href="javascript:document.move_contact.submit()">'.imageTag('../img/prospec/move.png','','middle').' D&eacute;placer le contact</a>';
    }
    else {
	$titre 	= imageTag('../img/prospec/contact.png','resultat')." Ajout d'un nouveau contact";
	$option = 'none';
	$pied 	= '<a href="javascript:document.'.$tag['suffixe'].'.reset()">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
			   <a href="javascript:document.'.$tag['suffixe'].'.submit()">'.imageTag('../img/prospec/record.png','','middle').' Enregistrer ce nouveau contact</a>';
    }


    return generateZBox($titre,$titre,$corps,$pied,'contactdetail',$option);
}


/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function  FicheBlockContact2($id_cont,$id_ent,$opt,$BoxOpen = '') {
    $size_comm = 60;
    $height_comm  = 4;
    if($opt == "") $opt = "new";
    $tag['action'] = inputTag('hidden','action','','','',$opt,'');
    $tag['action'] .= inputTag('hidden','entreprise_cont','','','',$id_ent,'');
    $tag['suffixe'] = "new_contact";
    $tag['nom'] 	= inputTag('text','nom_cont','nextciv','', 20,'',' onblur="ToUpCase(this)"');
    $tag['prenom']  = inputTag('text','prenom_cont','','', 20,'','');
    $tag['fonction']= HtmlForm::addAutoSelect('fonction_cont','ref_fonction','','','','',$GLOBALS['PropsecConf']['DBPool']);
    $tag['LD'] 	= inputTag('text','tel_cont','','', 12,'','onblur="testPhone(this,true)"');
    $tag['fax'] 	= inputTag('text','fax_cont','','', 12,'','onblur="testPhone(this,true)"');
    $tag['mob'] 	= inputTag('text','mob_cont','','', 12,'','onblur="testPhone(this,true)"');
    $tag['mail'] 	= inputTag('text','mail_cont','','', 25,'','onblur="testEmail(this,true);"');
    $tag['link_mail'] = '';
    $tag['comment'] 	= textareaTag('comm_cont','','',$size_comm,$height_comm,'');
    $tag['civ'] 	= HtmlForm::addSelectCiv('civ_cont','');
    $tag['option'] = '';
    $tag['relance'] 	= inputTag('checkbox','relactive_cont','','','','0');

    $corpsDetail = templating('prospec/fiche.ContactDetail',$tag);

    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("SELECT * FROM contact LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
				  WHERE entreprise_cont = '".$id_ent."' ORDER BY nom_cont ASC");
    $res = $bddtmp->process();

    // remplissage du tableau
    if (count($res) > 0) {
	$j=1;
	foreach ($res as $key => $contact) {
	    $contact['onclick'] = "";
	    $contact['boutons'] = "<a href=\"#\" onclick=\"return zuno.popup.open('../prospec/PopupAppel.php','id_cont=".$contact['id_cont']."',650,275,'','','resize','ajoutapp');\">".imageTag('../img/prospec/appel.png','Ajouter un appel')."</a>";
	    if (($_SESSION['user']['right'] == '0')or
		    ($_SESSION['user']['right'] == '1'))
		$contact['boutons'] .= " <a href=\"#\" onclick=\"return zuno.popup.open('../prospec/PopupContact.php','id_cont=".$contact['id_cont']."&action=move',700,200,'','','resize','movecont');\">".imageTag('../img/prospec/contact.move.png','Déplacer')."</a>
							 <a href=\"#\" onclick=\"return zuno.popup.open('../prospec/PopupContact.php','id_cont=".$contact['id_cont']."',775,300,'','','resize','modifcont');\">".imageTag('../img/prospec/contact.modif.png','Modifier')."</a>
							 <a href=\"#\" onclick=\"return zuno.popup.open('../prospec/PopupContact.php','id_cont=".$contact['id_cont']."&action=supp',550,250,'','','resize','modifcont');\">".imageTag('../img/prospec/contact.delete.png','Supprimer')."</a>";

	    if ($id_cont != $contact['id_cont'])
		$contact['onclick'] = "onclick=\"parent.location='?id_cont=".$contact['id_cont']."';\" style='cursor:pointer;'";

	    $contact['altern'] = ($j++ % 2);
	    $tmptab .= templating('prospec/fiche.ContactListe.row',$contact);
	}
	$tab['liste'] = $tmptab ;
	$corpsListe = templating('prospec/fiche.ContactListe',$tab);
	$result = '';
    }
    else {
	$corpsListe = '<span class="importantblue">Pas de contacts dans cette entreprise</span>';
	$result = 'none';
    }



    // On prépare la mise en boite
    $titre 	= imageTag('../img/prospec/result.png','resultat').' Listes des contacts';
    $titre1 = imageTag('../img/prospec/contact.png','contact').' Ajouter un contact ';
    $pied 	= "<a href=\"javascript:MM_changeProp('listecontact_off','','style.display','none','DIV');MM_changeProp('listecontact_1','','style.display','','DIV');MM_changeProp('listecontact','','style.display','none','DIV');\">".imageTag('../img/prospec/add.png','','middle')." Ajouter un nouveau contact</a>";
    $pied1 	= '<a href="javascript:document.'.$tag['suffixe'].'.reset()">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a><a href="javascript:document.'.$tag['suffixe'].'.submit()">'.imageTag('../img/prospec/record.png','','middle').' Enregistrer ce contact</a>';

    if($BoxOpen != '') {
	$result = $BoxOpen;
    }
    return generateZBox3($titre,$titre1,$titre,$corpsListe,$pied,$corpsDetail,$pied1,'listecontact',$result);
}




/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function  FicheBlockAppel($id_cont,$id_app,$opt) {
    $app = array();
    // requete pour la liste des appel
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("SELECT nom_cont,prenom_cont,civ_cont FROM contact WHERE id_cont = '".$id_cont."'");
    $res = $bddtmp->process();
    $contact = $res[0];

    if(($opt != 'newpop')and($opt != 'modifpop')and($opt != 'delete')) {
	$bddtmp->makeRequeteFree("SELECT * FROM appel WHERE contact_app = '".$id_cont."' ORDER BY appel_app DESC");
	$res = $bddtmp->process();
	// remplissage du block de liste des appels
	if (count($res) > 0) {
	    $j=1;
	    foreach ($res as $key => $apps) {
		if (($_SESSION['user']['right'] == '0')or
			($_SESSION['user']['id'] == $apps['utilisateur_app'])) {
		    $apps['boutons'] = "<a href=\"#\" onclick=\"return zuno.popup.open('../prospec/PopupAppel.php','id_app=".$apps['id_app']."',700,275,'','','resize','modifapp');\">".imageTag('../img/prospec/appel.modif.png','Modifier')."</a>
						    <a href=\"#\" onclick=\"return zuno.popup.open('../prospec/PopupAppel.php','id_app=".$apps['id_app']."&action=supp',700,275,'','','resize','suppapp');\">".imageTag('../img/prospec/appel.delete.png','Supprimer')."</a>";
		}
		else {
		    $apps['boutons'] = "";
		}
		if ($apps['affaire_app'] != '') {
		    $apps['comm_app'] .= " (<a href=\"#\" onclick=\"return zuno.popup.open('../draco/PopupAffaire.php','id_aff=".$apps['affaire_app']."',800,400,'','','resize','affaire');\">".imageTag('../img/prospec/affaire.png','affaire')."</a> Affaire ".$apps['affaire_app'].")";
		}

		if ($id_app == $apps['id_app']) {
		    $apps['onclick'] = "";
		}
		else {
		    $apps['onclick'] = "onclick=\"zuno.popup.open('../prospec/PopupAppel.php','id_app=".$apps['id_app']."',700,275,'','','resize','modifapp');\" style='cursor:pointer;'";
		}


		$apps['altern'] = ($j++ % 2);
		$apps['appel_app1'] = DateUniv2Human($apps['appel_app'],'simpleDH');
		$apps['rappel_app1'] = DateUniv2Human($apps['rappel_app'],'simpleDH');
		$tmptab .= templating('prospec/fiche.Appel.row',$apps);
	    }
	    $tab['liste'] = $tmptab ;
	    $corps = templating('prospec/fiche.Appel',$tab);
	    $result = '';
	}
	else {
	    $corps = '<span class="importantblue">Pas d\'historique d\'appel</span>';
	    $result = 'none';
	}
	$id_app = '';
	$appel['appel_app'] = DateUniv2Human('','shortdetail');
	$appel['rappel_app'] = DateUniv2Human(DateTimestamp2Univ(mktime(0,0,0,date("n")+6,date("d"),date("Y"))),'shortdetail');
    }

    // On crée le formaulaire d'ajout ou de modif de l'appel
    $tag['formName']= 'ajout_appel';
    if ($opt == 'newpop') {
	$size_comm = 35;
	$height_comm = 5;
	$tag['action']  = inputTag('hidden','action','','','','newpop','');
	$tag['action'] .= inputTag('hidden','contact_app','','','',$id_cont,'');
    }
    elseif ($opt == 'delete') {
	$size_comm = 35;
	$height_comm = 5;
	$tag['formName']= 'delete_appel';
	$tag['action']  = inputTag('hidden','action','','','','delete','');
	$tag['action'] .= inputTag('hidden','id_app','','','',$id_app,'');
    }
    elseif ($id_app != '') {
	$bddtmp->makeRequeteFree("SELECT * FROM appel WHERE  id_app = '".$id_app."'");
	$res = $bddtmp->process();
	$appel = $res[0];
	$size_comm = 80;
	$height_comm = 5;
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','id_app','','','',$id_app,'');
	$tag['action'] .= inputTag('hidden','contact_app','','','',$id_cont,'');
    }
    else {
	$size_comm = 80;
	$height_comm = 5;
	$tag['action']  = inputTag('hidden','action','','','','new','');
	$tag['action'] .= inputTag('hidden','contact_app','','','',$id_cont,'');
    }

    if ($opt == 'modifpop') {
	$size_comm = 35;
	$height_comm = 5;
    }

    $tag['appel_app']   =inputDateTag('','appel_app',DateUniv2Human($appel['appel_app'],'shortdetail'),'%d/%m/%Y %H:%M');
    $tag['rappel_app']  =inputDateTag('','rappel_app',DateUniv2Human($appel['rappel_app'],'shortdetail'),'%d/%m/%Y %H:%M');
    $tag['comm'] 	= textareaTag('comm_app',$appel['comm_app'],'',$size_comm,$height_comm,'');
    $tag['contact_app'] = $appel['contact_app'];
    if ($appel['premiercont_app'] == '1') {
	$option = 'checked';
	$cod_cont = '1';
    }
    else {
	$option = '';
	$cod_cont = '1';
    }
    $tag['premcont'] = inputTag('checkbox','premiercont_app','','','',$cod_cont,$option);

    $corps1 = templating('prospec/fiche.AppelForm',$tag);

    if ($opt == 'newpop') {
	$titre = imageTag('../img/prospec/appel.png','appel').' Ajouter un appel avec '.$contact['civ_cont'].' '.$contact['prenom_cont'].' '.$contact['nom_cont'];
	$pied 	= '<a href="javascript:zuno.popup.close();">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
			   <a href="javascript:document.ajout_appel.submit()">'.imageTag('../img/prospec/record.png','','middle').' Enregistrer cet appel</a>';
	$out = generateZBox($titre, $titre, $corps1,$pied,'appel','');
    }
    elseif ($opt == 'modifpop') {
	$titre = imageTag('../img/prospec/appel.modif.png','appel').' Modifier l\'appel du '.DateUniv2Human($app['appel_app'],'simple').' avec '.$contact['civ_cont'].' '.$contact['prenom_cont'].' '.$contact['nom_cont'];
	$pied 	= '<a href="javascript:zuno.popup.close();">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
			   <a href="javascript:document.'.$tag['formName'].'.submit()">'.imageTag('../img/prospec/record.png','','middle').' Enregistrer cet appel</a>';
	$out = generateZBox($titre, $titre, $corps1,$pied,'appel','');
    }
    elseif ($opt == 'delete') {
	$titre = imageTag('../img/prospec/appel.delete.png','appel')." Supprimer l'appel du ".DateUniv2Human($app['appel_app'],'simple').' avec '.$contact['civ_cont'].' '.$contact['prenom_cont'].' '.$contact['nom_cont'];
	$pied = '<a href="javascript:zuno.popup.close();">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
			 <a href="../prospec/PopupAppel.php?id_appel='.$id_app.'&action=supp">'.imageTag('../img/prospec/cancel.png','','confirm').' Confirmer</a>';
	$corps1 = '<span class="importantblue">Vous souhaitez supprimer cet appel.<br/> les informations qu\'il contient seront perdues.</span>';
	$out = generateZBox($titre, $titre, $corps1,$pied,'appel','');
    }
    else {
	// On prépare la mise en boite
	$titre 	= imageTag('../img/prospec/appel.png','resultat').' Listes des appels avec '.$contact['civ_cont'].' '.$contact['prenom_cont'].' '.$contact['nom_cont'];
	$titre1 = imageTag('../img/prospec/appel.png','appel').' Ajouter un appel avec '.$contact['civ_cont'].' '.$contact['prenom_cont'].' '.$contact['nom_cont'];
	$pied 	= "<a href=\"#\" onclick=\"javascript:appelZBox.switchDetail();\">".imageTag('../img/prospec/add.png','','middle')." Ajouter un appel</a>";
	$pied1 	= '<a href="javascript:document.'.$tag['formName'].'.reset()">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
			   <a href="javascript:document.'.$tag['formName'].'.submit()">'.imageTag('../img/prospec/record.png','','middle').' Enregistrer cet appel</a>';
	$out = generateZBox3($titre,$titre1,$titre,$corps,$pied,$corps1,$pied1,'appel',$result);
    }
    return $out;
}

function ZunoSelectEntreprise($name,$id_select='',$class='',$autre='',$withBlank=TRUE,$subPath = '../') {
    if ($id_select != '') {
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT id_ent,nom_ent,type_ent,cp_ent,ville_ent FROM zncore_entreprise WHERE id_ent = '".$id_select."' ORDER BY nom_ent ASC");
	$res = stripslashesR($bddtmp->process());
	$ent = $res[0];
	if ($ent['id_ent'] != '')
	    $nom = '['.$ent['id_ent'].'] '.strtoupper($ent['nom_ent']).' ('.$ent['cp_ent'].' '.$ent['ville_ent'].')';
    }

    return '<input id="'.$name.'" type="text" value="'.$nom.'" class="ajax" '.$class.' name="'.$name.'" autocomplete="off"/>
			<div id="autocompleteEnt" class="autocomplete" style="display: none;">tmpContent</div>
				<script type="text/javascript">
					new Ajax.Autocompleter("'.$name.'", "autocompleteEnt", "'.$subPath.'AjaxGetEntreprise.php", {minChars: 3});
				</script>';
}


/**
 * Create select liste with contact list for a given company
 * @param $name String: name of the select tag
 * @param $id_ent String: company ID to get contact list from
 * @param $id_select String: Contact ID to select
 * @param $class String: CSS style to apply
 * @param $autre String: Other information to push into this tag
 * @return HTML Select list
 */
function ZunoSelectContact($name,$id_ent,$id_select = "",$class = "",$autre = "") {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("SELECT * FROM contact LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
				  WHERE entreprise_cont = '".$id_ent."' ORDER BY nom_cont ASC");
    $res = $bddtmp->process();
    // remplissage du block de liste des appels
    if (count($res) > 0) {
	$j=1;
	foreach($res as $key => $con) {
	    $champ[$con['id_cont']] = $con['civ_cont']." ".$con['prenom_cont']." ".$con['nom_cont']." (".$con['nom_fct'].")";
	}
	if($name == 'DISPLAY') {
	    return $champ[$id_select];
	}
	else {
	    return selectTag($name,$champ,$id_select,$class,$autre,TRUE,$GLOBALS['PropsecConf']['DBPool']);
	}
    }
    else {
	return "<i>pas de contact enregistrés</i>";
    }
}

/**
 * Create select liste with active affaire list
 * @param $name String: name of the select tag
 * @param $id_select String: Contact ID to select
 * @param $class String: CSS style to apply
 * @param $autre String: Other information to push into this tag
 * @return HTML Select list
 */
function ZunoSelectAffaire($name = "affaire",$id_select = "",$class = "",$autre = "") {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $req = "SELECT * FROM entreprise,affaire
		LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
		LEFT JOIN ref_statusaffaire ON affaire.status_aff = ref_statusaffaire.id_staff
		WHERE entreprise_cont = id_ent
		AND actif_aff = '1'
		ORDER BY id_aff ASC, detect_aff ASC,nom_ent ASC";
    $bddtmp->makeRequeteFree($req);
    $res = $bddtmp->process();
    // remplissage du block de liste des appels
    if (count($res) > 0) {
	foreach($res as $key => $con) {
	    if(strlen($con['nom_ent']) > 28) {
		$con['nom_ent'] = substr($con['nom_ent'],0,28)."...";
	    }
	    $champ[$con['id_aff']] = $con['id_aff']." - ".$con['nom_ent']." (".$con['nom_staff'].")";
	}
	if($name == 'DISPLAY')
	    return $champ[$id_select];
	else return selectTag($name,$champ,$id_select,$class,$autre,TRUE,$GLOBALS['PropsecConf']['DBPool']);
    }
    else return "<i>pas d'affaire enregistrées</i>";
}

/**
 * Create select liste with active commande list
 * @param $name String: name of the select tag
 * @param $id_select String: Contact ID to select
 * @param $class String: CSS style to apply
 * @param $autre String: Other information to push into this tag
 * @return HTML Select list
 */
function ZunoSelectCommande($name = "commande",$id_select = "",$class = "",$autre = "") {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $req = "SELECT id_cmd, titre_cmd, nom_ent, sommeHT_cmd FROM entreprise,devis,commande
		WHERE entreprise_dev = id_ent
		AND devis_cmd = id_dev
		ORDER BY id_cmd ASC, nom_ent ASC";
    $bddtmp->makeRequeteFree($req);
    $res = $bddtmp->process();
    // remplissage du block de liste des appels
    if (count($res) > 0) {
	$j=1;
	foreach($res as $key => $con) {
	    $champ[$con['id_cmd']] = $con['id_cmd']." - ".$con['titre_cmd'].", ".$con['nom_ent']."(".$con['sommeHT_cmd'].")";
	}
	if($name == 'DISPLAY')
	    return $champ[$id_select];
	else return selectTag($name,$champ,$id_select,$class,$autre,TRUE,$GLOBALS['PropsecConf']['DBPool']);
    }
    else return "<i>pas de commande enregistrées</i>";
}


/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function  BlockSendmail($id_cont,$data,$opt = "new") {
    if($id_cont != '') {
	$id_cont = $id_cont;
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT * FROM entreprise,contact WHERE entreprise_cont = id_ent AND id_cont = '".$id_cont."'");
	$cont = $bddtmp->process();
	$cont1 = $cont[0];
	if(is_array($data))
	    $cont = array_merge($cont1,$data);
	else  $cont = $cont1;
    }


    $tag['formName'] = "SendMail";
    if($opt == 'modif') {
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','id_cont','','','',$id_cont,'');
    }
    else {
	$opt = "new";
	$cont['sender'] = $_SESSION['user']['mail'];
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','id_cont','','','',$id_cont,'');
    }


    $tag['type'] = selectTag('type', array('mail' => 'Par e-mail', 'fax' => 'Par fax', 'courrier' => 'Par Courrier'), 'mail');
    $tag['message'] = $cont['message'];
    $tag['liste_sender'] = $_SESSION['user']['fullnom']." (".$_SESSION['user']['mail'].")".inputTag('hidden', 'sender','','', '',$cont['sender']);
    if ($cont['mail_cont'] != '')
	$tag['destinataire'] =  $cont['civ_cont']." ".$cont['prenom_cont']." ".$cont['nom_cont']." (".$cont['mail_cont'].")".
		inputTag('hidden','to','','','',$cont['mail_cont']);
    else  $tag['destinataire'] =  inputTag('text','to','','','',$cont['to']);
    $tag['titre'] 	= inputTag('text','titre','','',60,$cont['titre'],' style="float:left"');
    $tag['mess']	= textareaTag('mess',$cont['mess'],'',60,10,' id="MessageToSend" style="float:left"');

    foreach ($GLOBALS['ZunoSendMail'] as $key => $val) {
	$k = explode('.',$key,2);
	if($k[0] == 'pj')
	    $toto[$k[1]] = utf8_decode($val);
    }
    $tag['fileChoose'] = selectTag("file[]",$toto,'','','style="height:100px" multiple="multiple"',FALSE);
    $tag['fileChoose1'] = '';
    if($data['fileAttach'] != '')
	$tag['fileChoose1'] = '<div class="row"><div class="label">Fichier additionnel : </div><div class="field">'.$data['fileAttach'].
		inputTag('hidden','fileAdd','','','',$data['fileAttach']).'</div></div>';

    $tag['copie'] = inputTag('checkbox', 'cc', '', '', '', $_SESSION['user']['mail']);
    $input = array();
    $output= array();
    $cleanFrom = array("\n","\t","\r");
    $cleanTo  = array("\\n","\\t","\\r");
    foreach ($cont as $in => $out) {
	$cleanFrom[] = "{".$in."}";
	$cleanTo[] = $out;
    }
    loadPlugin(array('docGenerator'));
    foreach ($a = docGeneratorGetZunoConfInfo() as $in => $out) {
	$cleanFrom[] = "{".$in."}";
	$cleanTo[] = $out;
    }

    $i = 0;


    $corps = templating('prospec/SendMail',$tag);

    $titre 	= imageTag('../img/prospec/contact.png','resultat')." Envoi d'un mail à ".$cont['civ_cont']." ".$cont['prenom_cont']." ".$cont['nom_cont'];
    $pied 	= '<a name="annuler" onclick="zuno.popup.close();">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
		   <a name="valider" onclick="zuno.business.formTools.sendFormAjah(\'SendMail\', \'prospec/PopupSendMail.php\', \'ficheInterne\');zuno.popup.close();">'.imageTag('../img/prospec/record.png','','middle').' Envoyer</a>';

    return generateZBox($titre,$titre,$corps,$pied,'sendMail','');
}



?>