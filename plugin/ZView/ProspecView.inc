<?php
loadPlugin('ZView/GeneralView');
class projetView extends generalView {


    public function listeResult($datas) {
	return '<div id=listeResultProjet>'.$this->Result($datas).'</div>';
    }

    private function Result($datas) {
	$modHistorique = false;
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit']) {
	    $suffixeLink = './';
	    $modHistorique = true;
	}
	else $suffixeLink = '../';

	$out = '<div class="blockTable"><table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">
			<th class="barre">ID</th>
			<th class="center barre">Entreprise</th>
			<th class="center barre">Contact</th>
			<th>Titre</th>
			<th>Montant HT</th>
			<th class="barre">Status</th>';
	if(!$modHistorique)
	    $out .= '<th class="last center">Actions</th>';
	$out .= '</tr>';
	$alternance =  0;
	foreach($datas['data'] as $proj) {
	    $action = '';

	    if ($proj['affaire_proj'] != '')
		$action   = "<a href=\"${suffixeLink}draco/Affaire.php?id_aff=".$proj['affaire_proj']."\">".imageTag(getStaticUrl('img').'prospec/projet.affaire.png','Affaire '.$proj['affaire_proj'].' Liée')."</a> ";
	    if ($_SESSION['user']['id'] == $proj['utilisateur_proj'])
		$action   .= "<a href=\"${suffixeLink}prospec/Projet.php?id_proj=".$proj['id_proj']."\">".imageTag(getStaticUrl('img').'prospec/projet.modif.png','Modifier')."</a>
			      <a href=\"${suffixeLink}prospec/Projet.php?action=supp&id_proj=".$proj['id_proj']."\">".imageTag(getStaticUrl('img').'prospec/projet.delete.png','Supprimer')."</a>";

	    if ($proj['nom_ent'] != '') {
		if (strlen($proj['nom_ent']) > 25)
		    $proj['nom_ent'] = substr($proj['nom_ent'],0,25)."...";
		if ($proj['type_ent'] != '')
		    $proj['nom_ent']	= imageTag(getStaticUrl('img').$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$proj['type_ent'].'.png',$proj['nom_tyent']).' '.$proj['nom_ent'];
		if (strlen($proj['ville_ent']) > 18)
		    $proj['ville_ent'] = substr($proj['ville_ent'],0,18)."...";
		if ($proj['ville_ent'] != '')
		    $proj['ville_ent'] = ' ('.$proj['ville_ent'].')';
		$proj['contact'] = $proj['nom_ent'].$proj['ville_ent'];
		$actionJSLigne = "onclick=\"window.location='$suffixeLink/prospec/fiche.php?id_ent=".$proj['id_ent']."';\"";
	    }
	    else {
		$proj['nom_ent'] = '<i>Particulier</i>';
		$proj['nom_ent']	= imageTag(getStaticUrl('img').$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').' '.$proj['nom_ent'];
		if (strlen($proj['ville_cont']) > 18)
		    $proj['ville_cont'] = substr($proj['ville_cont'],0,18)."...";
		if ($proj['ville_cont'] != '')
		    $proj['ville_cont'] = ' ('.$proj['ville_cont'].')';
		$proj['contact'] = $proj['nom_ent'].$proj['ville_cont'];
		$actionJSLigne = "onclick=\"window.location='$suffixeLink/prospec/Contact.php?id_cont=".$proj['id_cont']."';\"";
	    }

	    if ($proj['rdv_proj'] != '')
		$proj['titre_proj'] = '<font color="'.$GLOBALS['PropsecConf']['color.leadRdv'].'"><b>** RDV le '.DateUniv2Human($proj['rdv_proj'],'simpleLong').' ** </b></font> '.$proj['titre_proj'];
	    $proj['detect_proj'] = DateUniv2Human($proj['detect_proj'],'simpleLong');

	    $out .= '<tr class="altern'.$alternance.'">';
	    $out .= '<td class="barre" style="cursor : pointer;" '.$actionJSLigne.'\'"><b>'.$proj['id_proj'].'</b></td>';
	    $out .= '<td class="barre" style="cursor : pointer;" '.$actionJSLigne.'\'">'.$proj['contact'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" '.$actionJSLigne.'\'">'.$proj['civ_cont'].' '.$proj['prenom_cont'].' '.$proj['nom_cont'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" '.$actionJSLigne.'\'">'.$proj['titre_proj'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" '.$actionJSLigne.'\'">'.$proj['budget_proj'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" '.$actionJSLigne.'\'">'.$proj['nom_typro'].'</td>';
	    if(!$modHistorique)
		$out .= '<td class="right">'.$action.'</td>';
	    $out .= '</tr>';
	    $alternance = ($alternance++ % 2);
	}

	$out .= '</tbody></table></div>';
	if($modHistorique)
	    $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'projetHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'projet');
	$bouton .= $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeProjetResultPage",$suffixeLink);

	loadPlugin(array('OOConverter'));
	$availableConvFormat = OOConverterAvailable('spreadsheet');
	$bouton .= '<div style="display:none;" id="divExportTableur">'.selectTag('format_export', $availableConvFormat, '', '', ' onchange="ChangeProjetAction(\'exportTableur\');ChangeFrom(this.value);ChangeLimit(\'ALL\'); document.searchProjet.submit();" ').'</div>';
	$bouton .= '<a name="export" style="cursor:pointer;" onclick="exportTab();"><img title="exportation tableur" alt="exportation tableur" src="'.getStaticUrl('img').'prospec/export-csv.png"/> Export Tableur</a>';

	if($modHistorique)
	    $bouton = '';
	return generateZBox($titre,$titre,$out,$bouton,"searchListe",'');
    }


    /**
     * Create portlet for Company detail
     * @param $id_ent String: company ID
     * @return HTML portlet ready to insert
     */
    static function ProjetFicheBlock($id_proj,$id_cont,$opt) {
	// on memorise la visite
	zunoHistoriqueVisite($id_proj,'projet');

	// On récupère les data de la base de donnée
	$ent = array();
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	if ($id_proj != '') {
	    $bddtmp->makeRequeteFree("SELECT * FROM projet WHERE  id_proj = '".$id_proj."'");
	    $res = $bddtmp->process();
	    $projet = $res[0];
	    $bddtmp->makeRequeteFree("SELECT * FROM contact LEFT JOIN entreprise ON entreprise_cont = id_ent WHERE  id_cont = '".$projet['contact_proj']."'");
	    $res = $bddtmp->process();
	    $con = $res[0];
	    $tag['action'] = inputTag('hidden','action','','','','modifProjet','');
	    $tag['action'] .= inputTag('hidden','id_proj','','','',$id_proj,'');
	    if(array_key_exists('out',$GLOBALS))
		$GLOBALS['out']->setTitle('Fiche projet "'.$projet['id_proj'].'" - '.$con['nom_ent'],'',true);
	}
	elseif ($id_cont != '') {
	    $bddtmp->makeRequeteFree("SELECT * FROM contact WHERE  id_cont = '".$id_cont."'");
	    $res = $bddtmp->process();
	    $con = $res[0];
	    $tag['action'] = inputTag('hidden','action','','','','newProjet','');
	}

	if($opt != 'supp') {
	    $tag['FormName'] = $opt.'_proj';
	    if (($opt == 'modif')and($id_proj > 0)) {
		$tag['action']  = inputTag('hidden','action','','','',$opt,'');
		$tag['action'] .= inputTag('hidden','id_proj','','','',$id_proj,'');
		$tag['action'] .= inputTag('hidden','contact_proj','','','',$projet['contact_proj'],'');
	    }
	    elseif ($opt == 'new') {
		$tag['action'] = inputTag('hidden','action','','','',$opt,'');
		$tag['action'] .= inputTag('hidden','contact_proj','','','',$id_cont,'');
	    }
	    elseif ($opt == 'newapp') {
		$tag['action'] = inputTag('hidden','action','','','',$opt,'');
		$tag['action'] .= inputTag('hidden','appel_proj','','','',$id_cont,'');
	    }

	    $tag['titre'] 	= inputTag('text','titre_proj','','', 75,$projet['titre_proj'],'');
	    $tag['typeproj']= HtmlForm::addAutoSelect('typeproj_proj','ref_typeproj',$projet['typeproj_proj'],'','','',$GLOBALS['PropsecConf']['DBPool']);
	    $tag['desc'] 	= textareaTag('desc_proj',$projet['desc_proj'],'', 75, 5,'');
	    if ($projet['appel_proj'] != '') {
		$bddtmp->makeRequeteFree("SELECT * FROM appel WHERE  id_app = '".$projet['appel_proj']."'");
		$res = $bddtmp->process();
		$appelapp = $res[0];
		$bddtmp->makeRequeteFree("SELECT * FROM contact WHERE  id_cont = '".$appelapp['contact_app']."'");
		$res = $bddtmp->process();
		$contactapp = $res[0];
		$tag['appel'] 	= "<br><font size='2'>Projet d&egrave;t&egrave;ct&egrave; le ".
			DateUniv2Human($appelapp['appel_app'],'simpleLong').
			" aupr&egrave;s de ".$contactapp['civ_cont']." ".$contactapp['prenom_cont']." ".$contactapp['nom_cont'].
			" <a href=\"#\" onclick=\"window.location.reload();window.location='fiche.php?id_app=".$projet['appel_proj']."';\">(voir le detail de cet appel)</a></font>";
	    }
	    $tag['contact'] = ZunoSelectContact('contact_proj',$con['entreprise_cont'],$projet['contact_proj'],'','');
	    if ($projet['affaire_proj'] != '')
		$tag['affaire']   = "<a href=\"javascript:window.location = '../draco/Affaire.php?id_aff=".$projet['affaire_proj']."';zuno.popup.close();\">".imageTag(getStaticUrl('img').'prospec/projet.affaire.png','Affaire Liée')."Voir l'affaire ".$projet['affaire_proj']."</a>";
	    elseif(($projet['affaire_proj'] == '')and($opt != 'new'))
		$tag['affaire']   = "<a href=\"#\" onclick=\"return zuno.popup.open('../draco/PopupAffaire.php','id_proj=".$projet['id_proj']."',800,400,'','','resize','affaire');\">".imageTag(getStaticUrl('img').'prospec/projet.affaire.png','Affaire Liée')."Créer une affaire à partir de ce projet"."</a>";
	    else $tag['affaire']   = "";

	    if ($projet['rdv_proj'] != '')
		$datoss  = DateUniv2Human($projet['rdv_proj'],'simpleLong');
	    else  $datoss  = '';
	    $tag['rdv'] 	= inputDateTag('','rdv_proj',$datoss);
	    if ($projet['echeance_proj'] != '')
		$datoss  = DateUniv2Human($projet['echeance_proj'],'simpleLong');
	    else  $datoss  = '';
	    $tag['echeance'] =inputDateTag('text','echeance_proj',$datoss);
	    if ($projet['actif_proj'] == '1') {
		$option = 'checked';
		$cod_actif = '1';
	    }
	    else {
		$option = '';
		$cod_actif = '1';
	    }
	    $tag['actif'] 	= inputTag('checkbox','actif_proj','','','',$cod_actif,$option);
	    $tag['rdvavec'] = ZunoSelectContact('rdvavec_proj',$con['entreprise_cont'],$projet['rdvavec_proj'],'','');
	    $tag['decid']   = ZunoSelectContact('decid_proj',$con['entreprise_cont'],$projet['decid_proj'],'','');
	    $tag['budget'] 	= inputTag('text','budget_proj','','', 6,$projet['budget_proj'],'');
	    $tag['comm']	= textareaTag('comm_proj',$projet['comm_proj'],'', 75, 5,'');

	    // On s'occupe de la boite Détail
	    $corps = templating('prospec/Projet.Fiche',$tag);
	}
	// On Prépare la grosse boite qui englobe tout ca
	if($opt == 'supp') {
	    $titre = " Suppression du projet ".$projet['id_proj']." d&eacute;t&eacute;ct&eacute; par ".$projet['utilisateur_proj']." le ".DateUniv2Human($projet['detect_proj'],'simpleLong');
	    $pied 	= '<a href="Projet.php?action=suppconfirm&id_proj='.$projet['id_proj'].'">'.imageTag(getStaticUrl('img').'prospec/confirm.png','','middle').' Confirmer</a>
				   <a href="javascript:zuno.popup.close();">'.imageTag(getStaticUrl('img').'prospec/cancel.png','','middle').' Annuler</a>';
	}
	elseif($opt == 'modif') {
	    $titre = " Projet d&eacute;t&eacute;ct&eacute; le ".DateUniv2Human($projet['detect_proj'],'simpleLong')." d&eacute;t&eacute;ct&eacute; par ".$projet['utilisateur_proj']." chez ".$con['nom_ent'];
	    $pied 	= '<a href="javascript:document.'.$tag['FormName'].'.reset()">'.imageTag(getStaticUrl('img').'prospec/cancel.png','','middle').' Annuler</a>
				   <a href="javascript:document.'.$tag['FormName'].'.submit()">'.imageTag(getStaticUrl('img').'prospec/record.png','','middle').' Modifier ce projet</a>';
	}
	else {
	    $titre = "Creation d'une fiche projet pour l'entreprise ".$ent['nom_ent'];
	    $pied  = '<a href="javascript:document.'.$tag['FormName'].'.reset()">Annuler</a>
				  <a href="javascript:document.'.$tag['FormName'].'.submit()">'.imageTag(getStaticUrl('img').'prospec/record.png','','middle').' Enregistrer ce projet</a>';
	}

	// On traite la boite et on retourne le tout
	return generateZBox($titre, $titre, $corps,$pied,'ProjectBox','');
    }


    /**
     * Create portlet for calling list
     * @param $date String: date to use for selecting list
     * @param $opt String: option
     * @return HTML portlet ready to insert
     */
    static function BoxLeadsSearch($valeurs) {
	$script = "<script type=\"text/javascript\">
			function ChangeProjetResultPage(limit,from) {
			    ChangeAction('searchProjet');
			    ChangeLimit(limit);
			    ChangeFrom(from);
			    zuno.business.formTools.sendFormAjah('searchProjet','prospec/ListeLeads.php','listeResultProjet');
			}
			function ChangeAction(value) {\$('actionProjetSearch').value = value;}
			function ChangeFrom(value) { \$('fromProjetSearch').value = value; }
			function ChangeLimit(value) { \$('limitProjetSearch').value = value; }
			</script>";
	$listeOrder['detect_proj'] = "Date de détéction";
	$listeOrder['rdv_proj'] = "Date de RDV";
	$listeOrder['echeance_proj'] = "Echeance du projet";
	$listeOrder['typeproj_proj'] = "Type de projet";
	$listeOrder['utilisateur_proj'] = "Commercial";
	$listeOrder['nom_cont'] = "Nom du contact";
	$listeOrder['fonction_cont'] = "Fonction du contact";
	$listeOrder['nom_ent'] = "Nom d'entreprise";
	$listeOrder['cp_ent'] = "Ville";

	$tag['FormName'] = "SearchLeads";
	$tag['action'] = '<input type="hidden" name="action" value="searchDevis" id="actionProjetSearch" />'.
		'<input type="hidden" name="from" value="0" id="fromProjetSearch" />'.
		'<input type="hidden" name="limit" value="30" id="limitProjetSearch" />';
	//Bouton de l'echeance
	if ($valeurs['echeance_proj'] == '') $valeurs['echeance_proj'] = DateUniv2Human('','simpleLong');
	$tag['echeance_proj']  =inputDateTag('','echeance_proj',$valeurs['echeance_proj']);

	//Choix de l'utilisateur
	if ($valeurs['utilisateur_proj'] == '')
	    $valeurs['utilisateur_proj'] = $_SESSION["user"]["id"];
	$bddtmp = new Bdd('');
	$bddtmp->makeRequeteFree('SELECT * FROM user WHERE actif = \'1\' ORDER BY nom ASC');
	$res = $bddtmp->process();
	if (count($res) > 0)
	    foreach ($res as $key => $data)
		$champ[$data['login']] = $data['civ']." ".$data['prenom']." ".$data['nom']." (".$data['login'].")";
	$tag['utilisateur_proj'] = selectTag('utilisateur_proj',$champ,$valeurs['utilisateur_proj']);

	//Choix des proj avec RDV
	if ($valeurs['rdv_proj'] == '1')
	    $option = 'checked="checked"';
	else $option = '';
	$tag['rdv_proj'] 	= inputTag('checkbox','rdv_proj','','','','1',$option);

	//Choix du département
	$tag['cp_ent']= HtmlForm::addAutoSelect('cp_ent','ref_departement',$valeurs['cp_ent'],'','','',$GLOBALS['PropsecConf']['DBPool']);
	//Choix du type de projets
	$tag['typeproj_proj']= HtmlForm::addAutoSelect('typeproj_proj','ref_typeproj',$valeurs['typeproj_proj'],'','','',$GLOBALS['PropsecConf']['DBPool']);
	//Choix du titre de projet
	$tag['titre_proj'] 	= inputTag('text','titre_proj','','',20,$valeurs['titre_proj']);
	$tag['ImgChart'] = shell_exec('php ../Img.ProjetChart.php');

	//Choix de l'activité du projet
	if ($valeurs['actif_proj'] == '1') {
	    $option = 'checked="checked"';
	}
	else {
	    $option = '';
	}
	$tag['actif_proj'] = inputTag('checkbox','actif_proj','','','','1',$option);

	//Choix de l'ordre
	$tag['order']= selectTag('order',$listeOrder,$valeurs['order']);

	$titre = 'Recherche des projets';
	$corps = $script.templating('prospec/Leads.BoxSearch',$tag);
	$pied  = '<a href="javascript:document.'.$tag['FormName'].'.reset()">'.imageTag(getStaticUrl('img').'prospec/cancel.png','','middle').' Annuler</a>
			    <a href="javascript:document.'.$tag['FormName'].'.submit()">'.imageTag(getStaticUrl('img').'prospec/voir.png','','middle').' Rechercher</a>';
	$pied .= "<a href=\"#\" onclick=\"return zuno.popup.open('Projet.php','&action=new&format=popup',800,535,'','','resize','projet');\">".imageTag(getStaticUrl('img').'prospec/add.png','','middle')." Ouvrir une nouvelle fiche projet</a>";
	return generateZBox($titre,$titre,$corps,$pied,'relanceSearch','none');
    }

    /**
     * Create portlet for calling list
     * @param $date String: date to use for selecting list
     * @param $opt String: option
     * @return HTML portlet ready to insert
     */
    static function BoxLeadsListe($valeurs,$opt) {
	//Choix de l'echeance
	if ($valeurs['echeance_proj'] != '')
	    $reqAdd .= "AND echeance_proj <= '".DateHuman2Univ($valeurs['echeance_proj'])."' ";

	//Choix de l'utilisateur
	if ($valeurs['utilisateur_proj'] != '')
	    $reqAdd .= "AND utilisateur_proj = '".$valeurs['utilisateur_proj']."' ";
	//Choix du type de projet
	if ($valeurs['typeproj_proj'] != '')
	    $reqAdd .= "AND typeproj_proj = '".$valeurs['typeproj_proj']."' ";
	//Choix de la relance active
	if ($valeurs['rdv_proj'] == '1')
	    $reqAdd .= 'AND rdv_proj IS NOT NULL ';
	//Choix du département
	if ($valeurs['cp_ent'] != '')
	    $reqAdd .= 'AND cp_ent LIKE \''.$valeurs['cp_ent'].'%\' ';
	//Choix du titre
	if ($valeurs['titre_proj'] != '')
	    $reqAdd .= 'AND titre_proj LIKE \'%'.$valeurs['titre_proj'].'%\' ';
	//Choix du type de contact
	if ($valeurs['actif_proj'] == '1')
	    $reqAdd .= 'AND actif_proj = \'0\' ';
	else
	    $reqAdd .= 'AND actif_proj = \'1\' ';
	//Choix de l'ordre
	if ($valeurs['order'] == '')
	    $reqOrder = 'detect_proj';
	else  $reqOrder = $valeurs['order'];

	$req = "SELECT * FROM projet,contact,entreprise
			WHERE contact_proj = id_cont
			AND entreprise_cont = id_ent
			".$reqAdd."
			ORDER BY ".$reqOrder." ASC, detect_proj ASC,nom_ent ASC";

	// requete pour la liste
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();

	// remplissage du tableau
	if (count($res) > 0) {
	    $j=1;
	    foreach ($res as $key => $proj) {
		if ($proj['affaire_proj'] != '')
		    $proj['Bouton']   = "<a href=\"../draco/Affaire.php?id_aff=".$proj['affaire_proj']."\">".imageTag(getStaticUrl('img').'prospec/projet.affaire.png','Affaire '.$proj['affaire_proj'].' Liée')."</a> ";
		if ($_SESSION['user']['id'] == $proj['utilisateur_proj'])
		    $proj['Bouton']   .= "<a href=\"../prospec/Projet.php?id_proj=".$proj['id_proj']."\">".imageTag(getStaticUrl('img').'prospec/projet.modif.png','Modifier')."</a>
			      <a href=\"../prospec/Projet.php?action=supp&id_proj=".$proj['id_proj']."\">".imageTag(getStaticUrl('img').'prospec/projet.delete.png','Supprimer')."</a>";
		else  $proj['Bouton']   .= "";
		$proj['TDAction'] = "onclick=\"window.location='Projet.php?id_proj=".$proj['id_proj']."';\"";

		if ($proj['ville_ent'] != '') {
		    $fdddddd = explode (" ",$proj['ville_ent']);
		    $villess = '('.$fdddddd[0].')';
		}
		else  $villess	= '';
		if (strlen ($proj['nom_ent']) > 40)
		    $entss = substr($proj['nom_ent'],0,40)."...";
		else  $entss = $proj['nom_ent'];
		$proj['entreprise'] = "<b>".$entss."</b> <font size='1'>".$villess."</font>";

		if ($proj['rdv_proj'] != '')
		    $proj['titre_proj'] = '<font color="'.$GLOBALS['PropsecConf']['color.leadRdv'].'"><b>** RDV le '.DateUniv2Human($proj['rdv_proj'],'simpleLong').' ** </b></font> '.$proj['titre_proj'];
		$proj['detect_proj'] = DateUniv2Human($proj['detect_proj'],'simpleLong');
		$proj['scorcolor'] = '';
		$proj['score'] = '';

		$proj['altern'] = ($j++ % 2);
		$tmptab .= templating('prospec/Leads.Liste.row',$proj);
	    }
	    $tab['liste'] = $tmptab ;
	    $corps = templating('prospec/Leads.Liste',$tab);
	    $result = '';
	    //$bouton .= $this->resultNavigation($data['from'],$data['limit'],$data['total'],"ChangeProjetResultPage",$suffixeLink);

	}
	else {
	    $corps = '<span class="important">Vous n\'avez pas de projet correspondant a cette demande</span>';
	    $result = 'none';
	}

	$titre = imageTag(getStaticUrl('img').'prospec/projet.liste.png','resultat').' '.(int)$j.' Projets corespondent à votre recherche';
	return generateZBox($titre,$titre,$corps,$bouton,'relance'.$opt,$result);
    }


    static function MyBureau() {
	$limit = 10;
	$req = new projetModel();
	$total = $req->getDataForHistoriqueVisit($limit,$type = 'COUNT');
	$result= $req->getDataForHistoriqueVisit($limit);
	$datas['total'] = $total[1][0]['counter'];
	$datas['data'] = $result[1];
	$datas['from'] = 0;
	$datas['limit'] = $limit;
	$datas['isHistoVisit'] = true;
	$view = new projetView();
	if($datas['total'] > 0)
	    return $view->listeResult($datas);
	else return "";
    }

}

?>
