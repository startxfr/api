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
include (getMutoolsPath('jpgraph')."src/jpgraph_pie.php");
include (getMutoolsPath('jpgraph')."src/jpgraph_pie3d.php");


$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree("SELECT COUNT(id_dev) devis FROM devis WHERE sommeHT_dev < 1000.00 AND status_dev < 7 AND status_dev != 2 ");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['devis'];
$lesLeng[] = "Inférieur a 1 000 euros";
$value[] = '0';
$max[] = '1000';
$bddtmp->makeRequeteFree("SELECT COUNT(id_dev) devis FROM devis WHERE sommeHT_dev >= 1000.00 AND  sommeHT_dev < 2500.00 AND status_dev < 7 AND status_dev != 2 ");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['devis'];
$lesLeng[] = "Entre 1 000 et 2 500 euros";
$value[] = '1000';
$max[] = '2500';
$bddtmp->makeRequeteFree("SELECT COUNT(id_dev) devis FROM devis WHERE sommeHT_dev >= 2500.00 AND  sommeHT_dev < 5000.00 AND status_dev < 7 AND status_dev != 2 ");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['devis'];
$lesLeng[] = "Entre 2 500 et 5 000 euros";
$value[] = '2500';
$max[] = '5000';
$bddtmp->makeRequeteFree("SELECT COUNT(id_dev) devis FROM devis WHERE sommeHT_dev >= 5000.00 AND  sommeHT_dev < 10000.00 AND status_dev < 7 AND status_dev != 2 ");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['devis'];
$lesLeng[] = "Entre 5 000 et 10 000 euros";
$value[] = '5000';
$max[] = '10000';
$bddtmp->makeRequeteFree("SELECT COUNT(id_dev) devis FROM devis WHERE sommeHT_dev >= 10000.00 AND status_dev < 7 AND status_dev != 2 ");
$lesdataTmp = $bddtmp->process();
$lesdata[] = $lesdataTmp[0]['devis'];
$lesLeng[] = "Plus de 10 000 euros";
$value[] = '10000';
$max[] = '';

$total = 0;
foreach($lesdata as $key => $val) {
    $total += $val;
}
if ($total != 0) {
    // Some data
    foreach($lesdata as $key => $val) {
	$data[]	= round(($val/$total),2);
	$targ[]	= "javascript:camenbertDevis('".$value[$key]."', '".$max[$key]."');";
	$alts[]	= $val." devis ";
	$lalegend[]	= utf8_decode($lesLeng[$key]);
	$total += round(($val/$total),2);
    }

    if($total > 0) {
	$p1 = new PiePlot3d($data);
	$p1->SetTheme("pastel");
	$p1->SetCenter(0.32,0.36);
	$p1->SetSize(120);
	$p1->SetAngle(35);
	$p1->SetStartAngle(45);
	$p1->value->SetFont(FF_ARIAL,FS_BOLD,9);
	$p1->value->SetColor("darkseagreen");
	$p1->SetLegends($lalegend);
	$p1->SetCSIMTargets($targ,$alts);

	$graph = new PieGraph(470,200,"auto");
	$graph->SetAntiAliasing();
	$graph->SetFrame(false);
	$graph->legend->Pos(0,0.55);
	$graph->Add($p1);
	$graph->StrokeCSIM("../Img.DevisChart.php");
    }
}


?>
