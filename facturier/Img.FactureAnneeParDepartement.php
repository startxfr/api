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
include (getMutoolsPath('jpgraph')."src/jpgraph_pie.php");
include (getMutoolsPath('jpgraph')."src/jpgraph_pie3d.php");

$maxLigne = '5';
$year = date('Y');

$req = "SELECT sum(sommeHT_fact) AS counter, SUBSTRING(cp_fact,1,2) AS depCode, id_dep, nom_dep
		FROM facture
		LEFT JOIN ref_departement ON ref_departement.id_dep = SUBSTRING(facture.cp_fact,1,2)
		WHERE daterecord_fact >= '".$year."' and daterecord_fact < '".($year+1)."'
		GROUP BY depCode
		ORDER BY `counter` DESC
		LIMIT 0 , ".$maxLigne;
$reqA = "SELECT sum(sommeHT_fact) AS counter, SUBSTRING(cp_fact,1,2) AS depCode, id_dep, nom_dep
		FROM facture
		LEFT JOIN ref_departement ON ref_departement.id_dep = SUBSTRING(facture.cp_fact,1,2)
		WHERE daterecord_fact >= '".$year."' and daterecord_fact < '".($year+1)."'
		GROUP BY depCode
		ORDER BY `counter` DESC
		LIMIT ".$maxLigne." , 200";
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree($req);
$lesdata = $bddtmp->process();
$bddtmp->makeRequeteFree($reqA);
$lesdataAutres = $bddtmp->process();
$total = 0;
$totalAutres = 0;

if (count($lesdata) > 0) {
    if(count($lesdata) == 5) {
	foreach($lesdataAutres as $key => $val) {
	    $totalAutres = $totalAutres+$val['counter'];
	}
	$lesdata[] = array('counter'=>$totalAutres,'id_dep'=>'','nom_dep'=>'Autres...');
    }

    foreach($lesdata as $key => $val) {
	$total = $total+$val['counter'];
    }

    // Some data
    foreach($lesdata as $key => $val) {
	$data[]	= round(($val['counter']/$total),2);
	$targ[]	= "FactureListe.php?cp_ent=".$val['id_dep'];
	$alts[]	= formatCurencyDisplay($val['counter'],0,' euros').' de facturation avec des établissements du département '.$val['nom_dep'];
	$lalegend[]	= $val['nom_dep'];
    }
    //print_r($data);
    //print_r($lalegend);
    // Create the Pie Graph.
    $graph = new PieGraph(500,250,"auto");
    $graph->SetAntiAliasing();
    $graph->SetFrame(false);
    $graph->title->Set('CA de l\'année par département');
    $graph->title->SetFont(FF_ARIAL,FS_NORMAL,11);
    $graph->title->SetColor('midnightblue');
    $graph->legend->Pos(0.05,0.1);
    $graph->subtitle->Set(' '.formatCurencyDisplay($total,0,' euros'));
    $graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,9);
    $graph->subtitle->SetColor('blue');

    // Create 3D pie plot
    $p1 = new PiePlot3d($data);
    $p1->SetTheme("water");
    $p1->SetCenter(0.33,0.5);
    $p1->SetSize(125);

    // Display the slice values
    $p1->value->SetFont(FF_ARIAL,FS_NORMAL,9);
    $p1->value->SetColor("darkblue@0.5");

    $p1->SetLegends($lalegend);

    $p1->SetCSIMTargets($targ,$alts);

    $graph->Add($p1);
    $graph->StrokeCSIM("Img.FactureAnneeParDepartement.php");
}


?>