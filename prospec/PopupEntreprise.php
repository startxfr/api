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
loadPlugin(array('ZunoCore','ZView/ProspecView'));
loadPlugin(array('ZView/ContactView','ZControl/ContactControl', "ZModels/ProduitModel"));
loadPlugin(array('ZControl/GeneralControl'));

// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetFullContext();
// Whe initialize page display
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
if($PC->rcvG['id_ent'] != '') {
    echo viewFiche($PC->rcvG['id_ent'], 'contactEntreprise', 'LightInfoSimpleBis', 'non', 'web', true);
}
elseif($PC->rcvG['id_fourn'] != '') {
    $model = new produitModel();
    $rs = $model->getFournisseurByID($PC->rcvG['id_fourn']);
    echo viewFiche($rs[1][0]['entreprise_fourn'], 'contactEntreprise', 'LightInfoSimpleBis', 'non', 'web', true);
}
elseif($PC->rcvG['action'] == 'searchContDev') {
    $v = new contactEntrepriseView();
    echo $v->popupSearch($PC->rcvG);
}
elseif($PC->rcvP['action'] == 'searchPopup') {
    $model = new contactParticulierModel();
    $datas['result'] = $model->getDataForSearch($PC->rcvP['recherche_cont']);
    $datas['idChamp'] = $PC->rcvP['idChamp'];
    $v = new contactEntrepriseView();
    echo $v->popupSearch($datas, false);
}
else  echo "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";
?>