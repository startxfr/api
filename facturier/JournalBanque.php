<?php
/*#########################################################################
#
#   name :       ListeDevis.php
#   desc :       Prospection list for sxprospec
#   categorie :  prospec page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZunoCore','ZView/JournalBanqueView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('facturier');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('journalBanque', 05, 'web');
if($PC->rcvP['action'] == 'searchJournalBanque') {
    if($PC->rcvP['libelle_jb'] != '')
	$data['libelle_jb'] = $PC->rcvP['libelle_jb'];
    if($PC->rcvP['dateStart_jb'] != '')
	$data['dateStart_jb'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['dateStart_jb']));
    if($PC->rcvP['dateEnd_jb'] != '')
	$data['dateEnd_jb']  = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['dateEnd_jb'].' 23:59:59'));
    if($PC->rcvP['montantMin_jb'] != '')
	$data['montantMin_jb'] = $PC->rcvP['montantMin_jb'];
    if($PC->rcvP['montantMax_jb'] != '')
	$data['montantMax_jb']  = $PC->rcvP['montantMax_jb'];
    if($PC->rcvP['sens'] != '')
	$data['sens']  = $PC->rcvP['sens'];
    if($PC->rcvP['ordre_jb'] != '')
	$ordre = 'ORDER BY '.$PC->rcvP['ordre_jb'];
    if($PC->rcvP['limit'] != '')
	$datas['limit'] = $PC->rcvP['limit'];
    else $datas['limit'] = '30';
    if($PC->rcvP['from'] != '')
	$datas['from'] = $PC->rcvP['from'];
    else $datas['from'] = '0';
    $req = new journalBanqueModel();
    $result = $req->getDataForSearchWeb('', $datas['from'], 'ALL', $ordre, $data);
    $datas['total'] = $result[1][0]['counter'];
    if($datas['total'] == '')
	$datas['total'] = '0';
    if($datas['limit'] == 'ALL') {
	$datas['limit'] = $datas['total'];
	$datas['from'] = 0;
    }
    $result = $req->getDataForSearchWeb('', $datas['from'], $datas['limit'], $ordre, $data);
    $datas['data'] = $result[1];
    $view = new journalBanqueView();
    echo $view->searchResult($datas,$PC->rcvP['result']);
    exit;
}
elseif ($PC->rcvP['action'] == 'groupedAction') {
    $bddtmp = new JournalBanqueModel();
    $list = $PC->rcvP['select'];
    $action = $PC->rcvP['groupedAction'];

    if (count($list) > 0) {
	$prodListString = '';
	foreach ($list as $k => $prod)
	    $prodListString .= ', \''.$prod.'\'';
	$prodListString = substr($prodListString,1);
    }

    if($action == 'delete') {
	$req = "SELECT id_jb,libelle_jb FROM journal_banque
		WHERE id_jb IN (".$prodListString.")
		GROUP BY id_jb ORDER BY id_jb ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		journalBanqueModel::markDeleteJournalBanqueInDB($prod['id_jb'],$PC->rcvP);
		$message.= "Ecriture bancaire ".$prod['id_jb']." - ".$prod['libelle_jb']." supprimé\n";
	    }
	    //$bddtmp->addActualite('', 'free', 'Lot de '.count($res).' fichiers d\'export comptable supprimés', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des écritures bancaires séléctionnées ne peuvent être supprimées</span>";
    }
    elseif($action == 'changeAttribute') {
	$req = "SELECT id_jb,libelle_jb FROM journal_banque
		WHERE id_jb IN (".$prodListString.")
		GROUP BY id_jb ORDER BY id_jb ASC";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $prod) {
		journalBanqueModel::changeAttributeJournalBanqueInDB($prod['id_jb'],$PC->rcvP);
		$message.= "changement des attributs de l'écriture bancaire ".$prod['id_jb']." - ".$prod['libelle_jb']."\n";
	    }
	    //$bddtmp->addActualite('', 'free', 'Lot de '.count($res).' fichiers d\'export comptable modifiés', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des écritures bancaires séléctionnées ne peuvent être modifiées</span>";
    }
    $message = $message.'<script type="text/javascript">setTimeout("window.location.href = \'JournalBanque.php\';",1000);</script>';
}
else {
    $req = new journalBanqueModel();
    $total = $req->getDataForSearchWeb('', '0', 'ALL', 'ORDER BY libelle_jb', $data);
    $total = $total[1][0]['counter'];
    $datas['total'] = $total;
    $result = $req->getDataForSearchWeb('', '0', '30', 'ORDER BY libelle_jb', $data);
    $result = $result[1];
    $datas['data'] = $result;
    $datas['form']['fournisseur_jb'] = $data['fournisseur_id'];
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $view = new journalBanqueView();
    $sortie = $view->searchResult($datas, '');
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>