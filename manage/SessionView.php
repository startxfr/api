<?php
/*#########################################################################
#
#   name :       SessionView.php
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
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*-----------------------------------------------------------------------*/


if ($PC->rcvP['action'] == 'search') {
    // on fabrique la requete en fonctions des criteres demandés
    $param = "WHERE ";
    //Type de critère pour la date
    if ($PC->rcvP['date_type'] == "fin" ) {
	$date_crit = "date_sess";
    }
    else {
	$date_crit = "datefin_sess";
    }
    // Date de debut
    if ($PC->rcvP['date_sess_from'] != "") {
	if ($PC->rcvP['heure_from'] == "") {
	    $PC->rcvP['heure_from'] = "00:00";
	}
	$param .= $date_crit." > '".DateHuman2Univ($PC->rcvP['date_sess_from'].' '.$PC->rcvP['heure_from'])."' ";
	$add_param = TRUE;
    }
    // Date de fin
    if ($PC->rcvP['date_sess_to'] != "") {
	if ($add_param) {
	    $param .= "AND ";
	}
	if ($PC->rcvP['heure_to'] == "") {
	    $PC->rcvP['heure_to'] = "00:00";
	}
	$param .= $date_crit." < '".DateHuman2Univ($PC->rcvP['date_sess_to'].' '.$PC->rcvP['heure_to'])."' ";
	$add_param = TRUE;
    }
    // ID de session
    if ($PC->rcvP['id_sess'] != "") {
	if ($add_param) {
	    $param .= "AND ";
	}
	$param .= "id_sess = '".$PC->rcvP['id_sess']."' ";
	$add_param = TRUE;
    }
    // Utilisateur de la session
    if ($PC->rcvP['user_sess'] != "") {
	if ($add_param) {
	    $param .= "AND ";
	}
	$param .= "user_sess = '".$PC->rcvP['user_sess']."' ";
	$add_param = TRUE;
    }
    //Channel de session
    if ($PC->rcvP['channel_sess'] == "admin") {
	$PC->rcvP['channel_sess'] = "admin";
    }
    else {
	$PC->rcvP['channel_sess'] = "normal";
    }
    if ($add_param) {
	$param .= "AND ";
    }
    $param .= "channel_sess = '".$PC->rcvP['channel_sess']."' ";

    // si une requete a été construite on l'execute et on affiche le resultat
    $bddtmp = new Bdd($GLOBALS['CHANNEL_'.$PC->rcvP['channel_sess']]['SessDBPool']);
    $bddtmp->makeRequeteAuto('session','',$param);
    $res = $bddtmp->process();
    // remplissage du tableau
    foreach ($res as $key => $val) {
	if($color_row) {
	    $color_row = FALSE;
	    $val['class']	='altern0';
	}
	else {
	    $color_row = TRUE;
	    $val['class']	='altern1';
	}
	$val["date_sess"] = DateUniv2Human($val["date_sess"], 'shortdetail');
	$val["datefin_sess"] = DateUniv2Human($val["datefin_sess"], 'shortdetail');
	$tmptab .= templating('manage/SessionView.TableRow', $val);
    }
    // preparation du tableau et ajout à la page
    $tab['liste'] = $tmptab ;
    $content .= templating('manage/SessionView.Table', $tab);

}
else {
    $PC->rcvP['date_sess_from'] =
	    $PC->rcvP['date_sess_to'] =
	    $PC->rcvP['id_sess'] =
	    $PC->rcvP['user_sess'] = "";
}


// on affiche le cadre de selection des logs
$PC->rcvP['input_date_from']  =inputDateTag('','date_sess_from',$PC->rcvP['date_sess_from']);
$PC->rcvP['input_date_to']  =inputDateTag('','date_sess_to',$PC->rcvP['date_sess_to']);
$PC->rcvP['select_heure_from'] = HtmlForm::addSelectHeure("heure_from");
$PC->rcvP['select_heure_to'] = HtmlForm::addSelectHeure("heure_to");
$content = templating('manage/SessionView',$PC->rcvP).$content;



/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->AddBodyContent("");
$out->Process();

?>
