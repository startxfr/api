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

$req = "SELECT sum(sommeHT_fact) AS counter, id_condreg, nom_condreg
		FROM facture
		LEFT JOIN ref_condireglement ON ref_condireglement.id_condreg = facture.condireglement_fact
		GROUP BY id_condreg
		ORDER BY `counter` DESC LIMIT 0, 5";
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree($req);
$lesdata = $bddtmp->process();
$reqA = "SELECT sum(sommeHT_fact) AS counter, id_condreg, nom_condreg
		FROM facture
		LEFT JOIN ref_condireglement ON ref_condireglement.id_condreg = facture.condireglement_fact
		GROUP BY id_condreg
		ORDER BY `counter` DESC LIMIT 5, 200";
$bddtmp->makeRequeteFree($reqA);
$lesdataAutres = $bddtmp->process();
$total = 0;
$totalAutres = 0;

if (count($lesdata) > 0) {
    if(count($lesdata) == 5) {
	foreach($lesdataAutres as $key => $val) {
	    $totalAutres = $totalAutres+$val['counter'];
	}
	$lesdata[] = array('counter'=>$totalAutres,'id_condreg'=>'','nom_condreg'=>'Autres...');
    }

    foreach($lesdata as $key => $val) {
	$total = $total+$val['counter'];
    }

    // Some data
    foreach($lesdata as $key => $val) {
	$data[]	= round(($val['counter']/$total),2);
	$targ[]	= "FactureListe.php?condireglement_fact=".$val['id_condreg'];
	$alts[]	= formatCurencyDisplay($val['counter'],0,' euros').' exigibles sous '.$val['nom_condreg'];
	$lalegend[]	= $val['nom_condreg'];
    }
    //print_r($data);
    //print_r($lalegend);
    // Create the Pie Graph.
    $graph = new PieGraph(500,300,"auto");
    $graph->SetAntiAliasing();
    $graph->SetFrame(false);
    $graph->title->Set('Conditions de règlement accordés');
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
    $graph->StrokeCSIM("Img.FactureCondiReglement.php");
}


?>
