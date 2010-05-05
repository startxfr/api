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
include ("plugin/jpgraph/src/jpgraph.php");
include ("plugin/jpgraph/src/jpgraph_pie.php");
include ("plugin/jpgraph/src/jpgraph_pie3d.php");


$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree("SELECT COUNT(id_cmd) commande FROM commande WHERE sommeHT_cmd < 1000.00");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['commande'];
$lesLeng[] = "Inférieur a 1 000 euros";
$value[] = 0;
$bddtmp->makeRequeteFree("SELECT COUNT(id_cmd) commande FROM commande WHERE sommeHT_cmd >= 1000.00 AND  sommeHT_cmd < 2500.00");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['commande'];
$lesLeng[] = "Entre 1 000 et 2 500 euros";
$value[] = 1000;
$bddtmp->makeRequeteFree("SELECT COUNT(id_cmd) commande FROM commande WHERE sommeHT_cmd >= 2500.00 AND  sommeHT_cmd < 5000.00");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['commande'];
$lesLeng[] = "Entre 2 500 et 5 000 euros";
$value[] = 2500;
$bddtmp->makeRequeteFree("SELECT COUNT(id_cmd) commande FROM commande WHERE sommeHT_cmd >= 5000.00 AND  sommeHT_cmd < 10000.00");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['commande'];
$lesLeng[] = "Entre 5 000 et 10 000 euros";
$value[] = 5000;
$bddtmp->makeRequeteFree("SELECT COUNT(id_cmd) commande FROM commande WHERE sommeHT_cmd >= 10000.00");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['commande'];
$lesLeng[] = "Plus de 10 000 euros";
$value[] = 10000;

$total = 0;
foreach($lesdata as $key => $val)
    $total += $val;
if ($total != 0) {
    foreach($lesdata as $key => $val) {
	$data[]	= round(($val/$total),2);
	$targ[]	= "?sommeHT_cmd=".$value[$key];
	$alts[]	= $val." commandes ";
	$lalegend[]	= utf8_decode($lesLeng[$key]);
    }

    // Create 3D pie plot
    $p1 = new PiePlot3d($data);
    $p1->SetTheme("water");
    $p1->SetCenter(0.31,0.6);
    $p1->SetSize(120);
    $p1->SetAngle(35);
    $p1->SetStartAngle(45);
    $p1->value->SetFont(FF_ARIAL,FS_BOLD,9);
    $p1->value->SetColor("darkseagreen");
    $p1->SetLegends($lalegend);
    $p1->SetCSIMTargets($targ,$alts);

    // Create the Pie Graph.
    $graph = new PieGraph(480,200,"auto");
    $graph->SetAntiAliasing();
    $graph->SetFrame(false);
    $graph->legend->Pos(0,0);
    $graph->Add($p1);
    $graph->StrokeCSIM("../Img.CommandeChart.php");
}


?>
