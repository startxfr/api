<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');
loadPlugin(array('ZDoc/DevisDoc','OOConverter','Send/Send'));

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerDevis.inc.php');
include_once ('V/GeneralView.inc.php');
loadPlugin(array('ZControl/GeneralControl'));
loadPlugin(array('ZModels/DevisModel','ZModels/AffaireModel','ZModels/ContactModel'));
include_once ('V/DevisView.inc.php');
include_once ('V/AffaireView.inc.php');
include_once ('V/ContactView.inc.php');
include_once ('V/SendView.inc.php');
loadPlugin(array('ZControl/DevisControl'));
loadPlugin(array('ZControl/SendControl'));

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
aiJeLeDroit('devis', 5);
if(verifDroits('devis',10)) {
    $plus = '';
}
else {
    $plus = " AND commercial_dev = '".$_SESSION['user']['id']."' ";
}
if($PC->rcvG['action'] == 'searchDevis' ) {
    viewResults($PC->rcvP['query'], 'devis', 'reset', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'searchDevisContinue') {
    viewResults('', 'devis', 'suite', 'iphone', true);
}
elseif($PC->rcvG['action'] == 'view') {
    viewFiche($PC->rcvG['id_dev'], 'devis');
}
/**
 * Visualisation des produits liés à un devis.
 */
elseif($PC->rcvG['action'] == 'produits') {
    viewProduitsLies ($PC->rcvG['id_dev'], 'devis', 'iphone');
}
elseif($PC->rcvG['action'] == 'modifProduit') {
    viewFormulaireRessourcesLies($PC->rcvG['id_dev'], 'devis', $PC->rcvG['id_prod'], $PC->rcvG['nbprod'], 'iphone', '', '');
}
/**
 * S'il faut vraiment faire la modification c'est ici
 */
elseif($PC->rcvG['action'] == 'doModifProduit') {
    $info= new devisModel();
    $result = array();
    for($k=1;$k<=$_SESSION['produits']['nombre'];$k++) {
	$idp = array($PC->rcvP['id_produit'.$k]);

	$data['id_produit']=FileCleanFileName($idp[0], 'SVN_PROP');
	$data['quantite']=($PC->rcvP['quantite'.$k] == NULL || $PC->rcvP['quantite'.$k] == '') ? 1 : $PC->rcvP['quantite'.$k];
	$data['remise']=($PC->rcvP['remise'.$k] == NULL || $PC->rcvP['remise'.$k] == '') ? 0 : $PC->rcvP['remise'.$k];
	$data['id_devis']=$PC->rcvG['id_dev'];
	$temp=$info->getInfoProduits($data['id_produit']);
	$data['desc']=($PC->rcvP['desc'.$k] == NULL || $PC->rcvP['desc'.$k] == '') ? '*'.$temp[1][0]['nom_prod'].'*' : $PC->rcvP['desc'.$k];
	$data['prix']= ($PC->rcvP['prix'.$k] == NULL || $PC->rcvP['prix'.$k] == '') ? $temp[1][0]['prix_prod'] : $PC->rcvP['prix'.$k];
	$id_produit = $PC->rcvP['old_id'.$k];
	$result[$k]=$info->updateProduits($data);
	if($PC->rcvP['memorize'.'id_produit'.$k] == 'ok') {
	    $datab['id_prod']=$data['id_produit'];
	    $datab['nom_prod'] = $PC->rcvP['desc'.$k];
	    $datab['prix_prod'] = $data['prix'];
	    $datab['famille_prod'] = '0';
	    $datab['remisefournisseur_prod'] = '0';
	    $resultat = $info->addProduit($datab);

	}

    }//On effectue la mise à jour dans la BDD autant de fois qu'il y a de produits.
    $ok = 1;
    foreach($result as $v) {
	if(!$v[0]) {
	    $ok=0;
	}
    }//Si une requète a planté, on le récupère ici.
    if($ok) {
	$result = $info->getProduitsFromID($PC->rcvG['id_dev']);
	$sommeHT = 0;
	foreach($result[1] as $v) {
	    $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
	}//On génère le total à entrer dans la BDD devis.
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select e.id_ent, e.nom_ent, c.id_cont from devis left join entreprise e on e.id_ent=devis.entreprise_dev left join contact c on c.id_cont=devis.contact_dev where id_dev='".$PC->rcvG['id_dev']."';");
	$infoprod=$sqlConn->process2();
	$sommeHT = formatCurencyDatabase($sommeHT);
	$sqlConn->makeRequeteFree("update devis set sommeHT_dev='".$sommeHT."' WHERE id_dev = '".$PC->rcvG['id_dev']."'");
	$temp = $sqlConn->process2();
    }
    else {
	$result[0] = 0;
    }//S'il y a eu une erreur, on balance l'erreur au bon endroit.

    if($result[0]) { ?>
<root><go to="waDevisProduits"/>
    <title set="waDevisProduits"><?php echo 'Produits'; ?></title>
    <part><destination mode="replace" zone="waDevisProduits" create="true"/>
        <data><![CDATA[ <?php echo devisView::produits($result[1], $PC->rcvG['id_dev']); ?> ]]></data>
    </part>
</root>
	<?php }//Tout s'est bien passé, j'affiche.
    else { ?>
<root><go to="waDevisProduits"/>
    <part><destination mode="replace" zone="waDevisProduits" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Ce devis n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
	<?php }//Rien ne va plus, je le dis à l'utilisateur.
}
/**
 * Dans le cas où l'on souhaite ajouter un produit à un devis
 */
elseif($PC->rcvG['action'] == 'addProduit') {
    placementAffichage('Produits', "waAddProduitsDevis", 'devisView::addProduits', array(array(), $PC->rcvG['id_dev']), '', 'replace');
    //J'affiche simplement le formulaire d'ajout.
}
/**
 * S'il faut vraiment faire l'ajout
 */
elseif($PC->rcvG['action'] == 'doAddProduit') {
    $control = produitControl::control($PC->rcvP);//Je controle les données

    if($control[0]) {
	$idp = array($PC->rcvP['id_produit']);
	$info= new devisModel();
	$data['id_produit']=FileCleanFileName($idp[0], 'SVN_PROP');
	$data['quantite']=($PC->rcvP['quantite'] == NULL || $PC->rcvP['quantite'] == '') ? 1 : $PC->rcvP['quantite'];
	$data['remise']=($PC->rcvP['remise'] == NULL || $PC->rcvP['remise'] == '') ? 0 : $PC->rcvP['remise'];
	$data['id_devis']=$PC->rcvG['id_dev'];
	$temp=$info->getInfoProduits($data['id_produit']);
	$data['desc']=($PC->rcvP['desc0'] == NULL || $PC->rcvP['desc0'] == '') ? '*'.$temp[1][0]['nom_prod'].'*' : '*'.$PC->rcvP['desc0'].'*';
	$data['prix']= ($PC->rcvP['prix'] == NULL || $PC->rcvP['prix'] == '') ? $temp[1][0]['prix_prod'] : $PC->rcvP['prix'];
	$result = $info->insertProduits($data);//Je fais l'insertion dans la BDD
	if($result[0]) {
	    if($PC->rcvP['memorize'.'id_produit'] == 'ok') {
		$datab['id_prod']=$PC->rcvP['id_produit'];
		$datab['nom_prod'] = $PC->rcvP['desc0'];
		$datab['prix_prod'] = $PC->rcvP['prix'];
		$datab['famille_prod'] = '0';
		$datab['remisefournisseur_prod'] = '0';
		$result = $info->addProduit($datab);


	    }
	    $result = $info->getProduitsFromID($PC->rcvG['id_dev']);
	    $sommeHT = 0;
	    foreach($result[1] as $v) {
		$sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
	    }
	    $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $sqlConn->makeRequeteFree("select e.id_ent, e.nom_ent, c.id_cont from devis left join entreprise e on e.id_ent=devis.entreprise_dev left join contact c on c.id_cont=devis.contact_dev where id_dev='".$PC->rcvG['id_dev']."';");
	    $infoprod=$sqlConn->process2();
	    $sommeHT = formatCurencyDatabase($sommeHT);
	    $sqlConn->makeRequeteFree("update devis set sommeHT_dev='".$sommeHT."' WHERE id_dev = '".$PC->rcvG['id_dev']."'");
	    $temp = $sqlConn->process2();

	    ?>
<root><go to="waDevisProduits"/>
    <title set="waDevisProduits"><?php echo 'Produits'; ?></title>
    <part><destination mode="replace" zone="waDevisProduits" create="true"/>
        <data><![CDATA[ <?php echo devisView::produits($result[1], $PC->rcvG['id_dev']); ?> ]]></data>
    </part>
</root>
	    <?php
	}//Tout va bien, je l'affiche.
	else {
	    $test = ereg("Duplicate",$result[1],$erreur);
	    if ($test == 9 ) {
		$mess = "Ce produit est déjà présent dans le devis en cours.";
	    }
	    else {
		$mess = "Une erreur est survenue";
	    }
	    ?><root><go to="waModifProduits"/>
    <title set="waModifProduits"><?php echo 'Produits'; ?></title>
    <part><destination mode="replace" zone="waModifProduits" create="true"/>
        <data><![CDATA[ <?php echo devisView::addProduits($PC->rcvP, $PC->rcvG['id_dev'], $mess); ?> ]]></data>
    </part>
</root><?php
	}

    }
    else {
	?><root><go to="waModifProduits"/>
    <title set="waModifProduits"><?php echo 'Produits'; ?></title>
    <part><destination mode="replace" zone="waModifProduits" create="true"/>
        <data><![CDATA[ <?php echo devisView::addProduits($PC->rcvP, $PC->rcvG['id_dev'], $control[1]); ?> ]]></data>
    </part>
</root><?php
    }//L'utilisateur n'a pas rentré de données valide, je le remets sur le formulaire.
}
/**
 * Dans le cas où a été demandée une sppression d'un produit dans un devis
 */
elseif($PC->rcvG['action'] == 'suppProduit') {
    $data['id_devis'] = $PC->rcvG['id_dev'];
    $data['id_produit'] = $PC->rcvP['id_produit'];
    $info= new devisModel();

    $result = $info->deleteProduits($data);//J'effectue la suppression du produit de la BDD.
    if($result[0]) {
	$result = $info->getProduitsFromID($PC->rcvG['id_dev']);
	$sommeHT = 0;
	foreach($result[1] as $v) {
	    $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
	}
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select e.id_ent, e.nom_ent, c.id_cont from devis left join entreprise e on e.id_ent=devis.entreprise_dev left join contact c on c.id_cont=devis.contact_dev where id_dev='".$PC->rcvG['id_dev']."';");
	$infoprod=$sqlConn->process2();
	$sommeHT = formatCurencyDatabase($sommeHT);
	$sqlConn->makeRequeteFree("update devis set sommeHT_dev='".$sommeHT."' WHERE id_dev = '".$PC->rcvG['id_dev']."'");
	$temp = $sqlConn->process2();

	?>
<root><go to="waDevisProduits"/>
    <title set="waDevisProduits"><?php echo 'Produits'; ?></title>
    <part><destination mode="replace" zone="waDevisProduits" create="true"/>
        <data><![CDATA[ <?php echo devisView::produits($result[1], $PC->rcvG['id_dev']); ?> ]]></data>
    </part>
</root>
	<?php
    }//J'affiche le résultat.
}
/**
 * Pour modifier un devis (tout sauf les produits)
 */
elseif($PC->rcvG['action'] == 'modifDevis') {
    viewFormulaire($PC->rcvG['id_dev'], 'devis', 'modif', 'iphone', true, '');
}
/**
 * S'il faut vraiment faire la modification
 */
elseif($PC->rcvG['action'] == 'doModifDevis') {

    // On verifie alors les données fournies
    $control = devisControl::control($PC->rcvP);

    if($control[0]) // si elles sont bonnes, on lance le modèle pour modification
    {
	$info = new devisModel();
	$result = $info->update($PC->rcvP,$PC->rcvG['id_dev']);//Je fais l'insertion
	if($result[0]) {

	    $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $sqlConn->makeRequeteFree("select e.id_ent, c.id_cont from devis left join entreprise e on e.id_ent=devis.entreprise_dev left join contact c on c.id_cont=devis.contact_dev where id_dev='".$PC->rcvG['id_dev']."';");
	    $infoprod=$sqlConn->process2();

	    viewFiche($PC->rcvG['id_dev'], 'devis');
	}//J'affiche le résultat.
    }
    else {
	?>
<root><go to="waDevisModif"/>
    <title set="waDevisModif"><?php echo $result[1][0]['id_dev'];?></title>
    <part><destination mode="replace" zone="waDevisModif" create="true"/>
        <data><![CDATA[ <?php echo devisView::modif($PC->rcvP,$control[2],$control[1],$PC->rcvG['id_dev']); ?> ]]></data>
    </part><script><![CDATA[ _KK(); ]]></script>
</root>
	<?php
    }//L'utilisateur n'a pas passé le controleur, je lui renvois son formulaire.
}
/**
 * Et si on veut supprimer un devis.
 */
elseif($PC->rcvG['action'] == 'suppDevis') {
    viewFormulaire($PC->rcvG['id_dev'], 'devis', 'supp', 'iphone', true, '');
}
/**
 * Si on a validé la demande de suppression d'un devis
 */
elseif($PC->rcvG['action'] == 'doDeleteDevis') {
    $info = new devisModel();
    $devis = $info->getDataFromID($PC->rcvG['id_dev']);
    $result = $info->delete($PC->rcvG['id_dev']);
    //Je récupère l'ID du devis puis le supprime.
    if($result[0]) {

	?>
<root><go to="waDevisDelete"/>
    <title set="waDevisDelete"><?php echo $result[1][0]['id_dev']; ?></title>
    <part><destination mode="replace" zone="waDevisDelete" create="true"/>
        <data><![CDATA[ <?php echo devisView::delete($result[1][0]); ?> ]]></data>
    </part>
</root>
	<?php }//J'affiche le résultat.
    else { ?>
<root><go to="waDevisFiche"/>
    <part><destination mode="replace" zone="waDevisFiche" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Ce devis n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
	<?php }//Problème, je préviens l'utilisateur.
}
/**
 * Si on veut ajouter un nouveau devis.
 */
elseif($PC->rcvG['action'] == 'addDevis') {
    viewFormulaire('', 'devis', 'add', 'iphone', true, '');
}
/**
 * Si on veut ajouter un nouveau devis directement d'une fiche affaire
 */
elseif($PC->rcvG['action'] == 'addDevisFromAffaire') {
    aiJeLeDroit('commande', 20);
    $info = new affaireModel();
    $affaire = $info->getDataFromID($PC->rcvG['id_aff']);
    $affaire = $affaire[1][0];
    $devis['affaire_dev'] = $affaire['id_aff'];
    $devis['titre_dev'] = $affaire['titre_aff'];
    $devis['contact_dev'] = $affaire['contact_aff'];
    ?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis"; ?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo devisView::add($devis); ?> ]]></data>
    </part>
</root>
    <?php //J'affiche le formulaire d'ajout.
}
/**
 * Si on a confirmé la demande d'ajout d'un devis.
 */
elseif($PC->rcvG['action'] == 'doAddDevis') {

    $control = devisControl::controlAdd($PC->rcvP);//Je controle les données forunies.
    if($control[0]) {
	$info = new devisModel();
	$id = $info->createId($PC->rcvP['affaire_dev']);
	$ent = $info->getEntrepriseData($PC->rcvP['affaire_dev']);
	$cont = $info->getContactData($PC->rcvP['contact_dev']);
	if((!$ent[0] and !$cont[0]) or
		($ent[0] and (
			$ent[1][0]['add1_ent'] == NULL ||
				$ent[1][0]['cp_ent'] == NULL ||
				$ent[1][0]['ville_ent'] == NULL)) or
		($cont[0] and (
			$cont[1][0]['add1_cont'] == NULL ||
				$cont[1][0]['cp_cont'] == NULL ||
				$cont[1][0]['ville_cont'] == NULL))) {//Si je n'ai pas d'entreprise liée, ou si les adresses sont vides, je préviens l'utilisateur.
	    if($cont[0]) {
		$data['nomdelivery_dev'] 	= $ent[1][0]['nom_cont'];
		$data['adressedelivery_dev'] 	= $ent[1][0]['add1_cont'];
		$data['adresse1delivery_dev'] = $ent[1][0]['add2_cont'];
		$data['villedelivery_dev'] 	= $ent[1][0]['ville_cont'];
		$data['cpdelivery_dev'] 	= $ent[1][0]['cp_cont'];
		$data['paysdelivery_dev'] 	= $ent[1][0]['pays_cont'];
	    }
	    elseif($ent[0]) {
		$data['nomdelivery_dev'] 	= $ent[1][0]['nom_ent'];
		$data['adressedelivery_dev'] 	= $ent[1][0]['add1_ent'];
		$data['adresse1delivery_dev'] = $ent[1][0]['add2_ent'];
		$data['villedelivery_dev'] 	= $ent[1][0]['ville_ent'];
		$data['cpdelivery_dev'] 	= $ent[1][0]['cp_ent'];
		$data['paysdelivery_dev'] 	= $ent[1][0]['pays_ent'];
	    }
	    else  $data = array();
	    $in = array_merge($data,$PC->rcvP);
	    ?>
<root><go to="waDevisAddPlus"/>
    <title set="waDevisAddPlus"><?php echo "Coordonnées";?></title>
    <part><destination mode="replace" zone="waDevisAddPlus" create="true"/>
        <data><![CDATA[ <?php echo devisView::addPlus($in,array(),''); ?> ]]></data>
    </part><script><![CDATA[ _KK(); ]]></script>
</root>
	    <?php //J'affiche un nouveau formulaire pour être sur que l'utilisateur sait ce qu'il fait.

	}
	elseif($ent[0] or $cont[0]) {//Conditions parfaites, je prépare les variables pour insertion.
	    if($cont[0]) {
		$data['nomdelivery_dev'] 	= $ent[1][0]['nom_cont'];
		$data['adressedelivery_dev'] 	= $ent[1][0]['add1_cont'];
		$data['adresse1delivery_dev'] = $ent[1][0]['add2_cont'];
		$data['villedelivery_dev'] 	= $ent[1][0]['ville_cont'];
		$data['cpdelivery_dev'] 	= $ent[1][0]['cp_cont'];
		$data['paysdelivery_dev'] 	= $ent[1][0]['pays_cont'];
		$data['maildelivery_dev'] 	= $cont[1][0]['mail_cont'];
	    }
	    if($ent[0]) {
		$data['entreprise_dev'] 	= $ent[1][0]['id_ent'];
		$data['nomdelivery_dev'] 	= $ent[1][0]['nom_ent'];
		$data['adressedelivery_dev'] 	= $ent[1][0]['add1_ent'];
		$data['adresse1delivery_dev'] = $ent[1][0]['add2_ent'];
		$data['villedelivery_dev'] 	= $ent[1][0]['ville_ent'];
		$data['cpdelivery_dev'] 	= $ent[1][0]['cp_ent'];
		$data['paysdelivery_dev'] 	= $ent[1][0]['pays_ent'];
	    }

	    $data['id_dev'] = $id;
	    $data['affaire_dev'] = $PC->rcvP['affaire_dev'];
	    $data['status_dev'] = '1';
	    $data['titre_dev'] = ($PC->rcvP['titre_dev'] != '') ? $PC->rcvP['titre_dev'] : '';
	    $data['commercial_dev'] = $PC->rcvP['commercial_dev'];
	    $data['sommeHT_dev'] = '0';
	    $data['contact_dev'] = $PC->rcvP['contact_dev'];
	    $data['contact_achat_dev'] = ($PC->rcvP['contact_achat_dev'] != '') ? $PC->rcvP['contact_achat_dev'] : NULL;
	    $data['tva_dev'] = $ent[1][0]['tauxTVA_ent'];
	    $resultinsert = $info->insert($data);//Je fais l'insertion.
	    if($resultinsert[0]) {

		viewFiche($id, 'devis');
	    }
	    else {
		?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
		<?php
	    }//Problème que j'explique à l'utilisateur.
	}
	else {
	    ?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
	    <?php
	}//Un autre problème...
    }
    else {
	?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo devisView::add($PC->rcvP,$control[2],$control[1]); ?> ]]></data>
    </part><script><![CDATA[ _KK(); ]]></script>
</root>
	<?php //L'utilisateur n'a pas passé le controleur.
    }
}
/**
 * Dans le cas d'un ajout d'un devis sans adresse.
 */
elseif($PC->rcvG['action'] == 'doAddDevisPlus') {
    $control = devisControl::controlAdd($PC->rcvP);//Je passe les données au controleur.
    if($control[0]) {
	$info = new devisModel();
	$id = $info->createId($PC->rcvP['affaire_dev']);
	$ent = $info->getEntrepriseData($PC->rcvP['affaire_dev']);
	$cont = $info->getContactData($PC->rcvP['contact_dev']);
	if($ent[0] && $cont[0] && ($PC->rcvP['affaire_dev'] != $PC->rcvG['id_aff']) && ($ent[1][0]['add1_ent'] == NULL || $ent[1][0]['cp_ent'] == NULL || $ent[1][0]['ville_ent'] == NULL)) {//Si l'utilisateur a modifié l'affaire liée à son devis, et que cette dernière n'est toujours pas liée à une entreprise ayant une adresse.

	    ?>
<root><go to="waDevisAddPlus"/>
    <title set="waDevisAddPlus"><?php echo "Coordonnées";?></title>
    <part><destination mode="replace" zone="waDevisAddPlus" create="true"/>
        <data><![CDATA[ <?php echo devisView::addPlus($PC->rcvP, array(),'', $cont[1][0]); ?> ]]></data>
    </part><script><![CDATA[ _KK(); ]]></script>
</root>
	    <?php //Je lui redemande confirmation que c'est bien son choix.

	}
	elseif($ent[0] && $cont[0] && ($PC->rcvP['affaire_dev'] != $PC->rcvG['id_aff']) && ($ent[1][0]['add1_ent'] != NULL&& $ent[1][0]['cp_ent'] != NULL && $ent[1][0]['ville_ent'] != NULL)) {//La nouvelle affaire est liée à une entreprise, je prend en compte !
	    $data['id_dev'] = $id;
	    $data['affaire_dev'] = $PC->rcvP['affaire_dev'];
	    $data['entreprise_dev'] = $ent[1][0]['id_ent'];
	    $data['status_dev'] = '1';
	    $data['titre_dev'] = ($PC->rcvP['titre_dev'] != '') ? $PC->rcvP['titre_dev'] : $ent[1][0]['titre_aff'];
	    $data['commercial_dev'] = $PC->rcvP['commercial_dev'];
	    $data['sommeHT_dev'] = '0';
	    $data['contact_dev'] = $PC->rcvP['contact_dev'];
	    $data['contact_achat_dev'] = ($PC->rcvP['contact_achat_dev'] != '') ? $PC->rcvP['contact_achat_dev'] : NULL;
	    $data['nomdelivery_dev'] = $ent[1][0]['nom_ent'];
	    $data['adressedelivery_dev'] = $ent[1][0]['add1_ent'];
	    $data['adresse1delivery_dev'] = $ent[1][0]['add2_ent'];
	    $data['villedelivery_dev'] = $ent[1][0]['ville_ent'];
	    $data['cpdelivery_dev'] = $ent[1][0]['cp_ent'];
	    $data['paysdelivery_dev'] = $ent[1][0]['pays_ent'];
	    $data['maildelivery_dev'] = $cont[1][0]['mail_cont'];
	    $data['tva_dev'] = $ent[1][0]['tauxTVA_ent'];
	    $resultinsert = $info->insert($data);//Je fais l'insertion.
	    if($resultinsert[0]) {

		viewFiche($id, 'devis');
	    }
	    else {
		?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
		<?php
	    }//Problème que j'explique à l'utilisateur.
	}
	elseif($ent[0] && $cont[0]) {//Sinon, je fais l'insertion dans la BDD.
	    $data['id_dev'] = $id;
	    $data['affaire_dev'] = $PC->rcvP['affaire_dev'];
	    $data['entreprise_dev'] = ($ent[1][0]['id_ent'] == NULL || $ent[1][0]['id_ent'] == '') ? NULL : $ent[1][0]['id_ent'];
	    $data['status_dev'] = '1';
	    $data['titre_dev'] = ($PC->rcvP['titre_dev'] != '') ? $PC->rcvP['titre_dev'] : $ent[1][0]['titre_aff'];
	    $data['commercial_dev'] = $PC->rcvP['commercial_dev'];
	    $data['sommeHT_dev'] = '0';
	    $data['contact_dev'] = $PC->rcvP['contact_dev'];
	    $data['contact_achat_dev'] = ($PC->rcvP['contact_achat_dev'] != '') ? $PC->rcvP['contact_achat_dev'] : NULL;
	    $data['nomdelivery_dev'] = ($PC->rcvP['nomdelivery_dev'] != '') ? $PC->rcvP['nomdelivery_dev'] : NULL;
	    $data['adressedelivery_dev'] = ($PC->rcvP['adressedelivery_dev'] != '') ? $PC->rcvP['adressedelivery_dev'] : NULL;
	    $data['adresse1delivery_dev'] = ($PC->rcvP['adresse1delivery_dev'] != '') ? $PC->rcvP['adresse1delivery_dev'] : NULL;
	    $data['villedelivery_dev'] = ($PC->rcvP['villedelivery_dev'] != '') ? $PC->rcvP['villedelivery_dev'] : NULL;
	    $data['cpdelivery_dev'] = ($PC->rcvP['cpdelivery_dev'] != '') ? $PC->rcvP['cpdelivery_dev'] : NULL;
	    $data['paysdelivery_dev'] = $PC->rcvP['paysdelivery_dev'];
	    $data['maildelivery_dev'] = $cont[1][0]['mail_cont'];
	    $data['tva_dev'] = ($data['paysdelivery_dev'] == NULL || $data['paysdelivery_dev'] == '' || $data['paysdelivery_dev'] == '1') ? '19.6' : '0';
	    $resultinsert = $info->insert($data);
	    if($resultinsert[0]) {

		viewFiche($id, 'devis');
	    }
	    else {
		?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
		<?php
	    }//Une erreur est survenue avec la BDD.
	}
	else {
	    ?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
	    <?php
	}//Une autre erreur avec la BDD
    }
    else {
	?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo devisView::add($PC->rcvP,$control[2],$control[1]); ?> ]]></data>
    </part><script><![CDATA[ _KK(); ]]></script>
</root>
	<?php
    }//L'utilisateur n'a pas passé le controleur.

}
elseif($PC->rcvG['action'] == 'inputProduit') {
    $_SESSION['searchProduitLayerBackTo'] = $PC->rcvG['__source'];
    $_SESSION['searchProduitTagsBackTo'] = $PC->rcvG['tag'];
    ?>
<root><go to="waProduitInputAjax"/>
    <title set="waProduitInputAjax">Recherche</title>
    <part><destination mode="replace" zone="waProduitInputAjax" create="true"/>
        <data><![CDATA[ <?php echo ZunoLayerDevis::headerFormSearchProd(); ?> ]]></data>
    </part>
    <script><![CDATA[ new dynAjax('formSearchProduitInput',3,'formSearchProduitajax'); ]]></script>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'inputProduitDESuite') {
    $nombre = $_SESSION['devisExpress']['nb_prod'];
    $nombre++;
    $coukejesuis = ($_SESSION['searchProduitLayerBackTo'] == "waDevisAddExpressSuite" ) ? 'Devis' : 'Facture';
    $out='<fieldset><legend>Produit '.$nombre.'</legend><ul>';
    $id = devisView::inputAjaxProduit('id_produit'.$coukejesuis.'Express'.$nombre, $val['id_produit'.$coukejesuis.'Express'.$nombre],'Produit : ',true, 'express', 'DE');
    $qtte = HtmlFormIphone::InputLabel('quantite'.$nombre, $val['quantite'.$nombre], 'Quantité : ', 'id="id_produit'.$coukejesuis.'Express'.$nombre.'quantite", onchange="quantiteOn'.$coukejesuis.'Express(this.value,'.$_SESSION['devisExpress']['nb_prod'].','.$nombre.',\'19.6\');"');
    $remise = HtmlFormIphone::InputLabel('remise'.$nombre, $val['remise'.$nombre], 'Remise : ', 'id="id_produit'.$coukejesuis.'Express'.$nombre.'remise", onchange="remiseOn'.$coukejesuis.'Express(this.value,'.$_SESSION['devisExpress']['nb_prod'].','.$nombre.',\'19.6\');"');
    $prix = HtmlFormIphone::InputLabel('prix'.$nombre,$val['prix'.$nombre], 'Px unit. : ', 'id="id_produit'.$coukejesuis.'Express'.$nombre.'prix", onchange="prixOn'.$coukejesuis.'Express(this.value,'.$_SESSION['devisExpress']['nb_prod'].','.$nombre.',\'19.6\');"');
    $desc = HtmlFormIphone::TextareaLabel('desc'.$nombre, $val['desc'.$nombre],' id="id_produit'.$coukejesuis.'Express'.$nombre.'desc" ', 'Description : ');
    $out .='<li>'.$id.'</li>';
    $out .='<li>'.$desc.'</li>';
    $out .='<li>'.$qtte.'</li>';
    $out .='<li>'.$remise.'</li>';
    $out .='<li>'.$prix.'</li>';
    $out .='<li>Ss total : <div style="display:inline" id="sstotalid_produit'.$coukejesuis.'Express'.$nombre.'">0</div> €</li>';
    $out .='</ul></fieldset>';
    $_SESSION['devisExpress']['nb_prod']++;
    ?>
<root>
    <part>
        <destination mode="append" zone="<?php echo "DEProd".$coukejesuis; ?>" />
        <data><![CDATA[ <?php echo $out; ?> ]]></data>
    </part>
</root>
    <?php

}
elseif($PC->rcvG['action'] == 'inputProduitDE') {
    $_SESSION['searchProduitLayerBackTo'] = $PC->rcvG['__source'];
    $_SESSION['searchProduitTagsBackTo'] = $PC->rcvG['tag'];
    ?>
<root><go to="waProduitInputAjax"/>
    <title set="waProduitInputAjax">Recherche</title>
    <part><destination mode="replace" zone="waProduitInputAjax" create="true" />
        <data><![CDATA[ <?php echo ZunoLayerDevis::headerFormSearchProdDE(); ?> ]]></data>
    </part>
    <script><![CDATA[ new dynAjax('formSearchProduitInput',3,'formSearchProduitajax'); ]]></script>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'inputProduitResult') {
    $_SESSION['searchProduitQuery'] = $PC->rcvP['search'];
    $layerBackTo = $_SESSION['searchProduitLayerBackTo'];
    $tagsBackTo = $_SESSION['searchProduitTagsBackTo'];
    $info = new devisModel();
    $from = 0;
    $limit = $_SESSION['user']['config']['LenghtSearchDevis'];
    $produit = $info->getDataForSearchProd($PC->rcvP['search'],$limit,$from);
    $total = $info->getDataForSearchProd($PC->rcvP['search']);
    $total = $total[1][0]["counter"];
    if($produit[0]) {
	$out .= '<ul id="searchResultInputProduitUl">';
	$out .= devisView::searchInputResultRowProd($produit[1],$_SESSION['searchProduitLayerBackTo'],$_SESSION['searchProduitTagsBackTo']);
	if($total > $limit)
	    $out .= '<li class="iMore" id="searchResultInputProduitMore'.$from.'"><a href="Devis.php?action=inputProduitContinue&from='.$limit.'&total='.$total.'" rev="async">Plus de résultats</a></li>';
	$out .= '<li class="iMore" id="addProduit'.$from.'"><a href="#_'.substr($layerBackTo,2).'" onclick="returnAjaxInputResultProdNew(\''.$tagsBackTo.'\',\''.$_SESSION['searchProduitQuery'].'\',\''.$_SESSION['searchProduitQuery'].'\');WA.Back()">Utiliser cette référence</a></li>';
	$out .= '</ul>';
    }
    else	$out .= '<h2 class="Devis">Produit (0)</h2>';

    ?>
<root>
    <part>
        <destination mode="replace" zone="SearchProduitResultAsync"/>
        <data><![CDATA[
            <div class="iList">
		    <?php echo $out; ?>
            </div>
			]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'inputProduitContinue') {
    $layerBackTo = $_SESSION['searchProduitLayerBackTo'];
    $tagsBackTo = $_SESSION['searchProduitTagsBackTo'];
    $info = new devisModel();
    $zoneTo = $outJs = $out = '';
    $limit = $_SESSION['user']['config']['LenghtSearchDevis'];
    $from = ($PC->rcvG['from'] != '') ? $PC->rcvG['from'] : 0;
    $result = $info->getDataForSearchProd($_SESSION['searchProduitQuery'],$limit,$from);
    if($result[0]) {
	$out .= devisView::searchInputResultRowProd($result[1],$_SESSION['searchProduitLayerBackTo'],$_SESSION['searchProduitTagsBackTo']);
	if($PC->rcvG['total'] > $limit+$from)
	    $out .= '<li class="iMore" id="searchResultInputProduitMore'.$from.'"><a href="Devis.php?action=inputProduitContinue&from='.($from+$limit).'&total='.$PC->rcvG['total'].'" rev="async">Plus de résultats</a></li>';
	$out .= '<li class="iMore" id="addProduit'.$from.'"><a href="#_'.substr($layerBackTo,2).'" onclick="returnAjaxInputResultProdNew(\''.$tagsBackTo.'\',\''.$_SESSION['searchProduitQuery'].'\',\''.$_SESSION['searchProduitQuery'].'\');WA.Back()">Utiliser cette référence</a></li>';
	$outJs = 'removeElementFromDom(\'searchResultInputProduitMore'.($from-$limit).'\');removeElementFromDom(\'addProduit'.($from-$limit).'\')';
	$zoneTo = 'searchResultInputProduitUl';
    }

    if($zoneTo != '') {	?>
<root>
    <part>
        <destination mode="append" zone="<?php echo $zoneTo; ?>"/>
        <data><![CDATA[ <?php echo $out; ?> ]]></data>
        <script><![CDATA[ <?php echo $outJs; ?> ]]></script>
    </part>
</root>
	<?php
    }
}
elseif($PC->rcvG['action'] == 'cloner') {
    viewFormulaire($PC->rcvG['id_dev'], 'devis', 'cloner', 'iphone', true, '');
}
elseif($PC->rcvG['action'] == 'doCloner') {
    $info = new devisModel();
    $result = $info->getDataFromID($PC->rcvG['id_dev']);
    $id = $info->createId($result[1][0]['affaire_dev']);
    $prod=$info->getProduitsFromID($PC->rcvG['id_dev']);
    $result[1][0]['id_dev'] = $id;
    $result[1][0]['status_dev'] = '1';

    $resultat = $info->insert($result[1][0], 'cloner', $prod[1], $PC->rcvG['id_dev']);
    if($resultat[0]) {

	viewFiche($id, 'devis');
    }
    else {
	?>
<root><go to="waDevisAction"/>
    <title set="waDevisAction"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAction" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
	<?php
    }
}
elseif($PC->rcvG['action'] == 'voir') {
    $info = new devisModel();
    $result = $info->getDataFromID($PC->rcvG['id_dev']);
    if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	aiJeLeDroit('devis', 63);
    }
    else {
	aiJeLeDroit('devis', 62);
    }
    ?>
<root><go to="waDevisAction"/>
    <title set="waDevisAction">Voir le Devis</title>
    <part><destination mode="replace" zone="waDevisAction" create="true"/>
        <data><![CDATA[ <?php echo devisView::actionVoir($result[1][0]); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'send') {
    $info = new devisModel();
    $result = $info->getDataFromID($PC->rcvG['id_dev']);
    ?>
<root><go to="waDevisAction"/>
    <title set="waDevisAction">Envoyer le Devis</title>
    <part><destination mode="replace" zone="waDevisAction" create="true"/>
        <data><![CDATA[ <?php echo devisView::actionSend($result[1][0]); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'send1') {
    $info = new devisModel();
    $result = $info->getDataFromID($PC->rcvG['id_dev']);
    $r = $result[1][0];
    $ooGnose = new devisGnose();
    $Doc =$ooGnose->DevisGenerateDocument($PC->rcvG['id_dev'],$PC->rcvP['OutputExt'],$PC->rcvP['Cannevas']);
    $dir = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];

    $_SESSION['DevisActionRecSend']['id_dev'] = $PC->rcvG['id_dev'];
    $_SESSION['DevisActionRecSend']['Cannevas'] = $PC->rcvP['Cannevas'];
    $_SESSION['DevisActionRecSend']['OutputExt'] = $PC->rcvP['OutputExt'];
    $_SESSION['DevisActionRecSend']['message'] = $PC->rcvP['message'];
    $_SESSION['DevisActionRecSend']['type'] = $PC->rcvP['type'];
    $_SESSION['DevisActionRecSend']['file'] = $dir.$Doc;

    if($PC->rcvP['type'] == 'courrier') {
	if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	    aiJeLeDroit('devis', 55);
	}
	else {
	    aiJeLeDroit('devis', 54);
	}
	$_SESSION['DevisActionRecSend']['nom'] = $r['nomdelivery_dev'];
	$_SESSION['DevisActionRecSend']['add1'] = $r['adressedelivery_dev'];
	$_SESSION['DevisActionRecSend']['add2'] = $r['adresse1delivery_dev'];
	$_SESSION['DevisActionRecSend']['cp'] = $r['cpdelivery_dev'];
	$_SESSION['DevisActionRecSend']['ville'] = $r['villedelivery_dev'];
	$_SESSION['DevisActionRecSend']['code_pays'] = $r['paysdelivery_dev'];
    }
    elseif($PC->rcvP['type'] == 'fax') {
	if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	    aiJeLeDroit('devis', 53);
	}
	else {
	    aiJeLeDroit('devis', 52);
	}
	if($r['nomdelivery_dev'] != '')
	    $_SESSION['DevisActionRecSend']['nom'] = $r['nomdelivery_dev'];
	else  $_SESSION['DevisActionRecSend']['nom'] = $r['civ_cont'].' '.$r['prenom_cont'].' '.$r['nom_cont'];
	if($r['fax_cont'] != '')
	    $_SESSION['DevisActionRecSend']['fax'] = $r['fax_cont'];
	elseif($r['fax_ent'] != '')
	    $_SESSION['DevisActionRecSend']['fax'] = $r['fax_ent'];
	else  $_SESSION['DevisActionRecSend']['fax'] = $r['fax_achat'];
    }
    else {
	if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	    aiJeLeDroit('devis', 51);
	}
	else {
	    aiJeLeDroit('devis', 50);
	}
	if($r['maildelivery_dev'] != '')
	    $_SESSION['DevisActionRecSend']['email'] = $r['maildelivery_dev'];
	elseif($r['mail_cont'] != '')
	    $_SESSION['DevisActionRecSend']['email'] = $r['mail_cont'];
	else  $_SESSION['DevisActionRecSend']['email'] = $r['mail_achat'];
	$_SESSION['DevisActionRecSend']['titre'] = 'STARTX : Devis '.$r['id_dev'];
    }
    $in = array_merge($r,$_SESSION['DevisActionRecSend']);
    ?>
<root><go to="waDevisAction1"/>
    <title set="waDevisAction1">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waDevisAction1" create="true"/>
        <data><![CDATA[ <?php echo devisView::actionSend1($in); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'rec') {
    $info = new devisModel();
    $result = $info->getDataFromID($PC->rcvG['id_dev']);
    if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	aiJeLeDroit('devis', 61);
    }
    else {
	aiJeLeDroit('devis', 60);
    }
    ?>
<root><go to="waDevisAction"/>
    <title set="waDevisAction">Enregistrer le Devis</title>
    <part><destination mode="replace" zone="waDevisAction" create="true"/>
        <data><![CDATA[ <?php echo devisView::actionRecord($result[1][0]); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'recsend') {
    $info = new devisModel();
    $result = $info->getDataFromID($PC->rcvG['id_dev']);
    if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	aiJeLeDroit('devis', 61);
    }
    else {
	aiJeLeDroit('devis', 60);
    }
    ?>
<root><go to="waDevisAction"/>
    <title set="waDevisAction">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waDevisAction" create="true"/>
        <data><![CDATA[ <?php echo devisView::actionRecordSend($result[1][0]); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'recsend1') {
    $info = new devisModel();
    $result = $info->getDataFromID($PC->rcvG['id_dev']);
    $r = $result[1][0];
    $ooGnose = new devisGnose();
    $Doc = $ooGnose->DevisGenerateDocument($PC->rcvG['id_dev'],$PC->rcvP['OutputExt'],$PC->rcvP['Cannevas']);
    $dir = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];

    $_SESSION['DevisActionRecSend']['id_dev'] = $PC->rcvG['id_dev'];
    $_SESSION['DevisActionRecSend']['Cannevas'] = $PC->rcvP['Cannevas'];
    $_SESSION['DevisActionRecSend']['OutputExt'] = $PC->rcvP['OutputExt'];
    $_SESSION['DevisActionRecSend']['message'] = $PC->rcvP['message'];
    $_SESSION['DevisActionRecSend']['type'] = $PC->rcvP['type'];
    $_SESSION['DevisActionRecSend']['file'] = $dir.$Doc;

    if($PC->rcvP['type'] == 'courrier') {
	if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	    aiJeLeDroit('devis', 55);
	}
	else {
	    aiJeLeDroit('devis', 54);
	}
	$_SESSION['DevisActionRecSend']['nom'] = $r['nomdelivery_dev'];
	$_SESSION['DevisActionRecSend']['add1'] = $r['adressedelivery_dev'];
	$_SESSION['DevisActionRecSend']['add2'] = $r['adresse1delivery_dev'];
	$_SESSION['DevisActionRecSend']['cp'] = $r['cpdelivery_dev'];
	$_SESSION['DevisActionRecSend']['ville'] = $r['villedelivery_dev'];
	$_SESSION['DevisActionRecSend']['code_pays'] = $r['paysdelivery_dev'];
    }
    elseif($PC->rcvP['type'] == 'fax') {
	if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	    aiJeLeDroit('devis', 53);
	}
	else {
	    aiJeLeDroit('devis', 52);
	}
	if($r['nomdelivery_dev'] != '')
	    $_SESSION['DevisActionRecSend']['nom'] = $r['nomdelivery_dev'];
	else  $_SESSION['DevisActionRecSend']['nom'] = $r['civ_cont'].' '.$r['prenom_cont'].' '.$r['nom_cont'];
	if($r['fax_cont'] != '')
	    $_SESSION['DevisActionRecSend']['fax'] = $r['fax_cont'];
	elseif($r['fax_ent'] != '')
	    $_SESSION['DevisActionRecSend']['fax'] = $r['fax_ent'];
	else  $_SESSION['DevisActionRecSend']['fax'] = $r['fax_achat'];
    }
    else {
	if($result[1][0]['commercial_dev'] != $_SESSION['user']['id']) {
	    aiJeLeDroit('devis', 51);
	}
	else {
	    aiJeLeDroit('devis', 50);
	}
	if($r['maildelivery_dev'] != '')
	    $_SESSION['DevisActionRecSend']['email'] = $r['maildelivery_dev'];
	elseif($r['mail_cont'] != '')
	    $_SESSION['DevisActionRecSend']['email'] = $r['mail_cont'];
	else  $_SESSION['DevisActionRecSend']['email'] = $r['mail_achat'];
	$_SESSION['DevisActionRecSend']['titre'] = 'STARTX : Devis '.$r['id_dev'];
    }
    $in = array_merge($r,$_SESSION['DevisActionRecSend']);
    ?>
<root><go to="waDevisAction1"/>
    <title set="waDevisAction1">Enregistrer et envoyer</title>
    <part><destination mode="replace" zone="waDevisAction1" create="true"/>
        <data><![CDATA[ <?php echo devisView::actionRecordSend1($in); ?> ]]></data>
    </part>
</root>
    <?php
}
elseif (($PC->rcvG['action'] == 'doVoir')or
	($PC->rcvG['action'] == 'doRec')or
	($PC->rcvG['action'] == 'doSend')or
	($PC->rcvG['action'] == 'doRecsend')) {
    $ooGnose = new devisGnose();
    if ($PC->rcvG['action'] == 'doRecsend') $PC->rcvP = array_merge($_SESSION['DevisActionRecSend'],$PC->rcvP);
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $info = new devisModel();
    $devis = $info->getDataFromID($PC->rcvG['id_dev']);
    $dev = $devis[1][0];
    $Doc = $ooGnose->DevisGenerateDocument($dev['id_dev'],$PC->rcvP['OutputExt'],$PC->rcvP['Cannevas']);
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
    <part><destination mode="append" zone="formDevisDoVoirResponse"/>
        <data><![CDATA[ <?php echo $fileAdd; ?> ]]></data>
    </part>
</root>
	<?php
    }
    if (($PC->rcvG['action'] == 'doRec')or($PC->rcvG['action'] == 'doRecsend')) {
	if (trim($PC->rcvP['message']) == "")
	    $PC->rcvP['message'] = "Changement du devis ".$dev['id_dev'];
	$save = $ooGnose->DevisSaveDocInGnose($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc,$dev['id_aff'],$PC->rcvP['message']);

	$actuTitre = 'Re-enregistrement du devis '.$dev['id_dev'].' pour le client '.$dev['nom_ent'];
	$actuDesc = 'Le devis '.$dev['id_dev'].' vient d\'être re-enregistré. Il a une valeur de '.formatCurencyDisplay($dev['sommeHT_dev']).' HT. Commentaire de l\'enregistrement : '.$PC->rcvP['message'];
	if ($dev['status_dev'] < 3) {
	    $actuTitre = 'Enregistrement du devis '.$dev['id_dev'].' pour le client '.$dev['nom_ent'];
	    $actuDesc = 'Le devis '.$dev['id_dev'].' vient d\'être enregistré. Il a une valeur de '.formatCurencyDisplay($dev['sommeHT_dev']).' HT. Commentaire de l\'enregistrement : '.$PC->rcvP['message'];
	}

	if($dev['status_aff'] < 3) {
	    $inActualiteRec['status_aff'] = '3';
	    $bddtmp->makeRequeteUpdate('affaire','id_aff',$dev['id_aff'],array('status_aff'=>$inActualiteRec['status_aff']));
	    $bddtmp->process();
	}
	if($dev['status_dev'] < 3) {
	    $inActualiteRec['status_dev'] = '3';
	    $bddtmp->makeRequeteUpdate('devis','id_dev',$dev['id_dev'],array('status_dev'=>$inActualiteRec['status_dev']));
	    $bddtmp->process();
	}



	if ($PC->rcvG['action'] == 'doRec') {	?>
<root><go to="waDevisAction"/>
    <part><destination mode="replace" zone="waDevisAction"/>
        <data><![CDATA[ <div class="msg">Votre document est maintenant enregistré dans le module ZunoGed.</div> ]]></data>
    </part>
</root>
	    <?php
	}
    }
    if (($PC->rcvG['action'] == 'doRecsend')or($PC->rcvG['action'] == 'doSend')) {
	if ($PC->rcvG['action'] == 'doRecsend')
	    $PC->rcvP = array_merge($_SESSION['DevisActionRecSend'],$PC->rcvP);

	if ($PC->rcvP['type'] == 'courrier')
	    $control = sendControl::sendCourrier($PC->rcvP);
	elseif ($PC->rcvP['type'] == 'fax')
	    $control = sendControl::sendFax($PC->rcvP);
	else  $control = sendControl::sendMail($PC->rcvP);

	if($control[0]) {
	    $PC->rcvP['dir_aff'] = $dev['dir_aff'];
	    $PC->rcvP['partie'] = "devis";
	    $PC->rcvP['typeE'] = $PC->rcvP['type'];
	    $PC->rcvP['cc'] = $PC->rcvP['emailcc'];
	    $PC->rcvP['mail'] = $PC->rcvP['email'];
	    $PC->rcvP['from'] = $_SESSION['user']['mail'];
	    $PC->rcvP['expediteur'] = $_SESSION['user']['fullnom'];
	    $PC->rcvP['destinataire'] = $PC->rcvP['nom'];
	    $PC->rcvP['fichier'] = substr($PC->rcvP['file'], strripos($PC->rcvP['file'], "/")+1);
	    $PC->rcvP['id'] = $_SESSION['DevisActionRecSend']['id_dev'];
	    $PC->rcvP['channel'] = 'iphone';
	    $PC->rcvP['sujet'] = $PC->rcvP['titre'];
	    $send = new Sender($PC->rcvP);
	    $result = $send->send($_SESSION['user']['mail']);
	    $dest = ($PC->rcvP['destinataire'] == '') ? $PC->rcvP['mail'] : $PC->rcvP['destinataire'];
	    if($result[0]) {
		$actuPrefix = ($dev['status_dev'] >= 3) ? 'Re-e' : 'E';
		if($dev['status_aff'] < 4) {
		    $inActualiteEnvoi['status_aff'] = '4';
		    $bddtmp->makeRequeteUpdate('affaire','id_aff',$dev['id_aff'],array('status_aff'=>$inActualiteEnvoi['status_aff']));
		    $bddtmp->process();
		}
		if($dev['status_dev'] < 4) {
		    $inActualiteEnvoi['status_dev'] = '4';
		    $bddtmp->makeRequeteUpdate('devis','id_dev',$dev['id_dev'],array('status_dev'=>$inActualiteEnvoi['status_dev']));
		    $bddtmp->process();
		}
		unset($_SESSION['DevisActionRecSend']);
		?>
<root><go to="waDevisAction"/>
    <part><destination mode="replace" zone="waDevisAction" />
        <data><![CDATA[ <div class="msg">Votre document vient d'être envoyé par <?php echo $PC->rcvP['type']; ?>. </div> ]]></data>
    </part>
</root>
		<?php

	    }
	    else {
		echo $result[1];
		exit;
	    }
	}
	else {	?>
<root><go to="waDevisAction1"/>
    <title set="waDevisAction1">Envoi de <?php echo $PC->rcvP['type']; ?></title>
    <part><destination mode="replace" zone="waDevisAction1" create="true"/>
        <data><![CDATA[ <?php echo devisView::actionRecordSend1($PC->rcvP,$control[2],$control[1]); ?> ]]></data>
    </part>
</root>
	    <?php
	}
    }
}

elseif($PC->rcvG['action'] == 'inputDevis') {
    $_SESSION['searchDevisLayerBackTo'] = $PC->rcvG['__source'];
    $_SESSION['searchDevisTagsBackTo'] = $PC->rcvG['tag'];

    ?>
<root><go to="waDevisInputAjax"/>
    <title set="waDevisInputAjax">Choix d'un devis</title>
    <part><destination mode="replace" zone="waDevisInputAjax" create="true"/>
        <data><![CDATA[ <?php echo ZunoLayerDevis::headerFormSearchDev(); ?> ]]></data>
    </part>
    <script><![CDATA[ new dynAjax('formSearchDevisInput',3,'formSearchDevisajax'); ]]></script>
</root>
    <?php
}

elseif($PC->rcvG['action'] == 'inputDevisResult') {
    $_SESSION['searchDevisQuery'] = $PC->rcvP['search'];
    $info = new devisModel();
    $from = 0;
    $limit = $_SESSION['user']['config']['LenghtSearchDevis'];
    $devis = $info->getDataForSearchCommande($PC->rcvP['search'],$limit,$from);
    if($devis[0]) {
	$out .= '<ul id="searchResultInputDevisUl">';
	$out .= devisView::searchInputResultRow($devis[1],$_SESSION['searchDevisLayerBackTo'],$_SESSION['searchDevisTagsBackTo']);
	if(count($devis[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultInputDevisMore'.$from.'"><a href="Devis.php?action=inputDevisContinue&from='.$limit.'" rev="async">Plus de résultats</a></li>';
	$out .= '</ul>';
    }
    else	$out .= '<h2 class="Contact">Devis (0)</h2>';

    ?>
<root>
    <part>
        <destination mode="replace" zone="SearchDevisResultAsync"/>
        <data><![CDATA[
            <div style="height:25px;width:200px"> </div>
            <div class="iList">
		    <?php echo $out; ?>
            </div>
			]]></data>
    </part>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'inputDevisContinue') {
    $info = new devisModel();
    $zoneTo = $outJs = $out = '';
    $limit = $_SESSION['user']['config']['LenghtSearchDevis'];
    $from = ($PC->rcvG['from'] != '') ? $PC->rcvG['from'] : 0;
    $result = $info->getDataForSearchCommande($_SESSION['searchDevisQuery'],$limit,$from);
    if($result[0]) {
	$out .= devisView::searchInputResultRow($result[1],$_SESSION['searchDevisLayerBackTo'],$_SESSION['searchDevisTagsBackTo']);
	if(count($result[1]) >= $limit)
	    $out .= '<li class="iMore" id="searchResultInputDevisMore'.$from.'"><a href="Devis.php?action=inputDevisContinue&from='.($from+$limit).'" rev="async">Plus de résultats</a></li>';
	$outJs = 'removeElementFromDom(\'searchResultInputDevisMore'.($from-$limit).'\')';
	$zoneTo = 'searchResultInputDevisUl';
    }

    if($zoneTo != '') {	?>
<root>
    <part>
        <destination mode="append" zone="<?php echo $zoneTo; ?>"/>
        <data><![CDATA[ <?php echo $out; ?> ]]></data>
        <script><![CDATA[ <?php echo $outJs; ?> ]]></script>
    </part>
</root>
	<?php
    }
}
elseif($PC->rcvG['action'] == 'addDevisExpress') {
    aiJeLeDroit('devis', 20);
    ?>
<root><go to="waDevisAddExpress"/>
    <title set="waDevisAddExpress"><?php echo "Devis Express"; ?></title>
    <part><destination mode="replace" zone="waDevisAddExpress" create="true"/>
        <data><![CDATA[ <?php echo devisView::addExpress(); ?> ]]></data>
    </part>
</root>
    <?php

}
elseif($PC->rcvG['action'] == 'entrepriseDevisExpress') {
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
    $outJS .= 'doModifEntrepriseDevisExpress("Devis");';
    $outJS .= ' </script>';

    ?>
<root>

    <part><destination mode="append" zone="waDevisAddExpress" create="false"/>
        <data><![CDATA[ <?php echo '<script id="scriptDevisExpress">'.$outJS.'</script>';?> ]]></data>
    </part>
</root>
    <?php

}
elseif($PC->rcvG['action'] == 'addDevisExpressSuite') {
    $control = new devisControl();
    $result = $control->controlExpress1($PC->rcvP);
    if(!$result[0]) {
	?>
<root><go to="waDevisAddExpress"/>
    <title set="waDevisAddExpress"><?php echo "Devis Express"; ?></title>
    <part><destination mode="replace" zone="waDevisAddExpress" create="true"/>
        <data><![CDATA[ <?php echo devisView::addExpress($PC->rcvP, $result[2], $result[1]); ?> ]]></data>
    </part>
</root>
	<?php
    }
    else {
	if(($PC->rcvP['entreprise_dev'] == NULL || $PC->rcvP['entreprise_dev'] == '') && ($PC->rcvP['nomdelivery_dev'] != NULL || $PC->rcvP['nomdelivery_dev'] != '')) {
	    $entreprise = new contactEntrepriseModel();
	    $data['nom_ent'] = $PC->rcvP['nomdelivery_dev'];
	    $data['type_ent'] = '1';
	    $data['add1_ent'] = $PC->rcvP['adressedelivery_dev'];
	    $data['add2_ent'] = $PC->rcvP['adresse1delivery_dev'];
	    $data['cp_ent'] = $PC->rcvP['cpdelivery_dev'];
	    $data['ville_ent'] = $PC->rcvP['villedelivery_dev'];
	    $data['pays_ent'] = $PC->rcvP['paysdelivery_dev'];
	    $data['tauxTVA_ent'] = $PC->rcvP['tva_dev'];
	    $data['tel_ent'] = $PC->rcvP['tel_ent'];
	    $result = $entreprise->insert($data);
	    $_SESSION['devisExpress']['entreprise_dev'] = $entreprise->getLastId();
	    $_SESSION['devisExpress']['contact_dev'] = '0';

	    if(($PC->rcvP['listeContact'] == NULL || $PC->rcvP['listeContact'] == '') && ($PC->rcvP['contact_dev'] != NULL || $PC->rcvP['contact_dev'] != '')) {
		$contact = new contactParticulierModel();
		$data['entreprise_cont'] = $entreprise->getLastId();
		$data['nom_cont'] = $PC->rcvP['contact_dev'];
		$data['civ_cont'] = $PC->rcvP['civ_cont'];
		$data['prenom_cont'] = $PC->rcvP['prenom_cont'];
		$data['add1_cont'] = $PC->rcvP['adressedelivery_dev'];
		$data['add2_cont'] = $PC->rcvP['adresse1delivery_dev'];
		$data['cp_cont'] = $PC->rcvP['cpdelivery_dev'];
		$data['ville_cont'] = $PC->rcvP['villedelivery_dev'];
		$data['pays_cont'] = $PC->rcvP['paysdelivery_dev'];
		$data['mail_cont'] = $PC->rcvP['maildelivery_dev'];
		$data['tel_cont'] = $PC->rcvP['tel_cont'];
		$result = $contact->insert($data);
		$_SESSION['devisExpress']['contact_dev'] = $contact->getLastId();
		$_SESSION['devisExpress']['nomdelivery_dev'] = $PC->rcvP['nomdelivery_dev'];
	    }
	}
	elseif(($PC->rcvP['listeContact'] == NULL || $PC->rcvP['listeContact'] == '') && ($PC->rcvP['contact_dev'] != NULL || $PC->rcvP['contact_dev'] != '')) {
	    $contact = new contactParticulierModel();
	    $data['nom_cont'] = $PC->rcvP['contact_dev'];
	    $data['civ_cont'] = $PC->rcvP['civ_cont'];
	    $data['prenom_cont'] = $PC->rcvP['prenom_cont'];
	    $data['add1_cont'] = $PC->rcvP['adressedelivery_dev'];
	    $data['add2_cont'] = $PC->rcvP['adresse1delivery_dev'];
	    $data['cp_cont'] = $PC->rcvP['cpdelivery_dev'];
	    $data['ville_cont'] = $PC->rcvP['villedelivery_dev'];
	    $data['pays_cont'] = $PC->rcvP['paysdelivery_dev'];
	    $data['tel_cont'] = $PC->rcvP['tel_cont'];
	    $data['mail_cont'] = $PC->rcvP['maildelivery_dev'];
	    $data['entreprise_cont'] = $PC->rcvP['entreprise_dev'];
	    $result = $contact->insert($data);
	    $_SESSION['devisExpress']['entreprise_dev'] = $PC->rcvP['entreprise_dev'];
	    $_SESSION['devisExpress']['contact_dev'] = $contact->getLastId();
	    $_SESSION['devisExpress']['nomdelivery_dev'] = substr($PC->rcvP['nomdelivery_dev'],0,strlen($PC->rcvP['nomdelivery_dev'])-8);
	}
	else {
	    $_SESSION['devisExpress']['entreprise_dev'] = $PC->rcvP['entreprise_dev'];
	    $_SESSION['devisExpress']['contact_dev'] = ($PC->rcvP['listeContact'] != NULL) ? $PC->rcvP['listeContact'] : '0';
	    $_SESSION['devisExpress']['nomdelivery_dev'] = substr($PC->rcvP['nomdelivery_dev'],0,strlen($PC->rcvP['nomdelivery_dev'])-8);
	}

	$_SESSION['devisExpress']['maildelivery_dev'] = $PC->rcvP['maildelivery_dev'];
	$_SESSION['devisExpress']['adressedelivery_dev'] = $PC->rcvP['adressedelivery_dev'];
	$_SESSION['devisExpress']['adresse1delivery_dev'] = $PC->rcvP['adresse1delivery_dev'];
	$_SESSION['devisExpress']['cpdelivery_dev'] = $PC->rcvP['cpdelivery_dev'];
	$_SESSION['devisExpress']['villedelivery_dev'] = $PC->rcvP['villedelivery_dev'];
	$_SESSION['devisExpress']['paysdelivery_dev'] = $PC->rcvP['paysdelivery_dev'];
	$_SESSION['devisExpress']['nb_prod'] = 1;
	$_SESSION['devisExpress']['tva_dev'] = $PC->rcvP['tva_dev'];
	$_SESSION['devisExpress']['titre_dev'] = 'Devis';
	?>
<root><go to="waDevisAddExpressSuite"/>
    <title set="waDevisAddExpressSuite"><?php echo "Devis Express"; ?></title>
    <part><destination mode="replace" zone="waDevisAddExpressSuite" create="true"/>
        <data><![CDATA[ <?php echo devisView::addExpressSuite(); ?> ]]></data>
    </part>
</root>
	<?php
    }
}
elseif($PC->rcvG['action'] == 'doAddDevisExpress') {
    $info = new devisModel();
    $control = new devisControl();
    $result = $control->controlExpress2($PC->rcvP);
    if(!$result[0]) {
	?>
<root><go to="waDevisAddExpressSuite"/>
    <title set="waDevisAddExpressSuite"><?php echo "Devis ExpressSuite"; ?></title>
    <part><destination mode="replace" zone="waDevisAddExpressSuite" create="true"/>
        <data><![CDATA[ <?php echo devisView::addExpressSuite($PC->rcvP, $result[2], $result[1]); ?> ]]></data>
    </part>
</root>
	<?php
    }
    else {
	$temp = 0;
	$_SESSION['devisExpress']['status_dev'] = '1';
	$_SESSION['devisExpress']['commercial_dev'] = $_SESSION['user']['id'];
	$numero = 0;
	for ($nombre = 1; $nombre <= $_SESSION['devisExpress']['nb_prod']; $nombre++) {
	    if($PC->rcvP['id_produitDevisExpress'.$nombre] != NULL || $PC->rcvP['id_produitDevisExpress'.$nombre] != '') {
		$temp += $PC->rcvP['quantite'.$nombre]*$PC->rcvP['prix'.$nombre]*(1-$PC->rcvP['remise'.$nombre]/100);
		$prod[$numero]['id_produit']=$PC->rcvP['id_produitDevisExpress'.$nombre];
		$prod[$numero]['desc']=$PC->rcvP['desc'.$nombre];
		$prod[$numero]['quantite']=$PC->rcvP['quantite'.$nombre];
		$prod[$numero]['prix']=$PC->rcvP['prix'.$nombre];
		$prod[$numero]['remise']=$PC->rcvP['remise'.$nombre];
		if($PC->rcvP['memorize'.'id_produitDevisExpress'.$nombre] == 'ok') {
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
	$_SESSION['devisExpress']['sommeHT_dev'] = $temp;
	$result = $info->insert($_SESSION['devisExpress'], 'express', $prod);
	if($result[0]) {
	    viewFiche($_SESSION['devisExpress']['id'], 'devis');
	}
	else {
	    ?>
<root><go to="waDevisAdd"/>
    <title set="waDevisAdd"><?php echo "Nouv. devis";?></title>
    <part><destination mode="replace" zone="waDevisAdd" create="true"/>
        <data><![CDATA[ <?php echo "Erreur lors de la connexion à la base de données"; ?> ]]></data>
    </part>
</root>
	    <?php
	}//Problème que j'explique à l'utilisateur.
    }
}
elseif($PC->rcvG['action'] == 'tri_montant') {
    viewTri('devis', 'montant', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triMontantMore') {
    viewTri('devis', 'montant', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'tri_creation') {
    viewTri('devis', 'creation', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triCreationMore') {
    viewTri('devis', 'creation', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'tri_entreprise') {
    viewTri('devis', 'entreprise', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triEntrepriseMore') {
    viewTri('devis', 'entreprise', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'tri_contact') {
    viewTri('devis', 'contact', 'reset', 0, 0, 'iphone', true);
}
elseif($PC->rcvG['action'] == 'triContactMore') {
    viewTri('devis', 'contact', 'suite', $PC->rcvG['from'], $PC->rcvG['total'], 'iphone', true);
}
elseif($PC->rcvG['action'] == 'voirStats') {
    aiJeLeDroit('devis', 45);
    $datas = getStats('devis', 'oui');
    placementAffichage('Statistiques', "waStatsDevis", 'devisView::afficherStats', array($datas), '', 'replace');
}
ob_end_flush();
?>
