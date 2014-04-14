<?php
/*#########################################################################
#
#   name :       Application.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id: Affaire.php 2318 2009-03-03 23:33:47Z cl $
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

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
	include (dirname(__FILE__).'/../../plugin/jpgraph/src/jpgraph.php');
	include (dirname(__FILE__).'/../../plugin/jpgraph/src/jpgraph_pie.php');
	include (dirname(__FILE__).'/../../plugin/jpgraph/src/jpgraph_pie3d.php');


if($PC->rcvG['type'] == 'typeAffActives' or $PC->rcvG['type'] == 'typeAffGlobal')
{
	if($PC->rcvG['type'] == 'typeAffActives')
		$req="SELECT A1.id_typro id,A1.nom_typro ref_typeproj, COUNT(A2.id_aff) projet
			FROM ref_typeproj A1, affaire A2
			WHERE A1.id_typro = A2.typeproj_aff AND A2.actif_aff = '1'
			GROUP BY A1.id_typro
			ORDER BY projet DESC";
	else  $req="SELECT A1.id_typro id,A1.nom_typro ref_typeproj, COUNT(A2.id_aff) projet
			FROM ref_typeproj A1, affaire A2
			WHERE A1.id_typro = A2.typeproj_aff
			GROUP BY A1.id_typro
			ORDER BY projet DESC";
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree($req);
	$lesdata = $bddtmp->process();
	$total = 0;

	if (count($lesdata) > 0)
	{
		foreach($lesdata as $key => $val) $total = $total+$val['projet'];
		foreach($lesdata as $key => $val)
		{
			$data[]	= round(($val['projet']/$total),2);
			$lalegend[]	= $val['ref_typeproj'];
		}
		// Create the Pie Graph.
		$graph = new PieGraph(290,250,"auto");
		$graph->SetFrame(false);
		$graph->legend->Pos(0,0.60);

		// Create 3D pie plot
		$p1 = new PiePlot3d($data);
		$p1->SetTheme("sand");
		$p1->SetCenter(0.5,0.3);
		$p1->SetSize(130);

		$p1->SetAngle(40);	// Adjust projection angle
		$p1->SetStartAngle(45); // Adjsut angle for first slice

		$p1->value->SetFont(FF_ARIAL,FS_BOLD,9); // Display the slice values
		$p1->value->SetColor("brown");

		$p1->SetLegends($lalegend);

		$graph->Add($p1);
		$graph->Stroke();
	}
}

?>
