<?php
loadPlugin(array('ZModels/ContactModel','ZDoc/ContactDoc', 'ZView/GeneralView', 'ZunoRenduHTML'));

class contactView extends generalView {
    protected function infos($datas = array(),$control = null) {
	return '<div id="interneInfos">'.$this->interneInfos($datas,'open1',$control).'</div>';
    }

    protected function interneInfos($datas, $statut = 'open1',$control = null, $message = '') {
	if(array_key_exists('data', $datas)) {
	    $data = $datas['data'];
	    $titre = '<img src="../img/prospec/entreprise.png" alt="entreprise" title="Entreprise" /> Informations sur l\'entreprise '.$data['nom_ent'];
	    $pied = '<a title="Reset" href="javascript:document.LightInfos.reset();" ><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
	    $pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'LightInfos\', \'prospec/fiche.php\',\'interneInfos\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Modifier cette entreprise</a>';
	    $pied .= '<a title="fil RSS" href="../rss.php?type=client&token='.$data['tokenRss'].'" style="cursor: pointer;"><img align="middle" title="fil RSS" alt="fil RSS" src="../img/prospec/rss.png"/> Consulter le fil RSS</a>';
	    $pied1 = '<a title="Reset" href="javascript:document.BigInfos.reset();" ><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
	    $pied1 .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'BigInfos\', \'prospec/fiche.php\',\'interneInfos\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Modifier cette entreprise</a>';
	}
	else {
	    $datas['data'] = array();
	    $titre = '<img src="../img/prospec/entreprise.png" alt="entreprise" title="Entreprise" /> Nouvelle entreprise';
	    $pied = '<a title="Reset" href="javascript:document.LightInfos.reset();" ><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
	    $pied .= '<a name="submit" onclick="changeToDo(\'creerEnt\');zuno.business.formTools.sendFormAjah(\'LightInfos\', \'prospec/fiche.php\',\'ficheComplete\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Créer</a>';
	    $pied1 = '<a title="Reset" href="javascript:document.BigInfos.reset();" ><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
	    $pied1 .= '<a name="submit" onclick="changeToDo(\'creerEnt\');zuno.business.formTools.sendFormAjah(\'BigInfos\', \'prospec/fiche.php\',\'ficheComplete\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Créer</a>';
	}

	if($control != null and is_array($control) and $control[0] == false)
	    $message = '<div class="important">'.$control[1].'</div>';

	$corps = $message.'<form name="LightInfos" action="#" method="post">';
	$corps .= '<input type="hidden" id="toDO" name="action" value="modifLightEnt" />';
	$corps .= '<input type="hidden" name="id_ent" id="idEntrepriseLightInfo" value="'.$data['id_ent'].'" />';
	$corps .= '<div id="ContenuLightInfos">';
	$corps .= $this->interneLightInfos($datas);
	$corps .= '</div></form>';

	$corps1 = $message.'<form name="BigInfos" action="#" method="post">';
	$corps1 .= '<input type="hidden" id="toDO2" name="action" value="modifBigEnt" />';
	$corps1 .= '<input type="hidden" name="id_ent" value="'.$data['id_ent'].'" />';
	$corps1 .= '<div id="ContenuBigInfos">';
	$corps1 .= $this->interneBigInfos($datas);
	$corps1 .= '</div></form>';


	return generateZBox3($titre, $titre, $titre, $corps, $pied, $corps1, $pied1,'infosEnt',$statut, true);

    }

    protected function interneInfosSimple($datas, $statut = 'close',$control = null, $popup = false, $message = '') {
	if(array_key_exists('data', $datas)) {
	    $data = $datas['data'];
	    $titre = '<img src="../img/prospec/entreprise.png" alt="entreprise" title="Entreprise" /> Informations sur l\'entreprise '.$data['nom_ent'];
	    $pied = '<a title="Reset" href="javascript:document.LightInfos.reset();" ><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
	    if($popup)
		$pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'LightInfos\', \'prospec/fiche.php\',\'entrepriseDevis\', \'popup2\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Modifier cette entreprise</a>';
	    else
		$pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'LightInfos\', \'prospec/fiche.php\',\'interneInfos\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Modifier cette entreprise</a>';
	    $pied .= '<a title="fil RSS" href="../rss.php?type=client&token='.$data['tokenRss'].'" style="cursor: pointer;"><img align="middle" title="fil RSS" alt="fil RSS" src="../img/prospec/rss.png"/> Consulter le fil RSS</a>';
	}
	else {
	    $datas['data'] = array();
	    $titre = '<img src="../img/prospec/entreprise.png" alt="entreprise" title="Entreprise" /> Nouvelle entreprise';
	    $pied = '<a title="Reset" href="javascript:document.LightInfos.reset();" ><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
	    $pied .= '<a name="submit" onclick="changeToDo(\'creerEnt\');zuno.business.formTools.sendFormAjah(\'LightInfos\', \'prospec/fiche.php\',\'ficheComplete\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Créer</a>';
	}

	if($control != null and is_array($control) and $control[0] == false)
	    $message = '<div class="important">'.$control[1].'</div>';

	$corps = $message.'<form name="LightInfos" action="#" method="post">';
	if($popup)
	    $corps .= '<input type="hidden" name="action" value="modifEntPopup" />';
	else
	    $corps .= '<input type="hidden" id="toDO" name="action" value="modifLightEnt" />';
	$corps .= '<input type="hidden" name="id_ent" id="idEntrepriseLightInfo" value="'.$data['id_ent'].'" />';
	$corps .= '<div id="ContenuLightInfos">';
	$corps .= $this->interneLightInfos($datas);
	$corps .= '</div></form>';


	return generateZBox($titre, $titre, $corps, $pied,'infosSimpleEnt',$statut);

    }

    private function interneLightInfos($datas) {
	$data = $datas['data'];
	if($data['siege_ent'] == '1')
	    $siege = 'checked="checked"';

	$web = ($data['www_ent'] != "") ? $data['www_ent'] : "http://";

	$out = '<div class="block width50">' .
		'<fieldset class="form">' .
		'<legend>Information sur l\'entreprise</legend>' .
		'<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.nom);">Nom</div>' .
		'<div class="field">'.inputTag('text', 'nom_ent', '', '', '16', $data['nom_ent']).'</div>' .
		'</div>' .
		'<div class="row">'.
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.type);">Type</div>'.
		'<div class="field">'.selectTag('type_ent', $datas['type'], $data['type_ent'],'','',false).'</div>'.
		'</div>'.
		'</fieldset></div>';
	$out .= '<div class="block width50">'.
		'<fieldset class="form">'.
		'<legend>Coordonnées</legend>' .
		'<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.web);">Web</div>' .
		'<div class="field">'.inputTag('text', 'www_ent', '', '', '16', $web).'</div>' .
		'</div>' .
		'<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.fax);">Fax</div>' .
		'<div class="field">'.inputTag('text', 'fax_ent', '', '', '16', prepareTelAffichage($data['fax_ent'])).'</div>' .
		'</div>'.
		'<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.cpVille);">CP - Ville</div>
							<div class="field">'.inputTag('text', "cp_ent", 'civ', '', '6', $data['cp_ent'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'entVille\');"').'<select name=\'ville_ent\'  id="entVille" class="nextciv" onblur="finishEditing();" onclick="beginEditing(this);" ><option value=""> </option><option value="'.$data['ville_ent'].'" selected="selected">'.$data['ville_ent'].'</option></select></div>
						</div>'.
		'<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.tel);">Téléphone</div>' .
		'<div class="field">'.inputTag('text', 'tel_ent', '', '', '16', prepareTelAffichage($data['tel_ent'])).'</div>' .
		'</div>'.
		'</fieldset></div>';
	return $out;
    }

    private function interneBigInfos($datas) {
	$data = $datas['data'];
	if($data['siege_ent'] == '1')
	    $siege = 'checked="checked"';
	$tva = ($data['tauxTVA_ent'] == '') ? $GLOBALS['zunoClientStatut']['tauxTVA'] : $data['tauxTVA_ent'];

	$out = '<div class="block width50">
			<fieldset class="form">
				<legend>Adresse</legend>
			  	<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.nom);">Nom</div>

					<div class="field">'.inputTag('text', "nom_ent", '', '', '30', $data['nom_ent']).'</div>
				</div>
				<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.add);">Adresse</div>
					<div class="field">'.inputTag('text', "add1_ent", '', '', '40', $data['add1_ent']).'</div>
				</div>
				<div class="row">
					<div class="label">&nbsp;</div>
					<div class="field">'.inputTag('text', "add2_ent", '', '', '40', $data['add2_ent']).'</div>

				</div>
				<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.cpVille);">CP - Ville</div>
					<div class="field">'.inputTag('text', "cp_ent", 'civ', '', '6', $data['cp_ent'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'ent2Ville\');"').'<select name=\'ville_ent\'  id="ent2Ville" class="nextciv" onblur="finishEditing();" onclick="beginEditing(this);" ><option value=""> </option><option value="'.$data['ville_ent'].'" selected="selected">'.$data['ville_ent'].'</option></select></div>
				</div>
				<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.pays);">Pays</div>
					<div class="field">'.selectTag('pays_ent',$datas['pays'],$data['pays_ent'], '', '', false).'</div>
				</div>
			</fieldset>';

	$web = ($data['www_ent'] != "") ? $data['www_ent'] : "http://";

	$out .= '<fieldset class="form">'.
		'<legend>Autres coordonnées</legend>' .
		'<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.tel);">Téléphone</div>' .
		'<div class="field">'.inputTag('text', 'tel_ent', '', '', '16', prepareTelAffichage($data['tel_ent'])).'</div>' .
		'</div>' .
		'<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.fax);">Fax</div>' .
		'<div class="field">'.inputTag('text', 'fax_ent', '', '', '16', prepareTelAffichage($data['fax_ent'])).'</div>' .
		'</div>'.
		'<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.web);">Site internet</div>' .
		'<div class="field">'.inputTag('text', 'www_ent', '', '', '16', $web).'</div>' .
		'</div>' .
		'</fieldset></div>';
	$out .= '<div class="block width50">
					<fieldset class="form">
						<legend>Profil de l\'entreprise</legend>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.type);">Type</div>
							<div class="field">'.selectTag('type_ent', $datas['type'], $data['type_ent'], '', '', false).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.activite);">Activité</div>
							<div class="field">'.selectTag('activite_ent', $datas['activite'], $data['activite_ent']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.effectif);">Effectif</div>
							<div class="field">'.inputTag('text', 'effectif_ent', '', '', '16', $data['effectif_ent']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.groupe);">Maison mère</div>
							<div class="field">'.selectTag('groupe_ent', $datas['groupe'], $data['groupe_ent']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.siege);">Siège</div>
							<div class="field"><input type="checkbox" '.$siege.' name="siege_ent" value="1" /></div>
						</div>
					</fieldset>
				</div>

				<div class="block width50">
					<fieldset class="form">
						<legend>Autres informations</legend>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.nfourn);">N° fournisseur</div>
							<div class="field">'.inputTag('text', 'codefourn_ent', '', '', '16', $data['codefourn_ent']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.nsiret);">N° SIRET</div>
							<div class="field">'.inputTag('text', 'SIRET_ent', '', '', '16', $data['SIRET_ent']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.ntva);">N° TVA</div>
							<div class="field">'.inputTag('text', 'numeroTVA_ent', '', '', '16', $data['numeroTVA_ent']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.tva);">Taux TVA</div>
							<div class="field">'.selectTVATag('tauxTVA_ent', array(), $tva).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.remise);">Taux remise</div>
							<div class="field">'.inputTag('text', 'remise_ent', '', '', '16', prepareNombreAffichage($data['remise_ent'])).'</div>
						</div>

					</fieldset>
				</div>

				<div class="block width50">
					<fieldset class="form">
						<legend>Commentaire</legend>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.commentaire);">Commentaire</div>
							<div class="field"><textarea rows="3" cols="35" name="commsociete_ent">'.$data['commsociete_ent'].'</textarea></div>
						</div>
					</fieldset>
				</div>';

	return $out;
    }

    protected function contactsLies($datas) {
	if(is_array($datas['data']['contact'][0]))
	    $corps = '<div id="ContenuContactsEntreprise" >'.$this->interneContacts($datas).'</div>';
	else
	    $corps = '<div id="ContenuContactsEntreprise" ><span class="important" style="text-align:center;">Aucun contact pour cette entreprise</span></div>';
	$titre = '<img src="../img/prospec/result.png" alt="liste" title="Liste" /> Contacts de l\'entreprise '.$datas['data']['nom_ent'];
	$pied = '<a name="addcont" onclick="addContact(\'\');" ><img alt="add" title="Ajouter un contact" src="../img/prospec/add.png" /> Ajouter un contact </a>';
	return generateZBox($titre,$titre,$corps,$pied,'contactsEntreprise','');

    }

    protected function interneContacts($datas, $mess = '') {
	$compteur = 0;
	$out = $mess.'<div class="blockTable">' .
		'<table width="100%" cellspacing="0" cellpadding="2" border="0">' .
		'<tbody>' .
		'<tr class="titre">
							<th width="5%" rowspan="2">Nom</th>
							<th width="5%" rowspan="2">Fonction</th>
							<th width="5%" colspan="4">Coordonnées</th>
							<th rowspan="2">Commentaires</th>
							<th width="1%" rowspan="2"> </th>
						</tr>' .
		'<tr class="subtitre">
							<th width="5%" nowrap="nowrap">Tel</th>
							<th width="5%" nowrap="nowrap">Portable</th>
							<th width="5%" nowrap="nowrap">Fax</th>
							<th width="5%" nowrap="nowrap">e-mail</th>
						</tr>';
	if(is_array($datas['data']['contact']))
	    foreach($datas['data']['contact'] as $v) {
		$out .= '<tr class="altern'.($compteur % 2).'">';
		$out .= '<td nowrap="nowrap" class="barre" ><a href="Contact.php?id_cont='.$v['id_cont'].'">'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].'</a></td>';
		$out .= '<td nowrap="nowrap" class="center barre" ><a href="Contact.php?id_cont='.$v['id_cont'].'">'.$v['nom_fct'].'</a></td>';
		$out .= '<td nowrap="nowrap" class="center" ><a href="Contact.php?id_cont='.$v['id_cont'].'">'.prepareTelAffichage($v['tel_cont']).'</a></td>';
		$out .= '<td nowrap="nowrap" class="center" ><a href="Contact.php?id_cont='.$v['id_cont'].'">'.prepareTelAffichage($v['mob_cont']).'</a></td>';
		$out .= '<td nowrap="nowrap" class="center" ><a href="Contact.php?id_cont='.$v['id_cont'].'">'.prepareTelAffichage($v['fax_cont']).'</a></td>';
		$out .= '<td nowrap="nowrap" class="center" ><a href="Contact.php?id_cont='.$v['id_cont'].'">'.$v['mail_cont'].'</a></td>';
		$out .= '<td nowrap="nowrap" class="barre" ><a href="Contact.php?id_cont='.$v['id_cont'].'">'.$v['comm_cont'].'</a></td>';
		$out .= '<td nowrap="nowrap" class="center">';
		$out .= '<a name="appel" title="Nouvel appel à ce contact" onclick="appelContact(\''.$v['id_cont'].'\')"><img alt="appel" title="Enregistrer un nouvel appel" src="../img/prospec/appel.png" /></a>';
		$out .= '<a name="modif" title="Modifier le contact" onclick="modifContact(\''.$v['id_cont'].'\')"><img alt="modif" title="Modifier le Contact" src="../img/prospec/contact.modif.png" /></a>';
		$out .= '<a name="move" title="Déplacer ce contact" onclick="moveContact(\''.$v['id_cont'].'\')"><img alt="deplacer" title="Déplacer ce contact" src="../img/prospec/contact.move.png" /></a>';
		$out .= '<a name="supp" title="Supprimer ce contact" onclick="suppContact(\''.$v['id_cont'].'\')"><img alt="supprimer" title="Supprimer ce contact" src="../img/prospec/contact.delete.png" /></a>';
		$out .= '</td>';
		$out .= '</tr>';
		$compteur++;
	    }
	$out .= '</table></div>';
	return $out;
    }

    protected function projets($datas) {
	if(is_array($datas['projet'][0]) or is_array($datas['affaire'][0]))
	    $corps = '<div id="ContenuProjetsEntreprise" >'.$this->interneProjets($datas).'</div>';
	else
	    $corps = '<div id="ContenuProjetsEntreprise" ><span class="important" style="text-align:center;">Aucun projet pour cette entreprise</span></div>';
	$titre = '<img src="../img/prospec/result.png" alt="liste" title="Liste" /> Projets de l\'entreprise '.$datas['data']['nom_ent'];
	$pied = '<a name="bouttonAddproj" onclick="addProjetE(\'\');" ><img alt="add" title="Ajouter un projet" src="../img/prospec/add.png" /> Ajouter un projet </a>';
	return generateZBox($titre,$titre,$corps,$pied,'projetEntreprise','');
    }

    protected function interneProjets($datas, $mess = '') {
	$compteur = 0;
	$out = $mess.'<div class="blockTable">
		    <table width="100% border="0" cellspacing="0" cellpadding="2">
		    <tr class="titre">
			<th colspan="2" width="10%">Titre</th>
			<th>Type</th>
			<th>Description</th>
			<th width="5%">Detecté le</th>
			<th width="5%">Contact</th>
			<th width="1%"> </th>
		    </tr>';
	if(is_array($datas['projet']))
	    foreach($datas['projet'] as $v) {
		if($v['detect_proj'] != '')
		    $v['detect_proj'] = strftime('%d/%m/%Y', strtotime($v['detect_proj']));
		$jsurl =  ' onclick="modifProj(\''.$v['id_proj'].'\');"';
		$out .= '<tr class="altern'.($compteur %2).'">
				<td style="white-space: nowrap; color:#827F00"'.$jsurl.'><div style="width:14px;height:14px;background-color:#827F00;float:left;margin-right:.5em">&nbsp;</div> Projet</td>
				<td class="center barre" nowrap="nowrap"'.$jsurl.'>'.$v['titre_proj'].'</td>
				<td class="center barre" nowrap="nowrap">'.$v['nom_typro'].'</td>
				<td class="center barre" nowrap="nowrap">'.$v['desc_proj'].'</td>
				<td class="center barre" nowrap="nowrap">'.$v['detect_proj'].'</td>
				<td class="center barre" nowrap="nowrap">'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].'</td>
				<td class="center" nowrap="nowrap"><a name="modifProj"'.$jsurl.'><img alt="modif" title="Modifier ce projet" src="../img/prospec/projet.modif.png" /></a><a name="suppProj" onclick="suppProj(\''.$v['id_proj'].'\');"><img alt="supp" title="Supprimer ce projet" src="../img/prospec/projet.delete.png" /></a></td>
			</tr>';
		$compteur++;
	    }
	if(is_array($datas['affaire']))
	    foreach($datas['affaire'] as $v) {
		if($v['detect_aff'] != '')
		    $v['detect_aff'] = strftime('%d/%m/%Y', strtotime($v['detect_aff']));
		$url = '../draco/Affaire.php?id_aff='.$v['id_aff'];
		$jsurl =  ' onclick="document.location.href=\''.$url.'\'"';
		$out .= '<tr class="altern'.($compteur %2).'" >
				<td style="white-space: nowrap; color:#007F00"'.$jsurl.'><div style="width:14px;height:14px;background-color:#007F00;float:left;margin-right:.5em">&nbsp;</div> Affaire</td>
				<td class="center barre" nowrap="nowrap"'.$jsurl.'>'.$v['id_aff'].'</td>
				<td class="center barre" nowrap="nowrap">'.$v['titre_aff'].'</td>
				<td class="barre nowrap" style="color:#'.$v['color_staff'].'"><div style="width:14px;height:14px;background-color:#'.$v['color_staff'].';float:left;margin-right:.5em">&nbsp;</div> '.$v['nom_staff'].'</td>
				<td class="center barre" nowrap="nowrap">'.$v['detect_aff'].'</td>
				<td class="center barre" nowrap="nowrap">'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].'</td>
				<td class="center" nowrap="nowrap"><a href="'.$url.'"><img alt="affaire" title="Voir l\'affaire" src="../img/prospec/affaire.png"</a></td>
			</tr>';
		$compteur++;
	    }

	$out .= '</table>
			</div>';
	return $out;
    }

    protected function appels($datas) {
	if(is_array($datas['appel'][0]))
	    $corps = '<div id="ContenuAppelsEntreprise" >'.$this->interneAppels($datas).'</div>';
	else
	    $corps = '<div id="ContenuAppelsEntreprise" ><span class="important" style="text-align:center;">Aucun appel passé à cette entreprise</span></div>';
	$titre = '<img src="../img/prospec/result.png" alt="liste" title="Liste" /> Appels passés à l\'entreprise '.$datas['data']['nom_ent'];
	$pied = '<a name="addapp" onclick="addAppel(\'\');" ><img alt="add" title="Ajouter un appel" src="../img/prospec/add.png" /> Ajouter un appel </a>';
	return generateZBox($titre,$titre,$corps,$pied,'appelsEntreprise','');
    }

    protected function interneAppels($datas, $mess = '') {
	$compteur = 0;
	$out = $mess .'<div class="blockTable">
			<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr class="titre">
				<th width="7%">Appel</th>
				<th width="7%">IC</th>
				<th width="7%">Rappel</th>
				<th width="7%">Contact</th>
				<th>Commentaires</th>
				<th width="1%"> </th>
			</tr>';
	if(is_array($datas['appel']))
	    foreach($datas['appel'] as $v) {
		if($v['rappel_app'] != '')
		    $v['rappel_app'] = strftime('%d/%m/%Y', strtotime($v['rappel_app']));
		$out .= '<tr class="altern'.($compteur % 2).'">
				<td class="center barre" nowrap="nowrap">'.strftime('%d/%m/%Y', strtotime($v['appel_app'])).'</td>
				<td class="barre" nowrap="nowrap"><a name="user" onclick="return zuno.popup.open(\'../User.php\',\'type=popup&id='.$v['utilisateur_app'].'\',"680","300","","","",\'User\');">'.$v['utilisateur_app'].'</a></td>
				<td class="center barre" nowrap="nowrap"><b>'.$v['rappel_app'].'</b></td>
				<td class="barre" nowrap="nowrap">'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].'</td>
				<td class="barre">'.$v['comm_app'].'</td>
				<td class="center" nowrap="nowrap">
				<a name="modifApp" title="Modifier" onclick="modifAppel(\''.$v['id_app'].'\');"><img alt="modif" title="Modifier l\'appel" src="../img/prospec/appel.modif.png" /></a>
				<a name="suppApp" title="Supprimer" onclick="suppAppel(\''.$v['id_app'].'\');"><img alt="supp" title="Supprimer l\'appel" src="../img/prospec/appel.delete.png" /></a>
				</td>
			</tr>';
		$compteur++;
	    }

	$out .= '</table></div>';
	return $out;
    }

    protected function contactsInfos($datas) {
	if(is_array($datas['data'])) {
	    $titre = '<img src="../img/prospec/contact.png" alt="contact" title="Contact" /> Informations sur le contact '.$datas['data']['prenom_cont'].' '.$datas['data']['nom_cont'];
	    $pied = '<a title="Reset" href="javascript:document.infosContact.reset();" ><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
	    $pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'infosContact\', \'prospec/Contact.php\',\'interneInfos\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Modifier ce contact</a>';
	}
	else {
	    $datas['data'] = array();
	    $titre = '<img src="../img/prospec/contact.png" alt="contact" title="Contact" /> Nouveau contact';
	    $pied = '<a title="Reset" href="javascript:document.LightInfos.reset();" ><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Recommencer</a>';
	    $pied .= '<a name="submit" onclick="changeToDo(\'creerCont\');zuno.business.formTools.sendFormAjah(\'infosContact\', \'prospec/Contact.php\',\'ficheComplete\');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Créer</a>';
	}
	$corps = '<form name="infosContact" action="#" method="post">';
	$corps .= '<input type="hidden" id="toDO" name="action" value="modifCont" />';
	$corps .= '<input type="hidden" name="id_cont" value="'.$datas['data']['id_cont'].'" />';
	$corps .= '<div id="interneInfos">';
	$corps .= $this->interneInfosContact($datas);
	$corps .= '</div></form>';
	return generateZBox($titre, $titre, $corps, $pied, 'infosCont');
    }

    protected function interneInfosContact($datas) {
	$data = $datas['data'];
	$mail = ($data['mail_cont'] != '') ? '<img alt="mail" title="Envoyer un mail" src="../img/prospec/SendMail.png" onclick="zuno.popup.open(\'PopupSendMail.php\',\'id_cont='.$data['id_cont'].'\');" />' : '';
	$out .= '<div class="block width33">
					<fieldset class="form">
						<legend>Détails du contact</legend>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.civ);">Civilité</div>
							<div class="field">'.selectTag('civ_cont', array('M.'=>'Monsieur', 'Mme'=>'Madame', 'Melle'=>'Mademoiselle'), $data['civ_cont']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.prenom);">Prénom</div>
							<div class="field">'.inputTag('text', 'prenom_cont', '', '', '16', $data['prenom_cont']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.nom);">Nom</div>
							<div class="field">'.inputTag('text', 'nom_cont', '', '', '16', $data['nom_cont']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.fonction);">Fonction</div>
							<div class="field">'.selectTag('fonction_cont', $datas['fonctions'], $data['fonction_cont']).'</div>
						</div>
					</fieldset>
				</div>

				<div class="block width33">
					<fieldset class="form">
						<legend>Coordonnées</legend>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.add);">Adresse</div>
							<div class="field">'.inputTag('text', 'add1_cont', '', '', '16', $data['add1_cont']).'</div>
						</div>
						<div class="row">
							<div class="label">Complément</div>
							<div class="field">'.inputTag('text', 'add2_cont', '', '', '16', $data['add2_cont']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.cpVille);">CP - Ville</div>
							<div class="field">'.inputTag('text', 'cp_cont', 'civ', '', '6', $data['cp_cont'], 'onchange="zuno.ajax.get.html(\'../ajaxRef.php\', \'action=villeForCp&cp=\'+this.value, \'contVille\');"').'<select name="ville_cont" id="contVille" class="nextciv" onblur="finishEditing();" onclick="beginEditing(this);" ><option value=""></option><option value="'.$data['ville_cont'].'" selected="selected">'.$data['ville_cont'].'</option></select></div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.pays);">Pays</div>
							<div class="field">'.selectTag('pays_cont', $datas['pays'], $data['pays_cont']).'</div>
						</div>
					</fieldset>
				</div>

				<div class="block width33">
					<fieldset class="form">
						<legend>Autres Coordonnées</legend>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.tel);">Téléphone</div>
							<div class="field">'.inputTag('text', 'tel_cont', '', '', '16', prepareTelAffichage($data['tel_cont'])).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.mob);">Mobile</div>
							<div class="field">'.inputTag('text', 'mob_cont', '', '', '16', prepareTelAffichage($data['mob_cont'])).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.fax);">Fax</div>
							<div class="field">'.inputTag('text', 'fax_cont', '', '', '16', prepareTelAffichage($data['fax_cont'])).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.mail);">E-mail</div>
							<div class="field">'.inputTag('text', 'mail_cont', '', '', '16', $data['mail_cont']).$mail.'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.web);">Site internet</div>
							<div class="field">'.inputTag('text', 'www_cont', '', '', '16', $data['civ_cont']).'</div>
						</div>
					</fieldset>
				</div>
				<div class="block width33">
					<fieldset class="form">
						<legend>Commentaires</legend>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.commentaire);">Commentaires</div>
							<div class="field"><textarea name="comm_cont">'.$data['comm_cont'].'</textarea></div>
						</div>
					</fieldset>
				</div>';
	if($data['id_ent'] != null)
	    $out .= '<div class="block width33">
					<fieldset class="form">
						<legend>Entreprise</legend>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.entrepriseNom);">Nom</div>
							<div class="field"><a href="fiche.php?id_ent='.$data['id_ent'].'" title="Voir la fiche entreprise">'.$data['nom_ent'].'</a></div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.entrepriseType);">Type</div>
							<div class="field">'.$data['nom_tyent'].'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.entrepriseWeb);">Site internet</div>
							<div class="field">'.$data['www_ent'].'</div>
						</div>
					</fieldset>
				</div>';
	return $out;
    }

    public function popupCont($data, $light ='', $mess='', $action='addContPopup') {
	if($light == 'erreurCont') {
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->formPopupCont($data, $action);
	}
	else {
	    if($action == 'modifContPopup')
		$titre = $data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'];
	    else
		$titre = 'Nouveau Contact';
	    $corps = '<div id="popupAddCont" >';
	    $corps .= $this->formPopupCont($data, $action);
	    $corps .= '</div>';
	    $pied = '<a href="javascript:document.addContDevis.reset();" ><img align="middle" title="Annuler" alt="cancel.png" name="img" src="../img/prospec/cancel.png"/>Annuler</a>';
	    if($data['from'] == 'contactEntreprise')
		$pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'addContDevis\', \'prospec/Contact.php\',\'ContenuContactsEntreprise\', \'popup2\');"><img align="middle" title="Enregistrer" alt="record.png" name="img" src="../img/prospec/record.png"/>Enregistrer</a>';
	    else
		$pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'addContDevis\', \'prospec/Contact.php\',\'popupAddCont\', \'popup\');"><img align="middle" title="Enregistrer" alt="record.png" name="img" src="../img/prospec/record.png"/>Enregistrer</a>';
	    return generateZBox($titre, $titre, $corps, $pied, 'popupAddContDevis');
	}
    }

    private function formPopupCont($data, $action = 'addContPopup') {
	if($data['idRetour'] == '' and $data['retour'] != '')
	    $data['idRetour'] = $data['retour'];

	$corps = '<form name="addContDevis" method="post" action="Contact.php">';
	$corps .= '<input type="hidden" name="entreprise_cont" value="'.$data['entreprise_cont'].'" />';
	$corps .= '<input type="hidden" name="action" value="'.$action.'" />';
	$corps .= '<input type="hidden" name="from" value="'.$data['from'].'" />';
	$corps .= '<input type="hidden" name="retour" value="'.$data['idRetour'].'" />';
	$corps .= '<input type="hidden" name="idChamp" value="'.$data['idChamp'].'" />';
	$corps .= '<div class="block width33">';
	$corps .= '<fieldset class="form"><legend>Détail du contact</legend>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.civ);">Civilité</div>';
	$corps .= '<div class="field">'.selectTag('civ_cont', array('M.'=>'Monsieur', 'Mme'=>'Madame', 'Melle'=>'Mademoiselle'), $data['civ_cont'], '', '', false).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.prenom);">Prenom</div>';
	$corps .= '<div class="field">'.inputTag('text', 'prenom_cont', '', '', '', $data['prenom_cont']).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.nom);">Nom</div>';
	$corps .= '<div class="field">'.inputTag('text', 'nom_cont', '', '', '', $data['nom_cont']).'</div></div>';
	$corps .= '</fieldset>';
	$corps .= '<fieldset class="form"><legend>Autres Coordonnées</legend>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.mail);">Mail</div>';
	$corps .= '<div class="field">'.inputTag('text', 'mail_cont', '', '', '', $data['mail_cont']).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.tel);">Téléphone</div>';
	$corps .= '<div class="field">'.inputTag('text', 'tel_cont', '', '', '', prepareTelAffichage($data['tel_cont'])).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.mob);">Portable</div>';
	$corps .= '<div class="field">'.inputTag('text', 'mob_cont', '', '', '', prepareTelAffichage($data['mob_cont'])).'</div></div>';
	$corps .= '</fieldset></div>';
	$corps .= '<div class="block width33">';
	$corps .= '<fieldset class="form"><legend>Coordonnées postales</legend>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.add);">Adresse</div>';
	$corps .= '<div class="field">'.inputTag('text', 'add1_cont', '', '', '', $data['add1_cont']).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label">Complément</div>';
	$corps .= '<div class="field">'.inputTag('text', 'add2_cont', '', '', '', $data['add2_cont']).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.cpVille);">CP - Ville</div>';
	$corps .= '<div class="field">'.inputTag('text','cp_cont','civ','',4,$data['cp_cont'],' id="contcp" onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'contVille\');"').selectTag('ville_cont',array($data['ville_cont']),$data['ville_cont'], 'nextciv',' id="contVille"  onblur="finishEditing();" onclick="beginEditing(this);"').'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactPers.pays);">Pays</div>';
	$corps .= '<div class="field">'.selectTag('pays_cont', $data['listePays'], $data['pays_cont'],'','', false).'</div></div>';
	$corps .= '</fieldset></div></form>';
	return $corps;
    }

    public function popupEnt($data, $light ='', $mess='') {
	if($light == 'erreurEnt') {
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->formPopupEnt($data);
	}
	else {
	    $titre = 'Nouvelle Entreprise';
	    $corps = '<div id="popupAddEnt" >';
	    $corps .= $this->formPopupEnt($data);
	    $corps .= '</div>';
	    $pied = '<a href="javascript:document.addEntDevis.reset();" ><img align="middle" title="Annuler" alt="cancel.png" name="img" src="../img/prospec/cancel.png"/>Annuler</a>';
	    $pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\'addEntDevis\', \'prospec/Contact.php\',\'popupAddEnt\', \'popup\');"><img align="middle" title="Enregistrer" alt="record.png" name="img" src="../img/prospec/record.png"/>Enregistrer</a>';
	    return generateZBox($titre, $titre, $corps, $pied, 'popupAddEntDevis');
	}
    }

    private function formPopupEnt($data) {

	$tva = ($data['tauxTVA_ent'] != "") ? $data['tauxTVA_ent'] : $GLOBALS['zunoClientStatut']['tauxTVA'];

	$corps = '<form name="addEntDevis" method="post" action="Contact.php">';
	$corps .= '<input type="hidden" name="action" value="addEntPopup" />';
	$corps .= '<div class="block width33">';
	$corps .= '<fieldset class="form"><legend>Informations sur l\'entreprise</legend>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.nom);">Nom</div>';
	$corps .= '<div class="field">'.inputTag('text', 'nom_ent', '', '', '', $data['nom_ent']).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.type);">Type</div>';
	$corps .= '<div class="field">'.selectTag('type_ent', $data['types'], $data['type_ent'], '', '', false).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.ttva);">Taux TVA</div>';
	$corps .= '<div class="field">'.selectTVATag('tauxTVA_ent', array(), $tva, '', '', false).'</div></div>';
	$corps .= '</fieldset></div>';
	$corps .= '<div class="block width33">';
	$corps .= '<fieldset class="form"><legend>Autres coordonnées</legend>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.fax);">Faximile</div>';
	$corps .= '<div class="field">'.inputTag('text', 'fax_ent', '', '', '', prepareTelAffichage($data['fax_ent'])).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.tel);">Téléphonne</div>';
	$corps .= '<div class="field">'.inputTag('text', 'tel_ent', '', '', '', prepareTelAffichage($data['tel_ent'])).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.web);">Site Web</div>';
	$corps .= '<div class="field">'.inputTag('text', 'www_ent', '', '', '', $data['www_ent']).'</div></div>';
	$corps .= '</fieldset></div>';
	$corps .= '<div class="block width33">';
	$corps .= '<fieldset class="form"><legend>Coordonnées postales</legend>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.add);">Adresse</div>';
	$corps .= '<div class="field">'.inputTag('text', 'add1_ent', '', '', '', $data['add1_ent']).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label">Complément</div>';
	$corps .= '<div class="field">'.inputTag('text', 'add2_ent', '', '', '', $data['add2_ent']).'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.cpVille);">CP - Ville</div>';
	$corps .= '<div class="field">'.inputTag('text','cp_ent','civ','',4,$data['cp_ent'],' id="entcp" onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'entVille\');"').selectTag('ville_ent',array($data['ville_ent']),$data['ville_ent'], 'nextciv',' id="entVille"  onblur="finishEditing();" onclick="beginEditing(this);"').'</div></div>';
	$corps .= '<div class="row">';
	$corps .= '<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.pays);">Pays</div>';
	$corps .= '<div class="field">'.selectTag('pays_ent', $data['listePays'], $data['pays_ent']).'</div></div>';
	$corps .= '</fieldset></div></form>';
	return $corps;
    }

    public function listeResult($datas) {
	return '<div id=listeResultProspec>'.$this->ResultProspec($datas).'</div>';
    }

    private function ResultProspec($datas) {
	$suffixeLink = '../';
	$linkOrderFunction = 'tagLinkOrderSwitch';
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit']) {
	    $suffixeLink = './';
	    $linkOrderFunction = 'tagNoLinkOrderSwitch';
	}

	$out = "<script type=\"text/javascript\">
		function exportTab() {
		    if ($('divExportTableur').style.display == 'inline')
			$('divExportTableur').style.display = 'none';
		    else
			$('divExportTableur').style.display = 'inline';
		}
		function contactDoExportTableur(value) {
		    $('resultContactAction').value = 'exportTableur';
		    $('resultContactExportType').value = value;
		    $('resultFormContact').submit();
		}
		</script>";
	$out .= '<div class="blockTable">
		    <form id="resultFormContact" method="post" action="Recherche.php" name="resultContact">
		    <input type="hidden" name="action" value="" id="resultContactAction" />
		    <input type="hidden" name="exportType" value="" id="resultContactExportType" />
		    <table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">';
	$out .= '<th class="barre"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormContact\');"/></th>';
	$out .= '<th class="center barre">'.$this->$linkOrderFunction('Entreprise','nom_ent',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeContactResultPage',$suffixeLink).'</th>';
	$out .= '<th class="barre"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormContact\');"/></th>';
	$out .= '<th class="center barre">'.$this->$linkOrderFunction('Contact','nom_cont',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeContactResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Ville','ville_ent',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeContactResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Tel','tel_cont',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeContactResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('e-mail','mail_cont',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeContactResultPage',$suffixeLink).'</th>';
	$out .= '</tr>';
	$alternance =  0;
	foreach($datas['data'] as $result) {
	    $action = '';
	    if($result['id_ent'] == '')
		$link = $suffixeLink."prospec/Contact.php?id_cont=".$result['id_cont'];
	    else $link = $suffixeLink."prospec/fiche.php?id_ent=".$result['id_ent'];

	    if ($result['nom_ent'] != '') {
		if (strlen($result['nom_ent']) > 25)
		    $result['nom_ent'] = substr($result['nom_ent'],0,25)."...";
		if ($result['type_ent'] != '')
		    $result['nom_ent']	= imageTag($suffixeLink.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$result['type_ent'].'.png',$result['nom_tyent']).' '.$result['nom_ent'];
		$result['contact'] = $result['nom_ent'];
		if($result['ville_cont'] == '') $result['ville_cont'] = $result['ville_ent'];
		if($result['tel_cont'] == '')   $result['tel_cont']   = $result['tel_ent'];
		if($result['mail_cont'] == '')  $result['mail_cont']  = $result['mail_ent'];
	    }
	    else {
		$result['nom_ent'] = '<i>Particulier</i>';
		$result['nom_ent']	= imageTag($suffixeLink.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').' '.$result['nom_ent'];
		$result['contact'] = $result['nom_ent'];
		if($result['ville_cont'] == '') $result['ville_cont'] = $result['ville_ent'];
		if($result['tel_cont'] == '')   $result['tel_cont']   = $result['tel_ent'];
		if($result['mail_cont'] == '')  $result['mail_cont']  = $result['mail_ent'];
	    }
	    $out .= '<tr class="altern'.$alternance.'">';
	    $out .= '<td class="barre" style="width:10px"><input type="checkbox" name="selectE[]" value="'.$result['id_ent'].'" checked="checked"/></td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['contact'].'</td>';
	    $out .= '<td class="barre" style="width:10px"><input type="checkbox" name="selectC[]" value="'.$result['id_cont'].'" checked="checked"/></td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['civ_cont'].' '.$result['prenom_cont'].' '.$result['nom_cont'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['ville_cont'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.prepareTelAffichage($result['tel_cont']).'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['mail_cont'].'</td>';
	    //				$out .= '<td class="barre right" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['id_app'].'</td>';
	    $out .= '</tr>';
	    $alternance = (($alternance+1) % 2);
	}

	$out .= '</tbody></table>';
	$out .= $this->ResultGroupActionForm();
	$out .= '</form></div>';
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit'])
	    $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'contactHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'contact');

	$bouton .= $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeContactResultPage",$suffixeLink);
	loadPlugin(array('OOConverter'));
	$availableConvFormat = OOConverterAvailable('spreadsheet');
	$bouton .= '<div style="display:none;" id="divExportTableur">'.selectTag('format_export', $availableConvFormat, '', '', ' onchange="contactDoExportTableur(this.value);" ').'</div>';
	$bouton .= '<a name="export" style="cursor:pointer;" onclick="exportTab();"><img title="exportation tableur" alt="exportation tableur" src="'.$suffixeLink.'img/prospec/export-csv.png"/> Export Tableur</a>';
	$bouton .= $this->ResultGroupActionBouton($suffixeLink);
	return generateZBox($titre,$titre,$out,$bouton,"searchListeContact",'');
    }



    private function ResultGroupActionForm() {
	$sql = new contactEntrepriseModel();
	$out = '<div id="divGroupAction" style="display: none"><div class="block width50">';
	$fieldset = new ZunoFieldset('Type d\'action');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttributeC', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttributeC\')"'),'Changer un attribut de contact','ZTips.contactEnt.groupActionChangeAttributeC');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttributeE', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttributeE\')"'),'Changer un attribut d\'entreprise','ZTips.contactEnt.groupActionChangeAttributeE');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'deleteC', ' onclick="toggleSubGroupedAction(\'groupedSubActionDeleteC\')"'),'Supprimer les contacts','ZTips.contactEnt.groupActionDeleteC');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'deleteE', ' onclick="toggleSubGroupedAction(\'groupedSubActionDeleteE\')"'),'Supprimer les entreprises','ZTips.contactEnt.groupActionDeleteE');
	$out.= $fieldset->generateFieldset();
	$out.= '</div><div class="block width50" id="groupedSubAction">';
	$fieldset = new ZunoFieldset('Information des contacts','groupedSubActionChangeAttributeC');
	$fieldset->ligneSelect('Fonction', 'fonction_cont','',$sql->getFonction());
	$fieldset->ligneInput('Commentaire', 'comm_cont');
	$fieldset->ligneInput('Ville', 'ville_cont');
	$fieldset->ligneInput('CP', 'cp_cont');
	$fieldset->ligneRadio('Relance tel', 'relactive_cont', ' ', array(array('label'=>'inchangé','value'=>' '),array('label'=>'non','value'=>'0'),array('label'=>'oui','value'=>'1')));
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Information des entreprises','groupedSubActionChangeAttributeE');
	$fieldset->ligneSelect('Activité', 'activite_ent','',$sql->getActivitesEnt());
	$fieldset->ligneSelect('Type', 'type_ent','',$sql->getTypesEnt());
	$fieldset->ligneInput('Remise', 'remise_ent');
	$fieldset->ligneInput('Effectifs', 'effectif_ent');
	$fieldset->ligneInput('Commentaire', 'commsociete_ent');
	$fieldset->ligneInput('Ville', 'ville_ent');
	$fieldset->ligneInput('CP', 'cp_ent');
	$fieldset->ligneFree('TVA',selectTVATag("tauxTVA_ent", array(), '', '', '', true));
	$fieldset->ligneRadio('Siège', 'siege_ent', ' ', array(array('label'=>'inchangé','value'=>' '),array('label'=>'non','value'=>'0'),array('label'=>'oui','value'=>'1')));
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Supprimer les contacts','groupedSubActionDeleteC');
	$fieldset->addOtherLigne('<div class="row"><i>Seul les contacts qui ne sont liés à aucun document seront supprimés</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Supprimer les entreprises','groupedSubActionDeleteE');
	$fieldset->addOtherLigne('<div class="row"><i>Seul les entreprises qui ne sont liés à aucun document seront supprimés</i></div>');
	$out.= $fieldset->generateFieldset();
	$out.= '</div>';
	$out.= '<div class="block width50">';
	$fieldset = new ZunoFieldset('Executer le changement');
	$fieldset->ligneLinkTo('','javascript:submitGroupAction(\'resultFormContact\',\'resultContactAction\');" class="bouton','Effectuer les changements','../img/prospec/voir.png');
	$out.= $fieldset->generateFieldset();
	$out.= '</div></div>';
	return $out;
    }

    private function ResultGroupActionBouton($suffixeLink) {
	$bouton = '<a name="groupAction" style="cursor:pointer;" onclick="toggleGroupedAction(\'divGroupAction\');"><img title="actions groupées" alt="actions groupées" src="'.$suffixeLink.'img/prospec/work-multiple.png"/> Actions groupées</a>';
	return $bouton;
    }





    /**
     * Fonction Result utilisée pour le bureau
     * @deprecated 1 - 26 oct. 2009
     */
    private function Result($datas) {
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit'])
	    $suffixeLink = './';
	else $suffixeLink = '../';

	$out  = '<div class="blockTable"><table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">
			<th class="center barre">Entreprise</th>
			<th class="center barre">Contact</th>
			<th>Ville</th>
			<th>Tel</th>
			<th>e-mail</th>
			<th class="last right">type</th>
		</tr>';
	$alternance =  0;
	foreach($datas['data'] as $v) {
	    foreach($v as $result) {
		$action = '';
		if($result['id_ent'] == '')
		    $link = $suffixeLink."prospec/Contact.php?id_cont=".$result['id_cont'];
		else $link = $suffixeLink."prospec/fiche.php?id_ent=".$result['id_ent'];

		if ($result['nom_ent'] != '') {
		    if (strlen($result['nom_ent']) > 25)
			$result['nom_ent'] = substr($result['nom_ent'],0,25)."...";
		    if ($result['type_ent'] != '')
			$result['nom_ent']	= imageTag($suffixeLink.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$result['type_ent'].'.png',$result['nom_tyent']).' '.$result['nom_ent'];
		    $result['contact'] = $result['nom_ent'];
		    if($result['ville_cont'] == '') $result['ville_cont'] = $result['ville_ent'];
		    if($result['tel_cont'] == '')   $result['tel_cont']   = $result['tel_ent'];
		    if($result['mail_cont'] == '')  $result['mail_cont']  = $result['mail_ent'];
		}
		else {
		    $result['nom_ent'] = '<i>Particulier</i>';
		    $result['nom_ent']	= imageTag($suffixeLink.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').' '.$result['nom_ent'];
		    $result['contact'] = $result['nom_ent'];
		}
		$out .= '<tr class="altern'.$alternance.'">';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['contact'].'</td>';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['civ_cont'].' '.$result['prenom_cont'].' '.$result['nom_cont'].'</td>';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['ville_cont'].'</td>';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.prepareTelAffichage($result['tel_cont']).'</td>';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['mail_cont'].'</td>';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="window.location=\''.$link.'\'">'.$result['nom_fct'].'</td>';
		$out .= '</tr>';
		$alternance = (($alternance+1) % 2);
	    }
	}

	$out .= '</tbody></table></div>';
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit'])
	    $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'contactHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'contact');
	$bouton = '';
	return generateZBox($titre,$titre,$out,$bouton,"searchListeContact",'');
    }

    /**
     * Fonction appelée pour rechercher des produits.
     * @param $datas	Les données nécessaires
     * @return $retour	Le HTML
     */
    public function searchResult($datas, $light='') {
	if($light == 'result')
	    return $this->ResultProspec($datas);
	else {
	    $script = "<script type=\"text/javascript\">
			function autoCompletage() { var ent = 1; }
			function ChangeContactResultPage(limit,from,order,sens) {
			    ChangeAction('search');
			    if(limit != undefined) ChangeLimit(limit);
			    if(from != undefined)  ChangeFrom(from);
			    if(order != undefined) ChangeOrder(order,sens);
			    ChangeSubmit();
			}
			function ChangeAction(value) {\$('ACTION').value = value;}
			function ChangeFrom(value) { \$('FROM').value = value; }
			function ChangeLimit(value) { \$('LIMIT').value = value; }
			function ChangeOrder(value,sens) {
			    if(sens == undefined) sens = 'ASC';
			    \$('ORDER').value = value;
			    \$('ORDERSENS').value = sens;
			}
			function ChangeSubmit() {
			    zuno.business.formTools.sendFormAjah('searchProspec','prospec/Recherche.php','listeResultProspec');
			}
			</script>";
	    $form = $this->searchForm($datas);
	    $rendu = new ZunoRenduHtml('rechercheProspec');
	    $titreF = imageTag('../img/prospec/contact.png','recherche').' Recherche de contacts';
	    $rendu->generateZBox($titreF,$titreF, $form->generateForm(), $form->generateButtons('listeResultProspec', 'Annuler', 'Rechercher'), 'divSearchProspec', 'open');
	    $rendu->addBox($this->listeResult($datas));
	    $rendu->insertJS($script);
	    return $rendu->generateRendu();
	}

    }

    /**
     * Fonction searchForm qui génère le formulaire de recherche
     * @param array $datas Les données du formulaire
     * @return form Le formulaire généré
     */
    private function searchForm($datas) {
	$liste[0]['value'] = 'entreprise';
	$liste[0]['label'] = 'Recherche sur les entreprises';
	$liste[1]['value'] = 'contact';
	$liste[1]['label'] = 'Recherche sur les contacts';
	$liste[2]['value'] = 'appel';
	$liste[2]['label'] = 'Recherche sur les appels';
	$liste[3]['value'] = 'global';
	$liste[3]['label'] = 'Recherche globale';
	$form = new ZunoForm('searchProspec', 'prospec/Recherche.php', 'POST');
	$fieldset = new ZunoFieldset('Recherche');
	$fieldset->ligneInput('Recherche', 'recherche', $datas['recherche']);
	$fieldset2 = new ZunoFieldset('Type');
	$fieldset2->ligneRadio('Type de recherche', 'type', ($datas['type'] != '') ? $datas['type'] : 'global', $liste);
	$form->newBlock($fieldset->generateFieldset(),'50');
	$form->newBlock($fieldset2->generateFieldset(), '50');
	$form->newInputHidden('action', 'search', 'ACTION');
	$form->newInputHidden('limit', $datas['limit'], 'LIMIT');
	$form->newInputHidden('from', $datas['from'], 'FROM');
	$form->newInputHidden('order','nom_ent','ORDER');
	$form->newInputHidden('orderSens','ASC','ORDERSENS');
	return $form;
    }


    /**
     * Create portlet for calling list
     * @param $date String: date to use for selecting list
     * @param $opt String: option
     * @return HTML portlet ready to insert
     */
    static function BoxSearchAppelForm($valeurs) {
	$listeOrder['utilisateur_app  ASC'] = "Commercial";
	$listeOrder['nom_ent ASC'] = "Entreprise";
	$listeOrder['nom_cont ASC'] = "Contact";
	$listeOrder['nom_fct ASC'] = "Fonction";
	$listeOrder['ville_ent ASC'] = "Ville";
	$listeOrder['type_ent DESC'] = "Type d'entreprise";
	$tag['FormName'] = "SearchAppel";

	//Bouton de la periode
	if ($valeurs['debut'] == '')
	    $valeurs['debut'] = DateUniv2Human('','simpleLong');

	//Choix de l'utilisateur
	if ($valeurs['utilisateur_app'] == '')
	    $valeurs['utilisateur_app'] = $_SESSION["user"]["id"];
	$bddtmp = new Bdd('');
	$bddtmp->makeRequeteFree('SELECT * FROM user WHERE actif = \'1\' ORDER BY nom ASC');
	$res = $bddtmp->process();
	if (count($res) > 0)
	    foreach ($res as $key => $data)
		$champ[$data['login']] = $data['civ']." ".$data['prenom']." ".$data['nom']." (".$data['login'].")";

	//Choix de la relance active
	if ($valeurs['relactive_app'] == '1') {
	    $option = 'checked="checked"';
	}
	else {
	    $option = '';
	}

	//Choix du type de contact
	if ($valeurs['premiercont_app'] == '1')
	    $option1 = 'checked="checked"';
	else $option1 = '';

	//Choix de l'ordre
	$corps = '<form method="post" action="?" name="'.$tag['FormName'].'">
				<div class="block width50">
					<fieldset class="form">
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelDebut);">Date de début</div>
							<div class="field">'.inputDateTag('','debut',$valeurs['debut']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelRelancer);">A relancer</div>
							<div class="field">'.inputTag('checkbox','relactive_app','','','','1',$option).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelCommercial);">Commercial</div>
							<div class="field">'.selectTag('utilisateur_app',$champ,$valeurs['utilisateur_app']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelTypeent);">Type d\'entreprise</div>
							<div class="field">'.HtmlForm::addAutoSelect('type_ent','ref_typeentreprise',$valeurs['type_ent'],'','','',$GLOBALS['PropsecConf']['DBPool']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelOrder);">Ordre de tri</div>
							<div class="field">'.selectTag('order',$listeOrder,$valeurs['order']).'</div>
						</div>
					</fieldset>
				</div>
				<div class="block width50">
					<fieldset class="form">
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelFin);">Date de fin</div>
							<div class="field">'.inputDateTag('','fin',$valeurs['fin']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelPremierc);">Premier contact</div>
							<div class="field">'.inputTag('checkbox','premiercont_app','','','','1',$option1).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelDep);">Département</div>
							<div class="field">'.HtmlForm::addAutoSelect('cp_ent','ref_departement',$valeurs['cp_ent'],'','','',$GLOBALS['PropsecConf']['DBPool']).'</div>
						</div>
						<div class="row">
							<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.searchAppelFonction);">Fct du contact</div>
							<div class="field">'.HtmlForm::addAutoSelect('fonction_cont','ref_fonction',$valeurs['fonction_cont'],'','','',$GLOBALS['PropsecConf']['DBPool']).'</div>
						</div>
					</fieldset>
				</div>
				</form>' ;

	$titre = imageTag('../img/prospec/voir.png','recherche').' Recherche des relances';
	$pied 	= '<a href="javascript:document.'.$tag['FormName'].'.reset()">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
			   <a href="javascript:document.'.$tag['FormName'].'.submit()">'.imageTag('../img/prospec/voir.png','','middle').' Rechercher</a>';
	return generateZBox($titre,$titre,$corps,$pied,'relanceSearch','none');
    }

    /**
     * Create portlet for calling list
     * @param $date String: date to use for selecting list
     * @param $opt String: option
     * @return HTML portlet ready to insert
     */
    static function BoxSearchAppelResult($valeurs,$opt) {
	//Choix de la periode
	if ($valeurs['debut'] != '') {
	    if($valeurs['fin'] != '')
		$reqAdd .= "AND (rappel_app >= '".DateHuman2Univ($valeurs['debut'])."' OR rappel_app <= '".DateHuman2Univ($valeurs['fin'])."') ";
	    else  $reqAdd .= "AND rappel_app = '".DateHuman2Univ($valeurs['debut'])."' ";
	}
	elseif($valeurs['fin'] != '')
	    $reqAdd .= "AND rappel_app = '".$valeurs['fin']."' ";
	//Choix de l'utilisateur
	if ($valeurs['utilisateur_app'] == '')
	    $reqAdd .= "AND utilisateur_app = '".$_SESSION["user"]["id"]."' ";
	else  $reqAdd .= "AND utilisateur_app = '".$valeurs['utilisateur_app']."' ";
	//Choix de la relance active
	if ($valeurs['relactive_app'] == '1')
	    $reqAdd .= 'AND relactive_app = 1 ';
	//Choix du département
	if ($valeurs['cp_ent'] != '')
	    $reqAdd .= 'AND cp_ent LIKE \''.$valeurs['cp_ent'].'%\' ';
	//Choix du type
	if ($valeurs['type_ent'] != '')
	    $reqAdd .= 'AND type_ent = \''.$valeurs['type_ent'].'\' ';
	//Choix de la fonction
	if ($valeurs['fonction_cont'] != '')
	    $reqAdd .= 'AND fonction_cont = \''.$valeurs['fonction_cont'].'\' ';
	//Choix du type de contact
	if ($valeurs['premiercont_app'] == '1')
	    $reqAdd .= 'AND premiercont_app = 1 ';
	else  $reqAdd .= 'AND premiercont_app = 0 ';

	//Choix de l'ordre
	if ($valeurs['order'] == '')
	    $reqOrder = 'rappel_app';
	else  $reqOrder = $valeurs['order'];

	$req = "SELECT utilisateur_app,id_ent,id_cont,id_app,nom_ent,ville_ent,tel_ent,nom_cont,prenom_cont,civ_cont,nom_fct,type_ent,nom_tyent,rappel_app
			FROM contact
			LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
			LEFT JOIN appel ON appel.contact_app = contact.id_cont
			RIGHT JOIN entreprise ON entreprise.id_ent = contact.entreprise_cont
			LEFT JOIN ref_typeentreprise ON ref_typeentreprise.id_tyent = entreprise.type_ent WHERE
			relactive_cont = '1'
			".$reqAdd."
			GROUP BY id_cont
			ORDER BY ".$reqOrder." , rappel_app ASC,nom_ent ASC LIMIT 0,".$GLOBALS['PropsecConf']['list.limite.relance'];

	// requete pour la liste
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();

	// remplissage du tableau
	if (count($res) > 0) {
	    $j=0;
	    foreach ($res as $key => $relance) {
		$relance['pictoType'] = imageTag('../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$relance['type_ent'].'.png',$relance['nom_tyent']);
		$relance['rappel_app'] = DateUniv2Human($relance['rappel_app'], 'simpleLong');
		$relance['altern'] = ($j % 2);
		$tmptab .= templating('prospec/listeRelance.row',$relance);
		$j++;
	    }
	    $tab['liste'] = $tmptab ;
	    $corps = templating('prospec/listeRelance',$tab);
	    $result = '';
	}
	else {
	    $corps = '<span class="important">Vous n\'avez pas de relance aujourd\'hui</span>';
	    $result = 'none';
	}

	$titre = imageTag('../img/prospec/relance.png','resultat').' '.$j.' contacts &agrave; relancer';
	return generateZBox($titre,$titre,$corps,'','relance'.$opt,$result);
    }


    static function MyBureau() {
	$limit = 10;
	$req  = new contactParticulierModel();
	$req1 = new contactEntrepriseModel();
	$total = $req->getDataForHistoriqueVisit($limit,$type = 'COUNT');
	$total1= $req1->getDataForHistoriqueVisit($limit,$type = 'COUNT');
	$result = $req->getDataForHistoriqueVisit($limit);
	$result1= $req1->getDataForHistoriqueVisit($limit);
	$datas['total'] = $total[1][0]['counter']+$total1[1][0]['counter'];
	$datas['data'][] = $result[1];
	$datas['data'][] = $result1[1];
	$datas['from'] = 0;
	$datas['limit'] = $limit;
	$datas['isHistoVisit'] = true;
	$view = new contactView();
	if($datas['total'] > 0)
	    return $view->Result($datas);
	else return "";
    }


}

class contactEntrepriseView extends contactView {

    public function view($datas, $light, $mess) {
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fiche entreprise '.$datas['data']['nom_ent'],'',true);
	$messAdd = ($mess != '') ? '<span class="important" style="text-align:center;" >'.$mess.'</span>' : '';
	$script = '<script type="text/javascript">function autoCompletage() {var toto = 1;}';
	$script .= "\n".'function addContact(idRetour) { var ent=\'action=addContEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function addProjetE(idRetour) { var ent=\'action=addProjetEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function addAppel(idRetour) { var ent=\'action=addAppelEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifContact(cont) { var ent=\'action=modifContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifAppel(cont) { var ent=\'action=modifAppelEnt&entreprise='.$datas['data']['id_ent'].'&appel=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifProj(cont) { var ent=\'action=modifProjetEnt&entreprise='.$datas['data']['id_ent'].'&projet=\'+cont; zuno.popup.open(\'fiche.php\', ent); } ';
	$script .= "\n".'function suppContact(cont) { var ent=\'action=suppContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function suppAppel(cont) { var ent=\'action=suppAppelEnt&entreprise='.$datas['data']['id_ent'].'&appel=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function suppProj(cont) { var ent=\'action=suppProjetEnt&entreprise='.$datas['data']['id_ent'].'&projet=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function appelContact(cont) { var ent=\'action=appelCont&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$script .= "\n".'function autoComplete() { new Ajax.Autocompleter("ACentreprise_cont", "ACentreprise_cont_choix", "../ajaxRef.php?action=listeEntreprise", { paramName: "value", minChars: 2, indicator: "loadingCont",  afterUpdateElement : autoCompleteHidden } ); } ';
	$script .= "\n".'function autoComplete2() { new Ajax.Autocompleter("ACaffaire_app", "ACaffaire_app_choix", "../ajaxRef.php?action=listeAffaire&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingAff", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACprojet_app", "ACprojet_app_choix", "../ajaxRef.php?action=listeProjet&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingProj", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_app", "ACcontact_app_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont2", afterUpdateElement : autoCompleteHidden }); } ';
	$script .= "\n".'function autoComplete3() {new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); } ';
	$script .= "\n".'function autoComplete4() { new Ajax.Autocompleter("ACaffaire_app", "ACaffaire_app_choix", "../ajaxRef.php?action=listeAffaire&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingAff", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACprojet_app", "ACprojet_app_choix", "../ajaxRef.php?action=listeProjet&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingProj", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); } ';

	$script .= "\n".'function autoCompleteHidden(text, li) { $(text.id+\'hidden\').value = li.title;} ';
	$script .= "\n".'function moveContact(cont) { var ent=\'action=moveContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function afficherRappel() {if($(\'dateRappel\').style.display == \'none\') { $(\'dateRappel\').style.display = \'table-row\'; $(\'heureRappel\').style.display = \'table-row\'; } else { $(\'dateRappel\').value = ""; $(\'heureRappel\').value = ""; $(\'dateRappel\').style.display = \'none\'; $(\'heureRappel\').style.display = \'none\'; } }';
	$script .= "\n".'function changePopupProjet() {Effect.SlideUp(\'formAppel\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel2\').innerHTML; Effect.SlideDown(\'formProjet\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel2\').innerHTML; } ';
	$script .= "\n".'function changePopupAffaire() {Effect.SlideUp(\'formAppel\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel3\').innerHTML; Effect.SlideDown(\'formAffaire\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel3\').innerHTML; } ';
	$script .= "\n".'function retourFormAppel() {Effect.SlideUp(\'formProjet\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel1\').innerHTML; Effect.SlideDown(\'formAppel\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel1\').innerHTML;} ';
	$script .= "\n".'function retourFormAppel2() {Effect.SlideUp(\'formAffaire\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel1\').innerHTML; Effect.SlideDown(\'formAppel\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel1\').innerHTML;} ';
	$script .= '</script>';
	if($light == 'LightInfoSimple')
	    return $this->interneInfosSimple($datas,'', null, $messAdd);
	if($light == 'LightInfoSimpleBis')
	    return $this->interneInfosSimple($datas,'', null, true, $messAdd);
	elseif($light == 'LightInfo')
	    return $this->interneInfos($datas, 'close', null, $messAdd);
	elseif($light == 'BigInfo')
	    return $this->interneInfos($datas, '1', null, $messAdd);
	elseif($light == 'all')
	    return $messAdd.$this->infos($datas).$this->contactsLies($datas).$this->projets($datas).$this->appels($datas);
	elseif($light == 'listeCont')
	    return $this->interneContacts($datas, $messAdd);
	elseif($light == 'interneAppel')
	    return $this->interneAppels($datas, $messAdd);
	elseif($light == 'interneProjet')
	    return $this->interneProjets($datas, $messAdd);
	else
	    return '<div id="error"></div><div id="ficheInterne">'.$this->infos($datas).$this->contactsLies($datas).$this->projets($datas).$this->appels($datas).'</div>'.$script;

    }



    public function creerEntreprise($datas,$control = null) {
	$script = '<script type="text/javascript">function autoCompletage() {var toto = 1;} function changeToDo(value) {$(\'toDO\').value = $(\'toDO2\').value = value;}';
	$script .= "\n".'function addContact(idRetour) { var ent=\'action=addContEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function addProjetE(idRetour) { var ent=\'action=addProjetEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function addAppel(idRetour) { var ent=\'action=addAppelEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifContact(cont) { var ent=\'action=modifContEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifAppel(cont) { var ent=\'action=modifAppelEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&appel=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifProj(cont) { var ent=\'action=modifProjetEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&projet=\'+cont; zuno.popup.open(\'fiche.php\', ent); } ';
	$script .= "\n".'function suppContact(cont) { var ent=\'action=suppContEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function suppAppel(cont) { var ent=\'action=suppAppelEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&appel=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function suppProj(cont) { var ent=\'action=suppProjetEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&projet=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function appelContact(cont) { var ent=\'action=appelCont&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$script .= "\n".'function autoComplete() { new Ajax.Autocompleter("ACentreprise_cont", "ACentreprise_cont_choix", "../ajaxRef.php?action=listeEntreprise", { paramName: "value", minChars: 2, indicator: "loadingCont",  afterUpdateElement : autoCompleteHidden } ); } ';
	$script .= "\n".'function autoComplete2() { new Ajax.Autocompleter("ACaffaire_app", "ACaffaire_app_choix", "../ajaxRef.php?action=listeAffaire", { paramName:"value", minChars: 2, indicator: "loadingAff", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACprojet_app", "ACprojet_app_choix", "../ajaxRef.php?action=listeProjet", { paramName:"value", minChars: 2, indicator: "loadingProj", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent="+$(\'idEntrepriseLightInfo\').value, { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_app", "ACcontact_app_choix", "../ajaxRef.php?action=listeContact&id_ent="+$(\'idEntrepriseLightInfo\').value, { paramName:"value", minChars: 2, indicator: "loadingCont2", afterUpdateElement : autoCompleteHidden }); } ';
	$script .= "\n".'function autoComplete3() {new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent="+$(\'idEntrepriseLightInfo\').value, { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); } ';
	$script .= "\n".'function autoComplete4() { new Ajax.Autocompleter("ACaffaire_app", "ACaffaire_app_choix", "../ajaxRef.php?action=listeAffaire", { paramName:"value", minChars: 2, indicator: "loadingAff", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACprojet_app", "ACprojet_app_choix", "../ajaxRef.php?action=listeProjet", { paramName:"value", minChars: 2, indicator: "loadingProj", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent="+$(\'idEntrepriseLightInfo\').value, { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); } ';

	$script .= "\n".'function autoCompleteHidden(text, li) { $(text.id+\'hidden\').value = li.title;} ';
	$script .= "\n".'function moveContact(cont) { var ent=\'action=moveContEnt&entreprise=\'+$(\'idEntrepriseLightInfo\').value+\'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function afficherRappel() {if($(\'dateRappel\').style.display == \'none\') { $(\'dateRappel\').style.display = \'table-row\'; $(\'heureRappel\').style.display = \'table-row\'; } else { $(\'dateRappel\').value = ""; $(\'heureRappel\').value = ""; $(\'dateRappel\').style.display = \'none\'; $(\'heureRappel\').style.display = \'none\'; } }';
	$script .= "\n".'function changePopupProjet() {Effect.SlideUp(\'formAppel\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel2\').innerHTML; Effect.SlideDown(\'formProjet\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel2\').innerHTML; } ';
	$script .= "\n".'function retourFormAppel() {Effect.SlideUp(\'formProjet\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel1\').innerHTML; Effect.SlideDown(\'formAppel\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel1\').innerHTML;} ';
	$script .= '</script>';
	return $script.'<div id="error"></div><div id="ficheComplete">'.$this->infos($datas,$control).'</div>';
    }

    public function creerEntrepriseError($datas, $control = null) {
	return $this->infos($datas, $control);
    }

    public function confirmSupp($data) {
	$titre = '<img alt="supp" title="Supprimer Contact" src="../img/prospec/contact.delete.png" /> Supprimer le contact '.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'];
	$corps = '<h4>Confirmez vous la suppression définitive de ce contact ?</h4>';
	$corps .= '<form name="supp" action="fiche.php"><input type="hidden" name="action" value="suppCont" /><input type="hidden" name="id_cont" value="'.$data['id_cont'].'" /><input type="hidden" name="entreprise_cont" value="'.$data['entreprise_cont'].'" /></form>';
	$pied = '<a name="annuler" onclick="zuno.popup.close();"><img alt="annuler" title="Annuler" src="../img/prospec/cancel.png"/> Annuler</a>';
	$pied .= '<a name="valid" onclick="zuno.business.formTools.sendFormAjah(\'supp\', \'prospec/fiche.php\',\'ContenuContactsEntreprise\', \'popup2\');"><img alt="valider" title="Supprimer le contact" src="../img/prospec/confirm.png" /> Valider</a>';
	return generateZBox($titre,$titre,$corps,$pied,'popupSupp','');
    }
    public function confirmSuppAppel($data) {
	$titre = '<img alt="supp" title="Supprimer Appel" src="../img/prospec/appel.delete.png" /> Supprimer l\'appel ';
	$corps = '<h4>Confirmez vous la suppression définitive de cet appel ?</h4>';
	$corps .= '<br />Contact : '.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'].'<br />';
	$corps .= 'Date de l\'appel : '.strftime('%A %d %B %Y', strtotime($data['appel_app']));
	$corps .= '<form name="supp" action="fiche.php"><input type="hidden" name="action" value="suppApp" /><input type="hidden" name="id_app" value="'.$data['id_app'].'" /><input type="hidden" name="entreprise_cont" value="'.$data['entreprise_cont'].'" /></form>';
	$pied = '<a name="annuler" onclick="zuno.popup.close();"><img alt="annuler" title="Annuler" src="../img/prospec/cancel.png"/> Annuler</a>';
	$pied .= '<a name="valid" onclick="zuno.business.formTools.sendFormAjah(\'supp\', \'prospec/fiche.php\',\'ContenuAppelsEntreprise\', \'popup2\');"><img alt="valider" title="Supprimer appel" src="../img/prospec/confirm.png" /> Valider</a>';
	return generateZBox($titre,$titre,$corps,$pied,'popupSupp','');
    }
    public function confirmSuppProjet($data) {
	$titre = '<img alt="supp" title="Supprimer Projet" src="../img/prospec/projet.delete.png" /> Supprimer le projet ';
	$corps = '<h4>Confirmez vous la suppression définitive de ce projet ?</h4>';
	$corps .= '<br />Contact : '.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'].'<br />';
	$corps .= 'Date du projet : '.strftime('%A %d %B %Y', strtotime($data['detect_proj']));
	$corps .= '<form name="supp" action="fiche.php"><input type="hidden" name="action" value="suppProj" /><input type="hidden" name="id_proj" value="'.$data['id_proj'].'" /><input type="hidden" name="entreprise_cont" value="'.$data['entreprise_cont'].'" /></form>';
	$pied = '<a name="annuler" onclick="zuno.popup.close();"><img alt="annuler" title="Annuler" src="../img/prospec/cancel.png"/> Annuler</a>';
	$pied .= '<a name="valid" onclick="zuno.business.formTools.sendFormAjah(\'supp\', \'prospec/fiche.php\',\'ContenuProjetsEntreprise\', \'popup2\');"><img alt="valider" title="Supprimer projet" src="../img/prospec/confirm.png" /> Valider</a>';
	return generateZBox($titre,$titre,$corps,$pied,'popupSupp','');
    }

    public function moveContact($data) {
	$titre = '<img alt="move" title="Déplacer Contact" src="../img/prospec/contact.move.png" onload="autoComplete();" /> Déplacer le contact '.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'];
	$corps = '<form name="move" action="fiche.php">';
	$corps .= '<input type="hidden" name="action" value="moveCont" />';
	$corps .= '<input type="hidden" name="entrepriseC" value="'.$data['entreprise_cont'].'" />';
	$corps .= '<input type="hidden" name="id_cont" value="'.$data['id_cont'].'" />';
	$corps .= '<div class="block width50">';
	$corps .= '<fieldset class="form">';
	$corps .= '<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.contactEnt.ajaxField);">Entreprise</div>
					<div class="field">'.inputTag('text', 'entreprise','icon', '', '16',$data['nom_ent'], ' id="ACentreprise_cont" onchange="verifAutocompleteur(this);" ').'<img style="display:none; margin-left:5px;" id="loadingCont" src="../img/ajax-loader.gif" alt="loader"/></div>
					<input type="hidden" name="entreprise_cont" id="ACentreprise_conthidden" value="'.$data['entreprise_cont'].'" /><input type="hidden" name="contold" id="ACentreprise_contold" value="'.$data['entreprise_cont'].'" />
					<div id="ACentreprise_cont_choix" class="autocomplete"></div>
				</div>';
	$corps .= '</fieldset></div></form>';
	$pied = '<a name="annuler" onclick="zuno.popup.close();"><img alt="annuler" title="Annuler" src="../img/prospec/cancel.png"/> Annuler</a>';
	$pied .= '<a name="valid" onclick="zuno.business.formTools.sendFormAjah(\'move\', \'prospec/fiche.php\',\'ContenuContactsEntreprise\', \'popup2\');"><img alt="valider" title="Déplacer le contact" src="../img/prospec/confirm.png" /> Valider</a>';
	return generateZBox($titre,$titre,$corps,$pied,'popupMove','');

    }

    public function appelContact($data) {
	if($data['id_cont'] != '')
	    $onload = 'onload="autoComplete4();"';
	else
	    $onload = 'onload="autoComplete2();"';

	if($data['id_def'] != '') {
	    $data['id_cont'] = $data['id_def'];
	    $data['civ_cont'] = $data['civ_def'];
	    $data['prenom_cont'] = $data['prenom_def'];
	    $data['nom_cont'] = $data['nom_def'];
	    $data['cont'] = false;
	}
	elseif($data['id_cont'] == '') {
	    $data['cont'] = false;
	}
	else {
	    $data['cont'] = true;
	}

	$titre = '<img alt="move" title="Appeler Contact" src="../img/prospec/appel.png" '.$onload.' /> Appeler le contact '.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'];
	$pied = '<a name="annuler" onclick="zuno.popup.close();"><img alt="annuler" title="Annuler" src="../img/prospec/cancel.png"/> Annuler</a>';
	$pied .= '<a name="valid" onclick="zuno.business.formTools.sendFormAjah(\'appel\', \'prospec/fiche.php\',\'ficheInterne\', \'popup2\');"><img alt="valider" title="Enregistrer l\'appel" src="../img/prospec/confirm.png" /> Enregistrer</a>';
	$corps = '<div id="formAppel">';
	$corps .= '<div id="titrePopupAppel1" style="display:none;">'.$titre.'</div>';
	$corps .= '<div id="titrePopupAppel2" style="display:none;"><img alt="projet" title="Nouveau Projet" src="../img/prospec/projet.png" /> Nouveau projet pour le nouvel appel</div>';
	$corps .= '<div id="piedPopupAppel1" style="display:none;">'.$pied.'</div>';
	$corps .= '<div id="piedPopupAppel2" style="display:none;">';
	$corps .= '<a name="annuler" onclick="retourFormAppel();"><img alt="annuler" title="Annuler" src="../img/prospec/cancel.png"/> Annuler</a>';
	$corps .= '<a name="valid" onclick="zuno.business.formTools.sendFormAjah(\'addProjet\', \'prospec/fiche.php\',\'ContenuProjetsEntreprise\', \'popup\');retourFormAppel();"><img alt="valider" title="Enregistrer l\'appel" src="../img/prospec/confirm.png" /> Enregistrer</a>';
	$corps .= '</div>';
	$corps .= '<form name="appel" method="POST" action="fiche.php">';
	$corps .= '<input type="hidden" name="contact_app" value="'.$data['id_cont'].'" id="ACcontact_apphidden"/>';
	$corps .= '<input type="hidden" name="contold" id="ACcontact_appold" value="'.$data['contact_app'].'" />';
	$corps .= '<input type="hidden" name="id_ent" value="'.$data['id_ent'].'" />';
	if(array_key_exists('id_app', $data)) {
	    $corps .= '<input type="hidden" name="id_app" value="'.$data['id_app'].'" />';
	    $corps .= '<input type="hidden" name="action" value="modifAppelCont" />';
	}
	else {
	    $corps .= '<input type="hidden" name="action" value="addAppelCont" />';
	}
	$corps .= '<div class="block width33">';
	$corps .= '<fieldset class="form">';
	if($data['cont'])
	    $corps .= '<div class="row">' .
		    '<div class="label">Contact</div>' .
		    '<div class="field">'.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'].'</div>' .
		    '</div>';
	else
	    $corps .= '<div class="row">
					<div class="label">Contact</div>
					<div class="field" onmouseover="zuno.tooltip(this,ZTips.contactPers.ajaxField);">'.inputTag('text', 'contact','icon', '', '16',$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'], ' id="ACcontact_app" onchange="verifAutocompleteur(this);" ').'<img style="display:none; margin-left:5px;" id="loadingCont2" src="../img/ajax-loader.gif" alt="loader"/></div>
					<div id="ACcontact_app_choix" class="autocomplete"></div>
				</div>';
	if($data['appel_app'] != '')
	    $appelT = strftime('%d/%m/%Y', strtotime($data['appel_app']));
	else
	    $appelT = date('d/m/Y');
	$corps .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.appelDate);">Date de l\'appel</div>' .
		'<div class="field">'.inputDateTag('text', 'appel_app', $appelT).'</div>' .
		'</div>';
	if($data['rappel_app'] != '') {
	    $check = 'checked="checked"';
	    $style = '';
	}
	else {
	    $check = '';
	    $style = 'style="display:none;"';
	}
	$corps .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.appelRappel);">Prévoir un rappel</div>' .
		'<div class="field"><input type="checkbox" name="rappel" value="1" '.$check.' onchange="afficherRappel();"/></div>' .
		'</div>';
	if($data['rappel_app'] != '')
	    $rappelT = strftime('%d/%m/%Y', strtotime($data['rappel_app']));
	else
	    $rappelT = date('d/m/Y',strtotime('+1 month'));
	$corps .= '<div class="row" id="dateRappel" '.$style.'>' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.appelRappelD);">Date du rappel</div>' .
		'<div class="field">'.inputDateTag('text', 'rappel_app', $rappelT).'</div>' .
		'</div>';
	$corps .= '<div class="row" id="heureRappel" '.$style.'>' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.appelRappelH);">Heure du rappel</div>' .
		'<div class="field">'.inputTag('text', 'heure_app','', '', '16', $data['heure_app']).'</div>' .
		'</div></fieldset></div>';
	$corps .= '<div class="block width33"><fieldset class="form">';
	$corps .= '<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.appelProjet);">Projet</div>
					<div class="field">'.inputTag('text', 'projet','icon', '', '16',$data['titre_proj'], ' id="ACprojet_app" onchange="verifAutocompleteur(this);" ').'<a name="proj" onclick="changePopupProjet();"><img alt="addProjet" title="Créer un projet" src="../img/prospec/add.png" /></a><img style="display:none; margin-left:5px;" id="loadingProj" src="../img/ajax-loader.gif" alt="loader"/></div>
					<input type="hidden" name="projet_app" id="ACprojet_apphidden" value="'.$data['projet_app'].'" /><input type="hidden" name="contold" id="ACprojet_appold" value="'.$data['projet_app'].'" />
					<div id="ACprojet_app_choix" class="autocomplete"></div>
				</div>';
	$corps .= '<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.appelAffaire);">Affaire</div>
					<div class="field">'.inputTag('text', 'affaire','icon', '', '16',$data['affaire_app'], ' id="ACaffaire_app" onchange="verifAutocompleteur(this);" ').'<a name="aff" onclick="changePopupAffaire();"><img alt="addAffaire" title="Créer une affaire" src="../img/prospec/add.png" /></a><img style="display:none; margin-left:5px;" id="loadingAff" src="../img/ajax-loader.gif" alt="loader"/></div>
					<input type="hidden" name="affaire_app" id="ACaffaire_apphidden" value="'.$data['affaire_app'].'" /><input type="hidden" name="contold" id="ACaffaire_appold" value="'.$data['affaire_app'].'" />
					<div id="ACaffaire_app_choix" class="autocomplete"></div>
				</div>';
	$corps .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.contacts.appelComment);">Détails</div>' .
		'<div class="field"><textarea name="comm_app" cols="35" rows="3">'.$data['comm_app'].'</textarea></div>' .
		'</div>';
	$corps .= '</fieldset></div></form>';
	$corps .= '</div>';
	$corps .= $this->internePopupProjet($data);
	$corps .= $this->internePopupAffaire($data);
	return '<div id="erreurPopupAppel"></div>'.generateZBox($titre,$titre,$corps,'<div id="piedPopupID">'.$pied.'</div>','popupAppel','');

    }

    private function internePopupAffaire($data) {
	include_once('AffaireView.inc');
	$aff = new affaireView();
	$block = $aff->popupCreation($data);

	$titre = '<img alt="move" title="Nouvelle affaire" src="../img/prospec/affaire.png"  /> Nouvelle affaire ';

	$pied = '<a name="annuler" onclick="retourFormAppel2();"><img alt="annuler" title="Annuler" src="../img/prospec/cancel.png"/> Annuler</a>';
	$pied .= '<a name="valid" onclick="zuno.business.formTools.sendFormAjah(\'AffaireCreate\', \'draco/AffaireCreate.php\',\'ContenuProjetsEntreprise\', \'popup\');retourFormAppel2();"><img alt="valider" title="Enregistrer l\'appel" src="../img/prospec/confirm.png" /> Enregistrer</a>';

	$out = '<div id="formAffaire" style="display:none;">';
	$out .= $block;
	$out .= '</div>';
	$out .= '<div id="titrePopupAppel3" style="display:none;">'.$titre.'</div>';
	$out .= '<div id="piedPopupAppel3" style="display:none;">'.$pied.'</div>';
	return $out;
    }

    private function internePopupProjet($data) {
	if($data['contact_proj'] == '' and $data['id_cont'] != '')
	    $contact = $data['id_cont'];
	else
	    $contact = $data['contact_proj'];
	$style = '';
	if($data['action'] == 'addProj')
	    $todo = 'addProjet';
	elseif($data['action'] == 'modifProj')
	    $todo = 'modifProj';
	else {
	    $todo = 'addProjAppel';
	    $style = 'style="display:none;"';
	}

	$out = '<div id="formProjet" '.$style.'>';
	$out .= '<form name="addProjet" action="fiche.php" method="POST">';
	$out .= '<input type="hidden" name="id_ent" value="'.$data['id_ent'].'" />';
	$out .= '<input type="hidden" name="action" value="'.$todo.'" />';
	if($data['action'] == 'modifProj')
	    $out .= '<input type="hidden" name="id_proj" value="'.$data['id_proj'].'" />';
	$out .= '<input type="hidden" name="contact_proj" value="'.$contact.'" id="ACcontact_projApphidden" />';
	$out .= '<div class="block width33">';
	$out .= '<fieldset class="form">';
	$out .= '<legend>Projet</legend>';
	$out .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.projets.titre);">Titre</div>' .
		'<div class="field">'.inputTag('text', 'titre_proj', '', '', '16', $data['titre_proj']).'</div>' .
		'</div>';
	$out .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.projets.type);">Type</div>' .
		'<div class="field">'.selectTag('typeproj_proj', $data['types'], $data['typeproj_proj'], '', '', false).'</div>' .
		'</div>';
	$out .= '<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.projets.contact);">Contact</div>
					<div class="field">'.inputTag('text', 'contact2','', '', '16',$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'], ' id="ACcontact_projApp" onchange="verifAutocompleteur(this);" ').'<img style="display:none; margin-left:5px;" id="loadingCont3" src="../img/ajax-loader.gif" alt="loader"/></div>
					<input type="hidden" name="contold" id="ACcontact_projAppold" value="'.$data['contact_proj'].'" />
					<div id="ACcontact_projApp_choix" class="autocomplete"></div>
				</div>';
	if($data['rdv_proj'] != '')
	    $dateRDV = strftime('%d/%m/%Y',strtotime($data['rdv_proj']));
	else
	    $dateRDV = '';
	$out .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.projets.dateRdv);">Date de rendez-vous</div>' .
		'<div class="field">'.inputDateTag('text', 'rdv_proj', $dateRDV).'</div>' .
		'</div>';
	$out .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.projets.heureRdv);">Heure du rendez-vous</div>' .
		'<div class="field">'.inputTag('text', 'heure_proj', '', '', '16', $data['heure_proj']).'</div>' .
		'</div>';
	$out .= '</fieldset>';
	$out .= '</div>';
	$out .= '<div class="block width33">';
	$out .= '<fieldset class="form">';
	$out .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.projets.budget);">Budget (€)</div>' .
		'<div class="field">'.inputTag('text', 'budget_proj', '', '', '16', $data['budget_proj']).'</div>' .
		'</div>';
	$out .= '<div class="row">' .
		'<div class="label" onmouseover="zuno.tooltip(this,ZTips.projets.desc);">Description</div>' .
		'<div class="field"><textarea name="desc_proj" cols="35" rows="3">'.$data['desc_proj'].'</textarea></div>' .
		'</div>';
	$out .= '</fieldset></div>';
	$out .= '</form>';
	$out .= '</div>';
	return $out;
    }

    public function popupProjet($data) {
	$pied .= '<a name="annuler" onclick="zuno.popup.close();"><img alt="annuler" title="Annuler" src="../img/prospec/cancel.png" onload="autoComplete3();"/> Annuler</a>';
	$pied .= '<a name="valid" onclick="zuno.business.formTools.sendFormAjah(\'addProjet\', \'prospec/fiche.php\',\'ContenuProjetsEntreprise\', \'popup2\');"><img alt="valider" title="Enregistrer l\'appel" src="../img/prospec/confirm.png" /> Enregistrer</a>';
	if($data['action'] == 'modifProj')
	    $titre = '<img alt="projet" title="Modifier Projet" src="../img/prospec/projet.modif.png" /> Modifier projet';
	else
	    $titre = '<img alt="projet" title="Nouveau Projet" src="../img/prospec/projet.png" /> Nouveau projet';
	$corps = $this->internePopupProjet($data);
	return '<div id="erreurPopupProj"></div>'.generateZBox($titre, $titre, $corps, $pied, 'popupAddProjet');
    }
}

class contactParticulierView extends contactView {
    public function view($datas, $light = '', $mess = '') {
	$addEntInTitle = ($datas['data']['id_ent'] != '') ? ' ('.$datas['data']['nom_ent'].')' : '';
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fiche contact '.$datas['data']['civ_cont'].' '.$datas['data']['prenom_cont'].' '.$datas['data']['nom_cont'].$addEntInTitle,'',true);
	$script = '<script type="text/javascript">function autoCompletage() {var toto = 1;}';
	$script .= "\n".'function addContact(idRetour) { var ent=\'action=addContEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function addProjet(idRetour) { var ent=\'action=addProjetEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function addAppel(idRetour) { var ent=\'action=addAppelEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifContact(cont) { var ent=\'action=modifContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifAppel(cont) { var ent=\'action=modifAppelEnt&entreprise='.$datas['data']['id_ent'].'&appel=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifProj(cont) { var ent=\'action=modifProjetEnt&entreprise='.$datas['data']['id_ent'].'&projet=\'+cont; zuno.popup.open(\'fiche.php\', ent); } ';
	$script .= "\n".'function suppContact(cont) { var ent=\'action=suppContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function suppAppel(cont) { var ent=\'action=suppAppelEnt&entreprise='.$datas['data']['id_ent'].'&appel=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function suppProj(cont) { var ent=\'action=suppProjetEnt&entreprise='.$datas['data']['id_ent'].'&projet=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function appelContact(cont) { var ent=\'action=appelCont&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$script .= "\n".'function autoComplete() { new Ajax.Autocompleter("ACentreprise_cont", "ACentreprise_cont_choix", "../ajaxRef.php?action=listeEntreprise", { paramName: "value", minChars: 2, indicator: "loadingCont",  afterUpdateElement : autoCompleteHidden } ); } ';
	$script .= "\n".'function autoComplete2() { new Ajax.Autocompleter("ACaffaire_app", "ACaffaire_app_choix", "../ajaxRef.php?action=listeAffaire", { paramName:"value", minChars: 2, indicator: "loadingAff", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACprojet_app", "ACprojet_app_choix", "../ajaxRef.php?action=listeProjet", { paramName:"value", minChars: 2, indicator: "loadingProj", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_app", "ACcontact_app_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont2", afterUpdateElement : autoCompleteHidden }); } ';
	$script .= "\n".'function autoComplete3() {new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); } ';
	$script .= "\n".'function autoComplete4() { new Ajax.Autocompleter("ACaffaire_app", "ACaffaire_app_choix", "../ajaxRef.php?action=listeAffaire&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingAff", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACprojet_app", "ACprojet_app_choix", "../ajaxRef.php?action=listeProjet&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingProj", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); } ';

	$script .= "\n".'function autoCompleteHidden(text, li) { $(text.id+\'hidden\').value = li.title;} ';
	$script .= "\n".'function moveContact(cont) { var ent=\'action=moveContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function afficherRappel() {if($(\'dateRappel\').style.display == \'none\') { $(\'dateRappel\').style.display = \'table-row\'; $(\'heureRappel\').style.display = \'table-row\'; } else { $(\'dateRappel\').value = ""; $(\'heureRappel\').value = ""; $(\'dateRappel\').style.display = \'none\'; $(\'heureRappel\').style.display = \'none\'; } }';
	$script .= "\n".'function changePopupProjet() {Effect.SlideUp(\'formAppel\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel2\').innerHTML; Effect.SlideDown(\'formProjet\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel2\').innerHTML; } ';
	$script .= "\n".'function retourFormAppel() {Effect.SlideUp(\'formProjet\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel1\').innerHTML; Effect.SlideDown(\'formAppel\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel1\').innerHTML;} ';
	$script .= '</script>';
	if($light == 'infosContact')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneInfosContact($datas);
	elseif($light == 'all' or $light == 'Traitement')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->ContactsInfos($datas).$this->contactsLies($datas).$this->projets($datas).$this->appels($datas);
	elseif($light == 'listeCont')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneContacts($datas);
	elseif($light == 'interneAppel')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneAppels($datas);
	elseif($light == 'interneProjet')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneProjets($datas);
	else
	    return $script.'<div id="error"></div><div id="ficheInterne">'.$this->contactsInfos($datas).$this->contactsLies($datas).$this->projets($datas).$this->appels($datas).'</div>';
    }

    public function creerContact($datas) {
	$script = '<script type="text/javascript">function autoCompletage() {var toto = 1;}';
	$script .= "\n".'function addContact(idRetour) { var ent=\'action=addContEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function addProjet(idRetour) { var ent=\'action=addProjetEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function addAppel(idRetour) { var ent=\'action=addAppelEnt&entreprise='.$datas['data']['id_ent'].'&idRetour=\'+idRetour; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifContact(cont) { var ent=\'action=modifContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifAppel(cont) { var ent=\'action=modifAppelEnt&entreprise='.$datas['data']['id_ent'].'&appel=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function modifProj(cont) { var ent=\'action=modifProjetEnt&entreprise='.$datas['data']['id_ent'].'&projet=\'+cont; zuno.popup.open(\'fiche.php\', ent); } ';
	$script .= "\n".'function suppContact(cont) { var ent=\'action=suppContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function suppAppel(cont) { var ent=\'action=suppAppelEnt&entreprise='.$datas['data']['id_ent'].'&appel=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function suppProj(cont) { var ent=\'action=suppProjetEnt&entreprise='.$datas['data']['id_ent'].'&projet=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function appelContact(cont) { var ent=\'action=appelCont&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$script .= "\n".'function autoComplete() { new Ajax.Autocompleter("ACentreprise_cont", "ACentreprise_cont_choix", "../ajaxRef.php?action=listeEntreprise", { paramName: "value", minChars: 2, indicator: "loadingCont",  afterUpdateElement : autoCompleteHidden } ); } ';
	$script .= "\n".'function autoComplete2() { new Ajax.Autocompleter("ACaffaire_app", "ACaffaire_app_choix", "../ajaxRef.php?action=listeAffaire", { paramName:"value", minChars: 2, indicator: "loadingAff", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACprojet_app", "ACprojet_app_choix", "../ajaxRef.php?action=listeProjet", { paramName:"value", minChars: 2, indicator: "loadingProj", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); ';
	$script .= "\n".'new Ajax.Autocompleter("ACcontact_app", "ACcontact_app_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont2", afterUpdateElement : autoCompleteHidden }); } ';
	$script .= "\n".'function autoComplete3() {new Ajax.Autocompleter("ACcontact_projApp", "ACcontact_projApp_choix", "../ajaxRef.php?action=listeContact&id_ent='.$datas['data']['id_ent'].'", { paramName:"value", minChars: 2, indicator: "loadingCont3", afterUpdateElement : autoCompleteHidden }); } ';
	$script .= "\n".'function autoCompleteHidden(text, li) { $(text.id+\'hidden\').value = li.title;} ';
	$script .= "\n".'function moveContact(cont) { var ent=\'action=moveContEnt&entreprise='.$datas['data']['id_ent'].'&contact=\'+cont; zuno.popup.open(\'fiche.php\', ent);} ';
	$script .= "\n".'function afficherRappel() {if($(\'dateRappel\').style.display == \'none\') { $(\'dateRappel\').style.display = \'table-row\'; $(\'heureRappel\').style.display = \'table-row\'; } else { $(\'dateRappel\').value = ""; $(\'heureRappel\').value = ""; $(\'dateRappel\').style.display = \'none\'; $(\'heureRappel\').style.display = \'none\'; } }';
	$script .= "\n".'function changePopupProjet() {Effect.SlideUp(\'formAppel\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel2\').innerHTML; Effect.SlideDown(\'formProjet\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel2\').innerHTML; } ';
	$script .= "\n".'function retourFormAppel() {Effect.SlideUp(\'formProjet\');$(\'ZunoPopupWindowTitle\').innerHTML = $(\'titrePopupAppel1\').innerHTML; Effect.SlideDown(\'formAppel\'); $(\'piedPopupID\').innerHTML = $(\'piedPopupAppel1\').innerHTML;} ';
	$script .= "\n".'function changeToDo (value) {$(\'toDO\').value = value; } ';
	$script .= '</script>';
	return $script.'<div id="ficheComplete">'.$this->contactsInfos($datas).'</div>';

    }
}

?>
