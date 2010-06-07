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


$req = "SELECT A1.id_typro id,A1.nom_typro ref_typeproj, COUNT(A2.id_aff) projet
		FROM ref_typeproj A1, affaire A2
		WHERE A1.id_typro = A2.typeproj_aff AND A2.actif_aff = '1'
		GROUP BY A1.id_typro";
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree($req);
$lesdata = $bddtmp->process();
$total = 0;

if (count($lesdata) > 0) {
    foreach($lesdata as $key => $val) {
	$total = $total+$val['projet'];
    }

    // Some data
    foreach($lesdata as $key => $val) {
	$data[]	= round(($val['projet']/$total),2);
	$targ[]	= "?typeaff=".$val['id'];
	$alts[]	= $val['projet']." affaires du type: ".$val['ref_typeproj'];
	$lalegend[]	= utf8_decode($val['ref_typeproj']);
	$total += round(($val['projet']/$total),2);
    }
    //print_r($data);
    //print_r($lalegend);
    if($total > 0) {
	// Create the Pie Graph.
	$graph = new PieGraph(470,230,"auto");
	$graph->SetAntiAliasing();
	$graph->SetFrame(false);
	$graph->legend->Pos(0,0.5);

	// Create 3D pie plot
	$p1 = new PiePlot3d($data);
	$p1->SetTheme("sand");
	$p1->SetCenter(0.3,0.3);
	$p1->SetSize(110);

	// Display the slice values
	$p1->value->SetFont(FF_ARIAL,FS_BOLD,9);
	$p1->value->SetColor("navy");

	// Add colored edges to the 3D pie
	// NOTE: You can't have exploded slices with edges!
	//$p1->SetEdge("navy");

	$p1->SetLegends($lalegend);
	$p1->SetCSIMTargets($targ,$alts);

	$graph->Add($p1);
	$graph->StrokeCSIM("../Img.AffaireChart.php");
    }
}


?>
