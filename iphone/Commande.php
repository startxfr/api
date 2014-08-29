<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');
$GLOBALS['LOG']['DisplayDebug'] = $GLOBALS['LOG']['DisplayError'] = false;
loadPlugin(array('ZDoc/CommandeDoc', 'OOConverter', 'Send/Send'));

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerCommande.inc.php');
include_once ('lib/ZunoLayerDevis.inc.php');
include_once ('lib/ZunoLayerGeneral.inc.php');
include_once ('V/GeneralView.inc.php');
loadPlugin(array('ZModels/CommandeModel', 'ZModels/AffaireModel', 'ZModels/DevisModel', 'ZModels/ContactModel'));
include_once ('V/CommandeView.inc.php');
include_once ('V/AffaireView.inc.php');
include_once ('V/ContactView.inc.php');
loadPlugin(array('ZControl/CommandeControl'));
loadPlugin(array('ZControl/GeneralControl'));
include_once ('V/CommandeView.inc.php');
include_once ('V/DevisView.inc.php');
loadPlugin(array('ZControl/SendControl'));
include_once ('V/SendView.inc.php');
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
$info = new commandeModel();
// Contrôle de la session et de sa validité
if ($PC->GetSessionContext('', false) === false) {
    echo HtmlElementIphone::redirectOnSessionEnd();
    ob_end_flush();
    exit;
}
aiJeLeDroit('commande', 5);
if (verifDroits('commande', 10)) {
    $plus = '';
} else {
    $plus = " AND commercial_cmd = '" . $_SESSION['user']['id'] . "' ";
}
if ($PC->rcvG['action'] == 'searchCommande') {
    viewResults($PC->rcvP['query'], 'commande', 'reset', 'iphone', true);
} elseif ($PC->rcvG['action'] == 'searchCommandeContinue') {
    viewResults('', 'commande', 'suite', 'iphone', true);
} elseif ($PC->rcvG['action'] == 'view') {
    viewFiche($PC->rcvG['id_cmd'], 'commande');
} elseif ($PC->rcvG['action'] == 'produits') {
    viewProduitsLies($PC->rcvG['id_cmd'], 'commande', 'iphone');
} elseif ($PC->rcvG['action'] == 'modifProduit') {
    viewFormulaireRessourcesLies($PC->rcvG['id_cmd'], 'commande', $PC->rcvG['id_prod'], 'one', 'iphone', '', $PC->rcvG['tva']);
} elseif ($PC->rcvG['action'] == 'doModifProduit') {
    $idp = array($PC->rcvP['id_produitC']);

    $data['id_produit'] = FileCleanFileName($idp[0], 'SVN_PROP');
    $data['quantite_cmd'] = ($PC->rcvP['quantite_cmd'] == NULL || $PC->rcvP['quantite_cmd'] == '') ? 1 : $PC->rcvP['quantite_cmd'];
    $data['quantite'] = ($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '') ? 1 : $PC->rcvP['quantite'];
    $data['remise'] = ($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '') ? 0 : $PC->rcvP['remise'];
    $data['id_commande'] = $PC->rcvG['id_cmd'];
    $data['remiseF'] = ($PC->rcvP['remiseF'] == NULL || $PC->rcvP['remiseF'] == '') ? 0 : $PC->rcvP['remiseF'];
    $temp = $info->getInfoProduits($data['id_produit']);
    $data['desc'] = ($PC->rcvP['desc'] == NULL || $PC->rcvP['desc'] == '') ? '*' . $temp[1][0]['nom_prod'] . '*' : $PC->rcvP['desc'];
    $data['prix'] = ($PC->rcvP['prix' . $k] == NULL || $PC->rcvP['prix'] == '') ? $temp[1][0]['prix_prod'] : $PC->rcvP['prix'];
    $data['prixF'] = $PC->rcvP['prixF'];
    $data['fournisseur'] = $PC->rcvP['fournisseur'];
    $id_produit = $PC->rcvP['old_id'];
    $result = $info->updateProduits($data);
    if ($PC->rcvP['memorize' . 'id_produit'] == 'ok') {
        $datab['id_prod'] = $data['id_produit'];
        $datab['nom_prod'] = $PC->rcvP['desc'];
        $datab['prix_prod'] = $data['prix'];
        $datab['famille_prod'] = '0';
        $datab['remisefournisseur_prod'] = '0';
        $resultat = $info->addProduit($datab);
    }
    if ($result[0]) {
        $result = $info->getProduitsFromID($PC->rcvG['id_cmd']);
        $sommeHT = 0;
        foreach ($result[1] as $v) {
            $sommeHT += $v['prix'] * (1 - $v['remise'] / 100) * $v['quantite'];
            $FHT += $v['prixF'] * $v['quantite'] * (1 - $v['remiseF'] / 100);
        }//On génère le total à entrer dans la BDD devis.
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont from commande left join entreprise e on e.id_ent=commande.entreprise_cmd left join contact c on c.id_cont=commande.contact_cmd where id_cmd='" . $PC->rcvG['id_cmd'] . "';");
        $infoprod = $sqlConn->process2();
        $sommeHT = formatCurencyDatabase($sommeHT);
        $sqlConn->makeRequeteFree("update commande set sommeHT_cmd='" . $sommeHT . "', sommeFHT_cmd='" . $FHT . "' WHERE id_cmd = '" . $PC->rcvG['id_cmd'] . "'");
        $temp = $sqlConn->process2();
    } else {
        $result[0] = 0;
    }//S'il y a eu une erreur, on balance l'erreur au bon endroit.
    if ($result[0]) {
        ?>
        <root><go to="waCommandeProduits"/>
        <title set="waCommandeProduits"><?php echo 'Produits'; ?></title>
        <part><destination mode="replace" zone="waCommandeProduits" create="true"/>
            <data><![CDATA[ <?php echo commandeView::produits($result[1], $PC->rcvG['id_cmd'], $PC->rcvG['tva']); ?> ]]></data>
        </part>
        </root>
    <?php
    }//Tout s'est bien passé, j'affiche.
    else {
        ?>
        <root><go to="waCommandeProduits"/>
        <part><destination mode="replace" zone="waCommandeProduits" create="true"/>
            <data><![CDATA[ <div class="iBlock"><div class="err">Cette commande n'<strong>existe plus</strong><br/></div></div> ]]></data>
        </part>
        </root>
        <?php
    }//Rien ne va plus, je le dis à l'utilisateur.
}
/**
 * Dans le cas où l'on souhaite ajouter un produit à une commande
 */ elseif ($PC->rcvG['action'] == 'addProduit') {
    placementAffichage('Produits', "waAddProduitsCommande", 'commandeView::addProduits', array(array(), $PC->rcvG['id_cmd'], $PC->rcvG['tva']), '', 'replace');
    //J'affiche simplement le formulaire d'ajout.
} elseif ($PC->rcvG['action'] == 'doAddProduit') {
    $idp = array($PC->rcvP['id_produitC']);
    $data['id_produit'] = FileCleanFileName($idp[0], 'SVN_PROP');
    $data['quantite'] = ($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '') ? 1 : $PC->rcvP['quantite'];
    $data['quantite_cmd'] = ($PC->rcvP['quantite_cmd'] == NULL || $PC->rcvP['quantite_cmd'] == '') ? 1 : $PC->rcvP['quantite_cmd'];
    $data['remise'] = ($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '') ? 0 : $PC->rcvP['remise'];
    $data['id_commande'] = $PC->rcvG['id_cmd'];
    $data['remiseF'] = ($PC->rcvP['remiseF'] == NULL || $PC->rcvP['remiseF'] == '') ? 0 : $PC->rcvP['remiseF'];
    $temp = $info->getInfoProduits($data['id_produit']);
    $data['desc'] = ($PC->rcvP['desc'] == NULL || $PC->rcvP['desc'] == '') ? '*' . $temp[1][0]['nom_prod'] . '*' : $PC->rcvP['desc'];
    $data['prix'] = ($PC->rcvP['prix' . $k] == NULL || $PC->rcvP['prix'] == '') ? $temp[1][0]['prix_prod'] : $PC->rcvP['prix'];
    $data['prixF'] = $PC->rcvP['prixF'];
    $data['fournisseur'] = $PC->rcvP['fournisseur'];
    $result = $info->insertProduits($data);
    if ($PC->rcvP['memorize' . 'id_produit'] == 'ok') {
        $datab['id_prod'] = $data['id_produit'];
        $datab['nom_prod'] = $PC->rcvP['desc'];
        $datab['prix_prod'] = $data['prix'];
        $datab['famille_prod'] = '0';
        $datab['remisefournisseur_prod'] = '0';
        $resultat = $info->addProduit($datab);
    }
    if ($result[0]) {
        $result = $info->getProduitsFromID($PC->rcvG['id_cmd']);
        $sommeHT = 0;
        foreach ($result[1] as $v) {
            $sommeHT += $v['prix'] * (1 - $v['remise'] / 100) * $v['quantite'];
            $FHT += $v['prixF'] * $v['quantite'] * (1 - $v['remiseF'] / 100);
        }//On génère le total à entrer dans la BDD devis.
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont from commande left join entreprise e on e.id_ent=commande.entreprise_cmd left join contact c on c.id_cont=commande.contact_cmd where id_cmd='" . $PC->rcvG['id_cmd'] . "';");
        $infoprod = $sqlConn->process2();
        $sommeHT = formatCurencyDatabase($sommeHT);
        $sqlConn->makeRequeteFree("update commande set sommeHT_cmd='" . $sommeHT . "', sommeFHT_cmd='" . $FHT . "' WHERE id_cmd = '" . $PC->rcvG['id_cmd'] . "'");
        $temp = $sqlConn->process2();
    } else {
        $result[0] = 0;
    }//S'il y a eu une erreur, on balance l'erreur au bon endroit.
    if ($result[0]) {
        ?>
        <root><go to="waCommandeProduits"/>
        <title set="waCommandeProduits"><?php echo 'Produits'; ?></title>
        <part><destination mode="replace" zone="waCommandeProduits" create="true"/>
            <data><![CDATA[ <?php echo commandeView::produits($result[1], $PC->rcvG['id_cmd'], $PC->rcvG['tva']); ?> ]]></data>
        </part>
        </root>
        <?php
    }//Tout s'est bien passé, j'affiche.
    else {
        ?>
        <root><go to="waCommandeProduits"/>
        <part><destination mode="replace" zone="waCommandeProduits" create="true"/>
            <data><![CDATA[ <div class="iBlock"><div class="err">Cette commande n'<strong>existe plus</strong><br/></div></div> ]]></data>
        </part>
        </root>
        <?php
    }//Rien ne va plus, je le dis à l'utilisateur.
} elseif ($PC->rcvG['action'] == 'suppProduit') {
    $data['id_commande'] = $PC->rcvG['id_cmd'];
    $data['id_produit'] = $PC->rcvP['id_produit'];
    $result = $info->deleteProduits($data); //J'effectue la suppression du produit de la BDD.
    if ($result[0]) {
        $result = $info->getProduitsFromID($PC->rcvG['id_cmd']);
        $sommeHT = 0;
        foreach ($result[1] as $v) {
            $sommeHT += $v['prix'] * (1 - $v['remise'] / 100) * $v['quantite'];
            $FHT += $v['prix_prod'] * $v['quantite'] * (1 - $v['remiseF'] / 100);
        }
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont from commande left join entreprise e on e.id_ent=commande.entreprise_cmd left join contact c on c.id_cont=commande.contact_cmd where id_cmd='" . $PC->rcvG['id_cmd'] . "';");
        $infoprod = $sqlConn->process2();
        $sommeHT = formatCurencyDatabase($sommeHT);
        $sqlConn->makeRequeteFree("update commande set sommeHT_cmd='" . $sommeHT . "', sommeFHT_cmd='" . $FHT . "' WHERE id_cmd = '" . $PC->rcvG['id_cmd'] . "'");
        $temp = $sqlConn->process2();
    } else {
        $result[0] = 0;
    }//S'il y a eu une erreur, on balance l'erreur au bon endroit.
    if ($result[0]) {
        ?>
        <root><go to="waCommandeProduits"/>
        <title set="waCommandeProduits"><?php echo 'Produits'; ?></title>
        <part><destination mode="replace" zone="waCommandeProduits" create="true"/>
            <data><![CDATA[ <?php echo commandeView::produits($result[1], $PC->rcvG['id_cmd'], $PC->rcvG['tva']); ?> ]]></data>
        </part>
        </root>
        <?php
    }//Tout s'est bien passé, j'affiche.
    else {
        ?>
        <root><go to="waCommandeProduits"/>
        <part><destination mode="replace" zone="waCommandeProduits" create="true"/>
            <data><![CDATA[ <div class="iBlock"><div class="err">Cette commande n'<strong>existe plus</strong><br/></div></div> ]]></data>
        </part>
        </root>
        <?php
    }//Rien ne va plus, je le dis à l'utilisateur.
} elseif ($PC->rcvG['action'] == 'modifCommande') {
    viewFormulaire($PC->rcvG['id_cmd'], 'commande', 'modif', 'iphone', true, '');
}
/**
 * S'il faut vraiment faire la modification
 */ elseif ($PC->rcvG['action'] == 'doModifCommande') {
    $result = $info->update($PC->rcvP, $PC->rcvG['id_cmd']); //Je fais l'insertion
    if ($result[0]) {
        $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
        $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont from commande left join entreprise e on e.id_ent=commande.entreprise_cmd left join contact c on c.id_cont=commande.contact_cmd where id_cmd='" . $PC->rcvG['id_cmd'] . "';");
        $infoprod = $sqlConn->process2();
        viewFiche($PC->rcvG['id_cmd'], 'commande', 'afterModif');
    }//J'affiche le résultat.
}
/**
 * Et si on veut supprimer un commande.
 */ elseif ($PC->rcvG['action'] == 'suppCommande') {
    viewFormulaire($PC->rcvG['id_cmd'], 'commande', 'supp', 'iphone', true, '');
}
/**
 * Si on a validé la demande de suppression d'un commande
 */ elseif ($PC->rcvG['action'] == 'doDeleteCommande') {
    $commande = $info->getDataFromID($PC->rcvG['id_cmd']);
    $facture = ($PC->rcvG['facture'] >= 1) ? 'yes' : 'no';
    $result = $info->delete($PC->rcvG['id_cmd'], $facture);
    $prod = $info->getProduitsFromID($PC->rcvG['id_cmd']);
    foreach ($prod[1] as $v) {
        $resulti .= $info->deleteProduits($v, NULL, 'all');
    }
    //Je récupère l'ID de la commande puis la supprime.
    if ($result[0]) {
        ?>
        <root><go to="waCommandeDelete"/>
        <title set="waCommandeDelete"><?php echo $result[1][0]['id_cmd']; ?></title>
        <part><destination mode="replace" zone="waCommandeDelete" create="true"/>
            <data><![CDATA[ <?php echo commandeView::delete($result[1][0]); ?> ]]></data>
        </part>
        </root>
        <?php
    }//J'affiche le résultat.
    else {
        ?>
        <root><go to="waCommandeFiche"/>
        <part><destination mode="replace" zone="waCommandeFiche" create="true"/>
            <data><![CDATA[ <div class="iBlock"><div class="err">Ce commande n'<strong>existe plus</strong><br/></div></div> ]]></data>
        </part>
        </root>
        <?php
    }//Problème, je préviens l'utilisateur.
}
/**
 * Si on veut ajouter un nouveau commande.
 */ elseif ($PC->rcvG['action'] == 'addCommandePre') {
    aiJeLeDroit('commande', 20);
    $default['modereglement_cmd'] = '3';
    $default['condireglement_cmd'] = '4';
    ?>
    <root><go to="waCommandeAdd"/>
    <title set="waCommandeAdd"><?php echo "Nouv. commande"; ?></title>
    <part><destination mode="replace" zone="waCommandeAdd" create="true"/>
        <data><![CDATA[ <?php echo commandeView::addPre($default); ?> ]]></data>
    </part>
    </root>
    <?php
    //J'affiche le formulaire d'ajout.
} elseif ($PC->rcvG['action'] == 'addCommande') {
    if (!array_key_exists('devis_cmd', $PC->rcvP)) {
        $PC->rcvP['devis_cmd'] = $PC->rcvG['devis_cmd'];
    }
    viewFormulaire($PC->rcvP['devis_cmd'], 'commande', 'add', 'iphone', true, '');
}

/**
 * Si on a confirmé la demande d'ajout d'un commande.
 */ elseif ($PC->rcvG['action'] == 'doAddCommande') {
    $dev = $info->getEntrepriseData($PC->rcvP['devis_cmd']);
    $dev = $dev[1][0];
    $devisM = new devisModel();
    $produit = $devisM->getProduitsFromID($PC->rcvP['devis_cmd']);
    $produit = $produit[1];
    $FHT = 0;
    foreach ($produit as &$v) {
        if (array_key_exists('fournisseur', $PC->rcvP)) {
            if (array_key_exists($v['id_produit'], $PC->rcvP['fournisseur'])) {
                $v['fournisseur'] = $PC->rcvP['fournisseur'][$v['id_produit']];
                $v['prixF'] = $PC->rcvP['prixF'][$v['id_produit']];
                if (is_numeric($PC->rcvP['quantite'][$v['id_produit']]))
                    $v['quantite_cmd'] = $PC->rcvP['quantite'][$v['id_produit']];
            }
            else {
                $v['fournisseur'] = NULL;
            }
        }
        if (array_key_exists('remiseF', $PC->rcvP))
            $v['remiseF'] = (array_key_exists($v['id_produit'], $PC->rcvP['remiseF'])) ? $PC->rcvP['remiseF'][$v['id_produit']] : 0;
        $FHT += $v['prixF'] * $v['quantite_cmd'] * (1 - $v['remiseF'] / 100);
    }
    $data['id_cmd'] = $PC->rcvP['devis_cmd'] . 'BC';
    $data['devis_cmd'] = $PC->rcvP['devis_cmd'];
    $data['titre_cmd'] = $dev['titre_dev'];
    $data['commercial_cmd'] = $_SESSION['user']['id'];
    $data['sommeHT_cmd'] = $dev['sommeHT_dev'];
    $data['sommeFHT_cmd'] = $FHT;
    $data['modereglement_cmd'] = $PC->rcvP['modereglement_cmd'];
    $data['condireglement_cmd'] = $PC->rcvP['condireglement_cmd'];
    $data['BDCclient_cmd'] = $PC->rcvP['BDCclient'];
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

    $result = $info->insert($data, 'cloner', $produit);
    if ($result[0]) {
        viewFiche($data['id_cmd'], 'commande', 'afterModif');
    }
} elseif ($PC->rcvG['action'] == 'cloner') {
    viewFormulaire($PC->rcvG['id_cmd'], 'commande', 'cloner', 'iphone', true, '');
} elseif ($PC->rcvG['action'] == 'doCloner') {
    $result = $info->getDataFromID($PC->rcvG['id_cmd']);
    $prod = $info->getProduitsFromID($PC->rcvG['id_cmd']);
    $id = (substr($PC->rcvG['id_cmd'], 0, 11) == $PC->rcvG['id_cmd']) ? $PC->rcvG['id_cmd'] . '1' : substr($PC->rcvG['id_cmd'], 0, 11) . (substr($PC->rcvG['id_cmd'], 11, 1) + 1);
    if (substr($id, 11, 1) == '0') {
        ?>
        <root><go to="waCommandeCloner"/>
        <title set="waCommandeCloner"><?php echo "Nouv. commande"; ?></title>
        <part><destination mode="replace" zone="waCommandeCloner" create="true"/>
            <data><![CDATA[ <?php echo "Impossible de cloner cette commande. Nombre maximal de clones atteint."; ?> ]]></data>
        </part>
        </root>
        <?php
    } else {
        $result[1][0]['id_cmd'] = $id;
        $result[1][0]['status_cmd'] = '1';
        $resultat = $info->insert($result[1][0], 'cloner', $prod[1]);
        if ($resultat[0]) {
            viewFiche($id, 'commande');
        } else {
            ?>
            <root><go to="waCommandeCloner"/>
            <title set="waCommandeCloner"><?php echo "Nouv. commande"; ?></title>
            <part><destination mode="replace" zone="waCommandeCloner" create="true"/>
                <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
            </part>
            </root>
            <?php
        }//Une erreur est survenue avec la BDD.
    }
} elseif ($PC->rcvG['action'] == 'inputCommande') {
    $_SESSION['searchCommandeLayerBackTo'] = $PC->rcvG['__source'];
    $_SESSION['searchCommandeTagsBackTo'] = $PC->rcvG['tag'];
    ?>
    <root><go to="waCommandeInputAjax"/>
    <title set="waCommandeInputAjax">Choix d'une commande</title>
    <part><destination mode="replace" zone="waCommandeInputAjax" create="true"/>
        <data><![CDATA[ <?php echo ZunoLayerCommande::headerFormSearchCmd(); ?> ]]></data>
    </part>
    <script>< ![CDATA[ new dynAjax('formSearchCommandeInput', 3, 'formSearchCommandeajax'); ]] ></script>
    </root>
    <?php
} elseif ($PC->rcvG['action'] == 'inputCommandeResult') {
    aiJeLeDroit('commande', 5);
    if (verifDroits('commande', 10)) {
        $plus = '';
    } else {
        $plus = "AND commercial_cmd = '" . $_SESSION['user']['id'] . "' ";
    }
    $_SESSION['searchCommandeQuery'] = $PC->rcvP['search'];
    $from = 0;
    $limit = $_SESSION['user']['config']['LenghtSearchCommande'];
    $commande = $info->getDataForSearchFacture($PC->rcvP['search'], $limit, $from, $plus);
    if ($commande[0]) {
        $out .= '<ul id="searchResultInputCommandeUl">';
        $out .= commandeView::searchInputResultRow($commande[1], $_SESSION['searchCommandeLayerBackTo'], $_SESSION['searchCommandeTagsBackTo']);
        if (count($commande[1]) >= $limit)
            $out .= '<li class="iMore" id="searchResultInputCommandeMore' . $from . '"><a href="Devis.php?action=inputCommandeContinue&from=' . $limit . '" rev="async">Plus de résultats</a></li>';
        $out .= '</ul>';
    } else
        $out .= '<h2 class="Contact">Commande (0)</h2>';
    ?>
    <root>
    <part>
        <destination mode="replace" zone="SearchCommandeResultAsync"/>
        <data><![CDATA[
            <div class="iList">
                <?php echo $out; ?>
            </div>
            ]]></data>
    </part>
    </root>
    <?php
}
elseif ($PC->rcvG['action'] == 'inputCommandeContinue') {
    if (verifDroits('commande', 10))
        $plus = '';
    else
        $plus = "AND commercial_cmd = '" . $_SESSION['user']['id'] . "' ";
    $zoneTo = $outJs = $out = '';
    $limit = $_SESSION['user']['config']['LenghtSearchCommande'];
    $from = ($PC->rcvG['from'] != '') ? $PC->rcvG['from'] : 0;
    $result = $info->getDataForSearchFacture($_SESSION['searchCommandeQuery'], $limit, $from);
    if ($result[0]) {
        $out .= commandeView::searchInputResultRow($result[1], $_SESSION['searchCommandeLayerBackTo'], $_SESSION['searchCommandeTagsBackTo']);
        if (count($result[1]) >= $limit)
            $out .= '<li class="iMore" id="searchResultInputCommandeMore' . $from . '"><a href="Commande.php?action=inputCommandeContinue&from=' . ($from + $limit) . '" rev="async">Plus de résultats</a></li>';
        $outJs = 'removeElementFromDom(\'searchResultInputCommandeMore' . ($from - $limit) . '\')';
        $zoneTo = 'searchResultInputCommandeUl';
    }

    if ($zoneTo != '') {
        ?>
        <root>
        <part>
            <destination mode="append" zone="<?php echo $zoneTo; ?>"/>
            <data><![CDATA[ <?php echo $out; ?> ]]></data>
            <script>< ![CDATA[ <?php echo $outJs; ?> ]] ></script>
        </part>
        </root>
        <?php
    }
} elseif ($PC->rcvG['action'] == 'voir') {
    $result = $info->getFournisseurFromID($PC->rcvG['id_cmd']);
    $resultat = $info->getDataFromID($PC->rcvG['id_cmd']);
    if ($resultat[1][0]['commercial_cmd'] != $_SESSION['user']['id']) {
        aiJeLeDroit('commande', 63);
    } else {
        aiJeLeDroit('commande', 62);
    }
    ?>
    <root><go to="waCommandeAction"/>
    <title set="waCommandeAction">Voir la commande</title>
    <part><destination mode="replace" zone="waCommandeAction" create="true"/>
        <data><![CDATA[ <?php echo commandeView::actionVoir($result[1], $PC->rcvG['id_cmd']); ?> ]]></data>
    </part>
    </root>
    <?php
} elseif ($PC->rcvG['action'] == 'send') {
    $result = $info->getDataFromID($PC->rcvG['id_cmd']);
    $resultF = $info->getFournisseurFromID($PC->rcvG['id_cmd']);
    ?>
    <root><go to="waCommandeAction"/>
    <title set="waCommandeAction">Envoyer la Commande</title>
    <part><destination mode="replace" zone="waCommandeAction" create="true"/>
        <data><![CDATA[ <?php echo commandeView::actionSend($result[1][0], $resultF[1]); ?> ]]></data>
    </part>
    </root>
    <?php
} elseif ($PC->rcvG['action'] == 'send1') {
    $result = $info->getDataFromID($PC->rcvG['id_cmd']);
    $datas['data'] = $r = $result[1][0];
    $datas["data"]['tauxTVA_ent'] = $datas["data"]['tva_cmd'];
    $datas['pays'] = $info->getPays();
    $datas['user'] = $info->getUser($PC->rcvG['id_cmd']);
    $datas['produit'] = $info->getAllFournisseursFromID($PC->rcvG['id_cmd']);
    switch ($PC->rcvP['document']) {
        case 'ri':
            $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'RI');
            break;
        case 'pvr':
            $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'PVR');
            break;
        case 'bdcc':
            $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'BC');
            break;
        case 'bdl':
            $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'BDL');
            break;
        default:
            $bdd = new commandeModel();
            $bdd->makeRequeteFree("SELECT * FROM fournisseur f LEFT JOIN entreprise e ON e.id_ent = f.entreprise_fourn LEFT JOIN contact c ON c.id_cont=f.contactComm_fourn where id_fourn = '" . $fourn . "'");
            $datas['fournisseur'] = $bdd->process2();
            $datas['fournisseur'] = $datas['fournisseur'][1][0];
            $bdd->makeRequeteFree("Select * from commande_produit cp left join produit p ON p.id_prod = cp.id_produit where cp.fournisseur = '" . $fourn . "' AND cp.id_commande = '" . $PC->rcvP['id_cmd'] . "'");
            $datas['produit'] = $bdd->process2();
            $datas['produit'] = $datas['produit'][1];
            $Doc = commandeGnose::CommandeGenerateBDCF($datas, $PC->rcvP['document']);
            break;
    }
    $dir = $GLOBALS['REP']['appli'] . $GLOBALS['REP']['tmp'];

    $_SESSION['CommandeActionRecSend']['id_cmd'] = $PC->rcvG['id_cmd'];
    $_SESSION['CommandeActionRecSend']['Cannevas'] = $PC->rcvP['Cannevas'];
    $_SESSION['CommandeActionRecSend']['OutputExt'] = $PC->rcvP['OutputExt'];
    $_SESSION['CommandeActionRecSend']['message'] = $PC->rcvP['message'];
    $_SESSION['CommandeActionRecSend']['type'] = $PC->rcvP['type'];
    $_SESSION['CommandeActionRecSend']['file'] = $dir . $Doc;

    if ($PC->rcvP['type'] == 'courrier') {
        if ($result[1][0]['commercial_cmd'] != $_SESSION['user']['id']) {
            aiJeLeDroit('commande', 55);
        } else {
            aiJeLeDroit('commande', 54);
        }
        $_SESSION['CommandeActionRecSend']['nom'] = $r['nomdelivery_cmd'];
        $_SESSION['CommandeActionRecSend']['add1'] = $r['adressedelivery_cmd'];
        $_SESSION['CommandeActionRecSend']['add2'] = $r['adresse1delivery_cmd'];
        $_SESSION['CommandeActionRecSend']['cp'] = $r['cpdelivery_cmd'];
        $_SESSION['CommandeActionRecSend']['ville'] = $r['villedelivery_cmd'];
        $_SESSION['CommandeActionRecSend']['code_pays'] = $r['paysdelivery_cmd'];
    } elseif ($PC->rcvP['type'] == 'fax') {
        if ($result[1][0]['commercial_cmd'] != $_SESSION['user']['id']) {
            aiJeLeDroit('commande', 53);
        } else {
            aiJeLeDroit('commande', 52);
        }
        if ($r['nomdelivery_cmd'] != '')
            $_SESSION['CommandeActionRecSend']['nom'] = $r['nomdelivery_cmd'];
        else
            $_SESSION['CommandeActionRecSend']['nom'] = $r['civ_cont'] . ' ' . $r['prenom_cont'] . ' ' . $r['nom_cont'];
        if ($r['fax_cont'] != '')
            $_SESSION['CommandeActionRecSend']['fax'] = $r['fax_cont'];
        elseif ($r['fax_ent'] != '')
            $_SESSION['CommandeActionRecSend']['fax'] = $r['fax_ent'];
        else
            $_SESSION['CommandeActionRecSend']['fax'] = $r['fax_achat'];
    }
    else {
        if ($result[1][0]['commercial_cmd'] != $_SESSION['user']['id']) {
            aiJeLeDroit('commande', 51);
        } else {
            aiJeLeDroit('commande', 50);
        }
        if ($r['maildelivery_cmd'] != '')
            $_SESSION['CommandeActionRecSend']['email'] = $r['maildelivery_cmd'];
        elseif ($r['mail_cont'] != '')
            $_SESSION['CommandeActionRecSend']['email'] = $r['mail_cont'];
        else
            $_SESSION['CommandeActionRecSend']['email'] = $r['mail_achat'];
        $_SESSION['CommandeActionRecSend']['titre'] = 'STARTX : Commande n°' . $r['id_cmd'];
    }
    $in = array_merge($r, $_SESSION['CommandeActionRecSend']);
    ?>
    <root><go to="waCommandeAction1"/>
    <title set="waCommandeAction1">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waCommandeAction1" create="true"/>
        <data><![CDATA[ <?php echo commandeView::actionSend1($in); ?> ]]></data>
    </part>
    </root>
    <?php
}
elseif ($PC->rcvG['action'] == 'rec') {
    $result = $info->getDataFromID($PC->rcvG['id_cmd']);
    $resultF = $info->getFournisseurFromID($PC->rcvG['id_cmd']);
    if ($result[1][0]['commercial_cmd'] != $_SESSION['user']['id']) {
        aiJeLeDroit('commande', 61);
    } else {
        aiJeLeDroit('commande', 60);
    }
    ?>
    <root><go to="waCommandeAction"/>
    <title set="waCommandeAction">Enregistrer la Commande</title>
    <part><destination mode="replace" zone="waCommandeAction" create="true"/>
        <data><![CDATA[ <?php echo commandeView::actionRecord($result[1][0], $resultF[1]); ?> ]]></data>
    </part>
    </root>
    <?php
} elseif ($PC->rcvG['action'] == 'recsend') {
    $result = $info->getDataFromID($PC->rcvG['id_cmd']);
    $resultF = $info->getFournisseurFromID($PC->rcvG['id_cmd']);
    if ($result[1][0]['commercial_cmd'] != $_SESSION['user']['id']) {
        aiJeLeDroit('commande', 61);
    } else {
        aiJeLeDroit('commande', 60);
    }
    ?>
    <root><go to="waCommandeAction"/>
    <title set="waCommandeAction">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waCommandeAction" create="true"/>
        <data><![CDATA[ <?php echo commandeView::actionRecordSend($result[1][0], $resultF[1]); ?> ]]></data>
    </part>
    </root>
    <?php
} elseif ($PC->rcvG['action'] == 'recsend1') {
    $result = $info->getDataFromID($PC->rcvG['id_cmd']);
    $datas['data'] = $r = $result[1][0];
    $datas["data"]['tauxTVA_ent'] = $datas["data"]['tva_cmd'];
    $datas['pays'] = $info->getPays();
    $datas['user'] = $info->getUser($PC->rcvG['id_cmd']);
    $datas['produit'] = $info->getAllFournisseursFromID($PC->rcvG['id_cmd']);
    switch ($PC->rcvP['document']) {
        case 'ri':
            $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'RI');
            break;
        case 'pvr':
            $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'PVR');
            break;
        case 'bdcc':
            $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'BC');
            break;
        case 'bdl':
            $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'BDL');
            break;
        default:
            $bdd = new Bdd($GLOBALS['PropsecConf']['DBPool']);
            $bdd->makeRequeteFree("SELECT * FROM fournisseur f LEFT JOIN entreprise e ON e.id_ent = f.entreprise_fourn LEFT JOIN contact c ON c.id_cont=f.contactComm_fourn where id_fourn = '" . $fourn . "'");
            $datas['fournisseur'] = $bdd->process2();
            $datas['fournisseur'] = $datas['fournisseur'][1][0];
            $bdd->makeRequeteFree("Select * from commande_produit cp left join produit p ON p.id_prod = cp.id_produit where cp.fournisseur = '" . $fourn . "' AND cp.id_commande = '" . $PC->rcvP['id_cmd'] . "'");
            $datas['produit'] = $bdd->process2();
            $datas['produit'] = $datas['produit'][1];
            $Doc = commandeGnose::CommandeGenerateBDCF($datas, $PC->rcvP['document']);
            break;
    }
    $dir = $GLOBALS['REP']['appli'] . $GLOBALS['REP']['tmp'];

    $_SESSION['CommandeActionRecSend']['id_cmd'] = $PC->rcvG['id_cmd'];
    $_SESSION['CommandeActionRecSend']['Cannevas'] = $PC->rcvP['Cannevas'];
    $_SESSION['CommandeActionRecSend']['OutputExt'] = $PC->rcvP['OutputExt'];
    $_SESSION['CommandeActionRecSend']['message'] = $PC->rcvP['message'];
    $_SESSION['CommandeActionRecSend']['type'] = $PC->rcvP['type'];
    $_SESSION['CommandeActionRecSend']['file'] = $dir . $Doc;

    if ($PC->rcvP['type'] == 'courrier') {
        if ($result[1][0]['commercial_cmd'] != $_SESSION['user']['id']) {
            aiJeLeDroit('commande', 55);
        } else {
            aiJeLeDroit('commande', 54);
        }
        $_SESSION['CommandeActionRecSend']['nom'] = $r['nomdelivery_cmd'];
        $_SESSION['CommandeActionRecSend']['add1'] = $r['adressedelivery_cmd'];
        $_SESSION['CommandeActionRecSend']['add2'] = $r['adresse1delivery_cmd'];
        $_SESSION['CommandeActionRecSend']['cp'] = $r['cpdelivery_cmd'];
        $_SESSION['CommandeActionRecSend']['ville'] = $r['villedelivery_cmd'];
        $_SESSION['CommandeActionRecSend']['code_pays'] = $r['paysdelivery_cmd'];
    } elseif ($PC->rcvP['type'] == 'fax') {
        if ($result[1][0]['commercial_cmd'] != $_SESSION['user']['id'])
            aiJeLeDroit('commande', 53);
        else
            aiJeLeDroit('commande', 52);
        if ($r['nomdelivery_cmd'] != '')
            $_SESSION['CommandeActionRecSend']['nom'] = $r['nomdelivery_cmd'];
        else
            $_SESSION['CommandeActionRecSend']['nom'] = $r['civ_cont'] . ' ' . $r['prenom_cont'] . ' ' . $r['nom_cont'];
        if ($r['fax_cont'] != '')
            $_SESSION['CommandeActionRecSend']['fax'] = $r['fax_cont'];
        elseif ($r['fax_ent'] != '')
            $_SESSION['CommandeActionRecSend']['fax'] = $r['fax_ent'];
        else
            $_SESSION['CommandeActionRecSend']['fax'] = $r['fax_achat'];
    }
    else {
        if ($result[1][0]['commercial_cmd'] != $_SESSION['user']['id']) {
            aiJeLeDroit('commande', 51);
        } else
            aiJeLeDroit('commande', 50);
        if ($r['maildelivery_cmd'] != '')
            $_SESSION['CommandeActionRecSend']['email'] = $r['maildelivery_cmd'];
        elseif ($r['mail_cont'] != '')
            $_SESSION['CommandeActionRecSend']['email'] = $r['mail_cont'];
        else
            $_SESSION['CommandeActionRecSend']['email'] = $r['mail_achat'];
        $_SESSION['CommandeActionRecSend']['titre'] = 'STARTX : Commande n°' . $r['id_cmd'];
    }
    $in = array_merge($r, $_SESSION['CommandeActionRecSend']);
    ?>
    <root><go to="waCommandeAction1"/>
    <title set="waCommandeAction1">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waCommandeAction1" create="true"/>
        <data><![CDATA[ <?php echo commandeView::actionRecordSend1($in); ?> ]]></data>
    </part>
    </root>
    <?php
}
elseif (($PC->rcvG['action'] == 'doVoir') or ( $PC->rcvG['action'] == 'doRec') or ( $PC->rcvG['action'] == 'doSend') or ( $PC->rcvG['action'] == 'doRecsend')) {
    if ($PC->rcvG['action'] == 'doRecsend')
        $PC->rcvP = array_merge($_SESSION['CommandeActionRecSend'], $PC->rcvP);
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $commande = $info->getDataFromID($PC->rcvG['id_cmd']);
    $dev = $commande[1][0];
    if ($PC->rcvP['document'] != NULL) {
        $datas['data'] = $dev;
        $datas["data"]['tauxTVA_ent'] = $datas["data"]['tva_cmd'];
        $datas['pays'] = $info->getPays();
        $datas['user'] = $info->getUser($PC->rcvG['id_cmd']);
        $datas['produit'] = $info->getAllFournisseursFromID($PC->rcvG['id_cmd']);
        switch ($PC->rcvP['document']) {
            case 'ri':
                $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'RI');
                break;
            case 'pvr':
                $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'PVR');
                break;
            case 'bdcc':
                $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'BC');
                break;
            case 'bdl':
                $Doc = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'BDL');
                break;
            default:
                $bdd = new Bdd($GLOBALS['PropsecConf']['DBPool']);
                $bdd->makeRequeteFree("SELECT * FROM fournisseur f LEFT JOIN entreprise e ON e.id_ent = f.entreprise_fourn LEFT JOIN contact c ON c.id_cont=f.contactComm_fourn where id_fourn = '" . $fourn . "'");
                $datas['fournisseur'] = $bdd->process2();
                $datas['fournisseur'] = $datas['fournisseur'][1][0];
                $bdd->makeRequeteFree("Select * from commande_produit cp left join produit p ON p.id_prod = cp.id_produit where cp.fournisseur = '" . $fourn . "' AND cp.id_commande = '" . $PC->rcvP['id_cmd'] . "'");
                $datas['produit'] = $bdd->process2();
                $datas['produit'] = $datas['produit'][1];
                $Doc = commandeGnose::CommandeGenerateBDCF($datas, $PC->rcvP['document']);
                break;
        }
    } else {
        $datas['data'] = $dev;
        $datas["data"]['tauxTVA_ent'] = $datas["data"]['tva_cmd'];
        $datas['pays'] = $bdd->getPays();
        $datas['user'] = $bdd->getUser($PC->rcvP['id_cmd']);
        $datas['produit'] = $bdd->getAllFournisseursFromID($PC->rcvP['id_cmd']);
        foreach ($PC->rcvP as $v => $k) {
            if ($v != 'bdcc' && $v != 'bdl' && $v != 'ri' && $v != 'OutputExt' && $v != 'message' && $k == 'ok' && $v != NULL) {
                $bdd = new Bdd($GLOBALS['PropsecConf']['DBPool']);
                $bdd->makeRequeteFree("SELECT * FROM fournisseur f LEFT JOIN entreprise e ON e.id_ent = f.entreprise_fourn LEFT JOIN contact c ON c.id_cont=f.contactComm_fourn where id_fourn = '" . $fourn . "'");
                $datas['fournisseur'] = $bdd->process2();
                $datas['fournisseur'] = $datas['fournisseur'][1][0];
                $bdd->makeRequeteFree("Select * from commande_produit cp left join produit p ON p.id_prod = cp.id_produit where cp.fournisseur = '" . $fourn . "' AND cp.id_commande = '" . $PC->rcvP['id_cmd'] . "'");
                $datas['produit'] = $bdd->process2();
                $datas['produit'] = $datas['produit'][1];
                $Doc[] = commandeGnose::CommandeGenerateBDCF($datas, $v);
                $mess .= $v . ', ';
            }
        }
        if ($PC->rcvP['bdcc'] == 'ok') {
            $Doc[] = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'RI');
            $mess .='BDCC, ';
        }
        if ($PC->rcvP['pvr'] == 'ok') {
            $Doc[] = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'PVR');
            $mess .='PVR, ';
        }
        if ($PC->rcvP['bdl'] == 'ok') {
            $Doc[] = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'BDL');
            $mess .='BDL, ';
        }
        if ($PC->rcvP['ri'] == 'ok') {
            $Doc[] = commandeGnose::CommandeGenerateBDC($datas, 'pdf', 'RI');
            $mess .='RI, ';
        }
    }
    if ($PC->rcvG['action'] == 'doVoir') {
        $dir = $GLOBALS['REP']['appli'] . $GLOBALS['REP']['tmp'];
        if ($Doc != '' and file_exists($dir . $Doc)) {
            $fileSize = FileConvertSize2Human(filesize($dir . $Doc));
            $fileIcon = FileOutputType($Doc, 'image');
            $fileAdd = '<fieldset>
						<ul><li><a target="_blank" href="File.php?type=view&amp;file=' . $GLOBALS['REP']['tmp'] . $Doc . '">' . $fileIcon . $Doc . ' (' . $fileSize . ')</a></li></ul>
					</fieldset>';
        }
        ?>
        <root>
        <part><destination mode="append" zone="formCommandeDoVoirResponse"/>
            <data><![CDATA[ <?php echo $fileAdd; ?> ]]></data>
        </part>
        </root>
        <?php
    }
    if (($PC->rcvG['action'] == 'doRec') or ( $PC->rcvG['action'] == 'doRecsend')) {
        if (trim($PC->rcvP['message']) == "")
            $PC->rcvP['message'] = "Enregistrement des documents relatifs à la commande " . $dev['id_cmd'];
        if ($PC->rcvG['action'] == 'doRecsend')
            $Doc[] = substr($PC->rcvP['file'], strlen($GLOBALS['REP']['appli'] . $GLOBALS['REP']['tmp']), strlen($PC->rcvP['file']));
        $save = commandeGnose::CommandeSaveDocInGnose($Doc, $dev, $PC->rcvP['message']);

        $actuDesc = 'Les documents' . substr($mess, 0, -2) . ' de la commande ' . $dev['id_cmd'] . ' vient d\'être re-enregistrée. Elle a une valeur de ' . formatCurencyDisplay($dev['sommeHT_cmd']) . ' HT. Commentaire de l\'enregistrement : ' . $PC->rcvP['message'];
        if (is_array($Doc) and count($Doc) > 1)
            $actuDesc = 'Les documents ' . substr($mess, 0, -2) . ' de la commande ' . $dev['id_cmd'] . ' ont été généré. Ils sont disponibles dans le disque Dur Virtuel. Commentaire de l\'enregistrement : ' . $PC->rcvP['message'];
        else
            $actuDesc = 'Le document ' . substr($mess, 0, -2) . ' de la commande ' . $dev['id_cmd'] . ' vient d\'être généré. Il est disponible dans le disque Dur Virtuel. Commentaire de l\'enregistrement : ' . $PC->rcvP['message'];

        if ($dev['status_aff'] < 8) {
            $bddtmp->makeRequeteUpdate('affaire', 'id_aff', $dev['id_aff'], array('status_aff' => '8'));
            $bddtmp->process();
        }
        if ($dev['status_cmd'] < 3) {
            $bddtmp->makeRequeteUpdate('commande', 'id_cmd', $dev['id_cmd'], array('status_cmd' => '3'));
            $bddtmp->process();
        }
        $info->addActualite($dev['id_cmd'], 'record', $actuDesc, '', '', false);

        if ($PC->rcvG['action'] == 'doRec') {
            ?>
            <root><go to="waCommandeAction"/>
            <part><destination mode="replace" zone="waCommandeAction"/>
                <data><![CDATA[ <div class="msg">Votre document est maintenant enregistré dans le module ZunoGed.</div> ]]></data>
            </part>
            </root>
            <?php
        }
    }
    if (($PC->rcvG['action'] == 'doRecsend') or ( $PC->rcvG['action'] == 'doSend')) {
        if ($PC->rcvG['action'] == 'doRecsend')
            $PC->rcvP = array_merge($_SESSION['CommandeActionRecSend'], $PC->rcvP);

        if ($PC->rcvP['type'] == 'courrier')
            $control = sendControl::sendCourrier($PC->rcvP);
        elseif ($PC->rcvP['type'] == 'fax')
            $control = sendControl::sendFax($PC->rcvP);
        else
            $control = sendControl::sendMail($PC->rcvP);

        if ($control[0]) {
            $actuPrefix = ($dev['status_cmd'] >= 3) ? 'Re-e' : 'E';
            $PC->rcvP['dir_aff'] = $dev['dir_aff'];
            $PC->rcvP['partie'] = "devis";
            $PC->rcvP['typeE'] = $PC->rcvP['type'];
            $PC->rcvP['cc'] = $PC->rcvP['emailcc'];
            $PC->rcvP['mail'] = $PC->rcvP['email'];
            $PC->rcvP['from'] = $_SESSION['user']['mail'];
            $PC->rcvP['expediteur'] = $_SESSION['user']['fullnom'];
            $PC->rcvP['destinataire'] = $PC->rcvP['nom'];
            $PC->rcvP['fichier'] = substr($PC->rcvP['file'], strripos($PC->rcvP['file'], "/") + 1);
            $PC->rcvP['id'] = $_SESSION['CommandeActionRecSend']['id_cmd'];
            $PC->rcvP['channel'] = 'iphone';
            $PC->rcvP['sujet'] = $PC->rcvP['titre'];
            $send = new Sender($PC->rcvP);
            $result = $send->send($_SESSION['user']['mail']);
            $dest = ($PC->rcvP['destinataire'] == '') ? $PC->rcvP['mail'] : $PC->rcvP['destinataire'];
            if ($result[0]) {
                if ($dev['status_aff'] < 9) {
                    $bddtmp->makeRequeteUpdate('affaire', 'id_aff', $dev['id_aff'], array('status_aff' => '9'));
                    $bddtmp->process();
                }
                if ($dev['status_cmd'] < 4) {
                    $bddtmp->makeRequeteUpdate('commande', 'id_cmd', $dev['id_cmd'], array('status_cmd' => '4'));
                    $bddtmp->process();
                }
                $actuDesc = 'La commande ' . $dev['id_cmd'] . ' vient d\'être ' . strtolower($actuPrefix) . 'nvoyée par ' . $PC->rcvP['type'] . ' à ' . $dest;
                $info->addActualite($dev['id_cmd'], 'send', $actuDesc);
                ?>
                <root><go to="waCommandeAction"/>
                <part><destination mode="replace" zone="waCommandeAction"/>
                    <data><![CDATA[ <div class="msg">Votre document vient d'être envoyé par <?php echo $PC->rcvP['type']; ?>. </div> ]]></data>
                </part>
                </root>
                <?php
                unset($_SESSION['CommandeActionRecSend']);
            } else {
                ?><root><go to="waCommandeAction1"/>
                <title set="waCommandeAction1">Erreur de <?php echo $PC->rcvP['type']; ?></title>
                <part><destination mode="replace" zone="waCommandeAction1" create="true"/>
                    <data><![CDATA[ <?php echo commandeView::actionRecordSend1($PC->rcvP, array(), $result[1]); ?> ]]></data>
                </part>
                </root>
                <?php
            }
        } else {
            ?>
            <root><go to="waCommandeAction1"/>
            <title set="waCommandeAction1">Envoi de <?php echo $PC->rcvP['type']; ?></title>
            <part><destination mode="replace" zone="waCommandeAction1" create="true"/>
                <data><![CDATA[ <?php echo commandeView::actionRecordSend1($PC->rcvP, $control[2], $control[1]); ?> ]]></data>
            </part>
            </root>
            <?php
        }
    }
} elseif ($PC->rcvG['action'] == 'valide') {
    $data['status_cmd'] = '6';
    $result = $requete->update($data, $PC->rcvG['id_cmd']);
    $donnee = $requete->getDataFromID($PC->rcvG['id_cmd']);
    if ($donnee[1][0]['commercial_cmd'] != $_SESSION['user']['id'])
        aiJeLeDroit('commande', 14);
    else
        aiJeLeDroit('commande', 13);
    if ($result[0]) {
        $inActualite = array(
            'type' => 'commande',
            'titre' => 'Commande : ' . $donnee[1][0]['id_cmd'] . ' validée',
            'desc' => 'La commande ' . $PC->rcvG['id_cmd'] . ' vient d\'être marquée validée.',
            'date' => date('y-m-d H:i:s'),
            'user' => $_SESSION['user']['id'],
            'id_ent' => $donnee[1][0]['entreprise_fact'],
            'id_cont' => $donnee[1][0]['contact_fact'],
            'id_aff' => substr($donnee[1][0]['id_cmd'], 0, 6),
            'id_cmd' => $donnee[1][0]['id_cmd'],
            'status_fact' => '5');
        $requete->addActualite($PC->rcvG['id_cmd'], 'valid');
        ?>
        <root><go to="waCommandeFiche"/>
        <title set="waCommandeFiche"><?php echo $donnee[1][0]['id_cmd']; ?></title>
        <part><destination mode="replace" zone="waCommandeFiche" create="true"/>
            <data><![CDATA[ <?php echo commandeView::view($donnee[1][0]); ?> ]]></data>
        </part>
        </root>
        <?php
    }//Tout va bien, on affiche
} elseif ($PC->rcvG['action'] == 'recep') {
    $data['status_cmd'] = '8';
    $result = $requete->update($data, $PC->rcvG['id_cmd']);
    $donnee = $requete->getDataFromID($PC->rcvG['id_cmd']);
    if ($donnee[1][0]['commercial_cmd'] != $_SESSION['user']['id'])
        aiJeLeDroit('commande', 14);
    else
        aiJeLeDroit('commande', 13);
    if ($result[0]) {
        $inActualite = array('isPublieForClient' => 1);
        $requete->addActualite($PC->rcvG['id_cmd'], 'free', 'Commande : ' . $donnee[1][0]['id_cmd'] . ' réceptionnée', 'La commande ' . $PC->rcvG['id_cmd'] . ' vient d\'être réceptionnée.', '', false, $inActualite);
        $produit = $requete->getProduitsFromCommande($dev['id_cmd']);
        if ($produit[0]) {
            foreach ($produit[1] as $v) {
                $totalProd = $v['stock_prod'] + $v['quantite'];
                $bddtmp->makeRequeteUpdate('produit', 'id_prod', $v['id_prod'], array('stock_prod' => $totalProd));
                $bddtmp->process();
            }
        }
        ?>
        <root><go to="waCommandeFiche"/>
        <title set="waCommandeFiche"><?php echo $donnee[1][0]['id_cmd']; ?></title>
        <part><destination mode="replace" zone="waCommandeFiche" create="true"/>
            <data><![CDATA[ <?php echo commandeView::view($donnee[1][0]); ?> ]]></data>
        </part>
        </root>
        <?php
    }//Tout va bien, on affiche
} elseif ($PC->rcvG['action'] == 'tri_montant') {
    viewTri('commande', 'montant', 'reset', 0, 0, 'iphone', true);
} elseif ($PC->rcvG['action'] == 'triMontantMore') {
    viewTri('commande', 'montant', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
} elseif ($PC->rcvG['action'] == 'tri_creation') {
    viewTri('commande', 'creation', 'reset', 0, 0, 'iphone', true);
} elseif ($PC->rcvG['action'] == 'triCreationMore') {
    viewTri('commande', 'creation', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
} elseif ($PC->rcvG['action'] == 'tri_entreprise') {
    viewTri('commande', 'entreprise', 'reset', 0, 0, 'iphone', true);
} elseif ($PC->rcvG['action'] == 'triEntrepriseMore') {
    viewTri('commande', 'entreprise', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
} elseif ($PC->rcvG['action'] == 'tri_contact') {
    viewTri('commande', 'contact', 'reset', 0, 0, 'iphone', true);
} elseif ($PC->rcvG['action'] == 'triContactMore') {
    viewTri('commande', 'contact', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
} elseif ($PC->rcvG['action'] == 'voirStats') {
    aiJeLeDroit('commande', 45);
    $datas = getStats('commande', 'oui');
    placementAffichage('Statistiques', "waStatsCommande", 'commandeView::afficherStats', array($datas), '', 'replace');
}
ob_end_flush();
?>
