<?php
/*#########################################################################
#
#   name :       ListeCommande.php
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
loadPlugin(array('ZunoCore','ZView/FactureView'));
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

ini_set("memory_limit","20m");
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
aiJeLeDroit('facture', 05, 'web');
if ($PC->rcvP['action'] == 'reset') {
    unset($_SESSION['FactureSearch']);
    unset($_SESSION['FactureSearchQuery']);
}
else {
    $_SESSION['FactureSearch']['status_fact'] = '6';

    // Stockage de l'ordre de tri
    if (isset($PC->rcvP['order']))		$_SESSION['FactureSearch']['order'] = $PC->rcvP['order'];

    if (isset($PC->rcvG['viewmode']))		$_SESSION['FactureSearch']['view']['viewmode'] = $PC->rcvG['viewmode'];
    if (isset($PC->rcvP['viewmode']))		$_SESSION['FactureSearch']['view']['viewmode'] = $PC->rcvP['viewmode'];

    if (isset($PC->rcvG['_from']))		$_SESSION['FactureSearch']['view']['_from'] = $PC->rcvG['_from'];
    if (isset($PC->rcvP['_from']))		$_SESSION['FactureSearch']['view']['_from'] = $PC->rcvP['_from'];

    if (isset($PC->rcvG['_limit']))		$_SESSION['FactureSearch']['view']['_limit'] = $PC->rcvG['_limit'];
    if (isset($PC->rcvP['_limit']))		$_SESSION['FactureSearch']['view']['_limit'] = $PC->rcvP['_limit'];
}


if ($PC->rcvG['action'] == 'exportTableur') {
    $req = new factureModel();
    $result = $req->getDataForSearchWeb('',0,1000,'ORDER BY id_fact DESC',array('status_fact', 6));
    $gnose = new factureGnose();
    $file = $gnose->FactureExportTableurConverter($result[1],0);
    PushFileToBrowser($file);
    exit;
}

$req = new factureModel();
$total = $req->getDataForSearchWeb('', '0', 'ALL', 'ORDER BY id_fact DESC',array('status_fact' => 6));
$total = $total[1][0]['COUNT(*)'];
$datas['total'] = $total;
$result = $req->getDataForSearchWeb('','0', '30', 'ORDER BY id_fact DESC',array('status_fact' => 6));
$result = $result[1];
$datas['data'] = $result;
$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$req = "SELECT * from ref_statusfacture ";
$sqlConn->makeRequeteFree($req);
$status = $sqlConn->process2();
if(is_array($status[1]))
    foreach($status[1] as $v)
        $datas['status'][$v['id_stfact']] = $v['nom_stfact'];
$datas['from'] = 0;
$datas['limit'] = 30;
$view = new factureView();
$sortie = $view->searchResult($datas, '');

//$sortie .= BoxFactureListe($_SESSION['FactureSearch']);
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
