<?php

include_once('GeneralControl.inc');

/**
 * Classe gérant le controle des données pour les produits.
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class produitControl {
    /**
     * Méthode de controle de sélection d'un produit pour un devis
     * @param array $value Les données à vérifier
     * @return array Le résultat du controle
     */
    static function control($value = array()) {
	$out = 1;
	$mess = '';
	if($value['id_produit'] == NULL || $value['id_produit'] == '') {
	    $out = 0;
	    $mess='Aucun produit sélectionné<br />';
	}
	if($value['remise'] > 100 || $value['remise'] < 0) {
	    $out = 0;
	    $mess.='Valeur entrée de remise incohérente<br />';
	}
	if($value['prix'] < 0 || $value['prix'] == NULL ) {
	    $out = 0;
	    $mess.='Prix négatif ou non renseigné<br />';
	}
	if(!is_numeric($value['quantite'])) {
	    $out = 0;
	    $mess.='Veuillez entrer un nombre et non des lettres.<br />';
	}
	return array($out, $mess);
    }

    /**
     * Méthode de controle d'ajout d'un produit dans la base
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlajout($value = array()) {
	$mess = '';
	if(!array_key_exists('id_prod', $value) or $value['id_prod'] == null or $value['id_prod'] == '') {
	    $mess .= 'Merci de donner une référence pour ce produit<br />';
	    $fields[] = 'id_prod';
	}
	else {
	    $conn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $conn->makeRequeteFree("Select id_prod from produit where id_prod = '".$value["id_prod"]."' ");
	    $result = $conn->process2();
	    if(array_key_exists('0', $result['1'])) {
		$mess .= 'Cette référence existe déjà<br />';
		$fields[] = 'id_prod';
	    }
	}
	if(!array_key_exists('nom_prod', $value) or $value['nom_prod'] == null or $value['id_prod'] == '') {
	    $mess .='Merci de donner un nom pour ce produit<br />';
	    $fields[] = 'nom_prod';
	}
	if(!array_key_exists('description_prod', $value) or $value['description_prod'] == null or $value['description_prod'] == '') {
	    $mess .= 'Merci de fournir une description pour ce produit<br />';
	    $fields[] = 'description_prod';
	}
	if((!array_key_exists('prix_prod', $value) or $value['prix_prod'] == null or $value['prix_prod'] == '') and (!array_key_exists('id_fourn', $value) or $value['id_fourn'] == null or $value['id_fourn'] == '')) {
	    $mess .='Merci d\'indiquer un prix ou un fournisseur pour ce produit';
	    $fields[] = 'prix_prod';
	}
	elseif((!array_key_exists('remiseF', $value) or $value['remiseF'] == null or $value['remiseF'] == '') and array_key_exists('id_fourn', $value)) {
	    $mess .= 'Merci d\'indiquer une remise fournisseur pour ce produit';
	    $fields[] = 'remiseF';
	}
	elseif((!array_key_exists('prixF', $value) or $value['prixF'] == null or $value['prixF'] == '') and array_key_exists('id_fourn', $value)) {
	    $mess .= 'Merci de préciser un prix fournisseur pour ce produit';
	    $fields[] = 'prixF';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }

    /**
     * Méthode de controle de modification d'un produit dans la base
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlModif($value = array()) {
	$mess = '';
	if(!array_key_exists('id_prod', $value) or $value['id_prod'] == null or $value['id_prod'] == '') {
	    $mess .= 'Merci de donner une référence pour ce produit<br />';
	    $fields[] = 'id_prod';
	}
	elseif($value['id_prod'] != $value['idProduit']) {
	    $conn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $conn->makeRequeteFree("Select id_prod from produit where id_prod = '".$value["id_prod"]."' ");
	    $result = $conn->process2();
	    if(array_key_exists('0', $result['1'])) {
		$mess .= 'Cette référence existe déjà<br />';
		$fields[] = 'id_prod';
	    }
	}
	if(!array_key_exists('nom_prod', $value) or $value['nom_prod'] == null or $value['id_prod'] == '') {
	    $mess .='Merci de donner un nom pour ce produit<br />';
	    $fields[] = 'nom_prod';
	}
	if(!array_key_exists('description_prod', $value) or $value['description_prod'] == null or $value['description_prod'] == '') {
	    $mess .= 'Merci de fournir une description pour ce produit<br />';
	    $fields[] = 'description_prod';
	}
	if((!array_key_exists('prix_prod', $value) or $value['prix_prod'] == null or $value['prix_prod'] == '') and (!array_key_exists('id_fourn', $value) or $value['id_fourn'] == null or $value['id_fourn'] == '')) {
	    $mess .='Merci d\'indiquer un prix ou un fournisseur pour ce produit';
	    $fields[] = 'prix_prod';
	}
	elseif((!array_key_exists('remiseF', $value) or $value['remiseF'] == null or $value['remiseF'] == '') and array_key_exists('id_fourn', $value)) {
	    $mess .= 'Merci d\'indiquer une remise fournisseur pour ce produit';
	    $fields[] = 'remiseF';
	}
	elseif((!array_key_exists('prixF', $value) or $value['prixF'] == null or $value['prixF'] == '') and array_key_exists('id_fourn', $value)) {
	    $mess .= 'Merci de préciser un prix fournisseur pour ce produit';
	    $fields[] = 'prixF';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }
}


/**
 * Classe gérant le control des données pour un devis.
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class devisControl {

    /**
     * Méthode de controle des devis avant modification
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function control($value = array()) {
	if(!array_key_exists('commercial_dev',$value) or $value['commercial_dev'] == '') {
	    $mess.= 'merci de fournir un nom de commercial<br/>';
	    $fields[] = 'commercial_dev';
	}
	if(!array_key_exists('contact_dev',$value) or $value['contact_dev'] == '') {
	    $mess.= 'merci de fournir un contact<br/>';
	    $fields[] = 'contact_dev';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }

    /**
     * Méthode de controle des devis avant ajout depuis l'interface iphone
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlAdd($value = array()) {
	if(!array_key_exists('commercial_dev',$value) or $value['commercial_dev'] == '') {
	    $mess.= 'merci de fournir un nom de commercial<br/>';
	    $fields[] = 'commercial_dev';
	}
	if(!array_key_exists('affaire_dev',$value) or $value['affaire_dev'] == '') {
	    $mess.= 'merci d\'indiquer l\'affaire liée<br/>';
	    $fields[] = 'affaire_dev';
	}
	if(!array_key_exists('contact_dev',$value) or $value['contact_dev'] == '') {
	    $mess.= 'merci de fournir un contact<br/>';
	    $fields[] = 'contact_dev';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }

    /**
     * Méthode de controle des devis avant ajout depuis l'interface web
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlAddWeb($value = array()) {
	if((!array_key_exists('affaire_dev',$value) or $value['affaire_dev'] == '') and (!array_key_exists('entreprise_dev', $value) or $value['entreprise_dev'] == '')) {
	    $mess.= 'Merci d\'indiquer l\'affaire liée<br/>';
	    $fields[] = 'affaire_dev';
	}
	if(!array_key_exists('contact_dev',$value) or $value['contact_dev'] == '') {
	    $mess.= 'Merci de fournir un contact<br/>';
	    $fields[] = 'contact_dev';
	}
	if($value['adressedelivery_dev'] == '' || $value['adressedelivery_dev'] == ' ' || $value['adressedelivery_dev'] == NULL) {
	    $mess.= 'Merci de fournir une adresse<br />';
	    $fields[] = 'adressedelivery_dev';
	}
	if(($value['cpdelivery_dev'] == '' || $value['cpdelivery_dev'] == ' ' || $value['cpdelivery_dev'] == NULL || generalControl::codePostalControl($value['cpdelivery_dev']) == '0') && $value['paysdelivery_dev'] == '1' ) {
	    $mess.= 'Merci de fournir un code postal<br />';
	    $fields[] = 'cpdelivery_dev';
	}
	if($value['villedelivery_dev'] == '' || $value['villedelivery_dev'] == ' ' || $value['villedelivery_dev'] == NULL) {
	    $mess.= 'Merci de fournir une ville<br />';
	    $fields[] = 'villedelivery_dev';
	}

	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }

    /**
     * Méthode de controle des devis avant ajout depuis formulaire express
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlExpress1($value = array()) {
	if($value['nomdelivery_dev'] == '' || $value['nomdelivery_dev'] == ' ' || $value['nomdelivery_dev'] == NULL) {
	    $mess.= 'Merci de fournir un nom d\'entreprise<br />';
	    $fields[] = 'nomdelivery_dev';
	}
	if($value['adressedelivery_dev'] == '' || $value['adressedelivery_dev'] == ' ' || $value['adressedelivery_dev'] == NULL) {
	    $mess.= 'Merci de fournir une adresse<br />';
	    $fields[] = 'adressedelivery_dev';
	}
	if(($value['cpdelivery_dev'] == '' || $value['cpdelivery_dev'] == ' ' || $value['cpdelivery_dev'] == NULL || generalControl::codePostalControl($value['cpdelivery_dev']) == '0') && $value['paysdelivery_dev'] == '1' ) {
	    $mess.= 'Merci de fournir un code postal<br />';
	    $fields[] = 'cpdelivery_dev';
	}
	if($value['villedelivery_dev'] == '' || $value['villedelivery_dev'] == ' ' || $value['villedelivery_dev'] == NULL) {
	    $mess.= 'Merci de fournir une ville<br />';
	    $fields[] = 'villedelivery_dev';
	}
	if($value['maildelivery_dev'] == '' || $value['maildelivery_dev'] == ' ' || $value['maildelivery_dev'] == NULL || generalControl::mailControl($value['maildelivery_dev']) == '0') {
	    $mess.= 'Merci de fournir un mail<br />';
	    $fields[] = 'maildelivery_dev';
	}
	if(($value['listeContact'] == '' || $value['listeContact'] == ' ' || $value['listeContact'] == NULL) && ($value['contact_dev'] == '' || $value['contact_dev'] == ' ' || $value['contact_dev'] == null)) {
	    $mess.= 'Merci de fournir un contact<br />';
	    $fields[] = 'contact_dev';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }

    /**
     * Méthode de controle des devis avant ajout depuis formulaire express partie 2
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlExpress2($value = array()) {
	for ($nombre = 1; $nombre <= $_SESSION['devisExpress']['nb_prod']; $nombre++) {
	    if($value['id_produitDevisExpress'.$nombre] != NULL || $value['id_produitDevisExpress'.$nombre] != '') {
		if(!is_numeric($value['quantite'.$nombre]) || $value['quantite'.$nombre] < 1) {
		    $messq = 'Merci de fournir une quantité<br />';
		    $fields[] = 'quantite'.$nombre;
		}
		if(!is_numeric($value['prix'.$nombre]) || $value['prix'.$nombre] < 1) {
		    $messp = 'Merci de fournir un prix unitaire<br />';
		    $fields[] = 'prix'.$nombre;
		}
		if(!is_numeric($value['remise'.$nombre]) || $value['remise'.$nombre] < 0 || $value['remise'.$nombre] > 100) {
		    $messr = 'Merci de fournir une remise<br />';
		    $fields[] = 'remise'.$nombre;
		}
	    }
	}
	$mess = $messq.$messp.$messr;
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }
}
?>
