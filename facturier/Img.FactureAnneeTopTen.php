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
include (getMutoolsPath('jpgraph')."src/jpgraph.php");
include (getMutoolsPath('jpgraph')."src/jpgraph_bar.php");

$maxLigne = '10';
$year = date('Y');

$req = "SELECT nom_ent, id_ent, sum(sommeHT_fact) AS counter,
		count(id_fact) AS count
		FROM facture, entreprise
		WHERE entreprise_fact = id_ent
		AND dateenvoi_fact >= '$year-01-01' AND dateenvoi_fact <= '$year-12-31'
		GROUP BY entreprise_fact
		ORDER BY counter DESC
		LIMIT 0, $maxLigne";
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree($req);
$lesdata = $bddtmp->process();

if (count($lesdata) > 0) {
    foreach($lesdata as $key => $val) {
	$datay[]	= $val['counter'];
	$datay2[]	= $val['count'];
	$datazero[] = 0;
	$datax[]	= $val['nom_ent'];
    }

    $width=500;
    $height=350;

    // Create the graph.
    $graph = new Graph($width,$height,"auto");
    $graph->img->SetAntiAliasing();
    $graph->SetFrame(false);
    $graph->title->Set('Top ten des clients en '.$year);
    $graph->title->SetFont(FF_ARIAL,FS_NORMAL,11);
    $graph->title->SetColor('midnightblue');

    $graph->SetScale("textlin");
    $graph->yaxis->scale->SetGrace(0);
    $graph->yaxis->SetColor('gray@0.4');
    $graph->ygrid->SetColor('gray:0.9','darkgray@0.1');

    $graph->SetY2Scale("lin");
    $graph->y2grid->SetColor('green:0.5','darkgreen@0.5');
    $graph->y2axis->scale->SetGrace(0);
    $graph->y2axis->SetColor('green:0.7');


    $graph->xaxis->SetTickLabels($datax);
    $graph->xaxis->SetColor('midnightblue@0.5');

    $graph->Set90AndMargin(150,10,50,25);

    //$graph->ygrid->Show(true,true);
    $graph->ygrid->SetColor('darkgreen','lightgreen@0.5');

    // Create the "dummy" 0 bplot
    $bplotzero = new BarPlot($datazero);

    // Create the "Y" axis group
    $ybplot1 = new BarPlot($datay);
    $ybplot1->SetValuePos('center');
    $ybplot1->value->Show();
    $ybplot1->value->SetColor('darkgray');
    $ybplot1->SetFillGradient('AntiqueWhite1','AntiqueWhite4:0.8',GRAD_HOR);
    $ybplot1->SetFillColor('gray@0.4');
    $ybplot = new GroupBarPlot(array($ybplot1,$bplotzero));

    // Create the "Y2" axis group
    $ybplot2 = new BarPlot($datay2);
    $ybplot2->SetValuePos('center');
    $ybplot2->value->Show();
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