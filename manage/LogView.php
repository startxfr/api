<?php
/*#########################################################################
#
#   name :       Log.php
#   desc :       interface to view log
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*-----------------------------------------------------------------------*/


if ($PC->rcvP['action'] == 'search')
{
	// on fabrique la requete en fonctions des criteres demandés
	$param = "WHERE ";
	if ( $PC->rcvP['date_slog_from'] != "" )
	{
		if ($PC->rcvP['heure_from'] == "")
		{
			$PC->rcvP['heure_from'] = "00:00";
		}
		$param .= "date_slog > '". DateHuman2Univ($PC->rcvP['date_slog_from'].' '.$PC->rcvP['heure_from']) ."' ";
	}
	if ( $PC->rcvP['date_slog_to'] != "" )
	{
		if ( $param != "WHERE " )
		{
			$param .= "AND ";
		}
		if ($PC->rcvP['heure_to'] == "")
		{
			$PC->rcvP['heure_to'] = "00:00";
		}
		$param .= "date_slog < '". DateHuman2Univ($PC->rcvP['date_slog_to'].' '.$PC->rcvP['heure_to']) ."' ";
	}
	if ( $PC->rcvP['level_slog'] != "" )
	{
		if ( $param != "WHERE " )
		{
			$param .= "AND ";
		}
		$param .= "level_slog <= '". $PC->rcvP['level_slog'] ."' ";
	}
	if ( $PC->rcvP['type_slog'] != "" )
	{
		if ( $param != "WHERE " )
		{
			$param .= "AND ";
		}
		$param .= "type_slog = '". $PC->rcvP['type_slog'] ."' ";
	}

	// si une requete a été construite on l'execute et on affiche le resultat
	if ( $param != "WHERE " )
	{
		$bddtmp = new Bdd($GLOBALS['LOG']['DBPool']);
		$bddtmp->makeRequeteAuto($GLOBALS['LOG']['DBTable'], '', $param);
		$res = $bddtmp->process();

		// remplissage du tableau
		foreach ($res as $key => $val)
		{
			$val["date_slog"] = DateUniv2Human($val["date_slog"], 'shortdetail');
			$tmptab .= templating('manage/Log_table.Row', $val);
		}

		// preparation du tableau et ajout à la page
		$tab['liste'] = $tmptab ;

		$content .= templating('manage/Log_table', $tab);
	}
}



// on affiche le cadre de selection des logs
$temp['input_date_from']  =inputDateTag('','date_slog_from',$PC->rcvP['date_slog_from']);
$temp['input_date_to']  =inputDateTag('','date_slog_to',$PC->rcvP['date_slog_to']);
$temp['select_heure_from'] = HtmlForm::addSelectHeure("heure_from");
$temp['select_heure_to'] = HtmlForm::addSelectHeure("heure_to");
$content = templating('manage/Log', $temp).$content;



/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();

?>
