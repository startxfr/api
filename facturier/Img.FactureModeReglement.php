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
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/
// Whe get the page context
$PC = new PageContextVar();

ini_set('display_errors','off');
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
include ("../plugin/jpgraph/src/jpgraph.php");
include ("../plugin/jpgraph/src/jpgraph_pie.php");
include ("../plugin/jpgraph/src/jpgraph_pie3d.php");

$req = "SELECT sum(sommeHT_fact) AS counter, id_modereg, nom_modereg
		FROM facture
		LEFT JOIN ref_modereglement ON ref_modereglement.id_modereg = facture.modereglement_fact
		GROUP BY id_modereg
		ORDER BY `counter` DESC";
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree($req);
$lesdata = $bddtmp->process();
$total = 0;

if (count($lesdata) > 0) {
    foreach($lesdata as $key => $val) {
	$total = $total+$val['counter'];
    }

    // Some data
    foreach($lesdata as $key => $val) {
	$data[]	= round(($val['counter']/$total),2);
	$targ[]	= "FactureListe.php?modereglement_fact=".$val['id_modereg'];
	$alts[]	= formatCurencyDisplay($val['counter'],0,' euros').' réglé par '.$val['nom_modereg'];
	$lalegend[]	= $val['nom_modereg'];
    }
    //print_r($data);
    //print_r($lalegend);
    // Create the Pie Graph.
    $graph = new PieGraph(500,300,"auto");
    $graph->SetAntiAliasing();
    $graph->SetFrame(false);
    $graph->title->Set('Mode de règlement');
    $graph->title->SetFont(FF_ARIAL,FS_NORMAL,11);
    $graph->title->SetColor('midnightblue');
    $graph->legend->Pos(0.05,0.1);
    $graph->subtitle->Set(' '.formatCurencyDisplay($total,0,' euros'));
    $graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,9);
    $graph->subtitle->SetColor('blue');

    // Create 3D pie plot
    $p1 = new PiePlot3d($data);
    $p1->SetTheme("water");
    $p1->SetCenter(0.35,0.6);
    $p1->SetSize(115);

    // Adjust projection angle
    $p1->SetAngle(45);

    // Adjsut angle for first slice
    $p1->SetStartAngle(45);

    // Display the slice values
    $p1->value->SetFont(FF_ARIAL,FS_NORMAL,9);
    $p1->value->SetColor("darkblue@0.5");

    $p1->SetLegends($lalegend);

    $p1->SetCSIMTargets($targ,$alts);

    $graph->Add($p1);
    $graph->StrokeCSIM("Img.FactureModeReglement.php");
}


?>
