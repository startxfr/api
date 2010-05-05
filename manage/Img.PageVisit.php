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
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/
ini_set('memory_limit','13M');

// Whe get the page context
$PC = new PageContextVar();

$tmpreq = new Bdd();
if($PC->rcvG['type'] == '') 
{
	$PC->rcvG['type'] = 'normal';
}

$tmpreq->MakeRequeteFree("SELECT * FROM `page` WHERE channel_pg = '".$PC->rcvG['type']."' ORDER BY stat_pg DESC");
$result  = $tmpreq->process();
$tmpreq->MakeRequeteFree("SELECT SUM(stat_pg) AS SUM FROM `page` WHERE channel_pg = '".$PC->rcvG['type']."'");
$resultc  = $tmpreq->process();

	
if($resultc[0]['SUM'] == 0) 
{
	$tot1 = 1;
}
else
{
	$tot1 = $resultc[0]['SUM'];
}
foreach($result as $key => $page)
{
	$datax[]	= $page['nom_pg'];
	$datay[]	= $page['stat_pg'];
}


include ("../plugin/jpgraph/src/jpgraph.php");
include ("../plugin/jpgraph/src/jpgraph_bar.php");

// Size of graph

$width=800; 
$height=round(count($result)*30,0);

// Set the basic parameters of the graph 
$graph = new Graph($width,$height,'auto');
$graph->SetScale("textlin");

// No frame around the image
$graph->SetFrame(false);

// Rotate graph 90 degrees and set margin
$graph->Set90AndMargin(190,10,50,30);

// Set white margin color
$graph->SetMarginColor('white');

// Use a box around the plot area
$graph->SetBox();

// Use a gradient to fill the plot area
$graph->SetBackgroundGradient('white','lightgray',GRAD_HOR,BGRAD_PLOT);

// Setup title
$graph->title->Set("Fréquentation des pages");
$graph->title->SetFont(FF_ARIAL,FS_BOLD,11);
$graph->subtitle->Set("(graphique de synthese)");

// Setup X-axis
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

// Some extra margin looks nicer
$graph->xaxis->SetLabelMargin(5);

// Label align for X-axis
$graph->xaxis->SetLabelAlign('right','center');

// Add some grace to y-axis so the bars doesn't go
// all the way to the end of the plot area
$graph->yaxis->scale->SetGrace(10);

// We don't want to display Y-axis
$graph->yaxis->Hide();

// Now create a bar pot
$bplot = new BarPlot($datay);
$bplot->SetShadow();

//You can change the width of the bars if you like
$bplot->SetWidth(0.7);

// Set gradient fill for bars
$bplot->SetFillGradient('darkgreen','lightgreen',GRAD_HOR);

// We want to display the value of each bar at the top
$bplot->value->Show();
$bplot->value->SetFont(FF_VERDANA,FS_BOLD,9);
//$bplot->value->SetAlign('left','center');
$bplot->value->SetColor("white");
$bplot->value->SetFormat('%.0f');
$bplot->SetValuePos('max');

// Add the bar to the graph
$graph->Add($bplot);

// Add some explanation text
$txt = new Text('Total des pages vues : '.$tot1);
$txt->SetPos($width-10,$height-10,'right','bottom');
$txt->SetFont(FF_ARIAL,FS_BOLD,10);
$graph->Add($txt);

// .. and stroke the graph
$graph->Stroke();

?>
