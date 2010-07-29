<?php
/*#########################################################################
#
#   name :       application.php
#   desc :       Visiualize application data
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library


/*------------------------------------------------------------------------+
| SESSION ANALYSE
+------------------------------------------------------------------------*/
ini_set('memory_limit','13M');
// Whe get the page context
$PC = new PageContextVar();

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

$tmpreq = new Bdd();
$tmpreq->MakeRequeteAuto('ref_droit');
$result  = $tmpreq->process();
foreach ($result as $key => $droit) {
    $tmpreq->makeRequeteFree("SELECT COUNT(*) AS count FROM `user` WHERE droit = '".$droit['id_dt']."'");
    $result1  = $tmpreq->process();
    $data[]		= $result1[0]['count'];
    $targ[]		= "UserManage.php?droit=".$droit['id_dt'];
    $alts[]		= $result1['count']." ".$droit['nom_dt'];
    $lalegend[]	= $droit['nom_dt'];
}

include ("../plugin/jpgraph/src/jpgraph.php");
include ("../plugin/jpgraph/src/jpgraph_pie.php");
include ("../plugin/jpgraph/src/jpgraph_pie3d.php");

//print_r($data);
//print_r($lalegend);
// Create the Pie Graph.
$graph = new PieGraph(600,350,"auto");
$graph->SetFrame(false);

// Set A title for the plot
$graph->legend->Pos(0.1,0.1);

// Create 3D pie plot
$p1 = new PiePlot3d($data);
$p1->SetTheme("sand");
$p1->SetCenter(0.4);
$p1->SetSize(150);

// Adjust projection angle
$p1->SetAngle(45);

// Adjsut angle for first slice
$p1->SetStartAngle(45);

// Display the slice values
$p1->value->SetFont(FF_ARIAL,FS_BOLD,10);
$p1->value->SetColor("navy");

// Add colored edges to the 3D pie
// NOTE: You can't have exploded slices with edges!
//$p1->SetEdge("navy");

$p1->SetLegends($lalegend);
$p1->SetCSIMTargets($targ,$alts);

$graph->Add($p1);
$graph->StrokeCSIM('Img.DroitRepart.php');

?>
