<?php
loadPlugin(array('ZModels/ProduitModel', 'ZunoRenduHTML'));
loadPlugin('ZView/GeneralView');
/*
 * Created on 14 oct. 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/
/**
 * Classe qui gère les vues des fournisseurs.
 * @author Nicolas Mannocci
 * @version 0.1
 */
class FournisseurView extends generalView {
    private $resultats;
    private $listeProd;
    private $script;
    private $fieldset1;
    private $fieldset2;
    private $fieldset3;
    private $form;
    private $form2;
    private $html;

    /**
     * Constructeur de la classe
     */
    public function __construct() {
	$this->resultats = new ZunoTableau();
	$this->listeProd = new ZunoTableau();
	$this->script = '<script type="text/javascript">';
	$this->script .= 'function autoCompletage() {var ent = 1;} ';
	$this->script .= "function ChangeFournisseurResultPage(limit,from) {
					ChangeAction('searchFournisseur');
					ChangeLimit(limit);
					ChangeFrom(from);
					zuno.business.formTools.sendFormAjah('formSearchFourn','produit/FournisseurListe.php','searchListeFournisseur');
				    }
				    function ChangeFrom(value) { \$('fromFournisseurSearch').value = value; }
				    function ChangeLimit(value) { \$('limitFournisseurSearch').value = value; }
				    function ChangeAction(value) { \$('actionFournisseurSearch').value = value;} ";
	$this->script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$this->script .= "\n".'function submitPopupFournProd() {$(\'InputHiddenPF\').value = $(\'InputPF\').value; $(\'InputHiddenRF\').value = $(\'InputRF\').value; $(\'InputHiddenAction\').value = \'changeDatasProdFourn\'; zuno.business.formTools.sendFormAjah(\'formProdFourn\', \'produit/Fournisseur.php\', \'idformProdFournformAutoRetour\', \'popup2\');}';
	$this->script .= '</script>';
    }


    /**
     * Fonction result génère le tableau des résultats de la recherche.
     * @param array $datas	Les données à afficher
     * @return string $titre	Le titre à placer pour la checkbox
     */
    private function result($datas) {
	$this->resultats->autoHrefLigneID("Fournisseur.php", "id_fourn");
	$titre['Code']='';
	$titre['Nom'] = 'big';
	$titre['CP- Ville']='';
	$titre['Contact commercial']='';
	$titre['Contact ADV'] = '';
	$titre['Contact facturation']='';
	$this->resultats->ligneTitre($titre);
	foreach($datas['data'] as $v) {
	    $ligne[$v['id_fourn']] = '+ center';
	    $ligne[$v['nom_ent']] = '';
	    $ligne[$v['cp_ent'].' - '.$v['ville_ent']] = '';
	    $ligne['okou1'.$v['civ_cont1'].' '.$v['prenom_cont1'].' '.$v['nom_cont1']] = '';
	    $ligne['okou2'.$v['civ_cont2'].' '.$v['prenom_cont2'].' '.$v['nom_cont2']] = '';
	    $ligne['okou3'.$v['civ_cont3'].' '.$v['prenom_cont3'].' '.$v['nom_cont3']] = '';
	    $this->resultats->ligneTableau($ligne, $v['id_fourn']);
	    $ligne = null;
	}
	$titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'fournisseur');
	return $titre;
    }

    /**
     * Fonction searchForm qui génère le formulaire pour la recherche
     * @param array $datas	Les données nécessaires.
     * @return formulaire	Le formulaire généré.
     */
    private function searchForm($datas) {
	$form = new ZunoForm('formSearchFourn', 'produit/FournisseurListe.php', 'POST');
	$fieldset = new ZunoFieldset('Recherche');
	$fieldset->ligneInput('Référence', 'id_fourn', $datas['data']['id_fourn'],'','ZTips.fournisseur.searchReference');
	$fieldset->ligneInput('Nom', 'nom_ent', $datas['data']['nom_ent'],'','ZTips.fournisseur.searchNom');
	$fieldset->ligneInput('Code Postal', 'cp_ent', $datas['cp_ent'],'','ZTips.fournisseur.searchCP');
	$fieldset->ligneInput('Contact', 'nom_cont', $datas['nom_cont'],'','ZTips.fournisseur.searchContact');
	$fieldset->ligneCheckBox('Actif', 'actif', true,'','ZTips.fournisseur.searchActif');
	$fieldset->ligneSelect('Ordre', 'ordre_fourn', $datas['data']['ordre_fourn'], $datas['ordre'], false);
	$form->newBlock($fieldset->generateFieldset(), '50');
	$form->newInputHidden("action", "searchFournisseur", "actionFournisseurSearch");
	$form->newInputHidden("limit", 30, "limitFournisseurSearch");
	$form->newInputHidden("from", 0, "fromFournisseurSearch");

	return $form;
    }

    /**
     * Fonction searchResult appelée pour donner le formulaire de recherche et/ou les résultats
     * @param array $datas	Les données pour la recherche
     * @param string $light	Indique si on ne souhaite que les résultats ou la page complete
     */
    public function searchResult($datas, $light='') {
	if($light == 'result') {
	    $rendu = new ZunoRenduHtml('rechercheFourn');
	    $titreR = $this->result($datas);
	    $bouton = $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeFournisseurResultPage",'../');
	    $rendu->insertJS($this->script);
	    $rendu->generateZBox($titreR, $titreR, $this->resultats->generateTableau(), $bouton, 'searchListeFournisseur', 'open');
	    return $rendu->generateInterneRendu();
	}
	else {
	    $form = $this->searchForm($datas);
	    $titreF = imageTag('../img/page.menu/fournisseur.png','recherche').' Recherche de fournisseur';
	    $titreR = $this->result($datas);
	    $rendu = new ZunoRenduHtml('rechercheFourn');
	    $bouton = $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeFournisseurResultPage",'../');
	    $rendu->generateZBox($titreF,$titreF, $form->generateForm(), $form->generateButtons('listeResultFournisseur', 'Annuler', 'Rechercher'), 'divSerachFourn', 'open');
	    $rendu->generateZBox($titreR, $titreR, $this->resultats->generateTableau(), $bouton, 'searchListeFournisseur', 'open', true,'listeResultFournisseur');
	    $rendu->insertJS("<script>function autoCompletage() { var ent = 1;} </script><script type=\"text/javascript\">Event.observe(document, 'keypress', function(event){ if(event.keyCode == Event.KEY_RETURN) zuno.business.formTools.sendFormAjah('formSearchFourn', 'produit/FournisseurListe.php','listeResultFournisseur'); return false;}, false);</script>".$this->script);
	    return $rendu->generateRendu();
	}
    }
    /**
     * Fonction de visualisation d'une fiche fournisseur
     * @param array $datas	Les données à afficher
     * @param string $light	Précise ce qui doit être affiché (optionnel)
     * @param string $mess	Le message à indiquer à l'utilisateur le cas échéant
     * @return string		Le rendu HTML
     */
    public function view($datas, $light='', $mess='') {
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fiche fournisseur "'.$datas['data']['id_fourn'].'" - '.$datas['data']['nom_ent'],'',true);
	if($light == 'interneInfos')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->interneInfos($datas);
	elseif($light == 'afterCreate')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->vueTotale($datas);
	elseif($light == 'afterChangeProd')
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$this->vueTableau($datas);
	else
	    return $this->vueTotale($datas);
    }

    /**
     * Fonction qui génère toutes les parties de la fiche fournisseur.
     * @param array $datas	Les données à afficher
     * @return string		Le code HTML de la fiche
     */
    private function vueTotale($datas) {
	$this->initioFieldset($datas);
	$this->listeProduitsLies($datas);
	$this->form = new ZunoForm('formFourn', 'produit/Fournisseur.php', 'POST');
	$this->form2 = new ZunoForm('formProdFourn', 'produit/Fournisseur.php', 'POST','resultFormProduit');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newInputHidden('action', 'modifFournisseur', 'ToDo');
	$this->form->newInputHidden('idFournisseur', $datas['data']['id_fourn']);
	$this->form2->newInputHidden('idFournisseur', $datas['data']['id_fourn']);
	$this->form2->newInputHidden('action', 'changeDatas', "InputHiddenAction");
	$this->form2->newInputHidden('newPF', "", "InputHiddenPF");
	$this->form2->newInputHidden('newRF', "", "InputHiddenRF");

	$pied2 = '<a name="submit" onclick="zuno.popup.open(\'Fournisseur.php\', \'action=changeDatas\',480,150);" style="cursor: pointer;"><img alt="modifProduits" title="Modifier les produits sélectionnés" src="../img/prospec/produit.png" /> Modifier les produits sélectionnés</a>';

	$this->html = new ZunoRenduHtml('ficheCompleteFourn');
	$this->html->replaceJS($this->script);
	$this->html->generateZBox(imageTag('../img/prospec/produit.png','Fournisseur').' Fournisseur '.$datas['data']['id_fourn'], '', $this->form->generateForm(), $this->form->generateButtons('', 'Recommencer', 'Modifier ce fournisseur'),'formFournisseurModif', 'open');
	$this->form2->newTableauBlock($this->listeProd->generateTableau());
	$this->html->generateZBox('Liste des produits', 'Liste des produits', $this->form2->generateForm(), $pied2, 'ListeFournisseurProduit', 'open', true);

	return $this->html->generateRendu();
    }

    /**
     * Fonction qui initialise les fieldset de la fiche fournisseur
     * @param array $datas	Les données avec lesquelles initialiser les fieldset
     */
    private function initioFieldset($datas) {
	$this->fieldset1 = new ZunoFieldset('Fournisseur','','height:100px');
	$this->fieldset2 = new ZunoFieldset('Contacts','','height:100px');
	$this->fieldset1->ligneLinkTo('Entreprise', '../prospec/fiche.php?id_ent='.$datas['data']['entreprise_fourn'], $datas['data']['nom_ent']);
	$this->fieldset1->ligneCheckBox('Actif', 'actif', true,'','ZTips.fournisseur.actif');
	$this->fieldset1->ligneLinkTo('Produits liés', 'ProduitListe.php?fourn='.$datas['data']['id_fourn'].'', $datas['produits'].' produit(s)');
	$this->fieldset1->ligneInput('Remise', 'remise_fourn', prepareNombreAffichage($datas['data']['remise_fourn']), '', 'ZTips.fournisseur.remise');
	$this->fieldset2->ligneSelect('Commercial', 'contactComm_fourn', $datas['data']['contactComm_fourn'], $datas['contacts'], false,'','ZTips.fournisseur.commercial');
	$this->fieldset2->ligneSelect('ADV', 'ContactADV_fourn', $datas['data']['ContactADV_fourn'], $datas['contacts'],true,'','ZTips.fournisseur.contactAdv');
	$this->fieldset2->ligneSelect('Facturation', 'contactFact_fourn', $datas['data']['contactFact_fourn'], $datas['contacts'], false,'','ZTips.fournisseur.contactFacturation');
    }

    /**
     * Fonction qui charge le contenu du formulaire pour le resortir.
     * @param array $datas	Les données à afficher
     * @return string 		Le code HTML généré
     */
    private function interneInfos($datas) {
	$this->initioFieldset($datas);
	$this->form = new ZunoForm('formFourn', 'produit/Fournisseur.php', 'POST');
	$this->form->newInputHidden('action', 'modifFourn', 'ToDo');
	$this->form->newInputHidden('idFournisseur', $datas['data']['id_fourn']);
	$out = $this->form->getInputs();
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$out .= $this->form->getLastBlock();
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$out .= $this->form->getLastBlock();
	return $out;
    }

    public function creer($datas, $light = '', $mess = '') {
	$this->script = '<script type="text/javascript">';
	$this->script .= "\n".' function whatToDo(value) { if(value != "") {autoContact(value);}
                else {document.formAddFournisseur.contactComm_fourn.options.length = 0;
                        document.formAddFournisseur.ContactADV_fourn.options.length = 0;
                        document.formAddFournisseur.contactFact_fourn.options.length = 0;} } ';
	$this->script .= ' function autoContact(value) { zuno.ajax.get.json(\'../ajaxRef.php?action=listeContactFournisseur&value=\'+value, \'\', function(xhr) {
 			var json = xhr.responseText.evalJSON();
			document.formAddFournisseur.contactComm_fourn.options.length = json.datas.contacts.length;
			document.formAddFournisseur.ContactADV_fourn.options.length = json.datas.contacts.length;
			document.formAddFournisseur.contactFact_fourn.options.length = json.datas.contacts.length;
			for (i=0; i<json.datas.contacts.length; i++)
      		{
      			document.formAddFournisseur.contactComm_fourn.options[i].value = json.datas.contacts[i].id_cont;
      			document.formAddFournisseur.contactComm_fourn.options[i].text = json.datas.contacts[i].civ_cont+\' \'+json.datas.contacts[i].prenom_cont+\' \'+json.datas.contacts[i].nom_cont;
      			document.formAddFournisseur.ContactADV_fourn.options[i].value = json.datas.contacts[i].id_cont;
      			document.formAddFournisseur.ContactADV_fourn.options[i].text = json.datas.contacts[i].civ_cont+\' \'+json.datas.contacts[i].prenom_cont+\' \'+json.datas.contacts[i].nom_cont;
      			document.formAddFournisseur.contactFact_fourn.options[i].value = json.datas.contacts[i].id_cont;
      			document.formAddFournisseur.contactFact_fourn.options[i].text = json.datas.contacts[i].civ_cont+\' \'+json.datas.contacts[i].prenom_cont+\' \'+json.datas.contacts[i].nom_cont;
      		}
    		document.formAddFournisseur.contactComm_fourn.options.selectedIndex = 0;
    		document.formAddFournisseur.ContactADV_fourn.options.selectedIndex = 0;
    		document.formAddFournisseur.contactFact_fourn.options.selectedIndex = 0;
                $(\'NOM\').value = json.datas.nomEntreprise;
                $(\'CP\').value = json.datas.cpEntreprise;
 		} );}';
	$this->script .= 'function autoCompletage() {var ent = 1;} ';
	$this->script .= '</script>';
	return $this->vueCreation($datas);
    }


    private function initioFieldsetCreation($datas) {
	$this->fieldset1 = new ZunoFieldset("Entreprise",'','height:130px');
	$this->fieldset2 = new ZunoFieldset("Autres coordonnées",'','height:130px');
	$this->fieldset3 = new ZunoFieldset("Contact",'','height:130px');

	$this->fieldset1->ligneInput('Nom', 'nom_ent', $datas['data']['nom_ent'],'','ZTips.contactEnt.nom');
	$this->fieldset1->ligneInput('Adresse', 'add1_ent', $datas['data']['add1_ent'],'','ZTips.contactEnt.add');
	$this->fieldset1->ligneInput('Complément', 'add2_ent', $datas['data']['add2_ent'],'','ZTips.contactEnt.add');
	$this->fieldset1->ligneInputCPVille('cp_ent', $datas['data']['cp_ent'], 'ville_ent', $datas['data']['ville_ent'],'ZTips.contactEnt.cpVille');
	$this->fieldset1->ligneSelect('Pays', 'pays_ent', $datas['data']['pays_ent'], $datas['pays'], false,'','ZTips.contactEnt.pays');

	$this->fieldset2->ligneInput('Téléphone', 'tel_ent', $datas['data']['tel_ent'],'','ZTips.contactEnt.tel');
	$this->fieldset2->ligneInput('Faximile', 'fax_ent', $datas['data']['fax_ent'],'','ZTips.contactEnt.fax');
	$this->fieldset2->ligneInput('Web', 'www_ent', $datas['data']['www_ent'],'','ZTips.contactEnt.web');

	$this->fieldset3->ligneSelect('Civilité', 'civ_cont', $datas['data']['civ_cont'], array('M.' => 'Monsieur', 'Mme' => 'Madame', 'Mlle' => 'Mademoiselle'), false,'','ZTips.contactPers.civ');
	$this->fieldset3->ligneInput('Prenom', 'prenom_cont', $datas['data']['prenom_cont'],'','ZTips.contactPers.prenom');
	$this->fieldset3->ligneInput('Nom', 'nom_cont', $datas['data']['nom_cont'],'','ZTips.contactPers.nom');
	$this->fieldset3->ligneInput('E-mail', 'mail_cont', $datas['data']['mail_cont'],'','ZTips.contactPers.mail');
	$this->fieldset3->ligneInput('Téléphone', 'tel_cont', $datas['data']['tel_cont'],'','ZTips.contactPers.tel');
    }

    private function vueCreation($datas) {
	$this->initioFieldsetCreation($datas);
	$this->form = new ZunoForm('formAddFournisseur', 'produit/Fournisseur.php', 'POST');
	$this->form->newBlock($this->fieldset1->generateFieldset());
	$this->form->newBlock($this->fieldset2->generateFieldset());
	$this->form->newBlock($this->fieldset3->generateFieldSet());
	$this->form->newInputHidden('action', 'addFournisseur', 'ToDo');
	$this->form->newInputHidden('cpEnt', '', 'CP');
	$this->form->newInputHidden('nomEnt', '', 'NOM');

	$this->html = new ZunoRenduHtml('ficheFormAddFourn');
	$this->html->replaceJS($this->script);
	$this->html->generateZBox(imageTag('../img/prospec/produit.png','Fournisseur').' Nouveau Fournisseur ', '', $this->form->generateForm(), $this->form->generateButtons('ficheFormAddFourn', 'Recommencer', 'Créer ce fournisseur'),'formFournisseurAdd', 'open');
	return $this->html->generateRendu();
    }

    private function listeProduitsLies($datas) {
	$this->listeProd->autoHrefLigneID('Produit.php', 'id_prod');
	$titre['Ref']='small';
	$titre['Nom'] = '';
	$titre['Famille']='';
	$titre['Prix catalogue (€)']='';
	$titre['Remise (%)'] = '';
	$titre['Prix d\'achat (€)'] = '';
	$titre['Stock'] = '';
	$titre['<img src="../img/prospec/multiple-check.png" onclick="toggleCheckbox(\'resultFormProduit\');"/>']='small';
	$this->listeProd->ligneTitre($titre);
	$compteur = 0;
	foreach($datas['prod'] as $v) {
	    $ligne[$v['id_prod']] = '';
	    $ligne[$v['nom_prod']] = '';
	    $ligne[$v['treePathKey'].' '.$v['nom_prodfam']] = '';
	    $ligne['okou0'.prepareNombreAffichage($v['prixF'])] = '+ right';
	    $ligne['okou1'.prepareNombreAffichage($v['remiseF'])] = '+ right';
	    $ligne['okou2'.prepareNombreAffichage($v['prixF']*(1-$v['remiseF']/100))] = '+ right';
	    $ligne[$v['stock_prod']] = '+ right';
	    $ligne['<input value="'.$v['id_prod'].'" type="checkbox" name="prod['.$compteur.']" />'] = '+ right';
	    $this->listeProd->ligneTableau($ligne, $v['id_prod']);
	    $ligne = null;
	    $compteur ++;
	}
    }
    private function vueTableau($datas) {
	$this->listeProduitsLies($datas);
	return $this->listeProd->generateTableau();
    }
    public function popupActionProd() {
	$fieldset = new ZunoFieldset("Gestion des produits");
	$fieldset->ligneInput("Nouveau prix", "prixF", "", ' id="InputPF" ','ZTips.fournisseur.produitNewPrix');
	$fieldset->ligneInput("Nouvelle remise", "remiseF","", ' id="InputRF" ','ZTips.fournisseur.produitNewRemise');
	$form = new ZunoForm("formPopupProdFourn", "produit/Fournisseur.php", "POST");
	$pied = '<a name="reset" onclick="zuno.popup.close"><img align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> Annuler</a>';
	$pied .= '<a name="submit" onclick="submitPopupFournProd();"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> Enregistrer</a>';

	$form->newBlock($fieldset->generateFieldset(), '50');
	$rendu = new ZunoRenduHtml("popupProdFourn");
	$rendu->addBox('<div id="erreurPopup"></div>');
	$rendu->generateZBox("Modifier produits", "Modifier produits", $form->generateForm(), $pied, 'popupBoxProdFourn', 'open', true);
	return $rendu->generateInterneRendu();

    }
}
?>
