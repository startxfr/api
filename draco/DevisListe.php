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
loadPlugin(array('ZunoCore','ZView/DevisView'));
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


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('devis', 05, 'web');
if($PC->rcvP['action'] == 'searchDevis')
{
	if($PC->rcvP['entreprise_dev'] != '')
		$data['entreprise_dev'] = $PC->rcvP['entreprise_dev'];
	if($PC->rcvP['titre_dev'] != '')
		$data['titre_dev'] = $PC->rcvP['titre_dev'];
	if($PC->rcvP['affaire_dev'] != '')
		$data['affaire_dev'] = $PC->rcvP['affaire_dev'];
	if($PC->rcvP['status_dev'] != '')
		$data['status_dev'] = $PC->rcvP['status_dev'];
	if($PC->rcvP['commercial_dev'] != '')
		$data['commercial_dev'] = $PC->rcvP['commercial_dev'];
	if($PC->rcvP['cp_ent'] != '')
		$data['cp_ent'] = $PC->rcvP['cp_ent'];
	if($PC->rcvP['sommeHT_dev'] != '')
		$data['sommeHT_dev'] = $PC->rcvP['sommeHT_dev'];
	if($PC->rcvP['sommeHT_dev2'] != '')
		$data['sommeHT_dev2'] = $PC->rcvP['sommeHT_dev2'];
	if($PC->rcvP['order'] != '')
		$ordre = 'ORDER BY '.$PC->rcvP['order'].' '.$PC->rcvP['orderSens'];
	if($PC->rcvP['limit'] != '')
		$datas['limit'] = $PC->rcvP['limit'];
	else
		$datas['limit'] = '30';
	if($PC->rcvP['from'] != '')
		$datas['from'] = $PC->rcvP['from'];
	else
		$datas['from'] = '0';
	$req = new devisModel();
	$result = $req->getDataForSearchWeb('', $datas['from'], 'ALL', $ordre, $data);
	$datas['total'] = $result[1][0]['COUNT(*)'];
	if($datas['limit'] == 'ALL')
	{
		$datas['limit'] = $datas['total'];
		$datas['from'] = 0;
	}
	$datas['order'] = $PC->rcvP['order'];
	$datas['orderSens'] = $PC->rcvP['orderSens'];
	$result = $req->getDataForSearchWeb('', $datas['from'], $datas['limit'], $ordre, $data);
	$datas['data'] = $result[1];
	$view = new devisView();
	echo $view->searchResult($datas, 'result');
	exit;
}
elseif($PC->rcvP['action'] == 'exportTableur')
{
	$req = new devisModel();
	$result = $req->getDataForExportTableur($PC->rcvP['select']);
	$gnose = new devisGnose();
	$file = $gnose->DevisExportTableurConverter($result[1],$PC->rcvP['exportType']);
	PushFileToBrowser($file);
	exit;
}
elseif ($PC->rcvP['action'] == 'groupedAction') {
    $bddtmp = new devisModel();
    $list = $PC->rcvP['select'];
    $action = $PC->rcvP['groupedAction'];

    if (count($list) > 0) {
	$devListString = '';
	foreach ($list as $k => $dev)
	    $devListString .= ', \''.$dev.'\'';
	$devListString = substr($devListString,1);
	$reqAdd = 'AND id_dev IN ('.$devListString.')';
    }

    if($action == 'reinit') {
	$req = "SELECT devis.id_dev FROM devis
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_dev IN (".$devListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_dev NOT IN (2,7)
		GROUP BY id_dev ORDER BY id_dev ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $dev) {
		devisModel::markReinitDevisInDB($dev['id_dev'],$PC->rcvP);
		$message.= "re-initialisation du devis ".$dev['id_dev']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' devis re-initialisés', $message);
	    $message = "<span class=\"importantgreen\">$message</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des devis séléctionnés ne peut être re-initialisé</span>";
    }
    elseif($action == 'perdu') {
	$req = "SELECT devis.id_dev FROM devis
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_dev IN (".$devListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_dev NOT IN (2,6,7)
		GROUP BY id_dev ORDER BY id_dev ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $dev) {
		devisModel::markPerduDevisInDB($dev['id_dev'],$PC->rcvP);
		$message.= "Devis ".$dev['id_dev']." marqué comme perdu \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' devis marqués comme perdu', $message);
	    $message = "<span class=\"importantgreen\">$message</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des devis séléctionnés ne peut être marqué comme perdu</span>";
    }
    elseif($action == 'envoye') {
	$req = "SELECT devis.id_dev FROM devis
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_dev IN (".$devListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_dev IN (3,6)
		GROUP BY id_dev ORDER BY id_dev ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $dev) {
		devisModel::markEnvoyeDevisInDB($dev['id_dev'],$PC->rcvP);
		$message.= "Devis ".$dev['id_dev']." marqué comme envoyé \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' devis marqués comme envoyés', $message);
	    $message = "<span class=\"importantgreen\">$message</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des devis séléctionnés ne peut être marqué comme envoyé</span>";
    }
    elseif($action == 'delete') {
	$req = "SELECT devis.id_dev FROM devis
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_dev IN (".$devListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_dev IN (1,3)
		GROUP BY id_dev ORDER BY id_dev ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $dev) {
		devisModel::markDeleteDevisInDB($dev['id_dev'],$PC->rcvP);
		$message.= "Devis ".$dev['id_dev']." supprimé \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' devis supprimés', $message);
	    $message = "<span class=\"importantgreen\">$message</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des devis séléctionnés ne peut être supprimé</span>";
    }
    elseif($action == 'changeAttribute') {
	$req = "SELECT devis.id_dev FROM devis
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		WHERE id_dev IN (".$devListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND status_dev IN (1,3,4,5,6)
		GROUP BY id_dev ORDER BY id_dev ASC";
     $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $dev) {
		devisModel::changeAttributeDevisInDB($dev['id_dev'],$PC->rcvP);
		$message.= "changement des attributs du devis ".$dev['id_dev']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' devis modifiés', $message);
	    $message = "<span class=\"importantblue\">$message</span>";
	}
	else $message = "<span class=\"importantblue\">Aucun des devis séléctionnés ne peut être modifié</span>";
    }
    elseif($action == 'archivate') {
	$req = "SELECT devis.id_dev FROM devis
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
		LEFT JOIN facture ON commande.id_cmd = facture.commande_fact
		WHERE id_dev IN (".$devListString.")
		AND archived_aff = '0'
		AND actif_aff = '1'
		AND (status_dev IS NULL OR  status_dev IN (1,2,5,6,7))
		AND (status_cmd IS NULL OR  status_cmd IN (9,10))
		AND (status_fact IS NULL OR  status_fact IN (6,7))
		GROUP BY id_dev ORDER BY id_dev ASC";
        $bddtmp->makeRequeteFree($req);
        $res = $bddtmp->process();
	if(count($res) > 0) {
	    foreach($res as $k => $dev) {
		devisModel::archivateDevisInDB($dev['id_dev'],$PC->rcvP);
		$message.= "archivage de l'devis ".$dev['id_dev']." \n";
	    }
	    $bddtmp->addActualite('', 'free', 'Lot de '.count($res).' devis archivés', $message);
	    $message = "<span class=\"importantblue\">$message</span>";
	}
	else $message = "<span class=\"importantblue\">Aucune des devis séléctionnés ne peut être archivé</span>";
    }
    $message = $message.'<script type="text/javascript">setTimeout("window.location.href = \'DevisListe.php\';",1000);</script>';
}
else
{
	$req = new devisModel();
	$total = $req->getDataForSearchWeb('', '0', 'ALL');
	$total = $total[1][0]['COUNT(*)'];
	$datas['total'] = $total;
	$result = $req->getDataForSearchWeb('');
	$result = $result[1];
	$datas['data'] = $result;
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$req = "SELECT * from ref_statusdevis ";
	$sqlConn->makeRequeteFree($req);
	$status = $sqlConn->process2();
	if(is_array($status[1]))
		foreach($status[1] as $v)
			$datas['status'][$v['id_stdev']] = $v['nom_stdev'];
	$datas['from'] = 0;
	$datas['limit'] = 30;
	$view = new devisView();
	$sortie = $view->searchResult($datas, '');
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($message.$sortie);
$out->Process();
?>
