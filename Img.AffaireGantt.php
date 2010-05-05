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
if(!array_key_exists('id_aff', $_GET) or $_GET['id_aff'] == '') {
    $_GET['id_aff'] = $argv[1];
}
$PC = new PageContextVar();
ini_set('display_errors','off');
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
include ("plugin/jpgraph/src/jpgraph.php");
include ("plugin/jpgraph/src/jpgraph_gantt.php");

$id_aff = $PC->rcvG['id_aff'];
$req = "SELECT *
		FROM actualite
		LEFT JOIN ref_statusaffaire ON ref_statusaffaire.id_staff = actualite.status_aff
		LEFT JOIN ref_statusdevis ON ref_statusdevis.id_stdev = actualite.status_dev
		LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = actualite.status_cmd
		LEFT JOIN ref_statusfacture ON ref_statusfacture.id_stfact = actualite.status_fact
		LEFT JOIN ref_statusfacturefournisseur ON ref_statusfacturefournisseur.id_stfactfourn = actualite.status_factfourn
		WHERE id_aff = '".$id_aff."' ORDER BY date ASC";
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree($req);
$dataAff = $bddtmp->process();



$dateBegin = substr($dataAff[0]['date'],0,16);
$plageDateBegin = substr($dataAff[0]['date'],0,10);
$plageDateEnd = substr($dataAff[count($dataAff)-1]['date'],0,10);
$progress = $dataAff[count($dataAff)-1]['score_staff']/100;
$affBar = new GanttBar(0,"Affaire ".$id_aff,$plageDateBegin,$plageDateEnd,"[".round($progress*100)."%]");
$affBar->SetPattern(BAND_RDIAG,"green");
$affBar->SetFillColor("green");
$affBar->progress->Set($progress);
$affBar->progress->SetPattern(GANTT_SOLID,"darkgreen");
$affBar->title->SetCSIMTarget('../draco/Affaire.php?id_aff='.$id_aff,'Voir cette affaire');

$matrix['aff']['milestones'] = array();
foreach($dataAff as $key => $val) {
    if($val['id_factfourn'] != '')
	$matrix['factfourn']['milestones'][] = $val;
    elseif($val['id_fact'] != '')
	$matrix['fact']['milestones'][] = $val;
    elseif($val['id_cmd'] != '')
	$matrix['cmd']['milestones'][] = $val;
    elseif($val['id_dev'] != '')
	$matrix['dev']['milestones'][] = $val;
    else $matrix['aff']['milestones'][] = $val;
}

foreach($matrix as $type => $elements) {
    $matrix[$type]['dateBegin'] = substr($elements['milestones'][0]['date'],0,16);
    $matrix[$type]['dateEnd']   = substr($elements['milestones'][count($elements['milestones'])-1]['date'],0,16);
    if(substr($matrix[$type]['dateBegin'],0,10) == substr($matrix[$type]['dateEnd'],0,10)) {
	$matrix[$type]['dateBegin'] =  substr($matrix[$type]['dateBegin'],0,10).' 00:00';
	$matrix[$type]['dateEnd'] =  substr($matrix[$type]['dateEnd'],0,10).' 23:59';
    }
    if($type == 'factfourn') {
	$matrix[$type]['title']   = "Facturation fournisseurs";
	$matrix[$type]['progress']   = $elements['milestones'][count($elements['milestones'])-1]['pourcent_stfactfourn'];
    }
    elseif($type == 'fact') {
	$matrix[$type]['title']   = "Facturation client";
	$matrix[$type]['progress']   = $elements['milestones'][count($elements['milestones'])-1]['pourcent_stfact'];
    }
    elseif($type == 'cmd') {
	$matrix[$type]['title']   = "Commande";
	$matrix[$type]['progress']   = $elements['milestones'][count($elements['milestones'])-1]['pourcent_stcmd'];
    }
    elseif($type == 'dev') {
	$matrix[$type]['title']   = "Devis";
	$matrix[$type]['progress']   = $elements['milestones'][count($elements['milestones'])-1]['score_stdev'];
    }
    elseif($type == 'aff') {
	$matrix[$type]['title']   = "Affaire";
	$matrix[$type]['progress']   = $elements['milestones'][count($elements['milestones'])-1]['score_staff'];
    }
}

unset($dataAff);








ini_set("memory_limit","50m");
$graph = new GanttGraph();
// Gestion du titre
//$graph->title->Set("Evolution de l'affaire ".$id_aff);
//$graph->title->SetFont(FF_ARIAL,FS_BOLD,16);
//$graph->title->SetColor('darkgreen');
// Gestion de la big boite
$graph->SetMarginColor('darkgreen@1');
$graph->SetBox(true,'darkgreen@0.2',0);
$graph->SetFrame(true,'darkgreen@1',0);

// Gestion de la boite de diagram
$graph->scale->divider->SetColor('darkgreen@0.2');
$graph->scale->dividerh->SetColor('darkgreen@0.2');
$graph->ShowHeaders(GANTT_HMONTH | GANTT_HDAY);
// Month config
$graph->scale->month->SetStyle(MONTHSTYLE_SHORTNAMEYEAR4);
$graph->scale->month->SetFont(FF_FONT2,FS_BOLD);
$graph->scale->month->SetFontColor('darkgreen');
$graph->scale->month->grid->SetColor('darkgreen@0.6');
// Day
$graph->scale->day->SetStyle(DAYSTYLE_SHORTDATE4);
$graph->scale->day->SetFont(FF_FONT1);
$graph->scale->day->SetFontColor('darkgreen');
$graph->scale->day->grid->SetColor('darkgreen@0.8');

$graph->Add($affBar);

// On met la barre oblique sur la date du jour
$vline = new GanttVLine(date("Y-m-d"));
$vline->SetDayOffset(0.5);
$vline->title->Set(date("d/m"));
$vline->title->SetFont(FF_FONT1,FS_BOLD,10);
$graph->Add($vline);

$iBar = 1;
foreach($matrix as $type => $elements) {
    if($type != 'aff') {
	$bar = new GanttBar($iBar,$elements['title'],$elements['dateBegin'],$elements['dateEnd'],"[".$elements['progress']."%]");
	if($type == 'cmd')
	     $color = 'cyan';
	elseif($type == 'fact' or $type == 'factfourn')
	     $color = 'blue';
	else $color = 'green';
	$bar->SetPattern(BAND_RDIAG,$color.':0.75');
	$bar->SetFillColor($color.'@0.4');
	$bar->progress->Set($elements['progress']/100);
	$bar->progress->SetPattern(GANTT_SOLID,'dark'.$color);
	$bar->title->SetCSIMTarget($data[$i][5],$data[$i][6]);
	$graph->Add($bar);
	$iBar++;
    }
    foreach($elements['milestones'] as $iMilestone => $milestone) {
	$ms = new MileStone($iBar,"",substr($milestone['date'],0,10),"");
	if($type == 'factfourn')
	     $ms->SetCSIMTarget('../facturier/FactureFournisseur.php?id_factfourn='.$milestone['id_factfourn'],$milestone['titre']);
	elseif($type == 'fact')
	     $ms->SetCSIMTarget('../facturier/Facture.php?id_fact='.$milestone['id_fact'],$milestone['titre']);
	elseif($type == 'cmd')
	     $ms->SetCSIMTarget('../pegase/Commande.php?id_cmd='.$milestone['id_cmd'],$milestone['titre']);
	elseif($type == 'dev')
	     $ms->SetCSIMTarget('../draco/Devis.php?id_dev='.$milestone['id_dev'],$milestone['titre']);
	else $ms->SetCSIMTarget('../draco/Affaire.php?id_aff='.$milestone['id_aff'],$milestone['titre']);
	$graph->Add($ms);
    }
    $iBar++;
}

// And stroke
$graph->StrokeCSIM("../Img.AffaireGantt.php");


?>
