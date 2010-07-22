<?php
loadPlugin(array('ZModels/AffaireModel', 'ZunoRenduHTML'));
loadPlugin('ZView/GeneralView');


/**
 * Classe qui gère les vues des affaires
 * @author Nicolas Mannocci
 * @version 0.5
 */
class affaireView extends generalView {

    private $script;
    private $blockForm;
    private $blockEvo;
    private $blockHisto;
    private $blockSearch;
    private $blockResult;
    private $blockCreate;

    /**
     * Constrcuteur de la classe qui instancie le javascript par défault
     */
    public function __construct() {
	$this->script = '<script>';
	$this->script .= "\n".'function autoCompletage() { ent = 1;} ';
	$this->script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$this->script .= '</script>';
    }

    /**
     * Méthode qui gère l'affichage d'une fiche affaire
     * @param array $datas
     * @param string $light
     * @param string $mess
     * @return string
     */
    public function view($datas, $light = '', $mess = '') {
	if($light == 'interneInfos')
	    $out = '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneInfos($datas);
	else $out = $this->vueTotale($datas);
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fiche affaire "'.$datas['data']['id_aff'].'" - '.$datas['data']['nom_ent'],'',true);
	return $out;
    }

    /**
     * Méthode qui gère l'affichage du formulaire de création d'une affaire
     * @return string
     */
    public function creer() {
	$this->formCreation();

	$this->script = '<script>';
	$this->script .= "\n".'function autoCompletage() { entreprise_completer = new Ajax.Autocompleter("ACentreprise_aff", "ACentreprise_aff_choix", "../ajaxRef.php?action=listeEntreprise", {paramName: "value", minChars: 2, indicator: "loadingentreprise_aff", afterUpdateElement: autoCompleteEntreprise} );';
        $this->script .= "\n".'contact_completer = new Ajax.Autocompleter("ACcontact_aff", "ACcontact_aff_choix", "../ajaxRef.php?action=listeContact", { paramName: "value", minChars: 2, indicator : "loadingcontact_aff", afterUpdateElement : autoCompleteHidden } ); ';
	$this->script .= "\n".'contact_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_affhidden\') }';
	$this->script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$this->script .= "\n".'function autoCompleteEntreprise(text, li) {autoCompleteHidden(text, li); contact_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_affhidden\');}';
        $this->script .= '</script>';
	$rendu = new ZunoRenduHtml();
	$rendu->insertJS($this->script);
	$rendu->generateZBox('Nouvelle affaire', 'Nouvelle affaire', $this->blockCreate->generateForm(), $this->blockCreate->generateButtonsNoAjax('Annuler', 'Créer'), 'idAffaireCreation', 'open', true);
	return $rendu->generateRendu();
    }

    /**
     * Méthode qui sert à faire une popup de création
     * @param array $data Les données pré-établies
     * @return ZunoForm Le formulaire
     */
    public function popupCreation($data = array()) {
	$data['from'] = 'popupCont';
	$this->formCreation($data);
	$script = '<script>';
	$script .= "\n".'contact_completerBis = new Ajax.Autocompleter("ACcontact_aff", "ACcontact_aff_choix", "../ajaxRef.php?action=listeContact", { paramName: "value", minChars: 2, indicator : "loadingcontact_aff", afterUpdateElement : autoCompleteHidden } ); ';
	$script .= "\n".'contact_completerBis.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_affhidden\'); ';
	$script .= '</script>';

	$out = $this->blockCreate->generateForm();
	$out .= $script;
	return $out;
    }

    /**
     * Méthode qui génère une popup d'archivage d'affaire(s)
     * @param array $id L'e / L'ID de l'affaire à archiver
     * @return string Le code HTML
     */
    public function popupConfirmArchiv($id) {
	$titre = "Archivage";
	$form = new ZunoForm('AffaireArchiver', 'draco/Affaire.php', 'POST');
	$form->newInputHidden('action', 'doArchiver');
	$form->newInputHidden('aff', $id);
	$form->newInputHidden('ID['.$id.']',$id);
	$text = '<span class="important">Confirmez vous l\'archivage de l\'affaire "'.$id.'"</span>';
	$block = new ZunoRenduHtml();
	$block->generateZBox($titre, $titre, $text.$form->generateForm(), $form->generateButtons('idformAffaireformAutoRetour', "Annuler", "Effectuer", "popup2"), 'idPopupArchiAff');
	return $block->generateRendu();

    }

    /**
     * Méthode qui initialise le formulaire de création
     * @param array $data D'éventuelles données pré-établies
     */
    private function formCreation($data = array()) {
	$this->blockCreate = new ZunoForm('AffaireCreate', 'AffaireCreate.php', 'POST');
	$this->blockCreate->newInputHidden('action', 'creer');
	if(isset($data['from']))
	    $this->blockCreate->newInputHidden('from', $data['from']);

	$nom = trim($data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont']);

	$fieldset = new ZunoFieldset('Informations','','height: 90px');
	$fieldset->ligneInput('Nom', 'titre_aff');
	$fieldset->ligneAutoComplete('Entreprise', 'entreprise_aff', $data['nom_ent'], $data['id_ent'], 'listeEntreprise', '', false, '', false);
	$fieldset->ligneAutoCompleteObligatoire('Contact', 'contact_aff', $nom, $data['id_cont'], 'listeContact', '', false, '', false);
	$this->blockCreate->newBlock($fieldset->generateFieldset(), '50');

    }

    /**
     * Méthode qui renvoie l'intérieur du formulaire de modif d'une affaire
     * @param array $datas
     * @return string
     */
    private function interneInfos($datas) {
	$this->initioBlockForm($datas, 'modif');
	$block = $this->blockForm->getBlocks();
	foreach($block as $v) {
	    $out .= $v;
	}
	return $out;
    }

    /**
     * Méthode qui génère la vue de toute la fiche affaire
     * @param array $datas
     * @return string
     */
    private function vueTotale($datas) {
	$this->initioBlockFiles($datas);
	$this->initioBlockEvo($datas);
	$this->initioBlockForm($datas, 'modif');

	$rendu = new ZunoRenduHtml('ficheAffaire');
	$rendu->generateZBox(imageTag('../img/prospec/affaire.png','Affaire').' Informations sur l\'affaire '.$datas['data']['id_aff'], 'Informations', $this->blockForm->generateForm(), $this->blockForm->generateButtons(), 'idFormAffModif', 'open', true, 'blockAffaireModif');
	$rendu->generateZBox(imageTag('../img/prospec/affaire.png','Affaire-Evo').' Évolution de cette affaire', 'Évolution', $this->blockEvo, '', 'idAffaireEvolution', 'open');
	$rendu->generateZBox(imageTag('../img/page.menu/DirInfo.png','Affaire-Files').' Fichiers de cette affaire', 'Fichiers', $this->blockFiles, '', 'idAffaireFiles', 'open');
	$rendu->insertJS($this->script);
	return $rendu->generateRendu();
    }

    /**
     * Méthode qui initialise le block formulaire de modif d'une affaire
     * @param array $datas Les données
     * @param string $action
     */
    private function initioBlockForm($datas, $action = 'modif') {
	$data = $datas['data'];
	$this->blockForm = new ZunoForm('formAffaire', 'draco/Affaire.php', 'POST');
	$this->blockForm->newInputHidden('id_aff', $data['id_aff']);
	$this->blockForm->newInputHidden('action', $action);

	$fieldset = new ZunoFieldset('Informations','','height: 140px');
	$fieldset->ligneInput('Titre', 'titre_aff', $data['titre_aff']);
	if ($data['type_ent'] != '')
	      $img	= '../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$data['type_ent'].'.png';
	else {$img	= '../img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png'; $data['nom_ent'] = '<i>particulier</i>'; }
	$fieldset->ligneLinkTo('Entreprise', '../prospec/fiche.php?id_ent='.$data['entreprise_aff'], $data['nom_ent'],$img);
	$fieldset->ligneAutoComplete('Contact', 'contact_aff', trim($data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont']), $data['contact_aff'], 'listeContact&id_ent='.$data['entreprise_aff']);
	$fieldset->ligneSelect('Commercial', 'commercial_aff', $data['commercial_aff'], $datas['user']);
	$fieldset->addOtherLigne('<div class="row">
					<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.statut);">Statut</div>
					<div class="field" style="color: #'.$data['color_staff'].'"><div class="square" style="background-color:#'.$data['color_staff'].';">&nbsp;</div> '.$data['nom_staff'].'</div>
				</div>');
	$this->blockForm->newBlock($fieldset->generateFieldset());

	$fieldset = new ZunoFieldset('Détails','','height: 140px');
	$fieldset->ligneInput('Budget', 'budget_aff', $data['budget_aff']);
	$date = ($data['echeance_aff'] != null) ? strftime('%d/%m/%Y',strtotime($data['echeance_aff'])) : '';
	$fieldset->ligneInputDate('Échéance', 'echeance_aff', $date);
	$fieldset->ligneAutoComplete('Décideur', 'decid_aff', trim($data['civ_cont2'].' '.$data['prenom_cont2'].' '.$data['nom_cont2']), $data['decid_aff'], 'listeContact&id_ent='.$data['entreprise_aff']);
	$fieldset->ligneSelect('Type', 'typeproj_aff', $data['typeproj_aff'], $datas['type'], false);
	$fieldset->ligneCheckBox('Actif', 'actif_aff', ($data['actif_aff'] == 1) ? true : false);
	$this->blockForm->newBlock($fieldset->generateFieldset());

	$fieldset = new ZunoFieldset('Autre','','height: 140px');
	$fieldset->ligneTArea('Description', 'desc_aff', $data['desc_aff']);
	$fieldset->ligneTArea('Commentaire', 'comm_aff', $data['comm_aff']);
	$this->blockForm->newBlock($fieldset->generateFieldset());

	if(count($datas['related']['devis']) > 0 or count($datas['related']['commandes']) > 0 or count($datas['related']['factures']) > 0) {
	    $fieldset = new ZunoFieldset('Documents liées à cette affaire');
	    if(count($datas['related']['devis']) > 0) {
		foreach($datas['related']['devis'] as $v)
		    $fieldset->ligneLinkTo('Devis', '../draco/Devis.php?id_dev='.$v['id_dev'], $v['id_dev'].' '.$v['titre_dev'],'../img/actualite/devis.png');
	    }
	    if(count($datas['related']['commandes']) > 0) {
		foreach($datas['related']['commandes'] as $v)
		    $fieldset->ligneLinkTo('Commande', '../pegase/Commande.php?id_cmd='.$v['id_cmd'], $v['id_cmd'].' '.$v['titre_cmd'],'../img/actualite/commande.png');
	    }
	    if(count($datas['related']['factures']) > 0) {
		foreach($datas['related']['factures'] as $v)
		    $fieldset->ligneLinkTo('Facture', '../facturier/Facture.php?id_fact='.$v['id_fact'], $v['id_fact'].' '.$v['titre_fact'],'../img/actualite/facture.png');
	    }
	    $this->blockForm->newBlock($fieldset->generateFieldset());
	}
	$this->blockForm->addButton('Historique de cette affaire','return zuno.popup.open(\'../draco/Affaire.php\', \'action=actuAffaire&id_aff='.$data['id_aff'].'\', \'850\', \'450\', \'\', \'\', \'resize\', \'Actualités\');','../img/prospec/actu.png');
	if($data['archived_aff'] == 0)
	    $this->blockForm->addButton('Archiver cette affaire', 'return zuno.popup.open(\'../draco/Affaire.php\', \'action=archiver&aff='.$data['id_aff'].'\', \'300\', \'150\', \'\', \'\', \'resize\', \'Archiver\')', '../img/prospec/affaire.png');
    }

    /**
     * Méthode qui initialise le block du graphique de Gant
     * @param array $datas
     */
    private function initioBlockEvo($datas) {
	if(strlen($datas['data']['id_aff']) == 6 and is_numeric($datas['data']['id_aff']))
	    $this->blockEvo = shell_exec('php ../Img.AffaireGantt.php '.$datas['data']['id_aff']);
    }

    /**
     * Méthode qui initialise le block du graphique de Gant
     * @param array $datas
     */
    private function initioBlockFiles($datas) {
	loadPlugin(array('ZView/DocumentView'));
	if($datas['data']['archived_aff'] == 1)
	     $workDir = $GLOBALS['SVN_Pool1']['ArchivesDir'];
	else $workDir = $GLOBALS['SVN_Pool1']['WorkDir'];
	$blockFiles = new documentViewRepertoire($workDir.$GLOBALS['ZunoAffaire']['dir.affaire'].$datas['data']['dir_aff']);
	$this->blockFiles = $blockFiles->afficher();
    }


    public function searchResult($datas, $light='') {
	if($light == 'result')
	    return $this->Result($datas);
	else return $this->searchForm($datas).$this->listeResult($datas);
    }

    private function searchForm($datas) {
	$script = '<script type="text/javascript">function autoCompletage() { /* new Ajax.Autocompleter("cpAffaireSearch", "cpAffaireSearch_choix", "../ajaxRef.php?action=listeCp", { paramName: "value", minChars: 2, indicator : "loadingCp" , afterUpdateElement : autoCompleteHidden } ); ';
	$script .= 'new Ajax.Autocompleter("affaireAffaireSearch", "affaireAffaireSearch_choix", "../ajaxRef.php?action=listeAffaire", { paramName: "value", minChars: 2, indicator: "loadingAff" , afterUpdateElement : autoCompleteHidden } ); ';
	$script .= 'new Ajax.Autocompleter("entrepriseAffaireSearch", "entrepriseAffaireSearch_choix", "../ajaxRef.php?action=listeEntreprise", { paramName: "value", minChars: 2, indicator : "loadingEnt" , afterUpdateElement : autoCompleteHidden } ); ';
	$script .= 'new Ajax.Autocompleter("userAffaireSearch", "userAffaireSearch_choix", "../ajaxRef.php?action=listeUser", { paramName: "value", minChars: 2, indicator: "loadingUsr", afterUpdateElement : autoCompleteHidden } );*/   } ';
	$script .= 'function ChangeAffaireID(value) { $(\'idAffaireSearch\').value = value; } ';
	$script .= 'function verifVide(objet) { if(objet.value == "") { $(objet.id+\'hidden\').value = ""; } } ';
	$script.= "function ChangeAffaireResultPage(limit,from,order,sens) {
			    ChangeAffaireAction('searchAffaire');
			    if(limit != undefined) ChangeLimit(limit);
			    if(from  != undefined) ChangeFrom(from);
			    if(order != undefined) ChangeOrder(order,sens);
			    ChangeSubmit();
			}
			function ChangeAffaireAction(value) {\$('listeAffaireAction').value = value;}
			function ChangeFrom(value) { \$('fromAffaireSearch').value = value; }
			function ChangeLimit(value) { \$('limitAffaireSearch').value = value; }
			function ChangeOrder(value,sens) {
			    if(sens == undefined) sens = 'ASC';
			    \$('orderAffaireSearch').value = value;
			    \$('orderSensAffaireSearch').value = sens;
			}
			function ChangeSubmit() {
			    zuno.business.formTools.sendFormAjah('SearchAffaire', 'draco/ListeAffaires.php','listeResultAffaire');
			}
			Event.observe(document, 'keypress', function(event){ if(event.keyCode == Event.KEY_RETURN) ChangeSubmit(); return false;}, false);
			";
	$script .= 'function camenbertAffaire(value, max) { $(\'affaireBudgetMini\').value = value; $(\'affaireBudgetMaxi\').value = max; ChangeAffaireAction(\'searchAffaire\');zuno.business.formTools.sendFormAjah(\'searchAffaire\', \'draco/ListeAffaires.php\',\'listeResultAffaire\'); } ';
	$script .= 'function resetForm() {var form = $(\'searchFormAffaire\'); form.reset(); form.select(\'input[type="hidden"]\').each(function(input) {input.clear();}); } ';
	$script .= 'function exportTab() { if ($(\'divExportTableur\').style.display == \'inline\') {$(\'divExportTableur\').style.display = \'none\';} else {$(\'divExportTableur\').style.display = \'inline\';} }';
	$script .= '</script>';

	$tag['FormName'] = "SearchAffaire";
	$titre = imageTag('../img/prospec/voir.png','recherche').' Recherche des affaires';
	$corps = '<form name="'.$tag['FormName'].'" action="ListeAffaires.php" method="post" id="searchFormAffaire">';
	$corps .= '<div id="PortletSearchAffaire">';
	$corps .= '<input type="hidden" name="action" value="searchAffaire" id="listeAffaireAction" />';
	$corps .= '<input type="hidden" name="id_aff" value="" id="idAffaireSearch" />';
	$corps .= '<input type="hidden" name="from" value="0" id="fromAffaireSearch" />';
	$corps .= '<input type="hidden" name="limit" value="30" id="limitAffaireSearch" />';
	$corps .= '<input type="hidden" name="order" value="id_aff" id="orderAffaireSearch" />';
	$corps .= '<input type="hidden" name="orderSens" value="DESC" id="orderSensAffaireSearch" />';
	$corps .= $this->interneSearch($datas);
	$corps .= '</div></form>';
	$bouton = '<a name="reset" onclick="resetForm();" style="cursor:pointer;"><img title="Reset" alt="reset" src="../img/prospec/cancel.png"/> Annuler</a>';
	$bouton.= '<a name="valider" onclick="ChangeAffaireAction(\'searchAffaire\');ChangeSubmit();"><img title="Valider" alt="valid" src="../img/prospec/voir.png" />Rechercher</a>';
	return generateZBox($titre,$titre,$corps.$script,$bouton,'searchBox');
    }

    private function interneSearch($datas) {
	$tag['action']   = '';
	//Bouton de l'echeance
	$tag['echeance_aff']  =inputDateTag('','echeance_aff',$datas['echeance_aff']);
	$bddtmp = new Bdd('');
	$bddtmp->makeRequeteFree('SELECT * FROM user WHERE actif = \'1\' ORDER BY nom ASC');
	$res = $bddtmp->process();
	if (count($res) > 0) {
	    foreach ($res as $key => $data) {
		$champ[$data['login']] = $data['civ']." ".$data['prenom']." ".$data['nom']." (".$data['login'].")";
	    }
	}
	$tag['FormName'] = "SearchAffaire";
	$tag['commercial_aff'] = selectTag('commercial_aff',$champ,$datas['commercial_aff']);
	$tag['cp_ent']= HtmlForm::addAutoSelect('cp_ent','ref_departement',$datas['cp_ent'],'','','',$GLOBALS['PropsecConf']['DBPool']);
	$tag['typeproj_aff']= HtmlForm::addAutoSelect('typeproj_aff','ref_typeproj',$datas['typeproj_aff'],'','','',$GLOBALS['PropsecConf']['DBPool']);
	$tag['status_aff']= HtmlForm::addAutoSelect('status_aff','ref_statusaffaire',$datas['status_aff'],'','',' ORDER by id_staff ASC',$GLOBALS['PropsecConf']['DBPool']);
	$tag['titre_aff'] 	= inputTag('text','titre_aff','','',20,$datas['titre_aff']);
	$tag['id_aff'] 	= inputTag('text','id_aff','','',20,$datas['id_aff']);
	$tag['nom_ent'] = inputTag('text','nom_ent','','',20,$datas['nom_ent']);
	$tag['ImgChart'] = shell_exec('php ../Img.AffaireChart.php');
	if ($datas['actif_aff'] == '0')
	    $option = '';
	else $option = 'checked="checked"';
	$tag['actif_aff'] = inputTag('checkbox','actif_aff','','','','1',$option);
	if ($datas['archived_aff'] == '1')
	    $option = 'checked="checked"';
	else	$option = '';
	$tag['archived_aff'] = inputTag('checkbox','archived_aff','','','','1',$option);

	$corps = '<div class="PortletSearchAff">
	<div class="block width50"><fieldset class="form" style="height: 240px"><legend>Critères de recherche</legend>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchIC);">IC</div>
				<div class="field">'.$tag['commercial_aff'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchRegion);">Region</div>
				<div class="field">'.$tag['cp_ent'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchID);">ID</div>
				<div class="field">'.$tag['id_aff'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchEntreprise);">Entreprise</div>
				<div class="field">'.$tag['nom_ent'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchTitre);">Titre</div>
				<div class="field">'.$tag['titre_aff'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchArchive);">Archives</div>
				<div class="field">'.$tag['archived_aff'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchActif);">Activées</div>
				<div class="field">'.$tag['actif_aff'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchStatut);">Statut</div>
				<div class="field">'.$tag['status_aff'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchEcheance);">Echeance</div>
				<div class="field">'.$tag['echeance_aff'].'</div>
			</div>
			<div class="row">
				<div class="label" onmouseover="zuno.tooltip(this,ZTips.affaire.searchType);">Type</div>
				<div class="field">'.$tag['typeproj_aff'].'</div>
			</div>
		</fieldset>
	</div>
	<div class="block width50"><fieldset class="form" style="height: 240px"><legend>Répartition des opportunitées</legend>
			<div class="row">'.$tag['ImgChart'].'</div>
		</fieldset>
	</div>
</div>';
	return $corps;
    }


    /**
     * Méthode qui donne la liste de résultat utilisée pour le bureau
     * @param array $datas
     * @return string
     */
    public function listeResult($datas) {
	return '<div id=listeResultAffaire>'.$this->Result($datas).'</div>';
    }

    /**
     * Méthode qui s'occupe de générer la liste de résultat de la recherche
     * @param array $datas
     * @return string
     */
    private function Result($datas) {
	$suffixeLink = '../';
	$modHistorique = false;
	$linkOrderFunction = 'tagLinkOrderSwitch';
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit']) {
	    $suffixeLink = './';
	    $modHistorique = true;
	    $linkOrderFunction = 'tagNoLinkOrderSwitch';
	}
	$out = "<script type=\"text/javascript\">
			function affaireDoExportTableur(value) {
			    $('resultAffaireAction').value = 'exportTableur';
			    $('resultAffaireExportType').value = value;
			    $('resultFormAffaire').submit();
			}
			</script>";
	$out .= '<div class="blockTable">
		    <form id="resultFormAffaire" method="post" action="ListeAffaires.php" name="resultAffaire">
		    <input type="hidden" name="action" value="" id="resultAffaireAction" />
		    <input type="hidden" name="exportType" value="" id="resultAffaireExportType" />
		    <table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">';
	if(!$modHistorique)
	    $out .= '<th class="barre"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormAffaire\');"/></th>';
	$out .= '<th class="barre">'.$this->$linkOrderFunction('ID','id_aff',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeAffaireResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Titre','titre_aff',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeAffaireResultPage',$suffixeLink).'</th>';
	$out .= '<th class="center barre">'.$this->$linkOrderFunction('Entreprise','nom_ent',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeAffaireResultPage',$suffixeLink).'</th>';
	$out .= '<th class="center barre">'.$this->$linkOrderFunction('Contact','nom_cont',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeAffaireResultPage',$suffixeLink).'</th>';
	$out .= '<th>'.$this->$linkOrderFunction('Montant HT','budget_aff',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeAffaireResultPage',$suffixeLink).'</th>';
	$out .= '<th class="barre">'.$this->$linkOrderFunction('Status','status_aff',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeAffaireResultPage',$suffixeLink).'</th>';
	$out .= '<th class="barre" style="width:1%; white-space: nowrap">'.$this->$linkOrderFunction('Actif','actif_aff',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeAffaireResultPage',$suffixeLink).'</th>';
	$out .= '<th class="barre" style="width:1%; white-space: nowrap">'.$this->$linkOrderFunction('Archivé','archived_aff',$datas['order'],$datas['orderSens'],$datas['limit'],'ChangeAffaireResultPage',$suffixeLink).'</th>';
	if(!$modHistorique)
	    $out .= '<th class="last center">Actions</th>';
	$out .= '</tr>';
	$alternance =  0;
	foreach($datas['data'] as $proj) {
	    $action = '';

	    if ($_SESSION['user']['id'] == $proj['commercial_aff'])
		$action   .= "<a onclick=\"zuno.popup.open('PopupAffaire.php','id_aff=".$proj['id_aff']."&action=modif',450,450,'','','resize','affaire');\">".imageTag($suffixeLink.'img/prospec/affaire.modif.png','Modifier')."</a>
			      <a onclick=\"zuno.popup.open('PopupAffaire.php','id_aff=".$proj['id_aff']."&action=supp',450,200,'','','resize','affairerem');\">".imageTag($suffixeLink.'img/prospec/affaire.delete.png','Supprimer')."</a>";
	    $actionJSLigne = "onclick=\"window.location='$suffixeLink/draco/Affaire.php?id_aff=".$proj['id_aff']."';\"";

	    if ($proj['nom_ent'] != '') {
		if (strlen($proj['nom_ent']) > 25)
		    $proj['nom_ent'] = substr($proj['nom_ent'],0,25)."...";
		if ($proj['type_ent'] != '')
		    $proj['nom_ent']	= imageTag($suffixeLink.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$proj['type_ent'].'.png',$proj['nom_tyent']).' '.$proj['nom_ent'];
		if (strlen($proj['ville_ent']) > 18)
		    $proj['ville_ent'] = substr($proj['ville_ent'],0,18)."...";
		if ($proj['ville_ent'] != '')
		    $proj['ville_ent'] = ' ('.$proj['ville_ent'].')';
		$proj['contact'] = $proj['nom_ent'].$proj['ville_ent'];
	    }
	    else {
		$proj['nom_ent'] = '<i>Particulier</i>';
		$proj['nom_ent']	= imageTag($suffixeLink.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').' '.$proj['nom_ent'];
		if (strlen($proj['ville_cont']) > 18)
		    $proj['ville_cont'] = substr($proj['ville_cont'],0,18)."...";
		if ($proj['ville_cont'] != '')
		    $proj['ville_cont'] = ' ('.$proj['ville_cont'].')';
		$proj['contact'] = $proj['nom_ent'].$proj['ville_cont'];
	    }
	    $proj['detect_aff'] = DateUniv2Human($proj['detect_aff'],'simpleLong');
	    if($proj['actif_aff'] == '1')
		$imgActif = imageTag($suffixeLink.'img/valid.png','Affaire active');
	    else $imgActif = imageTag($suffixeLink.'img/unvalid.png','Affaire non-active');
	    if($proj['archived_aff'] == '1')
		$imgArchive = imageTag($suffixeLink.'img/valid.png','Affaire archivée');
	    else $imgArchive = imageTag($suffixeLink.'img/unvalid.png','Affaire non-archivée');

	    $out .= '<tr class="altern'.$alternance.'">';
	    if(!$modHistorique)
		$out .= '<td class="barre" style="width:10px"><input type="checkbox" name="select[]" value="'.$proj['id_aff'].'" checked="checked"/></td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$proj['color_staff'].' !important;" '.$actionJSLigne.'\'"><b>'.$proj['id_aff'].'</b></td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$proj['color_staff'].' !important;" '.$actionJSLigne.'\'">'.$proj['titre_aff'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$proj['color_staff'].' !important;" '.$actionJSLigne.'\'">'.$proj['contact'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$proj['color_staff'].' !important;" '.$actionJSLigne.'\'">'.$proj['civ_cont'].' '.$proj['prenom_cont'].' '.$proj['nom_cont'].'</td>';
	    $out .= '<td class="right barre" style="cursor : pointer; color : #'.$proj['color_staff'].' !important;" '.$actionJSLigne.'\'">'.formatCurencyDisplay($proj['budget_aff'],0).'</td>';
	    $out .= '<td class="barre" style="cursor : pointer; color : #'.$proj['color_staff'].' !important;" '.$actionJSLigne.'\'"><div class="square" style="background-color:#'.$proj['color_staff'].';">&nbsp;</div>'.$proj['nom_staff'].'</td>';
	    $out .= '<td class="barre center" style="cursor : pointer;" '.$actionJSLigne.'\'">'.$imgActif.'</td>';
	    $out .= '<td class="barre center" style="cursor : pointer;" '.$actionJSLigne.'\'">'.$imgArchive.'</td>';
	    if(!$modHistorique)
		$out .= '<td class="right">'.$action.'</td>';
	    $out .= '</tr>';
	    $alternance = ($alternance+1) % 2;
	}

	$out .= '</tbody></table>';
	if(!$modHistorique)
	    $out .= $this->ResultGroupActionForm();
	$out .= '</form></div>';
	if($modHistorique)
	     $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'affaireHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'affaire');
	$bouton .= $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeAffaireResultPage",$suffixeLink);
	loadPlugin(array('OOConverter'));
	$availableConvFormat = OOConverterAvailable('spreadsheet');
	$bouton .= '<div style="display:none;" id="divExportTableur">'.selectTag('format_export', $availableConvFormat, '', '', ' onchange="affaireDoExportTableur(this.value);" ').'</div>';
	$bouton .= '<a name="export" style="cursor:pointer;" onclick="exportTab();"><img title="exportation tableur" alt="exportation tableur" src="'.$suffixeLink.'img/prospec/export-csv.png"/> Export Tableur</a>';
	$bouton .= $this->ResultGroupActionBouton($suffixeLink);

	if($modHistorique)
	    $bouton = '';
	return generateZBox($titre,$titre,$out,$bouton,"searchListeAffaire",'');
    }

    private function ResultGroupActionForm() {
	$sql = new affaireModel();
	$out = '<div id="divGroupAction" style="display: none"><div class="block width50">';
	$fieldset = new ZunoFieldset('Type d\'action','','height: 130px');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'activate', ' onclick="toggleSubGroupedAction(\'groupedSubActionActivate\')"'),'Activer ces affaires','ZTips.affaire.groupActionActivate');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'desactivate', ' onclick="toggleSubGroupedAction(\'groupedSubActionDesactivate\')"'),'Désactiver ces affaires','ZTips.affaire.groupActionDesactivate');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'archivate', ' onclick="toggleSubGroupedAction(\'groupedSubActionArchive\')"'),'Archivage des affaires','ZTips.affaire.groupActionArchive');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttribute', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttribute\')"'),'Changer un attribut par lot','ZTips.affaire.groupActionChangeAttribute');
	$out.= $fieldset->generateFieldset();
	$out.= '</div><div class="block width50"><div id="groupedSubAction">';
	$fieldset = new ZunoFieldset('Activer ce lot d\'affaires','groupedSubActionActivate','height: 70px');
	$fieldset->addOtherLigne('<div class="row"><i>Les affaires qui sont archivées ne seront pas activées</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Désactiver ce lot d\'affaires','groupedSubActionDesactivate','height: 70px');
	$fieldset->addOtherLigne('<div class="row"><i>Seul les affaires n\'ayant plus de devis, commande ou facture en cours de traitement seront désactivées.<br/>Les affaires qui sont archivées ne seront pas traitées</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Archiver ce lot d\'affaires','groupedSubActionArchive','height: 70px');
	$fieldset->addOtherLigne('<div class="row"><i>Seul les affaires n\'ayant plus de devis, commande ou facture en cours de traitement seront archivées.<br/></i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Changer un attribut par lot','groupedSubActionChangeAttribute','height: 70px');
	$fieldset->ligneInput('Titre de l\'affaire', 'titre_aff');
	$fieldset->ligneInputDate('Echeance', 'necheance_aff','');
	$fieldset->ligneSelect('Type d\'affaire', 'typeproj_aff','',$sql->getTypesProj());
	$out.= $fieldset->generateFieldset();
	$out.= '</div>';
	$fieldset = new ZunoFieldset('Executer le changement');
	$fieldset->ligneLinkTo('','javascript:submitGroupAction(\'resultFormAffaire\',\'resultAffaireAction\');" class="bouton','Effectuer les changements','../img/prospec/voir.png');
	$out.= $fieldset->generateFieldset();
	$out.= '</div></div></div>';
	return $out;
    }

    private function ResultGroupActionBouton($suffixeLink) {
	$bouton = '<a name="groupAction" style="cursor:pointer;" onclick="toggleGroupedAction(\'divGroupAction\');"><img title="actions groupées" alt="actions groupées" src="'.$suffixeLink.'img/prospec/work-multiple.png"/> Actions groupées</a>';
	return $bouton;
    }

    /**
     * Create portlet for Company detail
     * @param $id_ent String: company ID
     * @return HTML portlet ready to insert
     * @deprecated
     */
    static function affaireFicheBlockSimple($id_aff,$id_cont,$opt) {
	if($opt != 'supp') { 	// On récupére les data de la base de donnée
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    if ($id_aff != '') {
		$bddtmp->makeRequeteFree("SELECT * FROM affaire WHERE  id_aff = '".$id_aff."'");
		$res = $bddtmp->process();
		$affaire = $res[0];
		$bddtmp->makeRequeteFree("SELECT * FROM contact,entreprise WHERE entreprise_cont = id_ent AND id_cont = '".$affaire['contact_aff']."'");
		$res = $bddtmp->process();
		$con = $res[0];
		$tag['action'] = inputTag('hidden','action','','','','modifAffaire','');
		$tag['action'] .= inputTag('hidden','id_aff','','','',$id_aff,'');
	    }
	    elseif ($id_cont != '') {
		$bddtmp->makeRequeteFree("SELECT * FROM contact,entreprise WHERE entreprise_cont = id_ent AND id_cont = '".$id_cont."'");
		$res = $bddtmp->process();
		$con = $res[0];
		$tag['action'] = inputTag('hidden','action','','','','newAffaire','');
		$affaire['contact_aff'] = $id_cont;
	    }

	    $tag['FormName'] = $opt.'_aff';
	    if (($opt == 'modif')and($id_aff > 0)) {
		$tag['action'] = inputTag('hidden','action','','','',$opt,'');
		$tag['action'] .= inputTag('hidden','id_aff','','','',$id_aff,'');
		$tag['action'] .= inputTag('hidden','contact_aff','','','',$affaire['contact_aff'],'');
	    }
	    elseif ($opt == 'new') {
		$tag['action'] = inputTag('hidden','action','','','',$opt,'');
		$tag['action'] .= inputTag('hidden','contact_aff','','','',$id_cont,'');
		$tag['action'] .= inputTag('hidden','entreprise_aff','','','',$con['entreprise_cont'],'');
		$affaire['actif_aff'] = '1';
		$affaire['status_aff'] = '1';
		$affaire['typeproj_aff'] = '1';
		$affaire['entreprise_aff'] = $con['entreprise_cont'];
	    }
	    elseif ($opt == 'newapp') {
		$tag['action'] = inputTag('hidden','action','','','',$opt,'');
		$tag['action'] .= inputTag('hidden','appel_aff','','','',$id_cont,'');
	    }

	    if ($affaire['echeance_aff'] != '')
		$datoss  = DateUniv2Human($affaire['echeance_aff'],'simpleLong');
	    else	$datoss  = '';

	    if ($affaire['actif_aff'] == '1') {
		$option = 'checked';
		$cod_actif = '1';
		$nom_actif = 'Affaire active';
	    }
	    else {
		$option = '';
		$cod_actif = '1';
		$nom_actif = 'Affaire inactive';
	    }

	    if ($affaire['archived_aff'] == '1') {
		$tag['titre'] 	= $affaire['titre_aff'];
		$tag['typeproj']= HtmlForm::addAutoSelect('DISPLAY','ref_typeproj',$affaire['typeproj_aff'],'','','',$GLOBALS['PropsecConf']['DBPool']);
		$tag['statusproj']= HtmlForm::addAutoSelect('DISPLAY','ref_statusaffaire',$affaire['status_aff'],'','',' ORDER by id_staff ASC',$GLOBALS['PropsecConf']['DBPool']);
		$tag['desc'] 	= '<pre>'.$affaire['desc_aff'].'</pre>';
		$tag['contact'] = $con['civ_cont'].' '.$con['prenom_cont'].' '.$con['nom_cont'];
		$tag['status'] 	= $nom_actif.' & archivée';
		$tag['decid']   = ZunoSelectContact('DISPLAY',$con['entreprise_cont'],$affaire['decid_aff'],'','');
		$tag['budget'] 	= $affaire['budget_aff'];
		$tag['comm']	= '<pre>'.$affaire['comm_aff'].'</pre>';
		$tag['echeance'] = $datoss;
		$urlGnose = 'BrowseArchive.php';
	    }
	    else {
		$tag['titre'] 	= inputTag('text','titre_aff','','', 30,$affaire['titre_aff'],'');
		$tag['typeproj']= HtmlForm::addAutoSelect('typeproj_aff','ref_typeproj',$affaire['typeproj_aff'],'','','',$GLOBALS['PropsecConf']['DBPool']);
		$tag['statusproj']= HtmlForm::addAutoSelect('status_aff','ref_statusaffaire',$affaire['status_aff'],'','',' ORDER by id_staff ASC',$GLOBALS['PropsecConf']['DBPool']);
		$tag['desc'] 	= textareaTag('desc_aff',$affaire['desc_aff'],'', 60, 2,'');
		$tag['contact'] = ZunoSelectContact('contact_aff',$con['entreprise_cont'],$affaire['contact_aff'],'','');
		$tag['status'] 	= inputTag('checkbox','actif_aff','','','',$cod_actif,$option);
		$tag['decid']   = ZunoSelectContact('decid_aff',$con['entreprise_cont'],$affaire['decid_aff'],'','');
		$tag['budget'] 	= inputTag('text','budget_aff','','', 6,$affaire['budget_aff'],'');
		$tag['comm']	= textareaTag('comm_aff',$affaire['comm_aff'],'', 60, 1,'');
		$tag['echeance'] =inputDateTag('','echeance_aff',$datoss);
		$urlGnose = 'BrowseWork.php';
	    }

	    if ($opt != 'new') {
		$tag['projet'] .= "<div class=\"row\">
								<div class=\"label\">Détail</div>
								<div class=\"field\"><a href=\"javascript:window.location.href = 'Affaire.php?id_aff=".$affaire['id_aff']."';zuno.popup.close()\">".imageTag('../img/prospec/voir.png','Affaire Liée')."Fiche affaire complète"."</a></div>
							</div>";
		$tag['projet'] .= "<div class=\"row\">
								<div class=\"label\">Documents</div>
								<div class=\"field\"><a href=\"#\" onclick=\"return zuno.popup.open('../gnose/$urlGnose','dir=/Affaire/".$affaire['dir_aff']."&format=popup',700,400,'','','resize','gnose');\">".imageTag('../img/prospec/projet.png','Affaire Liée')."Dossier GNOSE"."</a></div>
							</div>";
		if ($affaire['projet_aff'] != '')
		    $tag['projet'] .= "<div class=\"row\">
								<div class=\"label\">Projet</div>
								<div class=\"field\"> onclick=\"return zuno.popup.open('../prospec/Projet.php','id_proj=".$affaire['projet_aff']."&format=popup',600,450,'','','resize','projet');\">".imageTag('../img/prospec/projet.png','projet Lié')."voir"."</a></div>
							</div>";
	    }
	    else	$tag['projet'] .= "";

	    // On s'occupe de la boite Détail
	    $corps = templating('draco/AffaireFiche.Simple',$tag);
	}
	// On Prépare la grosse boite qui englobe tout ca
	if($opt == 'supp') {
	    $scoreAffaire = affaireModel::ScoreAffaire($id_aff);
	    $titre = $scoreAffaire['LinkBox']." Suppression de la fiche affaire ".$id_aff;
	    $corps 	= '<span class="important">Attention, la suppression d\'une affaire implique la supression du répertoire GNOSE
				   associé. Vous pourrez récuperer ce dossier dans l\'historique de la GED.
				   La fiche affaire sera définitivement supprimée de nos bases.<br/>
				   Il est préférable d\'archiver les affaires sur lesquels des actions ont été réalisées.</span>';
	    $pied 	= '<a href="javascript:zuno.popup.close();">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
				   <a href="PopupAffaire.php?action=suppconfirm&id_aff='.$id_aff.'">'.imageTag('../img/prospec/confirm.png','','middle').' Confirmer</a>';
	}
	elseif($opt == 'modif') {
	    $scoreAffaire = affaireModel::ScoreAffaire($affaire['id_aff']);
	    $titre = $scoreAffaire['LinkBox']." Affaire ".$affaire['id_aff']." d&eacute;t&eacute;ct&eacute; le ".DateUniv2Human($affaire['detect_aff'],'simpleLong')." d&eacute;t&eacute;ct&eacute; par ".$affaire['commercial_aff'];
	    $pied 	= '<a href="javascript:document.'.$tag['FormName'].'.reset()">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
				   <a href="javascript:document.'.$tag['FormName'].'.submit()">'.imageTag('../img/prospec/record.png','','middle').' Modifier ce affaire</a>';
	}
	else {
	    $titre = "Creation d'une fiche affaire pour l'entreprise ".$con['nom_ent'];
	    $pied  = '<a href="javascript:document.'.$tag['FormName'].'.reset()">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
				  <a href="javascript:document.'.$tag['FormName'].'.submit()">'.imageTag('../img/prospec/record.png','','middle').' Enregistrer cette affaire</a>';
	}

	// On traite la boite et on retourne le tout
	return generateZBox($titre, $titre, $corps,$pied,'ProjectBox','');
    }


    public function popupActu($datas) {
	$alternance = 0;

	$titre = 'Actualités';

	$corps = '<div class="blockTable">';
	$corps .= '<table width="100%" cellspacing="0" cellpadding="2" border="0">';
	$corps .= '<tbody><tr class="titre">';
	$corps .= '<th></th><th>Date</th><th>Titre</th><th>Utilisateur</th>';
	$corps .= '</tr>';

	foreach($datas as $v) {
	    $corps .= '<tr class="altern'.($alternance % 2).'">
			    <td class="nowrap"><img src=\'../img/actualite/'.$v['type'].'.png\' alt=\''.$v['type'].'\'/>'.$v['type'].'</td>
			    <td class="barre nowrap">'.DateUniv2Human($v['date'],'shortdetail').'</td>
			    <td class="barre"><b>'.$v['titre'].'</b><br/><span style="font-size:.8em;font-style:italic;">'.$v['desc'].'</span></td>
			    <td class="barre nowrap">'.$v['user'].'</td></tr>';
	    $alternance++;
	}
	$corps .= '</tbody></table></div>';

	return generateZBox($titre,$titre,$corps,'',"popupAffaireActu",'');
    }

}






function BureauMyAffaire() {
    $limit = 10;
    $req = new affaireModel();
    $total = $req->getDataForHistoriqueVisit($limit,$type = 'COUNT');
    $result= $req->getDataForHistoriqueVisit($limit);
    $datas['total'] = $total[1][0]['counter'];
    $datas['data'] = $result[1];
    $datas['from'] = 0;
    $datas['limit'] = $limit;
    $datas['isHistoVisit'] = true;
    $view = new affaireView();
    if($datas['total'] > 0)
	return $view->listeResult($datas);
    else return "";
}

?>
