<?php
/*
 * Created on 6 oct. 2009
*/
/**
 * Classe qui gère tout le rendu HTML de Zuno
 * @author Nicolas Mannocci
 * @version 1.0
 */
class ZunoRenduHtml {
    private $listeBox;
    private $js;
    private $grandID;
    private $rendu;

    /**
     * Constructeur de la classe
     * @param string $id Permet de placer toutes les box dans une div avec un id particulier
     */
    public function __construct($id='') {
	$this->js = '';
	$this->grandID = $id;
    }

    /**
     * Méthode generateRendu permet de générer le rendu HTML complet
     * @return string Le code HTML correspondant.
     */
    public function generateRendu() {
	$this->rendu = '<div id="error"></div>';
	$this->rendu .= $this->js;
	if($this->grandID != '') {
	    $this->rendu .= '<div id="'.$this->grandID.'">';
	}
	if(is_array($this->listeBox))
	    foreach($this->listeBox as $v) {
		$this->rendu .= $v;
	    }
	if($this->grandID != '') {
	    $this->rendu .= '</div>';
	}

	return $this->rendu;
    }

    /**
     * Méthode generateInterneRendu permet de généré du code HTML interne à une page dans une div précise
     * @return string Le code HTML du rendu.
     */
    public function generateInterneRendu() {
	$this->rendu = '';
	if(is_array($this->listeBox))
	    foreach($this->listeBox as $v) {
		$this->rendu .= $v;
	    }
	return $this->rendu;
    }

    /**
     * Méthode qui retourne le contenu du Javascript
     * @return string Le code Javascript
     */
    public function getJS() {
	return $this->js;
    }

    /**
     * Méthode replaceJS permet d'initialiser le javascript
     * @param string $javascript Le javascript à mettre
     */
    public function replaceJS($javascript) {
	$debut = strpos($javascript, '<script>');
	$fin = strpos($javascript, '</script>');
	if($debut ===false and  $fin === false)
	    $this->js = '<script>'.$javascript.'</script>';
	else {
	    if($debut != 0 or $fin != strlen($javascript)-9) {
		while($debut !== false) {
		    $javascript = substr($javascript, 0, $debut).substr($javascript,$debut+8);
		    $debut = strpos($javascript, '<script>');
		}
		$fin = strpos($javascript, '</script>');
		while($fin !== false) {
		    $javascript = substr($javascript, 0, $fin).substr($javascript,$fin+9);
		    $fin = strpos($javascript, '</script>');
		}
		$javascript = '<script>'.$javascript.'</script>';
	    }
	    $this->js = $javascript;
	}
    }

    /**
     * Méthode insertJS permet de rajouter du javascript sans supprimer le code déjà inséré
     * @param string $javascript Le javascript à rajouter
     */
    public function insertJS($javascript) {
	if($this->js == '')
	    $this->js = $javascript;
	else {
	    $debut = strpos($javascript, '<script>');
	    $fin = strpos($javascript, '</script>');
	    if($debut ===false and  $fin === false)
		$this->js = substr($this->js,0,strlen($this->js)-9)."\n".$javascript.'</script>';
	    else {
		while($debut !== false) {
		    $javascript = substr($javascript, 0, $debut).substr($javascript,$debut+8);
		    $debut = strpos($javascript, '<script>');
		}
		$fin = strpos($javascript, '</script>');
		while($fin !== false) {
		    $javascript = substr($javascript, 0, $fin).substr($javascript,$fin+9);
		    $fin = strpos($javascript, '</script>');
		}
		$this->js = substr($this->js,0,strlen($this->js)-9)."\n".$javascript.'</script>';
	    }
	}
    }

    /**
     * Create portlet with 2 box Open or closed
     * @param string $titre Title for first side
     * @param string $titre_off Title when box is closed
     * @param string $corps Body of the ZBox
     * @param string $pied Footer of the ZBox
     * @param string $id_div Id for the ZBox
     * @param string $etat close or open
     * @param bool $forceEtat
     * @param string $idCouvrante
     */
    public function generateZBox($titre, $titre_off = '', $corps = '', $pied = '', $id_div = '', $etat = '', $forceEtat = false, $idCouvrante = '') {
	if ($etat =='close' or $etat =='none')
	    $newState ='close';
	else $newState ='open';

	if($forceEtat)
	    $state = $_SESSION['ZBoxState'][$id_div] = $newState;
	elseif(array_key_exists('ZBoxState',$_SESSION) and array_key_exists($id_div,$_SESSION['ZBoxState']))
	    $state = $_SESSION['ZBoxState'][$id_div];
	else $state = $newState;

	if ($id_div == '')	$id_div = substr(md5(rand(0,1000).time()), 0, 7);
	if ($pied != '') 		$pied ='<div class="footer"><div class="content">'.$pied.'</div></div>';

	$tag['id_div'] 	= $id_div;
	$tag['titre'] 	= $titre;
	$tag['corps'] 	= $corps;
	$tag['pied']	= $pied;
	$tag['state'] 	= $state;

	if($idCouvrante != '')
	    $this->listeBox[] =  '<div id="'.$idCouvrante.'">'.templating('Box', $tag).'</div>';
	else
	    $this->listeBox[] = templating('Box', $tag);
    }

    /**
     * Create portlet with 3 box flick-flack
     * @param $titre String: Title for first side
     */
    public function generateZBox3($titre,$titre1 = '',$titre_off = '', $corps = '', $pied = '', $corps1 = '', $pied1 = '', $id_div = '', $etat = '', $forceEtat = false, $idCouvrante = '') {
	if ($etat =='close' or $etat =='none')
	    $newState ='close';
	elseif ($etat =='open1' or $etat =='1')
	    $newState ='open1';
	else $newState ='open';

	if($forceEtat)
	    $state = $_SESSION['ZBoxState'][$id_div] = $newState;
	elseif(array_key_exists('ZBoxState',$_SESSION) and array_key_exists($id_div,$_SESSION['ZBoxState']))
	    $state = $_SESSION['ZBoxState'][$id_div];
	else $state = $newState;


	if ($titre1 == '')	$titre1 = $titre;
	if ($id_div == '')	$id_div = md5(rand(0,1000).time());
	if ($pied != '')		$pied   ='<div class="footer"><div class="content">'.$pied.'</div></div>';
	if ($pied1 != '')		$pied1  ='<div class="footer"><div class="content">'.$pied1.'</div></div>';

	$tag['id_div'] 	= $id_div;
	$tag['titre'] 	= $titre;
	$tag['corps'] 	= $corps;
	$tag['pied']	= $pied;
	$tag['titre1']	= str_replace("'","\'",$titre1);
	$tag['corps1']	= $corps1;
	$tag['pied1']	= $pied1;
	$tag['state'] 	= $state;

	if($idCouvrante != '')
	    $this->listeBox[] =  '<div id="'.$idCouvrante.'">'.templating('Box3', $tag).'</div>';
	else
	    $this->listeBox[] = templating('Box3', $tag);
    }

    /**
     * Méthode addBox qui permet d'ajouter une box non générée par la classe.
     * @param string $box La box à ajouter
     */
    public function addBox($box) {
	$this->listeBox[] = $box;
    }
}

/**
 * Classe qui génère les fieldsets pour Zuno
 * @author Nicolas Mannocci
 * @version 1.1
 */
class ZunoFieldset {
    private $listeLignes;
    private $lastLigne;
    private $fieldset;
    private $fieldsetId;
    private $legend;

    /**
     * Constructeur de la classe
     * @param string $legend Paramêtre optionnel pour préciser une légende au fieldset
     * @param string $fieldsetId Paramêtre optionnel pour préciser un ID au fieldset
     */
    public function __construct($legend ='',$fieldsetId = '') {
	$this->legend = $legend;
	$this->fieldsetId = $fieldsetId;
    }

    /**
     * Méthode getLegend permet de récupérer la légende d'un fieldset
     * @return string La légende du fieldset
     */
    public function getLegend() {
	return $this->legend;
    }

    /**
     * Méthode generateFieldset qui génère le fieldset final et le renvoie
     * @return string Le fieldset généré
     */
    public function generateFieldset() {
	if($this->fieldsetId != '')
	    $this->fieldset = '<fieldset class="form" id="'.$this->fieldsetId.'">';
	else $this->fieldset = '<fieldset class="form">';
	if($this->legend != '')
	    $this->fieldset.= '<legend>'.$this->legend.'</legend>';
	if(is_array($this->listeLignes))
	    foreach($this->listeLignes as $v) {
		$this->fieldset .= $v;
	    }
	$this->fieldset .= '</fieldset>';

	return $this->fieldset;
    }

    /**
     * Méthode getLastLigne qui permet de donner la dernière ligne générée pour un éventuel traitement externe à la classe
     * @return string La dernière ligne générée par la classe
     */
    public function getLastLigne() {
	return $this->lastLigne;
    }

    /**
     * Méthode getListeLignes qui ressort la liste des lignes générées pour un éventuel traitement externe à la classe.
     * @return string La liste des lignes généres par la classe
     */
    function getListeLignes() {
	return $this->listeLignes;
    }

    /**
     * Méthode ligneLinkTo permet de générer une ligne avec un lien href
     * @param string $label Le label du champ
     * @param string $link L'adresse du lien vers qui pointer
     * @param string $value Le texte à placer dans le lien
     * @param string $srcImage Une image dans le lien, le cas échéant
     * @param string $attRow Attribut a ajouter dans la balise englobante class=row
     */
    public function ligneLinkTo($label, $link, $value, $srcImage = '', $attRow='') {
	if($srcImage == '') {
	    $img = '';
	}
	else {
	    $img = '<img src="'.$srcImage.'" alt="Image" />';
	}
	$lien = '<a href="'.$link.'" title="'.$value.'">'.$img.' '.$value.'</a>';
	$out = '<div class="row" '.$attRow.'>' .
		'<div class="label">'.$label.'</div>' .
		'<div class="field">'.$lien.'</div>' .
		'</div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode ligneInput qui ressort directement un champ préfait pour afficher dans ZUNO
     * @param string $label Le label du champ
     * @param string $nom Le nom du champ
     * @param string $value La valeur du champ
     * @param string $autre Si besoin d'autres tags dans le champ
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     * @param string $attRow Attribut a ajouter dans la balise englobante class=row
     */

    public function ligneInput($label, $nom, $value='', $autre='', $tips = '', $attRow='') {
	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	$out = '<div class="row" '.$attRow.'>'
		.'<div class="label" '.$tips.' >'.$label.'</div>'
		.'<div class="field">'.$this->inputTag('text', $nom, '', '', '16', $value, $autre).'</div>'
		.'</div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode ligneInputDate qui permet de gnérer une ligne d'insertion de date
     * @param string $label Le label du champ
     * @param string $nom Le nom du champ
     * @param string $value La valeur initiale du champ
     * @param string $format Le format de la date, optionnel
     * @param string $autre D'autres tags
     * @param string $attRow Permet de préciser d'autres tags sur la div row
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     */
    public function ligneInputDate($label, $nom, $value, $format = "", $autre = "", $attRow="", $tips='') {
	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	$out = '<div class="row" '.$attRow.'>'
		.'<div class="label" '.$tips.' >'.$label.'</div>'
		.'<div class="field">'.$this->inputDateTag('text', $nom, $value, $format, '', '../', $autre).'</div>'
		.'</div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode ligneInputCPVille qui permet de générer une ligne de CP - Ville facilement
     * @param string $nomCP Le nom du champ CP
     * @param string $valueCP La valeur initiale du champ CP
     * @param string $nomVille Le nom du champ ville
     * @param string $valueVille La valeur initiale du champ ville à afficher (le nom de la ville sélectionnée)
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     * @param string $attRow Attribut a ajouter dans la balise div englobante class=row
     */
    public function ligneInputCPVille($nomCP, $valueCP = '', $nomVille = '', $valueVille = '', $tips='', $attRow='') {
	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	if($valueVille == '')
	    $values = array();
	else
	    $values = array($valueVille => $valueVille);
	if($nomVille == '')
	    $nomVille = $nomCP."-Ville";
	$out .= '<div class="row" '.$attRow.'>';
	$out .= '<div class="label" '.$tips.' >CP - ville</div>';
	$out .= '<div class="field">'.$this->inputTag('text',$nomCP,'civ','',4,$valueCP,' id="ID'.$nomCP.'" onchange="zuno.ajax.get.html(\'../ajaxRef.php\',\'action=villeForCp&cp=\'+this.value,\'ID'.$nomVille.'\');"').$this->selectTag($nomVille,$values,$valueVille, 'nextciv',' id="ID'.$nomVille.'"  onblur="finishEditing();" onclick="beginEditing(this);"').'</div>';
	$out .= '</div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode ligneInputTVA qui permet de générer une ligne de Taux tva - montant TVA
     * @param string $nomInput Le nom du champ montant
     * @param string $valueInput La valeur initiale du champ montant
     * @param string $nomSelect Le nom du champ taux tva
     * @param string $valueSelect La valeur initiale du champ taux tva à afficher (le taux TVA sélectionnée)
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     * @param string $attRow Attribut a ajouter dans la balise div englobante class=row
     */
    public function ligneInputTVA($nomInput, $valueInput = '', $nomSelect = '', $valueSelect = '', $tips='', $attRow='') {
	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';

	if($nomSelect == '')
	    $nomSelect = $nomInput."-Taux";
	$out .= '<div class="row" '.$attRow.'>';
	$out .= '<div class="label" '.$tips.' >Taux / Montant TVA</div>';
	$out .= '<div class="field">'.$this->selectTVATag($nomSelect,$values,$valueSelect, 'civ',' id="ID'.$nomSelect.'" onchange="calculette();"  ', false).$this->inputTag('text',$nomInput,'nextciv','',4,$valueInput,' id="ID'.$nomInput.'" READONLY="READONLY" style="width:197px !important;" ').'</div>';
	$out .= '</div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode ligneAutoComplete qui génère directement l'autocompleteur pour ZUNO
     * @param string $label Le label du champ
     * @param string $nom Le nom du champ
     * @param string $value La valeur initiale du champ
     * @param string $valueBis La valeur cachée à renvoyer initiale
     * @param string $listing L'action qui doit être faite par le serveur
     * @param string $autre D'autres tags si nécessaires
     * @param bool $plus Booléen qui indique si on doit afficher le boutton +
     * @param string $actionPlus Précise l'action à faire si le boutton est cliqué
     * @param bool $autoScript Permet de ne pas générer le script automatique du autoCompleteur
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     * @param string $attRow Attribut a ajouter dans la balise englobante class=row
     */
    public function ligneAutoComplete($label, $nom, $value='', $valueBis = '', $listing='', $autre='', $plus = false, $actionPlus = '', $autoScript = true, $tips='', $attRow='') {
	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	if($valueBis == '')
	    $valueBis = $value;

	$out = '<div class="row" '.$attRow.'>'
		.'<div class="label" '.$tips.' >'.$label.'</div>'
		.'<div class="field">'.$this->inputTag('text', $nom.'aff','', '', '16',$value, ' id="AC'.$nom.'" onchange="verifAutocompleteur(this);" ').'<img style="display:none; margin-left:5px;" id="loading'.$nom.'" src="../img/ajax-loader.gif" alt="loader"/></div>';
	if($plus)
	    $out .= '<a id="AC'.$nom.'AddImg" style="display:none;" name="AC'.$nom.'AddImg" title="Créer" onclick="'.$actionPlus.'(\'AC'.$nom.'\');"><img alt="cont" title="Créer" src="../img/prospec/add.png" /></a>';

	$out .= '<input type="hidden" name="'.$nom.'" id="AC'.$nom.'hidden" value="'.$valueBis.'" />
						<input type="hidden" name="'.$nom.'old" id="AC'.$nom.'old" value="'.$value.'" />
						<div id="AC'.$nom.'_choix" class="autocomplete"></div>'
		.'</div>';
	if($autoScript)
	    $script = '<script>new Ajax.Autocompleter("AC'.$nom.'", "AC'.$nom.'_choix", "../ajaxRef.php?action='.$listing.'", { paramName: "value", minChars: 2, indicator: "loading'.$nom.'",  afterUpdateElement : autoCompleteHidden } ); </script>';
	else
	    $script = '';
	$this->lastLigne = $out.$script;
	$this->listeLignes[] = $out.$script;
    }

    /**
     * Méthode ligneTArea qui génère un textArea directement dans une ligne pour Zuno
     * @param string $label Le label du champ
     * @param string $nom Le nom du champ
     * @param string $value La valeur du champ
     * @param string $autre D'autres tags si nécessaires
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     * @param string $attRow Attribut a ajouter dans la balise englobante class=row
     */
    public function ligneTArea($label, $nom, $value='', $autre='', $tips='', $attRow='') {
	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	$out = '<div class="row" '.$attRow.'>
                    <div class="label" '.$tips.' >'.$label.'</div>
                    <div class="field"><textarea rows="3" cols="35" name="'.$nom.'" '.$autre.' >'.$value.'</textarea></div>
		</div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode ligneSelect qui génère une ligne avec un select pour Zuno
     * @param string $label Le label du champ
     * @param string $nom Le nom du champ
     * @param string $value La valeur initiale du champ
     * @param array $liste La liste des options du champ
     * @param bool $withBlank Précise si une ligne vide doit être dans le champ
     * @param string $autre D'autres tags si nécessaires
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     * @param string $attRow Attribut a ajouter dans la balise englobante class=row
     * @param bool $plus Permet de préciser si on veut le petit plus
     * @param string $actionplus Précise l'action à faire après click sur le plus
     */
    public function ligneSelect($label, $nom, $value, $liste = array(), $withBlank = true, $autre = '', $tips='', $attRow='', $plus = false, $actionplus = '') {
	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	if($plus) {
	    $class = "icon";
	    $img = '<a id="Se'.$nom.'AddImg" name="Se'.$nom.'AddImg" title="Créer" onclick="'.$actionplus.'"><img alt="famille" title="Créer" src="../img/prospec/add.png" /></a>';
	}
	else {
	    $class = $img = "";
	}
	$out ='<div class="row" '.$attRow.'>' .
		'<div class="label" '.$tips.' >'.$label.'</div>' .
		'<div class="field">'.$this->selectTag($nom, $liste, $value, $class, $autre, $withBlank).$img.'</div>'
		.'</div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode ligneCheckBox qui génère une checkbox dans une ligne pour Zuno
     * @param string $label Le label du champ
     * @param string $nom Le nom du champ
     * @param bool $checked Indique si le champ est coché ou pas
     * @param string $autre D'autres tags si nécessaires
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     * @param string $attRow Attribut a ajouter dans la balise englobante class=row
     */
    public function ligneCheckBox($label, $nom, $checked = false, $autre = '', $tips='', $attRow='') {
	if($checked == false or $checked == '0' or $checked == null)
	    $checked = '';
	else
	    $checked = ' checked="checked" ';

	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	$out = '<div class="row" '.$attRow.'>' .
		'<div class="label" '.$tips.' >'.$label.'</div>' .
		'<div class="field"><input type="checkbox" name="'.$nom.'" value="1" '.$checked.' '.$autre.' /></div>'
		.'</div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode ligneRadio qui génère un block de champ radio avec le même nom, un seul est sélectionable
     * @param string $label Le label du champ
     * @param string $nom Le nom du champ
     * @param string $checked La valeur sélectionnée
     * @param array $liste Tableau contenant pour chanque entrée une valeur et un label
     * @param string $autre Eventuels tags supplémentaires
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     * @param string $attRow Attribut a ajouter dans la balise englobante class=row
     */
    public function ligneRadio($label, $nom, $checked = '', $liste = array(), $autre = '', $tips='') {
	if(is_array($liste)) {
	    if($tips != '')
		$tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	    $out = '<div class="row" '.$attRow.'>
			<div class="label" '.$tips.' >'.$label.'</div>
			<div class="field">
				<optgroup label="type">';
	    foreach($liste as $v) {
		$out .= '<input type="radio" name="'.$nom.'" value="'.$v['value'].'"'.(($v["value"] == $checked) ? ' checked="checked"' : "").' '.$autre.' >'.$v['label'].'<br>';
	    }
	    $out .= '</optgroup>
				</div>
				</div>';
	    $this->lastLigne = $out;
	    $this->listeLignes[] = $out;
	}
    }

    /**
     * Méthode ligneRadio qui génère un block de champ radio avec le même nom, un seul est sélectionable
     * @param string $titre Le label du champ
     * @param string $desc La valeur du champ
     * @param string $tips Permet d'ajouter si besoin un tips au champ
     */
    public function ligneFree($titre, $desc, $tips='') {
	if($tips != '')
	    $tips = ' onmouseover="zuno.tooltip(this,'.$tips.');" ';
	$out = '<div class="row">
			<div class="label" '.$tips.' >'.$titre.'</div>
			<div class="field">'.$desc.'</div>
		    </div>';
	$this->lastLigne = $out;
	$this->listeLignes[] = $out;
    }

    /**
     * Méthode addOtherLigne qui permet d'ajouter une ligne non générée par la classe
     * @param string $ligne La ligne à ajouter
     */
    public function addOtherLigne($ligne = '') {
	if($ligne != '') {
	    $this->lastLigne = $ligne;
	    $this->listeLignes[] = $ligne;
	}
    }

    /**
     * Méthode ligneInputHidden qui permet de mettre un input hidden dans le fieldset
     * @param string $nom Le nom du champ
     * @param string $value La valeur du champ
     */
    public function ligneInputHidden($nom, $value) {
	$this->lastLigne = '<input type="hidden" name="'.$nom.'" value="'.$value.'" />';
	$this->listeLignes[] = $this->lastLigne;
    }

    /**
     * Generate clean input tag filled with appropriate information.
     * @param string $type Specify type of input '',text','hidden','submit','Password
     * @param string $name Tag name *
     * @param string $class CSS class
     * @param int $max Max data size
     * @param int $size Input size
     * @param string $value Default value
     * @param string $autre Free information
     * @return string Filled html input tag
     */
    private function inputTag($type, $name, $class = '', $max = '', $size = '', $value = '', $autre = "", $fs = '"') {
	if ($name != '') {
	    $chaine = '<input ';
	    // Input type of form element (button, password,...)
	    if (  ($type == 'hidden')or
		    ($type == 'password')or
		    ($type == 'reset')or
		    ($type == 'submit')or
		    ($type == 'text')or
		    ($type == 'checkbox')or
		    ($type == 'radio')or
		    ($type == 'file')  ) {
		$chaine .=  'type='.$fs.$type.$fs.' ';
	    }
	    else {
		$chaine .=  'type='.$fs.'text'.$fs.' ';
	    }
	    // Input name
	    if ($name != '') {
		$chaine .=  'name='.$fs.$name.$fs.' ';
	    }
	    // Input CSS class
	    if ($class != '') {
		$chaine .=  'class='.$fs.$class.$fs.' ';
	    }
	    // Input entered data size limitation
	    if (is_numeric($max)) {
		$chaine .=   'maxlength='.$fs.$max.$fs.' ';
	    }
	    // Input field length
	    if (is_numeric($size)) {
		$chaine .=   'size='.$fs.$size.$fs.' ';
	    }
	    // Input fill in with value
	    if ($value != '') {
		$chaine .=  'value='.$fs.$value.$fs.' ';
	    }
	    else {
		$chaine .= 'value='.$fs.$fs.' ';
	    }
	    // Ad other element into the tag
	    if ($autre != '') {
		$chaine .=  $autre." ";
	    }
	    $chaine .= '/>';
	}
	else {
	    Logg::error('HTML::FORM::HTFORM_Input::no_tag_name',FALSE);
	}

	return $chaine;
    }



    /**
     * Generate clean input tag filled with appropriate information.
     * @param string $type Specify type of input '',text','hidden','submit','Password
     * @param string $name Tag name *
     * @param string $class CSS class
     * @param int $max max data size
     * @param int $size Input size
     * @param string $value Default value
     * @param string $autre Free information
     * @return string Filled html input tag
     */
    private function inputDateTag($type, $name, $value = '', $dateFormat = '', $class = '', $imgPath = "../", $autre = "") {
	if ($name != '') {
	    $type = (in_array($type,array('text','hidden'))) ? $type : 'text';
	    $dateFormat = ($dateFormat != '') ? $dateFormat : '%d/%m/%Y';
	    $button_id = substr(md5(time().rand(0,10000)),0,12);

	    $chaine = '<input type="'.$type.'" name="'.$name.'" id="'.$name.'ID" class="'.$class.' icon" value="'.$value.'" READONLY '.$autre.'/>
				<a href="#" title="Calendrier" id="'.$button_id.'"><img src="'.$imgPath.'img/calendar.png" alt="calendrier" /></a>
				<script type="text/javascript">
					Calendar.setup({
						inputField	:	"'.$name.'ID",
						dateFormat	:	"'.$dateFormat.'",
						trigger	:	"'.$button_id.'",
						onSelect : function() {this.hide();}
					});
				</script>';
	}
	else {
	    Logg::error('HTML::FORM::HTFORM_Input::no_tag_name',FALSE);
	}
	return $chaine;
    }

    /**
     * Generate clean textarea tag filled with appropriate information
     * @param string $text Default content
     * @param string $name Tag name *
     * @param string $class CSS class
     * @param int $max Max data size
     * @param float $cols Horizontal size
     * @param float $rows Vertical size
     * @param string $autre Free information
     * @return string Filled html textarea tag
     */
    private function textareaTag($name = "text", $text = "", $class = "", $cols = "", $rows = "", $autre = "", $max = "") {
	if ($name != '') {
	    $chaine = '<textarea ';
	    // Textarea field name
	    if ($name != '') {
		$chaine .=  'name="'.$name.'" ';
	    }
	    // Textarea CSS class
	    if ($class != '') {
		$chaine .=  'class="'.$class.'" ';
	    }
	    // Textarea field length
	    if ($cols != '') {
		$chaine .=   'cols="'.$cols.'" ';
	    }else {
		$chaine .=   'cols="'.$GLOBALS['HTML']['textarea_cols'].'" ';
	    }
	    // Textarea field height
	    if ($rows != '') {
		$chaine .=   'rows="'.$rows.'" ';
	    }else {
		$chaine .=   'rows="'.$GLOBALS['HTML']['textarea_row'].'" ';
	    }
	    // Input entered data size limitation
	    if (is_numeric($max)) {
		$chaine .=   'maxlength="'.$max.'" ';
	    }
	    // Ad other element into the tag
	    if ($autre != '') {
		$chaine .=  $autre." ";
	    }
	    $chaine .=  ">";
	    // Textarea field value
	    if ($text != '') {
		$chaine .=  $text;
	    }
	    $chaine .= '</textarea>';
	}
	else {
	    error ('HTML:TAG_GENERATOR:TEXTAREA:no_tag_name',FALSE);
	}
	return "\n".$chaine."\n";
    }


    /**
     * Generate clean select tag filled with appropriate information.
     * @param  string $champ	content array with information
     * @param  string $name	tag name
     * @param  string $id_select Selected ID (default = create and select a bank line)
     * @param  string $class  	CSS class
     * @param  string $autre	free information
     * @return string filled html select tag
     */
    private function selectTag($name,$champ,$id_select="",$class="",$autre="",$withBlank = TRUE) {
	if ($name != '') {
	    $chaine_class =  '';
	    $chaine = "<select ";
	    // Select  field name
	    if ($name != '') {
		$chaine .=  'name=\''.$name.'\' ';
	    }
	    // Select  CSS class
	    if ($class != '') {
		$chaine .=  'class=\''.$class.'\' ';
		$chaine_class =  ' class=\''.$class.'\' ';
	    }
	    // Ad other element into the select tag
	    if ($autre != '') {
		$chaine .=  $autre." ";
	    }
	    $chaine .=  ">";
	    // Select  detect select row
	    if ($withBlank) {
		if ($id_select == '') {
		    $chaine .=  "<option".$chaine_class." value='' selected='selected'> </option>";
		}
		else {
		    $chaine .=  "<option".$chaine_class." value=''> </option>";
		}
	    }
	    // Loop for values
	    if(is_array($champ))
		foreach( $champ as $key => $val ) {
		    $chaine .= "<option".$chaine_class." value='".$key."'";
		    if (($id_select == $key)and($id_select != "")) {
			$chaine .= ' selected="selected"';
		    }
		    //$chaine .= ">".htmlentities($val)."</option>";
		    $chaine .= ">".$val."</option>";
		}
	    $chaine .= "</select>";
	}
	else {
	    Logg::error('HTML::AUTO_TAG_GENERATOR::SELECT_TAG::no_tag_name',FALSE);
	    return 'erreur select';
	}

	return $chaine;
    }

    private function selectTVATag($name, $champ = array(), $id_select="", $class = "", $autre = "", $withBlank = true) {
	$autre .= ' onblur="finishEditing();" onclick="beginEditing(this);" ';
	$champ = array('0' => '0', '5.5' => '5,5', '8.5' => '8,5 (DOM-TOM)', '19.6' => '19,6');
	return $this->selectTag($name, $champ, $id_select, $class, $autre, $withBlank);
    }
}

/**
 * Classe qui génère les formulaires pour Zuno
 * @author Nicolas Mannocci
 * @version 1.0
 */
class ZunoForm {
    private $listeInputsHidden;
    private $id;
    private $nom;
    private $action;
    private $method;
    private $listeBlock;
    private $lastBlock;
    private $form;
    private $retour;
    private $pied;
    private $droit;

    /**
     * Constructeur de la classe
     * @param string $nom	Le nom du formulaire
     * @param string $action	La page php à appeller lorsque le formulaire est envoyé
     * @param string $method	La méthode d'envoi du formulaire
     */
    public function __construct($nom, $action="#", $method = "POST", $id = "") {
	$this->id = $id;
	$this->nom = $nom;
	$this->action = $action;
	$this->method = $method;
	$this->retour = 'id'.$nom.'formAutoRetour';
	$this->pied = '';
	$this->droit = true;
    }
    /**
     * Méthode qui change la valeur de droit par défault à true
     * @param bool $value Vrai pour vérifier les droits
     */
    public function setDroit($value = true) {
	$this->droit = $value;
    }

    /**
     * Méthode generateForm qui génère le formulaire
     * @return string Le formulaire généré
     */
    public function generateForm() {
	if($this->id != '') $addId = ' id="'.$this->id.'"';
	$this->form = '<form name="'.$this->nom.'" action="'.$this->action.'" method="'.$this->method.'"'.$addId.'>';
	if(is_array($this->listeInputsHidden))
	    foreach($this->listeInputsHidden as $v) {
		$this->form .= $v;
	    }
	if(is_array($this->listeBlock)) {
	    $this->form .= '<div id="'.$this->retour.'">';
	    foreach($this->listeBlock as $v) {
		$this->form .= $v;
	    }
	    $this->form .= '</div>';
	}

	$this->form .= '</form>';
	return $this->form;
    }

    /**
     * Méthode newBlock génère un block avec un ou plusieurs fieldsets dedans
     * @param string $contenu	Contient ce qui va dans le block
     * @param int $taille	Indique si le block prend 50%, 33% ou 25% de l'écran
     */
    public function newBlock($contenu, $taille = '33') {
	if($taille != '50' and $taille != '25' and $taille != '100')
	    $taille = '33';
	if($taille != '100')
	    $this->lastBlock = '<div class="block width'.$taille.'">';
	else
	    $this->lastBlock = '<div class="block">';
	$this->lastBlock .= $contenu;
	$this->lastBlock .= '</div>';
	$this->listeBlock[] = $this->lastBlock;
    }

    /**
     *Fonction newTableauBlock insère un tableau généré dans la liste des blocks
     * @param string $tableau      Le code HTML d'un tableau généré
     */
    public function newTableauBlock($tableau) {
	$this->lastBlock = $tableau;
	$this->listeBlock[] = $this->lastBlock;
    }

    /**
     * Fonction getLastBlock qui donne le dernier block généré par la classe
     * @return string Le dernier block généré
     */
    public function getLastBlock() {
	return $this->lastBlock;
    }

    /**
     * Fonction getBlocks qui donne tous les blocks générés par la classe
     * @return array La liste des blocks générés
     */
    public function getBlocks() {
	return $this->listeBlock;
    }

    /**
     * Fonction newInputHidden permet d'ajouter un champ hidden au formulaire
     * @param string $name Le nom du champ
     * @param string $value La valeur initiale du champ
     * @param string $id Un éventuel ID au champ
     * @param string $autre D'autres tags si nécessaire
     */
    public function newInputHidden($name, $value, $id='', $autre = '') {
	$input = '<input type="hidden" name="'.$name.'" value="'.$value.'"';
	if($id != '')
	    $input .= ' id="'.$id.'" ';
	$input .= ' '.$autre.' />';
	$this->listeInputsHidden[] = $input;
    }

    /**
     * Fonction getInputs qui ressorts en code HTML les inputs hidden du formulaire
     * @return string Le code HTML des inputs hidden.
     */
    public function getInputs() {
	$out = '';
	if(is_array($this->listeInputsHidden))
	    foreach($this->listeInputsHidden as $v)
		$out .= $v;
	return $out;
    }

    /**
     * Fonction qui génère les bouttons d'envoi et de réinitialisation du formulaire
     * @param string $retour Précise l'id de retour si nécessaire
     * @param string $annuler Permet de modifier le titre du boutton d'anulation
     * @param string $valider Permet de modifier le titre du boutton de validation
     * @param string $popup Indique si on est en popup à fermer ou pas
     * @param string $plus Permet de rajouter des éléments sur l'image d'anulation
     * @return string Le code HTML du pied de formulaire
     */
    public function generateButtons($retour = '', $annuler = 'Annuler', $valider = 'Valider', $popup = '', $plus = 'onload="autoCompletage();"') {
	$close = '';
	if($retour == '')
	    $retour = $this->retour;
	if($popup == 'popup2' or $popup == 'popup') {
	    $popup = ',\''.$popup.'\'';
	    $close = 'onclick = "zuno.popup.close();"';
	}
	else
	    $popup = '';
	$pied = '<a title="Reset" href="javascript:document.'.$this->nom.'.reset();" '.$close.' ><img '.$plus.' align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> '.$annuler.'</a>';
	if($this->droit)
	    $pied .= '<a name="submit" onclick="zuno.business.formTools.sendFormAjah(\''.$this->nom.'\', \''.$this->action.'\',\''.$retour.'\''.$popup.');" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> '.$valider.'</a>';
	$this->pied = $pied.$this->pied;
	return $this->pied;
    }

    /**
     * Fonction qui permet de rajouter un boutton ajax au formulaire
     * @param string $nom Le nom du boutton
     * @param string $action L'action ajax à effectuer
     * @param string $srcImage Un lien html vers une image (optionnel)
     */
    public function addButton($nom, $action, $srcImage='') {
	if($srcImage=='')
	    $img = '';
	else
	    $img = '<img alt="Image" src="'.$srcImage.'" />';
	$this->pied .= '<a name="'.$nom.'" onclick="'.$action.'" style="cursor: pointer;">'.$img. ' '.$nom.'</a>';
    }

    /**
     * Fonction qui génère des bouttons avec href et sans onClick
     * @param string $annuler Le texte du boutton annuler
     * @param string $valider Le texte du boutton de validation
     * @return string Le code HTML du pied de formulaire
     */
    public function generateButtonsNoAjax($annuler = 'Annuler', $valider = 'Valider') {
	$pied = '<a title="Reset" href="javascript:document.'.$this->nom.'.reset();"  ><img onload="autoCompletage();" align="middle" title="Effacer" alt="Effacer" src="../img/prospec/cancel.png"/> '.$annuler.'</a>';
	$pied .= '<a name="submit" href="javascript:document.'.$this->nom.'.submit();" style="cursor: pointer;"><img align="middle" title="Enregistrer" alt="Enregistrer" src="../img/prospec/record.png"/> '.$valider.'</a>';
	$this->pied = $pied.$this->pied;
	return $this->pied;
    }
}

/**
 * Classe qui génère des tableaux HTML pour Zuno
 * @author Nicolas Mannocci
 * @version 1.0
 */
class ZunoTableau {
    private $titreLigne;
    private $sousTitreLigne;
    private $nbSousTitre;
    private $nbColonnes;
    private $lignes;
    private $alternance;
    private $pied;
    private $autoLink;
    private $autoLinkToDo;
    private $lien;
    private $rendu;

    /**
     * Constructeur de la classe. Il initialise les variables
     */
    function __construct() {
	$this->nbSousTitre = 0;
	$this->titreLigne = '';
	$this->sousTitreLigne = '';
	$this->alternance = 0;
	$this->nbColonnes = 0;
	$this->autoLink = false;
	$this->autoLinkToDo = false;
	$this->lien = '';
    }

    /**
     * Fonction ligneTitre qui prépare la ligne du haut d'un tableau
     * @param array $champ tableau des noms des différentes colonnes
     * @param boolean $sousTitre Indique s'il doit prévoir la place pour des sous titres
     */
    public function ligneTitre($champs = array(), $sousTitre = false, $champsId = array(), $orderJsFunction = '',$suffixLink = '../',$order = '',$orderSens = 'DESC',$limit = 30) {
	$this->titreLigne = '<tr class="titre">';
	if($sousTitre)
	    $rowspan = ' rowspan="2" ';
	else $rowspan = '';
	if(is_array($champs))

	    $generalView = new generalView;
	foreach($champs as $k => $v) {
	    if($v == 'small')
		$width = ' style="width:1%;white-sace: nowrap"';
	    else $width = ' ';

	    if($orderJsFunction != '' and array_key_exists($k, $champsId))
		$k = $generalView->tagLinkOrderSwitch($k,$champsId[$k],$order,$orderSens,$limit,$orderJsFunction,$suffixLink);

	    if(is_numeric($v) && $sousTitre) {
		$ligne = '<th '.$width.' colspan="'.$v.'">'.$k.'</th>';
		$this->nbSousTitre += $v;
		$this->nbColonnes += $v;
	    }

	    else {
		$ligne = '<th'.$width.$rowspan.'>'.$k.'</th>';
		$this->nbColonnes++;
	    }

	    $this->titreLigne .= $ligne;
	}
	$this->titreLigne .= '</tr>';
    }

    /**
     * Fonction ligneSousTitre qui génère les sous titres du tableau
     * @param array $champs Les noms des colonnes
     */
    public function ligneSousTitre($champs = array()) {
	if(is_array($champs) and $this->nbSousTitre = count($champs)) {
	    $this->sousTitreLigne = '<tr class="subtitre">';
	    foreach($champs as $k => $v) {
		$this->sousTitreLigne .= '<th width="5%" nowrap="nowrap">'.$k.'</th>';
	    }
	    $this->sousTitreLigne .= '</tr>';
	}
    }

    /**
     * Fonction ligneTableau qui génère une ligne du tableau
     * @param array $champs Le contenu des différentes cellules d'une ligne du tableau
     * @param string $id Champ obligatoire si autoHrefLigneID appelé, il doit contenir l'id sur qui pointer
     */
    public function ligneTableau($champs = array(), $id = '') {
	if(is_array($champs) and count($champs) == $this->nbColonnes) {
	    $ligne = '<tr class="altern'.$this->alternance.'">';
	    foreach($champs as $k => $v) {
		if(substr($k,0,4) == 'okou')
		    $k = substr($k,5, strlen($k));
		if($this->autoLink and !$this->autoLinkToDo and strpos($k, 'input') === false)
		    $val = $this->lien.$k.'</a>';
		elseif($this->autoLink and $this->autoLinkToDo and strpos($k, 'input') === false)
		    $val = $this->lien.$id.'" >'.$k.'</a>';
		else
		    $val = $k;
		if($v == '')
		    $class = "barre";
		elseif(substr($v,0,1) == "+")
		    $class = "barre ".substr($v,1,strlen($v));
		else
		    $class = $v;
		$ligne .= '<td class="'.$class.'" nowrap="nowrap">'.$val.'</td>';
	    }
	    $ligne .= '</tr>';
	    $this->lignes[] = $ligne;
	    $this->alternance = ($this->alternance+1)%2;
	}
    }

    /**
     * Fonction hrefLigne qui permet de mettre automatiquement un lien href sur toutes les cellules
     * @param string $lien le lien vers la page à pointer si on clique sur les champs du tableaux
     */
    public function hrefLigne ($lien) {
	$this->lien = '<a href="'.$lien.'">';
	$this->autoLink = true;
    }
    /**
     * Fonction autoHrefLigneID qui prépare un lien href vers une page en fonction de l'id
     * @param string $page	Le nom de la page vers laquelle pointer.
     * @param string $nomparam	Le nom du paramètre qui serra passé à la page
     */
    public function autoHrefLigneID($page, $nomparam = 'id') {
	$this->lien = '<a href="'.$page.'?'.$nomparam.'=';
	$this->autoLink = true;
	$this->autoLinkToDo = true;
    }

    /**
     * Fonction generateTableau qui génère l'HTML du tableau
     * @param int $width La largeur du tableau, par défaut 100
     * @return string Le code HTML du tableau
     */
    public function generateTableau($width = '100') {
	$this->rendu = '<div class="blockTable">';
	$this->rendu .= '<table width="'.$width.'" cellspacing="0" cellpadding="2" border="0"><tbody>';
	$this->rendu .= '<tbody>';

	$this->rendu .= $this->titreLigne;
	if($this->nbSousTitre > 0)
	    $this->rendu .= $this->sousTitreLigne;
	if(is_array($this->lignes))
	    foreach($this->lignes as $v)
		$this->rendu .= $v;

	$this->rendu .= '</tbody></table></div>';

	return $this->rendu;
    }
}

/**
 * Classe qui génère un formulaire d'upload de fichier
 * @author Nicolas Mannocci
 * @version 1.0
 */
class ZunoFormUpload {
    private $form;
    private $ligne;
    private $action;
    private $method;
    private $nom;
    private $retour;
    private $legend;

    /**
     * Constructeur de la classe
     * @param string $nom Le nom du formulaire
     * @param string $action L'action que doit accomplir le formulaire
     * @param string $method La méthode de transfert des informations
     */
    function __construct($nom, $action="gnose/Upload.php", $method="POST") {
	$this->action = $action;
	$this->method = $method;
	$this->nom = $nom;
	$this->form = '';
	$this->ligne = '';
	$retour = '';
    }

    /**
     * Méthode qui créé la ligne du formulaire avec le champ input
     * @param string $path Le chemin du dossier dans lequel devra être posé le fichier
     */
    public function setLigne($path) {
	$this->ligne = '<input type="file" name="fichier" />';
	$this->ligne .= '<input type="hidden" name="path" value="'.$path.'" />';
    }

    /**
     * Précise quelle page sera appellée une fois le transfert terminé
     * @param string $retour L'adresse de la page à appeller
     */
    public function setRetour($retour) {
	if($retour != '') {
	    $this->retour = '<input type="hidden" name="retour" value="'.$retour.'" />';
	}
    }

    /**
     * Méthode pour initialiser la légende
     * @param string $legend La légende à afficher
     */
    public function setLegend($legend) {
	$this->legend = '<legend>'.$legend.'</legend>';
    }

    /**
     * Méthode qui génère le code html complet du formulaire
     * @param string $width La taille du block en % (25,33,50)
     * @return string Le code HTML généré
     */
    public function generateForm($width) {
	$this->form = '<form METHOD="'.$this->method.'" enctype="multipart/form-data" name="form'.$this->nom.'" action="'.$this->action.'">';

	$this->form .= '<div class="block width'.$width.'">';
	$this->form .= '<fieldset class="form">'.$this->legend;
	$this->form .= '<div class="row"><div class="label"><input type="submit" name="submit" value="Envoyer" /></div>';
	$this->form .= '<div class="field">'.$this->ligne.'</div></div>';

	$this->form .= $this->retour;
	$this->form .= '</fieldset></div></form>';
	return $this->form;
    }
}
?>
