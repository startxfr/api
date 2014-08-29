<?php
/*#########################################################################
#
#   name :       Application.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('Aide'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$sortie	= "<h3>".$GLOBALS['Tx4Lg']['AppliTitre']."</h3>";
$svn	= shell_exec('svn info ');
$svnline= explode("\n", $svn);
foreach ($svnline as $key => $val) {
    $svnpart = explode(":", $val);
    foreach ($svnpart as $key1 => $val1) {
	if($key1 == 0) {
	    $sortie .= "<label>".$val1." :</label>";
	}
	else {
	    $sortie .= $val1;
	}
    }
    $sortie .= "<br/>\n";
}
if($PC->rcvG['path'] != '') {
    $path = $PC->rcvG['path'];
}
$sortiefile = file ("http://".$GLOBALS['URL']['appli'].'manage/Img.DiskUsage.php?path='.$path.'&uri=Application.php');
foreach ($sortiefile as $val1) {
    $sortie .= $val1;
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent("<div style=\"padding-left:100px\">".$sortie."</div>");
$out->Process();
?>
