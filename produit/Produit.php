<?php
/*#########################################################################
#
#   name :       Produit.php
#   desc :       Display page content
#   categorie :  produit
#   ID :  	 $Id: Produit.php 2814 2009-06-29 14:54:25Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/ProduitView', 'ZunoRenduHTML'));
loadPlugin(array('ZControl/GeneralControl'));
loadPlugin(array('ZControl/DevisControl'));

// Whe get the page context
$PC = new PageContext('produit');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if($PC->rcvG['id_prod'] != '') {
    $sortie = viewFiche($PC->rcvG['id_prod'], 'produit', '', 'non', 'web');
}
elseif($PC->rcvP['action'] == 'modifProduit') {
    aiJeLeDroit('produit', 15, 'web');
    $control = new produitControl();
    $rs = $control->controlModif($PC->rcvP);
    if($rs[0]) {
        $sql = new ProduitModel();
        if(!array_key_exists('bestsell_prod', $PC->rcvP))
            $PC->rcvP['bestsell_prod'] = 0;
        if(!array_key_exists('stillAvailable_prod', $PC->rcvP))
            $PC->rcvP['stillAvailable_prod'] = 0;
        if($PC->rcvP['stock_prod'] == null)
            $PC->rcvP['stock_prod'] = 0;
        $rs = $sql->updateProduit($PC->rcvP, 'non', $PC->rcvP['idProduit']);
        if($rs[0]) {
            echo viewFiche($PC->rcvP['id_prod'], 'produit', 'interneInfos', 'non', 'web', true, 'Enregistré');
            exit;
        }
    }
    else {
        echo '<erreur>error</erreur><span class="important" style="text-align:center;">'.$rs[1].'</span>';
        exit;
    }

}
elseif($PC->rcvP['action'] == 'addProduit') {
    aiJeLeDroit('produit', 20, 'web');
    $control = new produitControl();
    $rs = $control->controlajout($PC->rcvP);
    if($rs[0]) {
        $sql = new ProduitModel();
        if(!array_key_exists('bestsell_prod', $PC->rcvP))
            $PC->rcvP['bestsell_prod'] = 0;
        if(!array_key_exists('stillAvailable_prod', $PC->rcvP))
            $PC->rcvP['stillAvailable_prod'] = 0;
        if($PC->rcvP['stock_prod'] == null)
            $PC->rcvP['stock_prod'] = 0;
        $rs = $sql->insertProduit($PC->rcvP);
        if($rs[0]) {
            echo viewFiche($PC->rcvP['id_prod'], 'produit', 'afterCreate', 'non', 'web', true, 'Enregistré');
            exit;
        }
        else {
            echo '<erreur>error</erreur>'.$rs[1];
            exit;
        }
    }
    else {
        echo '<erreur>error</erreur><span class="important" style="text-align:center;">'.$rs[1].'</span>';
        exit;
    }

}
elseif($PC->rcvP['action'] == 'modifPF') {
    aiJeLeDroit('produit', 15, 'web');
    $sql = new produitModel();
    foreach($PC->rcvP['idFourn'] as $k=>$v) {
        if($PC->rcvP['ractif'][$k] != 1) {
            $data['fournisseur_id'] = $v;
            $data['produit_id'] = $PC->rcvP['idProduit'];
            $data['prixF'] = $PC->rcvP['prixF'][$k];
            $data['remiseF'] = $PC->rcvP['remiseF'][$k];
            if($PC->rcvP['actif'][$k] == 1)
                $data['actif'] = 0;
            else
                $data['actif'] = 1;
        }
        else {
            unset($data);
            $data['fournisseur_id'] = $v;
            $data['produit_id'] = $PC->rcvP['idProduit'];
            $data['actif'] = 1;
        }
        if($PC->rcvP['supp'][$k] != 1)
            $rs = $sql->updateProduitFournisseur($data);
        else
            $rs = $sql->supprimerProduitFournisseur($v, $PC->rcvP['idProduit']);
    }
    echo viewFiche($PC->rcvP['idProduit'], 'produit', 'interneFourn', 'non', 'web', true, 'Enregistré');
    exit;

}
elseif($PC->rcvG['action'] == 'addFournProd') {
    aiJeLeDroit('produit', 15, 'web');
    $sql = new produitModel();
    $datas['data']['id_prod'] = $PC->rcvG['idProd'];
    $temp = $sql->getPotentielFournisseur($PC->rcvG['idProd']);
    $datas['fourn'] = $temp['select'];
    $datas['remise'] = $temp['remise'];
    $view = new ProduitView();
    echo $view->popupProdFourn($datas);
    exit;
}
elseif($PC->rcvP['action'] == 'addProduitFourn') {
    aiJeLeDroit('produit', 15, 'web');
    if($PC->rcvP['fournisseur_id'] == '') {
        exit;
    }
    if($PC->rcvP['prixF'] == ''){
        echo '<erreur>error</erreur><span class="important" >Il vous faut remplir un prix public à ce fournisseur</span>';
        exit;
    }
    $sql = new produitModel();
    $PC->rcvP['produit_id'] = $PC->rcvP['idProduit'];
    $PC->rcvP['actif'] = '1';
    $rs = $sql->insertProduitFournisseur($PC->rcvP);
    echo viewFiche($PC->rcvP['idProduit'], 'produit', 'interneFourn', 'non', 'web', true, 'Enregistré');
    exit;
}
elseif($PC->rcvG['action'] == 'popupAddProd') {
    aiJeLeDroit('produit', 20, 'web');
    $sql = new ProduitModel();
    $datas['dureeR'] = $sql->getRenews();
    $datas['famille'] = $sql->getAllFamille();
    $view = new ProduitView();
    echo $view->creer($datas, true);
    exit;
}
elseif($PC->rcvG['action'] == 'popupSearchProd') {
    aiJeLeDroit('produit', 5, 'web');
    $sql = new ProduitModel();
    $datas['dureeR'] = $sql->getRenews();
    $datas['famille'] = $sql->getAllFamille();
    $datas['data'] = array();
    $view = new ProduitView();
    //return $view->searchResult($datas);
    echo $view->searchPopup($datas);
    exit;
}
elseif($PC->rcvP['action'] == 'addProdPopup') {
    aiJeLeDroit('produit', 20, 'web');
    $control = new produitControl();
    $rs = $control->controlajout($PC->rcvP);
    if($rs[0]) {
        $sql = new ProduitModel();
        if(!array_key_exists('bestsell_prod', $PC->rcvP))
            $PC->rcvP['bestsell_prod'] = 0;
        if(!array_key_exists('stillAvailable_prod', $PC->rcvP))
            $PC->rcvP['stillAvailable_prod'] = 0;
        if($PC->rcvP['stock_prod'] == null)
            $PC->rcvP['stock_prod'] = 0;
        $rs = $sql->insertProduit($PC->rcvP);
        if($rs[0]) {
            echo '{ "datas" : { "id_prod" : "'.$PC->rcvP['id_prod'].'", "prix_prod" : "'.$PC->rcvP['prix_prod'].'", "description_prod" : "'.$PC->rcvP['description_prod'].'" } }';
            exit;
        }
        else {
            echo '<erreur>error</erreur>'.$rs[1];
            exit;
        }
    }
    else {
        echo '<erreur>error</erreur><span class="important" style="text-align:center;">'.$rs[1].'</span>';
        exit;
    }
}
elseif($PC->rcvG['action'] == 'addFamille') {
    $view = new ProduitView();
    echo $view->popupAddFamille();
    exit;
}
elseif($PC->rcvP['action'] == 'addFamille'){
    if($PC->rcvP['nom_prodfam'] == ""){
        $view = new ProduitView();
        echo $view->popupAddFamille("Vous devez entrer un nom pour votre famille");
        exit;
    }
    else{
        $model = new produitModel();
        $model->insertFamille($PC->rcvP['nom_prodfam']);
        echo '<img alt="loader" src="../img/ajax-loader.gif" onload="$(\'selectFamilleProd\').options['.$model->getLastFamille().'] = new Option(\''.$PC->rcvP['nom_prodfam'].'\', \''.$model->getLastFamille().'\');$(\'selectFamilleProd\').selectedIndex = '.$model->getLastFamille().'; zuno.popup.close();" />';
        exit;
    }
}
else {
    aiJeLeDroit('produit', 20, 'web');
    $sql = new ProduitModel();
    $datas['dureeR'] = $sql->getRenews();
    $datas['famille'] = $sql->getAllFamille();
    $view = new ProduitView();
    $sortie = $view->creer($datas);
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
