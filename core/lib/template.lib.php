<?php
/*#########################################################################
#
#   name :       template.inc
#   desc :       library for templating processing
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| ProcessTemplating
|
| process template completion
+-------------------------------------------------------------------------+
| $template	*   Template URL
| $input	*   Array with pattern to find
| $output	*   Array with pattern to replace
+-------------------------------------------------------------------------+
| return template document with filled data
+------------------------------------------------------------------------*/
function ProcessTemplating ($template,$input,$output) {
    if (isset($GLOBALS['TmpTemplate'][$template])) {
	return preg_replace($input,$output,$GLOBALS['TmpTemplate'][$template]);
    }
    else {
	if (!($fp = @fopen($template, "r"))) {
	    Logg::loggerError('ProcessTemplating() ~ mauvais nom de template '.$template,'',__FILE__.'@'.__LINE__);
	}
	else {
	    $data = fread($fp, filesize($template));
	    fclose($fp);
	    $GLOBALS['TmpTemplate'][$template] = $data;
	    return preg_replace($input, $output, $data);
	}
    }
}

/*------------------------------------------------------------------------+
| templating
|
| find template, transform input pattern and process template
+-------------------------------------------------------------------------+
| $template	*   Template name
| $input	*   Array with pattern identifier
| $output	*   Array with data to fill
+-------------------------------------------------------------------------+
| return completed template filled with data
+------------------------------------------------------------------------*/
function templating ($template,$io = "") {
    $url_temp = $GLOBALS['REP']['appli'].$GLOBALS['REP']['template']."defaut/".$template.$GLOBALS['EXT']['template'];
    if ($_SESSION["language"] == $GLOBALS['LANGUE']['default']) {

    }
    else {
	$url_testLang = $GLOBALS['REP']['appli'].$GLOBALS['REP']['template'].$_SESSION["language"]."/".$template.$GLOBALS['EXT']['template'];
	if (@$fp = fopen($url_testLang, "r")) {
	    $url_temp = $url_testLang;
	}
    }

    if(is_array ($io)) {
	$input = array();
	$output= array();
	foreach ($io as $in => $out) {
	    $input[] = "/\#\#\#\[".$in."\]\#\#\#/";
	    $output[] = $out;
	}
	return ProcessTemplating($url_temp,$input,$output);
    }
    else {
	if (!($fp = fopen($url_temp, "r"))) {
	    Logg::loggerError('templating() ~ mauvais nom de fichier template '.$template,'',__FILE__.'@'.__LINE__);
	}
	else {
	    $data = fread($fp, filesize($url_temp));
	    fclose($fp);
	    return $data;
	}
    }
}


?>
