<?php
loadPlugin(array('ZModels/JournalBanqueModel','ZModels/FactureModel', 'ZunoRenduHTML','ZView/GeneralView'));
/*
 * Created on 5 oct. 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
/**
 * Classe qui gère les formulaires journalBanques
 * @author Nicolas Mannocci
 * @version 1
 */
class JournalBanqueView extends generalView {

    private $model;
    protected $fieldset1;
    protected $fieldset2;
    protected $fieldset3;
    private $fieldsetF;
    private $form;
    private $formF;
    private $html;
    private $script;
    private $topscript;
    private $compteurFourn;

    /**
     * Constructeur de la classe
     *
     */
    public function __construct() {
	$this->model = new journalBanqueModel();
	$this->compteurFourn = 0;
	$this->script = "<script>
	    function autoCompletage() { var ent = 1; } 
	    function ChangeJournalBanqueResultPage(limit,from) {
	    ChangeAction('searchJournalBanque');
	    ChangeLimit(limit);
	    ChangeFrom(from);
	    zuno.business.formTools.sendFormAjah('formSearchJournalBanque','facturier/JournalBanque.php','searchListeJournalBanque');
	}
	function ChangeFrom(value) { \$('fromJournalBanqueSearch').value = value; }
	function ChangeLimit(value) { \$('limitJournalBanqueSearch').value = value; }
	function ChangeAction(value) { \$('actionJournalBanqueSearch').value = value;}
	</script>";
	$this->topscript = "<script>
	 function autoCompleteHidden(text, li) { $(text.id+'hidden').value = li.title; }
	 function verifAutocompleteur(champ) {
		if(champ.value == \"\" || champ.value == null) {
		    $(champ.id+'hidden').value = \"\";
		}
		else if(!$(champ.id+'_choix').hasChildNodes()) {
		    alert('Cette valeur n\\'existe pas.');
		    champ.value=$(champ.id+'old').value;
		    setTimeout(function() { champ.focus(); }, 100)
		}
	}
	</script>";
    }

    /**
     * Méthode générant l'affichage de toute la fiche journalBanque
     * @param array $datas Tableau des valeurs sortant de la bdd
     * @return string Le code HTML
     */
    private function vueTotale($datas = array()) {
	$this->initioFieldSet($datas['data']);
	$this->form = new ZunoForm('formJournalBanque', '../facturier/JournalEcriture.php', 'POST', '',true);
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$this->form->newInputHidden('action', 'modifJournalEcriture', 'ToDo');
	$this->form->newInputHidden('idJournalBanque', $datas['data']['id_jb']);
	$this->form->newInputHidden('file_jb', $datas['data']['file_jb']);
	$pied = $this->form->generateButtonsNoAjax('Annuler', 'Modifier cette écriture');

	$this->html = new ZunoRenduHtml('ficheCompletePontComptaHisto');
	$this->html->insertTopJS($this->topscript);
	$this->html->replaceJS($this->script);
	$this->html->generateZBox(imageTag('../img/prospec/journalBanque.png','JournalBanques').' JournalBanque '.$datas['data']['id_jb'], '', $this->form->generateForm(), $pied,'formJournalBanqueModif', 'open');
	return $this->html->generateRendu();
    }

    /**
     * Méthode initialisant les filedsets contenus dans le fromulaire journalBanque
     * @param array $datas Données de la Bdd
     */
    protected function initioFieldSet($datas) {
	$modelFact = new factureModel();
	$this->fieldset1 = new ZunoFieldset('Enregistrement','','height: 150px');
	$this->fieldset1->ligneFreeObligatoire('Référence', $datas['id_jb'],'ZTips.journalBanque.reference');
	$this->fieldset1->ligneInputDateObligatoire('Date de création', 'date_record_jb', DateUniv2Human($datas['date_record_jb'],'simpleLong'), '', '', '', 'ZTips.journalBanque.dateRecord');
	$this->fieldset1->ligneTArea('Commentaire', 'commentaire_jb', $datas['commentaire_jb'],'','ZTips.journalBanque.commentaire');

	$this->fieldset2 = new ZunoFieldset('Détail de l\'écriture','','height: 150px');
	$sens = ($datas['montant_jb'] < 0) ? 'D' : 'C';
	$radioOpt = array(0 => array('value'=>'C','label'=>'Crédit'), 1 => array('value'=>'D','label'=>'Débit'));
	$this->fieldset2->ligneRadioObligatoire('sens', 'sens', $sens, $radioOpt,'','ZTips.journalBanque.sens', false);
	$this->fieldset2->ligneInputObligatoire('Montant', 'montant_jb', abs($datas['montant_jb']),'','ZTips.journalBanque.montant');
	$this->fieldset2->ligneInputObligatoire('Libellé', 'libelle_jb', $datas['libelle_jb'],'','ZTips.journalBanque.libelle');
	$this->fieldset2->ligneInputDateObligatoire('Date d\'effet', 'date_effet_jb', DateUniv2Human($datas['date_effet_jb'],'simpleLong'), '', '', '', 'ZTips.journalBanque.dateEffet');
	$this->fieldset2->ligneSelectObligatoire('Banque', 'banque_jb',  $datas['banque_jb'], $this->model->getAllBanques(), false,'','ZTips.journalBanque.banque');
	$this->fieldset2->ligneSelectObligatoire('Mode', 'modereglement_jb',  $datas['modereglement_jb'], $this->model->getModeReglement(), false,'','ZTips.journalBanque.modereglement');

	$this->fieldset3 = new ZunoFieldset('Pièces attachées','','height: 150px');
	$this->fieldset3->ligneInputFile('Justificatif', 'file_jb', $datas['file_jb'],'','ZTips.journalBanque.file');
	if($datas['file_jb'] != '')
	    $this->fieldset3->ligneLinkTo('Justificatif', '../facturier/JournalEcriture.php?action=get&id_jb='.$datas['id_jb'], $datas['file_jb'], '../img/download.png');
	$nomFact = ($datas['facture_jb'] != '') ? $datas['type_fact'].' '.$modelFact->getFormatedIdFromData($datas).': '.$datas['titre_fact'] : '';
	$idFact  = ($datas['facture_jb'] != '') ? $datas['facture_jb'] : '';
	$this->fieldset3->ligneAutoComplete("Facture", "facture_jb", $nomFact, $idFact, "listeFacturePlusEnt",'',false,'',true,'ZTips.journalBanque.facture');
	$nomEnt = ($datas['entreprise_jb'] != '') ? $datas['nom_ent'].' ('.strtoupper($datas['ville_ent']).')' : '';
	$idEnt  = ($datas['entreprise_jb'] != '') ? $datas['entreprise_jb'] : '';
	$this->fieldset3->ligneAutoComplete("Client", "entreprise_jb", $nomEnt, $idEnt, "listeEntreprise",'',false,'',true,'ZTips.factFourn.entreprise');

    }

    /**
     * Méthode initialisant les filedsets contenus dans le fromulaire journalBanque
     * @param array $datas Données de la Bdd
     */
    protected function initioFieldSetCreate($datas) {
	if($datas['date_record_jb'] == '')
	    $datas['date_record_jb'] = strftime("%d-%m-%Y %T");
	if($datas['date_effet_jb'] == '')
	    $datas['date_effet_jb'] = strftime("%d-%m-%Y %T");
	if($datas['sens'] == '')
	    $datas['sens'] = "C";


	$this->fieldset1 = new ZunoFieldset('Enregistrement','','height: 150px');
	$this->fieldset1->ligneFree('Référence', '<i>aucun</i>','ZTips.journalBanque.reference');
	$this->fieldset1->ligneInputDateObligatoire('Date de création', 'date_record_jb', DateUniv2Human($datas['date_record_jb'],'simpleLong'), '', '', '', 'ZTips.journalBanque.dateRecord');
	$this->fieldset1->ligneTArea('Commentaire', 'commentaire_jb', $datas['commentaire_jb'],'','ZTips.journalBanque.commentaire');

	$this->fieldset2 = new ZunoFieldset('Détail de l\'écriture','','height: 150px');
	$radioOpt = array(0 => array('value'=>'C','label'=>'Crédit'), 1 => array('value'=>'D','label'=>'Débit'));
	$this->fieldset2->ligneRadioObligatoire('sens', 'sens', $datas['sens'], $radioOpt,'','ZTips.journalBanque.sens', false);
	$nomFact = ($datas['facture_jb'] != '') ? "Facture ".$datas['facture_jb'] : '';
	$idFact  = ($datas['facture_jb'] != '') ? $datas['facture_jb'] : '';
	$this->fieldset2->ligneInputObligatoire('Montant', 'montant_jb', $datas['montant_jb'],'','ZTips.journalBanque.montant');
	$this->fieldset2->ligneInputObligatoire('Libellé', 'libelle_jb', $datas['libelle_jb'],'','ZTips.journalBanque.libelle');
	$this->fieldset2->ligneInputDateObligatoire('Date d\'effet', 'date_effet_jb', DateUniv2Human($datas['date_effet_jb'],'simpleLong'), '', '', '', 'ZTips.journalBanque.dateEffet');
	$this->fieldset2->ligneSelectObligatoire('Banque', 'banque_jb',  $datas['banque_jb'], $this->model->getAllBanques(), false,'','ZTips.journalBanque.banque');
	$this->fieldset2->ligneSelectObligatoire('Mode', 'modereglement_jb',  $datas['modereglement_jb'], $this->model->getModeReglement(), false,'','ZTips.journalBanque.modereglement');

	$this->fieldset3 = new ZunoFieldset('Pièces attachées','','height: 150px');
	$radioOpt = array(0 => array('value'=>'C','label'=>'Crédit'), 1 => array('value'=>'D','label'=>'Débit'));
	$this->fieldset3->ligneAutoComplete("Facture", "facture_jb", $nomFact, $idFact, "listeFacturePlusEnt",'',false,'',true,'ZTips.factFourn.facture');
	$nomEnt = ($datas['entreprise_jb'] != '') ? "Client ".$datas['entreprise_jb'] : '';
	$idEnt  = ($datas['entreprise_jb'] != '') ? $datas['entreprise_jb'] : '';
	$this->fieldset3->ligneAutoComplete("Client", "entreprise_jb", $nomEnt, $idEnt, "listeEntreprise",'',false,'',true,'ZTips.factFourn.entreprise');
    }

    /**
     * Méthode qui ne génère que le contenu du formulaire journalBanque
     * @param array $datas Les données de la base de données
     * @return string Le code HTML
     */
    private function interneInfos($datas) {
	$this->initioFieldSet($datas['data']);
	$this->form = new ZunoForm('formJournalBanque', 'facturier/JournalEcriture.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$out = $this->form->getLastBlock();
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$out .= $this->form->getLastBlock();
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$out .= $this->form->getLastBlock();
	return $out;
    }

    /**
     * Méthode appelée dès que l'on cherche à voir une fiche journalBanque
     * @param array $datas Les données de la Bdd
     * @param string $light Précise ce que l'on veut voir
     * @param string $mess Indique le mesage à afficher le cas échéant
     * @return string Le code HTML
     */
    public function view($datas, $light='', $mess='') {
	$titrePontComptaHisto = (strlen($datas['data']['libelle_jb']) > 30) ? substr($datas['data']['libelle_jb'],0,30).'...' : $datas['data']['libelle_jb'];
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Ecriture bancaire "'.$datas['data']['id_jb'].'" - '.$titrePontComptaHisto,'',true);
	if($light == 'interneInfos')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneInfos($datas);
	else return $this->vueTotale($datas);
    }

    /**
     * Methode appelée pour voir le formulaire de création d'un journalBanque
     * @param array $datas Les données de la base
     * @param bool $popup Indique si on est en popup ou pas
     * @return string Le rendu HTML
     */
    public function creer($datas, $popup = false) {
	$this->initioFieldSetCreate($datas['data']);
	$this->form = new ZunoForm('formJournalBanque', 'facturier/JournalEcriture.php', 'POST', '',true);
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$this->form->newInputHidden('action', 'addJournalEcriture', 'addJournalBanqueID');
	$pied = $this->form->generateButtons('ficheCompletePontComptaHisto', 'Annuler', 'Enregistrer cette écriture');

	$this->html = new ZunoRenduHtml('ficheCompletePontComptaHisto');
	$this->html->insertTopJS($this->topscript);
	$this->html->replaceJS($this->script);
	$this->html->generateZBox(imageTag('../img/prospec/journalBanque.png','JournalBanques').' Nouveau fichier d\'export comptable', '', $this->form->generateForm(), $pied,'creationPontComptaHisto' , 'open');

	return $this->html->generateRendu();
    }

    /**
     * Méthode qui affiche la liste des résultats d'une recherche de journalBanques
     * @param array $datas Les données à afficher
     * @return string Le code HTML
     */
    public function listeResult($datas,$option = null) {
	return '<div id=listeResultJournalBanque>'.$this->Result($datas,$option).'</div>';
    }

    /**
     * Méthode qui génère le code HTML du tableau de résultats d'une recherche
     * @param array $datas Les données à afficher
     * @return string Le code HTML généré
     */
    private function Result($datas,$option = null) {
	$modelFact = new factureModel();
	$modHistorique = false;
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit']) {
	    $suffixeLink = './';
	    $modHistorique = true;
	}
	else $suffixeLink = '../';

	$out .= '<div class="blockTable">
		    <form id="resultFormJournalBanque" method="post" action="JournalBanque.php" name="resultJournalBanque">
		    <input type="hidden" name="action" value="" id="resultJournalBanqueAction" />
		    <input type="hidden" name="exportType" value="" id="resultJournalBanqueExportType" />
		    <table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">
			<th class="barre" rowspan="2" style="width:10px"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormJournalBanque\');"/></th>
			<th class="center barre" rowspan="2">Libellé</th>
			<th class="center barre" colspan="2">Date</th>
			<th class="last center" colspan="2">Montant</th>
			<th class="last center" rowspan="2" style="width:150px">Justif</th>
			<th class="last center" rowspan="2" style="width:150px">Facture</th>
			<th class="last center" rowspan="2" style="width:150px">Client</th>
		</tr>
		<tr class="subtitre">
			<th class="center barre" style="width:100px">Enregistrement</th>
			<th class="center barre" style="width:100px">Effet</th>
			<th class="center barre" style="width:150px">Crédit</th>
			<th class="center barre" style="width:150px">Débit</th>
		</tr>';
	$alternance =  0;
	$totalC = $totalD = 0;
	if(is_array($datas['data'])) {
	    foreach($datas['data'] as $result) {
		$linkEnt = $linkFact = $linkFile = $action = '';
		$link = $suffixeLink."facturier/JournalEcriture.php?id_jb=".$result['id_jb'];
		$onclick = 'window.location=\''.$link.'\'';
		if($result['file_jb'] != '')
		    $linkFile = linkTag($suffixeLink.'facturier/JournalEcriture.php?action=get&id_jb='.$result['id_jb'],imageTag($suffixeLink.'img/download.png','Pièce justificative').$result['file_jb'], '', 'Pièce justificative');
		if($result['facture_jb'] != '')
		    $linkFact = linkTag($suffixeLink.'facturier/Facture.php?id_fact='.$result['facture_jb'],imageTag($suffixeLink.'img/actualite/facture.png','Facture associée').$result['type_fact'].' '.$modelFact->getFormatedIdFromData($result), '', 'Facture associée');
		if($result['entreprise_jb'] != '')
		    $linkEnt = linkTag($suffixeLink.'prospec/fiche.php?id_ent='.$result['entreprise_jb'],imageTag($suffixeLink.'img/actualite/contact.png','Client associé').$result['nom_ent'], '', 'Client associé');
		$totalC += ($result['montant_jb'] < 0) ? 0 : $result['montant_jb'];
		$totalD += ($result['montant_jb'] < 0) ? abs($result['montant_jb']) : 0;
		$montantC = ($result['montant_jb'] < 0) ? '' : formatCurencyDisplay($result['montant_jb']).'';
		$montantD = ($result['montant_jb'] < 0) ? formatCurencyDisplay(abs($result['montant_jb'])) : '';
		$out .= '<tr class="altern'.($alternance % 2).'">';
		$out .= '<td class="barre"><input type="checkbox" name="select[]" value="'.$result['id_jb'].'" checked="checked"/></td>';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['libelle_jb'].'</td>';
		$out .= '<td class="barre right" style="cursor : pointer;" onclick="'.$onclick.'">'.DateUniv2Human($result['date_record_jb'],'simpleLong').'</td>';
		$out .= '<td class="barre right" style="cursor : pointer;" onclick="'.$onclick.'">'.DateUniv2Human($result['date_effet_jb'],'simpleLong').'</td>';
		$out .= '<td class="barre right" style="cursor : pointer;" onclick="'.$onclick.'">'.$montantC.'</td>';
		$out .= '<td class="barre right" style="cursor : pointer;" onclick="'.$onclick.'">'.$montantD.'</td>';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="'.$onclick.'">'.$linkFile.'</td>';
		$out .= '<td class="barre" style="cursor : pointer;">'.$linkFact.'</td>';
		$out .= '<td class="barre" style="cursor : pointer;">'.$linkEnt.'</td>';
		$out .= '</tr>';
		$alternance++;
	    }
	}
	$out .= '<tr class="titre noround roundbottom">
			<td colspan="4"><br/></td>
			<th class="bordG right barre">'.formatCurencyDisplay(abs($totalC)).'</th>
			<th class="bordD right">'.formatCurencyDisplay(abs($totalD)).'</th>
			<td class="last center" colspan="2"><br/></td>
		</tr>';
	$out .= '</tbody></table>';
	$out .= $this->ResultGroupActionForm();
	$out .= '</form></div>';
	if ($modHistorique)
	    $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'journalBanqueHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'journalBanque');
	if ($modHistorique)
	    $bouton = '';
	else $bouton = $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeJournalBanqueResultPage",$suffixeLink);
	$bouton .= $this->ResultGroupActionBouton($suffixeLink);

	return generateZBox($titre,$titre,$out,$bouton,"searchListeJournalBanque",'');
    }




    private function ResultGroupActionForm() {
	$sql = new journalBanqueModel();
	$out = '<div id="divGroupAction" style="display: none"><div class="block width50">';
	$fieldset = new ZunoFieldset('Type d\'action','','height: 90px');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'delete', ' onclick="toggleSubGroupedAction(\'groupedSubActionDelete\')"'),'Supprimer ces écritures','ZTips.journalBanque.groupActionDelete');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttribute', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttribute\')"'),'Changer un attribut par lot','ZTips.journalBanque.groupActionChangeAttribute');
	$out.= $fieldset->generateFieldset();
	$out.= '</div><div class="block width50"><div id="groupedSubAction">';
	$fieldset = new ZunoFieldset('Supprimer ces écritures','groupedSubActionDelete','height: 30px');
	$fieldset->ligneCheckBox('Supprimer les fichiers', 'deleteFile', true, '', 'ZTips.journalBanque.groupActionDeleteWithFile');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Changer un attribut par lot','groupedSubActionChangeAttribute','height: 30px');
	$fieldset->ligneInput('Libellé', 'libelle_jb','','','ZTips.journalBanque.libelle');
	$out.= $fieldset->generateFieldset();
	$out.= '</div>';
	$fieldset = new ZunoFieldset('Executer le changement');
	$fieldset->ligneLinkTo('','javascript:submitGroupAction(\'resultFormJournalBanque\',\'resultJournalBanqueAction\');" class="bouton','Effectuer les changements','../img/prospec/voir.png');
	$out.= $fieldset->generateFieldset();
	$out.= '</div></div></div>';
	return $out;
    }

    private function ResultGroupActionBouton($suffixeLink) {
	$bouton = '<a name="groupAction" style="cursor:pointer;" onclick="toggleGroupedAction(\'divGroupAction\');"><img title="actions groupées" alt="actions groupées" src="'.$suffixeLink.'img/prospec/work-multiple.png"/> Actions groupées</a>';
	return $bouton;
    }





    /**
     * Méthode appelée pour rechercher des journalBanques.
     * @param array $datas Les données nécessaires
     * @return string Le HTML
     */
    public function searchResult($datas, $light='') {
	if($light == 'result' or $light == 'jsResult')
	    return $this->Result($datas,$light).$this->script;
	else {
	    $popup = '';
	    if($light == 'jsReturn') {
		$light = 'jsResult';
		$popup = 'popup';
	    }
	    elseif($light == '') $light = 'result';
	    $form = $this->searchForm($datas,$light);
	    $rendu = new ZunoRenduHtml('recherchePontComptaHisto');
	    $form->addButton('Ajouter une nouvelle écriture', 'window.location = \'../facturier/JournalEcriture.php?action=new\';', '../img/prospec/journalBanque.png');
	    $rendu->generateZBox('Recherche', 'Recherche', $form->generateForm(), $form->generateButtons('listeResultJournalBanque', 'Annuler', 'Rechercher',$popup), 'divSearchPontComptaHisto', 'open');
	    $rendu->addBox($this->listeResult($datas,$light));
	    $rendu->insertJS("<script type=\"text/javascript\">Event.observe(document, 'keypress', function(event){ if(event.keyCode == Event.KEY_RETURN) zuno.business.formTools.sendFormAjah('formSearchJournalBanque', 'facturier/JournalBanque.php','listeResultJournalBanque'); return false;}, false);</script>".$this->script);
	    return $rendu->generateRendu();
	}

    }

    /**
     * Méthode searchForm qui génère le formulaire de recherche d'un journalBanque
     * @param array $datas D'éventuels champs pré-remplis
     * @return form Le formulaire
     */
    public function searchForm($datas,$option = 'result') {
	$form = new ZunoForm('formSearchJournalBanque', 'facturier/JournalBanque.php', 'POST');
	$fieldset = new ZunoFieldset('Recherche');
	$fieldset->ligneInput('Libellé', 'libelle_jb', $datas['form']['libelle_jb'],'','ZTips.journalBanque.searchLibelle');
	$fieldset->ligneInputDate('Date d\'effet entre', 'dateStart_jb', $datas['form']['date_record_jb'],"","","",'ZTips.journalBanque.searchDateEffetMin');
	$fieldset->ligneInputDate('et', 'dateEnd_jb', $datas['form']['date_effet_jb'],"","","",'ZTips.journalBanque.searchDateEffetMax');
	$fieldset->ligneInput('Montant min', 'montantMin_jb', $datas['form']['montantMin'],'','ZTips.journalBanque.searchMontantMin');
	$fieldset->ligneInput('Montant max', 'montantMax_jb', $datas['form']['montantMax'],'','ZTips.journalBanque.searchMontantMax');
	$radioOpt = array(' ' => array('value'=>'','label'=>'Les deux'), 0 => array('value'=>'C','label'=>'Crédit'), 1 => array('value'=>'D','label'=>'Débit'));
	$fieldset->ligneRadio('sens', 'sens', $datas['form']['sens'], $radioOpt,'','ZTips.journalBanque.searchSens', false);
	$form->newBlock($fieldset->generateFieldset(), '50');
	$form->newInputHidden('result', $option);
	$form->newInputHidden("action", "searchJournalBanque", "actionJournalBanqueSearch");
	$form->newInputHidden("limit", 30, "limitJournalBanqueSearch");
	$form->newInputHidden("from", 0, "fromJournalBanqueSearch");
	return $form;
    }


    /**
     * Fonction appelée pour voir le formulaire de création d'un journalBanque
     * @param $datas	les données de la base
     * @param $popup	Indique si on est en popup ou pas
     * @return $rendu	Le rendu HTML
     */
    public function searchPopup($datas) {

	return $this->searchResult($datas,'jsReturn');
    }
}

?>
