<?php
/*#########################################################################
#
#   name :       Application.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id: Img.FactureParDepartement.php 3628 2010-01-17 01:46:21Z cl $
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

$maxLigne = '10';
$year = date('Y');


$req = "SELECT sum( facture_produit.prix *  facture_produit.quantite ) AS counter, id_produit,nom_prodfam
	FROM facture,facture_produit
	LEFT JOIN produit ON id_produit = id_prod
	LEFT JOIN ref_prodfamille ON id_prodfam = famille_prod
	WHERE daterecord_fact >= '".$year."' and daterecord_fact < '".($year+1)."'
	AND id_fact = id_facture
	GROUP BY famille_prod
	ORDER BY `counter` DESC
	LIMIT 0 , ".$maxLigne;
$reqA = "SELECT sum( facture_produit.prix *  facture_produit.quantite ) AS counter, id_produit,nom_prodfam
	FROM facture,facture_produit
	LEFT JOIN produit ON id_produit = id_prod
	LEFT JOIN ref_prodfamille ON id_prodfam = famille_prod
	WHERE daterecord_fact >= '".$year."' and daterecord_fact < '".($year+1)."'
	AND id_fact = id_facture
	GROUP BY famille_prod
	ORDER BY `counter` DESC
	LIMIT ".$maxLigne.", 5000";
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree($req);
$lesdata = $bddtmp->process();
$bddtmp->makeRequeteFree($reqA);
$lesdataAutres = $bddtmp->process();
$total = 0;
$totalAutres = 0;

if (count($lesdata) > 0) {
    if(count($lesdata) == $maxLigne) {
	foreach($lesdataAutres as $key => $val) {
	    $totalAutres = $totalAutres+$val['counter'];
	}
	$lesdata[] = array('counter'=>$totalAutres,'id_produit'=>'Autres...','nom_prodfam'=>'Autres...');
    }

    foreach($lesdata as $key => $val) {
	$total = $total+$val['counter'];
    }

    // Some data
    foreach($lesdata as $key => $val) {
	$data[]	= round(($val['counter']/$total),2);
	$lalegend[]	= substr($val['nom_prodfam'],0,15);
    }
    //print_r($data);
    //print_r($lalegend);
    // Create the Pie Graph.
    $graph = new PieGraph(500,250,"auto");
    $graph->SetAntiAliasing();
    $graph->SetFrame(false);
    $graph->title->Set('CA annuel par famille de produits');
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

    $graph->Add($p1);
    $graph->Stroke();
}


?>