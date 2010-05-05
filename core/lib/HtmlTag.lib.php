<?php
/*#########################################################################
#
#   name :       HtmlTag.inc
#   desc :       library for HTML form creation
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/



/**
 * Convert special chars & " ' < > into entities
 * replace htmlentities fonction available in php4
 * @param $string content to analyse
 * @return cleaned string
 */
function SXhtmlentities($string, $option = ENT_QUOTES) {
    if (($option == 'ONLY_QUOTES')or($option == 'ONLY_DBQUOTES')) {
        $option1 = ENT_QUOTES;
    }
    else {
        $option1 = $option;
    }
    $trans = get_html_translation_table(HTML_SPECIALCHARS, $option1);
    if (($option == 'ONLY_QUOTES')or($option == 'ONLY_DBQUOTES')) {
        unset($trans['<']);
        unset($trans['>']);
        if ($option == 'ONLY_QUOTES') {
            unset($trans['"']);
        }
        elseif ($option == 'ONLY_DBQUOTES') {
            unset($trans["'"]);
        }
    }

    return strtr($string, $trans);
}

/**
 * Add an image.
 * @param $img url of the image
 * @param $alt comment
 * @param $align alignement
 * @param $css css style
 * @param $name name
 * @return filled html image tag
 */
function imageTag($img, $alt = '', $align= '', $css= '', $name= 'img') {
    if ($alt == '')
        $alt = $img;
    if (	($align == 'left')or
            ($align == 'right')or
            ($align == 'center')or
            ($align == 'top')or
            ($align == 'middle')or
            ($align == 'baseline')or
            ($align == 'bottom')	)
        $align =' align="'.$align.'"';
    if ($css != '')
        $css = ' class="'.$css.'"';

    return '<img src="'.$img.'" name="'.$name.'" alt="'.$alt.'" title="'.$alt.'"'.$css.$align.'/>';
}

/**
 * Add a button.
 * @param $page page to link
 * @param $hidden caracteristic
 * @param $class css style
 * @param $titre name of the button
 * @return filled html button tag
 */
function buttonTag($page,$hidden,$class, $titre = 'modifier', $name = 'action') {
    if($page != '') {
        if($class == 'inline') {
            $class1  =  $class;
            $class   =  '';
        }
        $left  =  '<form method="post" action="'.$page.'" class="'.$class1.'">';
        $right =  '</form>';
    }
    if(is_array($hidden)) {
        foreach ($hidden as $key => $val) {
            $html_hid .= '<input type="hidden" name="'.$key.'" value="'.$val.'">';
        }
    }
    return $left.$html_hid.'<input type="submit" name="'.$name.'" class="'.$class.'" value="'.$titre.'">'.$right;
}


/**
 * Generate clean input tag filled with appropriate information.
 * @param $type			Specify type of input '',text','hidden','submit','Password
 * @param $name			tag name *
 * @param $class		CSS class
 * @param $max 			max data size
 * @param $size 		input size
 * @param $value		default value
 * @param $autre		free information
 * @return filled html input tag
 */
function inputTag($type, $name, $class = '', $max = '', $size = '', $value = '', $autre = "", $fs = '"') {
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
 * @param $type			Specify type of input '',text','hidden','submit','Password
 * @param $name			tag name *
 * @param $class		CSS class
 * @param $max 			max data size
 * @param $size 		input size
 * @param $value		default value
 * @param $autre		free information
 * @return filled html input tag
 */
function inputDateTag($type, $name, $value = '', $dateFormat = '', $class = '', $imgPath = "../", $autre = "") {
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
 * @param $text		  	default content
 * @param $name		 	tag name *
 * @param $class	  	CSS class
 * @param $max		  	max data size
 * @param $cols		  	horizontal size
 * @param $rows		  	vertical size
 * @param $autre	  	free information
 * @return filled html textarea tag
 */
function textareaTag($name = "text", $text = "", $class = "", $cols = "", $rows = "", $autre = "", $max = "") {
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
 * @param  $champ		content array with information *
 * @param  $name		tag name *
 * @param  $id_select	  	Selected ID (default = create and select a bank line)
 * @param  $class	  	CSS class
 * @param  $autre	  	free information
 * @return filled html select tag
 */
function selectTag($name,$champ,$id_select="",$class="",$autre="",$withBlank = TRUE) {
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
                $chaine .= ">".htmlentities($val, ENT_COMPAT, 'UTF-8')."</option>";
            }
        $chaine .= "</select>";
    }
    else {
        Logg::error('HTML::AUTO_TAG_GENERATOR::SELECT_TAG::no_tag_name',FALSE);
        return 'erreur select';
    }

    return $chaine;
}

function selectTVATag($name, $champ = array(), $id_select="", $class = "", $autre = "", $withBlank = false) {
    $autre = ' onblur="finishEditing();" onclick="beginEditing(this);" '.$autre;
    $champ = array('0' => '0', '5.5' => '5,5', '8.5' => '8,5 (DOM-TOM)', '19.6' => '19,6');
    return selectTag($name, $champ, $id_select, $class, $autre, $withBlank);
}

/**
 * Add a link.
 * @param $url			url of the link
 * @param $texte		text of the link
 * @param $class		css style
 * @param $title		title
 * @param $autres_elements	other param
 * @return filled html link tag
 */
function linkTag($url, $texte = '', $class = '', $title = '', $autres_elements = '') {
    $chaine = '<a href="';

    if ($url == '') {
        $chaine .= '#" ';
    }
    else {
        $chaine .=  $url.'" ';
    }

    if ($title != '') {
        $chaine .=  'title="'.$title.'" ';
    }
    else {
        $chaine .=  'title="'.$url.'" ';
    }

    if ($class != '') {
        $chaine .=  'class="'.$class.'" ';
    }
    if ($autres_elements != '') {
        $chaine .=  $autres_elements.' ';
    }

    $chaine .= '>';

    if ($texte == '') {
        $chaine .=  $url.'</a>';
    }
    else {
        $chaine .= $texte.'</a>';
    }

    return $chaine;
}

?>
