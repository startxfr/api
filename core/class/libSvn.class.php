<?php
/*#########################################################################
#
#   name :       SVN.inc
#   desc :       Client library for Subversion
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


class libSvn {


    static function SVNStatus($file = '',$SVNPool = 1) {
	if($file{0} == '/') {
	    $files[] = $file;
	}
	// plusieurs fichiers specifiés
	elseif(is_array($file)) {
	    foreach($file as $key => $val) { //Le fichier n'est pas specifié: analyse de la WC+WD
		if ($val == '') {
		    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'];
		}
		// Le fichier specifié est URL Absolue
		elseif($val{0} == '/') {
		    $files[] = $val;
		}
		// Le fichier specifié est relatif (WC+WD+File)
		else {
		    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'].$val;
		}
	    }

	}

	// aucun ou 1 fichier specifié avec URL relative (WC+WD+File)
	else {
	    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'].$file;
	}

	//Préparation de la commande
	$CmdBase = "export LANG=\"en_US.UTF-8\"; export LC_CTYPE=\"en_US.UTF-8\"; svn status  --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool'.$SVNPool]['ConfigDir']." ";

	foreach($files as $key => $uri) {
	    Logg::loggerNotice('SVN::SVNStatus() ~ Commande : '.$CmdBase.$uri,'',__FILE__.'@'.__LINE__);
	    if ($GLOBALS['LOG']['DisplayDebug']) {
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
	    }
	    $result= shell_exec($CmdBase.$uri);
	    $svnline= explode("\n", $result);
	    foreach ($svnline as $key1 => $val1) {
		$value = $val1{0}.$val1{1}.$val1{2}.$val1{3}.$val1{4}.$val1{5}.$val1{6};
		$file  = str_replace($value,"",$val1);
		if($file != '') {
		    $OutList[$file] = $value;
		}
	    }
	}
	return $OutList;
    }
    /*------------------------------------------------------------------------+
| SVNInfo
|
| Perform SVN info query on a working copy
+-------------------------------------------------------------------------+
| $output	kind of information
| $file	    	array or var with absolute URI or relative to SVNPool
| $recursive	do recursive search
| $SVNPool	Pool ID to use (see GFX.ini for multiple Pool)
+-------------------------------------------------------------------------+
| echo array with single or multiple query
+------------------------------------------------------------------------*/
    static function SVNInfo($output = '' , $file = '' , $recursive = false, $SVNPool = 1) {
	$tmpURI = '';
	if($file{0} == '/') {
	    $files[] = $file;
	}
	// plusieurs fichiers specifiés
	elseif(is_array($file)) {
	    foreach($file as $key => $val) { //Le fichier n'est pas specifié: analyse de la WC+WD
		if ($val == '') {
		    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'];
		}
		// Le fichier specifié est URL Absolue
		elseif($val{0} == '/') {
		    $files[] = $val;
		}
		// Le fichier specifié est relatif (WC+WD+File)
		else {
		    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'].$val;
		}
	    }

	}

	// aucun ou 1 fichier specifié avec URL relative (WC+WD+File)
	else {
	    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'].$file;
	}

	//Préparation de la commande
	$CmdBase = "export LANG=\"en_US.UTF-8\"; svn info ";
	if ($recursive) {
	    $CmdBase .= "-R  ";
	}

	foreach($files as $key => $uri) {
	    Logg::loggerNotice('SVN::SVNInfo() ~ Commande : '.$CmdBase.$uri,'',__FILE__.'@'.__LINE__);
	    if ($GLOBALS['LOG']['DisplayDebug']) {
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$uri." --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool'.$SVNPool]['ConfigDir'];
	    }
	    $result= shell_exec($CmdBase.$uri." --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool'.$SVNPool]['ConfigDir']);
	    $svnline= explode("\n", $result);
	    //print_r($svnline);
	    if ($recursive) {
		foreach ($svnline as $key1 => $val1) {
		    list($titre, $value) = explode(":", $val1, 2);
		    $titre = libSvn::SVNCleanKeywords($titre);
		    if ($titre == '') $tmpURI = '';
		    elseif ($titre == 'Path') {
			$value1 = trim(str_replace($uri,"",$value));
			if ($value1 != '') {
			    $OutList[$value1][$titre] = $value1;
			    $OutList[$value1]['Relative_Path'] = $value1;
			    $OutList[$value1]['Size'] = FileGetDirectorySize($uri.$value1);
			    $OutList[$value1]['FormatedSize'] = FileConvertSize2Human($OutList[$value1]['Size']);
			    $tmpURI = $value1;
			}
			else {
			    $OutList[$uri][$titre] = $uri;
			    $OutList[$uri]['Relative_Path'] = $uri;
			    $OutList[$value1]['Size'] = FileGetDirectorySize($uri);
			    $OutList[$value1]['FormatedSize'] = FileConvertSize2Human($OutList[$value1]['Size']);
			    $tmpURI = $uri;
			}
		    }
		    else {
			if ($tmpURI != '') $OutList[$tmpURI][$titre] = trim($value);
			else 			 $OutList[$uri][$titre] = trim($value);
		    }
		}

		//print_r($OutList);
		if(is_array($OutList)) {
		    foreach ($OutList as $key => $val) {
			if (($key.'/' == $uri)or($key == $uri)) $SVNINFO['ROOT'] = $val;
			else {
			    $explode = explode("/", $key);
			    foreach ($explode as $key1 => $val1) {
				if ($val1 != '') $newArray .= "[\"".$val1."\"]";
			    }
			    eval("\$SVNINFO['ROOT']".$newArray."= \$val;");
			    unset($newArray);
			}
		    }
		}
		$OutList = $SVNINFO;
	    }
	    else {
		foreach ($svnline as $key1 => $val1) {
		    list($titre, $value) = explode(":", $val1, 2);
		    $titre = libSvn::SVNCleanKeywords($titre);
		    if ((($output != '')and(libSvn::SVNCleanKeywords($output) == $titre))or(($output == '')and($titre != ''))) {
			if (count($files) == 1) {
			    $OutList[$titre] = trim($value);
			}
			else {
			    $OutList[$uri][$titre] = trim($value);
			}
		    }
		}
	    }

	}

	return $OutList;
    }

    /*------------------------------------------------------------------------+
| SVNPropList
|
| Perform SVN info query on a working copy
+-------------------------------------------------------------------------+
| $output	kind of information
|			xxx:	=>	XML space name (ex svn:* , sxf:*)
|			xxx	=>	return coresponding property
|			''	=>	All property found
| $file	    	array or var with absolute URI or relative to SVNPool
| $recursive	do recursive search
| $SVNPool	Pool ID to use (see GFX.ini for multiple Pool)
+-------------------------------------------------------------------------+
| echo array with single or multiple property
+------------------------------------------------------------------------*/
    static function SVNPropList($output = '' , $file = '' , $recursive = false, $SVNPool = 1) {
	$SVNPLIST = $tmpArray = $newArray = array();
	if($file{0} == '/') {
	    $files[] = $file;
	}
	// plusieurs fichiers specifiés
	elseif(is_array($file)) {
	    foreach($file as $key => $val) { //Le fichier n'est pas specifié: analyse de la WC+WD
		if ($val == '') {
		    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'];
		}
		// Le fichier specifié est URL Absolue
		elseif($val{0} == '/') {
		    $files[] = $val;
		}
		// Le fichier specifié est relatif (WC+WD+File)
		else {
		    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'].$val;
		}
	    }

	}

	// aucun ou 1 fichier specifié avec URL relative (WC+WD+File)
	else {
	    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'].$file;
	}

	//Préparation de la commande
	$CmdBase = "export LANG=\"en_US.UTF-8\"; export LC_CTYPE=\"en_US.UTF-8\"; svn proplist --non-interactive --verbose --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool'.$SVNPool]['ConfigDir']." ";
	if ($recursive) {
	    $CmdBase .= "-R ";
	}

	foreach($files as $key => $val) {
	    $decompuri= explode("/", $val);
	    $compuri= count($decompuri);
	    //echo $CmdBase.$val;
	    Logg::loggerNotice('SVN::SVNPropList() ~ Commande : '.$CmdBase.$val,'',__FILE__.'@'.__LINE__);
	    if ($GLOBALS['LOG']['DisplayDebug']) {
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$val;
	    }
	    $result= shell_exec($CmdBase.$val);
	    $svnline= explode("\n", $result);
	    //print_r($svnline);
	    if ($recursive) {
		foreach ($svnline as $key1 => $val1) {
		    if(strncmp($val1,"Properties on",13) == 0) {
			$uri = explode("'",$val1);
			//echo "<br/>=FILE==".$val."==".$uri[1];
			if (($uri[1].'/' == $val)or($uri[1] == $val)) {
			    $tmpArray = "\$SVNPLIST['ROOT']";
			    unset($newArray);
			}
			else {
			    $explode = explode("/",$uri[1]);
			    $explode = array_splice ($explode, $compuri);
			    foreach ($explode as $key2 => $val2) {
				$newArray .= "[\"".$val2."\"]";
			    }
			    $tmpArray = "\$SVNPLIST['ROOT']".$newArray;
			    unset($newArray);
			}
		    }
		    else {
			$property = explode(":", $val1, 3);
			$property[0] = trim($property[0]);
			$property[1] = trim($property[1]);
			$property[2] = trim($property[2]);
			if ((count($property) == 1)or($property[1] == '')) {

			}
			elseif (count($property) == 2) {
			    eval($tmpArray."[\"".$property[0]."\"]"."= \$property[1];");
			}
			else {
			    @eval($tmpArray."[\"".$property[0].":".$property[1]."\"]"."= \$property[2];");
			}
			//echo "<br/>==PROP==".serialize($property);
		    }
		}
		//print_r($SVNPLIST);
		$OutList = $SVNPLIST;
	    }
	    else {
		foreach ($svnline as $key1 => $val1) {
		    $property = explode(":", $val1, 3);
		    foreach ($property as $kp => $vp) $property[$kp] = trim($property[$kp]);
		    if (count($files) == 1) {
			if ((count($property) == 1)or($property[1] == '')) {

			}
			elseif (count($property) == 2) {
			    $OutList[$property[0]] = $property[1];
			}
			else {
			    $OutList[$property[0].":".$property[1]] = $property[2];
			}
		    }
		    else {
			if ((count($property) == 1)or($property[1] == '')) {

			}
			elseif (count($property) == 2) {
			    $OutList[$val][$property[0]] = $property[1];
			}
			else {
			    $OutList[$val][$property[0].":".$property[1]] = $property[2];
			}
		    }
		}
	    }
	}

	return $OutList;
    }


    /*------------------------------------------------------------------------+
| SVNPropList
|
| Perform SVN info query on a working copy
+-------------------------------------------------------------------------+
| $output	kind of information
|			xxx:	=>	XML space name (ex svn:* , sxf:*)
|			xxx	=>	return coresponding property
|			''	=>	All property found
| $file	    	array or var with absolute URI or relative to SVNPool
| $recursive	do recursive search
| $SVNPool	Pool ID to use (see GFX.ini for multiple Pool)
+-------------------------------------------------------------------------+
| echo array with single or multiple property
+------------------------------------------------------------------------*/
    static function SVNLog($output = '' , $file = '' , $SVNPool = 1, $revision = '') {
	if($file{0} == '/') {
	    $files[] = $file;
	}
	// plusieurs fichiers specifiés
	elseif(is_array($file)) {
	    foreach($file as $key => $val) { //Le fichier n'est pas specifié: analyse de la WC+WD
		if ($val == '') {
		    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'];
		}
		// Le fichier specifié est URL Absolue
		elseif($val{0} == '/') {
		    $files[] = $val;
		}
		// Le fichier specifié est relatif (WC+WD+File)
		else {
		    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'].$val;
		}
	    }

	}

	// aucun ou 1 fichier specifié avec URL relative (WC+WD+File)
	else {
	    $files[] = $GLOBALS['SVN_Pool'.$SVNPool]['WorkCopy'].$GLOBALS['SVN_Pool'.$SVNPool]['WorkDir'].$file;
	}

	//Préparation de la commande
	$CmdBase = "export LANG=\"en_US.UTF-8\";svn up; svn log -v ";
	if ($revision != '') {
	    $CmdBase .= "-r ".$revision." ";
	}
	if ($output == 'xml') {
	    $CmdBase .= "--xml ";
	}

	foreach($files as $key => $val) {
	    //echo $CmdBase.$val;
	    Logg::loggerNotice('SVN::SVNLog() ~ Commande : '.$CmdBase.$val,'',__FILE__.'@'.__LINE__);
	    if ($GLOBALS['LOG']['DisplayDebug']) {
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$val;
	    }
	    $result = shell_exec($CmdBase.$val);
	    if ($output == 'xml') {
		$toto = explode('<?xml version="1.0"?>',$result);
		$resultat .= $toto[1];
	    }
	    else {
		$resultat .= $result;
	    }
	}

	return $resultat;
    }

    /*------------------------------------------------------------------------+
| SVNCleanKeywords
|
| Clean a field from every specials characters (exept : for XML space name)
+-------------------------------------------------------------------------+
| $keywords	string to change
+-------------------------------------------------------------------------+
| echo cleaned string
+------------------------------------------------------------------------*/
    static function SVNCleanKeywords($keywords = '',$type = '') {
	if ($type == 'OK_SPACE') {
	    $in = "???????¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ;[]=+'{}|/^*+-=()@`#~&!$";
	    $out= "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy________________________";
	}
	if ($type == 'LIGHT') {
	    $in = "???????¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝß;[]=+'{}|/^*+-=()`#~&!$";
	    $out= "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYs_______________________";
	}
	else {
	    $in = "???????¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ; []=+'{}|/^*+-=()@`#~&!$";
	    $out= "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy_________________________";
	}
	if ($keywords != '') {
	    return strtr($keywords,$in,$out);
	}
    }


    /*------------------------------------------------------------------------+
| SVNDateSvn2human
|
| convert SVN date to human date
| 2005-05-13 23:52:55 +0200 (Fri, 13 May 2005) >> 13/05/2005 23:52:55
+-------------------------------------------------------------------------+
| $date	string to change
+-------------------------------------------------------------------------+
| echo cleaned string
+------------------------------------------------------------------------*/
    static function SVNDateSvn2human($date = '',$output = '') {
	if (substr($date,0,25) != '')
	    return DateUniv2Human(substr($date,0,25),$output,FALSE);
	else  return '';
    }

}
?>
