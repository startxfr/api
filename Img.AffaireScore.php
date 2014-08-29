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
include (getMutoolsPath('jpgraph')."src/jpgraph.php");
include (getMutoolsPath('jpgraph')."src/jpgraph_canvas.php");

$tab[0] = "#1f6d9f";
$tab[1] = "#1f4c9f";
$tab[2] = "#1f259f";
$tab[3] = "#461f9f";
$tab[4] = "#731f9f";
$tab[5] = "#911f9f";
$tab[6] = "#9f1f91";
$tab[7] = "#9f1f73";
$tab[8] = "#9f1f4f";
$tab[9] = "#9f1f31";
$tab[10] = "#9f1f1f";
$tab[11] = "#d54c4c";
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
