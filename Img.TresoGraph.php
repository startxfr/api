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
include ("plugin/jpgraph/src/jpgraph_line.php");
include ("plugin/jpgraph/src/jpgraph_iconplot.php");

$annee = date("Y");
$ActualMonth = date("n");
$month = $ActualMonth;
$anneeA= $annee;
$monthText  = array("Janv","Fev","Mars","Avr","Mai","Juin","Juil","Aout","Sept","Oct","Nov","Dec");
for($i = 12; $i > 0; $i--) {
    if($month < 10) $monthD = '0'.$month;
    else $monthD = $month;
    $monthList[$month] = $anneeA.'-'.$monthD;
    $monthAxisList[$month] = $monthText[$month-1];
    if($month == 1) {
	$month = 12;
	$anneeA--;
    }
    else $month--;
}
$monthList = array_reverse($monthList,TRUE);
$monthAxisList = array_reverse($monthAxisList);
$statList = array("5","6");
$statColor = array("AntiqueWhite","olivedrab");
$statTxtColor = array("gray","green");
$statLegend = array("Attentes","Payes");

$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
foreach($statList as $status) {
    foreach($monthList as $month => $monthYear) {
	$bddtmp->makeRequeteFree("SELECT SUM(sommeHT_fact) AS total
						  FROM facture,actualite
						  WHERE date LIKE '$monthYear%'
						  AND actualite.status_fact = '$status'
						  AND actualite.id_fact = facture.id_fact");
	$tmpData = $bddtmp->process();
	if($tmpData[0]['total'] == '')
	    $tmpData[0]['total'] = 0;
	$data[$status][] = $tmpData[0]['total'];
    }
}

if($_GET['format'] == 'small') {
    $graph = new Graph(500,250);
    $graph->SetMargin(50,50,5,10);
}
else {
    $graph = new Graph(500,350);
    $graph->SetMargin(50,40,20,30);
}
$graph->img->SetAntiAliasing();
$graph->SetScale("textlin");
$graph->SetFrame(false,'white');

$graph->title->Set('Trésorerie '.$annee);
$graph->title->SetFont(FF_ARIAL,FS_NORMAL,12);

//$graph->SetBackgroundGradient('red','blue');

$graph->xaxis->SetPos('min');
$graph->xaxis->SetTickLabels($monthAxisList);

foreach($statList as $key => $status) {
    $p[$key] = new LinePlot($data[$status]);
    $p[$key]->SetColor($statTxtColor[$key].':0.5');
    $p[$key]->SetFillColor($statTxtColor[$key].'@0.4');
    $p[$key]->SetLegend(utf8_decode($statLegend[$key]));
    $p[$key]->SetWeight(1);
    $graph->Add($p[$key]);
}

// Output line
$graph->Stroke();


?>
