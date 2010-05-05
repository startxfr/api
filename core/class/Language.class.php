<?php
/*#########################################################################
#
#   name :       Language.inc
#   desc :       library for language management
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for language management and dockable content relative to language switch
 * This class is use for loading appropriate language file, display language flag
 * as defined in language.ini
 */
class Language {
    /**
     * Analyse client authorized language and application authorized language.
     * The first client language to match server authorized is recorded in session
     * and appropriate initLanguageFile is loaded
     */
    static function LanguageDetect() {
	if ($_SESSION["language"] == "") {
	    $localebrowsertmp = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
	    foreach($localebrowsertmp as $val) {
		$val = strtolower($val);
		$tmp = $val{0}.$val{1};
		$localebrowser[$tmp] = $tmp;
	    }
	    $listesupported = explode(",",$GLOBALS['LANGUE']['supported']);
	    $langok1 = array_intersect($localebrowser, $listesupported);
	    $langok = each($langok1);
	    if (count ($langok) > 0) {
		$_SESSION["language"] = $langok[0];
	    }
	    else {
		$_SESSION["language"] = $GLOBALS['LANGUE']['default'];
	    }
	}
	Logg::loggerInfo('Language::LanguageDetect() ~ detection du language '.$_SESSION["language"],'',__FILE__.'@'.__LINE__);
	Language::LoadInit($_SESSION["language"]);
    }

    /**
     * switch actual session language to the given new language and load appropriate init file
     */
    static function LanguageSwitch($newlang) {
	$listesupported = explode(",",$GLOBALS['LANGUE']['supported']);
	foreach ($listesupported as $val) {
	    if ($val == $newlang) {
		Logg::loggerInfo('Language::LanguageSwitch() ~ changement de la langue utilisée pour '.$newlang,'',__FILE__.'@'.__LINE__);
		$_SESSION["language"] = $newlang;
		Language::LoadInit($_SESSION["language"]);
	    }
	}
    }

    /**
     * Dockable elements to use for language switch link, image, bouton or indicator
     */
    static function LanguageLink4Switch($output = 'a', $admin = FALSE) {
	if ($admin) {
	    $addadmin = "../";
	}
	else {
	    $addadmin = "";
	}
	$listesupported = explode(",",$GLOBALS['LANGUE']['supported']);
	$out = $out1 = $out2 = $out3 = $out4 = "";
	foreach ($listesupported as $val) {
	    if ($val == $_SESSION["language"]) {
		$out .= $val." - ";
		$out1.= $GLOBALS['LANGUE_'.$_SESSION["language"]][$val]." - ";
		$out3 = $GLOBALS['LANGUE_'.$_SESSION["language"]][$val];
		$out2.= imageTag($addadmin.$GLOBALS['REP']['img']."lang/".$val.".png",$val)." ".$GLOBALS['LANGUE_'.$_SESSION["language"]][$val]." - ";
		$out4.= "<li id=\"".$_SESSION["language"]."\">".imageTag($addadmin.$GLOBALS['REP']['img']."lang/".$val.".on.png",$val,'','on')."</li>\n\t\t\t\t\t";
	    }
	    else {
		$out .= linkTag("?lang=".$val,$GLOBALS['LANGUE_'.$_SESSION["language"]][$val])." - ";
		$out1.= $GLOBALS['LANGUE_'.$_SESSION["language"]][$val]." - ";
		$out2.= linkTag("?lang=".$val,imageTag($addadmin.$GLOBALS['REP']['img']."lang/".$val.".png",$val)." ".$GLOBALS['LANGUE_'.$_SESSION["language"]][$val])." - ";
		$out4.= "<li id=\"".$val."\">".linkTag("?lang=".$val,imageTag($addadmin.$GLOBALS['REP']['img']."lang/".$val.".png",$val,'','','BoutonLang'.$val),'',$val," onmouseover=\"SX_RollIMG('BoutonLang".$val."','".$addadmin.$GLOBALS['REP']['img']."lang/".$val.".on.png');\" onmouseout=\"SX_RollIMG('BoutonLang".$val."','".$addadmin.$GLOBALS['REP']['img']."lang/".$val.".png')\"")."</li>\n\t\t\t\t\t";
	    }
	}
	$out   = substr($out, 0, -2);
	$out1   = substr($out1, 0, -2);
	$out2   = substr($out2, 0, -2);

	if ($output =='a')
	    return $out;
	elseif ($output =='txt')
	    return $out1;
	elseif ($output =='actif')
	    return $out3;
	elseif ($output =='img')
	    return $out2;
	elseif ($output =='imglist')
	    return $out4;
	else
	    return $out;
    }

    /**
     * method to load appropriate language init file found in conf/lang
     * this file give translated content to use in this page
     */
    static function LoadInit($lang = '') {
	Logg::loggerInfo('Language::LoadInit() ~ chargement de la langue '.$lang,'',__FILE__.'@'.__LINE__);
	if ($lang == '') {
	    if (isset($_SESSION) and $_SESSION["language"] != "") {
		$lang = $_SESSION["language"];
	    }
	    else {
		$lang = $GLOBALS['LANGUE']['default'];
	    }
	}
	setlocale(LC_ALL,$lang.'_'.strtoupper($lang).".UTF8");
	date_default_timezone_set('Europe/Paris');
	// use fonction created in inc/conf.inc
	exportConf2Global($GLOBALS['REP']['appli'].'conf/lang/'.$lang.".xml");
    }
}
?>
