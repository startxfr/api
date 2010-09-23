<?php
loadPlugin(array('ZModels/FactureFournisseurModel'));
loadPlugin('ZView/GeneralView');

/**
 * Description of FactureFournisseurView
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class FactureFournisseurView extends generalView {

    private $formSearch;
    private $formAdd;
    private $listeResult;
    private $ficheFact;
    private $script;
    private $form;
    private $optionnel;
    private $resultat;

    /**
     * Constructeur de la classe.
     * Il instancie le Javascript commun à toutes les pages
     */
    public function __construct() {
	$this->script = '<script type="text/javascript">';
	$this->script .= "\n".'contact_completer = new Ajax.Autocompleter("ACcontact_factfourn", "ACcontact_factfourn_choix", "../ajaxRef.php?action=listeContact", { paramName: "value", minChars: 2, indicator : "loadingcontact_factfourn", afterUpdateElement : autoCompleteHidden } ); ';
	$this->script .= "\n".'contact_completer.options.defaultParams=\'id_ent=\'+$F(\'ACentreprise_factfournhidden\'); ';
	$this->script .= "function ChangeFactureFournResultPage(limit,from,order,sens) {
		    ChangeFactureFournAction('searchFactureFourn');
		    if(limit != undefined) ChangeLimit(limit);
		    if(from  != undefined) ChangeFrom(from);
		    if(order != undefined) ChangeOrder(order,sens);
		    ChangeSubmit();
		}
		function ChangeFactureFournAction(value) {\$('listeFactureFournAction').value = value;}
		function ChangeFrom(value) { \$('fromFactureFournSearch').value = value; }
		function ChangeLimit(value) { \$('limitFactureFournSearch').value = value; }
		function ChangeOrder(value,sens) {
		    if(sens == undefined) sens = 'ASC';
		    \$('orderFactureFournSearch').value = value;
		    \$('orderSensFactureFournSearch').value = sens;
		}
		function ChangeSubmit() {
			    zuno.business.formTools.sendFormAjah('searchFactFourn','facturier/FactureFournisseurListe.php','searchListeFournisseur');
		}";
	$this->script .= "\n".'function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {alert(\'Cette valeur n\\\'existe pas.\'); champ.value=$(champ.id+\'old\').value; setTimeout(function() { champ.focus(); }, 100) } } ';
	$this->script .= "\n".'function calculette() {TTC = $(\'TTC\').value.replace(/,/g, ".").replace(/ /g, ""); TX = $(\'IDtaux_tva\').value; TVA = TTC*(TX/100); TVA = Math.round(TVA*100)/100; $(\'IDmontantTVA_factfourn\').value = TVA; $(\'HT\').value = TTC - TVA;}';
	$this->script .= "\n".'function getReg(value) { if(value >= 4) {$(\'dateReg\').style.display=\'table-row\'; } else {$(\'dateReg\').style.display=\'none\';} } ';
	$this->script .= "\n".'function affRen(cbox) { if(cbox.checked) { $(\'mailF\').parentNode.parentNode.style.display = \'table-row\'; $(\'statusF\').parentNode.parentNode.style.display = \'table-row\'; $(\'fin_renID\').parentNode.parentNode.style.display = \'table-row\'; $(\'selectF\').parentNode.parentNode.style.display = \'table-row\'; } else { $(\'mailF\').parentNode.parentNode.style.display = \'none\'; $(\'fin_renID\').parentNode.parentNode.style.display = \'none\'; $(\'selectF\').parentNode.parentNode.style.display = \'none\'; $(\'statusF\').parentNode.parentNode.style.display = \'none\'; } } ';
	$this->script .= '</script>';
    }

    /**
     * Méthode qui initialise le formulaire de recherche des factures fournisseurs
     * @param array $datas Les données nécessaires
     */
    private function initioFormSearch($datas) {
	$this->script .= '<script type="text/javascript">';
	$this->script .= "Event.observe(document, 'keypress', function(event){ if(event.keyCode == Event.KEY_RETURN) ChangeSubmit(); return false;}, false);";
	$this->script .= '</script>';
	$fieldset = new ZunoFieldset("Recherche");
	$fieldset->ligneInput("Titre", "titre_factfourn",'','','ZTips.factFourn.searchTitre');
	$fieldset->ligneInput("Fournisseur", "nom_ent",'','','ZTips.factFourn.searchEnt');
	$fieldset->ligneInput("Contact", "nom_cont",'','','ZTips.factFourn.searchContact');
	$fieldset->ligneInput("Montant minimal", "montantHT_factfourn",'','','ZTips.factFourn.searchContact');
	$fieldset->ligneInput("Montant maximal", "montantHT_factfourn2",'','','ZTips.factFourn.searchContact');
	$fieldset->ligneSelect("Status", "status_factfourn", "", $datas['status'],true,'','ZTips.factFourn.searchStatut');

	$this->formSearch = new ZunoForm("searchFactFourn", "facturier/FactureFournisseurListe.php", "POST");
	$this->formSearch->newInputHidden("action", "searchFactFourn", "actionFactureFournSearch");
	$this->formSearch->newInputHidden("limit", 30, "limitFactureFournSearch");
	$this->formSearch->newInputHidden("from", 0, "fromFactureFournSearch");
	$this->formSearch->newInputHidden("order",'id_id_factfourn', "orderFactureFournSearch");
	$this->formSearch->newInputHidden("orderSens", 'DESC', "orderSensFactureFournSearch");
	$this->formSearch->newBlock($fieldset->generateFieldset(), '50');
    }

    /**
     * Méthode qui génère le formulaire de création d'une facture fournisseur
     * @param array $datas Les données nécessaires
     */
    private function initioFormAdd($datas) {

	$fieldset = new ZunoFieldset("Facture",'','height:150px');
	$fieldset2 = new ZunoFieldset("Règlement",'','height:150px');
	$fieldset->ligneInputObligatoire("Nom", "titre_factfourn",'','','ZTips.factFourn.nom');
	$fieldset->ligneAutoCompleteObligatoire("Fournisseur", "entreprise_factfourn", "", "", "listeEntreprise",'',false,'',true,'ZTips.factFourn.fournisseur');
	$fieldset->ligneAutoCompleteObligatoire("Contact", "contact_factfourn", "", "", "", "", false, "", false,'ZTips.factFourn.contact');
	$fieldset->ligneTArea("Description", "desc_factfourn",'','','ZTips.factFourn.desc');

	$fieldset2->ligneInputDateObligatoire("Attendu le", "datePaye_factfourn", date('d/m/Y'),'','','','ZTips.factFourn.dateReglementAttendu');
	$fieldset2->ligneInput("Montant HT (€)", "montantHT", "0", ' id="HT" readonly="readonly" ','ZTips.factFourn.montantHT');
	$fieldset2->ligneInputTVAObligatoire('montantTVA_factfourn', "0", 'taux_tva', '19.6','ZTips.factFourn.tauxTVA');
	$fieldset2->ligneInputObligatoire("Montant TTC (€)", "TTC", "0", ' id="TTC" onchange="calculette();" ','ZTips.factFourn.montantTTC');
	$fieldset2->ligneSelectObligatoire("Mode", "modeReglement_factfourn", "3", $datas['mode'], false,'','ZTips.factFourn.reglementMode');

	$this->formAdd = new ZunoForm("addFactFourn", "FactureFournisseurCreate.php", "POST");
	$this->formAdd->newInputHidden("action", "addFactFourn");
	$this->formAdd->newBlock($fieldset->generateFieldset(), '50');
	$this->formAdd->newBlock($fieldset2->generateFieldset(), '50');
    }

    /**
     * Méthode qui initialise les éléments nécessaires pour la fiche facture
     * @param array $datas Les données nécessaires
     */
    private function initioFicheFact($datas) {
	$date = $datas['data']['datePaye_factfourn'];
	$date = substr($date, 8,2)."/".substr($date,5,2)."/".substr($date,0,4);

	$tva = $datas['data']['montantTTC_factfourn']*($datas['data']['tauxTVA_factfourn']/100);
	$ht = $datas['data']['montantTTC_factfourn'] - $tva;

	$fieldset = new ZunoFieldset("Facture",'','height:160px');
	$fieldset2 = new ZunoFieldset("Règlement",'','height:160px');
	$fieldsetR = new ZunoFieldset("Récurence",'','height:160px');

	$fieldset->ligneInputObligatoire("Nom", "titre_factfourn", $datas['data']['titre_factfourn'],'','ZTips.factFourn.nom');
	$fieldset->ligneAutoCompleteObligatoire("Fournisseur", "entreprise_factfourn", $datas['data']['nom_ent'], $datas['data']['entreprise_factfourn'], "listeEntreprise",'',false,'',true,'ZTips.factFourn.fournisseur');
	$fieldset->ligneAutoCompleteObligatoire("Contact", "contact_factfourn", $datas['data']['civ_cont']." ".$datas['data']['prenom_cont']." ".$datas['data']['nom_cont'], $datas['data']['contact_factfourn'], "", "", false, "", false,'ZTips.factFourn.contact');
	$fieldset->ligneSelectObligatoire("Statut", "status_factfourn", $datas['data']['status_factfourn'], $datas['status'], false, ' onchange="getReg(this.value);" ','ZTips.factFourn.statut');
	$fieldset->ligneTArea("Description", "desc_factfourn", $datas['data']['desc_factfourn'],'','ZTips.factFourn.desc');

	$fieldset2->ligneInputDateObligatoire("Attendu le", "datePaye_factfourn", $date,'','','','ZTips.factFourn.dateReglementAttendu');
	$fieldset2->ligneInput("Montant HT (€)", "montantHT", prepareNombreAffichage($ht), ' id="HT" readonly="readonly" ','ZTips.factFourn.montantHT');
	$fieldset2->ligneInputTVAObligatoire('montantTVA_factfourn', prepareNombreAffichage($tva), 'taux_tva', $datas['data']['tauxTVA_factfourn'],'ZTips.factFourn.tauxTVA');
	$fieldset2->ligneInputObligatoire("Montant TTC (€)", "TTC", prepareNombreAffichage($datas['data']['montantTTC_factfourn']), ' id="TTC" onchange="calculette();" ','ZTips.factFourn.montantTTC');
	$fieldset2->ligneSelectObligatoire("Mode", "modeReglement_factfourn", $datas['data']['modeReglement_factfourn'], $datas['mode'], false,'','ZTips.factFourn.reglementMode');

	if($datas['ren']['fin_ren'] != '0000-00-00 00:00:00' and $datas['ren']['fin_ren'] != "") {
	    $date2 = $datas['ren']['fin_ren'];
	    $date2 = substr($date2, 8,2)."/".substr($date2,5,2)."/".substr($date2,0,4);
	}
	else {
	    $date2 = date('d/m/').(date('Y')+1);
	}
	if($datas['ren']['actif_ren'] == 1 )
	    $styleDisplay="";
	else $styleDisplay = ' style="display:none" ';
	if($datas['data']['id_factfourn'] == $datas['ren']['idChamp_ren']) {
	    $fieldsetR->ligneCheckBox("Activer", "actif_ren", $datas['ren']['actif_ren'], " onchange='affRen(this)' ",'ZTips.factFourn.renewActiver');
	    $fieldsetR->ligneSelectObligatoire("Fréquence", "periode_ren", $datas['ren']['periode_ren'], $datas['periode'], false, " id='selectF' ",'ZTips.factFourn.renewFrequence',$styleDisplay);
	    $fieldsetR->ligneInputDateObligatoire("Date de fin", "fin_ren", $date2, "",'',$styleDisplay,'ZTips.factFourn.renewDateFin');
	    $fieldsetR->ligneSelectObligatoire("Statut", "statusChamp_ren", $datas['ren']['statusChamp_ren'], $datas['status'], false, " id='statusF' ",'ZTips.factFourn.renewStatut',$styleDisplay);
	    $fieldsetR->ligneInput("E-Mail", "mail_ren", $datas['ren']['mail_ren'], " id='mailF' ",'ZTips.factFourn.renewMail',$styleDisplay);
	}
	else if($datas['data']['ren_factfourn'] == "") {
	    $fieldsetR->ligneCheckBox("Activer", "actif_ren", "", " onchange='affRen(this)' ",'ZTips.factFourn.renewActiver');
	    $fieldsetR->ligneSelectObligatoire("Fréquence", "periode_ren", "", $datas['periode'], false, " id='selectF' ",'ZTips.factFourn.renewFrequence',$styleDisplay);
	    $fieldsetR->ligneInputDateObligatoire("Date de fin", "fin_ren", $date2, "",'',$styleDisplay,'ZTips.factFourn.renewDateFin');
	    $fieldsetR->ligneSelectObligatoire("Statut", "statusChamp_ren", "", $datas['status'], false, " id='statusF' ",'ZTips.factFourn.renewStatut',$styleDisplay);
	    $fieldsetR->ligneInput("E-Mail", "mail_ren", $_SESSION['user']['mail'], " id='mailF' ",'ZTips.factFourn.renewMail',$styleDisplay);
	}
	else {
	    $fieldsetR->ligneLinkTo("Facture originale", "FactureFournisseur.php?id_factfourn=".$datas['ren']['idChamp_ren'], "Cette facture a été créée en renouvellement de la facture ".$datas['ren']['idChamp_ren']);
	}
	if($datas['data']['dateReglement_factfourn'] != '0000-00-00 00:00:00') {
	    $date2 = $datas['data']['dateReglement_factfourn'];
	    $date2 = substr($date2, 8,2)."/".substr($date2,5,2)."/".substr($date2,0,4);
	}
	else {
	    $date2 = "";
	}
	if($datas['data']['status_factfourn'] < 4)
	    $display = 'style="display:none;" ';
	else
	    $display = "";
	$fieldset2->ligneInputDate("Payée le : ", "dateReglement_factfourn", $date2, "", "", ' id="dateReg" '.$display);
	if($datas['data']['fichier_factfourn'] == '') {
	    $upload = new ZunoFormUpload("uploadFichier", "../gnose/Upload.php");
	    $upload->setLegend("Fichier joint");
	    $upload->setLigne($GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.factureFournisseur']);
	    $upload->setRetour("../facturier/FactureFournisseur.php?newFichier=".$datas['data']['id_factfourn']);
	    $this->optionnel = $upload->generateForm('50');

	}
	else {
	    $fieldset->ligneLinkTo("Fichier joint", "FactureFournisseur.php?action=voir&id=".$datas['data']['id_factfourn'], $datas['data']['fichier_factfourn'], "../img/files/pdf.png");
	    $this->optionnel = "";
	}
	$this->form = new ZunoForm("modifFactFourn", "facturier/FactureFournisseur.php", "POST");
	$this->form->newInputHidden("action", "modifFactFourn", 'idaction');
	$this->form->newInputHidden("id_factfourn", $datas['data']['id_factfourn']);
	$this->form->newInputHidden("oldstatus", $datas['data']['status_factfourn']);
	if($datas['data']['id_factfourn'] == $datas['ren']['idChamp_ren'] or $datas['data']['ren_factfourn'] == "")
	    $this->form->newInputHidden("ren_factfourn", $datas['data']['ren_factfourn']);
	$this->form->newBlock($fieldset->generateFieldset(), '50');
	$this->form->newBlock($fieldset2->generateFieldset(), '50');
	$this->form->newBlock($fieldsetR->generateFieldset(), '50');

    }

    /**
     * Méthode qui affiche une fiche facture fournisseur
     * @param array $datas Les données ) afficher
     * @param string $light Précise si on veut toute la fiche ou non
     * @return string Le code HTML à afficher
     */
    public function view($datas, $light='', $mess = '') {
	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Fiche facture fournisseur "'.$datas['data']['id_factfourn'].'" - '.$datas['data']['nom_ent'],'',true);
	$this->initioFicheFact($datas);
	if($light == 'interneInfos') {
	    $blocks = $this->form->getBlocks();
	    $retour = '';
	    foreach($blocks as $v) {
		$retour .= $v;
	    }
	    return '<span class="important" style="text-align:center;">'.$mess.'</span>'.$retour;
	}
	else {
	    $this->ficheFact = new ZunoRenduHtml("ficheFactureFournisseur");
	    $this->ficheFact->generateZBox('<img src="../img/prospec/factureFourn.png" alt="Facture fournisseur" title="Facture fournisseur" /> Détail de la facture fournisseur','<img src="../img/prospec/factureFourn.png" alt="Facture fournisseur" title="Facture fournisseur" /> Détail de la facture fournisseur', $this->form->generateForm().$this->optionnel, $this->form->generateButtons().'<a name="journal" onclick="function(){$(\'idaction\').value = \'journalise\';};zuno.business.formTools.sendFormAjah(\'modifFactFourn\', \'facturier/FactureFournisseur.php\',\'idmodifFactFournformAutoRetour\');"><img alt="journal" src="../img/page.menu/JournalBanque.png"/> Inscrire dans le journal de banque</a>', 'divFicheFact', 'open', true);
	    $this->ficheFact->insertJS($this->script);

	    return $this->ficheFact->generateRendu();
	}

    }

    /**
     * Méthode qui génère le rendu HTML du formulaire de récation
     * @param array $datas Les données nécessaires à l'initialisation du formulaire
     * @param string $light Inutilisé, peut servir à séparer plusieurs possibilités
     * @param string $mess Un message à afficher
     * @return string Le code HTML
     */
    public function creer($datas, $light='', $mess = '') {
	$this->initioFormAdd($datas);

	$rendu = new ZunoRenduHtml("CreerFactureFournisseur");
	$rendu->insertJS($this->script);
	$rendu->generateZBox('<img src="../img/prospec/factureFourn.png" alt="Facture fournisseur" title="Facture fournisseur" /> Nouvelle facture fournisseur', '<img src="../img/prospec/factureFourn.png" alt="Facture fournisseur" title="Facture fournisseur" /> Nouvelle facture fournisseur', $mess.$this->formAdd->generateForm(), $this->formAdd->generateButtonsNoAjax(), 'addFactFournBlock', 'open', true);
	return $rendu->generateRendu();
    }

    /**
     * Fonction appelée pour rechercher des factures fournisseurs.
     * @param $datas	Les données nécessaires
     * @return $retour	Le HTML
     */
    public function searchResult($datas, $light='') {
	if($light == 'result') {
	    $rendu = new ZunoRenduHtml('rechercheFourn');
	    $titreR = $this->Result($datas);
	    $bouton = $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeFactureFournResultPage",'../');
	    $rendu->insertJS($this->script);
	    $rendu->generateZBox($titreR, $titreR, $this->resultat->generateTableau(),$bouton, 'searchListeFournisseur', 'open');
	    return $rendu->generateInterneRendu();
	}
	else {
	    $this->initioFormSearch($datas);
	    $titreR = $this->Result($datas);
	    $rendu = new ZunoRenduHtml('rechercheFourn');
	    $rendu->insertJS($this->script);
	    $bouton = $this->resultNavigation($datas['from'],$datas['limit'],$datas['total'],"ChangeFactureFournResultPage",'../');
	    $rendu->generateZBox('<img src="../img/prospec/voir.png" alt="rech" title="Critères de recherche" /> Recherche', '<img src="../img/prospec/voir.png" alt="rech" title="Critères de recherche" /> Recherche', $this->formSearch->generateForm(), $this->formSearch->generateButtons('listeResultFournisseur', 'Annuler', 'Rechercher'), 'divSerachFourn', 'open');
	    $rendu->generateZBox($titreR, $titreR, $this->resultat->generateTableau(), $bouton, 'searchListeFournisseur', 'open', true,'listeResultFournisseur');
	    $rendu->insertJS('<script> ent = 1;</script>');
	    return $rendu->generateRendu();
	}

    }

    /**
     * Méthode qui génère le tableau de résultat d'une recherche
     * @param array $datas Les données à afficher
     * @return string Le titre du tableau
     */
    private function Result($datas) {
	$this->resultat = new ZunoTableau();
	$champs['Titre']="";
	$champs1['Titre']="titre_factfourn";
	$champs['Fournisseur']="big";
	$champs1['Fournisseur']="entreprise_factfourn";
	$champs['Contact']="";
	$champs1['Contact']="contact_factfourn";
	$champs['Total&nbsp;TTC']="";
	$champs1['Total&nbsp;TTC']="montantTTC_factfourn";
	$champs['Date paiement']="";
	$champs1['Date paiement']="datePaye_factfourn";
	$champs['Statut']="";
	$champs1['Statut']="status_factfourn";
	$champs['Récurrence'] = "";
	$this->resultat->ligneTitre($champs,false,$champs1,'ChangeFactureFournResultPage','../',$datas['order'],$datas['orderSens'],$datas['limit']);

	$this->resultat->autoHrefLigneID("FactureFournisseur.php", "id_factfourn");
	foreach($datas['data'] as $k => $v) {
	    $date = $v['datePaye_factfourn'];
	    $date = substr($date, 8,2)."/".substr($date,5,2)."/".substr($date,0,4);
	    $ligne[$v['titre_factfourn']] = '+ center';
	    $ligne[$v['nom_ent']." (".$v['cp_ent']." - ".$v['ville_ent'].")"] = '';
	    $ligne['okou1'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont']] = '';
	    $ligne[prepareNombreAffichage($v['montantHT_factfourn']+$v['montantTVA_factfourn'])] = '';
	    $ligne[$date]='';
	    $ligne[$v['nom_stfactfourn']] = '';
	    if($datas['ren'][$k]['idChamp_ren'] == $v['id_factfourn'] and $datas['ren'][$k]['actif_ren'] == 1)
		$ligne[imageTag('../img/prospec/relance.png', "Renouvellée")]="right";
	    else
		$ligne["&nbsp;"] ="";
	    $this->resultat->ligneTableau($ligne, $v['id_factfourn']);
	    $ligne = null;
	}
	$titre = $this->resultNavigationTitle($datas['from'],$datas['limit'],$datas['total'],'factureFourn');
	return $titre;

    }
}
?>
