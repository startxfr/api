<?php
/*#########################################################################
#
#   name :       page.php
#   desc :       Display page content
#   categorie :  page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/AffaireView', 'ZModels/ActualiteModel'));

// Whe get the page context
$PC = new PageContext('draco');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);

$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if($PC->rcvG['action'] == 'actuAffaire') {
    aiJeLeDroit('actualite', 10, 'web');
    $sql = new actualiteModel();
    $result = $sql->getData4Affaire($PC->rcvG['id_aff']);
    $view = new affaireView();
    echo $view->popupActu($result[1]);
    exit;
}
elseif($PC->rcvG['id_aff'] != '') {
    $sortie = viewFiche($PC->rcvG['id_aff'], 'affaire', '', 'non', 'web', true);
}
elseif($PC->rcvP['action'] == 'modif') {
    aiJeLeDroit('affaire', 15, 'web');
    $req = new affaireModel();
    $id = $PC->rcvP['id_aff'];
    unset($PC->rcvP['id_aff']);
    $result = $req->update($PC->rcvP, $id);
    if($result[0]) {
	echo viewFiche($id, 'affaire', 'interneInfos', 'non', 'web', true, 'Enregistré');
    }
    else {
	echo viewFiche($id, 'affaire', 'interneInfos', 'non', 'web', true, $result[1]);
    }
    exit;
}
elseif($PC->rcvG['action'] == "archiver") {
    $view = new affaireView();
    echo $view->popupConfirmArchiv($PC->rcvG['aff']);
    exit;
}
elseif($PC->rcvP['action'] == "doArchiver") {
    foreach($PC->rcvP['ID'] as $k => $v)
	affaireModel::archivateAffaireInDB($k);
    echo viewFiche($PC->rcvP['aff'], 'affaire', 'interneInfos', 'non', 'web', true, 'Archivée');
    exit;
}
elseif($PC->rcvG['action'] == "doArchiver") {
    foreach($PC->rcvG['ID'] as $k => $v)
	affaireModel::archivateAffaireInDB($k);
    echo viewFiche($PC->rcvG['aff'], 'affaire', 'interneInfos', 'non', 'web', true, 'Archivée');
    exit;
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
