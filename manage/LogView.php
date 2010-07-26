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


if ($PC->rcvP['action'] == 'search') {
    // on fabrique la requete en fonctions des criteres demandés
    $param = "WHERE ";
    if ( $PC->rcvP['date_slog_from'] != "" ) {
	if ($PC->rcvP['heure_from'] == "")
	    $PC->rcvP['heure_from'] = "00:00";
	$param .= "date_log > ". DateUniv2Timestamp(DateHuman2Univ($PC->rcvP['date_slog_from'].' '.$PC->rcvP['heure_from'])) ." AND ";
    }
    if ( $PC->rcvP['date_slog_to'] != "" ) {
	if ($PC->rcvP['heure_to'] == "")
	    $PC->rcvP['heure_to'] = "00:00";
	$param .= "date_log < ". DateUniv2Timestamp(DateHuman2Univ($PC->rcvP['date_slog_to'].' '.$PC->rcvP['heure_to'])) ." AND ";
    }
    if ( $PC->rcvP['component_log'] != "" )
	$param .= "component_log LIKE '%". $PC->rcvP['component_log'] ."%' AND ";
    if ( $PC->rcvP['session_log'] != "" )
	$param .= "session_log LIKE '%". $PC->rcvP['session_log'] ."%' AND ";
    if ( $PC->rcvP['level_log'] != "" )
	$param .= "level_log LIKE '%". $PC->rcvP['level_log'] ."%' AND ";
    if ( $PC->rcvP['channel_log'] != "" )
	$param .= "channel_log LIKE '%". $PC->rcvP['channel_log'] ."%' AND ";

    // si une requete a été construite on l'execute et on affiche le resultat
    if ( $param != "WHERE " )
	$param = substr($param, 0, -4);
    else $param = null;

    $GLOBALS['logSearchSqlQuery'] = 'SELECT * FROM log '.$param.' ORDER BY id_log DESC LIMIT 0, 50';
}
else {
    $GLOBALS['logSearchSqlQuery'] = 'SELECT * FROM log ORDER BY id_log DESC LIMIT 0, 50';
}

$bddtmp = new Bdd($GLOBALS['LOG_DB']['base_pool']);
$bddtmp->makeRequeteFree($GLOBALS['logSearchSqlQuery']);
$res = $bddtmp->process();

$tab['liste'] = '';
if(count($res) > 0) {
    $tmptab = '';
    foreach ($res as $key => $val) {
	$val["trace_log"] = htmlentities($val["trace_log"], ENT_QUOTES);
	$val["date_log"] = DateUniv2Human(DateTimestamp2Univ(substr($val["date_log"], 0, strpos($val["date_log"], '.'))), 'shortdetail').' '.substr($val["date_log"],strpos($val["date_log"], '.')+1).'ms';
	$tmptab .= templating('manage/Log_table.Row', $val);
    }
    $tab['liste'] = $tmptab ;
}
$titre = 'Résultat de votre recherche';
$corps = templating('manage/Log_table', $tab);
$pied  = '';
$content = generateZBox($titre, $titre, $corps, $pied,'searchLogResult');



// on affiche le cadre de selection des logs
$temp['channel']  =inputTag('', 'channel_log','','','',$PC->rcvP['channel_log']);
$temp['component']  =inputTag('', 'component_log','','','',$PC->rcvP['component_log']);
$temp['session']  =inputTag('', 'session_log','','','',$PC->rcvP['session_log']);
$temp['level']  =inputTag('', 'level_log','','','',$PC->rcvP['level_log']);
$temp['input_date_from']  =inputDateTag('','date_slog_from',$PC->rcvP['date_slog_from']);
$temp['input_date_to']  =inputDateTag('','date_slog_to',$PC->rcvP['date_slog_to']);
$temp['select_heure_from'] = HtmlForm::addSelectHeure("heure_from",$PC->rcvP['heure_from']);
$temp['select_heure_to'] = HtmlForm::addSelectHeure("heure_to",$PC->rcvP['heure_to']);


$titre = 'Recherche de log';
$corps = templating('manage/Log', $temp);
$pied  = '<a  href="javascript:document.SearchLog.reset()"><img align="middle" title="Effacer" alt="Effacer" name="img" src="../img/prospec/cancel.png"> Recommencer</a>
	  <a  href="javascript:document.SearchLog.submit()"><img align="middle" title="../img/prospec/record.png" alt="../img/prospec/record.png" name="img" src="../img/prospec/record.png"> Rechercher</a>';
$content = generateZBox($titre, $titre, $corps, $pied,'searchLog').$content;



/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();

?>
