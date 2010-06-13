<?php
/*#########################################################################
#
#   name :       ListeAffaire.php
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
loadPlugin(array('ZunoCore','ZView/AffaireView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('draco');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

aiJeLeDroit('affaire', 05, 'web');
if($PC->rcvP['action'] == 'searchAffaire') {
    if($PC->rcvP['limit'] != '')
	$datas['limit'] = $PC->rcvP['limit'];
    else	$datas['limit'] = '30';
    if($PC->rcvP['from'] != '')
	$datas['from'] = $PC->rcvP['from'];
    else	$datas['from'] = '0';
    if($PC->rcvP['order'] == '')
	$PC->rcvP['order'] = 'id_aff';
    $req = new affaireModel();
    $result = $req->getDataForSearchWeb($PC->rcvP, 'ALL', $datas['from'], '', $PC->rcvP['order'], $PC->rcvP['orderSens']);
    $datas['total'] = $result[1][0]['counter'];
    if($datas['limit'] == 'ALL') {
	$datas['limit'] = $datas['total'];
	$datas['from'] = 0;
    }
    $datas['order'] = $PC->rcvP['order'];
    $datas['orderSens'] = $PC->rcvP['orderSens'];
    $result = $req->getDataForSearchWeb($PC->rcvP, $datas['limit'], $datas['from'], '', $PC->rcvP['order'], $PC->rcvP['orderSens']);
    $datas['data'] = $result[1];
    $view = new affaireView();
    echo $view->searchResult($datas,'result');
    exit;
}
elseif($PC->rcvP['action'] == 'exportTableur') {
    $req = new affaireModel();
    $result = $req->getDataForExportTableur($PC->rcvP['select']);
    $file = $req->AffaireExportTableurConverter($result[1],$PC->rcvP['exportType']);

    PushFileToBrowser($file);
    exit;
}
elseif ($PC->rcvP['action'] == 'groupedAction') {
    $bddtmp = new AffaireModel();
    $list = $PC->rcvP['select'];
    $action = $PC->rcvP['groupedAction'];

    if (count($list) > 0) {
	$affListString = '';
	foreach ($list as $k => $aff)
	    $affListString .= ', \''.$aff.'\'';
	$affListString = substr($affListString,1);
	$reqAdd = 'AND id_aff IN ('.$affListString.')';
    }

    if($action == 'activate') {
	$req = "SELECT affaire.id_aff FROM affaire
		WHERE id_aff IN (".$affListString.")
		AND archived_aff = '0'
		AND actif_aff = '0'
		GROUP BY id_aff ORDER BY id_aff ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $aff) {
		affaireModel::activateAffaireInDB($aff['id_aff']);
		$message.= "re-activation de l'affaire ".$aff['id_aff']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' affaires re-activées', $message);
	    $message = "<span class=\"importantgreen\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des affaires séléctionnées ne peuvent être activée</span>";
    }
    elseif($action == 'desactivate') {
	$req = "SELECT affaire.id_aff FROM affaire
		WHERE id_aff IN (".$affListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		GROUP BY id_aff ORDER BY id_aff ASC";
	$bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $aff) {
		affaireModel::desactivateAffaireInDB($aff['id_aff']);
		$message.= "désactivation de l'affaire ".$aff['id_aff']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' affaires désactivées', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des affaires séléctionnées ne peuvent être désactivée</span>";
    }
    elseif($action == 'changeAttribute') {
	$req = "SELECT affaire.id_aff FROM affaire
		WHERE id_aff IN (".$affListString.")
		AND archived_aff = '0'
		GROUP BY id_aff ORDER BY id_aff ASC";
	$bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $aff) {
		affaireModel::changeAttributeAffaireInDB($aff['id_aff'],$PC->rcvP);
		$message.= "changement des attributs de l'affaire ".$aff['id_aff']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' affaires modifiées', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des affaires séléctionnées ne peuvent être modifiée</span>";
    }
    elseif($action == 'archivate') {
	$req = "SELECT affaire.id_aff FROM affaire
		LEFT JOIN devis ON devis.affaire_dev = affaire.id_aff
		LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
		LEFT JOIN facture ON commande.id_cmd = facture.commande_fact
		WHERE id_aff IN (".$affListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND (status_dev IS NULL OR  status_dev IN (1,2,5,6,7))
		AND (status_cmd IS NULL OR  status_cmd IN (9,10))
		AND (status_fact IS NULL OR  status_fact IN (6,7))
		GROUP BY id_aff ORDER BY id_aff ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $aff) {
		affaireModel::archivateAffaireInDB($aff['id_aff']);
		$message.= "archivage de l'affaire ".$aff['id_aff']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' affaires archivées', $message);
	    $message = "<span class=\"importantblue\">".nl2br($message)."</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des affaires séléctionnées ne peuvent être archivée</span>";
    }
    $message = $message.'<script type="text/javascript">setTimeout("window.location.href = \'ListeAffaires.php\';",1000);</script>';
}
else {
    $datas['from'] = 0;
    $datas['limit'] = 30;
    $datas['order'] = 'id_aff';
    $datas['orderSens'] = 'DESC';
    $datas['actif_aff'] = 1;
    $datas['typeproj_aff'] = $PC->rcvG['typeaff'];
    $req = new affaireModel();
    $total = $req->getDataForSearchWeb($datas, 'ALL', $datas['from']);
    $datas['total'] = $total[1][0]['counter'];
    $result = $req->getDataForSearchWeb($datas, $datas['limit'], $datas['from']);
    $datas['data'] = $result[1];
    $view = new affaireView();
    $sortie = $view->searchResult($datas);
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();

?>
