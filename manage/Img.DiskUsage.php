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

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
if($PC->rcvP['path'] != '')
{
	$lepath = $PC->rcvP['path'];
}
elseif($PC->rcvG['path'] != '')
{
	$lepath = $PC->rcvG['path'];
}
else
{
	$lepath = "";
}
if($PC->rcvG['uri'] != '')
{
	$leuri = $PC->rcvG['uri'];
}
else
{
	$leuri = "Img.DiskUsage.php";
}


if(FileIsFileExist($GLOBALS['REP']['appli'].$lepath))
{
	$liste = FileDirectoryDetail($GLOBALS['REP']['appli'].$lepath,'',1);
	$exeptfolder = array('.svn');
	foreach($liste as $key => $val)
		{
		if (!in_array($key,$exeptfolder))
			{ if ($val['type'] == "repertoire")
				{ $lesdata[$val['nom']."/"]	=$lesdata[$val['nom']."/"]+$val['Osize']; }
			  else	{ $lesdata[$val['type']]	=$lesdata[$val['type']]+$val['Osize']; }
			  $total	=$total+$val['Osize'];
			}
		}
	//print_r($lesdata);

	include ("../plugin/jpgraph/src/jpgraph.php");
	include ("../plugin/jpgraph/src/jpgraph_pie.php");
	include ("../plugin/jpgraph/src/jpgraph_pie3d.php");

	// Some data
	foreach($lesdata as $key => $val)
		{ $data[]	= round(($val/$total),2);
		  if (strrpos($key, "/") === false)
			{ $targ[]	= "#";
			  $alts[]	= "Taille: ".FileConvertSize2Human($val); }
		  else	{ $targ[]	= $leuri."?path=".$lepath.$key;
			  $alts[]	= "Taille: ".FileConvertSize2Human($val).". Cliquez pour voir le détail"; }
		  $lalegend[]	= $key; }
	//print_r($data);
	//print_r($lalegend);
	// Create the Pie Graph.
	$graph = new PieGraph(500,375,"auto");
	$graph->SetFrame(false);

	// Set A title for the plot
	$graph->title->Set("REP:: ".$lepath." (".FileConvertSize2Human($total).")");
	$graph->title->SetFont(FF_VERDANA,FS_BOLD,16);
	$graph->title->SetColor("darkblue");
	$graph->legend->Pos(0.05,0.1);

	// Create 3D pie plot
	$p1 = new PiePlot3d($data);
	$p1->SetTheme("sand");
	$p1->SetCenter(0.35);
	$p1->SetSize(150);

	// Adjust projection angle
	$p1->SetAngle(45);

	// Adjsut angle for first slice
	$p1->SetStartAngle(45);

	// Display the slice values
	$p1->value->SetFont(FF_ARIAL,FS_BOLD,10);
	$p1->value->SetColor("navy");

	// Add colored edges to the 3D pie
	// NOTE: You can't have exploded slices with edges!
	//$p1->SetEdge("navy");

	$p1->SetLegends($lalegend);
	$p1->SetCSIMTargets($targ,$alts);

	$graph->Add($p1);
	$graph->StrokeCSIM("Img.DiskUsage.php");
	}

?>
