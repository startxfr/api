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
include ('inc/conf.inc');		// Declare global variables from config files
include ('inc/core.inc');		// Load core library
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/
// Whe get the page context
$PC = new PageContextVar();

ini_set('display_errors','off');
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
include "plugin/jpgraph/src/jpgraph.php";
include "plugin/jpgraph/src/jpgraph_canvas.php";

$tab[0] = "#eeeeee";
$tab[1] = "#dddddd";
$tab[2] = "#cccccc";
$tab[3] = "#bbbbbb";
$tab[4] = "#aaaaaa";
$tab[5] = "#999999";
$tab[6] = "#888888";
$tab[7] = "#777777";
$tab[8] = "#666666";
$tab[9] = "#555555";
$tab[10] = "#444444";
$tab[11] = "#333333";
$w = $GLOBALS['PropsecConf']['image.score.width'];
if(empty($PC->rcvG['score'])) {
    $fromColor = $tab[0];
    $toColor = $tab[1];
    $score = "0%";
}
else {
    $score1 = round(((int) $PC->rcvG['score'])/10,0);
    $fromColor = $tab[$score1];
    $toColor = $tab[$score1+1];
    $score = $PC->rcvG['score']."%";
}

$g = new CanvasGraph($w,14);
$g->SetBackgroundGradient($fromColor,$toColor,1);
$g->SetFrame(false);
$g->InitFrame();


$t = new Text($score,$w/2,2);
$t->SetFont(FF_ARIAL,FS_NORMAL,7);
$t->SetColor("white");
$t->Align('center','top');
$t->ParagraphAlign('center');
$t->Stroke($g->img);

$g->Stroke();
?>
