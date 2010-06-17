<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');
loadPlugin(array('ZDoc/FactureDoc','OOConverter','Send/SendFax','Send/SendLetter'));

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerCommande.inc.php');
include_once ('lib/ZunoLayerDevis.inc.php');
include_once ('lib/ZunoLayerFacture.inc.php');
loadPlugin(array('ZControl/GeneralControl', 'ZControl/CommandeControl', 'ZControl/FactureControl', 'ZControl/SendControl'));
loadPlugin(array('ZModels/AffaireModel','ZModels/DevisModel','ZModels/CommandeModel','ZModels/FactureModel'));
include_once ('V/FactureView.inc.php');
include_once ('V/SendView.inc.php');
include_once ('V/CommandeView.inc.php');
include_once ('V/AffaireView.inc.php');
include_once ('V/ContactView.inc.php');
include_once ('V/DevisView.inc.php');
include_once ('V/GeneralView.inc.php');
//On notera la présence de librairies en rapport avec d'autres parties de l'appli.
//C'est normal y en a besoin.

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
aiJeLeDroit('facture',5);
if(verifDroits('facture',10)) {
    $plus = '';
}
else {
    $plus = "WHERE commercial_fact = '".$_SESSION['user']['id']."' ";
}
/**
 * Recherche simple d'une facture
 */
if($PC->rcvG['action'] == 'searchFacture' ) {
    if($PC->rcvP['type'] == 'Tout') {
        $type = '';
    }
    else {
        $type = $PC->rcvP['type'];
    }
    $_SESSION['type_fact']=$type;
    viewResults($PC->rcvP['query'], 'facture', 'reset', 'iphone', true);
}
/**
 * Dans le cas d'une recherche appelant plus de résultats que la limite utilisateur
 * La suite des résultats est gérée ici.
 */
elseif($PC->rcvG['action'] == 'searchFactureContinue') {
    viewResults('', 'facture', 'suite', 'iphone', true);
}
/**
 * Visualisation d'une facture.
 */
elseif($PC->rcvG['action'] == 'view') {
    viewFiche($PC->rcvG['id_fact'], 'facture');
}
/**
 * Génération d'un avoir à partir d'une facture
 */
elseif($PC->rcvG['action'] == 'avoir') {
    $info = new factureModel();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    aiJeLeDroit('avoir', 20);
    ?>
<root><go to="waFactureCloner"/>
    <title set="waFactureCloner"><?php echo $result[1][0]['id_fact']; ?></title>
    <part><destination mode="replace" zone="waFactureCloner" create="true"/>
        <data><![CDATA[ <?php echo factureView::cloner($result[1][0], 'Avoir', 'oui'); ?> ]]></data>
    </part>
</root>
    <?php
}

elseif($PC->rcvG['action'] == 'doAvoir') {
    $info = new factureModel();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    $prod=$info->getProduitsFromID($PC->rcvG['id_fact']);
    $id = $info->GetLastId();
    $id ++;
    $result[1][0]['id_fact'] = $id;
    $result[1][0]['status_fact'] = '1';
    $result[1][0]['type_fact'] = 'Avoir';
    $result[1][0]['titre_fact'] = 'Avoir de la facture '.$PC->rcvG['id_fact'];
    $resultat = $info->insert($result[1][0], 'toAvoir', $prod[1]);
    if($resultat[0]) {
        viewFiche($id, 'facture');
    }
    else {
        ?>
<root><go to="waFactureCloner"/>
    <title set="waFactureCloner"><?php echo "Nouv. facture";?></title>
    <part><destination mode="replace" zone="waFactureCloner" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
        <?php
    }//Une erreur est survenue avec la BDD.
}
/**
 * Visualisation des produits liés à une facture.
 */
elseif($PC->rcvG['action'] == 'produits') {
    viewProduitsLies ($PC->rcvG['id_fact'], 'facture', 'iphone');
}
/**
 * Demande de modification d'un produit déjà entré dans la facture.
 */
elseif($PC->rcvG['action'] == 'modifProduit') {
    viewFormulaireRessourcesLies($PC->rcvG['id_fact'], 'facture', $PC->rcvG['id_prod'], '1', 'iphone', $PC->rcvG['type'], $PC->rcvG['tva']);
}
elseif($PC->rcvG['action'] == 'doModifProduit') {
    $info= new factureModel();
    $idp = array($PC->rcvP['id_produitF']);

    $data['id_produit']=FileCleanFileName($idp[0], 'SVN_PROP');
    $data['quantite']=($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '') ? 1 : $PC->rcvP['quantite'];
    $data['remise']=($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '') ? 0 : $PC->rcvP['remise'];
    $data['id_facture']=$PC->rcvG['id_fact'];
    $temp=$info->getInfoProduits($data['id_produit']);
    $data['desc']=($PC->rcvP['desc'] == NULL || $PC->rcvP['desc'] == '') ? '*'.$temp[1][0]['nom_prod'].'*' : $PC->rcvP['desc'];
    $data['prix']= ($PC->rcvP['prix'.$k] == NULL || $PC->rcvP['prix'] == '') ? $temp[1][0]['prix_prod'] : $PC->rcvP['prix'];
    if($PC->rcvG['type'] == 'Avoir')
        $data['prix'] = (-1)*abs($data['prix']);
    $id_produit = $PC->rcvP['old_id'];
    $result=$info->updateProduits($data);
    if($PC->rcvP['memorize'.'id_produit'] == 'ok') {
        $datab['id_prod']=$data['id_produit'];
        $datab['nom_prod'] = $PC->rcvP['desc'];
        $datab['prix_prod'] = $data['prix'];
        $datab['famille_prod'] = '0';
        $datab['remisefournisseur_prod'] = '0';
        $resultat = $info->addProduit($datab);
    }
    if($result[0]) {
        $result = $info->getProduitsFromID($PC->rcvG['id_fact']);
        $sommeHT = 0;
        foreach($result[1] as $v) {
            $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
        }//On génère le total à entrer dans la BDD devis.
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont, f.commande_fact from facture f left join entreprise e on e.id_ent=f.entreprise_fact left join contact c on c.id_cont=f.contact_fact where id_fact='".$PC->rcvG['id_fact']."';");
        $infoprod=$sqlConn->process2();
        $sommeHT = formatCurencyDatabase($sommeHT);
        $sqlConn->makeRequeteFree("update facture set sommeHT_fact='".$sommeHT."' WHERE id_fact = '".$PC->rcvG['id_fact']."'");
        $temp = $sqlConn->process2();
    }
    else {
        $result[0] = 0;
    }//S'il y a eu une erreur, on balance l'erreur au bon endroit.
    if($result[0]) { ?>
<root><go to="waFactureProduits"/>
    <title set="waFactureProduits"><?php echo 'Produits'; ?></title>
    <part><destination mode="replace" zone="waFactureProduits" create="true"/>
        <data><![CDATA[ <?php echo factureView::produits($result[1], $PC->rcvG['id_fact'], $PC->rcvG['tva'], '', $PC->rcvG['type']); ?> ]]></data>
    </part>
</root>
        <?php }//Tout s'est bien passé, j'affiche.
    else { ?>
<root><go to="waFactureProduits"/>
    <part><destination mode="replace" zone="waFactureProduits" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Cette facture n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
        <?php }//Rien ne va plus, je le dis à l'utilisateur.

}
/**
 * Dans le cas où l'on souhaite ajouter un produit à une facture
 */
elseif($PC->rcvG['action'] == 'addProduit') {
    placementAffichage('Produits', "waAddProduitsFacture", 'factureView::addProduits', array(array(), $PC->rcvG['id_fact'], $PC->rcvG['tva'], $PC->rcvG['type']), '', 'replace');
//J'affiche simplement le formulaire d'ajout.
}
elseif($PC->rcvG['action'] == 'doAddProduit') {
    $info= new factureModel();
    $idp = array($PC->rcvP['id_produitF']);

    $data['id_produit']=FileCleanFileName($idp[0], 'SVN_PROP');
    $data['quantite']=($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '') ? 1 : $PC->rcvP['quantite'];
    $data['remise']=($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '') ? 0 : $PC->rcvP['remise'];
    $data['id_facture']=$PC->rcvG['id_fact'];
    $temp=$info->getInfoProduits($data['id_produit']);
    $data['desc']=($PC->rcvP['desc'] == NULL || $PC->rcvP['desc'] == '') ? '*'.$temp[1][0]['nom_prod'].'*' : $PC->rcvP['desc'];
    $data['prix']= ($PC->rcvP['prix'.$k] == NULL || $PC->rcvP['prix'] == '') ? $temp[1][0]['prix_prod'] : $PC->rcvP['prix'];
    if($PC->rcvG['type'] == 'Avoir')
        $data['prix'] = (-1)*abs($data['prix']);
    $result=$info->insertProduits($data);
    if($PC->rcvP['memorize'.'id_produit'] == 'ok') {
        $datab['id_prod']=$data['id_produit'];
        $datab['nom_prod'] = $PC->rcvP['desc'];
        $datab['prix_prod'] = $data['prix'];
        $datab['famille_prod'] = '0';
        $datab['remisefournisseur_prod'] = '0';
        $resultat = $info->addProduit($datab);
    }
    if($result[0]) {
        $result = $info->getProduitsFromID($PC->rcvG['id_fact']);
        $sommeHT = 0;
        foreach($result[1] as $v) {
            $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
        }//On génère le total à entrer dans la BDD devis.
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont, f.commande_fact from facture f left join entreprise e on e.id_ent=f.entreprise_fact left join contact c on c.id_cont=f.contact_fact where id_fact='".$PC->rcvG['id_fact']."';");
        $infoprod=$sqlConn->process2();
        $sommeHT = formatCurencyDatabase($sommeHT);
        $sqlConn->makeRequeteFree("update facture set sommeHT_fact='".$sommeHT."' WHERE id_fact = '".$PC->rcvG['id_fact']."'");
        $temp = $sqlConn->process2();

    }
    else {
        $result[0] = 0;
    }//S'il y a eu une erreur, on balance l'erreur au bon endroit.
    if($result[0]) { ?>
<root><go to="waFactureProduits"/>
    <title set="waFactureProduits"><?php echo 'Produits'; ?></title>
    <part><destination mode="replace" zone="waFactureProduits" create="true"/>
        <data><![CDATA[ <?php echo factureView::produits($result[1], $PC->rcvG['id_fact'], $PC->rcvG['tva'],'', $PC->rcvG['type']); ?> ]]></data>
    </part>
</root>
        <?php }//Tout s'est bien passé, j'affiche.
    else { ?>
<root><go to="waFactureProduits"/>
    <part><destination mode="replace" zone="waFactureProduits" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Cette facture n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
        <?php }//Rien ne va plus, je le dis à l'utilisateur.

}
elseif($PC->rcvG['action'] == 'suppProduit') {
    $data['id_facture'] = $PC->rcvG['id_fact'];
    $data['id_produit'] = $PC->rcvP['id_produit'];
    $info= new factureModel();
    $result = $info->deleteProduits($data);//J'effectue la suppression du produit de la BDD.
    if($result[0]) {
        $result = $info->getProduitsFromID($PC->rcvG['id_fact']);
        $sommeHT = 0;
        foreach($result[1] as $v) {
            $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
        }
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont, f.commande_fact from facture f left join entreprise e on e.id_ent=f.entreprise_fact left join contact c on c.id_cont=f.contact_fact where id_fact='".$PC->rcvG['id_fact']."';");
        $infoprod=$sqlConn->process2();
        $sommeHT = formatCurencyDatabase($sommeHT);
        $sqlConn->makeRequeteFree("update facture set sommeHT_fact='".$sommeHT."' WHERE id_fact = '".$PC->rcvG['id_fact']."'");
        $temp = $sqlConn->process2();

    }
    else {
        $result[0] = 0;
    }//S'il y a eu une erreur, on balance l'erreur au bon endroit.
    if($result[0]) { ?>
<root><go to="waFactureProduits"/>
    <title set="waFactureProduits"><?php echo 'Produits'; ?></title>
    <part><destination mode="replace" zone="waFactureProduits" create="true"/>
        <data><![CDATA[ <?php echo factureView::produits($result[1], $PC->rcvG['id_fact'], $PC->rcvG['tva'], '', $PC->rcvG['type']); ?> ]]></data>
    </part>
</root>
        <?php }//Tout s'est bien passé, j'affiche.
    else { ?>
<root><go to="waFactureProduits"/>
    <part><destination mode="replace" zone="waFactureProduits" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Cette facture n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
        <?php }//Rien ne va plus, je le dis à l'utilisateur.
}
elseif($PC->rcvG['action'] == 'modifFacture') {
    viewFormulaire($PC->rcvG['id_fact'], 'facture', 'modif', 'iphone', true, $PC->rcvG['type']);
}
/**
 * S'il faut vraiment faire la modification
 */
elseif($PC->rcvG['action'] == 'doModifFacture') {
    $info = new factureModel();
    $result = $info->update($PC->rcvP,$PC->rcvG['id_fact']);//Je fais l'insertion
    if($result[0]) {
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont, f.commande_fact from facture f left join entreprise e on e.id_ent=f.entreprise_fact left join contact c on c.id_cont=f.contact_fact where id_fact='".$PC->rcvG['id_fact']."';");
        $infoprod=$sqlConn->process2();

        viewFiche($PC->rcvG['id_fact'], 'facture', 'afterModif');
    }//J'affiche le résultat.

}
/**
 * Et si on veut supprimer une facture.
 */
elseif($PC->rcvG['action'] == 'suppFacture') {
    viewFormulaire($PC->rcvG['id_fact'], 'facture', 'supp', 'iphone', true, $PC->rcvG['type']);
}
/**
 * Si on a validé la demande de suppression d'une facture.
 */
elseif($PC->rcvG['action'] == 'doDeleteFacture') {
    $info = new factureModel();
    $commande = $info->getDataFromID($PC->rcvG['id_fact']);
    $result = $info->delete($PC->rcvG['id_fact']);
    $prod=$info->getProduitsFromID($PC->rcvG['id_fact']);
    foreach($prod[1] as $v) {
        $resulti .= $info->deleteProduits($v, NULL, 'all');
    }
    //Je récupère l'ID de la commande puis la supprime.
    if($result[0]) {

        ?>
<root><go to="waFactureDelete"/>
    <title set="waFactureDelete"><?php echo $result[1][0]['id_fact']; ?></title>
    <part><destination mode="replace" zone="waFactureDelete" create="true"/>
        <data><![CDATA[ <?php echo factureView::delete($result[1][0], $PC->rcvG['type']); ?> ]]></data>
    </part>
</root>
        <?php }//J'affiche le résultat.
    else { ?>
<root><go to="waFactureFiche"/>
    <part><destination mode="replace" zone="waFactureFiche" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Cette facture n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
        <?php }//Problème, je préviens l'utilisateur.
}
/**
 * Si on veut ajouter une nouvelle facture.
 */
elseif($PC->rcvG['action'] == 'addFacturePre') {
    aiJeLeDroit('facture', 20);
    $default['modereglement_fact']= '3';
    $default['condireglement_fact']= '4';
    ?>
<root><go to="waFactureAdd"/>
    <title set="waFactureAdd"><?php echo "Nouv. facture"; ?></title>
    <part><destination mode="replace" zone="waFactureAdd" create="true"/>
        <data><![CDATA[ <?php echo factureView::addPre($default, array(), '', $PC->rcvG['type']); ?> ]]></data>
    </part>
</root>
    <?php //J'affiche le formulaire d'ajout.
}

elseif($PC->rcvG['action'] == 'addFacture') {
    $_SESSION['temp']['modereglement_fact'] = ($PC->rcvP['modereglement_fact'] == NULL) ? '3' : $PC->rcvP['modereglement_fact'];
    $_SESSION['temp']['condireglement_fact'] = ($PC->rcvP['condireglement_fact'] == NULL) ? '4' : $PC->rcvP['condireglement_fact'];
    if(!array_key_exists('commande_fact', $PC->rcvP)) {
        $PC->rcvP['commande_fact'] = $PC->rcvG['commande_fact'];
    }
    viewFormulaire($PC->rcvP['commande_fact'], 'facture', 'add', 'iphone', true, $PC->rcvG['type']);
}


/**
 * Si on a confirmé la demande d'ajout d'une facture.
 */
elseif($PC->rcvG['action'] == 'doAddFacture') {
    $info = new factureModel();
    $cmd = $info->getEntrepriseData($PC->rcvP['commande_fact']);
    $cmd = $cmd[1][0];
    $devisM = new devisModel();
    $produit = $devisM->getProduitsFromID(substr($PC->rcvP['commande_fact'],0,9));
    $produit = $produit[1];

    $id = $info->GetLastId();
    $id ++;
    $data['id_fact'] = $id;
    $data['commande_fact'] = $PC->rcvP['commande_fact'];
    $data['titre_fact'] = $cmd['titre_cmd'];
    $data['commercial_fact'] = $_SESSION['user']['id'];
    $data['sommeHT_fact'] = $cmd['sommeHT_cmd'];
    $data['modereglement_fact'] = $_SESSION['temp']['modereglement_fact'];
    $data['condireglement_fact'] = $_SESSION['temp']['condireglement_fact'];
    $data['BDCclient_fact'] = $PC->rcvP['BDCclient'];
    $data['entreprise_fact'] = $cmd['entreprise_cmd'];
    $data['contact_fact'] = $cmd['contact_cmd'];
    $data['contact_achat_fact'] = $cmd['contact_achat_cmd'];
    $data['nomentreprise_fact'] = $cmd['nomdelivery_cmd'];
    $data['add1_fact'] = $cmd['adressedelivery_cmd'];
    $data['add2_fact'] = $cmd['adresse1delivery_cmd'];
    $data['ville_fact'] = $cmd['villedelivery_cmd'];
    $data['cp_fact'] = $cmd['cpdelivery_cmd'];
    $data['pays_fact'] = $cmd['paysdelivery_cmd'];
    $data['tauxTVA_fact'] = $cmd['tva_cmd'];
    $data['type_fact'] = $PC->rcvG['type'];
    if($PC->rcvG['type'] == 'Avoir')
         $result = $info->insert($data, 'toAvoir', $produit);
    else $result = $info->insert($data, 'cloner', $produit);
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("UPDATE entreprise SET type_ent = '3' WHERE id_ent = ".$data['entreprise_fact']." AND type_ent < '3' ; ");
    $bddtmp->process2();
    if($result[0]) {

        viewFiche($data['id_fact'], 'facture');
    }

}
elseif($PC->rcvG['action'] == 'addFactureFromDevis') {
    aiJeLeDroit('commande', 20);
    $info = new commandeModel();
    $aijecommande = $info->getDataFromID($PC->rcvG['devis_cmd']."BC");
    if($aijecommande[1][0]['id_cmd'] == null || $aijecommande[1][0]['id_cmd'] == '') {
        $dev = $info->getEntrepriseData($PC->rcvG['devis_cmd']);
        $dev = $dev[1][0];
        $devisM = new devisModel();
        $produit = $devisM->getProduitsFromID($PC->rcvP['devis_cmd']);
        $produit = $produit[1];
        $FHT = 0;
        $data['id_cmd'] = $PC->rcvG['devis_cmd'].'BC';
        $data['devis_cmd'] = $PC->rcvG['devis_cmd'];
        $data['titre_cmd'] = $dev['titre_dev'];
        $data['commercial_cmd'] = $_SESSION['user']['id'];
        $data['sommeHT_cmd'] = $dev['sommeHT_dev'];
        $data['sommeFHT_cmd'] = $FHT;
        $data['modereglement_cmd'] = '3';
        $data['condireglement_cmd'] = '4';
        $data['entreprise_cmd'] = $dev['entreprise_dev'];
        $data['contact_cmd'] = $dev['contact_dev'];
        $data['contact_achat_cmd'] = $dev['contact_achat_dev'];
        $data['nomdelivery_cmd'] = $dev['nomdelivery_dev'];
        $data['adressedelivery_cmd'] = $dev['adressedelivery_dev'];
        $data['adresse1delivery_cmd'] = $dev['adresse1delivery_dev'];
        $data['villedelivery_cmd'] = $dev['villedelivery_dev'];
        $data['cpdelivery_cmd'] = $dev['cpdelivery_dev'];
        $data['paysdelivery_cmd'] = $dev['paysdelivery_dev'];
        $data['maildelivery_cmd'] = $dev['maildelivery_dev'];
        $data['complementdelivery_cmd'] = $dev['complementdelivery_dev'];
        $data['tva_cmd'] = $dev['tva_dev'];
        $data['status_cmd'] = 9;
        $result = $info->insert($data, 'cloner', $produit);
        $aijecommande[1][0] = $data;
        if($result[0]) {

        }
    }
    $produit = $info->getProduitsFromID($aijecommande[1][0]['id_cmd']);
    $produit = $produit[1];
    $facture = new factureModel();
    $id = $facture->GetLastId();
    $id ++;
    $data['id_fact'] = $id;
    $data['commande_fact'] = $aijecommande[1][0]['id_cmd'];
    $data['titre_fact'] = $aijecommande[1][0]['titre_cmd'];
    $data['commercial_fact'] = $_SESSION['user']['id'];
    $data['sommeHT_fact'] = $aijecommande[1][0]['sommeHT_cmd'];
    $data['modereglement_fact'] = $aijecommande[1][0]['modereglement_cmd'];
    $data['condireglement_fact'] = $aijecommande[1][0]['condireglement_cmd'];
    $data['BDCclient_fact'] = $aijecommande[1][0]['BDCclient_cmd'];
    $data['entreprise_fact'] = $aijecommande[1][0]['entreprise_cmd'];
    $data['contact_fact'] = $aijecommande[1][0]['contact_cmd'];
    $data['contact_achat_fact'] = $aijecommande[1][0]['contact_achat_cmd'];
    $data['nomentreprise_fact'] = $aijecommande[1][0]['nomdelivery_cmd'];
    $data['add1_fact'] = $aijecommande[1][0]['adressedelivery_cmd'];
    $data['add2_fact'] = $aijecommande[1][0]['adresse1delivery_cmd'];
    $data['ville_fact'] = $aijecommande[1][0]['villedelivery_cmd'];
    $data['cp_fact'] = $aijecommande[1][0]['cpdelivery_cmd'];
    $data['pays_fact'] = $aijecommande[1][0]['paysdelivery_cmd'];
    $data['tauxTVA_fact'] = $aijecommande[1][0]['tva_cmd'];
    $data['maildelivery_fact'] = $aijecommande[1][0]['maildelivery_cmd'];
    $data['complementdelivery_fact'] = $aijecommande[1][0]['complementdelivery_cmd'];
    $data['type_fact'] = 'Facture';
    $result2 = $facture->insert($data, 'cloner', $produit);
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("UPDATE entreprise SET type_ent = '3' WHERE id_ent = ".$data['entreprise_fact']." AND type_ent < '3' ; ");
    $bddtmp->process2();
    if($result2[0]) {

        viewFiche($data['id_fact'], 'facture', 'afterModif');
    }


}
elseif($PC->rcvG['action'] == 'cloner') {
    viewFormulaire($PC->rcvG['id_fact'], 'facture', 'cloner', 'iphone', true, '');
}

elseif($PC->rcvG['action'] == 'doCloner') {
    $info = new factureModel();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    $prod=$info->getProduitsFromID($PC->rcvG['id_fact']);
    $id = $info->GetLastId();
    $id ++;
    $result[1][0]['id_fact'] = $id;
    $result[1][0]['status_fact'] = '1';
    $resultat = $info->insert($result[1][0], 'cloner', $prod[1]);
    if($resultat[0]) {

        viewFiche($id, 'facture');
    }
    else {
        ?>
<root><go to="waFactureCloner"/>
    <title set="waFactureCloner"><?php echo "Nouv. facture";?></title>
    <part><destination mode="replace" zone="waFactureCloner" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
        <?php
    }//Une erreur est survenue avec la BDD.
}
elseif($PC->rcvG['action'] == 'addFactureExpress') {
    aiJeLeDroit('facture', 20);
    ?>
<root><go to="waFactureAddExpress"/>
    <title set="waFactureAddExpress"><?php echo "Facture Express"; ?></title>
    <part><destination mode="replace" zone="waFactureAddExpress" create="true"/>
        <data><![CDATA[ <?php echo factureView::addExpress(); ?> ]]></data>
    </part>
</root>
    <?php

}
elseif($PC->rcvG['action'] == 'entrepriseFactureExpress') {
    $model = new devisModel();
    $val = $model->getDataForExpress($PC->rcvP['entreprise']);
    $temp[1] = $val['cont'];
    $toto['cont'] = $temp;
    $val['cont'] = $toto['cont']['1'];
    $temp[1] = $val['ent'];
    $toto['ent'] = $temp;
    $val['ent'] = $toto['ent']['1'];

    $outJS = '<script> ';
    $outJS = 'entreprise = new Array();'."\n";
    $outJS .= 'contact = new Array();'."\n";
    $numberEnt = 1;
    foreach($val['ent'] as $v) {
        $outJS .= 'entreprise["'.$numberEnt.'"] = new Array();'."\n";
        $outJS .= ($v['nom_ent'] != NULL) ? 'entreprise["'.$numberEnt.'"]["nom_ent"] = "'.$v['nom_ent'].'"'.";\n" : '';
        $outJS .= ($v['id_ent'] != NULL) ? 'entreprise["'.$numberEnt.'"]["id_ent"] = "'.$v['id_ent'].'"'.";\n" : '';
        $outJS .= ($v['add1_ent'] != NULL) ? 'entreprise["'.$numberEnt.'"]["add1_ent"] = "'.$v['add1_ent'].'"'.";\n" : '';
        $outJS .= ($v['add2_ent'] != NULL) ? 'entreprise["'.$numberEnt.'"]["add2_ent"] = "'.$v['add2_ent'].'"'.";\n" : '';
        $outJS .= ($v['cp_ent'] != NULL) ? 'entreprise["'.$numberEnt.'"]["cp_ent"] = "'.$v['cp_ent'].'"'.";\n" : '';
        $outJS .= ($v['ville_ent'] != NULL) ? 'entreprise["'.$numberEnt.'"]["ville_ent"] = "'.$v['ville_ent'].'"'.";\n" : '';
        $outJS .= ($v['pays_ent'] != NULL) ? 'entreprise["'.$numberEnt.'"]["pays_ent"] = "'.$v['pays_ent'].'"'.";\n" : '';
        $numberEnt++;
    }
    $numberCont = 1;
    foreach($val['cont'] as $v) {
        $outJS .= 'contact["'.$numberCont.'"] = new Array();'."\n";
        $outJS .= ($v['nom_cont'] != NUL) ? 'contact["'.$numberCont.'"]["nom_cont"] = "'.$v['nom_cont'].'"'.";\n" : '';
        $outJS .= ($v['id_cont'] != NUL) ? 'contact["'.$numberCont.'"]["id_cont"] = "'.$v['id_cont'].'"'.";\n" : '';
        $outJS .= ($v['prenom_cont'] != NUL) ? 'contact["'.$numberCont.'"]["prenom_cont"] = "'.$v['prenom_cont'].'"'.";\n" : '';
        $outJS .= ($v['mail_cont'] != NUL) ? 'contact["'.$numberCont.'"]["mail_cont"] = "'.$v['mail_cont'].'"'.";\n" : '';
        $outJS .= ($v['entreprise_cont'] != NUL) ? 'contact["'.$numberCont.'"]["entreprise_cont"] = "'.$v['entreprise_cont'].'"'.";\n" : '';
        $numberCont++;
    }
    $outJS .= 'doModifEntrepriseDevisExpress("Facture");';
    $outJS .= ' </script>';

    ?>
<root>

    <part><destination mode="append" zone="waFactureAddExpress" create="false"/>
        <data><![CDATA[ <?php echo '<script id="scriptFactureExpress">'.$outJS.'</script>';?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'addFactureExpressSuite') {
    $control = new factureControl();
    $result = $control->controlExpress1($PC->rcvP);
    if(!$result[0]) {
        ?>
<root><go to="waFactureAddExpress"/>
    <title set="waFactureAddExpress"><?php echo "Facture Express"; ?></title>
    <part><destination mode="replace" zone="waFactureAddExpress" create="true"/>
        <data><![CDATA[ <?php echo factureView::addExpress($PC->rcvP, $result[2], $result[1]); ?> ]]></data>
    </part>
</root>
        <?php
    }
    else {
        if(($PC->rcvP['entreprise_fact'] == NULL || $PC->rcvP['entreprise_fact'] == '') && ($PC->rcvP['nomdelivery_fact'] != NULL || $PC->rcvP['nomdelivery_fact'] != '')) {
            $entreprise = new contactEntrepriseModel();
            $data['nom_ent'] = $PC->rcvP['nomdelivery_fact'];
            $data['type_ent'] = '1';
            $data['add1_ent'] = $PC->rcvP['adressedelivery_fact'];
            $data['add2_ent'] = $PC->rcvP['adresse1delivery_fact'];
            $data['cp_ent'] = $PC->rcvP['cpdelivery_fact'];
            $data['ville_ent'] = $PC->rcvP['villedelivery_fact'];
            $data['pays_ent'] = $PC->rcvP['paysdelivery_fact'];
            $data['tauxTVA_ent'] = $PC->rcvP['tva_fact'];
            $data['tel_ent'] = $PC->rcvP['tel_ent'];
            $result = $entreprise->insert($data);
            $_SESSION['factureExpress']['entreprise_fact'] = $entreprise->getLastId();
            $_SESSION['factureExpress']['contact_fact'] = '0';

            if(($PC->rcvP['listeContact'] == NULL || $PC->rcvP['listeContact'] == '') && ($PC->rcvP['contact_fact'] != NULL || $PC->rcvP['contact_fact'] != '')) {
                $contact = new contactParticulierModel();
                $data['entreprise_cont'] = $entreprise->getLastId();
                $data['nom_cont'] = $PC->rcvP['contact_fact'];
                $data['civ_cont'] = $PC->rcvP['civ_cont'];
                $data['prenom_cont'] = $PC->rcvP['prenom_cont'];
                $data['add1_cont'] = $PC->rcvP['adressedelivery_fact'];
                $data['add2_cont'] = $PC->rcvP['adresse1delivery_fact'];
                $data['cp_cont'] = $PC->rcvP['cpdelivery_fact'];
                $data['ville_cont'] = $PC->rcvP['villedelivery_fact'];
                $data['pays_cont'] = $PC->rcvP['paysdelivery_fact'];
                $data['mail_cont'] = $PC->rcvP['maildelivery_fact'];
                $data['tel_cont'] = $PC->rcvP['tel_cont'];
                $result = $contact->insert($data);
                $_SESSION['factureExpress']['contact_fact'] = $contact->getLastId();
                $_SESSION['factureExpress']['nomentreprise_fact'] = $PC->rcvP['nomdelivery_fact'];
            }
        }
        elseif(($PC->rcvP['listeContact'] == NULL || $PC->rcvP['listeContact'] == '') && ($PC->rcvP['contact_fact'] != NULL || $PC->rcvP['contact_fact'] != '')) {
            $contact = new contactParticulierModel();
            $data['nom_cont'] = $PC->rcvP['contact_fact'];
            $data['civ_cont'] = $PC->rcvP['civ_cont'];
            $data['prenom_cont'] = $PC->rcvP['prenom_cont'];
            $data['add1_cont'] = $PC->rcvP['adressedelivery_fact'];
            $data['add2_cont'] = $PC->rcvP['adresse1delivery_fact'];
            $data['cp_cont'] = $PC->rcvP['cpdelivery_fact'];
            $data['ville_cont'] = $PC->rcvP['villedelivery_fact'];
            $data['pays_cont'] = $PC->rcvP['paysdelivery_fact'];
            $data['tel_cont'] = $PC->rcvP['tel_cont'];
            $data['mail_cont'] = $PC->rcvP['maildelivery_fact'];
            $data['entreprise_cont'] = $PC->rcvP['entreprise_fact'];
            $result = $contact->insert($data);
            $_SESSION['factureExpress']['entreprise_fact'] = $PC->rcvP['entreprise_fact'];
            $_SESSION['factureExpress']['contact_fact'] = $contact->getLastId();
            $_SESSION['factureExpress']['nomentreprise_fact'] = substr($PC->rcvP['nomdelivery_fact'], 0, strlen($PC->rcvP['nomdelivery_fact'])-8);
        }
        else {
            $_SESSION['factureExpress']['entreprise_fact'] = $PC->rcvP['entreprise_fact'];
            $_SESSION['factureExpress']['contact_fact'] = ($PC->rcvP['listeContact'] != NULL) ? $PC->rcvP['listeContact'] : '0';
            $_SESSION['factureExpress']['nomentreprise_fact'] = substr($PC->rcvP['nomdelivery_fact'], 0, strlen($PC->rcvP['nomdelivery_fact'])-8);
        }
        $_SESSION['factureExpress']['maildelivery_fact'] = $PC->rcvP['maildelivery_fact'];
        $_SESSION['factureExpress']['add1_fact'] = $PC->rcvP['adressedelivery_fact'];
        $_SESSION['factureExpress']['add2_fact'] = $PC->rcvP['adresse1delivery_fact'];
        $_SESSION['factureExpress']['cp_fact'] = $PC->rcvP['cpdelivery_fact'];
        $_SESSION['factureExpress']['ville_fact'] = $PC->rcvP['villedelivery_fact'];
        $_SESSION['factureExpress']['pays_fact'] = $PC->rcvP['paysdelivery_fact'];
        $_SESSION['factureExpress']['nb_prod'] = 1;
        $_SESSION['devisExpress']['nb_prod'] = 1;
        $_SESSION['factureExpress']['tauxTVA_fact'] = $PC->rcvP['tva_fact'];
        $_SESSION['factureExpress']['titre_fact'] = 'Facture';
        ?>
<root><go to="waFactureAddExpressSuite"/>
    <title set="waFactureAddExpressSuite"><?php echo "Facture Express"; ?></title>
    <part><destination mode="replace" zone="waFactureAddExpressSuite" create="true"/>
        <data><![CDATA[ <?php echo factureView::addExpressSuite(); ?> ]]></data>
    </part>
</root>
        <?php
    }
}

elseif($PC->rcvG['action'] == 'doAddFactureExpress') {
    $info = new factureModel();
    $control = new factureControl();
    $result = $control->controlExpress2($PC->rcvP);
    if(!$result[0]) {
        ?>
<root><go to="waFactureAddExpressSuite"/>
    <title set="waFactureAddExpressSuite"><?php echo "Facture ExpressSuite"; ?></title>
    <part><destination mode="replace" zone="waFactureAddExpressSuite" create="true"/>
        <data><![CDATA[ <?php echo factureView::addExpressSuite($PC->rcvP, $result[2], $result[1]); ?> ]]></data>
    </part>
</root>
        <?php
    }
    else {
        $temp = 0;
        $_SESSION['factureExpress']['status_fact'] = '1';
        $_SESSION['factureExpress']['commercial_fact'] = $_SESSION['user']['id'];
        $numero = 0;
        for ($nombre = 1; $nombre <= $_SESSION['devisExpress']['nb_prod']; $nombre++) {
            if($PC->rcvP['id_produitFactureExpress'.$nombre] != NULL || $PC->rcvP['id_produitFactureExpress'.$nombre] != '') {
                $temp += $PC->rcvP['quantite'.$nombre]*$PC->rcvP['prix'.$nombre]*(1-$PC->rcvP['remise'.$nombre]/100);
                $prod[$numero]['id_produit']=$PC->rcvP['id_produitFactureExpress'.$nombre];
                $prod[$numero]['desc']=$PC->rcvP['desc'.$nombre];
                $prod[$numero]['quantite']=$PC->rcvP['quantite'.$nombre];
                $prod[$numero]['prix']=$PC->rcvP['prix'.$nombre];
                $prod[$numero]['remise']=$PC->rcvP['remise'.$nombre];
                if($PC->rcvP['memorize'.'id_produitFactureExpress'.$nombre] == 'ok') {
                    $datab['id_prod']=$prod[$numero]['id_produit'];
                    $datab['nom_prod'] = $prod[$numero]['desc'];
                    $datab['prix_prod'] = $prod[$numero]['prix'];
                    $datab['famille_prod'] = '0';
                    $datab['remisefournisseur_prod'] = '0';
                    $resultat = $info->addProduit($datab);
                }
                $numero++;
            }
        }
        $_SESSION['factureExpress']['sommeHT_fact'] = $temp;
        $result = $info->insert($_SESSION['factureExpress'], 'express', $prod);
        $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $bddtmp->makeRequeteFree("UPDATE entreprise SET type_ent = '3' WHERE id_ent = '".$_SESSION['factureExpress']['entreprise_fact']."' AND type_ent < '3' ; ");
        $bddtmp->process2();
        if($result[0]) {

            viewFiche($_SESSION['factureExpress']['id'], 'facture');
        }
        else {
            ?>
<root><go to="waFactureAdd"/>
    <title set="waFactureAdd"><?php echo "Nouv. facture";?></title>
    <part><destination mode="replace" zone="waFactureAdd" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
            <?php
        }//Problème que j'explique à l'utilisateur.
    }
}
elseif($PC->rcvG['action'] == 'voir') {
    $info = new factureModel();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
        aiJeLeDroit('facture', 63);
    }
    else {
        aiJeLeDroit('facture', 62);
    }
    ?>
<root><go to="waFactureAction"/>
    <title set="waFactureAction">Voir</title>
    <part><destination mode="replace" zone="waFactureAction" create="true"/>
        <data><![CDATA[ <?php echo factureView::actionVoir($result[1][0], $PC->rcvG['type']); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'send') {
    $info = new factureModel();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    ?>
<root><go to="waFactureAction"/>
    <title set="waFactureAction">Envoyer</title>
    <part><destination mode="replace" zone="waFactureAction" create="true"/>
        <data><![CDATA[ <?php echo factureView::actionSend($result[1][0], $PC->rcvG['type']); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'send1') {
    $info = new factureModel();
    $gnose = new factureGnose();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    $r = $result[1][0];
    $Doc = $gnose->FactureGenerateDocument($PC->rcvG['id_fact'],'pdf',$PC->rcvP['Cannevas']);
    $dir = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];

    $_SESSION['FactureActionRecSend']['id_fact'] = $PC->rcvG['id_fact'];
    $_SESSION['FactureActionRecSend']['Cannevas'] = $PC->rcvP['Cannevas'];
    $_SESSION['FactureActionRecSend']['OutputExt'] = $PC->rcvP['OutputExt'];
    $_SESSION['FactureActionRecSend']['message'] = $PC->rcvP['message'];
    $_SESSION['FactureActionRecSend']['type'] = $PC->rcvP['type'];
    $_SESSION['FactureActionRecSend']['file'] = $dir.$Doc;

    if($PC->rcvP['type'] == 'courrier') {
        if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
            aiJeLeDroit('facture', 55);
        }
        else {
            aiJeLeDroit('facture', 54);
        }
        $_SESSION['FactureActionRecSend']['nom'] = $r['nomdelivery_fact'];
        $_SESSION['FactureActionRecSend']['add1'] = $r['adressedelivery_fact'];
        $_SESSION['FactureActionRecSend']['add2'] = $r['adresse1delivery_fact'];
        $_SESSION['FactureActionRecSend']['cp'] = $r['cpdelivery_fact'];
        $_SESSION['FactureActionRecSend']['ville'] = $r['villedelivery_fact'];
        $_SESSION['FactureActionRecSend']['code_pays'] = $r['paysdelivery_fact'];
    }
    elseif($PC->rcvP['type'] == 'fax') {
        if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
            aiJeLeDroit('facture', 53);
        }
        else {
            aiJeLeDroit('facture', 52);
        }
        if($r['nomdelivery_fact'] != '')
            $_SESSION['FactureActionRecSend']['nom'] = $r['nomdelivery_fact'];
        else  $_SESSION['FactureActionRecSend']['nom'] = $r['civ_cont'].' '.$r['prenom_cont'].' '.$r['nom_cont'];
        if($r['fax_cont'] != '')
            $_SESSION['FactureActionRecSend']['fax'] = $r['fax_cont'];
        elseif($r['fax_ent'] != '')
            $_SESSION['FactureActionRecSend']['fax'] = $r['fax_ent'];
        else  $_SESSION['FactureActionRecSend']['fax'] = $r['fax_achat'];
    }
    else {
        if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
            aiJeLeDroit('facture', 51);
        }
        else {
            aiJeLeDroit('facture', 50);
        }
        if($r['maildelivery_fact'] != '')
            $_SESSION['FactureActionRecSend']['email'] = $r['maildelivery_fact'];
        elseif($r['mail_cont'] != '')
            $_SESSION['FactureActionRecSend']['email'] = $r['mail_cont'];
        else  $_SESSION['FactureActionRecSend']['email'] = $r['mail_achat'];
        $_SESSION['FactureActionRecSend']['titre'] = 'STARTX : Facture n°'.$r['id_fact'];
    }
    $in = array_merge($r,$_SESSION['FactureActionRecSend']);
    ?>
<root><go to="waFactureAction1"/>
    <title set="waFactureAction1">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waFactureAction1" create="true"/>
        <data><![CDATA[ <?php echo factureView::actionSend1($in, $PC->rcvP['type']); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'rec') {
    $info = new factureModel();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
        aiJeLeDroit('facture', 61);
    }
    else {
        aiJeLeDroit('facture', 60);
    }
    ?>
<root><go to="waFactureAction"/>
    <title set="waFactureAction">Enregistrer</title>
    <part><destination mode="replace" zone="waFactureAction" create="true"/>
        <data><![CDATA[ <?php echo factureView::actionRecord($result[1][0], $PC->rcvG['type']); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'recsend') {
    $info = new factureModel();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
        aiJeLeDroit('facture', 61);
    }
    else {
        aiJeLeDroit('facture', 60);
    }
    ?>
<root><go to="waFactureAction"/>
    <title set="waFactureAction">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waFactureAction" create="true"/>
        <data><![CDATA[ <?php echo factureView::actionRecordSend($result[1][0]); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'recsend1') {
    $info = new factureModel();
    $result = $info->getDataFromID($PC->rcvG['id_fact']);
    $r = $result[1][0];
    $gnose = new factureGnose();
    $Doc = $gnose->FactureGenerateDocument($PC->rcvG['id_fact'],'pdf',$PC->rcvP['Cannevas']);
    $dir = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];

    $_SESSION['FactureActionRecSend']['id_fact'] = $PC->rcvG['id_fact'];
    $_SESSION['FactureActionRecSend']['Cannevas'] = $PC->rcvP['Cannevas'];
    $_SESSION['FactureActionRecSend']['OutputExt'] = $PC->rcvP['OutputExt'];
    $_SESSION['FactureActionRecSend']['message'] = $PC->rcvP['message'];
    $_SESSION['FactureActionRecSend']['type'] = $PC->rcvP['type'];
    $_SESSION['FactureActionRecSend']['file'] = $dir.$Doc;

    if($PC->rcvP['type'] == 'courrier') {
        if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
            aiJeLeDroit('facture', 55);
        }
        else {
            aiJeLeDroit('facture', 54);
        }
        $_SESSION['FactureActionRecSend']['nom'] = $r['nomdelivery_fact'];
        $_SESSION['FactureActionRecSend']['add1'] = $r['adressedelivery_fact'];
        $_SESSION['FactureActionRecSend']['add2'] = $r['adresse1delivery_fact'];
        $_SESSION['FactureActionRecSend']['cp'] = $r['cpdelivery_fact'];
        $_SESSION['FactureActionRecSend']['ville'] = $r['villedelivery_fact'];
        $_SESSION['FactureActionRecSend']['code_pays'] = $r['paysdelivery_fact'];
    }
    elseif($PC->rcvP['type'] == 'fax') {
        if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
            aiJeLeDroit('facture', 53);
        }
        else {
            aiJeLeDroit('facture', 52);
        }
        if($r['nomdelivery_fact'] != '')
            $_SESSION['FactureActionRecSend']['nom'] = $r['nomdelivery_fact'];
        else  $_SESSION['FactureActionRecSend']['nom'] = $r['civ_cont'].' '.$r['prenom_cont'].' '.$r['nom_cont'];
        if($r['fax_cont'] != '')
            $_SESSION['FactureActionRecSend']['fax'] = $r['fax_cont'];
        elseif($r['fax_ent'] != '')
            $_SESSION['FactureActionRecSend']['fax'] = $r['fax_ent'];
        else  $_SESSION['FactureActionRecSend']['fax'] = $r['fax_achat'];
    }
    else {
        if($result[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
            aiJeLeDroit('facture', 51);
        }
        else {
            aiJeLeDroit('facture', 50);
        }
        if($r['maildelivery_fact'] != '')
            $_SESSION['FactureActionRecSend']['email'] = $r['maildelivery_fact'];
        elseif($r['mail_cont'] != '')
            $_SESSION['FactureActionRecSend']['email'] = $r['mail_cont'];
        else  $_SESSION['FactureActionRecSend']['email'] = $r['mail_achat'];
        $_SESSION['FactureActionRecSend']['titre'] = 'STARTX : Facture n°'.$r['id_fact'];
    }
    $in = array_merge($r,$_SESSION['FactureActionRecSend']);
    ?>
<root><go to="waFactureAction1"/>
    <title set="waFactureAction1">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waFactureAction1" create="true"/>
        <data><![CDATA[ <?php echo factureView::actionRecordSend1($in, $PC->rcvP['type']); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif (($PC->rcvG['action'] == 'doVoir')or
        ($PC->rcvG['action'] == 'doRec')or
        ($PC->rcvG['action'] == 'doSend')or
        ($PC->rcvG['action'] == 'doRecsend')) {
    if ($PC->rcvG['action'] == 'doRecsend') $PC->rcvP = array_merge($_SESSION['FactureActionRecSend'],$PC->rcvP);
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $info = new factureModel();
    $gnose = new factureGnose();
    $facture = $info->getDataFromID($PC->rcvG['id_fact']);
    $dev = $facture[1][0];
    $Doc = $gnose->FactureGenerateDocument($PC->rcvG['id_fact'],$PC->rcvP['OutputExt'],$PC->rcvP['Cannevas']);
    if ($PC->rcvG['action'] == 'doVoir') {
        $dir = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
        if($Doc != '' and file_exists($dir.$Doc)) {
            $fileSize = FileConvertSize2Human(filesize($dir.$Doc));
            $fileIcon = FileOutputType($Doc,'image','../');
            $fileAdd = '<fieldset>
						<ul><li><a target="_blank" href="File.php?type=view&amp;file='.$GLOBALS['REP']['tmp'].$Doc.'">'.$fileIcon.$Doc.' ('.$fileSize.')</a></li></ul>
					</fieldset>';
        }
        ?>
<root>
    <part><destination mode="append" zone="formFactureDoVoirResponse"/>
        <data><![CDATA[ <?php echo $fileAdd; ?> ]]></data>
    </part>
</root>
        <?php
    }
    if (($PC->rcvG['action'] == 'doRec')or($PC->rcvG['action'] == 'doRecsend')) {
        $ckoi = ($PC->rcvP['type'] == 'Avoir') ? 'l\'avoir' : 'la facture';
        $petite = ($PC->rcvP['type'] == 'Avoir') ? '' : 'e';
        $pronom = ($PC->rcvP['type'] == 'Avoir') ? 'Il' : 'Elle';
        if (trim($PC->rcvP['message']) == "")
            $PC->rcvP['message'] = "Changement de la facture ".$dev['id_fact'];
        $save = $gnose->FactureSaveDocInGnose($Doc,$dev['id_aff'],$PC->rcvP['message']);

        if ($dev['status_fact'] < 2) {
            $actuTitre = 'Enregistrement de '.$ckoi.' '.$dev['id_fact'];
            $actuDesc = ucfirst($ckoi).' '.$dev['id_fact'].' vient d\'être enregistré'.$petite.'. '.$pronom.' a une valeur de '.formatCurencyDisplay($dev['sommeHT_fact']).' HT. Commentaire de l\'enregistrement : '.$PC->rcvP['message'];
        }
        if($dev['status_aff'] < 3) {
            $inActualiteRec['status_aff'] = '3';
            $bddtmp->makeRequeteUpdate('affaire','id_aff',$dev['id_aff'],array('status_aff'=>$inActualiteRec['status_aff']));
            $bddtmp->process();
        }
        if($dev['status_fact'] < 3) {
            $inActualiteRec['status_fact'] = '3';
            $bddtmp->makeRequeteUpdate('facture','id_fact',$dev['id_fact'],array('status_fact'=>$inActualiteRec['status_fact']));
            $bddtmp->process();
        }


        if ($PC->rcvG['action'] == 'doRec') {	?>
<root><go to="waFactureAction"/>
    <part><destination mode="replace" zone="waFactureAction"/>
        <data><![CDATA[ <div class="msg">Votre document est maintenant enregistré dans le module ZunoGed.</div> ]]></data>
    </part>
</root>
            <?php
        }
    }
    if (($PC->rcvG['action'] == 'doRecsend')or($PC->rcvG['action'] == 'doSend')) {
        if ($PC->rcvG['action'] == 'doRecsend')
            $PC->rcvP = array_merge($_SESSION['FactureActionRecSend'],$PC->rcvP);

        if ($PC->rcvP['typeEnvoi'] == 'courrier')
            $control = sendControl::sendCourrier($PC->rcvP);
        elseif ($PC->rcvP['typeEnvoi'] == 'fax')
            $control = sendControl::sendFax($PC->rcvP);
        else  $control = sendControl::sendMail($PC->rcvP);

        if($control[0]) {
            $PC->rcvP['dir_aff'] = $dev['dir_aff'];
            $PC->rcvP['partie'] = "facture";
            $PC->rcvP['typeE'] = $PC->rcvP['type'];
            $PC->rcvP['cc'] = $PC->rcvP['emailcc'];
            $PC->rcvP['mail'] = $PC->rcvP['email'];
            $PC->rcvP['from'] = $_SESSION['user']['mail'];
            $PC->rcvP['expediteur'] = $_SESSION['user']['fullnom'];
            $PC->rcvP['destinataire'] = $PC->rcvP['nom'];
            $PC->rcvP['fichier'] = substr($PC->rcvP['file'], strripos($PC->rcvP['file'], "/")+1);
            $PC->rcvP['id'] = $_SESSION['FactureActionRecSend']['id_fact'];
            $PC->rcvP['channel'] = 'iphone';
            $PC->rcvP['sujet'] = $PC->rcvP['titre'];
            $send = new Sender($PC->rcvP);
            $result = $send->send($_SESSION['user']['mail']);
            $dest = ($PC->rcvP['destinataire'] == '') ? $PC->rcvP['mail'] : $PC->rcvP['destinataire'];

            if($result[0]) {
                if($dev['status_aff'] < 4) {
                    $inActualiteEnvoi['status_aff'] = '4';
                    $bddtmp->makeRequeteUpdate('affaire','id_aff',$dev['id_aff'],array('status_aff'=>$inActualiteEnvoi['status_aff']));
                    $bddtmp->process();
                }
                if($dev['status_fact'] < 4) {
                    $inActualiteEnvoi['status_fact'] = '4';
                    $bddtmp->makeRequeteUpdate('facture','id_fact',$dev['id_fact'],array('status_fact'=>$inActualiteEnvoi['status_fact']));
                    $bddtmp->process();
                }

                $produit = $info->getProduitsFromFacture($dev['id_fact']);
                if($produit[0] && $dev['status_fact'] < 4) {
                    foreach($produit[1] as $v) {
                        $totalProd = ($v['fournisseur_id'] == null or $v['fournisseur_id'] == '') ? '0' : $v['stock_prod']-$v['quantite'];
                        $bddtmp->makeRequeteUpdate('produit', 'id_prod', $v['id_prod'], array('stock_prod'=>$totalProd));
                        $bddtmp->process();
                    }
                }
                ?>
<root><go to="waFactureAction"/>
    <part><destination mode="replace" zone="waFactureAction"/>
        <data><![CDATA[ <div class="msg">Votre document vient d'être envoyé par <?php echo $PC->rcvP['type']; ?>. </div> ]]></data>
    </part>
</root>
                <?php
                unset($_SESSION['FactureActionRecSend']);
            }
            else { ?><root><go to="waFactureAction1"/>
    <title set="waFactureAction1">Erreur de <?php echo $PC->rcvP['type']; ?></title>
    <part><destination mode="replace" zone="waFactureAction1" create="true"/>
        <data><![CDATA[ <?php echo factureView::actionRecordSend1($PC->rcvP,array(),$result[1], $PC->rcvP['type']); ?> ]]></data>
    </part>
</root>
                <?php
            }
        }
        else {	?>
<root><go to="waFactureAction1"/>
    <title set="waFactureAction1">Envoi de <?php echo $PC->rcvP['type']; ?></title>
    <part><destination mode="replace" zone="waFactureAction1" create="true"/>
        <data><![CDATA[ <?php echo factureView::actionRecordSend1($PC->rcvP,$control[2],$control[1], $PC->rcvP['type']); ?> ]]></data>
    </part>
</root>
            <?php
        }
    }
}
elseif($PC->rcvG['action'] == 'nonregle') {
    $data['status_fact'] = '5';
    $requete = new factureModel();
    $result = $requete->update($data, $PC->rcvG['id_fact']);
    $donnee = $requete->getDataFromID($PC->rcvG['id_fact']);
    if($donnee[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
        aiJeLeDroit('facture', 14);
    }
    else {
        aiJeLeDroit('facture', 13);
    }
    if($result[0]) {

        ?>
<root><go to="waFactureFiche"/>
    <title set="waFactureFiche"><?php echo $donnee[1][0]['id_fact']; ?></title>
    <part><destination mode="replace" zone="waFactureFiche" create="true"/>
        <data><![CDATA[ <?php echo factureView::view($donnee[1][0], ''); ?> ]]></data>
    </part>
</root>
        <?php
    }//Tout va bien, on affiche
}
elseif($PC->rcvG['action'] == 'regle') {
    $data['status_fact'] = '6';
    $requete = new factureModel();
    $result = $requete->update($data, $PC->rcvG['id_fact']);
    $donnee = $requete->getDataFromID($PC->rcvG['id_fact']);
    if($donnee[1][0]['commercial_fact'] != $_SESSION['user']['id']) {
        aiJeLeDroit('facture', 14);
    }
    else {
        aiJeLeDroit('facture', 13);
    }
    if($result[0]) {
        
        ?>
<root><go to="waFactureFiche"/>
    <title set="waFactureFiche"><?php echo $donnee[1][0]['id_fact']; ?></title>
    <part><destination mode="replace" zone="waFactureFiche" create="true"/>
        <data><![CDATA[ <?php echo factureView::view($donnee[1][0], ''); ?> ]]></data>
    </part>
</root>
        <?php
    }//Tout va bien, on affiche
}
elseif($PC->rcvG['action'] == 'tri_montant') {
    viewTri('facture', 'montant', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triMontantMore') {
    viewTri('facture', 'montant', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'tri_creation') {
    viewTri('facture', 'creation', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triCreationMore') {
    viewTri('facture', 'creation', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'tri_entreprise') {
    viewTri('facture', 'entreprise', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triEntrepriseMore') {
    viewTri('facture', 'entreprise', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'tri_contact') {
    viewTri('facture', 'contact', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triContactMore') {
    viewTri('facture', 'contact', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'voirStats') {
    aiJeLeDroit('facture', 45);
    $facture = getStats('facture', 'oui', 'Facture');
    $avoir = getStats('facture', 'oui', 'Avoir');
    placementAffichage('Statistiques', "waStatsFacture", 'factureView::afficherStats', array($facture, $avoir), '', 'replace');
}
ob_end_flush();
?>

