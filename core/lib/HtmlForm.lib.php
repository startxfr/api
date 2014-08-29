<?php
/*#########################################################################
#
#   name :       HtmlForm.inc
#   desc :       library for HTML form creation
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Create portlet with 3 box flick-flack
 * @param $titre String: Title for first side
 * @return HTML portlet ready to insert
 */
function generateZBox3($titre,$titre1 = '',$titre_off = '', $corps = '', $pied = '', $corps1 = '', $pied1 = '', $id_div = '', $etat = '', $forceEtat = false) {
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
    if ($id_div == '')	$id_div = md5(rand(1000).time());
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

    return templating('Box3', $tag);
}


/**
 * Create portlet with 2 box Open or closed
 * @param $titre String: Title for first side
 * @return HTML portlet ready to insert
 */
function generateZBox($titre, $titre_off = '', $corps = '', $pied = '', $id_div = '', $etat = '', $forceEtat = false) {
    if ($etat =='close' or $etat =='none')
	$newState ='close';
    else $newState ='open';

    if($forceEtat)
	$state = $_SESSION['ZBoxState'][$id_div] = $newState;
    elseif(array_key_exists('ZBoxState',$_SESSION) and array_key_exists($id_div,$_SESSION['ZBoxState']))
	$state = $_SESSION['ZBoxState'][$id_div];
    else $state = $newState;

    if ($id_div == '')	$id_div = md5(rand(0,1000).time());
    if ($pied != '') 		$pied ='<div class="footer"><div class="content">'.$pied.'</div></div>';

    $tag['id_div'] 	= $id_div;
    $tag['titre'] 	= $titre;
    $tag['corps'] 	= $corps;
    $tag['pied']	= $pied;
    $tag['state'] 	= $state;

    return templating('Box', $tag);
}


/**
 * Create contextBox with header and body
 * @param $linkText String: text to link this contextBox
 * @param $body String: body of the context box
 * @param $title String: Title for this context box
 * @return HTML Span Link ready to be used with JS BoxOver
 */
function ContextBox($linkText,$body,$title="") {
    $BoxOverCmd = '';
    if ($title !='')
	$BoxOverCmd .='header=['.$title.'] ';
    $BoxOverCmd .='body=['.$body.'] ';
    return "<span title=\"".$BoxOverCmd." cssheader=[ContextBoxHeader] cssbody=[ContextBoxContent]\" class=\"ContextBoxLink\">".$linkText."</span>";
}







/**
 * Generate html form.
 * Toolkit to use for creting HTML form or HTML form element if
 * accessing without creating a object
 */

class HtmlForm {
    /**
     * Contain the whole html code of the form.
     * all add* functions add their code to this variable
     */
    var $output;

    function __construct() {
    }

    /**
     * Add an internet link.
     * @param $uri the url which have to be linked
     * @param $text to use for this link
     * @param $class css style
     * @param $title title
     * @param $autres_elements other param
     */
    static function addLink($uri,$text,$class='',$title='',$autres_elements='') {
	if ($uri != '') {
	    $output = linkTag($uri,$text,$class,$title,$autres_elements);
	    if(isset($this))
		$this->output .= $output;
	    return $output;
	}
    }

    /**
     * Add a button.
     * @param $page page to link
     * @param $hidden caracteristic
     * @param $class css style
     * @param $titre name of the button
     */
    static function addButton($page,$hidden,$class,$titre='modifier') {
	$output = buttonTag($page,$hidden,$class,$titre);
	if(isset($this))
	    $this->output .= $output;
	return $output;
    }

    /**
     * Add list to select hour
     * @param $nom name
     * @param $select default value
     * @param $class css style
     * @param $min min hour
     * @param $max max hour
     */
    static function addSelectHeure($nom='heure',$select='',$class='',$min='',$max='') {
	if ( $min == '' || $max == '' ) {
	    $min = $GLOBALS['HOURS']['min'];
	    $max = $GLOBALS['HOURS']['max'];
	}

	$j=0;
	for ( $i=$min; $i<=$max; $i++) {
	    if ($i < 10)
		$is = "0".$i;
	    else  $is = $i;
	    $champ[$is.":00"] = $is.":00";
	    $champ[$is.":30"] = $is.":30";
	}

	$output = selectTag($nom,$champ,$select,$class,'');
	if(isset($this))
	    $this->output .= $output;
	return $output;
    }

    /**
     * Add list to select civility
     * @param $nom name
     * @param $select default value
     * @param $class css style
     */
    static function addSelectCiv($nom='civ',$select='',$class='',$withBlank = false) {
	if(is_array($GLOBALS['CIV_'.$_SESSION["language"]]))
	    $civToUse = $GLOBALS['CIV_'.$_SESSION["language"]];
	else $civToUse = $GLOBALS['CIV_'.$GLOBALS['LANGUE']['default']];
	foreach ($civToUse as $key => $val) {
	    $champ[$key] = $val;
	    if ($select == $key) {
		$select = $key;
	    }
	}

	$output = selectTag($nom,$champ,$select,$class.' civ',$withBlank);
	if(isset($this))
	    $this->output .= $output;
	return $output;
    }

    /**
     * Add list to select language
     * @param $nom name
     * @param $select default value
     * @param $class css style
     */
    static function addSelectLang($nom='lang', $select='', $class='') {
	if(is_array($GLOBALS['LANGUE_'.$_SESSION["language"]]))
	    $langToUse = $GLOBALS['LANGUE_'.$_SESSION["language"]];
	else  $langToUse = $GLOBALS['LANGUE_'.$GLOBALS['LANGUE']['default']];
	foreach ($langToUse as $key => $val) {
	    $champ[$key] = $val;
	    if ($select == $key) {
		$select = $key;
	    }
	}

	$output = selectTag($nom,$champ,$select,$class,'');
	if(isset($this))
	    $this->output .= $output;
	return $output;
    }

    /**
     * Create select liste according to the given table
     * @param $name String: name of the select tag
     * @param $table String: table to get data from
     * @param $id_select String: Contact ID to select
     * @param $class String: CSS style to apply
     * @param $autre String: Other information to push into this tag
     * @param $req_add String: aditional SQL Query
     * @return HTML Select list
     */
    static function addAutoSelect($name,$table,$id_select = "",$class = "",$autre = "",$req_add = "",$DBPool = 1,$withBlank = TRUE) {
	if ($name != '') {
	    $bddtmp = new Bdd();
	    $tableDesc = $bddtmp->AnalyseTableStructure($table,'');
	    if ($req_add == '') $req_add = ' ORDER BY '.$tableDesc['titre'].' ASC';
	    if ($tableDesc['color'] != '') $req_addColor = ','.$tableDesc['color'];
	    $bddtmp->makeRequeteFree('SELECT '.$tableDesc['key'].','.$tableDesc['titre'].$req_addColor.' FROM '.$table.' '.$req_add);
	    $res = $bddtmp->process();
	    if (count($res) > 0) {
		foreach ($res as $key => $data) {
		    $champ[$data[$tableDesc['key']]] = $data[$tableDesc['titre']];
		    $champC[$data[$tableDesc['key']]] = ($tableDesc['color'] != '') ? $data[$tableDesc['color']] : '';
		}
	    }
	    if ($name == 'DISPLAY') {
		$output = $champ[$id_select];
	    }
	    else {
		if (count($champ) == 0) {
		    $output = '<i>pas de résultat trouvé pour la table '.$table.'</i>';
		}
		else {
		    $chaine_class =  '';
		    $chaine = "<select ";
		    if ($name != '') {
			$chaine .=  'name=\''.$name.'\' ';
		    }
		    if ($class != '') {
			$chaine .=  'class=\''.$class.'\' ';
			$chaine_class =  ' class=\''.$class.'\' ';
		    }
		    if ($autre != '') {
			$chaine .=  $autre." ";
		    }
		    $chaine .=  ">";
		    if ($withBlank) {
			if ($id_select == '') $chaine .=  "<option".$chaine_class." value='' selected=\"selected\"> </option>";
			else $chaine .=  "<option".$chaine_class." value=''> </option>";
		    }
		    foreach( $champ as $key => $val ) {
			$chaine .= "<option".$chaine_class." value='".$key."'";
			if (($id_select == $key)and($id_select != "")) $chaine .= ' selected="selected"';
			if ($champC[$key] != '') $chaine .= ' style="background-color: #'.$champC[$key].'"';
			$chaine .= ">".$val."</option>";
		    }
		    $chaine .= "</select>";
		    $output = $chaine;
		}
	    }
	}
	if(isset($this)) {
	    $this->output .= $output;
	}
	return $output;
    }

    /** echo the html code of the form
     * @param $action page which receve form
     * @param $name name
     * @param $form if true echo begin and end of html code of form
     * @param $methode define html method used by the form
     */
    function process($action = "", $name = 'formulaire', $form = TRUE, $methode = 'post') {
	if ($form) {
	    echo "<form method=".$methode." action=".$action." name=".$name.">" ;
	}
	echo $this->output ;
	if ($form) {
	    echo "</form>" ;
	}
    }

}


?>
