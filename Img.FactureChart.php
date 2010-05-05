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


$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree("SELECT A.status_fact AS status, SUM(A.sommeHT_fact) AS somme, B.nom_stfact AS nom
				  FROM `facture` A, ref_statusfacture B
				  WHERE B.id_stfact = A.status_fact  AND A.status_fact != 6 AND A.status_fact != 7 AND A.type_fact = 'Facture'
				  GROUP BY A.status_fact");
$lesdata = $bddtmp->process();

if (count($lesdata) > 0) {
// Some data
    foreach($lesdata as $key => $val) {
	$data[]	= $val['somme'];
	$targ[]	= "?status_fact=".$val['status'];
	$alts[]	= $val['somme']." euros";
	$lalegend[]	= utf8_decode($val['nom']);
    }



    $p1 = new PiePlot3d($data);
    $p1->SetTheme("water");
    $p1->SetCenter(0.33,0.6);
    $p1->SetSize(120);
    $p1->value->SetFont(FF_ARIAL,FS_BOLD,9);
    $p1->value->SetColor("mediumpurple");
    $p1->SetLegends($lalegend);
    $p1->SetCSIMTargets($targ,$alts);

    $graph = new PieGraph(480,220,"auto");
    $graph->SetAntiAliasing();
    $graph->SetFrame(false);
    $graph->legend->Pos(0,0);
    $graph->Add($p1);
    $graph->StrokeCSIM("../Img.FactureChart.php");
}


?>
