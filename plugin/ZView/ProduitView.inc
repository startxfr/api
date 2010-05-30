<?php
loadPlugin(array('ZModels/ProduitModel', 'ZunoRenduHTML'));
loadPlugin('ZView/GeneralView');
/*
 * Created on 5 oct. 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
/**
 * Classe qui gère les formulaires produits
 * @author Nicolas Mannocci
 * @version 1
 */
class ProduitViewOriginal extends generalView {

    private $listeFournisseur;
    private $id_prod;
    protected $fieldset1;
    protected $fieldset2;
    protected $fieldset3;
    private $fieldsetF;
    private $form;
    private $formF;
    private $html;
    private $script;
    private $compteurFourn;

    /**
     * Constructeur de la classe
     *
     */
    public function __construct() {
	$this->compteurFourn = 0;
	$this->script = '<script>';
	$this->script .= 'function autoCompletage() {var ent = 1;} ';
	$this->script .= "function ChangeProduitResultPage(limit,from) {
				ChangeAction('searchProduit');
				ChangeLimit(limit);
				ChangeFrom(from);
				zuno.business.formTools.sendFormAjah('formSearchProd','produit/ProduitListe.php','searchListeProduit');
			    }
			    function ChangeFrom(value) { \$('fromProduitSearch').value = value; }
			    function ChangeLimit(value) { \$('limitProduitSearch').value = value; }
			    function ChangeAction(value) { \$('actionProduitSearch').value = value;} ";
	$this->script .= "\n".'function autoCalcul(numero) {px = $(\'px\'+numero).value.replace(/,/,"."); rem = $(\'rem\'+numero).value.replace(/,/,"."); tot=px*(1-rem/100); tot = Math.round(tot*100)/100; $(\'tot\'+numero).value = tot; }';
	$this->script .= "\n".'function autoCompleteHidden(text, li) { $(text.id+\'hidden\').value = li.title;} ';
	$this->script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$this->script .= "\n".'var REM = new Array();';
	$this->script .= "\n".'function synchToSB(){zuno.ajax.get.json(\'../ajaxRef.php\', \'action=synchToSB\', function(xhr){var json = xhr.responseText.evalJSON(); if (json.good == \'true\') {alert(json.message);} else{alert(json.message);} } );}';
	$this->script .= '</script>';
    }

    /**
     * Méthode générant l'affichage de toute la fiche produit
     * @param array $datas Tableau des valeurs sortant de la bdd
     * @return string Le code HTML
     */
    private function vueTotale($datas = array()) {


	$this->initioFieldSet($datas);
	$this->initioFieldSetFourn($datas);
	$this->initioFieldsetExFourn($datas);

	$this->form = new ZunoForm('formProduit', 'produit/Produit.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$this->form->newInputHidden('action', 'modifProduit', 'ToDo');
	$this->form->newInputHidden('idProduit', $datas['data']['id_prod']);

	$this->formF = new ZunoForm('formProduitFournisseur', 'produit/Produit.php', 'POST');
	if(count($this->fieldsetF) > 0)
	    foreach($this->fieldsetF as $v) {
		$this->formF->newBlock($v->generateFieldset());
	    }
	else
	    $this->formF->newBlock('');
	$this->formF->newInputHidden('action', 'modifPF', 'ToDo');
	$this->formF->newInputHidden('idProduit', $datas['data']['id_prod']);
	$this->formF->addButton("Ajouter un fournisseur", "return zuno.popup.open('../produit/Produit.php', 'action=addFournProd&idProd=".$datas['data']['id_prod']."',550,250);", "../img/prospec/add.png");

	$this->html = new ZunoRenduHtml('ficheCompleteProd');
	$this->html->replaceJS($this->script);
	$this->html->generateZBox(imageTag('../img/prospec/produit.png','Produits').' Produit '.$datas['data']['id_prod'], '', $this->form->generateForm(), $this->form->generateButtons('', 'Recommencer', 'Modifier ce produit'),'formProduitModif', 'open');
	$this->html->generateZBox(imageTag('../img/prospec/produit.png','Fournisseurs').' Fournisseurs de '.$datas['data']['id_prod'],'', $this->formF->generateForm(), $this->formF->generateButtons('', 'Annuler', 'Modifier'),'formFournProduit', 'open');

	return $this->html->generateRendu();
    }

    /**
     * Méthode initialisant les filedsets contenus dans le fromulaire produit
     * @param array $datas Données de la Bdd
     */
    protected function initioFieldSet($datas) {
	$this->fieldset1 = new ZunoFieldset('Produit');
	$this->fieldset2 = new ZunoFieldset('Informations complémentaires');
	$this->fieldset3 = new ZunoFieldset('Description');
	$this->fieldset1->ligneInput('Référence', 'id_prod', $datas['data']['id_prod'],'','ZTips.produit.reference');
	$this->fieldset1->ligneInput('Nom', 'nom_prod', $datas['data']['nom_prod'],'','ZTips.produit.nom');
	$this->fieldset1->ligneFree('Famille', $this->selectTagProdFamille('famille_prod', $datas['data']['famille_prod'], $datas['famille'], false, 'id="selectFamilleProd"', true, "zuno.popup.open('../produit/Produit.php', 'action=addFamille', 300,250)"), 'ZTips.produit.famille');
	$this->fieldset3->ligneTArea('Description', 'description_prod',$datas['data']['description_prod'],'','ZTips.produit.desc');
	$this->fieldset1->ligneInput('Prix (€)', 'prix_prod', $datas['data']['prix_prod'],'','ZTips.produit.prix');
	$this->fieldset2->ligneInput('Stock', 'stock_prod', $datas['data']['stock_prod'],'','ZTips.produit.stock');
	$this->fieldset2->ligneSelect('Renouvellement', 'dureeRenouvellement_prod', $datas['data']['dureeRenouvellement_prod'], $datas['dureeR'],true,'','ZTips.produit.renew');
	$this->fieldset2->ligneCheckBox('BestSeller', 'bestsell_prod', $datas['data']['bestsell_prod'],'','ZTips.produit.bestsell');
	$this->fieldset2->ligneCheckBox('Disponible', 'stillAvailable_prod', (array_key_exists('data',$datas) and array_key_exists('stillAvailable_prod',$datas['data'])) ? $datas['data']['stillAvailable_prod'] : true,'','ZTips.produit.stillAvailable');

    }

    /**
     * Méthode qui ne génère que le contenu du formulaire produit
     * @param array $datas Les données de la base de données
     * @return string Le code HTML
     */
    private function interneInfos($datas) {
	$this->initioFieldSet($datas);
	$this->form = new ZunoForm('formProduit', 'produit/Produit.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$out = $this->form->getLastBlock();
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$out .= $this->form->getLastBlock();
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$out .= $this->form->getLastBlock();
	return $out;
    }

    /**
     * Méthode appelée dès que l'on cherche à voir une fiche produit
     * @param array $datas Les données de la Bdd
     * @param string $light Précise ce que l'on veut voir
     * @param string $mess Indique le mesage à afficher le cas échéant
     * @return string Le code HTML
     */
    public function view($datas, $light='', $mess='') {
	$titreProd = (strlen($datas['data']['nom_prod']) > 30) ? substr($datas['data']['nom_prod'],0,30).'...' : $datas['data']['nom_prod'];
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fiche produit "'.$datas['data']['id_prod'].'" - '.$titreProd,'',true);
	if($light == 'interneInfos')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneInfos($datas);
	elseif($light == 'afterCreate')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->afterCreate($datas);
	elseif($light == 'interneFourn')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneFourn($datas);
	else
	    return $this->vueTotale($datas);
    }

    /**
     * Methode appelée pour voir le formulaire de création d'un produit
     * @param array $datas Les données de la base
     * @param bool $popup Indique si on est en popup ou pas
     * @return string Le rendu HTML
     */
    public function creer($datas, $popup = false) {
	$this->initioFieldSet($datas);
	$this->form = new ZunoForm('formProduit', 'produit/Produit.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newBlock($this->fieldset3->generateFieldset());
	if(!$popup) {
	    $this->form->newInputHidden('action', 'addProduit', 'ToDo');
	    $pied = $this->form->generateButtons('ficheCompleteProd', 'Annuler', 'Créer le produit');
	}
	else {
	    $this->form->newInputHidden('action', 'addProdPopup');
	    $pied = '<a href="javascript:document.formProduit.reset();" title="Reset"><img align="middle" src="../img/prospec/cancel.png" alt="Effacer" title="Effacer" onload="autoCompletage();"/> Annuler</a>';
	    $pied .= '<a style="cursor: pointer;" onclick="saveProd();" name="submit"><img align="middle" src="../img/prospec/record.png" alt="Enregistrer" title="Enregistrer"/> Créer le produit</a>';
	}

	$this->form->newInputHidden('idProduit', $datas['data']['id_prod']);

	$this->html = new ZunoRenduHtml('ficheCompleteProd');
	$this->html->replaceJS($this->script);
	$this->html->generateZBox(imageTag('../img/prospec/produit.png','Produits').' Nouveau Produit', '', $this->form->generateForm(), $pied,'creationProdPopup' , 'open');

	return $this->html->generateRendu();
    }

    /**
     * Méthode qui génère l'intérieur de la page sans le javascript appelée après une création
     * @param array $datas Les données de la base
     * @return string Le rendu HTML
     */
    private function afterCreate($datas) {
	$this->initioFieldSet($datas);
	$this->initioFieldSetFourn($datas);

	$this->form = new ZunoForm('formProduit', 'produit/Produit.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newBlock($this->fieldset3->generateFieldset());
	$this->form->newInputHidden('action', 'modifProduit', 'ToDo');
	$this->form->newInputHidden('idProduit', $datas['data']['id_prod']);

	$this->formF = new ZunoForm('formProduitFournisseur', 'produit/Produit.php', 'POST');
	if(count($this->fieldsetF) > 0)
	    foreach($this->fieldsetF as $v) {
		$this->formF->newBlock($v->generateFieldset());
	    }
	else
	    $this->formF->newBlock('');
	$this->formF->newInputHidden('action', 'modifPF', 'ToDo');
	$this->formF->newInputHidden('idProduit', $datas['data']['id_prod']);
	$this->formF->addButton("Ajouter un fournisseur", "return zuno.popup.open('../produit/Produit.php', 'action=addFournProd&idProd=".$datas['data']['id_prod']."',550,250);", "../img/prospec/add.png");

	$this->html = new ZunoRenduHtml();
	$this->html->generateZBox(imageTag('../img/prospec/produit.png','Produits').' Produit '.$datas['data']['id_prod'], '', $this->form->generateForm(), $this->form->generateButtons('', 'Recommencer', 'Modifier ce produit'),'formModifProd', 'open');
	$this->html->generateZBox(imageTag('../img/prospec/produit.png','Fournisseurs').' Fournisseurs du produit','', $this->formF->generateForm(), $this->formF->generateButtons('', 'Annuler', 'Modifier'),'formFournProduit', 'open');

	return $this->html->generateInterneRendu();
    }

    /**
     * Méthode qui initialise les fieldset de la box des fournisseurs
     * @param array $datas Les données de la base
     */
    private function initioFieldSetFourn($datas) {
	if($datas['fourn'][0] and count($datas['fourn'][1]) > 0) {
	    foreach($datas['fourn'][1] as $v) {
		$pxrevient = $v['prixF']*(1-$v['remiseF']/100);
		$pxrevient = prepareNombreAffichage($pxrevient);
		$fieldset = new ZunoFieldset($v['id_fourn'].' - '.$v['nom_ent']);
		$fieldset->ligneLinkTo("Entreprise", "../prospec/fiche.php?id_ent=".$v['id_ent'], $v['nom_ent'],'','');
		$fieldset->ligneInput("Px public (€)", 'prixF['.$this->compteurFourn.']', prepareNombreAffichage($v['prixF']), ' onchange="autoCalcul('.$this->compteurFourn.');" id="px'.$this->compteurFourn.'" ','ZTips.produit.fournPxPub');
		$fieldset->ligneInput("Remise (%)", 'remiseF['.$this->compteurFourn.']', prepareNombreAffichage($v['remiseF']), ' onchange="autoCalcul('.$this->compteurFourn.');" id="rem'.$this->compteurFourn.'" ','ZTips.produit.fournRemise');
		$fieldset->ligneInput("Px achat (€)", 'pxRevient', $pxrevient, 'disabled="disabled" id="tot'.$this->compteurFourn.'"','ZTips.produit.fournPxAchat');
		$fieldset->ligneCheckBox("Désactiver", "actif[".$this->compteurFourn."]",false,'','ZTips.produit.fournDisable');
		$fieldset->ligneInputHidden('idFourn['.$this->compteurFourn.']', $v['id_fourn']);
		$this->fieldsetF[] = $fieldset;
		$this->compteurFourn++;
	    }
	}
    }

    /**
     * Méthode qui initialise le fieldset des fournisseurs à réactiver
     * @param array $datas Les données à afficher
     */
    private function initioFieldsetExFourn($datas) {
	if($datas['fournEx'][0] and count($datas['fournEx'][1]) >0) {
	    foreach($datas['fournEx'][1] as $v) {
		$fieldset = new ZunoFieldset($v['id_fourn']);
		$fieldset->ligneLinkTo("Entreprise", "../prospec/fiche.php?id_ent=".$v['id_ent'], $v['nom_ent']);
		$fieldset->ligneCheckBox("Réactiver", "ractif[".$this->compteurFourn."]",false,'','ZTips.produit.fournEnable');
		$fieldset->ligneCheckBox("Supprimer", "supp[".$this->compteurFourn."]",false,'','ZTips.produit.fournDelete');
		$fieldset->ligneInputHidden('idFourn['.$this->compteurFourn.']', $v['id_fourn']);
		$this->fieldsetF[] = $fieldset;
		$this->compteurFourn++;
	    }
	}
    }

    /**
     * Méthode interneFourn retoure l'intérieur du formulaire des fournisseurs
     * @param array $datas Les données de la base
     * @return string Le HTML généré
     */
    private function interneFourn($datas) {
	$this->initioFieldSetFourn($datas);
	$this->initioFieldsetExFourn($datas);
	$out = '';
	$form = new ZunoForm('bidon','test.php','POST');
	if(count($this->fieldsetF) > 0)
	    foreach($this->fieldsetF as $v) {
		$form->newBlock($v->generateFieldset());
		$out .= $form->getLastBlock();
	    }

	return $out;
    }

    /**
     * Méthode popupProdFourn génère l'affichage de la popup d'ajout d'un fournisseur à un produit
     * @param array $datas Les données de la base
     * @return string Le HTML généré
     */

    public function popupProdFourn($datas) {

	if($datas['data']['remiseF'] == '' and is_array($datas['remise']))
	    $datas['data']['remiseF'] = current($datas['remise']);
	$fieldset = new ZunoFieldset('Ajout Fournisseur');
	if(count( $datas['fourn']) > 0)
	     $fieldset->ligneSelect('Fournisseur', 'fournisseur_id', $datas['data']['fournisseur_id'], $datas['fourn'],false,'onchange="$(\'rem5597\').value = REM[this.value]; autoCalcul(\'5597\');"','ZTips.produit.fournEntreprise');
	else $fieldset->ligneFree('Fournisseur', '<i>Aucun fournisseur disponible</i>','ZTips.produit.fournEntreprise');
	$fieldset->ligneInput("Px public (€)", 'prixF', $datas['data']['prixF'], 'id="px5597" onchange="autoCalcul(\'5597\');" ','ZTips.produit.fournPxPub');
	$fieldset->ligneInput("Remise (%)", 'remiseF', $datas['data']['remiseF'], 'id="rem5597" onchange="autoCalcul(\'5597\');" ','ZTips.produit.fournRemise');
	$fieldset->ligneInput("Px achat (€)", 'pxRevient', '0', 'disabled="disabled", id="tot5597"','ZTips.produit.fournPxAchat');

	$this->form = new ZunoForm('formProduitFournAdd', 'produit/Produit.php', 'POST');
	$this->form->newBlock($fieldset->generateFieldset(), '50');
	$this->form->newInputHidden('action', 'addProduitFourn', 'ToDo');
	$this->form->newInputHidden('idProduit', $datas['data']['id_prod']);

	$this->html = new ZunoRenduHtml('popupFournProd');
	$this->html->generateZBox(imageTag('../img/prospec/TypeEntreprise/5.png','Forunisseurs').' Ajouter Fournisseur','', $this->form->generateForm(), $this->form->generateButtons('idformProduitFournisseurformAutoRetour', 'Annuler', 'Enregistrer', 'popup2'),'popupProdFourn', 'open');

	if(is_array($datas['remise'])) {
	    $script = '';
	    foreach($datas['remise'] as $k => $v)
		$script .= 'REM[\''.$k.'\'] = \''.$v.'\'; '."\n";
	    $this->html->insertJS('<script>'.$script.'</script>');
	}
	return $this->html->generateRendu();
    }

    /**
     * Méthode qui affiche la liste des résultats d'une recherche de produits
     * @param array $datas Les données à afficher
     * @return string Le code HTML
     */
    public function listeResult($datas,$option = null) {
	return '<div id=listeResultProduit>'.$this->Result($datas,$option).'</div>';
    }

    /**
     * Méthode qui génère le code HTML du tableau de résultats d'une recherche
     * @param array $datas Les données à afficher
     * @return string Le code HTML généré
     */
    private function Result($datas,$option = null) {
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit'])
	    $suffixeLink = './';
	else $suffixeLink = '../';

	$out .= '<div class="blockTable">
		    <form id="resultFormProduit" method="post" action="ProduitListe.php" name="resultProduit">
		    <input type="hidden" name="action" value="" id="resultProduitAction" />
		    <input type="hidden" name="exportType" value="" id="resultProduitExportType" />
		    <table cellspacing="0"><tbody>';
	$out .= '<tr class="titre">
			<th class="barre"><img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormProduit\');"/></th>
			<th class="bg barre">Ref</th>
			<th class="center barre">Nom</th>
			<th class="center barre">Famille</th>
			<th>Description</th>
			<th class="center">Prix</th>
			<th class="last center">Stock</th>
			<th class="last center">Best</th>
			<th class="last center">Etat</th>
		</tr>';
	$alternance =  0;
	foreach($datas['data'] as $result) {
	    $action = '';
	    $link = $suffixeLink."produit/Produit.php?id_prod=".$result['id_prod'];
	    if($option == 'jsResult')
		$onclick = 'return searchProdReturn(\''.str_replace("\n", ' ',$result['id_prod']).'\',\''.str_replace("\n", ' ',$result['nom_prod'].' - '.$result['description_prod']).'\',\''.$result['prix_prod'].'\')';
	    else $onclick = 'window.location=\''.$link.'\'';
	    if($result['bestsell_prod'] == '1')
		$imgBest = imageTag($suffixeLink.'img/best.png','Meilleur ventes');
	    else $imgBest = '';
	    if($result['stillAvailable_prod'] == '1')
		$imgActif = imageTag($suffixeLink.'img/valid.png','Produit en vente');
	    else $imgActif = imageTag($suffixeLink.'img/unvalid.png','Produit désactivé');
	    $out .= '<tr class="altern'.($alternance % 2).'">';
	    $out .= '<td class="barre" style="width:10px"><input type="checkbox" name="select[]" value="'.$result['id_prod'].'" checked="checked"/></td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['id_prod'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['nom_prod'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['treePathKey'].' '.$result['nom_prodfam'].'</td>';
	    $out .= '<td class="barre" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['description_prod'].'</td>';
	    $out .= '<td class="barre right" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['prix_prod'].' &euro;</td>';
	    $out .= '<td class="right" style="cursor : pointer;" onclick="'.$onclick.'">'.$result['stock_prod'].'</td>';
	    $out .= '<td class="right" style="cursor : pointer;" onclick="'.$onclick.'">'.$imgBest.'</td>';
	    $out .= '<td class="right" style="cursor : pointer;" onclick="'.$onclick.'">'.$imgActif.'</td>';
	    $out .= '</tr>';
	    $alternance++;
	}

	$out .= '</tbody></table>';
	$out .= $this->ResultGroupActionForm();
	$out .= '</form></div>';
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit'])
	    $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'produitHisto');
	else $titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'produit');
	if (array_key_exists('isHistoVisit',$datas) and $datas['isHistoVisit'])
	    $bouton = '';
	else
	    $bouton = $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeProduitResultPage",$suffixeLink);
	$bouton .= $this->ResultGroupActionBouton($suffixeLink);
	$bouton .= '<a name="synchroProduit" onclick="synchToSB();"><img alt="produit" src="../img/prospec/produit.png"/> Synchroniser les produits sur Superbase</a>';

	return generateZBox($titre,$titre,$out,$bouton,"searchListeProduit",'');
    }




    private function ResultGroupActionForm() {
	$sql = new produitModel();
	$out = '<div id="divGroupAction" style="display: none"><div class="block width50">';
	$fieldset = new ZunoFieldset('Type d\'action');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'delete', ' onclick="toggleSubGroupedAction(\'groupedSubActionDelete\')"'),'Marquer ces produits comme supprimés','ZTips.produit.groupActionDelete');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'activate', ' onclick="toggleSubGroupedAction(\'groupedSubActionActivate\')"'),'Activation des produits','ZTips.produit.groupActionActivate');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'desactivate', ' onclick="toggleSubGroupedAction(\'groupedSubActionDesactivate\')"'),'Desactivation des produits','ZTips.produit.groupActionDesactivate');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'fournisseur', ' onclick="toggleSubGroupedAction(\'groupedSubActionFournisseur\')"'),'Modification des fournisseurs','ZTips.produit.groupActionFournisseur');
	$fieldset->ligneFree(inputTag('radio', 'groupedAction', '', '', '', 'changeAttribute', ' onclick="toggleSubGroupedAction(\'groupedSubActionChangeAttribute\')"'),'Changer un attribut par lot','ZTips.produit.groupActionChangeAttribute');
	$out.= $fieldset->generateFieldset();
	$out.= '</div><div class="block width50" id="groupedSubAction">';
	$fieldset = new ZunoFieldset('Activer ces produits','groupedSubActionActivate');
	$fieldset->addOtherLigne('<div class="row"><i>Aucune action sécifique n\'est requise</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Désactiver ces produits','groupedSubActionDesactivate');
	$fieldset->addOtherLigne('<div class="row"><i>Aucune action sécifique n\'est requise</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Marquer ces produits comme supprimés','groupedSubActionDelete');
	$fieldset->addOtherLigne('<div class="row"><i>Seul les produits qui ne sont présent dans aucun document seront supprimés. Les autres produits seront conservés tant qu\'ils seront présent dans un document</i></div>');
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Modification des fournisseurs','groupedSubActionFournisseur');
	$fieldset->ligneSelect('Fournisseur', 'fournisseur','',$sql->getAllFournisseurs(), false);
	$fieldset->ligneInput('Tx de remise', 'remiseF');
	$fieldset->ligneInput('Prix public', 'prixF');
	$fieldset->ligneRadio('Actif', 'actifF', ' ', array(array('label'=>'inchangé','value'=>' '),array('label'=>'non','value'=>'0'),array('label'=>'oui','value'=>'1')));
	$out.= $fieldset->generateFieldset();
	$fieldset = new ZunoFieldset('Changer un attribut par lot','groupedSubActionChangeAttribute');
	$fieldset->ligneInput('Nom du produit', 'nom_prod');
	$fieldset->ligneFree('Famille', $this->selectTagProdFamille('famille_prod', '', $sql->getAllFamille(), true));
	$fieldset->ligneInput('Déscription du produit', 'description_prod');
	$fieldset->ligneSelect('Renouvellement', 'dureeRenouvellement_prod','',$sql->getRenews());
	$fieldset->ligneInput('Prix public', 'prix_prod');
	$fieldset->ligneInput('Quantité en stock', 'stock_prod');
	$fieldset->ligneInput('Remise fournisseur', 'remisefournisseur_prod');
	$fieldset->ligneRadio('Meilleur vente', 'bestsell_prod', ' ', array(array('label'=>'inchangé','value'=>' '),array('label'=>'non','value'=>'0'),array('label'=>'oui','value'=>'1')));
	$out.= $fieldset->generateFieldset();
	$out.= '</div>';
	$out.= '<div class="block width50">';
	$fieldset = new ZunoFieldset('Executer le changement');
	$fieldset->ligneLinkTo('','javascript:submitGroupAction(\'resultFormProduit\',\'resultProduitAction\');" class="bouton','Effectuer les changements','../img/prospec/voir.png');
	$out.= $fieldset->generateFieldset();
	$out.= '</div></div>';
	return $out;
    }

    private function ResultGroupActionBouton($suffixeLink) {
	$bouton = '<a name="groupAction" style="cursor:pointer;" onclick="toggleGroupedAction(\'divGroupAction\');"><img title="actions groupées" alt="actions groupées" src="'.$suffixeLink.'img/prospec/work-multiple.png"/> Actions groupées</a>';
	return $bouton;
    }





    /**
     * Méthode appelée pour rechercher des produits.
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
	    $rendu = new ZunoRenduHtml('rechercheProd');
	    $rendu->generateZBox('Recherche', 'Recherche', $form->generateForm(), $form->generateButtons('listeResultProduit', 'Annuler', 'Rechercher',$popup), 'divSerachProd', 'open');
	    $rendu->addBox($this->listeResult($datas,$light));
	    $rendu->insertJS('<script>function autoCompletage() { var ent = 1;} </script>'.$this->script);
	    return $rendu->generateRendu();
	}

    }

    /**
     * Méthode searchForm qui génère le formulaire de recherche d'un produit
     * @param array $datas D'éventuels champs pré-remplis
     * @return form Le formulaire
     */
    public function searchForm($datas,$option = 'result') {
	$form = new ZunoForm('formSearchProd', 'produit/ProduitListe.php', 'POST');
	$fieldset = new ZunoFieldset('Recherche');
	$fieldset->ligneInput('Référence', 'id_prod', $datas['form']['id_prod'],'','ZTips.produit.searchReference');
	$fieldset->ligneInput('Nom', 'nom_prod', $datas['form']['nom_prod'],'','ZTips.produit.searchNom');
	$fieldset->ligneFree('Famille', $this->selectTagProdFamille('famille_prod', $datas['form']['famille_prod'], $datas['famille'], true), 'ZTips.produit.searchFamille');
	$fieldset->ligneSelect('Fournisseur', 'fournisseur_prod', $datas['form']['fournisseur_prod'], $datas['fournisseur'], true,'','ZTips.produit.searchFournisseur');
	$fieldset->ligneCheckBox('Disponible', 'stillAvailable_prod', true,'','ZTips.produit.searchDisponible');
	$fieldset->ligneInput('Prix minimal', 'pxmin_prod', $datas['pxmin_prod'],'','ZTips.produit.searchPrixMin');
	$fieldset->ligneInput('Prix maximal', 'pxmax_prod', $datas['pxmax_prod'],'','ZTips.produit.searchPrixMax');
	$form->newBlock($fieldset->generateFieldset(), '50');
	$form->newInputHidden('result', $option);
	$form->newInputHidden("action", "searchProduit", "actionProduitSearch");
	$form->newInputHidden("limit", 30, "limitProduitSearch");
	$form->newInputHidden("from", 0, "fromProduitSearch");

	return $form;
    }


    /**
     * Fonction appelée pour voir le formulaire de création d'un produit
     * @param $datas	les données de la base
     * @param $popup	Indique si on est en popup ou pas
     * @return $rendu	Le rendu HTML
     */
    public function searchPopup($datas) {

	return $this->searchResult($datas,'jsReturn');
    }

    /**
     * Méthode qui génère le code HTML de la popup de création des familles de produits
     * @param string $erreur Précise un éventuell message d'erreur
     * @return string Le code HTML
     */
    public function popupAddFamille($erreur = '') {
	$fieldset = new ZunoFieldset("Informations");
	$fieldset->ligneInput('Nom', 'nom_prodfam');

	$form = new ZunoForm('addFamille', "produit/Produit.php", "POST");
	$form->newInputHidden('action', 'addFamille');
	$form->newBlock($fieldset->generateFieldset(), '50');

	$rendu = new ZunoRenduHtml();
	$rendu->generateZBox('Nouvelle Famille', 'Nouvelle Fammille', $form->generateForm(), $form->generateButtons('', 'Annuler', 'Créer', 'popup'), 'idPopupAddFamille');
	return $rendu->generateInterneRendu();
    }



    /**
     * Méthode selectTagProdFamille qui génère une ligne avec un select pour les familles de produit
     * @param string $nom Le nom du champ
     * @param string $value La valeur initiale du champ
     * @param array $liste La liste des options du champ
     * @param bool $withBlank Précise si une ligne vide doit être dans le champ
     * @param string $autre D'autres tags si nécessaires
     * @param bool $plus Permet de préciser si on veut le petit plus
     * @param string $actionplus Précise l'action à faire après click sur le plus
     */
    public function selectTagProdFamille($nom, $value, $liste = array(), $withBlank = true, $autre = '', $plus = false, $actionplus = '') {
	$indenteur = '&nbsp;&nbsp;&nbsp;&nbsp;';
	// generate le select
	if ($nom != '') {
	    if(!is_array($value))
		$value = array($value=>$value);
	    $chaine_class =  '';
	    if($plus) {
		$class .= "icon ";
		$img = '<a id="Se'.$nom.'AddImg" name="Se'.$nom.'AddImg" title="Créer" onclick="'.$actionplus.'"><img alt="famille" title="Créer" src="../img/prospec/add.png" /></a>';
	    }
	    else $class = $img = "";
	    $chaine = "<select ";
	    // Select  field name
	    if ($nom != '')
		$chaine .=  'name=\''.$nom.'\' ';
	    // Select  CSS class
	    if ($class != '') {
		$chaine .=  'class=\''.$class.'\' ';
		$chaine_class =  ' class=\''.$class.'\' ';
	    }
	    // Ad other element into the select tag
	    if ($autre != '')
		$chaine .=  $autre." ";
	    $chaine .=  ">";
	    // Select  detect select row
	    if ($withBlank) {
		if ($value == '')
		    $chaine .=  "<option".$chaine_class." value='' selected='selected'> </option>";
		else $chaine .=  "<option".$chaine_class." value=''> </option>";
	    }
	    // Loop for values
	    if(is_array($liste)) {
		$sortedOther = $sorted = array();
		foreach( $liste as $key => $val ) {
		    $d = explode(' ', $val, 2);
		    if ($d[0] != '' and $d[1] != '' ) {
			if(strlen($d[0]) == 1)
			    $sorted[substr($d[0],0,1)] = array('nom'=>$d[1],'id'=>$key);
			elseif(strlen($d[0]) == 2)
			    $sorted[substr($d[0],0,1)]['d'][substr($d[0],0,2)] = array('nom'=>$d[1],'id'=>$key);
			elseif(strlen($d[0]) == 3)
			    $sorted[substr($d[0],0,1)]['d'][substr($d[0],0,2)]['d'][substr($d[0],0,3)] = array('nom'=>$d[1],'id'=>$key);
		    }
		    else $sortedOther[] = array('nom'=>$val,'id'=>$key);
		}
		if(count($sorted)) {
		    foreach( $sorted as $k1 => $val1 ) {
			$chaine .= "<option value='".$val1['id']."' style='font-style: italic;font-weight: bold'";
			if ((is_array($value) and (in_array($val1['id'], $value))) or (($value == $val1['id'])and($value != "")))
			    $chaine .= ' selected="selected"';
			$chaine .= "> ".$val1['nom']."</option>";
			if(array_key_exists('d',$val1)) {
			    foreach( $val1['d'] as $k2 => $val2 ) {
				$chaine .= "<option value='".$val2['id']."' style='font-style: italic'";
				if ((is_array($value) and (in_array($val2['id'], $value))) or (($value == $val2['id'])and($value != "")))
				    $chaine .= ' selected="selected"';
				$chaine .= ">".$indenteur." ".$val2['nom']."</option>";
				if(array_key_exists('d',$val2)) {
				    foreach( $val2['d'] as $k3 => $val3 ) {
					$chaine .= "<option value='".$val3['id']."'";
					if ((is_array($value) and (in_array($val3['id'], $value))) or (($value == $val3['id'])and($value != "")))
					    $chaine .= ' selected="selected"';
					$chaine .= ">".$indenteur.$indenteur." ".$val3['nom']."</option>";
				    }
				}
			    }
			}
		    }
		}
		if(count($sortedOther)) {
		    foreach( $sortedOther as $key => $val ) {
			$chaine .= "<option value='".$val['id']."'";
			if ((is_array($value) and (in_array($val['id'], $value))) or (($value == $val['id'])and($value != "")))
			    $chaine .= ' selected="selected"';
			$chaine .= ">".$val['nom']."</option>";
		    }
		}
	    }
	    $chaine .= "</select>".$img;
	}
	else {
	    Logg::error('HTML::AUTO_TAG_GENERATOR::SELECT_TAG::no_tag_name',FALSE);
	    $chaine = 'erreur select';
	}

	return $chaine;
    }
}
loadPlugin('ZunoSxa');


function BureauMyProduit() {
    $limit = 10;
    $req  = new produitModel();
    $total = $req->getDataForHistoriqueVisit($limit,$type = 'COUNT');
    $result = $req->getDataForHistoriqueVisit($limit);
    $datas['total'] = $total[1][0]['counter'];
    $datas['data'] = $result[1];
    $datas['from'] = 0;
    $datas['limit'] = $limit;
    $datas['isHistoVisit'] = true;
    $view = new produitView();
    if($datas['total'] > 0)
	return $view->listeResult($datas);
    else return "";
}

?>
