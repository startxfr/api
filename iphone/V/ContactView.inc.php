<?php

/**
 * La classe de gestion des fiches entreprises.
 */
class contactEntrepriseView {
    /**
     * FORMULAIRES COMPLETS
     */

    /**
     * Affichage d'une liste d'entreprise
     */
    static function searchResult($result, $from = 0, $limit = 5, $total = 0, $qTag ='', $fourn = 'non') {
	if(is_array($result) and count($result) > 0) {
	    $letter = $_SESSION['user']['LastLetterSearch'];
	    foreach($result as $k => $v) {
		if($letter != strtoupper($v['nom_ent']{0})) {
		    $letter = strtoupper($v['nom_ent']{0}) ;
		    $list .= '</ul><h2>'.$letter.'</h2><ul class="iArrow">';
		}
		elseif($from != 0) {
		    $list .= '</ul><ul class="iArrow">';
		}
		if($fourn == 'non') {
		    $list .= '<li><a href="Contact.php?action=viewEnt&id_ent='.$v['id_ent'].'" rev="async"><em>'.$v['nom_ent'].'</em><small>'.$v['cp_ent'].' - '.$v['ville_ent'].'</small></a></li>';
		}
		else {
		    $list .= '<li><a href="Produit.php?action=viewFourn&id_fourn='.$v['id_fourn'].'" rev="async"><em>'.$v['nom_ent'].'</em><small>'.$v['cp_ent'].' - '.$v['ville_ent'].'</small></a></li>';
		}
	    }
	    $list = substr($list,5).'</ul>';
	    if ($from == 0) {
		$out 	 = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>
						';
	    }
	    $out     .='<div class="iList">
						'.$list.'
					</div>';
	    if($total > ($limit+$from)) {
		if($fourn == 'non') {
		    $out .= '<div class="iMore" id="searchResultEntrepriseMore'.$from.'"><a href="Contact.php?action=searchEntContinue&total='.$total.'&from='.($limit+$from).'" rev="async">Plus de résultats</a></div>';
		}
		else {
		    $out .= '<div class="iMore" id="searchResultFournMore'.$from.'"><a href="Produit.php?action=searchFournContinue&total='.$total.'&from='.($limit+$from).'" rev="async">Plus de résultats</a></div>';
		}
	    }
	    $_SESSION['user']['LastLetterSearch'] = $letter;
	    return $out;
	}
    }

    /**
     * Formulaire complet d'ajout d'une entreprise.
     */

    static function formAdd($value = array(),$onError = array(),$errorMess = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddContactCompany\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddContactCompany" action="Contact.php?action=doAddEnt" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockAdd($value,$onError, 'addCompagnyCP').'
				</div>
				</form>';
	$out .= '<form id="addCompagnyCPForm" action="Contact.php?action=listeVille&id=addCompagnyCP" onsubmit="return WA.Submit(this,null,event)">';
	$out .= '<input type="hidden" id="addCompagnyCP_hidden" name="cp"></input>';
	$out .= '<a href="#_ContactEntAdd" onclick="return WA.Submit(\'addCompagnyCPForm\')" id="addCompagnyCP_valid" style="display:none">lien caché</a>';
	$out .= '</form>';
	return $out;
    }

    /**
     * Formulaire complet de modification d'une entreprise.
     */

    static function formModif($value = array(),$onError = array(),$errorMess = '',$ident = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formModifContactCompany\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifContactCompany" action="Contact.php?action=doModifEnt&id_ent='.$ident.'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.self::blockModif($value,$onError, 'modifCompagnyCP').'
				</div>
				</form>';
	$out .= '<form id="modifCompagnyCPForm" action="Contact.php?action=listeVille&id=modifCompagnyCP" onsubmit="return WA.Submit(this,null,event)">';
	$out .= '<input type="hidden" id="modifCompagnyCP_hidden" name="cp" />';
	$out .= '<a href="#_ContactEnt" onclick="return WA.Submit(\'modifCompagnyCPForm\')" id="modifCompagnyCP_valid" style="display:none">lien caché</a>';
	$out .= '</form>';
	return $out;
    }
    static function modif($value = array(),$onError = array(),$errorMess = '',$ident = '') {
	return self::formModif($value ,$onError, $errorMess,$ident);
    }

    /**
     * Block complet d'ajout
     */
    static function blockAdd($value = array(),$onError = array(), $id = '') {
	$out 	 = self::subBlockFormCompany($value,$onError);
	$out	.= self::subBlockFormMailTel($value,$onError);
	$out	.= self::subBlockFormAdresse($value,$onError, $id);
	$out	.= self::subBlockFormCompanyDetail($value,$onError);
	if(count($value) == 0) $value['addCont'] = 'ok';
	$out  .= '<fieldset>
					<ul>
						<li>'.HtmlFormIphone::Checkbox('addCont','Ajouter un contact','OUI|NON',$value['addCont']).'</li>
					</ul>
				</fieldset>';
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddContactCompany\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }


    static function blockModif($value = array(),$onError = array(), $id = '') {
	$out 	 = self::subBlockFormCompany($value,$onError);
	$out	.= self::subBlockFormMailTel($value,$onError);
	$out	.= self::subBlockFormAdresse($value,$onError, $id);
	$out	.= self::subBlockFormCompanyDetail($value,$onError);
	if(count($value) == 0) $value['addCont'] = 'ok';
	$out  .= '<fieldset>
					<ul>
						<li>'.HtmlFormIphone::Checkbox('addCont','Ajouter un contact','OUI|NON',$value['addCont']).'</li>
					</ul>
				</fieldset>';
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formModifContactCompany\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
//		$out .='<a href="Contact.php?action=suppEnt&id_ent='.$value["id_ent"].'" rev="async" class="redButton"><span>Supprimer cette entreprise</span></a>';
	return $out;
    }

    /**
     * Formulaire complet d visualisation d'une entreprise.
     */
    static function view($value = array(), $fourn = 'non', $nbprod = '0') {
	$info = self::contactLinkLong($value);
	$telBlock = $addBlock = $info1Block = $info2Block = $personBlock = $addAfterFieldset = '';
	$telBlock .= ($value["mail_ent"] != '') ? '<li>'.HtmlElementIphone::linkIconMailWithZSend($value["mail_ent"],false, true).HtmlElementIphone::linkIconMail($value["mail_ent"]).'</li>' : '';
	$telBlock .= ($value["tel_ent"] != '') ? '<li>'.HtmlElementIphone::linkIconTel($value["tel_ent"]).'</li>' : '';
	$telBlock .= ($value["fax_ent"] != '') ? '<li>'.HtmlElementIphone::linkIconFaxWithZSend($value["fax_ent"],false,$value["nom_ent"], true).HtmlElementIphone::linkIconFax($value["fax_ent"]).'</li>' : '';
	$telBlock .= ($value["www_ent"] != '') ? '<li>'.HtmlElementIphone::linkIconWeb($value["www_ent"]).'</li>' : '';
	if( $value["add2_ent"] == '')$value["add2_ent"]='<br />';
	if( $value["pays_ent"] == '1') $value["nom_pays"] = '';
	if( $value["add1_ent"] != ''
		and $value["cp_ent"] != ''
		and $value["ville_ent"] != '')
	    $addAdresse  .= '<ul><li>'.
		    HtmlElementIphone::linkIconAddress($value["add1_ent"],$value["add2_ent"],$value["cp_ent"],$value["ville_ent"],$value["nom_pays"],$value["nom_ent"]).
		    HtmlElementIphone::linkIconAddressWithZSend($value["add1_ent"],$value["add2_ent"],$value["cp_ent"],$value["ville_ent"],$value["nom_pays"],$value["nom_ent"], false, true).'</li></ul>';
	if($value["nom_tyent"] != '')   $info1Block .= '<li><span>Type</span>'.$value["nom_tyent"].'</li>';
	if($value["nom_act"] != '') 	  $info1Block .= '<li><span>Activité</span>'.$value["nom_act"].'</li>';
	if($value["effectif_ent"] != '')$info1Block .= '<li><span>Effectif</span>'.$value["effectif_ent"].' employés</li>';
	if( $info1Block != '')
	    $addAfterFieldset .= '<fieldset>
					<legend>Profil</legend>
					<ul>'.$info1Block.'</ul>
				</fieldset>';
	if($value["tauxTVA_ent"] != '')  $info2Block .= '<li><span>tx TVA</span>'.$value["tauxTVA_ent"].'%</li>';
	if($value["SIRET_ent"] != '')    $info2Block .= '<li><span>N° SIRET</span>'.$value["SIRET_ent"].'</li>';
	if($value["numeroTVA_ent"] != '')$info2Block .= '<li><span>N° TVA</span>'.$value["numeroTVA_ent"].'</li>';
	if($value["codefourn_ent"] != '')$info2Block .= '<li><span>N° fourn.</span>'.$value["codefourn_ent"].'</li>';
	if( $info2Block != '')
	    $addAfterFieldset .= '<fieldset>
					<legend>Informations administratives</legend>
					<ul>'.$info2Block.'</ul>
				</fieldset>';
	if($fourn == 'non') {
	    if(is_array($value["contact"]) and count($value["contact"])>0)
		foreach($value["contact"] as $k => $v)
		    $personBlock .= '<li>'.contactParticulierView::contactLinkSimple($v).'</li>';
	    if( $personBlock != '')
		$addAfterFieldset .= '<fieldset>
						<legend>Contacts</legend>
						<ul>'.$personBlock.'</ul>
					</fieldset>';

	    $out = '<a href="Contact.php?action=modifEnt&id_ent='.$value["id_ent"].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/edit.png" alt="Modifier" /></a>
					<div class="iPanel">
					<fieldset><ul><li>'.$info.'</li></ul></fieldset>
					<fieldset>
						<legend>Coordonnées</legend>
						<ul>
							'.$telBlock.'
						</ul>
						'.$addAdresse.'
					</fieldset>
					'.$addAfterFieldset.self::subBlockRessourcesLiees($value);
	    $out .= '<a class="whiteButton" href="Contact.php?action=addContactLie&id_ent='.$value['id_ent'].'" rev="async"><span>Ajouter un contact lié</span></a></div>';
	}
	else {
	    $out = '<a href="Produit.php?action=modifFourn&id_fourn='.$value["id_fourn"].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/edit.png" alt="Modifier" /></a>
					<div class="iPanel">
					<fieldset><ul><li>'.$info.'</li></ul></fieldset>
					<fieldset>
						<legend>Coordonnées</legend>
						<ul>
							'.$telBlock.'
						</ul>
						'.$addAdresse.'
					</fieldset>'.self::subBlockRessourcesLiees($value, 'oui');
	    if($value['actif'] == '1')
		$out .= '<a href="#_ContactFicheEnt" class="redButton" onClick="confirmBeforeClickFourn(\'confirmSuppFournFiche\', \''.$nbprod.'\')"><span>Désactiver ce fournisseur</span></a>';
	    else {
		$out .= '<a href="Produit.php?action=activer&id_fourn='.$value["id_fourn"].'" class="redButton" rev="async" ><span>Activer ce fournisseur</span></a>';
	    }
	    if($nbprod == 0)
		$out .= '<br /><br /><a href="Produit.php?action=vraiSupp&id_fourn='.$value["id_fourn"].'" class="redButton" rev="async" ><span>Supprimer ce fournisseur</span></a>';

	    $out .= '<form id="formSuppFournFiche" action="Produit.php?action=suppFourn&id_fourn='.$value['id_fourn'].'" onsubmit="return WA.Submit(this,null,event)">' .
		    '<div style="display:none;"><a id="confirmSuppFournFiche" onclick="return WA.Submit(\'formSuppFournFiche\',null,event)">Lien caché</a>' .
		    '</div></form>';
	}
	return $out;
    }

    static function delete($value = array(), $nombredecontact = 0, $fourn = 0) {
	if ($value["id_ent"] == 0) {
	    $out='<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a>
			  <div class="iPanel">
			  <div class="err">
			  		<strong> Entreprise supprimée ! </strong>
				</div>
						';
	    return $out;
	}
	if ($fourn != 0) {
	    $out='<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a>
			  <div class="iPanel">
			  <div class="err">
			  		<strong> Cette entreprise est fournisseur, impossible de la supprimer ! </strong>
				</div>
						';
	    return $out;
	}

	$info = self::contactLinkLong($value);
	$telBlock = $addBlock = $info1Block = $info2Block = $personBlock = $addAfterFieldset = '';
	$telBlock .= ($value["mail_ent"] != '') ? '<li>'.HtmlElementIphone::linkIconMail($value["mail_ent"]).'</li>' : '';
	$telBlock .= ($value["tel_ent"] != '') ? '<li>'.HtmlElementIphone::linkIconTel($value["tel_ent"]).'</li>' : '';
	$telBlock .= ($value["fax_ent"] != '') ? '<li>'.HtmlElementIphone::linkIconFax($value["fax_ent"]).HtmlElementIphone::linkIconFaxWithZSend($value["fax_ent"],true,$value["nom_ent"]).'</li>' : '';
	$telBlock .= ($value["www_ent"] != '') ? '<li>'.HtmlElementIphone::linkIconWeb($value["www_ent"]).'</li>' : '';
	if( $value["add2_ent"] != '')$add2_ent = $value["add2_ent"].'<br/>';
	if( $value["pays_ent"] == '1') $value["nom_pays"] = '';
	if( $value["add1_ent"] != ''
		and $value["cp_ent"] != ''
		and $value["ville_ent"] != '')
	    $addAdresse  .= '<ul><li>'.
		    HtmlElementIphone::linkIconAddress($value["add1_ent"],$value["add2_ent"],$value["cp_ent"],$value["ville_ent"],$value["nom_pays"],$value["nom_ent"]).
		    HtmlElementIphone::linkIconAddressWithZSend($value["add1_ent"],$value["add2_ent"],$value["cp_ent"],$value["ville_ent"],$value["nom_pays"],$value["nom_ent"]).'</li></ul>';
	if($value["nom_tyent"] != '')   $info1Block .= '<li><span>Type</span>'.$value["nom_tyent"].'</li>';
	if($value["nom_act"] != '') 	  $info1Block .= '<li><span>Activité</span>'.$value["nom_act"].'</li>';
	if($value["effectif_ent"] != '')$info1Block .= '<li><span>Effectif</span>'.$value["effectif_ent"].' employés</li>';
	if( $info1Block != '')
	    $addAfterFieldset .= '<fieldset>
					<legend>Profil</legend>
					<ul>'.$info1Block.'</ul>
				</fieldset>';
	if($value["tauxTVA_ent"] != '')  $info2Block .= '<li><span>tx TVA</span>'.$value["tauxTVA_ent"].'%</li>';
	if($value["SIRET_ent"] != '')    $info2Block .= '<li><span>N° SIRET</span>'.$value["SIRET_ent"].'</li>';
	if($value["numeroTVA_ent"] != '')$info2Block .= '<li><span>N° TVA</span>'.$value["numeroTVA_ent"].'</li>';
	if($value["codefourn_ent"] != '')$info2Block .= '<li><span>N° fourn.</span>'.$value["codefourn_ent"].'</li>';
	if( $info2Block != '')
	    $addAfterFieldset .= '<fieldset>
					<legend>Informations administratives</legend>
					<ul>'.$info2Block.'</ul>
				</fieldset>';
	if(is_array($value["contact"]) and count($value["contact"])>0)
	    foreach($value["contact"] as $k => $v)
		$personBlock .= '<li>'.contactParticulierView::contactLinkWithLinks($v).'</li>';
	if( $personBlock != '')
	    $addAfterFieldset .= '<fieldset>
					<legend>Contacts</legend>
					<ul>'.$personBlock.'</ul>
				</fieldset>';

	$out = '<a href="Contact.php?action=doSuppEnt&id_ent='.$value["id_ent"].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/remove.png" alt="Supprimer" /></a>
				<div class="iPanel">
				<div class="err">
					Cette entreprise est liée à '.$nombredecontact.' contact(s).<br />
			  		<strong> Êtes vous sur de vouloir supprimer cette entreprise et tous les éléments qui y sont liés ? </strong>
				</div>
				<fieldset><ul><li>'.$info.'</li></ul></fieldset>
				<fieldset>
					<legend>Coordonnées</legend>
					<ul>
						'.$telBlock.'
					</ul>
					'.$addAdresse.'
				</fieldset>
				'.$addAfterFieldset.'
			</div>';
	return $out;
    }

    /**
     * SOUS BLOCK DE FORMULAIRES
     */

    static function contactLinkSimple($value = array()) {
	return '<a href="Contact.php?action=viewEnt&id_ent='.$value['id_ent'].'" class="Entreprise" rev="async"><img src="../img/actualite/contact.png"/> '.strtoupper($value['nom_ent']).'</a>';
    }



    /**
     * Fieldset complet pour un lien vers la fiche de l'entreprise et les informaitons de localisation (tel, mail, fax, add)
     */
    static function contactLinkWithLinks($value = array(), $fourn = 'non') {
	$b = HtmlElementIphone::linkIconMail($value["mail_ent"],false).
		HtmlElementIphone::linkIconMailWithZSend($value["mail_ent"],false).
		HtmlElementIphone::linkIconTel($value["tel_ent"],false).
		HtmlElementIphone::linkIconFax($value["fax_ent"],false).
		HtmlElementIphone::linkIconFaxWithZSend($value["fax_ent"],false,$value["nom_ent"]).
		HtmlElementIphone::linkIconWeb($value["www_ent"],false).
		HtmlElementIphone::linkIconAddress($value["add1_ent"],$value["add2_ent"],$value["cp_ent"],$value["ville_ent"],$value["nom_pays"],$value["nom_ent"],false).
		HtmlElementIphone::linkIconAddressWithZSend($value["add1_ent"],$value["add2_ent"],$value["cp_ent"],$value["ville_ent"],$value["nom_pays"],$value["nom_ent"],false);
	$i = ($value["type_ent"] != '' && $value["type_ent"] != '6' ) ? '<span><img src="../img/prospec/TypeEntreprise/'.$value["type_ent"].'.png"/></span>' : '';
	$b = ($b != '') ? '<br class="clear"/><div class="listIconLink">'.$b.'<br class="clear"/></div>' : '';
	if($value["cp_ent"] != '' or $value["ville_ent"] != '') $add = $value["cp_ent"].' - '.$value["ville_ent"];
	elseif($value["tel_ent"] != '') $add = $value["tel_ent"];
	else $add = $value["mail_ent"];
	if($fourn == 'non') {
	    $out = '<a href="Contact.php?action=viewEnt&id_ent='.$value["id_ent"].'" rev="async">'.$i.$value["nom_ent"].'<br/>
						<small>'.$add.'</small></a>'.$b;
	}
	else {
	    $out = '<a href="Produit.php?action=viewFourn&id_fourn='.$value["fournisseur_id"].'" rev="async">'.$i.$value["nom_ent"].'<br/>
						<small>'.$add.'</small></a>'.$b;
	}
	return $out;
    }

    /**
     * Fieldset complet pour un lien vers la fiche de l'entreprise
     */
    static function contactLinkLong($value = array()) {
	if($value["cp_ent"] != '' or $value["ville_ent"] != '') $add = $value["cp_ent"].' - '.$value["ville_ent"];
	elseif($value["tel_ent"] != '') $add = $value["tel_ent"];
	else $add = $value["mail_ent"];
	$i = ($value["type_ent"] != '' && $value["type_ent"] != '6') ? '<span><img src="../img/prospec/TypeEntreprise/'.$value["type_ent"].'.png"/></span>' : '';

	$out = '<a href="Contact.php?action=viewEnt&id_ent='.$value["id_ent"].'" rev="async">'.$i.$value["nom_ent"].'<br/>
							  <small>'.$add.'</small></a>';
	return $out;
    }

    /**
     * Sous-Block pour les personnes.
     */
    static function subBlockFormCompany($value = array(), $onError = array()) {

//			$statutList 	= array( 'sa'=>'SA',
//						 'sarl'=>'SARL',
//						 'sas'=>'SAS',
//						 'ass'=>'Association',
//						 'etp'=>'Etablissement public');

	$nom 		= HtmlFormIphone::Input('nom_ent',$value['nom_ent'],'nom',' autocomplete="off"');
	$nomERR	= (in_array('nom_ent',$onError)) ? '<span class="iFormErr"/>' : '';
//			$statut	= HtmlFormIphone::Select('statut_ent',$statutList,$value['statut_ent'],false);

	$out = '<fieldset>
					<ul>
						<li>'.$nom.$nomERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }
    /**
     * Sous-Block pour les personnes.
     */
    static function subBlockFormCompanyDetail($value = array(), $onError = array()) {

	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_tyent, nom_tyent from ref_typeentreprise;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) {
	    $typeList[$v['id_tyent']] = $v['nom_tyent'];
	}


	$tvaList 	= array( '19.6'=>'19.6%',
		'5.5'=>'5.5%',
		'0'=>'0%');

	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_act, nom_act from ref_activite;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v) {
	    $activiteList[$v['id_act']] = $v['nom_act'];
	}
	$tvapardefaut = ($value['tauxTVA_ent'] == NULL) ? '19.6' : $value['tauxTVA_ent'];

	$type 	= HtmlFormIphone::Select('type_ent',$typeList,$value['type_ent'],false);
	$tva 		= HtmlFormIphone::Select('tauxTVA_ent',$tvaList,$tvapardefaut,false);
	$activite	= HtmlFormIphone::Select('activite_ent',$activiteList,$value['activite_ent']);
	$effectif	= HtmlFormIphone::InputLabel('effectif_ent',$value['effectif_ent'],'effectif');
	$SIRET	= HtmlFormIphone::InputLabel('SIRET_ent',$value['SIRET_ent'],'N° SIRET');
	$remise	= HtmlFormIphone::InputLabel('remise_ent',$value['remise_ent'],'Taux Rem.');
	$numeroTVA	= HtmlFormIphone::InputLabel('numeroTVA_ent',$value['numeroTVA_ent'],'N° TVA');
	$codefourn	= HtmlFormIphone::InputLabel('codefourn_ent',$value['codefourn_ent'],'N° fourn.');


	$out = '<fieldset>
					<legend>Profil</legend>
					<ul>
						<li>'.$type.'</li>
						<li>'.$activite.'</li>
						<li>'.$effectif.'</li>
					</ul>
				</fieldset>';
	$out.= '<fieldset>
					<legend>Informations administratives</legend>
					<ul>
						<li>'.$remise.'</li>
						<li>'.$tva.'</li>
						<li>'.$SIRET.'</li>
						<li>'.$numeroTVA.'</li>
						<li>'.$codefourn.'</li>
					</ul>
				</fieldset>';
	return $out;
    }


    /**
     * Sous-Block pour les adresses.
     */
    static function subBlockFormMailTel($value = array(),$onError = array()) {

	$mailERR= (in_array('mail_ent',$onError)) ? '<span class="iFormErr"/>' : '';
	$tel	= HtmlFormIphone::InputLabel('tel_ent',$value['tel_ent'],'Téléphone');
	$telERR= (in_array('tel_ent',$onError)) ? '<span class="iFormErr"/>' : '';
	$fax	= HtmlFormIphone::InputLabel('fax_ent',$value['fax_ent'],'Faximile');
	$web 	= HtmlFormIphone::InputLabel('www_ent',$value['www_ent'],'Site', 'autocapitalize="off" ');

	$out = '<fieldset>
					<legend>Coordonnées</legend>
					<ul>
						<li>'.$tel.$telERR.'</li>
						<li>'.$fax.'</li>
						<li>'.$web.'</li>
					</ul>
				</fieldset>';
	return $out;
    }


    /**
     * Sous-Block pour les adresses.
     */
    static function subBlockFormAdresse($value = array(),$onError = array(), $id = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_pays, nom_pays from ref_pays;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_pays']] = $v['nom_pays'];

	$add1 	= HtmlFormIphone::Input('add1_ent',$value['add1_ent'],'adresse');
	$add1ERR= (in_array('add1_ent',$onError)) ? '<span class="iFormErr"/>' : '';
	$add2	= HtmlFormIphone::Input('add2_ent',$value['add2_ent'],'complement');
	$cp 	= HtmlFormIphone::Input('cp_ent',$value['cp_ent'],'Code Postal', 'onchange="autoVille(this.value, \''.$id.'\')"');
	$cpERR	= (in_array('cp_ent',$onError)) ? '<span class="iFormErr"/>' : '';
	$ville	= HtmlFormIphone::Input('ville_ent',$value['ville_ent'],'Ville', 'id="'.$id.'"');
	$villeERR= (in_array('ville_ent',$onError)) ? '<span class="iFormErr"/>' : '';
	$pays 	= HtmlFormIphone::Select('pays_ent',$countryList,$value['pays_ent'],false);
	$paysERR= (in_array('pays_ent',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
					<legend>Adresse</legend>
					<ul>
						<li>'.$add1.$add1ERR.'</li>
						<li>'.$add2.'</li>
						<li>'.$cp.$cpERR.'</li>
						<li id="'.$id.'li">'.$ville.$villeERR.'</li>
						<li>'.$pays.$paysERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }
    static function subBlockRessourcesLiees($value = array(), $fourn = 'non') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(*) as C FROM affaire WHERE entreprise_aff = '".$value['id_ent']."'");
	$temp = $sqlConn->process2();
	$totalAff = $temp[1][0]['C'];
	$sqlConn->makeRequeteFree("select count(*) as C FROM devis WHERE entreprise_dev = '".$value['id_ent']."'");
	$temp = $sqlConn->process2();
	$totalDev = $temp[1][0]['C'];
	$sqlConn->makeRequeteFree("select count(*) as C FROM commande WHERE entreprise_cmd = '".$value['id_ent']."'");
	$temp = $sqlConn->process2();
	$totalCmd = $temp[1][0]['C'];
	$sqlConn->makeRequeteFree("select count(*) as C FROM facture WHERE entreprise_fact = '".$value['id_ent']."'");
	$temp = $sqlConn->process2();
	$totalFact = $temp[1][0]['C'];
	$affaires = ($totalAff == 0) ? '' : '<li><a rev="async" href="Contact.php?action=viewAffaireEnt&amp;id='.$value['id_ent'].'"><img src="../img/actualite/affaire.png"/> '.$totalAff.' Affaires</a></li>';
	$devis = ($totalDev == 0) ? '' : '<li><a rev="async" href="Contact.php?action=viewDevisEnt&amp;id='.$value['id_ent'].'"><img src="../img/actualite/devis.png"/> '.$totalDev.' Devis</a></li>';
	$commandes = ($totalCmd == 0) ? '' : '<li><a rev="async" href="Contact.php?action=viewCommandeEnt&amp;id='.$value['id_ent'].'"><img src="../img/actualite/commande.png"/> '.$totalCmd.' Commandes</a></li>';
	$factures = ($totalFact == 0) ? '' : '<li><a rev="async" href="Contact.php?action=viewFactureEnt&amp;id='.$value['id_ent'].'"><img src="../img/actualite/facture.png"/> '.$totalFact.' Factures</a></li>';
	$produits = ($fourn == 'oui') ? self::subBlockProduitsLies($value) : '';
	$out = ($totalAff == 0 and $totalDev == 0 and $totalFact == 0 and $totalCmd == 0 and $fourn == 'non') ? '<br class="clear" /><a href="Contact.php?action=suppEnt&id_ent='.$value["id_ent"].'" rev="async" class="redButton"><span>Supprimer cette entreprise</span></a><br /><br />' :  '<fieldset>
					<legend>Ressources Liées</legend>
					<ul class="iArrow">
						'.$affaires.$devis.$commandes.$factures.$produits.'
					</ul>
				</fieldset>';
	return $out;
    }
    static function subBlockProduitsLies($value = array()) {
	$out = '';
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_fourn as F FROM fournisseur WHERE entreprise_fourn = '".$value['id_ent']."'");
	$temp = $sqlConn->process2();
	$fourn = $temp[1][0]['F'];
	$sqlConn->makeRequeteFree("select count(*) as C FROM produit_fournisseur WHERE fournisseur_id = '".$fourn."'");
	$temp = $sqlConn->process2();
	$total = $temp[1][0]['C'];
	if($total > 0) {
	    $out = '<li><a rev="async" href="Contact.php?action=viewProduitEnt&amp;id='.$fourn.'"><img src="../img/voir.png" /> '.$total.' Produits</a></li>';
	}
	return $out;
    }
    static function contactEntrepriseResultRow($result) {
	if(is_array($result) and count($result) > 0) {
	    if(array_key_exists('id_aff', $result[0])) {
		foreach($result as $k => $v) {
		    $out .= '<li>'.affaireView::affaireLinkSimple($v).'</li>';
		}
	    }
	    elseif(array_key_exists('id_dev', $result[0])) {
		foreach($result as $k => $v) {
		    $out .= '<li>'.devisView::devisLinkSimple($v).'</li>';
		}
	    }
	    elseif(array_key_exists('id_cmd', $result[0])) {
		foreach($result as $k => $v) {
		    $out .= '<li>'.commandeView::commandeLinkSimple($v).'</li>';
		}
	    }
	    elseif(array_key_exists('id_fact', $result[0])) {
		foreach($result as $k => $v) {
		    $out .= '<li>'.factureView::factureLinkSimple($v).'</li>';
		}
	    }
	    elseif(array_key_exists('id_prod', $result[0])) {
		foreach($result as $k => $v) {
		    if($v['id_prod'] != null)
			$out .= '<li><a href="Produit.php?action=viewProd&id_prod='.$v['id_prod'].'" class="Produit" rev="async"><img src="../img/voir.png"/> '.$v['nom_prod'].' ('.$v['id_prod'].')<small style="display:block;">'.$v['prixF'].' € (Vente : '.$v['prix_prod'].' €)</small></a></li>';
		}
	    }
	}
	return '<div class="iPanel"><fieldset><ul>'.$out.'</ul></fieldset></div>';
    }
}

/**
 *
 */
class contactParticulierView {


    /**
     * LISTE DE RECHERCHE
     */
    static function searchResult($result, $from = 0, $limit = 5, $total = 0, $qTag = '') {
	if(is_array($result) and count($result) > 0) {
	    $letter = $_SESSION['user']['LastLetterSearch'];
	    foreach($result as $k => $v) {
		if($letter != $v['nom_cont']{0}) {
		    $letter = strtoupper($v['nom_cont']{0}) ;
		    $list .= '</ul><h2>'.$letter.'</h2><ul class="iArrow">';
		}
		elseif($from != 0) {
		    $list .= '</ul><ul class="iArrow">';
		}
		$list .= '<li><a href="Contact.php?action=viewPart&id_cont='.$v['id_cont'].'" rev="async"><em>'.$v['civ_cont'].' '.$v['nom_cont'].' '.$v['prenom_cont'].'</em><small>'.$v['nom_fct'].'</small></a></li>';
	    }
	    $list = substr($list,5).'</ul>';
	    if ($from == 0) {
		$out 	 = '<a href="#_MainMenu"  rel="action" class="iButton iBAction"><img src="Img/home.png" alt="Accueil" /></a>			' ;
	    }
	    $out	.='<div class="iList">
						'.$list.'
					</div>';
	    if($total > ($limit+$from))
		$out .= '<div class="iMore" id="searchResultPartMore'.$from.'"><a href="Contact.php?action=searchPartContinue&from='.($limit+$from).'&total='.$total.'" rev="async">Plus de résultats</a></div>';
	    $_SESSION['user']['LastLetterSearch'] = $letter;
	    return $out;
	}
    }


    /**
     * FORMULAIRE D'AJOUT
     */
    static function formAdd($value = array(),$onError = array(),$errorMess = '',$entrepriseData = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$addEnt  = (is_array($entrepriseData)) ? '<ul><li>'.contactEntrepriseView::contactLinkWithLinks($entrepriseData).'<li></ul>' : '';
	$title   = ($title != '') ? '<h1>'.$title.'</h1>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddContactPers\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddContactPers" action="Contact.php?action=doAddPart" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.$addEnt.self::blockAdd($value,$onError, 'addContactCP').'
				</div>
				</form>';
	$out .= '<form id="addContactCPForm" action="Contact.php?action=listeVille&id=addContactCP" onsubmit="return WA.Submit(this,null,event)">';
	$out .= '<input type="hidden" id="addContactCP_hidden" name="cp"></input>';
	$out .= '<a href="#_ContactPartAdd" onclick="return WA.Submit(\'addContactCPForm\')" id="addContactCP_valid" style="display:none">lien caché</a>';
	$out .= '</form>';
	return $out;
    }

    static function formAddBis($value = array(),$onError = array(),$errorMess = '',$entrepriseData = '') {
	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$addEnt  = (is_array($entrepriseData)) ? '<ul><li>'.contactEntrepriseView::contactLinkLong($entrepriseData).'</li></ul>' : '';
	$title   = ($title != '') ? '<h1>'.$title.'</h1>' : '';
	if($value == array()) {
	    $value['add1_cont'] = $entrepriseData['add1_ent'];
	    $value['add2_cont'] = $entrepriseData['add2_ent'];
	    $value['cp_cont'] = $entrepriseData['cp_ent'];
	    $value['ville_cont'] = $entrepriseData['ville_ent'];
	    $value['tel_cont'] = $entrepriseData['tel_ent'];
	    $value['fax_cont'] = $entrepriseData['fax_ent'];
	    $value['www_cont'] = $entrepriseData['www_ent'];
	}
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formAddContactPers\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formAddContactPers" action="Contact.php?action=doAddPartBis&id_ent='.$entrepriseData["id_ent"].'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">' .
		'<fieldset>'
		.$addEnt.
		'</fieldset>
					'.self::blockAdd($value,$onError, 'addContactCP').'
				</div>
				</form>';
	$out .= '<form id="addContactCPForm" action="Contact.php?action=listeVille&id=addContactCP" onsubmit="return WA.Submit(this,null,event)">';
	$out .= '<input type="hidden" id="addContactCP_hidden" name="cp"></input>';
	$out .= '<a href="#_ContactPartAdd" onclick="return WA.Submit(\'addContactCPForm\')" id="addContactCP_valid" style="display:none">lien caché</a>';
	$out .= '</form>';
	return $out;
    }


    /**
     * Formulaire de modification de particulier
     */

    static function formModif($value = array(),$onError = array(),$errorMess = '',$entrepriseData = '') {

	$error   = ($errorMess != '') ? '<div class="err">'.$errorMess.'</div>' : '';
	$addEnt  = (is_array($entrepriseData)) ? contactEntrepriseView::view($entrepriseData) : '';
	$title   = ($title != '') ? '<h1>'.$title.'</h1>' : '';
	$out 	 = '<a href="#"  onclick="return WA.Submit(\'formModifContactPers\',null,event)" rel="action" class="iButton iBAction"><img src="Img/save.png" alt="Enregistrer" /></a>
				<form id="formModifContactPers" action="Contact.php?action=doModifPart&id_cont='.$value['id_cont'].'" onsubmit="return WA.Submit(this,null,event)">
				'.$error.'
				<div class="iPanel">
					'.$addEnt.self::blockModif($value,$onError, 'modifContactCP').'
				</div>
				</form>';
	$out .= '<form id="modifContactCPForm" action="Contact.php?action=listeVille&id=modifContactCP" onsubmit="return WA.Submit(this,null,event)">';
	$out .= '<input type="hidden" id="modifContactCP_hidden" name="cp" />';
	$out .= '<a href="#_ContactPart" onclick="return WA.Submit(\'modifContactCPForm\')" id="modifContactCP_valid" style="display:none">lien caché</a>';
	$out .= '</form>';
	return $out;
    }
    static function modif($value = array(),$onError = array(),$errorMess = '',$entrepriseData = '') {
	return self::formModif($value,$onError,$errorMess,$entrepriseData);
    }


    /**
     * BLOCK AJOUT PRET POUR FORM
     */
    static function blockAdd($value = array(),$onError = array(), $id = '') {

	$out 	 = self::subBlockFormPerson($value,$onError);
	$out	.= self::subBlockFormMailTel($value,$onError);
	$out	.= self::subBlockFormAdresse($value,$onError, $id);
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formAddContactPers\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
	return $out;
    }
    /**
     * Block de modification pour formulaire
     */
    static function blockModif($value = array(),$onError = array(), $id = '') {

	$out 	 = self::subBlockFormPerson($value,$onError);
	$out	.= self::subBlockFormMailTel($value,$onError);
	$out	.= self::subBlockFormAdresse($value,$onError, $id);
	$out .= '<fieldset>
					<a href="#" class="BigButtonValidLeft" onclick="return WA.Back()"><img src="Img/big.annuler.png" alt="Annuler" /></a>
					<a href="#" class="BigButtonValidRight" onclick="return WA.Submit(\'formModifContactPers\', null, event)"><img src="Img/big.valider.png" alt="Valider" /></a>
				</fieldset>';
//		$out = '<a href="Contact.php?action=suppPart&id_cont='.$value["id_cont"].'" rev="async" class="redButton"><span> Supprimer ce contact</span></a>';
	return $out;
    }


    /**
     * FICHE DE VISUALISATION
     */

    /**
     * Formulaire complet de visualisation d'un particulier.
     */
    static function view($value = array()) {

	if($value[entreprise_cont] != NULL) {
	    $entreprise = '<fieldset>
					<legend>Entreprise liée</legend>
					<ul class="iArrow">
						<li>'.contactEntrepriseView::contactLinkWithLinks($value).'</li>
					</ul>
				</fieldset>';
	}
	else {
	    $entreprise = '';
	}

	$telBlock = $addBlock = '';
	$telBlock .= ($value["mail_cont"] != '') ? '<li>'.HtmlElementIphone::linkIconMailWithZSend($value["mail_cont"],false, true).HtmlElementIphone::linkIconMail($value["mail_cont"]).'</li>' : '';
	$telBlock .= ($value["tel_cont"] != '') ? '<li>'.HtmlElementIphone::linkIconTel($value["tel_cont"]).'</li>' : '';
	$telBlock .= ($value["mob_cont"] != '') ? '<li>'.HtmlElementIphone::linkIconTel($value["mob_cont"]).'</li>' : '';
	$telBlock .= ($value["fax_cont"] != '') ? '<li>'.HtmlElementIphone::linkIconFaxWithZSend($value["fax_cont"],false,$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"], true).HtmlElementIphone::linkIconFax($value["fax_cont"]).'</li>' : '';
	if( $value["add2_cont"] == '')
	    $value["add2_cont"] = '<br/>';
	if( $value["pays_cont"] == '1')
	    $value["nom_pays"] = '';
	if( $value["add1_cont"] != ''
		and $value["cp_cont"] != ''
		and $value["ville_cont"] != '')
	    $addBlock  .= '<ul><li>'.
		    HtmlElementIphone::linkIconAddress($value["add1_cont"],$value["add2_cont"],$value["cp_cont"],$value["ville_cont"],$value["nom_pays"],$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"]).
		    HtmlElementIphone::linkIconAddressWithZSend($value["add1_cont"],$value["add2_cont"],$value["cp_cont"],$value["ville_cont"],$value["code_pays"],$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"], false, true).'</li></ul>';
	$out = '<a href="Contact.php?action=modifPart&id_cont='.$value["id_cont"].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/edit.png" alt="Modifier" /></a>
			  <div class="iPanel">
				<fieldset>
					<ul>
						<li><strong>'.$value["civ_cont"].' '.$value["nom_cont"].'</strong><br/>
						    '.$value["prenom_cont"].'</li>
					</ul>
					<ul>
						'.$telBlock.'
					</ul>
					'.$addBlock.'
				</fieldset>'.$entreprise.self::subBlockRessourcesLiees($value).'
			</div>';
	return $out;
    }


    /**
     * Fonction de suppression d'un contact'
     */

    static function delete($value = array(), $fourn = 0) {
	if ($value["id_cont"] == 0) {
	    $out='<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a>
			  <div class="iPanel">
			  <div class="err">
			  		<strong> Contact supprimé ! </strong>
				</div>
						';
	    return $out;
	}
	if ($fourn != 0) {
	    $out='<a href="#_MainMenu"  rel="action" class="iButton iBBack"><img src="Img/home.png" alt="Accueil" /></a>
			  <div class="iPanel">
			  <div class="err">
			  		<strong> Contact lié à un fournisseur, impossible de le supprimer ! </strong>
				</div>
						';
	    return $out;
	}

	$telBlock = $addBlock = '';
	$telBlock .= ($value["mail_cont"] != '') ? '<li>'.HtmlElementIphone::linkIconMail($value["mail_cont"]).HtmlElementIphone::linkIconMailWithZSend($value["mail_cont"],false).'</li>' : '';
	$telBlock .= ($value["tel_cont"] != '') ? '<li>'.HtmlElementIphone::linkIconTel($value["tel_cont"]).'</li>' : '';
	$telBlock .= ($value["mob_cont"] != '') ? '<li>'.HtmlElementIphone::linkIconTel($value["mob_cont"]).'</li>' : '';
	$telBlock .= ($value["fax_cont"] != '') ? '<li>'.HtmlElementIphone::linkIconFax($value["fax_cont"]).HtmlElementIphone::linkIconFaxWithZSend($value["fax_cont"],false,$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"]).'</li>' : '';
	if( $value["add2_cont"] != '')
	    $value["add2_cont"].= '<br/>';
	if( $value["pays_cont"] == 'fr')
	    $value["pays_cont"] = '';
	if( $value["add1_cont"] != ''
		and $value["cp_cont"] != ''
		and $value["ville_cont"] != '')
	    $addBlock  .= '<ul><li>'.
		    HtmlElementIphone::linkIconAddress($value["add1_cont"],$value["add2_cont"],$value["cp_cont"],$value["ville_cont"],$value["nom_pays"],$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"]).
		    HtmlElementIphone::linkIconAddressWithZSend($value["add1_cont"],$value["add2_cont"],$value["cp_cont"],$value["ville_cont"],$value["nom_pays"],$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"]).'</li></ul>';

	$out = '<a href="Contact.php?action=doSuppPart&id_cont='.$value["id_cont"].'"  rev="async" rel="action" class="iButton iBAction"><img src="Img/remove.png" alt="Supprimer" /></a>
			  <div class="iPanel">
			  <div class="err">
			  		<strong> Êtes vous sur de vouloir supprimer ce contact ? </strong>
				</div>
						<fieldset>
					<ul>
						<li><strong>'.$value["civ_cont"].' '.$value["nom_cont"].'</strong><br/>
						    '.$value["prenom_cont"].'</li>
					</ul>
					<ul>
						'.$telBlock.'
					</ul>
					'.$addBlock.'
				</fieldset>
			</div>';
	return $out;
    }



    /**
     * SOUS BLOCK DE FORMULAIRES
     */


    /**
     * Fieldset complet pour un lien vers la fiche de l'entreprise et les informaitons de localisation (tel, mail, fax, add)
     */
    static function contactLinkSimple($value = array(), $contact = 'cont') {
	if($contact == 'cont') {
	    return '<a href="Contact.php?action=viewPart&id_cont='.$value['id_cont'].'" class="Contact" rev="async"><img src="../img/actualite/contact.png"/> '.$value['civ_cont'].' '.ucfirst($value['prenom_cont']).' '.strtoupper($value['nom_cont']).'</a>';
	}
	elseif($contact == 'achat') {
	    return '<a href="Contact.php?action=viewPart&id_cont='.$value['id_achat'].'" class="Contact" rev="async"><img src="../img/actualite/contact.png"/> '.$value['civ_achat'].' '.ucfirst($value['prenom_achat']).' '.strtoupper($value['nom_achat']).'</a>';
	}
	else {
	    return '<a href="Contact.php?action=viewPart&id_cont='.$value['id_cont'].'" class="Contact" rev="async"><img src="../img/actualite/contact.png"/> '.$value['civ_cont'].' '.ucfirst($value['prenom_cont']).' '.strtoupper($value['nom_cont']).'</a>';
	}
    }

    /**
     * Fieldset complet pour un lien vers la fiche de l'entreprise et les informaitons de localisation (tel, mail, fax, add)
     */
    static function contactLinkWithLinks($value = array()) {
	$b = HtmlElementIphone::linkIconMail($value["mail_cont"],false).
		HtmlElementIphone::linkIconMailWithZSend($value["mail_cont"],false).
		HtmlElementIphone::linkIconTel($value["tel_cont"],false).
		HtmlElementIphone::linkIconFax($value["fax_cont"],false).
		HtmlElementIphone::linkIconFaxWithZSend($value["fax_cont"],false,$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"]).
		HtmlElementIphone::linkIconTel($value["mob_cont"],false).
		HtmlElementIphone::linkIconWeb($value["www_cont"],false).
		HtmlElementIphone::linkIconAddress($value["add1_cont"],$value["add2_cont"],$value["cp_cont"],$value["ville_cont"],$value["nom_pays"],$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"],false).
		HtmlElementIphone::linkIconAddressWithZSend($value["add1_cont"],$value["add2_cont"],$value["cp_cont"],$value["ville_cont"],$value["nom_pays"],$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"],false);
	$b = ($b != '') ? '<br class="clear"/><div class="listIconLink">'.$b.'<br class="clear"/></div>' : '';

	return '<a href="Contact.php?action=viewPart&id_cont='.$value["id_cont"].'" rev="async">'.$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"].'
				</a>'.$b;
    }

    /**
     * Fieldset complet pour un lien vers la fiche de l'entreprise
     */
    static function contactLinkLong($value = array()) {
	if($value["cp_cont"] != '' or $value["ville_cont"] != '') $add = $value["cp_cont"].' - '.$value["ville_cont"];
	elseif($value["tel_cont"] != '') $add = $value["tel_cont"];
	elseif($value["mob_cont"] != '') $add = $value["mob_cont"];
	else $add = $value["mail_cont"];

	return '<a href="Contact.php?action=viewPart&id_cont='.$value["id_cont"].'" rev="async">'.$value["civ_cont"].' '.$value["prenom_cont"].' '.$value["nom_cont"].'<br/>
					  <small>'.$add.'</small></a>';
    }

    /**
     * Sous-Block pour les personnes.
     */
    static function subBlockFormPerson($value = array(),$onError = array()) {

	$civList 	= $GLOBALS['CIV_'.$_SESSION["language"]];

	$nom 	= HtmlFormIphone::Input('nom_cont',$value['nom_cont'],'Nom');
	$nomERR	= (in_array('nom_cont',$onError)) ? '<span class="iFormErr"/>' : '';
	$prenom	= HtmlFormIphone::Input('prenom_cont',$value['prenom_cont'],'Prénom');
	$civ 	= HtmlFormIphone::Select('civ_cont',$civList,$value['civ_cont'],false);
	$civERR	= (in_array('civ_cont',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
					<legend>Contact</legend>
					<ul>
						<li>'.$nom.$nomERR.'</li>
						<li>'.$prenom.'</li>
						<li>'.$civ.$civERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }


    /**
     * Sous-Block pour les adresses.
     */
    static function subBlockFormMailTel($value = array(),$onError = array()) {

	$mail 	= HtmlFormIphone::Input('mail_cont',$value['mail_cont'],'e-mail', 'autocapitalize="off" ');
	$mailERR= (in_array('mail_cont',$onError)) ? '<span class="iFormErr"/>' : '';
	$tel	= HtmlFormIphone::Input('tel_cont',$value['tel_cont'],'téléphone');
	$telERR= (in_array('tel_cont',$onError)) ? '<span class="iFormErr"/>' : '';
	$mob	= HtmlFormIphone::Input('mob_cont',$value['mob_cont'],'portable');
	$fax	= HtmlFormIphone::Input('fax_cont',$value['fax_cont'],'fax');

	$out = '<fieldset>
					<legend>Coordonnées</legend>
					<ul>
						<li>'.$mail.$mailERR.'</li>
						<li>'.$tel.$telERR.'</li>
						<li>'.$mob.'</li>
						<li>'.$fax.'</li>
					</ul>
				</fieldset>';
	return $out;
    }


    /**
     * Sous-Block pour les adresses.
     */
    static function subBlockFormAdresse($value = array(),$onError = array(), $id = '') {

	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select id_pays, nom_pays from ref_pays;");
	$temp = $sqlConn->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_pays']] = $v['nom_pays'];

	$add1 	= HtmlFormIphone::Input('add1_cont',$value['add1_cont'],'Adresse');
	$add1ERR= (in_array('add1_cont',$onError)) ? '<span class="iFormErr"/>' : '';
	$add2	= HtmlFormIphone::Input('add2_cont',$value['add2_cont'],'Complement');
	$cp 	= HtmlFormIphone::Input('cp_cont',$value['cp_cont'],'CP', 'onchange="autoVille(this.value, \''.$id.'\')"');
	$cpERR	= (in_array('cp_cont',$onError)) ? '<span class="iFormErr"/>' : '';
	$ville	= HtmlFormIphone::Input('ville_cont',$value['ville_cont'],'Ville', 'id="'.$id.'"');
	$villeERR= (in_array('ville_cont',$onError)) ? '<span class="iFormErr"/>' : '';
	$pays 	= HtmlFormIphone::Select('pays_cont',$countryList,$value['pays_cont'],false);
	$paysERR= (in_array('pays_cont',$onError)) ? '<span class="iFormErr"/>' : '';

	$out = '<fieldset>
					<legend>Adresse</legend>
					<ul>
						<li>'.$add1.$add1ERR.'</li>
						<li>'.$add2.'</li>
						<li>'.$cp.$cpERR.'</li>
						<li id="'.$id.'li">'.$ville.$villeERR.'</li>
						<li>'.$pays.$paysERR.'</li>
					</ul>
				</fieldset>';
	return $out;
    }

    static function subBlockRessourcesLiees($value = array()) {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(*) as C FROM affaire WHERE contact_aff = '".$value['id_cont']."'");
	$temp = $sqlConn->process2();
	$totalAff = $temp[1][0]['C'];
	$sqlConn->makeRequeteFree("select count(*) as C FROM devis WHERE contact_dev = '".$value['id_cont']."'");
	$temp = $sqlConn->process2();
	$totalDev = $temp[1][0]['C'];
	$sqlConn->makeRequeteFree("select count(*) as C FROM commande WHERE contact_cmd = '".$value['id_cont']."'");
	$temp = $sqlConn->process2();
	$totalCmd = $temp[1][0]['C'];
	$sqlConn->makeRequeteFree("select count(*) as C FROM facture WHERE contact_fact = '".$value['id_cont']."'");
	$temp = $sqlConn->process2();
	$totalFact = $temp[1][0]['C'];
	$affaires = ($totalAff == 0) ? '' : '<li><a rev="async" href="Contact.php?action=viewAffaireCont&amp;id='.$value['id_cont'].'"><img src="../img/actualite/affaire.png"/> '.$totalAff.' Affaires</a></li>';
	$devis = ($totalDev == 0) ? '' : '<li><a rev="async" href="Contact.php?action=viewDevisCont&amp;id='.$value['id_cont'].'"><img src="../img/actualite/devis.png"/> '.$totalDev.' Devis</a></li>';
	$commandes = ($totalCmd == 0) ? '' : '<li><a rev="async" href="Contact.php?action=viewCommandeCont&amp;id='.$value['id_cont'].'"><img src="../img/actualite/commande.png"/> '.$totalCmd.' Commandes</a></li>';
	$factures = ($totalFact == 0) ? '' : '<li><a rev="async" href="Contact.php?action=viewFactureCont&amp;id='.$value['id_cont'].'"><img src="../img/actualite/facture.png"/> '.$totalFact.' Factures</a></li>';
	$out = ($totalAff == 0 and $totalDev == 0 and $totalFact == 0 and $totalCmd == 0) ? '<br class="clear" /><a href="Contact.php?action=suppPart&id_cont='.$value["id_cont"].'" rev="async" class="redButton"><span> Supprimer ce contact</span></a>' :  '<fieldset>
					<legend>Ressources Liées</legend>
					<ul class="iArrow">
						'.$affaires.$devis.$commandes.$factures.'
					</ul>
				</fieldset>';
	return $out;
    }




    /**
     * LISTE DE RECHERCHE
     */
    static function inputAjaxContact($nom = '', $selected = '', $titre = '', $withBlank = true) {
	$nom = ($nom != '') ? $nom : 'contact';
	$titre = ($titre != '') ? '<label style="float:left;">'.$titre.'</label>' : '';
	if($selected != '') {
	    $info = new contactParticulierModel();
	    $result = $info->getDataFromID($selected);
	    if($result[0]) {
		$nomSelected = $result[1][0]['civ_cont'].' '.$result[1][0]['prenom_cont'].' '.$result[1][0]['nom_cont'];
		$idSelected = $result[1][0]['id_cont'];
	    }
	    elseif(!$withBlank) $nomSelected = '<i>Veuillez choisir un contact</i>';
	    else			  $nomSelected = '&nbsp;';
	}
	elseif(!$withBlank) $nomSelected = '<i>Veuillez choisir un contact</i>';
	else			  $nomSelected = '&nbsp;';
	$out = $titre.' <a href="Contact.php?action=inputContact&tag='.$nom.'"  id="'.$nom.'AId" style="float:left;width:70%" rev="async"/>'.$nomSelected.'</a>
			<input type="hidden" name="'.$nom.'" id="'.$nom.'InputId" value="'.$idSelected.'"/><br class="clear"/>';
	return $out;
    }

    /**
     * Affichage d'une liste de contact
     */
    static function searchInputResultRow($result,$layerBackTo,$tagsBackTo) {
	$out = '';
	if(is_array($result) and count($result) > 0)
	    foreach($result as $k => $v) {
		$n = $v['civ_cont'].' '.ucfirst($v['prenom_cont']).' '.strtoupper($v['nom_cont']);
		$out .= '<li><a href="#_'.substr($layerBackTo,2).'" onclick="returnAjaxInputResult(\''.$tagsBackTo.'\',\''.$v['id_cont'].'\',\''.$n.'\')">' .
			'<em>'.$n.'</em>' .
			'<small>'.$v['mail_cont'].'</small>' .
			'</a></li>';
	    }
	return $out;
    }



    static function contactParticulierResultRow($result) {
	if(is_array($result) and count($result) > 0) {
	    if(array_key_exists('id_aff', $result[0])) {
		foreach($result as $k => $v) {
		    $out .= '<li>'.affaireView::affaireLinkSimple($v).'</li>';
		}
	    }
	    elseif(array_key_exists('id_dev', $result[0])) {
		foreach($result as $k => $v) {
		    $out .= '<li>'.devisView::devisLinkSimple($v).'</li>';
		}
	    }
	    elseif(array_key_exists('id_cmd', $result[0])) {
		foreach($result as $k => $v) {
		    $out .= '<li>'.commandeView::commandeLinkSimple($v).'</li>';
		}
	    }
	    elseif(array_key_exists('id_fact', $result[0])) {
		foreach($result as $k => $v) {
		    $out .= '<li>'.factureView::factureLinkSimple($v).'</li>';
		}
	    }
	}
	return '<div class="iPanel"><fieldset><ul>'.$out.'</ul></fieldset></div>';
    }


}

?>
