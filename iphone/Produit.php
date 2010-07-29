<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerContact.inc.php');
include_once ('V/GeneralView.inc.php');
loadPlugin(array('ZModels/ProduitModel','ZModels/ContactModel'));
include_once ('V/ContactView.inc.php');
include_once ('V/ProduitView.inc.php');
loadPlugin(array('ZControl/DevisControl'));
loadPlugin(array('ZControl/ContactControl'));
loadPlugin(array('ZControl/GeneralControl'));

// On lance la bufferisation de sortie et les entetes qui vont bien
ob_start();
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>';

// On recupère les informations sur le contexte de la page
// (channel, variables d'entrée, session)
$PC = new PageContext('iPhone');
$PC->GetVarContext();
$PC->GetChannelContext();
// Contrôle de la session et de sa validité
if($PC->GetSessionContext('',false) === false) {
    echo HtmlElementIphone::redirectOnSessionEnd();
    ob_end_flush();
    exit;
}
if($PC->rcvG['action'] == 'viewProd') {
    viewFiche($PC->rcvG['id_prod'], 'produit');
}
elseif($PC->rcvG['action'] == 'searchProduit') {
    $_SESSION['rechercheproduit'] = 'produit';
    viewResults($PC->rcvP['query'], 'produit', 'reset', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'searchProdContinue') {
    viewResults('', 'produit', 'suite', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'addProduit') {
    viewFormulaire('', 'produit', 'add', 'iphone', false, '');
}
elseif($PC->rcvG['action'] == 'doAddProd') {
    $control = produitControl::controlajout($PC->rcvP);
    if(!$control) {
	?>
<root><go to="waProduitAdd"/>
    <part><destination mode="replace" zone="waProduitAdd" create="true"/>
	<data><![CDATA[ <?php echo produitView::formAdd($PC->rcvP, $control['2'], $control['1']) ?> ]]></data>
    </part>
</root>
	<?php
	exit;
    }
    $req = new produitModel();
    if($PC->rcvP['famille_prod'] == '' or $PC->rcvP['famille_prod'] == null) {
	$famille = $req->insertFamille($PC->rcvP['nom_prodfam']);
	$PC->rcvP['famille_prod'] = $famille;
    }
    $resultat = $req->insertProduit($PC->rcvP, 'non');
    if($resultat[0]) {
	viewFiche($PC->rcvP['id_prod'], 'produit');
    }
}
elseif($PC->rcvG['action'] == 'familleProduitAdd') {
    $model = new produitModel();
    $val = $model->getDataForFamille($PC->rcvP['famille']);
    $outJS = '<script> ';
    $outJS = 'famille = new Array();'."\n";
    $number = 1;
    foreach($val['1'] as $v) {
	$outJS .= 'famille["'.$number.'"] = new Array();'."\n";
	$outJS .= ($v['nom_prodfam'] != NULL) ? 'famille["'.$number.'"]["nom_prodfam"] = "'.$v['treePathKey'].' '.$v['nom_prodfam'].'"'.";\n" : '';
	$outJS .= ($v['id_prodfam'] != NULL) ? 'famille["'.$number.'"]["id_prodfam"] = "'.$v['id_prodfam'].'"'.";\n" : '';
	$number++;
    }
    $outJS .= 'doModifFamille();';
    $outJS .= ' </script>';

    ?>
<root>

    <part><destination mode="append" zone="waProduitAdd" create="false"/>
	<data><![CDATA[ <?php echo '<script id="scriptFamille">'.$outJS.'</script>';?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'modifProd') {
    viewFormulaire($PC->rcvG['id_prod'], 'produit', 'modif', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doModifProd') {
    $fourn = ($PC->rcvP['nombrefourn'] == 1) ? 'non' :'oui';
    $req = new produitModel();
    if($PC->rcvP['famille_prod'] == '' or $PC->rcvP['famille_prod'] == null) {
	$famille = $req->insertFamille($PC->rcvP['nom_prodfam']);
	$PC->rcvP['famille_prod'] = $famille;
    }
    for($nombre = 1; $nombre < $PC->rcvP['nombrefourn']; $nombre++) {
	$PC->rcvP['fourn'][$nombre]['prixF'] = $PC->rcvP['prixF'.$nombre];
	$PC->rcvP['fourn'][$nombre]['remiseF'] = $PC->rcvP['remiseF'.$nombre];
	$PC->rcvP['fourn'][$nombre]['fournisseur_id'] = $PC->rcvP['fournisseur_id'.$nombre];
	$PC->rcvP['fourn'][$nombre]['actif'] = '1';
	$PC->rcvP['fourn'][$nombre]['produit_id'] = $PC->rcvP['id_prod'];
    }
    $result = $req->updateProduit($PC->rcvP, $fourn, $PC->rcvP['id_prod']);
    if($result['0']) {
	viewFiche($PC->rcvP['id_prod'], 'produit');
    }
}
elseif($PC->rcvG['action'] == 'suppProduit') {
    $req = new produitModel();
    $data['stillAvailable_prod'] = '0';
    $result = $req->updateProduit($data, 'non', $PC->rcvG['id_prod']);
    ?>
<root><go to="waFicheProduit"/>
    <part><destination mode="replace" zone="waFicheProduit" />
	<data><![CDATA[ <?php  echo '<div class="err">Produit désactivé</div>'?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'activProduit') {
    $req = new produitModel();
    $data['stillAvailable_prod'] = '1';
    $result = $req->updateProduit($data, 'non', $PC->rcvG['id_prod']);
    if($result[0]) {
	viewFiche($PC->rcvG['id_prod'], 'produit');
    }
}
elseif($PC->rcvG['action'] == 'dellFournProd') {
    $req = new produitModel();
    $fournisseur = $req->getFournisseurByProduitID($PC->rcvG['id_prod']);
    ?>
<root><go to="waDellFourn"/>
    <title set="waDellFourn"><?php echo $PC->rcvG['id_prod']; ?></title>
    <part><destination mode="replace" zone="waDellFourn" create="true"/>
	<data><![CDATA[ <?php echo produitView::deleteFournProd($fournisseur[1], $PC->rcvG['id_prod']); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'doDellFournProd') {
    $req = new produitModel();
    $resultat = $req->enleverProduitFournisseur($PC->rcvP['fourntosupp'], $PC->rcvG['id_prod']);
    if($resultat[0]) {
	$result = $req->getProduitByID($PC->rcvG['id_prod']);
	$fourn = $req->getFournisseurByProduitID($PC->rcvG['id_prod']);
	if($result[0]) { ?>
<root><go to="waProduitFiche"/>
    <title set="waProduitFiche"><?php echo $result[1]['id_prod']; ?></title>
    <part><destination mode="replace" zone="waProduitFiche" create="true"/>
	<data><![CDATA[ <?php echo '<div class="err">Fournisseur '.$PC->rcvP['fourntosupp'].' enlevé</div>'.produitView::view($result[1][0], $fourn[1]); ?> ]]></data>
    </part>
</root>
	    <?php }
    }
}
elseif($PC->rcvG['action'] == 'addFournProd') {
    $req = new produitModel();
    if($PC->rcvG['fourn'] == 'non') {
	$fourn = $req->getFournisseurByProduitID($PC->rcvG['id_prod']);
	foreach($fourn[1] as $v) {
	    $liste[$v['fournisseur_id']] = '1';
	}
    }

    $fournisseur = $req->getAllFournisseur();

    if($PC->rcvG['fourn'] == 'non') {
	foreach($fournisseur[1] as $v) {
	    if(!array_key_exists($v['id_fourn'], $liste)) {
		$temp[]=$v;
	    }
	}
	$fournisseur[1]=$temp;
    }

    ?>
<root><go to="waAddFourn"/>
    <title set="waAddFourn"><?php echo $PC->rcvG['id_prod']; ?></title>
    <part><destination mode="replace" zone="waAddFourn" create="true"/>
	<data><![CDATA[ <?php echo produitView::addFournProd($fournisseur[1], $PC->rcvG['id_prod']); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'doAddFournProd') {
    if($PC->rcvP['fournisseur_id'] == '' or $PC->rcvP['fournisseur_id'] == null) {
	?>
<root><go to="waAddFourn"/>
    <title set="waAddFourn"><?php echo $PC->rcvG['id_prod']; ?></title>
    <part><destination mode="replace" zone="waAddFourn" create="true"/>
	<data><![CDATA[ <?php echo '<div class="err">Veuillez indiquer un fournisseur</div>'.produitView::addFournProd($fournisseur[1], $PC->rcvG['id_prod']); ?> ]]></data>
    </part>
</root>
	<?php
    }
    else {
	$PC->rcvP['produit_id'] = $PC->rcvG['id_prod'];
	$PC->rcvP['actif'] = '1';
	if($PC->rcvP['remiseF'] == null)
	    $PC->rcvP['remiseF'] = '0';
	$req = new produitModel();
	$delete = $req->supprimerProduitFournisseur($PC->rcvP['fournisseur_id'], $PC->rcvP['produit_id']);
	$resultat = $req->insertProduitFournisseur($PC->rcvP);
	viewFiche($PC->rcvG['id_prod'], 'produit');
    }
}
elseif($PC->rcvG['action'] == 'searchFournisseur') {
    $_SESSION['rechercheproduit'] = 'fournisseur';
    viewResults($PC->rcvP['query'], 'produit', 'reset', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'searchFournContinue') {
    viewResults('', 'produit', 'suite', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'viewFourn') {
    viewFiche($PC->rcvG['id_fourn'], 'produit', '', 'fourn');
}
elseif($PC->rcvG['action'] == 'suppFourn') {
    $data['actif'] = 0;
    $info = new produitModel();
    $fourn = $info->updateFournisseur($data, 'non', $PC->rcvG['id_fourn']);
    $prodfourn = $info->desactiverProduitFournisseur($PC->rcvG['id_fourn']);
    ?>
<root><go to="waDellFourn"/>
    <title set="waDellFourn"><?php echo 'Fournisseur'; ?></title>
    <part><destination mode="replace" zone="waDellFourn" create="true"/>
	<data><![CDATA[ <?php echo '<div class=err>Fournisseur désactivé</div>'; ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'activer') {
    $data['actif'] = 1;
    $info = new produitModel();
    $fourn = $info->updateFournisseur($data, 'non', $PC->rcvG['id_fourn']);
    ?>
<root><go to="waActifFourn"/>
    <title set="waActifFourn"><?php echo 'Fournisseur'; ?></title>
    <part><destination mode="replace" zone="waActifFourn" create="true"/>
	<data><![CDATA[ <?php echo '<div class=err>Fournisseur activé</div>'; ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'addFournisseur') {
    viewFormulaire('', 'produit', 'add', 'iphone', true, 'fourn');
}
elseif($PC->rcvG['action'] == 'listeContFourn') {
    $cont = new contactEntrepriseModel();
    $liste = $cont->getDataFromID($PC->rcvP['id_ent']);
    ?>
<root>
    <part><destination mode="replace" zone="ContactNewFourn" />
	<data><![CDATA[ <?php echo produitView::addNewFournCont($liste[1][0]['contact']); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'doAddNewFourn') {

    if(!array_key_exists('contactComm_fourn', $PC->rcvP)) {
	exit;
    }

    $info = new contactEntrepriseModel();
    $prod = new produitModel();
    $detail = $info->getDataFromID($PC->rcvP['entreprise_fourn']);
    $cp = substr($detail[1][0]['cp_ent'],0,2);
    $nom = substr($detail[1][0]['nom_ent'],0,2);
    $id = $cp.$nom;
    $deja = $prod->getFournisseurByID($id);
    $k = 1;
    while(array_key_exists('0',$deja[1])) {
	$nom = substr($detail[1][0]['nom_ent'],$k,2);
	$k++;
	$id = $cp.$nom;
	$deja = $prod->getFournisseurByID($id);
    }
    $PC->rcvP['id_fourn'] = $id;
    $PC->rcvP['actif'] = '1';
    $result = $prod->insertFournisseur($PC->rcvP);
    viewFiche($PC->rcvP['id_fourn'], 'produit', '', 'fourn');
}
elseif($PC->rcvG['action'] == 'vraiSupp') {
    $req = new produitModel();
    $supp = $req->supprimerFournisseur($PC->rcvG['id_fourn']);
    ?>
<root><go to="waMenuProduit"/>
    <part><destination mode="append" zone="waMenuProduit" />
	<data><![CDATA[  ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'modifFourn') {
    viewFormulaire($PC->rcvG['id_fourn'], 'produit', 'modif', 'iphone', true, 'fourn');
}
elseif($PC->rcvG['action'] == 'doModifFourn') {
    $prod = new produitModel();
    $result = $prod->updateFournisseur($PC->rcvP, 'non', $PC->rcvG['id_fourn']);
    if($result[0]) {
	viewFiche($PC->rcvG['id_fourn'], 'produit');
    }
}
elseif($PC->rcvG['action'] == 'stock') {
    $req = new ProduitModel();
    $info = $req->getProduitByID($PC->rcvG['id_prod']);
    ?>
<root><go to="waStockProd"/>
    <title set="waStockProd"><?php echo 'Stock'; ?></title>
    <part><destination mode="replace" zone="waStockProd" create="true"/>
	<data><![CDATA[ <?php echo produitView::stockModif($info[1][0]); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'doStock') {
    if(is_numeric($PC->rcvP['stock_prod'])) {
	$req = new ProduitModel();
	$info = $req->updateStock($PC->rcvG['id_prod'], $PC->rcvP['stock_prod']);
	viewFiche($PC->rcvG['id_prod'], 'produit');
    }
    else {
	$req = new ProduitModel();
	$info = $req->getProduitByID($PC->rcvG['id_prod']);
	?>
<root><go to="waStockProd"/>
    <title set="waStockProd"><?php echo 'Stock'; ?></title>
    <part><destination mode="replace" zone="waStockProd" create="true"/>
	<data><![CDATA[ <?php echo produitView::stockModif($info[1][0], array(), 'Veuillez entrer une valeur numérique positive'); ?> ]]></data>
    </part>
</root>
	<?php
    }
}
?>
