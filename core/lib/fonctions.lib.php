<?php
/*
 * Created on 15 avr. 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/

/**
 * Fonction qui appelle la fonction du viewer et la place dans
 * la bonne div pour le framework.
 */
function placementAffichage ($titre = 'Zuno', $div, $fonction, $parametres = array(), $script = '', $type = 'replace') {
    if(strpos($fonction,'::') !== false)
	$fonction = explode('::',$fonction,2);


    if($type == 'replace') {
	?>
<root><go to="<?php echo $div; ?>"/>
    <title set="<?php echo $div; ?>"><?php echo $titre; ?></title>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo call_user_func_array($fonction,$parametres); ?> ]]></data>
    </part><script><?php echo $script; ?></script>
</root>
	<?php
    }
    elseif($type == 'append') {
	?>
<root>
    <part><destination mode="append" zone="<?php echo $div; ?>" />
        <data><![CDATA[ <?php echo call_user_func_array($fonction,$parametres); ?> ]]></data>
    </part><script><?php echo $script; ?></script>
</root>
	<?php
    }
    elseif($type == 'tri') {
	?>
<root>
    <part>
        <destination mode="replace" zone="<?php echo $div; ?>" />
        <data><![CDATA[	<?php echo call_user_func_array($fonction,$parametres); ?> ]]></data>
    </part><script><?php echo $script; ?></script>
</root>
	<?php
    }
}

/**
 * Fonction générale qui s'occupe de récupérer les infos nécessaires pour les envoyer
 * par la suite aux fonctions d'affichage.
 */
function viewFiche($id, $partie, $mode = '', $fournisseur = 'non', $channel = 'iphone', $droits = true, $mess = '') {
// on memorise la visite
    zunoHistoriqueVisite($id,$partie);

    if($partie == 'avoir')
	$nomclass = 'factureModel';
    else $nomclass = $partie.'Model';
    $info = new $nomclass();
    //Génération du nom de la classe puis appel de la classe en question.

    if($partie == 'produit' && $fournisseur != 'fourn') {
	$result = $info->getProduitByID($id);
	$fourn = $info->getFournisseurByProduitID($id);
    }
    elseif($partie == 'commande') {
	$result = $info->getDataFromID($id);
	$fourn = $info->getFournisseurFromID($id);
    }
    elseif($partie == 'produit' && $fournisseur == 'fourn') {
	$result = $info->getFournisseurByID($id);
	$prod = $info->getProduitByFournisseurID($id);
    }
    else {
	$result = $info->getDataFromID($id);
    }
    //Récupération des informations nécessaires pour l'affichage.
    if($droits) {
	switch($partie) {
	    case 'affaire':
		$underscore = 'commercial_aff';
		break;
	    case 'devis':
		$underscore = 'commercial_dev';
		break;
	    case 'commande':
		$underscore = 'commercial_cmd';
		break;
	    case 'facture':
	    case 'avoir':
		$underscore = 'commercial_fact';
		break;
	    case 'actualite':
		$underscore = 'user';
		break;
	    default:
		$underscore = 'non';
		break;
	}
	if($underscore != 'non') {
	    if($result[1][0][$underscore] != $_SESSION['user']['id']) {
		aiJeLeDroit($partie, 10, $channel);
	    }
	}
	if($partie == 'produit') {
	    aiJeLeDroit($partie, 10, $channel);
	}
    }
    //Dans le cas d'une vérification de droits, on vérifie que l'utilisateur a les droits nécessaires pour visualiser la fiche.
    if($channel == 'iphone') {
	//Si on est sur l'iphone, on prépare  l'affichage.
	if($partie == 'contactEntreprise' or ($partie == 'produit' && $fournisseur == 'fourn')) {
	    $partie = 'contactEntreprise';
	    $partie2 = 'Contact';
	    $plus = 'Ent';
	    $parametres = array($result[1][0], $fournisseur, $prod[1][0]['counter']);
	}
	elseif($partie == 'contactParticulier') {
	    $partie2 = 'Contact';
	    $plus = 'Part';
	    $parametres = array($result[1][0]);
	}
	elseif($partie == 'produit') {
	    $partie2=ucfirst($partie);
	    $plus = '';
	    $parametres = array($result[1][0], $fourn[1]);
	}
	elseif($partie =='avoir') {
	    $partie2 = 'Facture';
	    $plus = '';
	    $parametres = array($result[1][0], $mode, $fourn[1]);
	}
	else {
	    $partie2=ucfirst($partie);
	    $plus = '';
	    if($partie != 'commande') {
		$parametres = array($result[1][0], $mode);
	    }
	    else {
		$parametres = array($result[1][0], $mode, $fourn[1]);
	    }
	}
	$div = "wa".$partie2."Fiche".$plus;
	$fonction = $partie.'View::view';
	if($result[0]) {
	    placementAffichage ($id, $div, $fonction, $parametres);
	}
	else { ?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Cette fiche n'<strong>existe plus</strong><br/></div></div> ]]></data>
    </part>
</root>
	    <?php
	}
    }
    elseif($channel == 'web') {
	$datas['data'] = $result[1][0];
	if($partie != 'produit' and $partie != 'factureFournisseur') {
	    $datas['pays'] =  $info->getPays();
	    $datas['user'] = $info->getUser();
	}
	if($partie == 'commande') {
	    $datas['produits'] = $info->getAllFournisseursFromID($id);
	    $datas['actu'] = $info->getActualites($id);
	    $datas['LF'] = $info->getAllFournisseursFromID($id, $datas['data']['status_cmd']);
	}
	elseif($partie =='contactEntreprise') {
	    $datas['type'] = $info->getTypesEnt();
	    $datas['groupe'] = $info->getGroupesEnt($id);
	    $datas['activite'] = $info->getActivitesEnt();
	    $datas['projet'] = $info->getProjets($id);
	    $datas['affaire'] = $info->getAffaires($id);
	    $datas['appel'] = $info->getAppels($id);
	}
	elseif($partie == 'contactParticulier') {
	    $datas['fonctions'] = $info->getFonction();
	    $datas['projet'] = $info->getProjets($id);
	    $datas['affaire'] = $info->getAffaires($id);
	    $datas['appel'] = $info->getAppels($id);
	    $datas['data']['contact'] = $info->getCollegue($id);
	}
	elseif($partie == 'produit' and $fournisseur != 'fourn') {
	    $datas['dureeR'] = $info->getRenews();
	    $datas['famille'] = $info->getAllFamille();
	    $datas['fourn'] = $info->getFournisseurByProduitID($id);
	    $datas['fournEx'] = $info->getExFournisseurByProduitID($id);
	}
	elseif($partie == 'produit' and $fournisseur == 'fourn') {
	    $partie = 'Fournisseur';
	    $datas['produits'] = $prod[1][0]['counter'];
	    $datas['prod'] = $info->getProduitsByFournisseurID($id);
	    $datas['contacts'] = $info->getContactsPotentiels($id);
	}
	elseif($partie == 'affaire') {
	    $datas['related'] = $info->getRelatedRessourcesForAffaire($id);
	    $datas['actu'] = $info->getActusByID($id);
	    $datas['type'] = $info->getTypesProj();
	}
	elseif($partie == 'factureFournisseur') {
	    $datas['status'] = $info->getStatutFactFourn();
	    $datas['mode'] = $info->getModeReglement();
	    $datas['periode'] = $info->getRenews();
	    $datas['ren'] = ($datas['data']['ren_factfourn'] != "") ? $info->getRenouvellement($datas['data']['ren_factfourn']) : array();
	}
	elseif($partie == 'facture' or $partie == 'avoir') {
	    $result = $info->getProduitsFromID($id);
	    $datas['produits'] = $result[1];
	    $datas['periode'] = $info->getRenews();
	    $datas['status'] = $info->getStatutFact();
	    $datas['modereglement'] = $info->getModeReglement();
	    $datas['condireglement'] = $info->getCondReglement();
	    $datas['last'] = $info->getLastId();
	    $datas['ren'] = ($datas['data']['ren_fact'] != "") ? $info->getRenouvellement($datas['data']['ren_fact']) : array();
	}
	elseif($partie == 'devis') {
	    $result = $info->getProduitsFromID($id);
	    $datas['produits'] = $result[1];
	    $datas['periode'] = $info->getRenews();
	    $datas['status'] = $info->getStatutDevis();
	    $datas['ren'] = ($datas['data']['ren_dev'] != "") ? $info->getRenouvellement($datas['data']['ren_dev']) : array();
	}
	elseif($partie == 'pontComptable' or $partie == 'journalBanque') {

	}
	else {
	    $result = $info->getProduitsFromID($id);
	    $datas['produits'] = $result[1];
	}
	$partie = ($partie == 'avoir') ? 'facture' : $partie;
	$nomclass = $partie.'View';
	$aff = new $nomclass();
	return $aff->view($datas, $mode, $mess);
    }
}

/**
 * Fonction générale qui s'occupe de récupérer les infos nécessaires pour
 * l'affichage des résultats d'une recherche.
 */
function viewResults($recherche = '', $partie, $mode = 'reset', $channel = 'iphone', $droits = true) {
    if($mode == 'reset') {
	$_SESSION['user']['LastLetterSearch'] = '~#~|~';
	$_SESSION['user']['annee'] = '~~#~~';
	$from = 0;
	if($partie == 'contactEntreprise') {
	    $limit = $_SESSION['user']['config']['LenghtSearchContactEnt'];
	}
	elseif($partie == 'contactParticulier') {
	    $limit = $_SESSION['user']['config']['LenghtSearchContactPart'];
	}
	else {
	    $limit = $_SESSION['user']['config']['LenghtSearch'.ucfirst($partie)];
	}
	$_SESSION[$partie.'Search'] = trim($recherche);
	$_SESSION['total'] = 0;
    }
    elseif($mode == 'suite') {
	$from = $_GET['from'];
	if($partie == 'contactEntreprise') {
	    $limit = $_SESSION['user']['config']['LenghtSearchContactEnt'];
	}
	elseif($partie == 'contactParticulier') {
	    $limit = $_SESSION['user']['config']['LenghtSearchContactPart'];
	}
	else {
	    $limit = $_SESSION['user']['config']['LenghtSearch'.ucfirst($partie)];
	}
    }
    if($droits) {
	switch($partie) {
	    case 'affaire':
		$underscore = 'commercial_aff';
		break;
	    case 'devis':
		$underscore = 'commercial_dev';
		break;
	    case 'commande':
		$underscore = 'commercial_cmd';
		break;
	    case 'facture':
		$underscore = 'commercial_fact';
		break;
	    case 'actualite':
		$underscore = 'user';
		break;
	    default:
		$underscore = 'non';
		break;
	}
	if($underscore != 'non') {
	    if(verifDroits($partie,10)) {
		$plus = '';
	    }
	    else {
		$plus = " AND $underscore = '".$_SESSION['user']['id']."' ";
	    }
	}
    }
    $nomclass = $partie.'Model';
    $info = new $nomclass();
    if($partie == 'facture') {
	$result = $info->getDataForSearch($_SESSION[$partie.'Search'],$limit,$from, $_SESSION['type_fact'], $plus);
    }
    elseif($partie == 'produit' && $_SESSION['rechercheproduit'] == 'produit') {
	$result = $info->getDataForSearchProduit($_SESSION[$partie.'Search'],$limit,$from, $plus);
    }
    elseif($partie == 'produit' && $_SESSION['rechercheproduit'] == 'fournisseur') {
	$result = $info->getDataForSearchFournisseur($_SESSION[$partie.'Search'],$limit,$from, $plus);
    }
    else {
	$result = $info->getDataForSearch($_SESSION[$partie.'Search'],$limit,$from, $plus);
    }

    if($partie == 'contactEntreprise' or $partie == 'contactParticulier') {
	$div = "waContactSearchResult";
    }
    else {
	$div = "wa".ucfirst($partie)."SearchResult";
    }

    if($partie == 'produit' && $_SESSION['rechercheproduit'] == 'fournisseur') {
	$fonction = 'contactEntrepriseView::searchResult';
    }
    else {
	$fonction = $partie.'View::searchResult';
    }

    if($channel == 'iphone' && $mode == 'reset') {
	if($partie == 'facture') {
	    $total = $info->getDataForSearch($_SESSION[$partie.'Search'],'ALL',$from, $_SESSION['type_fact'], $plus);
	}
	elseif($partie == 'produit' && $_SESSION['rechercheproduit'] == 'produit') {
	    $total = $info->getDataForSearchProduit($_SESSION[$partie.'Search'],'ALL',$from, $plus);
	}
	elseif($partie == 'produit' && $_SESSION['rechercheproduit'] == 'fournisseur') {
	    $total = $info->getDataForSearchFournisseur($_SESSION[$partie.'Search'],'ALL',$from, $plus);
	}
	else {
	    $total = $info->getDataForSearch($_SESSION[$partie.'Search'],'ALL',$from, $plus);
	}

	$total = $total[1][0]["counter"];
	$_SESSION['total'] = $total;
	if($partie == 'produit' && $_SESSION['rechercheproduit'] == 'fournisseur') {
	    $parametres = array($result[1],$from, $limit, $total, '', 'fourn');
	}
	else {
	    $parametres = array($result[1],$from, $limit, $total);
	}

	if($result[0]) {
	    placementAffichage('Résultats ('.$total.')', $div, $fonction, $parametres);
	}
	else { ?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <div class="iBlock"><div class="err">Aucun résultat<br/></div></div> ]]></data>
    </part>
</root>
	    <?php
	}
    }
    elseif($channel == 'iphone' && $mode == 'suite') {
	if($result[0]) {
	    if($partie == 'produit' && $_SESSION['rechercheproduit'] == 'fournisseur') {
		$partie2='Fourn';
	    }
	    elseif($partie == 'contactEntreprise') {
		$partie2 = 'Entreprise';
	    }
	    elseif($partie == 'contactParticulier') {
		$partie2 = 'Part';
	    }
	    else {
		$partie2 = $partie;
	    }
	    $parametres = array($result[1],$from, $limit, $_SESSION['total']);
	    $script= 'removeElementFromDom(\'searchResult'.ucfirst($partie2).'More'.($from-$limit).'\')';
	    placementAffichage('', $div, $fonction, $parametres, $script, 'append');
	}
	else { ?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo '<div class="iBlock"><div class="err">Aucun autre résultat<br/></div></div>'; ?> ]]></data>
    </part>
</root>
	    <?php
	}
    }

}

/**
 * Fonction générale qui s'occuper de récupérer les infos nécessaires pour
 * l'affichage des produits d'un devis/commande/facture.
 */
function viewProduitsLies ($id, $partie, $channel = 'iphone') {
    $nomclass = $partie.'Model';
    $fonctionstatus = 'getStatus'.$partie;
    $info = new $nomclass();
    $result = $info->getProduitsFromID($id);
    $status = $info->$fonctionstatus($id);
    switch($partie) {
	case 'devis' :
	    $status = ($status[1][0]['status_dev'] <= 4) ? '' : 'valide';
	    $parametres = array($result[1], $id, '', $status);
	    break;
	case 'commande' :
	    $tva = $status[1][0]['tva_cmd'];
	    $status = ($status[1][0]['status_cmd'] <= 6) ? '' : 'valide';
	    $parametres = array($result[1], $id, $tva, $status);
	    break;
	case 'facture' :
	    $tva = $status[1][0]['tauxTVA_fact'];
	    $type = $status[1][0]['type_fact'];
	    $status = ($status[1][0]['status_fact'] < 4) ? '' : 'valide';
	    $parametres = array($result[1], $id, $tva, $status, $type);
	    break;
	default :
	    $parametres = array($result[1], $id, '', '');
	    break;
    }

    //On récupère les informations relatives au produits.
    if($channel == 'iphone') {
	$div = 'wa'.ucfirst($partie).'Produits';
	$fonction = $partie.'View::produits';

	if($result[0]) {
	    placementAffichage ('Produits', $div, $fonction, $parametres, '', 'replace');
	}
	else {  ?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo '<div class="iBlock"><div class="err">Problème de connexion avec la Base de données<br/></div></div>'; ?> ]]></data>
    </part>
</root>
	    <?php
	}//Rien ne va plus, on le précise.
    }
}

/**
 * Fonction générale qui s'occupe de récupérer les infos nécessaires pour
 * l'affichage d'un formulaire de modif ou d'ajout.
 */
function viewFormulaire($id = '', $partie, $mode = 'add', $channel = 'iphone', $droits = true, $type = '') {
// on memorise la visite
    zunoHistoriqueVisite($id,$partie);

    $nomclass = $partie.'Model';
    $info = new $nomclass();
    if($mode == 'add') {
	if($droits) {
	    aiJeLeDroit($partie, 20, $channel);
	}
	if($partie == 'contactEntreprise' and $type == 'contactlie') {
	    $div = 'waContactPartAdd';
	    $fonction = 'contactParticulierView::formAddBis';
	    $ent = $info->getDataFromID($id);
	}
	elseif($partie == 'contactEntreprise') {
	    $div = 'waContactEntAdd';
	    $fonction = $partie.'View::formAdd';
	}
	elseif($partie == 'contactParticulier') {
	    $div = 'waContactPartAdd';
	    $fonction = $partie.'View::formAdd';
	}
	elseif($partie == 'produit' and $type == 'fourn') {
	    $fonction = 'produitView::addNewFourn';
	    $div = "waFournAdd";
	}
	else {
	    $div = 'wa'.ucfirst($partie).'Add';
	    $fonction = $partie.'View::add';
	}

	switch($partie) {
	    case 'commande' :
		$produits = $info->getProduitsDevis($id);
		$parametres = array($produits[1]);
		break;
	    case 'facture' :
		$produits = $info->getProduitsFacture($id);
		$parametres = array($produits[1]);
		break;
	    case 'produit' :
		if($type == 'fourn') {
		    $result = $info->getEntrepriseFournisseur();
		    $parametres = array($result[1]);
		}
		break;
	    default :
		$parametres = ($type == 'contactlie') ? array(array(), array(), '', $ent[1][0]) : array();
		break;
	}
	if($channel == 'iphone') {
	    placementAffichage ('Nouveau', $div, $fonction, $parametres,  '', 'replace');
	}
    }
    elseif($mode == 'modif') {
	if($partie == 'produit' and $type == 'fourn') {
	    $result = $info->getFournisseurByID($id);
	    $cont = new contactEntrepriseModel();
	    $liste = $cont->getDataFromID($result[1][0]['id_ent']);
	}
	elseif($partie == 'produit') {
	    $result = $info->getProduitByID($id);
	    $fourn = $info->getFournisseurByProduitID($id);
	}
	else {
	    $result = $info->getDataFromID($id);
	}
	if($droits) {
	    switch($partie) {
		case 'affaire':
		    $nom = 'commercial_aff';
		    $underscore = '_aff';
		    break;
		case 'devis':
		    $nom = 'commercial_dev';
		    $underscore = '_dev';
		    break;
		case 'commande':
		    $nom = 'commercial_cmd';
		    $underscore = '_cmd';
		    break;
		case 'facture':
		    $nom = 'commercial_fact';
		    $underscore = '_fact';
		    break;
		case 'produit':
		    $nom = '';
		    $underscore = '_prod';
		case 'actualite':
		    $nom = 'user';
		    break;
		case 'contactEntreprise' :
		    $nom = '';
		    break;
		case 'contactParticulier' :
		    $nom = '';
		    break;
		default:
		    $nom = 'non';
		    break;
	    }
	    if($nom != 'non') {
		if($result[1][0][$nom] != $_SESSION['user']['id'] && $nom != '') {
		    aiJeLeDroit($partie, 17, $channel);
		}
		else {
		    aiJeLeDroit($partie, 15, $channel);
		}
	    }
	}
	if($partie == 'affaire' or $partie == 'devis' or $partie == 'commande' or $partie == 'facture') {
	    $control = $partie.'SuppControl';
	    $result[1][0]['supprimable'] = generalControl::$control($id);
	}
	if($channel == 'iphone') {
	    if($partie == 'contactEntreprise') {
		$div = 'waContactEntModif';
	    }
	    elseif($partie == 'contactParticulier') {
		$div = 'waContactPartModif';
	    }
	    else {
		$div = 'wa'.ucfirst($partie).'Modif';
	    }
	    $fonction = $partie.'View::modif';
	    if($partie == 'facture') {
		$parametres = array($result[1][0], array(), '', $id, $type);
	    }
	    elseif($partie == 'produit' and $type == 'fourn') {
		$fonction = 'produitView::modifFourn';
		$parametres = array($liste[1][0]['contact'], $result[1][0]);
		$underscore = '_fourn';
	    }
	    elseif($partie == 'produit') {
		$parametres = array($result[1][0], array(), '', $id, $fourn['1']);
	    }
	    else {
		$parametres = array($result[1][0], array(), '', $id);
	    }
	    if($result[0]) {
		placementAffichage($result[1][0]['id'.$underscore], $div, $fonction, $parametres, '', 'replace');
	    }
	    else {  ?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo '<div class="iBlock"><div class="err">Cette fiche n\'existe plus.<br/></div></div>'; ?> ]]></data>
    </part>
</root>
		<?php
	    }//Rien ne va plus, on le précise.
	}
    }
    elseif($mode == 'cloner') {
	$result = $info->getDataFromID($id);
	if($droits) {
	    switch($partie) {
		case 'affaire':
		    $nom = 'commercial_aff';
		    $underscore = '_aff';
		    break;
		case 'devis':
		    $nom = 'commercial_dev';
		    $underscore = '_dev';
		    break;
		case 'commande':
		    $nom = 'commercial_cmd';
		    $underscore = '_cmd';
		    break;
		case 'facture':
		    $nom = 'commercial_fact';
		    $underscore = '_fact';
		    break;
		case 'produit':
		    $nom = 'non';
		    $underscore = '_prod';
		    break;
		default:
		    $nom = 'non';
		    break;
	    }
	    if($nom != 'non') {
		if($result[1][0][$nom] != $_SESSION['user']['id'] && $nom != '') {
		    aiJeLeDroit($partie, 35, $channel);
		}
		else {
		    aiJeLeDroit($partie, 25, $channel);
		}
	    }
	    else {
		aiJeLeDroit($partie, 25, $channel);
	    }
	}
	$div = "wa".ucfirst($partie)."Cloner";
	if($channel == 'iphone' && $result[0]) {
	    $fonction = $partie.'View::cloner';
	    $parametres = array($result[1][0]);
	    placementAffichage ('Cloner', $div, $fonction, $parametres, '', 'replace');
	}
	elseif($channel == 'iphone') {  ?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo '<div class="iBlock"><div class="err">Cette fiche n\'existe plus.<br/></div></div>'; ?> ]]></data>
    </part>
</root>
	    <?php
	}//Rien ne va plus, on le précise.
    }
    elseif($mode == 'supp') {
	$result = $info->getDataFromID($id);

	if($droits) {
	    switch($partie) {
		case 'affaire':
		    $nom = 'commercial_aff';
		    $underscore = '_aff';
		    break;
		case 'devis':
		    $nom = 'commercial_dev';
		    $underscore = '_dev';
		    break;
		case 'commande':
		    $nom = 'commercial_cmd';
		    $underscore = '_cmd';
		    break;
		case 'facture':
		    $nom = 'commercial_fact';
		    $underscore = '_fact';
		    break;
		case 'produit':
		    $nom = 'non';
		    $underscore = '_prod';
		    break;
		default:
		    $nom = 'non';
		    break;
	    }
	    if($nom != 'non') {
		if($result[1][0][$nom] != $_SESSION['user']['id'] && $nom != '') {
		    aiJeLeDroit($partie, 40, $channel);
		}
		else {
		    aiJeLeDroit($partie, 30, $channel);
		}
	    }
	    else {
		aiJeLeDroit($partie, 30, $channel);
	    }
	}
	if($result[0] and $channel == 'iphone') {
	    $div = "wa".ucfirst($partie)."Delete";
	    $titre = 'Suppression';
	    $fonction = $partie.'View::delete';
	    $parametres = ($type == '') ? array($result[1][0]) : array($result[1][0], $type);
	    if($partie == 'contactEntreprise') {
		$temp = $info->getTotalParticuliersFromID($id);
		$fourn = $info->getTotalFournisseurFromID($id);
		$parametres = array($result[1][0], $temp, $fourn);
		$div = "waContactDeleteEnt";
	    }
	    if($partie == 'contactParticulier') {
		$fourn = $info->getTotalFournisseurFromID($id);
		$parametres = array($result[1][0], $fourn);
		$div = "waContactDeletePart";
	    }
	    placementAffichage ($titre, $div, $fonction, $parametres, '', 'replace');
	}
	elseif($channel == 'iphone') {  ?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo '<div class="iBlock"><div class="err">Cette fiche n\'existe plus.<br/></div></div>'; ?> ]]></data>
    </part>
</root>
	    <?php
	}//Rien ne va plus, on le précise.
    }

}

/**
 * Fonction générale qui s'occupe d'afficher des infos liés
 * à une fiche quelque soit la fiche.
 */
function viewRessourcesLies($id, $partie, $souspartie ='', $channel = 'iphone', $droits = true) {
    if($droits) {
	switch($souspartie) {
	    case 'affaire':
		$underscore = '_aff';
		break;
	    case 'devis':
		$underscore = '_dev';
		break;
	    case 'commande':
		$underscore = '_cmd';
		break;
	    case 'facture':
		$underscore = '_fact';
		break;
	    default:
		$underscore = 'non';
		break;
	}
	if($partie == 'actualite') {
	    $nom = 'user';
	}
	else {
	    $nom = 'commercial'.$underscore;
	}
	if($nom != 'non') {
	    if(verifDroits($partie,10)) {
		$plus = '';
	    }
	    else {
		$plus = " AND $nom = '".$_SESSION['user']['id']."' ";
	    }
	}
    }
    $nomclass = $partie.'Model';
    $info = new $nomclass();
    $nomfonction = 'getData4'.ucfirst($souspartie);
    $result = $info->$nomfonction($id, $plus);
    if($partie == 'contactEntreprise') {
	$partie2 = 'contactEnt';
    }
    elseif($partie == 'contactParticulier') {
	$partie2 = 'contactPart';
    }
    else {
	$partie2 = $partie;
    }
    $div = 'wa'.ucfirst($partie2).ucfirst($souspartie);
    if($result[0]) {
	$fonction = $partie.'View::'.$partie.'ResultRow';
	placementAffichage ( ucfirst($souspartie), $div, $fonction, array($result[1]), '', 'replace');
    }
    else { 	?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo '<div class="iBlock"><div class="err">Aucune liste à afficher.<br/></div></div>'; ?> ]]></data>
    </part>
</root>
	<?php
    }
}

/**
 * Fonction générale qui s'occupe d'afficher les formulaires
 * de ressources liées
 */
function viewFormulaireRessourcesLies($id, $partie, $idRessource = '', $nombreRessource = 'one', $channel = 'iphone', $type = '', $tva = '19.6') {
    $class = $partie.'Model';
    $info = new $class();
    //	$id_prod = urldecode($PC->rcvG['id_prod']);
    //	$id_prod = urlencode($PC->rcvG['id_prod']);

    if($nombreRessource == 'one') {
	$result = $info->getProduitsFromID($id, '1', $idRessource);
    }
    else {
	$result = $info->getProduitsFromID($id);
    }

    //On récupère les données pour afficher les formulaires de modif
    if($result[0] and $channel == 'iphone') {
	$div = "waModifProduits".ucfirst($partie);
	$titre = 'Produit';
	$fonction = $partie.'View::modifProduits';
	switch($partie) {
	    case 'devis' :
		$parametres = array($result[1], $id);
		break;
	    case 'commande' :
		$parametres = array($result[1], $id, $tva);
		break;
	    case 'facture' :
		$parametres = array($result[1], $id, $tva, $type);
		break;
	}
	placementAffichage($titre, $div, $fonction, $parametres, '', 'replace');

    }
}
/**
 * Fonction générale qui s'occupe d'afficher les listes de tri dans les menus des parties.
 */
function viewTri($partie, $souspartie, $mode = 'reset', $from = 0, $total = 0, $channel = 'iphone', $droits = true) {
    $nomclass = $partie.'Model';
    $info = new $nomclass();
    $nomfonction = 'getDataBy'.ucfirst($souspartie);
    if($droits) {
	switch($partie) {
	    case 'affaire':
		$underscore = 'commercial_aff';
		break;
	    case 'devis':
		$underscore = 'commercial_dev';
		break;
	    case 'commande':
		$underscore = 'commercial_cmd';
		break;
	    case 'facture':
		$underscore = 'commercial_fact';
		break;
	    case 'actualite':
		$underscore = 'user';
		break;
	    default:
		$underscore = 'non';
		break;
	}
	if($underscore != 'non') {
	    if(verifDroits($partie,10)) {
		$plus = '';
	    }
	    else {
		$plus = " AND $underscore = '".$_SESSION['user']['id']."' ";
	    }
	}
    }
    if($mode == 'reset') {
	$_SESSION['user']['LastLetterSearch'] = '~#~|#';

	$total = $info->$nomfonction('total', 0, $plus);
	$total = $total[1][0]["counter"];
    }
    $limit = $_SESSION['user']['config']['LenghtSearch'.ucfirst($partie)];
    $result = $info->$nomfonction($limit, $from, $plus);
    $div = ucfirst($partie).'TriResultatAsync';
    $fonction = $partie.'View::tri_'.$souspartie;
    $parametres = array($result, $limit, $from, $total);
    if($result[0]) {
	if($mode == 'reset' && $channel == 'iphone') {
	    placementAffichage ('', $div, $fonction, $parametres, '', 'tri');
	}
	elseif($mode == 'suite' && $channel == 'iphone') {
	    $outJs = 'removeElementFromDom(\'tri'.ucfirst($souspartie).ucfirst($partie).'More'.($from-$limit).'\')';
	    placementAffichage ('', $div, $fonction, $parametres, $outJs, 'append');
	}
    }
    else { 	?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo '<div class="iBlock"><div class="err">Aucune liste à afficher.<br/></div></div>'; ?> ]]></data>
    </part>
</root>
	<?php
    }

}

/**
 * Fonction générale qui s'occupe de faire les insertions dans la BDD
 */
function insertBDD($partie, $data = array(), $source, $channel = 'iphone', $droits = true) {
    if($droits) {
	aiJeLeDroit($partie, 20,$channel);
    }
    switch($partie) {
	case 'contactParticulier' :
	    $bool = ($data['entreprise_cont'] != '') ? true: false ;
	    $control = contactControl::addParticulier($data, $bool);
	    $fonctionAff = 'contactParticulierView::formAdd';
	    $id = 'id_cont';
	    break;
	case 'contactEntreprise' :
	    $control = contactControl::addEntreprise($data);
	    $fonctionAff = 'contactEntrepriseView::formAdd';
	    $id = 'id_ent';
	    break;
    }

    if($control[0]) // si elles sont bonnes, on lance le model pour insertion
    {
	$class = $partie.'Model';
	$model  = new $class();
	$data = stripslashs($data);
	if($data['civ_cont'] == 'M')
	    $data['civ_cont'] = 'M.';
	$result = $model->insert($data);

	if($result[0]) // si l'insertion est bonne, on continue...
	{
	    if($partie == 'contactEntreprise' and $data['addCont'] == 'ok') {
		$ent = $model->getDataFromID($model->getLastId());
		$ent = $ent[1][0];
		placementAffichage ('Nouveau', 'waContactPartAdd', 'contactParticulierView::formAddBis', array(array(), array(), '', $ent), '', 'replace');
	    }
	    else {
		if($channel == 'iphone')
		    viewFiche($model->getLastId(), $partie);
		else
		    echo viewFiche($model->getLastId(), $partie, $source, 'non', $channel, true, "Fiche créée");
	    }
	}
	else // Il y a eu un problème lors de l'ajout de la fiche, on reste sur l'interface d'ajout et on affiche un message
	{
	    $mess = 'Problème lors de l\'insertion de cette fiche<br/>';
	    if($channel == 'iphone')
		placementAffichage ('Nouveau', $source, $fonctionAff, array($data, array(), $mess), '_KK();', 'replace');
	    else {
		if($partie == 'contactParticulier') {
		    $info = new contactParticulierModel();
		    $datas['fonctions'] = $info->getFonction();
		    $datas['pays'] =  $info->getPays();
		    $datas['user'] = $info->getUser();
		    $datas['data'] = $data;
		    $view = new contactParticulierView();
		    $sortie = $view->creerContact($datas);

		}else {
		    $info = new contactEntrepriseModel();
		    $datas['pays'] =  $info->getPays();
		    $datas['user'] = $info->getUser();
		    $datas['type'] = $info->getTypesEnt();
		    $datas['groupe'] = $info->getGroupesEnt();
		    $datas['activite'] = $info->getActivitesEnt();
		    $datas['data'] = $data;
		    $view = new contactEntrepriseView();
		    echo $view->creerEntrepriseError($datas,array(false,$mess));
		}
	    }
	}
    }
    else // Il y a eu un problème lors du contrôle des données saisies, on reste sur l'interface d'ajout et on affiche un message
    {
	if($channel == 'iphone')
	    placementAffichage ('Nouveau', $source, $fonctionAff, array($data, $control[2], $control[1]), '_KK();', 'replace');
	else {
	    if($partie == 'contactParticulier') {
		$info = new contactParticulierModel();
		$datas['fonctions'] = $info->getFonction();
		$datas['pays'] =  $info->getPays();
		$datas['user'] = $info->getUser();
		$datas['data'] = $data;
		$view = new contactParticulierView();
		$sortie = $view->creerContact($datas);

	    }
	    else {
		$info = new contactEntrepriseModel();
		$datas['pays'] =  $info->getPays();
		$datas['user'] = $info->getUser();
		$datas['type'] = $info->getTypesEnt();
		$datas['groupe'] = $info->getGroupesEnt();
		$datas['activite'] = $info->getActivitesEnt();
		$datas['data'] = $data;
		$view = new contactEntrepriseView();
		echo $view->creerEntrepriseError($datas,$control);
	    }
	}
    }
}

/**
 * Fonction générale qui s'occupe de faire les updates dans la BDD
 */
function updateBDD($id, $partie, $data = array(), $source, $channel = 'iphone', $droits = true) {
    if($droits) {
	switch($partie) {
	    case 'affaire':
		$underscore = 'commercial_aff';
		break;
	    case 'devis':
		$underscore = 'commercial_dev';
		break;
	    case 'commande':
		$underscore = 'commercial_cmd';
		break;
	    case 'facture':
		$underscore = 'commercial_fact';
		break;
	    case 'actualite':
		$underscore = 'user';
		break;
	    default:
		$underscore = 'non';
		break;
	}
	if($underscore != 'non') {
	    if($data[$underscore] != $_SESSION['user']['id']) {
		aiJeLeDroit($partie, 17, $channel);
	    }
	    else {
		aiJeLeDroit($partie, 15, $channel);
	    }
	}
	else {
	    aiJeLeDroit('contact', 15, $channel);
	}
    }
    switch($partie) {
	case 'contactParticulier' :
	    $bool = ($data['entreprise_cont'] != '') ? true: false ;
	    $control = contactControl::addParticulier($data, $bool);
	    $fonctionAff = 'contactParticulierView::formAdd';
	    $underscore = '_cont';
	    break;
	case 'contactEntreprise' :
	    $control = contactControl::addEntreprise($data);
	    $fonctionAff = 'contactEntrepriseView::formAdd';
	    $underscore = '_ent';
	    break;
    }

    if($control[0]) // si elles sont bonnes, on lance le model pour modification
    {
	$class = $partie.'Model';
	$model  = new $class();
	$data = stripslashs($data);
	$result = $model->update($data,$id);
	if($result[0]) // si la modification est applicable, on continue...
	{
	    if($data['addCont'] == 'ok' and $partie == 'contactEntreprise') // si une demande d'ajout de contact a été formulé, on lance l'interface ajout contact
	    {
		$ent = $model->getDataFromID($id);
		$ent = $ent[1][0];
		if($channel == 'iphone')
		    placementAffichage ('Nouveau', 'waContactPartAdd', 'contactParticulierView::formAddBis', array(array(), array(), '', $ent), '', 'replace');

		else return viewFiche($id, $partie,$source,'non', $channel, true, 'Enregistré');
	    }
	    else // sinon, on affiche la fiche
	    {
		if($channel == 'iphone')
		    viewFiche($id, $partie);
		elseif($source == 'popup')
		    return $data['nom_ent'];
		else
		    return viewFiche($id, $partie,$source,'non', $channel, true, 'Enregistré');
	    }
	}
	else // Il y a eu un problème lors de l'ajout de la fiche, on reste sur l'interface d'ajout et on affiche un message
	{
	    $mess = 'Problème lors de la modification de cette fiche<br/>';
	    if($channel == 'iphone')
		placementAffichage ('Nouveau', $source, $fonctionAff, array($data, array(), $mess), '_KK();', 'replace');
	}
    }
    else // Il y a eu un problème lors du contrôle des données saisies, on reste sur l'interface de modification et on affiche un message
    {
	$data['id'.$underscore] = $id;
	if($channel == 'iphone')
	    placementAffichage ('Nouveau', $source, $fonctionAff, array($data, $control[2], $control[1]), '_KK();', 'replace');
	else {
	    return '<erreur>error</erreur><span class="important" style="text-align:center;">'.$control[1].'</span>';
	}
    }
}

/**
 * Fonction générale qui s'occupe de faire les suppression dans la BDD
 */
function suppBDD($id, $partie, $channel = 'iphone', $recursif = false) {
    aiJeLeDroit($partie, 30, $channel);
    $class = $partie.'Model';
    $info = new $class();
    $result = $info->delete($id, $recursif);
    switch($partie) {
	case 'contactEntreprise' :
	    $fonctionAff = 'contactEntrepriseView::delete';
	    $div = 'waContactDeleteEnt';
	    break;
	case 'contactParticulier' :
	    $fonctionAff = 'contactParticulierView::delete';
	    $div = 'waContactDeletePart';
	    break;
    }
    if($result[0] and $channel == 'iphone') {
	placementAffichage ('Suppression', $div, $fonctionAff, array(), '', 'replace');
    }
    else { 	?>
<root><go to="<?php echo $div; ?>"/>
    <part><destination mode="replace" zone="<?php echo $div; ?>" create="true"/>
        <data><![CDATA[ <?php echo '<div class="iBlock"><div class="err">Erreur à la suppression.<br/></div></div>'; ?> ]]></data>
    </part>
</root>
	<?php
    }
}

/**
 * Fonction générale qui s'occupe de mettre les blocks
 * dans les tris par montant
 */
function triMontant($value, $prix = '', $valeur = array(500, 2500, 10000, 25000, 100000)) {
    if($value >= $valeur[4] && $prix != 100000) {
	$prix = 100000;
	$out .= '</ul><h2>Supérieur à '.formatCurencyDisplay($valeur[4], 0).'</h2><ul>';
    }
    elseif($value >= $valeur[3] && $value < $valeur[4] && $prix != 25000) {
	$prix = 25000;
	$out .= '</ul><h2>Entre '.formatCurencyDisplay($valeur[3], 0).' et '.formatCurencyDisplay($valeur[4], 0).'</h2><ul>';
    }
    elseif($value >= $valeur[2] && $value < $valeur[3] && $prix != 10000) {
	$prix = 10000;
	$out .= '</ul><h2>Entre '.formatCurencyDisplay($valeur[2], 0).' et '.formatCurencyDisplay($valeur[3], 0).'</h2><ul>';
    }
    elseif($value >= $valeur[1] && $value < $valeur[2] && $prix != 2500) {
	$prix = 2500;
	$out .= '</ul><h2>Entre '.formatCurencyDisplay($valeur[1], 0).' et '.formatCurencyDisplay($valeur[2], 0).'</h2><ul>';
    }
    elseif($value >= $valeur[0] && $value < $valeur[1] && $prix != 500) {
	$prix = 500;
	$out .= '</ul><h2>Entre '.formatCurencyDisplay($valeur[0], 0).' et '.formatCurencyDisplay($valeur[1], 0).'</h2><ul>';
    }
    elseif($value < $valeur[0] && $prix != 10) {
	$prix = 10;
	$out .= '</ul><h2>Inférieur à '.formatCurencyDisplay($valeur[0], 0).'</h2><ul>';
    }
    return array($out, $prix);
}

/**
 * Fonction générale qui s'occupe de récupérer des données
 * de statistiques pour le tri et ou des stats'
 */
function getStats($partie, $tous = 'non', $type = '') {
    $class = $partie.'Model';
    $info = new $class();
    if($partie == 'facture' and $type != '') {
	$data = $info->getDataForStats(" WHERE type_fact = '$type' ");
    }
    elseif($partie == 'affaire' and $type == '') {
	$data = $info->getDataForStats();
	$nbaff = $data[0]['aff'];
	$nbreste = array(prepareNombreAffichage($data[1]['dev']/$data[0]['aff']), prepareNombreAffichage($data[2]['cmd']/$data[0]['aff']), prepareNombreAffichage($data[3]['fact']/$data[0]['aff']));
	$pxmoyens = array(prepareNombreAffichage($data[1]['pxdev']), prepareNombreAffichage($data[2]['pxcmd']), prepareNombreAffichage($data[3]['pxfact']));
	$sommes = array(prepareNombreAffichage($data[1]['sumdev']), prepareNombreAffichage($data[2]['sumcmd']), prepareNombreAffichage($data[3]['sumfact']));
	$mediane = $info->getMediane('devis', 'sommeHT_dev');
	$medianedev = $mediane[1][0]['MEDIANE'];
	$mediane = $info->getMediane('commande', 'sommeFHT_cmd');
	$medianecmd = $mediane[1][0]['MEDIANE'];
	$mediane = $info->getMediane('facture where type_fact="Facture" ', 'sommeHT_fact');
	$medianefact = $mediane[1][0]['MEDIANE'];
	$medianes = array(prepareNombreAffichage($medianedev), prepareNombreAffichage($medianecmd), prepareNombreAffichage($medianefact));
	return array($nbaff, $nbreste, $pxmoyens, $sommes, $medianes);
    }
    else {
	$data = $info->getDataForStats();
    }
    $N = $data[1][0]['N'];
    if($tous == 'oui') {
	$X = round($data[1][0]['X'],2);
	$variance = $data[1][0]['variance'];
	$mediane = $data['mediane'][1][0]['MEDIANE'];
	$somme = $data[1][0]['somme'];
	$ecartType = sqrt($variance);
	$coefVar = $ecartType/$X;
	$X = formatCurencyDisplay($X,2,'');
	$N = prepareNombreAffichage($N);
	$mediane = prepareNombreAffichage($mediane);
	$variance = prepareNombreAffichage($variance);
	$ecartType = prepareNombreAffichage($ecartType);
	$somme = prepareNombreAffichage($somme);
	$coefVar = prepareNombreAffichage($coefVar);
	return array($N, $X, $mediane, $variance, $ecartType, $somme, $coefVar);
    }
    else {
	$pas = round($N/6, 0);
	if($pas <= $N/6) {
	    $val1 = $pas;
	}
	else {
	    $val1 = $pas-1;
	}
	$val2 = $val1+$pas;
	$val3 = $val2+$pas;
	$val4 = $val3 + $pas;
	$val5 = $val4 + $pas;
	$data = $info->getValues(array($val1, $val2, $val3, $val4, $val5));
	return array(round($data[1][0]['val']), round($data[1][1]['val']), round($data[1][2]['val']), round($data[1][3]['val']), round($data[1][4]['val']));
    }
}
/**
 * Fonction qui met au carré
 */
function carre($val) {
    return $val*$val;
}
/**
 * fonction qui prépare un nombre à l'affichage
 * @param $nombre	Le nombre à formater correctement
 * @return le nombre formaté, 2 chiffres après la virgule, avec une virgule et pas un point
 */
function prepareNombreAffichage($nombre,$round = 2) {
    return number_format(round($nombre,$round),$round,',',' ');
}


/**
 * Fonction qui prépare un nombre pour être traité en php et en SQL
 * @param $nombre	Le nombre à traiter
 * @return float Le nombre prêt à être utilisé
 */
function prepareNombreTraitement($nombre) {
    $nombre = str_replace(",",".",$nombre);
    $nombre = str_replace(" ","", $nombre);
    return $nombre;
}

/**
 * Fonction qui prépare un numéro de téléphone pour être traité en php et bien stocké en SQL
 * @param $numero Le numéro à traiter
 * @return string le numéro traité
 */
function prepareTelTraitement($numero) {
    $numero = str_replace(" ", "", $numero);
    $numero = str_replace(".","", $numero);
    while(strpos($numero, "(") !== false and strrpos($numero, ")") !== false) {
	$debut = strpos($numero,"(");
	$fin = strrpos($numero, ")");
	$numero = (strlen($numero) == $fin) ? substr($numero, 0, $debut) : substr($numero, 0, $debut).substr($numero, $fin+1, strlen($numero));
    }
    $numero = str_replace("(","",$numero);
    $numero = str_replace(")","",$numero);
    return $numero;
}

/**
 * Fonction qui prépare un numéro de téléphone pour être affiché
 * @param $numero le numéro à traiter
 * @return string le numéro formaté
 */
function prepareTelAffichage($numero) {
    if(strlen($numero) == 10 and substr($numero,0,1) == 0 and substr($numero,1,1) == 8) {
	$numero = "0 ".substr($numero,1,3)." ".substr($numero,4,3)." ".substr($numero,7,3);
    }
    elseif(strlen($numero) == 10) {
	$numero = substr($numero,0,2)." ".substr($numero, 2,2)." ".substr($numero,4,2)." ".substr($numero,6,2)." ".substr($numero,8,2);
    }
    elseif(substr($numero,0,3) == "+33") {
	$k = (substr($numero,3,1) == "0") ? 4 : 3;
	$numero = substr($numero,0,3)." (0) ".substr($numero,$k,1)." ".substr($numero, $k+1,2)." ".substr($numero, $k+3,2)." ".substr($numero, $k+5,2)." ".substr($numero, $k+7,2);
    }
    elseif(strlen($numero) == 4) {
	$numero = substr($numero,0,2)." ".substr($numero,2,2);
    }
    else {
	$k = 0;
	$rendu = "";
	while($k < strlen($numero)) {
	    $rendu .= substr($numero, $k, 3)." ";
	    $k += 3;
	}
	$numero = $rendu;
    }
    return $numero;
}

/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function  zunoHistoriqueVisite($id,$partie,$limit = 10) {
    if(!array_key_exists('historiqueVisite',$_SESSION))
	$_SESSION['historiqueVisite'] = array();
    if(!array_key_exists($partie,$_SESSION['historiqueVisite']))
	$_SESSION['historiqueVisite'][$partie] = array();

    if(!in_array($id,$_SESSION['historiqueVisite'][$partie]))
	array_unshift($_SESSION['historiqueVisite'][$partie],$id);
    if(count($_SESSION['historiqueVisite'][$partie]) > $limit)
	array_pop($_SESSION['historiqueVisite'][$partie]);
//var_dump($_SESSION['historiqueVisite']);exit;
}

function dateF($format, $timestamp = '-1') {
    if($timestamp == -1)
	$timestamp = time();
    if(strpos($format, 'F')!== false or strpos($format, 'M') !== false) {
	$m = date('n', $timestamp);
	switch($m) {
	    case 1 :
		$f = 'Janvier';
		break;
	    case 2 :
		$f = 'Février';
		break;
	    case 3 :
		$f = 'Mars';
		break;
	    case 4 :
		$f = 'Avril';
		break;
	    case 5 :
		$f = 'Mai';
		break;
	    case 6 :
		$f = 'Juin';
		break;
	    case 7 :
		$f = 'Juillet';
		break;
	    case 8 :
		$f = 'Août';
		break;
	    case 9 :
		$f = 'Septembre';
		break;
	    case 10 :
		$f = 'Octobre';
		break;
	    case 11 :
		$f = 'Novembre';
		break;
	    case 12 :
		$f = 'Décembre';
		break;
	}
	$m = substr($f, 0, 3);
    }
    if(strpos($format, 'D') !== false or strpos($format, 'l') !== false) {
	$d = date('w', $timestamp);
	switch($d) {
	    case '0' :
		$l = 'Dimanche';
		break;
	    case '1' :
		$l = 'Lundi';
		break;
	    case '2' :
		$l = 'Mardi';
		break;
	    case '3' :
		$l = 'Mercredi';
		break;
	    case '4' :
		$l = 'Jeudi';
		break;
	    case '5' :
		$l = 'Vendredi';
		break;
	    case '6' :
		$l = 'Samedi';
		break;
	}
	$d = substr($l, 0, 3);
    }
    $out = "";
    $slash = false;
    for( $i = 0; $i< strlen($format); $i++) {
	$lettre = $format[$i];
	if($slash) {
	    $out .= $lettre;
	    $slash = false;
	    continue;
	}
	if($lettre == '\\') {
	    $slash = true;
	    continue;
	}
	switch($lettre) {
	    case 'D' :
		$out .= $d;
		break;
	    case 'l' :
		$out .= $l;
		break;
	    case 'F' :
		$out .= $f;
		break;
	    case 'M' :
		$out .= $m;
		break;
	    default :
		$out .= date($lettre, $timestamp);
		break;
	}
    }
    return $out;
}


/**
 * Format a number and return a string with a curency représentation
 * @param <number> $number
 * @param <int> $round
 * @param <string> $csymbol
 */
function formatCurencyDisplay($number,$round = 2,$csymbol = ' &euro;') {
    return prepareNombreAffichage($number,$round).$csymbol;
}

/**
 * Format a number and return a string with a curency représentation
 * @param <number> $number
 * @param <int> $round
 * @param <string> $sign
 */
function formatCurencyDatabase($number,$round = 2) {
    $n = round(prepareNombreTraitement($number),$round);
    return prepareNombreTraitement($n);
}

function afficherMessages() {
    $sortie = "";
    if(array_key_exists('message', $_SESSION) and
       is_array($_SESSION['message']) and
       array_key_exists(0, $_SESSION['message'])  and 
       is_array($_SESSION['message'][0]) and 
       array_key_exists('titre_mess', $_SESSION['message'][0]) and 
       $_SESSION['message'][0]['titre_mess'] != '' and
       $_SESSION['message']['dejaVu'] != true) {
	$mess = '<div class="ZBox"><div class="body"><div class="content"><div class="block width50">';
	foreach($_SESSION['message'] as $v) {
	    if($v['contenu_mess'] != '') {
		$mess .= '<fieldset class="form">';
		$mess .= '<legend>'.$v['titre_mess'].'</legend>';
		$mess .= '<div class="row"><div class="label">Message : </div>';
		$mess .= '<div class="field">'.$v['contenu_mess'].'</div></div></fieldset>';
	    }
	}
	$mess .= '</div></div></div></div>';
	$sortie = '<script> var msg = \''.$mess.'\'; </script>';
	$sortie .= '<img alt="Image" style="display:none" src="img/zuno.png" onload="zuno.popup.doOpen(msg, \'Messages\',\'430\');" onerror="zuno.popup.doOpen(msg, \'Messages\',\'430\');" />';
	$_SESSION['message']['dejaVu'] = true;
    }
    return $sortie;
}
?>