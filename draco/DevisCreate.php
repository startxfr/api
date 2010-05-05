<?php
/*#########################################################################
#
#   name :       DevisCreate.php
#   desc :       Gère l'ajout d'un devis
#   categorie :  devis
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/DevisView', 'ZControl/DevisControl'));
loadPlugin(array('ZView/AffaireView'));
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
aiJeLeDroit('devis', 20, 'web');
if($PC->rcvP['action'] == 'addDevis') {
    $control = devisControl::controlAddWeb($PC->rcvP);
    if(!$control[0]) {
        $view = new devisView();
        $sortie = $view->creerDevis($control[1], $PC->rcvP);
    }
    else {
        $PC->rcvP['commercial_dev'] = $_SESSION['user']['id'];
        $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $req = "SELECT * from entreprise where id_ent = '".$PC->rcvP['entreprise_dev']."'";
        $bddtmp->makeRequeteFree($req);
        $result = $bddtmp->process2();
        $PC->rcvP['nomdelivery_dev'] = $result[1][0]['nom_ent'];
        $PC->rcvP['tva_dev'] = $result[1][0]['tauxTVA_ent'];
        $sql = new devisModel();
        if($PC->rcvP['affaire_dev'] != 'null') {
            $PC->rcvP['id_dev'] = $sql->createId($PC->rcvP['affaire_dev']);
            $result = $sql->insert($PC->rcvP);
            if($result[0]) {

                header('Location:Devis.php?id_dev='.$PC->rcvP['id_dev']);
                exit;
            }
            else {
                $view = new devisView();
                echo $view->creerDevis('light');exit;
            }
        }
        else {
            $result = $sql->insert($PC->rcvP, 'express', array());
            if($result[0]) {
                header('Location:Devis.php?id_dev='.$_SESSION['devisExpress']['id']);
                exit;
            }
        }
    }

}
else {
    $view = new devisView();
    $sortie = $view->creerDevis();
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/
$out->AddBodyContent($sortie);
$out->Process();
?>
