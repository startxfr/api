<?php
/*#########################################################################
#
#   name :       SessionDetail.php
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


if (($PC->rcvP['id'] != '')and($PC->rcvP['channel'] != '')) {
    $in['id_sess']	= $PC->rcvP['id'];
    $bddtmp = new Bdd($GLOBALS['CHANNEL_'.$PC->rcvP['channel']]['SessDBPool']);
    $bddtmp->makeRequeteAuto('session',$in);
    $detail = $bddtmp->process();
    $session = $detail[0];
    $session["date_sess"] = DateUniv2Human($session["date_sess"], 'simpleDH');
    $session["datefin_sess"] = DateUniv2Human($session["datefin_sess"], 'simpleDH');

    //On recupère les log de cette session
    $in1['session_slog']	= $PC->rcvP['id'];
    $bddtmp->makeRequeteAuto($GLOBALS['LOG']['DBTable'],$in1);
    $res = $bddtmp->process();
    // remplissage du tableau
    foreach ($res as $key => $val) {
	$val["date_slog"] = DateUniv2Human($val["date_slog"], 'shortdetail');
	$tmptab .= templating('manage/Log_table.Row', $val);
    }
    // preparation du tableau et ajout à la page
    $tab['liste'] = $tmptab ;
    $session['liste'] = templating('manage/Log_table',$tab);
    // preparation du portlet final
    $content = templating('manage/SessionDetail',$session);
}
else {
    header("Location: SessionView.php");
}


/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();

?>
