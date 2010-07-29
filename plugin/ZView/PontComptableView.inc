<?php
loadPlugin(array('ZModels/PontComptableModel', 'ZunoRenduHTML'));
loadPlugin('ZView/GeneralView');
/*
 * Created on 5 oct. 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
/**
 * Classe qui gère les formulaires pontComptables
 * @author Nicolas Mannocci
 * @version 1
 */
class PontComptableView extends generalView {

    private $model;
    protected $fieldset1;
    protected $fieldset2;
    protected $fieldset3;
    private $form;
    private $html;
    private $script;
    private $compteurFourn;

    /**
     * Constructeur de la classe
     *
     */
    public function __construct() {
	$this->model = new pontComptableModel();
	$this->compteurFourn = 0;
	$this->script = '<script>';
	$this->script .= 'function autoCompletage() {var ent = 1;} ';
	$this->script .= "function ChangePontComptableResultPage(limit,from) {
				ChangeAction('searchPontComptable');
				ChangeLimit(limit);
				ChangeFrom(from);
				zuno.business.formTools.sendFormAjah('formSearchPontComptaHisto','facturier/PontComptableHisto.php','searchListePontComptable');
			    }
			    function ChangeFrom(value) { \$('fromPontComptableSearch').value = value; }
			    function ChangeLimit(value) { \$('limitPontComptableSearch').value = value; }
			    function ChangeAction(value) { \$('actionPontComptableSearch').value = value;} ";
	$this->script .= "\n".'function autoCalcul(numero) {px = $(\'px\'+numero).value.replace(/,/,"."); rem = $(\'rem\'+numero).value.replace(/,/,"."); tot=px*(1-rem/100); tot = Math.round(tot*100)/100; $(\'tot\'+numero).value = tot; }';
	$this->script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$this->script .= "\n".'var REM = new Array();';
	$this->script .= '</script>';
    }

    /**
     * Méthode générant l'affichage de toute la fiche pontComptable
     * @param array $datas Tableau des valeurs sortant de la bdd
     * @return string Le code HTML
     */
    private function vueTotale($datas = array()) {

	$this->initioFieldSet($datas);

	$this->form = new ZunoForm('formPontComptable', 'facturier/PontComptable.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$this->form->newInputHidden('action', 'modifPontComptable', 'ToDo');
	$this->form->newInputHidden('idPontComptable', $datas['data']['id_pcth']);
	$this->form->newInputHidden('date_pcth', $datas['data']['date_pcth']);

	$this->html = new ZunoRenduHtml('ficheCompletePontComptaHisto');
	$this->html->replaceJS($this->script);
	$this->html->generateZBox(imageTag('../img/prospec/pontComptable.png','PontComptables').' PontComptable '.$datas['data']['id_pcth'], '', $this->form->generateForm(), $this->form->generateButtons('', 'Recommencer', 'Modifier ce fichier d\'export','','onload="autoCompletage();"',true),'formPontComptableModif', 'open');

	return $this->html->generateRendu();
    }

    /**
     * Méthode initialisant les filedsets contenus dans le fromulaire pontComptable
     * @param array $datas Données de la Bdd
     */
    protected function initioFieldSet($datas) {
	$this->fieldset1 = new ZunoFieldset('Enregistrement','','height: 150px');
	$this->fieldset1->ligneFree('Référence', $datas['data']['id_pcth'],'ZTips.pontComptable.reference');
	$this->fieldset1->ligneFree('Date', $datas['data']['date_pcth'],'ZTips.pontComptable.date');
	$this->fieldset1->ligneInputObligatoire('Nom', 'nom_pcth', $datas['data']['nom_pcth'],'','ZTips.pontComptable.nom');
	$this->fieldset1->ligneInputDateObligatoire('Début', 'config_dateDebut_pcth', DateUniv2Human($datas['data']['config_dateDebut_pcth'],'simpleLong'), '', '', '', 'ZTips.pontComptable.config_dateDebut');
	$this->fieldset1->ligneInputDateObligatoire('Fin', 'config_dateFin_pcth', DateUniv2Human($datas['data']['config_dateFin_pcth'],'simpleLong'), '', '', '', 'ZTips.pontComptable.config_dateFin');
	$this->fieldset1->ligneLinkTo('Fichier', '../facturier/PontComptable.php?action=get&id_pcth='.$datas['data']['id_pcth'], $datas['data']['fichier_pcth'], '../img/download.png');

	$this->fieldset2 = new ZunoFieldset('Facture Client','','height: 150px');
	$this->fieldset2->ligneCheckBoxObligatoire('inclure', 'config_hasFactureClient_pcth', $datas['data']['config_dateDebut_pcth'], '', 'ZTips.pontComptable.hasFactureClient');
	$this->fieldset2->ligneSelectMultiple('Statuts', 'config_statutFact_pcth',  explode(',', $datas['data']['config_statutFact_pcth']), $this->model->getAllFactureClientStatut(), false,'','ZTips.pontComptable.config_statutFact');
	
	$this->fieldset3 = new ZunoFieldset('Facture Fournisseur','','height: 150px');
	$this->fieldset3->ligneCheckBoxObligatoire('inclure', 'config_hasFactureFourn_pcth', $datas['data']['config_hasFactureFourn_pcth'], '', 'ZTips.pontComptable.hasFactureFourn');
	$this->fieldset3->ligneSelectMultiple('Statuts', 'config_statutFactFourn_pcth',  explode(',', $datas['data']['config_statutFactFourn_pcth']), $this->model->getAllFactureFournStatut(), false,'','ZTips.pontComptable.config_statutFactFourn');
    }

    /**
     * Méthode initialisant les filedsets contenus dans le fromulaire pontComptable
     * @param array $datas Données de la Bdd
     */
    protected function initioFieldSetCreate($datas) {
	if($datas['data']['date_pcth'] == '')
	    $datas['data']['date_pcth'] = strftime("%d/%m/%Y %T");
	if($datas['data']['config_hasFactureClient_pcth'] == '')
	    $datas['data']['config_hasFactureClient_pcth'] = 1;
	if($datas['data']['config_hasFactureFourn_pcth'] == '')
	    $datas['data']['config_hasFactureFourn_pcth'] = 1;
	if($datas['data']['config_dateDebut_pcth'] == '')
	    $datas['data']['config_dateDebut_pcth'] = strftime("%Y-%m-%d 00:00:00", mktime(0,0,0, strftime("%m"),1, strftime("%Y")));
	if($datas['data']['config_dateFin_pcth'] == '')
	    $datas['data']['config_dateFin_pcth'] = strftime("%Y-%m-%d 23:59:59", mktime(0,0,0, (strftime("%m")+1),0, strftime("%Y")));
	if($datas['data']['config_statutFact_pcth'] == '')
	    $datas['data']['config_statutFact_pcth'] = $GLOBALS['zunoPontComptable']['CreationDefaultSelectedStatutFactClient'];
	if($datas['data']['config_statutFactFourn_pcth'] == '')
	    $datas['data']['config_statutFactFourn_pcth'] = $GLOBALS['zunoPontComptable']['CreationDefaultSelectedStatutFactFourn'];

	$this->fieldset1 = new ZunoFieldset('Enregistrement','','height: 130px');
	$this->fieldset1->ligneFree('Référence', '<i>aucun</i>','ZTips.pontComptable.reference');
	$this->fieldset1->ligneFree('Date', $datas['data']['date_pcth'],'ZTips.pontComptable.date');
	$this->fieldset1->ligneInput('Nom', 'nom_pcth', $datas['data']['nom_pcth'],'','ZTips.pontComptable.nom');
	$this->fieldset1->ligneInputDateObligatoire('Début', 'config_dateDebut_pcth', DateUniv2Human($datas['data']['config_dateDebut_pcth'],'simpleLong'), '', '', '', 'ZTips.pontComptable.config_dateDebut');
	$this->fieldset1->ligneInputDateObligatoire('Fin', 'config_dateFin_pcth', DateUniv2Human($datas['data']['config_dateFin_pcth'],'simpleLong'), '', '', '', 'ZTips.pontComptable.config_dateFin');

	$this->fieldset2 = new ZunoFieldset('Facture Client','','height: 130px');
	$this->fieldset2->ligneCheckBoxObligatoire('inclure', 'config_hasFactureClient_pcth', $datas['data']['config_hasFactureClient_pcth'], '', 'ZTips.pontComptable.hasFactureClient');
	$this->fieldset2->ligneSelectMultiple('Statuts', 'config_statutFact_pcth', explode(',', $datas['data']['config_statutFact_pcth']), $this->model->getAllFactureClientStatut(), false,'','ZTips.pontComptable.config_statutFact');

	$this->fieldset3 = new ZunoFieldset('Facture Fournisseur','','height: 130px');
	$this->fieldset3->ligneCheckBoxObligatoire('Facture Fournisseur', 'config_hasFactureFourn_pcth', $datas['data']['config_hasFactureFourn_pcth'], '', 'ZTips.pontComptable.hasFactureFourn');
	$this->fieldset3->ligneSelectMultiple('Statuts', 'config_statutFactFourn_pcth',  explode(',', $datas['data']['config_statutFactFourn_pcth']), $this->model->getAllFactureFournStatut(), false,'','ZTips.pontComptable.config_statutFactFourn');
     }

    /**
     * Méthode qui ne génère que le contenu du formulaire pontComptable
     * @param array $datas Les données de la base de données
     * @return string Le code HTML
     */
    private function interneInfos($datas) {
	$this->initioFieldSet($datas);
	$this->form = new ZunoForm('formPontComptable', 'facturier/PontComptable.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$out = $this->form->getLastBlock();
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$out .= $this->form->getLastBlock();
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$out .= $this->form->getLastBlock();
	return $out;
    }

    /**
     * Méthode appelée dès que l'on cherche à voir une fiche pontComptable
     * @param array $datas Les données de la Bdd
     * @param string $light Précise ce que l'on veut voir
     * @param string $mess Indique le mesage à afficher le cas échéant
     * @return string Le code HTML
     */
    public function view($datas, $light='', $mess='') {
	$titrePontComptaHisto = (strlen($datas['data']['nom_pcth']) > 30) ? substr($datas['data']['nom_pcth'],0,30).'...' : $datas['data']['nom_pcth'];
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fichier d\'export comptable "'.$datas['data']['id_pcth'].'" - '.$titrePontComptaHisto,'',true);
	if($light == 'interneInfos')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneInfos($datas);
	else
	    return $this->vueTotale($datas);
    }

    /**
     * Methode appelée pour voir le formulaire de création d'un pontComptable
     * @param array $datas Les données de la base
     * @param bool $popup Indique si on est en popup ou pas
     * @return string Le rendu HTML
     */
    public function creer($datas, $popup = false) {
	$this->initioFieldSetCreate($datas);
	$this->form = new ZunoForm('formPontComptable', 'facturier/PontComptable.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$this->form->newInputHidden('action', 'addPontComptableHisto', 'addPontComptableID');
	$this->form->addButton('Enregistrer le fichier', '$(\'addPontComptableID\').value = \'addPontComptableHisto\';zuno.business.formTools.sendFormAjah(\'formPontComptable\', \'facturier/PontComptable.php\',\'ficheCompletePontComptaHisto\');', '../img/prospec/record.png');
	$this->form->addButton('Générer le fichier d\'export', '$(\'addPontComptableID\').value = \'generer\';document.formPontComptable.action = \'../facturier/PontComptable.php\';document.formPontComptable.submit();', '../img/download.png');
	$pied = $this->form->generateButtons('ficheCompletePontComptaHisto', 'Annuler', '');

	$this->html = new ZunoRenduHtml('ficheCompletePontComptaHisto');
	$this->html->replaceJS($this->script);
	$this->html->generateZBox(imageTag('../img/prospec/pontComptable.png','PontComptables').' Nouveau fichier d\'export comptable', '', $this->form->generateForm(), $pied,'creationPontComptaHisto' , 'open');

	return $this->html->generateRendu();
    }

    /**
     * Méthode qui affiche la liste des résultats d'une recherche de pontComptables
     * @param array $datas Les données à afficher
     * @return string Le code HTML
     */
    public function listeResult($datas,$option = null) {
	return '<div id=listeResultPontComptable>'.$this->Result($datas,$option).'</div>';
    }

    /**
     * Méthode qui génère le code HTML du tableau de résultats d'une recherche
     * @param array $datas Les données à afficher
     * @return string Le code HTML généré
     */
    private function Result($datas,$option = null) {
	$modHistorique = false;
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit']) {
	    $suffixeLink = './';
	    $modHistorique = true;
	}
	else $suffixeLink = '../';

	$out .= '<div class="blockTable">
		    <form id="resultFormPontComptable" method="post" action="PontComptableHisto.php" name="resultPontComptable">
		    <input type="hidden" name="action" value="" id="resultPontComptableAction" />
		    <input type="hidden" name="exportType" value="" id="resultPontComptableExportType" />
		    <table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">';
	if(!$modHistorique)
	    $out .= '<th class="barre"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormPontComptable\');"/></th>';
	$out .= '<th class="center barre">Nom</th>
			<th class="center barre">Date</th>
			<th class="last center">Client</th>
			<th class="last center">Fournisseur</th>
			<th class="last center">Fichier</th>
		</tr>';
	$alternance =  0;
	if(is_array($datas['data'])) {
	    foreach($datas['data'] as $result) {
		$action = '';
		$link = $suffixeLink."facturier/PontComptable.php?id_pcth=".$result['id_pcth'];
		$onclick = 'window.location=\''.$link.'\'';
		if($result['config_hasFactureClient_pcth'] == '1')
		    $imgFCActif = imageTag($suffixeLink.'img/valid.png','avec facture client');
		else $imgFCActif = imageTag($suffixeLink.'img/unvalid.png','sans facture client');
		if($result['config_hasFactureFourn_pcth'] == '1')
		    $imgFFActif = imageTag($suffixeLink.'img/valid.png','avec facture fournisseur');
		else $imgFFActif = imageTag($suffixeLink.'img/unvalid.png','sans facture fournisseur');
		$out .= '<tr class="altern'.($alternance % 2).'">';
		if(!$modHistorique)
		    $out .= '<td class="barre" style="width:10px"><input type="checkbox" name="select[]" value="'.$result['id_pcth'].'" checked="checked"/></td>';
		$out .= '<td class="barre" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['nom_pcth'].'</td>';
		$out .= '<td class="barre right" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['date_pcth'].'</td>';
		$out .= '<td class="barre center" style="cursor : pointer;" onclick="'.$onclick.'">'.$imgFCActif.'</td>';
		$out .= '<td class="barre center" style="cursor : pointer;" onclick="'.$onclick.'">'.$imgFFActif.'</td>';
		$out .= '<td class="right" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['fichier_pcth'].'</td>';
		$out .= '</tr>';
		$alternance++;
	    }
	}

	$out .= '</tbody></table>';
	$out .= $this->ResultGroupActionForm();
	$out .= '</form></div>';
	if($modHistorique)
	    $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'pontComptableHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'pontComptable');
	$bouton = $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangePontComptableResultPage",$suffixeLink);
	$bouton .= $this->ResultGroupActionBouton($suffixeLink);
	if($modHistorique)
	    $bouton = '';

	return generateZBox($titre,$titre,$out,$bouton,"searchListePontComptable",'');
    }




    private function ResultGroupActionForm() {
	$sql = new pontComptableModel();
	$out = '<div id="divGroupAction" style="display: none"><div class="block width50">';
	$fieldset = new ZunoFieldset('Type d\'action','','height: 90px');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'delete', ' onclick="toggleSubGroupedAction(\'groupedSubActionDelete\')"'),'Supprimer ces fichiers','ZTips.pontComptable.groupActionDelete');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttribute', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttribute\')"'),'Changer un attribut par lot','ZTips.pontComptable.groupActionChangeAttribute');
	$out.= $fieldset->generateFieldset();
	$out.= '</div><div class="block width50"><div id="groupedSubAction">';
	$fieldset = new ZunoFieldset('Supprimer ces fichiers','groupedSubActionDelete','height: 30px');
	$fieldset->ligneCheckBox('Supprimer les fichiers', 'deleteFile', true, '', 'ZTips.pontComptable.groupActionDeleteWithFile');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Changer un attribut par lot','groupedSubActionChangeAttribute','height: 30px');
	$fieldset->ligneInput('Nom du pontComptable', 'nom_pcth');
	$out.= $fieldset->generateFieldset();
	$out.= '</div>';
	$fieldset = new ZunoFieldset('Executer le changement');
	$fieldset->ligneLinkTo('','javascript:submitGroupAction(\'resultFormPontComptable\',\'resultPontComptableAction\');" class="bouton','Effectuer les changements','../img/prospec/voir.png');
	$out.= $fieldset->generateFieldset();
	$out.= '</div></div></div>';
	return $out;
    }

    private function ResultGroupActionBouton($suffixeLink) {
	$bouton = '<a name="groupAction" style="cursor:pointer;" onclick="toggleGroupedAction(\'divGroupAction\');"><img title="actions groupées" alt="actions groupées" src="'.$suffixeLink.'img/prospec/work-multiple.png"/> Actions groupées</a>';
	return $bouton;
    }





    /**
     * Méthode appelée pour rechercher des pontComptables.
     * @param array $datas Les données nécessaires
     * @return string Le HTML
     */
    public function searchResult($datas, $light='') {
	if($light == 'result' or $light == 'jsResult')
	    return $this->script.$this->Result($datas,$light);
	else {
	    $popup = '';
	    if($light == 'jsReturn') {
		$light = 'jsResult';
		$popup = 'popup';
	    }
	    elseif($light == '') $light = 'result';
	    $form = $this->searchForm($datas,$light);
	    $rendu = new ZunoRenduHtml('recherchePontComptaHisto');
	    $form->addButton('Créer un nouveau fichier d\'export', 'window.location = \'../facturier/PontComptable.php?action=creer\';', '../img/prospec/pontComptable.png');
	    $rendu->generateZBox('Recherche', 'Recherche', $form->generateForm(), $form->generateButtons('listeResultPontComptable', 'Annuler', 'Rechercher',$popup), 'divSearchPontComptaHisto', 'open');
	    $rendu->addBox($this->listeResult($datas,$light));
	    $rendu->insertJS("<script type=\"text/javascript\">Event.observe(document, 'keypress', function(event){ if(event.keyCode == Event.KEY_RETURN) zuno.business.formTools.sendFormAjah('formSearchPontComptaHisto', 'facturier/PontComptableHisto.php','listeResultPontComptable'); return false;}, false);</script>".$this->script);
	    return $rendu->generateRendu();
	}

    }

    /**
     * Méthode searchForm qui génère le formulaire de recherche d'un pontComptable
     * @param array $datas D'éventuels champs pré-remplis
     * @return form Le formulaire
     */
    public function searchForm($datas,$option = 'result') {
	$form = new ZunoForm('formSearchPontComptaHisto', 'facturier/PontComptableHisto.php', 'POST');
	$fieldset = new ZunoFieldset('Recherche');
	$fieldset->ligneInput('Nom', 'nom_pcth', $datas['form']['nom_pcth'],'','ZTips.pontComptable.searchNom');
	$fieldset->ligneInputDate('Date de début', 'dateStart_pcth', $datas['form']['dateStart_pcth'],"","","",'ZTips.pontComptable.searchDateStart');
	$fieldset->ligneInputDate('Date de fin', 'dateEnd_pcth', $datas['form']['dateEnd_pcth'],"","","",'ZTips.pontComptable.searchDateEnd');
	$form->newBlock($fieldset->generateFieldset(), '50');
	$form->newInputHidden('result', $option);
	$form->newInputHidden("action", "searchPontComptable", "actionPontComptableSearch");
	$form->newInputHidden("limit", 30, "limitPontComptableSearch");
	$form->newInputHidden("from", 0, "fromPontComptableSearch");
	return $form;
    }


    /**
     * Fonction appelée pour voir le formulaire de création d'un pontComptable
     * @param $datas	les données de la base
     * @param $popup	Indique si on est en popup ou pas
     * @return $rendu	Le rendu HTML
     */
    public function searchPopup($datas) {

	return $this->searchResult($datas,'jsReturn');
    }
}

?>
