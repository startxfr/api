<?php
class affaireView {

    /**
     * Génération de la liste des résultats de la recherche
     */
    static function searchResult($result, $from = 0, $limit = 5, $total = 0, $qTag = '') {
	if(is_array($result) and count($result) > 0) {
	    $letter = $_SESSION['user']['LastLetterSearch'];
	    $annee = $_SESSION['user']['annee'];
	    foreach($result as $k => $v) {
		//On se balade dans le tableau de résultat de la recherche pour générer la liste.
		$tempannee = substr($v['id_aff'],0,2);
		if($letter != substr($v['id_aff'],2,2) || $annee != $tempannee) {
		    $annee = $tempannee;
		    $letter = substr($v['id_aff'],2,2) ;
		    $list .= '</ul><h2>'.ucfirst(strftime("%B",strtotime('2008-'.$letter.'-27'))).' 20'.substr($v['id_aff'],0,2).'</h2><ul class="iArrow">';
		}
		elseif($from != 0) {
		    $list .= '</ul><ul class="iArrow">';
		}
		$brc = ($v['nom_cont'] != '') ? '<br/>': '';
		//On génère la liste ici :
		$list .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async"><em>'.$v['id_aff'].' - '.$v['titre_aff'].' </em><small><b>'.$v['nom_ent'].'</b>'.$brc.$v['civ_cont'].' '.$v['nom_cont'].' '.$v['prenom_cont'].'</small></a></li>';
	    }
	    $list = substr($list,5).'</ul>';
	    if ($from == 0) {//on affiche le haut de la page juste la première fois.
		$out 	 = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'home.png" alt="Accueil" /></a>' ;
	    }
	    //l'affichage de la liste générée
	    $out	.='<div class="iList">
						'.$list.'
					</div>';
	    if($total > ($limit+$from))
	    //on affiche le bouton : PLUS DE RESULTAT si besoin est.
		$out .= '<div class="iMore" id="searchResultAffaireMore'.$from.'"><a href="Affaire.php?action=searchAffaireContinue&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></div>';

	    $_SESSION['user']['LastLetterSearch'] = $letter;
	    $_SESSION['user']['annee'] = $annee;
	    return $out;
	}
    }

    /**
     * FICHE DE VISUALISATION
     */

    /**
     * Formulaire complet de visualisation d'une affaire.
     */
    static function view($value = array(),$mode = '') {

	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_dev, titre_dev from devis LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev where id_aff = '".$value['id_aff']."' ORDER BY id_dev ASC;");
	$temp = $sqlConn->process2();
	$temp = $temp[1];
	if($temp == array()) {
	    $devis = '';
	}
	else {
	    $devis = '<ul>';
	    foreach($temp as $v) {
		$devis .= '<li><a href="Devis.php?action=view&id_dev='.$v['id_dev'].'" class="Devis" rev="async"><img src="'.getStaticUrl('img').'actualite/devis.png"/> Devis : '.$v['id_dev'].' ['.$v['titre_dev'].']</a></li>';
	    }
	    $devis .='</ul>';
	}

	$sqlConn->makeRequeteFree("select id_cmd, titre_cmd from commande LEFT JOIN devis ON devis.id_dev = commande.devis_cmd where id_dev LIKE '%".$value['id_aff']."%' ORDER BY id_cmd ASC;");
	$temp = $sqlConn->process2();
	$temp = $temp[1];
	if($temp == array()) {
	    $commande = '';
	}
	else {
	    $commande = '<ul>';
	    foreach($temp as $v) {
		$commande .= '<li><a href="Commande.php?action=view&id_cmd='.$v['id_cmd'].'" class="Commande" rev="async"><img src="'.getStaticUrl('img').'actualite/commande.png"/> Commande : '.$v['id_cmd'].' ['.$v['titre_cmd'].']</a></li>';
	    }
	    $commande .='</ul>';
	}

	$sqlConn->makeRequeteFree("select id_fact, titre_fact from facture LEFT JOIN commande ON commande.id_cmd = facture.commande_fact where commande_fact LIKE '%".$value['id_aff']."%' ORDER BY id_fact ASC;");
	$temp = $sqlConn->process2();
	$temp = $temp[1];
	if($temp == array()) {
	    $facture = '';
	}
	else {
	    $facture = '<ul>';
	    foreach($temp as $v) {
		$facture .= '<li><a href="Facture.php?action=view&id_fact='.$v['id_fact'].'" class="Facture" rev="async"><img src="'.getStaticUrl('img').'actualite/facture.png"/> Facture : '.$v['id_fact'].' ['.$v['titre_fact'].']</a></li>';
	    }
	    $facture .='</ul>';
	}

	$descriptif  = ($value['desc_aff'] != '') ? '<br /><small style="color: #888">'.$value["desc_aff"].'</small>' : '';
	$actif	 = ($value['actif_aff'] == 1) ? 'Active' : 'Inactive';
	if($value['archived_aff'] == 1)
	    $archive = ' et archivée.';
	else  $archive = ($value['actif_aff'] == 1) ? '' : ' et non archivée.';
	$creation = $echeance = '';
	if($value['detect_aff'] != NULL)
	    $creation = strftime("%A %d %B %G", strtotime($value['detect_aff']));
	if($value['echeance_aff'] != NULL)
	    $echeance = '<li>Arrive à échance le : <small>'.strftime("%A %d %B %G &agrave %R", strtotime($value['echeance_aff'])).'</small></li>';

	$budget	 = ($value['budget_aff'] != NULL) ? '<li>Budget de l\'affaire : <small>'.formatCurencyDisplay($value['budget_aff']).'</small></li>' : '';
	$commentaire = ($value['comm_aff'] != NULL) ? '<li>Commentaire : <small>'.$value['comm_aff'].'</small></li>' : '';
	$entreprise	 = ($value['entreprise_aff'] != NULL) ? '<li>'.contactEntrepriseView::contactLinkSimple($value).'</li>' : '';
	$contact	 = ($value['contact_aff'] != NULL) ? '<li>'.contactParticulierView::contactLinkSimple($value).'</li>' : '';
	$typeprojet	 = ($value['typeproj_aff'] != 0) ? '<li>Type d\'affaire : <small>'.$value['nom_typro'].'</small></li>' : '';
	//On vient d'effectuer tout un tas de tests pour s'assurer  que l'on ne vas afficher que des blocks avec des choses dedans.

	if($mode == 'afterModif') {
	    $linkHead = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'home.png" alt="Accueil" /></a>';
	}
	else {
	    $linkHead = '<a href="Affaire.php?action=modifAffaire&id_aff='.$value["id_aff"].'"  rev="async" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'edit.png" alt="Modifier" /></a>';
	}

	//On génère maintenant le rendu visuel.
	$out = $linkHead.'<div class="iPanel">
				<fieldset>
					<legend></legend>
					<ul>
						<li><strong>'.$value["titre_aff"].'</strong>'.$descriptif.'</li>'
		.$typeprojet.'
					</ul>
				</fieldset>
				<fieldset>
					<legend>Contacts</legend>
					<ul>
							'.$entreprise.'
							'.$contact.'
					</ul>
				</fieldset>'.self::subBlockRessourcesLiees($value, $devis, $commande, $facture).'
				<fieldset>
					<legend>Autres informations</legend>
					<ul>
						<li>Affaire créée le : <small>'.$creation.'</small></li>
						'.$echeance.$budget.'
					</ul>
					<ul>
						<li>Etat : <small>'.$actif.$archive.' </small></li>
						<li>Status : <small>'.$value['nom_staff'].'</small></li>
						'.$commentaire.'
					</ul>
				</fieldset>
				<fieldset><legend>Actions</legend>' .
		'<ul><li><a href="Devis.php?action=addDevisFromAffaire&id_aff='.$value['id_aff'].'" rev="async"><img src="'.getStaticUrl('img').'prospec/devis.add.png" /> Créer un devis lié</a></li></ul>
				</fieldset>
			</div>';
	return $out;
    }


    /**
     * Fonction qui va gérer le formulaire de modification des affaires
     */
    static function modif($value = array(),$onError = array(),$errorMess = '',$id_aff = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formModifAffaire\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'save.png" alt="Enregistrer" /></a>
				<form id="formModifAffaire" action="Affaire.php?action=doModifAffaire&id_aff='.$id_aff.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockModif($value,$onError).'
				</div>
				</form>';
	return $out;
    }

    static function blockModif($value = array(), $onError = array()) {
	$out = self::subBlockAffaire($value, $onError);
	$out .= self::subBlockStatus($value, $onError);
	$out .= self::subBlockContactsLies($value, $onError);
	$out .= self::subBlockResponsables($value, $onError);
	$out .= self::subBlockAutresInfos($value, $onError);
	$out .= self::subBlockAction($value);
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="'.getStaticUrl('imgPhone').'big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formConnect\', null, event)"><img src="'.getStaticUrl('imgPhone').'big.valider.png" alt="Valider" /></a>
				</fieldset>';
	if($value['supprimable'] == '0')
	    $out .='<br /><br /><a href="Affaire.php?action=suppAffaire&id_aff='.$value["id_aff"].'" rev="async" class="redButton"><img style="float: left;" src="'.getStaticUrl('imgPhone').'delete.png"/><span>Supprimer cette affaire</span></a>';
	$out .='<br /><br /><a href="Affaire.php?action=marqueSuppAffaire&id_aff='.$value["id_aff"].'" rev="async" class="redButton"><span>Marquer comme supprimée</span></a>';
	return $out;
    }
    /**
     * Début de l'ensemble des fonctions qui génère les blocs pour les modifications et créations d'affaires.
     */
    static function subBlockAffaire($value = array(), $onError = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_typro, nom_typro from ref_typeproj;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) {
	    $typeList[$v['id_typro']] = $v['nom_typro'];
	}

	$titre = HtmlFormIphone::InputLabel('titre_aff',$value['titre_aff'],'Nom : ');
	$descriptif = HtmlFormIphone::InputLabel('desc_aff',$value['desc_aff'],'Descriptif : ');
	$titreERR	= (in_array('titre_aff',$onError)) ? '<span class="iFormErr"/>' : '';
	$type = HtmlFormIphone::SelectLabel('typeproj_aff',$typeList,$value['typeproj_aff'],'Type : ', false);
	$typeERR	= (in_array('typeproj_aff',$onError)) ? '<span class="iFormErr"/>' : '';
	$out = '<fieldset>
					<legend>Informations générales</legend>
					<ul>
						<li>'.$titre.$titreERR.'</li>
						<li>'.$descriptif.'</li>
						<li>'.$type.$typeERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }

    static function subBlockStatus($value = array(), $onError = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_staff, nom_staff from ref_statusaffaire;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) $statusList[$v['id_staff']] = $v['nom_staff'];
	$active = ($value['actif_aff'] == NULL) ? 1 : $value['actif_aff'];
	$etat = HtmlFormIphone::Checkbox('actif_aff','active','OUI|NON',$active);
	$etatERR	= (in_array('actif_aff',$onError)) ? '<span class="iFormErr"/>' : '';
	$status = HtmlFormIphone::Select('status_aff',$statusList,$value['status_aff'],false);
	$statusERR	= (in_array('status_aff',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
					<legend>Status</legend>
					<ul>
						<li>'.$etat.$etatERR.'</li>
						<li>'.$status.$statusERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }
    static function subBlockAutresInfos($value = array(), $onError = array()) {

	$timestamp = strtotime($value['echeance_aff']);
	$time = strftime("%d-%m-%G &agrave %R", $timestamp);


	if($time == '01-01-1970 &agrave 01:00') {
	    $time=NULL;
	}
	$commentaire = HtmlFormIphone::InputLabel('comm_aff',$value['comm_aff'],'Autres : ');
	$budget = HtmlFormIphone::InputLabel('budget_aff',$value['budget_aff'],'Budget (&euro;) : ');
	$date = HtmlFormIphone::Inputdate('echeance_aff',$time,'%d/%m/%Y à %H:%M', 'Echéance : ');

	$dateERR	= (in_array('echeance_aff',$onError)) ? '<span class="iFormErr"/>' : '';
	$out = '<fieldset>
					<legend>Informations complémentaires</legend>
					<ul>
						<li>'.$budget.'</li>
						<li>'.$date.$dateERR.'</li>
						<li>'.$commentaire.'</li>

					</ul>
				</fieldset>';
	return $out;

    }
    static function subBlockContactsLies($value = array(), $onError = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_cont, nom_cont, prenom_cont, civ_cont from contact order by nom_cont LIMIT 0, 25;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) {
	    $particulierList[$v['id_cont']] = $v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'];
	}
	$particulier = contactParticulierView::inputAjaxContact('contact_aff',$value['contact_aff'],'Contact : ',false);
	$particulierERR	= (in_array('contact_aff',$onError)) ? '<span class="iFormErr"/>' : '';
	$decideur = contactParticulierView::inputAjaxContact('decid_aff',$value['decid_aff'],'Décideur : ',true);
	$decideurERR	= (in_array('decid_aff',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
					<legend>Contacts</legend>
					<ul>
						<li>'.$particulier.$particulierERR.'</li>
						<li>'.$decideur.$decideurERR.'</li>
					</ul>
				</fieldset>';
	return $out;

    }

    static function subBlockResponsables($value = array(), $onError = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select login, nom, prenom, civ from user order by nom;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) {
	    $userList[$v['login']] = $v['civ'].' '.$v['prenom'].' '.$v['nom'];
	}
	$defaultcomm = ($value['commercial_aff'] == NULL) ? $_SESSION['user']['id'] : $value['commercial_aff'];
	$defaulttech = ($value['technique_aff'] == NULL) ? $_SESSION['user']['id'] : $value['technique_aff'];
	$commercial = HtmlFormIphone::SelectLabel('commercial_aff',$userList,$defaultcomm,'Commercial', false);
	$technicien = HtmlFormIphone::SelectLabel('technique_aff',$userList,$defaulttech,'Technique', false);
	$commercialERR	= (in_array('commercial_aff',$onError)) ? '<span class="iFormErr"/>' : '';
	$technicienERR	= (in_array('technique_aff',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
					<legend>Responsables</legend>
					<ul>
						<li>'.$commercial.$commercialERR.'</li>
						<li>'.$technicien.$technicienERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }

    static function subBlockRessourcesLiees($value = array(), $devis = '', $commande = '', $facture = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(id) as C FROM actualite WHERE id_aff = '".$value['id_aff']."'");
	$temp = $sqlConn->process2();
	$totalActu = $temp[1][0]['C'];
	$out = '<fieldset>
					<legend>Ressources Liées</legend>
					<ul class="iArrow">
						<li><a rev="async" href="Actualite.php?action=viewAffaire&amp;id_aff='.$value['id_aff'].'"><img src="'.getStaticUrl('imgPhone').'actualite.png"/> '.$totalActu.' Actualités</a></li>
					</ul>'.$devis.$commande.$facture.'
				</fieldset>';
	return $out;
    }

    static function subBlockAction($value = array()) {
	if($value['archived_aff'] != '1')
	    $out = '<fieldset>
					<legend>Actions</legend>
					<ul class="iArrow">
						<li><a rev="async" href="Affaire.php?action=archiver&amp;id_aff='.$value['id_aff'].'"><img src="'.getStaticUrl('imgPhone').'archiver.png"/> Archiver cette affaire</a></li>
						<li><a rev="async" href="Affaire.php?action=cloner&amp;id_aff='.$value['id_aff'].'"><img src="'.getStaticUrl('imgPhone').'cloner.png"/> Cloner cette affaire</a></li>
					</ul>
				</fieldset>';
	else $out='';
	return $out;
    }

    static function affaireLinkSimple($value = array()) {
	return '<a href="Affaire.php?action=view&id_aff='.$value['id_aff'].'" class="Affaire" rev="async"><img src="'.getStaticUrl('img').'actualite/affaire.png"/> Affaire : '.$value['id_aff'].' '.$value['titre_aff'].'</a>';
    }


    /**
     * Fieldset complet pour un lien vers la fiche de l'entreprise et les informaitons de localisation (tel, mail, fax, add)
     */
    static function contactLinkWithLinks($value = array()) {
	$b = HtmlElementIphone::linkIconMail($value["mail_ent"],false).
		HtmlElementIphone::linkIconMailWithZSend($value["mail_ent"],false).
		HtmlElementIphone::linkIconTel($value["tel_ent"],false).
		HtmlElementIphone::linkIconFaxWithZSend($value["fax_ent"],false,$value["nom_ent"]).
		HtmlElementIphone::linkIconWeb($value["www_ent"],false).
		HtmlElementIphone::linkIconAddress($value["add1_ent"],$value["add2_ent"],$value["cp_ent"],$value["ville_ent"],$value["nom_pays"],$value["nom_ent"],false);
	HtmlElementIphone::linkIconAddressWithZSend($value["add1_ent"],$value["add2_ent"],$value["cp_ent"],$value["ville_ent"],$value["nom_pays"],$value["nom_ent"],false);
	$i = ($value["type_ent"] != '') ? '<span><img src="'.getStaticUrl('img').'prospec/TypeEntreprise/'.$value["type_ent"].'.png"/></span>' : '';
	$b = ($b != '') ? '<br class="clear"/><div class="listIconLink">'.$b.'<br class="clear"/></div>' : '';
	if($value["cp_ent"] != '' or $value["ville_ent"] != '') $add = $value["cp_ent"].' - '.$value["ville_ent"];
	elseif($value["tel_ent"] != '') $add = $value["tel_ent"];
	else $add = $value["mail_ent"];

	$out = '<a href="Contact.php?action=viewEnt&id_ent='.$value["id_ent"].'" rev="async">'.$i.$value["nom_ent"].'<br/>
							  <small>'.$add.'</small></a>'.$b;
	return $out;
    }

    /**
     * Formulaire complet de visualisation d'une affaire.
     */
    static function archiver($value = array()) {
	return '<div class="iPanel"><br/><br/>
				<div class="msg"><br/>Merci de confirmer l\'archivage de cette affaire<br/></div>
				<br/>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="'.getStaticUrl('imgPhone').'big.annuler.png" alt="Annuler"></a>
					<a href="Affaire.php?action=doArchivage&id_aff='.$value["id_aff"].'" rev="async" class="BigButtonValidRight"><img src="'.getStaticUrl('imgPhone').'big.confirmer.png" alt="confirmer"></a>
				</fieldset>
			</div>';
    }
    /**
     * Gestion de l'affichage de la la création d'une affaire
     */
    static function add($value = array(),$onError = array(),$errorMess = '',$id_aff = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddAffaire\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'save.png" alt="Enregistrer" /></a>
				<form id="formAddAffaire" action="Affaire.php?action=doAddAffaire&id_aff='.$id_aff.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockAdd($value,$onError).'
				</div>
				</form>';
	return $out;
    }

    static function blockAdd($value = array(), $onError = array()) {
	$out = self::subBlockAffaire($value, $onError);
	$out .= self::subBlockContactsLies($value, $onError);
	$out .= self::subBlockResponsables($value, $onError);
	$out .= self::subBlockAutresInfos($value, $onError);
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="'.getStaticUrl('imgPhone').'big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddAffaire\', null, event)"><img src="'.getStaticUrl('imgPhone').'big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }
    /**
     * Gestion de l'affichage de la supression d'une affaire
     */
    static function delete($value = array(), $onfekoi = '') {
	if ($value["id_aff"] == 0 && $onfekoi == '') {
	    $out='<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="'.getStaticUrl('imgPhone').'home.png" alt="Accueil" /></a>
			  <div class="iPanel">
			  <div class="err">
			  		<strong> Affaire supprimé ! </strong>
				</div>
						';
	    return $out;
	}
	$descriptif  = ($value['desc_aff'] != '') ? '<br /><small style="color: #888">'.$value["desc_aff"].'</small>' : '';
	$actif	 = ($value['actif_aff'] == 1) ? 'Active' : 'Inactive';
	if($value['archived_aff'] == 1)
	    $archive = ' et archivée.';
	else  $archive = ($value['actif_aff'] == 1) ? '' : ' et non archivée.';
	$creation = $echeance = '';
	if($value['detect_aff'] != NULL)
	    $creation = strftime("%A %d %B %G", strtotime($value['detect_aff']));
	if($value['echeance_aff'] != NULL)
	    $echeance = '<li>Arrive à échance le : <small>'.strftime("%A %d %B %G &agrave %R", strtotime($value['echeance_aff'])).'</small></li>';

	$budget	 = ($value['budget_aff'] != NULL) ? '<li>Budget de l\'affaire : <small>'.formatCurencyDisplay($value['budget_aff']).'</small></li>' : '';
	$commentaire = ($value['comm_aff'] != NULL) ? '<li>Commentaire : <small>'.$value['comm_aff'].'</small></li>' : '';
	$entreprise	 = ($value['entreprise_aff'] != NULL) ? '<li>'.contactEntrepriseView::contactLinkSimple($value).'</li>' : '';
	$contact	 = ($value['contact_aff'] != NULL) ? '<li>'.contactParticulierView::contactLinkSimple($value).'</li>' : '';
	$typeprojet	 = ($value['typeproj_aff'] != 0) ? '<li>Type d\'affaire : <small>'.$value['nom_typro'].'</small></li>' : '';
	//On vient d'effectuer tout un tas de tests pour s'assurer  que l'on ne vas afficher que des blocks avec des choses dedans.
	if($onfekoi == '') {
	    $messageSupp = '<strong> Êtes vous sur de vouloir supprimer cette affaire ? </strong>';
	    $linkHead = '<a href="Affaire.php?action=doDeleteAffaire&id_aff='.$value["id_aff"].'"  rev="async" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'remove.png" alt="Supprimer" /></a>';
	}
	if($onfekoi == 'marqueSupp') {
	    $messageSupp = '<strong> Êtes vous sur de vouloir marquer cette affaire comme supprimée ?</strong>';
	    $messageSupp .= '<br /><strong> Rien ne serra supprimé, l\'affaire n\'apparaitra plus dans les recherches.</strong>';
	    $linkHead = '<a href="Affaire.php?action=doMarqueSuppAffaire&id_aff='.$value["id_aff"].'&entreprise='.$value['entreprise_aff'].'"  rev="async" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'remove.png" alt="Supprimer" /></a>';
	}
	//On génère maintenant le rendu visuel.
	$out = $linkHead.'<div class="iPanel">' .
		'<div class="err">
			  		'.$messageSupp.'
				</div>
				<fieldset>
					<legend></legend>
					<ul>
						<li><strong>'.$value["titre_aff"].'</strong>'.$descriptif.'</li>'
		.$typeprojet.'
					</ul>
				</fieldset>
				<fieldset>
					<legend>Contacts</legend>
					<ul>
							'.$entreprise.'
							'.$contact.'
					</ul>
				</fieldset>'.self::subBlockRessourcesLiees($value).'
				<fieldset>
					<legend>Autres informations</legend>
					<ul>
						<li>Affaire créée le : <small>'.$creation.'</small></li>
						'.$echeance.$budget.'
					</ul>
					<ul>
						<li>Etat : <small>'.$actif.$archive.' </small></li>
						<li>Status : <small>'.$value['nom_staff'].'</small></li>
						'.$commentaire.'
					</ul>
				</fieldset>
			</div>';
	return $out;
    }
    /**
     * Fonction de gestion de l'affichage de la fiche pour clonage
     */
    static function cloner($value = array()) {
	return '<div class="iPanel"><br/><br/>
				<div class="msg"><br/>Merci de confirmer le clonage de cette affaire<br/></div>
				<br/>
				<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="'.getStaticUrl('imgPhone').'big.annuler.png" alt="Annuler"></a>
					<a href="Affaire.php?action=doCloner&id_aff='.$value["id_aff"].'" rev="async" class="BigButtonValidRight"><img src="'.getStaticUrl('imgPhone').'big.confirmer.png" alt="confirmer"></a>
				</fieldset>
			</div>';
    }
    /**
     * Fin de l'ensemble des fonctions pour création et modifications des affaires.
     */


    static function tri_echeance($value, $limit, $from, $total) {
	$mois = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';

	foreach($value[1] as $k => $v) {
	    if($v['echeance_aff'] != NULL) {
		if(strtotime($v['echeance_aff']) <= time()) {
		    $echeance=strftime("%d/%m/%G", strtotime($v['echeance_aff']));
		    if( $mois != 'expire' ) {
			$out .= '</ul><h2>Expirées</h2><ul class="iArrow">';
		    }
		    $mois = 'expire';
		    $out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'"rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small>Echéance : <b>'.$echeance.'</b></small></a></li>';
		}
		else {
		    if($mois != ucfirst(strftime("%B %G", strtotime($v['echeance_aff'])))) {
			$mois = ucfirst(strftime("%B %G", strtotime($v['echeance_aff'])));
			$out .= '</ul><h2>'.$mois.'</h2><ul class="iArrow">';
		    }
		    $echeance=strftime("%d/%m/%G", strtotime($v['echeance_aff']));
		    $out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small>Echéance : <b>'.$echeance.'</b></small></a></li>';
		}
	    }
	}

	foreach($value[1] as $k => $v) {
	    if($v['echeance_aff'] == NULL) {
		if($mois != 'sansecheance') {
		    $out .='</ul><h2>Sans échéance</h2><ul class="iArrow">';
		    $mois = 'sansecheance';
		}
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small>Echéance : <i>aucune</i></small></a></li>';
	    }
	}
	if($total > ($limit+$from)) {
	    $out .= '<li class="iMore" id="triEcheanceAffaireMore'.$from.'"><a href="Affaire.php?action=triEcheanceMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

	}

	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $mois;
	return $out;
    }

    static function tri_creation($value, $limit, $from, $total) {
	$mois = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';

	foreach($value[1] as $k => $v) {
	    if($v['detect_aff'] != NULL) {

		if($mois != ucfirst(strftime("%B %G", strtotime($v['detect_aff'])))) {
		    $mois = ucfirst(strftime("%B %G", strtotime($v['detect_aff'])));
		    $out .= '</ul><h2>'.$mois.'</h2><ul class="iArrow">';
		}
		$echeance=strftime("%d/%m/%G", strtotime($v['detect_aff']));
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small>Création le : <b>'.$echeance.'</b></small></a></li>';

	    }
	}

	if($total > ($limit+$from)) {
	    $out .= '<li class="iMore" id="triCreationAffaireMore'.$from.'"><a href="Affaire.php?action=triCreationMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

	}

	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $mois;
	return $out;
    }

    static function tri_modification($value, $limit, $from, $total) {
	$mois = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';

	foreach($value[1] as $k => $v) {
	    if($v['modif_aff'] != NULL) {

		if($mois != ucfirst(strftime("%B %G", strtotime($v['modif_aff'])))) {
		    $mois = ucfirst(strftime("%B %G", strtotime($v['modif_aff'])));
		    $out .= '</ul><h2>'.$mois.'</h2><ul class="iArrow">';
		}
		$modification=strftime("%d/%m/%G", strtotime($v['modif_aff']));
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small>Modification le : <b>'.$modification.'</b></small></a></li>';

	    }
	}

	if($total > ($limit+$from)) {
	    $out .= '<li class="iMore" id="triModificationAffaireMore'.$from.'"><a href="Affaire.php?action=triModificationMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

	}

	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $mois;
	return $out;
    }

    static function tri_entreprise($value, $limit, $from, $total) {
	$ent = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';

	foreach($value[1] as $k => $v) {
	    if($v['nom_ent'] != NULL) {
		if($ent != strtoupper($v['nom_ent']{0})) {
		    $ent=strtoupper($v['nom_ent']{0});
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$entreprise=$v['nom_ent'];
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small><b>'.$entreprise.'</b></small></a></li>';

	    }

	    elseif($v['nom_ent'] == NULL) {

		if($ent != 'Sans entreprise') {
		    $ent='Sans entreprise';
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small><b>Aucune entreprise liée</b></small></a></li>';

	    }
	}

	if($total > ($limit+$from)) {
	    $out .= '<li class="iMore" id="triEntrepriseAffaireMore'.$from.'"><a href="Affaire.php?action=triEntrepriseMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

	}

	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $ent;
	return $out;
    }

    static function tri_nomAffaire($value, $limit, $from, $total) {
	$ent = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';

	foreach($value[1] as $k => $v) {
	    if($v['nom_ent'] != NULL) {

		if($ent != strtoupper($v['titre_aff']{0})) {
		    $ent=strtoupper($v['titre_aff']{0});
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$entreprise=$v['nom_ent'];
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small><b>'.$entreprise.'</b></small></a></li>';

	    }
	    else {
		if($ent != strtoupper($v['titre_aff']{0})) {
		    $ent=strtoupper($v['titre_aff']{0});
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$contact=$v['nom_cont'];
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small><b>'.$contact.'</b></small></a></li>';
	    }
	}


	if($total > ($limit+$from)) {
	    $out .= '<li class="iMore" id="triNomAffaireMore'.$from.'"><a href="Affaire.php?action=triNomMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

	}

	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $ent;
	return $out;
    }

    static function tri_contact($value, $limit, $from, $total) {
	$ent = $_SESSION['user']['LastLetterSearch'];
	$out = '<ul>';

	foreach($value[1] as $k => $v) {
	    if($v['nom_cont'] != NULL) {

		if($ent != strtoupper($v['nom_cont']{0})) {
		    $ent=strtoupper($v['nom_cont']{0});
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$entreprise=$v['nom_cont'];
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small><b>'.$entreprise.'</b></small></a></li>';

	    }

	    elseif($v['nom_cont'] == NULL) {

		if($ent != 'Sans contact') {
		    $ent='Sans contact';
		    $out .= '</ul><h2>'.$ent.'</h2><ul class="iArrow">';
		}
		$out .= '<li><a href="Affaire.php?action=view&id_aff='.$v['id_aff'].'" rev="async">'.$v['id_aff'].' - '.$v['titre_aff'].' <small><b>Aucun contact lié</b></small></a></li>';

	    }
	}

	if($total > ($limit+$from)) {
	    $out .= '<li class="iMore" id="triContactAffaireMore'.$from.'"><a href="Affaire.php?action=triContactMore&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></li>';

	}

	$out .= '</ul>';
	$_SESSION['user']['LastLetterSearch'] = $ent;
	return $out;
    }



    static function form_avance() {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_typro, nom_typro from ref_typeproj;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) {
	    $typeList[$v['id_typro']] = $v['nom_typro'];
	}
	$sqlConn->makeRequeteFree("select id_staff, nom_staff from ref_statusaffaire;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) $statusList[$v['id_staff']] = $v['nom_staff'];
	$etatList = array(
		1 => 'Active',
		2 => 'Inactive et non archivée',
		3 => 'Inactive et archivée');
	$affichageList = array(
		1 => 'Par nom d\'affaire',
		2 => 'Par identifiant',
		3 => 'Par entreprise',
		4 => 'Par contact',
		5 => 'Par date d\'échéance',
		6 => 'Par date de création',
		7 => 'Par date de modification');

	$id = HtmlFormIphone::InputLabel('id_aff', '', 'Identifiant : ');
	$titre = HtmlFormIphone::InputLabel('titre_aff', '', 'Nom : ');
	$ent = HtmlFormIphone::InputLabel('nom_ent', '', 'Entreprise : ');
	$cont = HtmlFormIphone::InputLabel('nom_cont', '', 'Contact : ');
	$decid = HtmlFormIphone::InputLabel('nom_decid', '', 'Décideur : ');
	$resp = '<h3>Responsables</h3><ul>';
	$resp .= '<li>'.HtmlFormIphone::InputLabel('login1', '', 'Commercial : ').'</li>';
	$resp .= '<li>'.HtmlFormIphone::InputLabel('login2', '', 'Technique : ').'</li>';
	$resp .= '</ul>';
	$budget = HtmlFormIphone::InputLabel('budget_aff', '', 'Budget : ');
	$description = HtmlFormIphone::InputLabel('desc_aff', '', 'Description : ');
	$echeance = '<h3>Date d\'échéance</h3><ul>';
	$echeance .= '<li>'.HtmlFormIphone::Inputdate('echeance_aff_debut', '', '%d/%m/%Y', 'Début : ').'</li>';
	$echeance .= '<li>'.HtmlFormIphone::Inputdate('echeance_aff_fin', '', '%d/%m/%Y', 'Fin : ').'</li>';
	$echeance .='</ul>';
	$creation = '<h3>Date de création</h3><ul>';
	$creation .= '<li>'.HtmlFormIphone::Inputdate('detect_aff_debut', '', '%d/%m/%Y', 'Début : ').'</li>';
	$creation .= '<li>'.HtmlFormIphone::Inputdate('detect_aff_fin', '', '%d/%m/%Y', 'Fin : ').'</li>';
	$creation .= '</ul>';
	$modification = '<h3>Date de modification</h3><ul>';
	$modification .= '<li>'.HtmlFormIphone::Inputdate('modif_aff_debut', '', '%d/%m/%Y', 'Début : ').'</li>';
	$modification .= '<li>'.HtmlFormIphone::Inputdate('modif_aff_fin', '', '%d/%m/%Y', 'Fin : ').'</li>';
	$modification .= '</ul>';
	$type = HtmlFormIphone::SelectLabel('typeproj_aff', $typeList, '', 'Type d\'affaire : ');
	$status = HtmlFormIphone::SelectLabel('status_aff', $statusList, '', 'Status : ');
	$etat = HtmlFormIphone::SelectLabel('etat', $etatList, '', 'Etat : ');

	$affichage = HtmlFormIphone::Radio('affichage', $affichageList, '', 'Préférences affichage', FALSE);

	$out = '<a href="#"  onclick="return WA.Submit(\'formAvanceAffaire\',null,event)" rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'search.png" alt="Rechercher" /></a>'.
		'<form id="formAvanceAffaire" action="Affaire.php?action=doRechercheAvancee" onsubmit="return WA.Submit(this,null,event)">' .
		'<div class="iPanel">' .
		'<fieldset>' .
		'<ul>' .
		'<li>'.$id.'</li>' .
		'<li>'.$titre.'</li></ul>' .
		'<ul><li>'.$ent.'</li>' .
		'<li>'.$cont.'</li>' .
		'<li>'.$decid.'</li></ul>' .
		$resp.
		'<ul><li>'.$type.'</li>' .
		'<li>'.$status.'</li></ul>' .
		'<ul><li>'.$description.'</li>' .
		'<li>'.$budget.'</li></ul>'.
		$echeance.
		$creation.
		$modification.
		'<ul><li>'.$etat.'</li>' .
		'</ul>' .
		'<ul>'.$affichage.'</ul>'.
		'</fieldset>' .
		'</div>' .
		'</form>';
	return $out;
    }

    static function result_avance ($value) {
	$ordre = $_SESSION['user']['ordre'];
	switch ($ordre) {
	    case 1:
		return '<div class="iList">'.self::tri_nomAffaire($value, 1, 1, 1).'</div>';
		break;
	    case 2:
		return '<div class ="iList">'.self::tri_creation($value, 1, 1, 1).'</div>';
		break;
	    case 3:
		return '<div class ="iList">'.self::tri_entreprise($value, 1, 1, 1).'</div>';
		break;
	    case 4:
		return '<div class ="iList">'.self::tri_contact($value, 1, 1, 1).'</div>';
		break;
	    case 5:
		return '<div class ="iList">'.self::tri_echeance($value, 1, 1, 1).'</div>';
		break;
	    case 6:
		return '<div class ="iList">'.self::tri_creation($value, 1, 1, 1).'</div>';
		break;
	    case 7:
		return '<div class="iList">'.self::tri_modification($value, 1, 1, 1).'</div>';
		break;
	    default :
		return '<div class ="iList">'.self::tri_creation($value, 1, 1, 1).'</div>';
	}
    }

    static function inputAjaxAffaire($nom = '', $selected = '', $titre = '', $withBlank = true) {
	$nom = ($nom != '') ? $nom : 'affaire_dev';
	$titre = ($titre != '') ? '<label style="float:left;">'.$titre.'</label>' : '';
	if($selected != '') {
	    $info = new affaireModel();
	    $result = $info->getDataFromID($selected);
	    if($result[0]) {
		$nomSelected = $result[1][0]['titre_aff'].' ['.$result[1][0]['id_aff'].']';
		$idSelected = $result[1][0]['id_aff'];
	    }
	    elseif(!$withBlank) $nomSelected = '<i>Veuillez choisir une affaire</i>';
	    else			  $nomSelected = '&nbsp;';
	}
	elseif(!$withBlank) $nomSelected = '<i>Veuillez choisir une affaire</i>';
	else			  $nomSelected = '&nbsp;';
	$out = $titre.' <a href="Affaire.php?action=inputAffaire&tag='.$nom.'"  id="'.$nom.'AId" style="float:left;width:70%" rev="async"/> '.$nomSelected.'</a>
			<input type="hidden" name="'.$nom.'" id="'.$nom.'InputId" value="'.$idSelected.'"/><br class="clear"/>';
	return $out;
    }

    static function searchInputResultRow($result,$layerBackTo,$tagsBackTo) {
	$out = '';
	if(is_array($result) and count($result) > 0) {
	    foreach($result as $k => $v) {
		$ent = ($v['nom_ent'] != NULL) ? '<small> ('.$v['nom_ent'].') </small>' : '';
		$n = $v['id_aff'].' '.strtoupper($v['titre_aff']).$ent;
		$out .= '<li><a href="#_'.substr($layerBackTo,2).'" onclick="returnAjaxInputResult(\''.$tagsBackTo.'\',\''.$v['id_aff'].'\',\''.$n.'\')">' .
			'<em>'.$n.'</em>' .
			'</a></li>';
	    }
	}
	return $out;
    }

    static function afficherStats($datas = array(), $type = 'global') {
	if($type == 'global') {
	    $out = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="'.getStaticUrl('imgPhone').'home.png" alt="Accueil" /></a>
			<a href="#" rel="back" class="iButton iBBack" onclick="return WA.Back()">Retour</a>
			<div class="iBlock">
				<h1>Types d\'affaires actives</h1>
					<p><img src="'.getStaticUrl('imgPhone').'Affaire.php?type=typeAffActives"/></p>
					<br/>
				<h1>Types d\'affaires</h1>
					<p><img src="'.getStaticUrl('imgPhone').'Affaire.php?type=typeAffGlobal"/></p>
			</div>';
	    $out .= '<div class="iPanel">';
	    $out .= '<fieldset><legend>Affaires</legend><ul><li>Nombre : '.$datas[0].'</li></ul></fieldset>';
	    $out .= '<fieldset><legend>Devis liés</legend><ul>';
	    $out .= '<li>Nb moyen par affaire : '.$datas[1][0].' </li>';
	    $out .= '<li>Prix moyen : '.$datas[2][0].' &euro;</li>';
	    $out .= '<li>Prix médian : '.$datas[4][0].' &euro;</li>';
	    $out .= '<li>Prix total : '.$datas[3][0].' &euro;</li>';
	    $out .= '</ul></fieldset>';
	    $out .= '<fieldset><legend>Commandes liées</legend><ul>';
	    $out .= '<li>Nb moyen par affaire : '.$datas[1][1].' </li>';
	    $out .= '<li>Prix moyen : '.$datas[2][1].' &euro;</li>';
	    $out .= '<li>Prix médian : '.$datas[4][1].' &euro;</li>';
	    $out .= '<li>Prix total : '.$datas[3][1].' &euro;</li>';
	    $out .= '</ul></fieldset>';
	    $out .= '<fieldset><legend>Factures liées</legend><ul>';
	    $out .= '<li>Nb moyen par affaire : '.$datas[1][2].' </li>';
	    $out .= '<li>Prix moyen : '.$datas[2][2].' &euro;</li>';
	    $out .= '<li>Prix médian : '.$datas[4][2].' &euro;</li>';
	    $out .= '<li>Prix total : '.$datas[3][2].' &euro;</li>';
	    $out .= '</ul></fieldset></div>';
	}
	else {
	    $out = '';
	}
	return $out;

    }
}

?>