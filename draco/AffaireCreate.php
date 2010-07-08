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
loadPlugin(array('ZunoCore','ZView/AffaireView', 'ZView/ContactView'));

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
aiJeLeDroit('affaire', 20, 'web');
if($PC->rcvP['action'] == 'creer' and !array_key_exists('from', $PC->rcvP)) {
    if($PC->rcvP['entreprise_aff'] != null ) {
        $PC->rcvP['titre_aff'] = str_replace(" ","_", $PC->rcvP['titre_aff']);
        $model  = new affaireModel();
        $resultinsert = $model->insert($PC->rcvP);
        header('Location:Affaire.php?id_aff='.$resultinsert['id_aff']);

    }
}
elseif($PC->rcvP['action'] == 'creer' and array_key_exists('from', $PC->rcvP)){
    if($PC->rcvP['entreprise_aff'] != null and $PC->rcvP['contact_aff'] != null) {
        $PC->rcvP['titre_aff'] = str_replace(" ","_", $PC->rcvP['titre_aff']);
        $model  = new affaireModel();
        $resultinsert = $model->insert($PC->rcvP);
        $model = new contactEntrepriseModel();
        $datas['projet'] = $model->getProjets($PC->rcvP['id_ent']);
        $datas['affaire'] = $model->getAffaires($PC->rcvP['id_ent']);
        $view = new contactEntrepriseView();
        echo $view->view($datas, 'interneProjet', 'Affaire enregistrée');
        exit;
    }
    else {
        echo '<erreur>erreurPopupAppel</erreur><span class="important">Il faut un contact et une entreprise !</span>';
        exit;
    }
}

$view = new affaireView();
$sortie = $view->creer();
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/


if($PC->rcvG['popup'] == 'on') {
    echo $sortie;
    exit;
}
else {
    $out->AddBodyContent($sortie);
    $out->Process();
}
?>
