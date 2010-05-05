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
include ("../plugin/jpgraph/src/jpgraph_bar.php");

$maxLigne = '5';
$year = date('Y');

$req = "SELECT DATEDIFF(dateenvoi_fact,datereglement_fact) AS datediff,
			sum(sommeHT_fact) AS counter,
			count(id_fact) AS count
			FROM facture
			GROUP BY datediff
			ORDER BY datediff ASC";
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree($req);
$lesdata = $bddtmp->process();
$total['counter'] = $total['count'] = '';
$dataOut = array( '-30'=>array('title'=>'retard de +30j'),
	'-7'=>array('title'=>'retard de 7-30j'),
	'-1'=>array('title'=>'retard de 1-7j'),
	'+1'=>array('title'=>'heure (0-7j)'),
	'+7'=>array('title'=>'avance (+7j)'));

if (count($lesdata) > 0) {
    foreach($lesdata as $key => $val) {
	if($val['datediff'] < -30) {
	    $dataOut['-30']['counter'] += $val['counter'];
	    $dataOut['-30']['count']   += $val['count'];
	}
	elseif($val['datediff'] < -7) {
	    $dataOut['-7']['counter'] += $val['counter'];
	    $dataOut['-7']['count']   += $val['count'];
	}
	elseif($val['datediff'] < 0) {
	    $dataOut['-1']['counter'] += $val['counter'];
	    $dataOut['-1']['count']   += $val['count'];
	}
	elseif($val['datediff'] < 7) {
	    $dataOut['+1']['counter'] += $val['counter'];
	    $dataOut['+1']['count']   += $val['count'];
	}
	else {
	    $dataOut['+7']['counter'] += $val['counter'];
	    $dataOut['+7']['count']   += $val['count'];
	}
	$total['counter'] += $val['counter'];
	$total['count'] += $val['count'];
    }

    // Some data
    foreach($dataOut as $key => $val) {
	$datay[]	= $val['counter'];
	$datay2[]	= $val['count'];
	$datazero[]= 0;
	$datax[]	= $val['title'];
    }

    // Create the graph.
    $graph = new Graph(500,300,"auto");
    $graph->img->SetAntiAliasing();
    $graph->img->SetMargin(55,25,20,30);
    $graph->SetMarginColor('white');
    $graph->SetFrame(false);
    $graph->title->Set('Délais de paiement');
    $graph->title->SetFont(FF_ARIAL,FS_NORMAL,11);
    $graph->title->SetColor('midnightblue');

    $graph->SetScale("textlin");
    $graph->yaxis->scale->SetGrace(20);
    $graph->yaxis->SetColor('gray@0.4');
    $graph->ygrid->SetColor('gray:0.9','darkgray@0.1');

    $graph->SetY2Scale("lin");
    $graph->y2grid->SetColor('green:0.5','darkgreen@0.5');
    $graph->y2axis->scale->SetGrace(20);
    $graph->y2axis->SetColor('green:0.7');


    $graph->xaxis->SetTickLabels($datax);
    $graph->xaxis->SetColor('midnightblue@0.5');


    $bplotzero = new BarPlot($datazero);

    // Create the "Y" axis group
    $ybplot1 = new BarPlot($datay);
    //$ybplot1->value->Show();
    $ybplot1->value->SetColor('gray@0.4');
    $ybplot1->SetFillGradient('AntiqueWhite1','AntiqueWhite4:0.8',GRAD_HOR);
    $ybplot1->SetFillColor('gray@0.4');
    $ybplot = new GroupBarPlot(array($ybplot1,$bplotzero));

    // Create the "Y2" axis group
    $ybplot2 = new BarPlot($datay2);
    //$ybplot2->value->Show();
    $ybplot2->value->SetColor('green:0.5');
    $ybplot2->SetFillGradient('olivedrab1','olivedrab4',GRAD_HOR);
    $ybplot2->SetFillColor('green:0.5');
    $y2bplot = new GroupBarPlot(array($bplotzero,$ybplot2));

    // Add the grouped bar plots to the graph
    $graph->Add($ybplot);
    $graph->AddY2($y2bplot);

    // .. and finally stroke the image back to browser
    $graph->Stroke();
}
?>