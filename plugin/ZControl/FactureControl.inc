<?php

include_once('GeneralControl.inc');

/**
 * Classe de controle sur les factures
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class factureControl {
    /**
     * Méthode de controle avant ajout d'une facture via formulaire express
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlExpress1($value = array()) {
	if($value['nomdelivery_fact'] == '' || $value['nomdelivery_fact'] == ' ' || $value['nomdelivery_fact'] == NULL) {
	    $mess.= 'Merci de fournir un nom d\'entreprise<br />';
	    $fields[] = 'nomdelivery_fact';
	}
	if($value['adressedelivery_fact'] == '' || $value['adressedelivery_fact'] == ' ' || $value['adressedelivery_fact'] == NULL) {
	    $mess.= 'Merci de fournir une adresse<br />';
	    $fields[] = 'adressedelivery_fact';
	}
	if(($value['cpdelivery_fact'] == '' || $value['cpdelivery_fact'] == ' ' || $value['cpdelivery_fact'] == NULL || generalControl::codePostalControl($value['cpdelivery_fact']) == '0') && $value['paysdelivery_fact'] == '1' ) {
	    $mess.= 'Merci de fournir un code postal<br />';
	    $fields[] = 'cpdelivery_fact';
	}
	if($value['villedelivery_fact'] == '' || $value['villedelivery_fact'] == ' ' || $value['villedelivery_fact'] == NULL) {
	    $mess.= 'Merci de fournir une ville<br />';
	    $fields[] = 'villedelivery_fact';
	}
	if($value['maildelivery_fact'] == '' || $value['maildelivery_fact'] == ' ' || $value['maildelivery_fact'] == NULL || generalControl::mailControl($value['maildelivery_fact']) == '0') {
	    $mess.= 'Merci de fournir un mail<br />';
	    $fields[] = 'maildelivery_fact';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }

    /**
     * Méthode de controle avant ajout d'une facture via formulaire express partie 2
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlExpress2($value = array()) {
	for ($nombre = 1; $nombre <= $_SESSION['factureExpress']['nb_prod']; $nombre++) {
	    if($value['id_produitFactureExpress'.$nombre] != NULL || $value['id_produitFactureExpress'.$nombre] != '') {
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

    /**
     * Méthode de controle des factures avant ajout depuis l'interface web
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlAddWeb($value = array()) {
	if((!array_key_exists('commande_fact',$value) or $value['commande_fact'] == '') and ((!array_key_exists('entreprise_fact', $value) or $value['entreprise_fact'] == '') and (!array_key_exists('contact_achat_fact',$value) or $value['contact_achat_fact'] == ''))) {
	    $mess.= 'Merci d\'indiquer la commande liée<br/>';
	    $fields[] = 'affaire_dev';
	}
	if(!array_key_exists('contact_achat_fact',$value) or $value['contact_achat_fact'] == '') {
	    $mess.= 'Merci de fournir un contact<br/>';
	    $fields[] = 'contact_dev';
	}
/*	if($value['add1_fact'] == '' || $value['add1_fact'] == ' ' || $value['add1_fact'] == NULL) {
	    $mess.= 'Merci de fournir une adresse<br />';
	    $fields[] = 'add1_fact';
	}
	if(($value['cp_fact'] == '' || $value['cp_fact'] == ' ' || $value['cp_fact'] == NULL || generalControl::codePostalControl($value['cp_fact']) == '0') && $value['pays_fact'] == '1' ) {
	    $mess.= 'Merci de fournir un code postal<br />';
	    $fields[] = 'cp_fact';
	}
	if($value['ville_fact'] == '' || $value['ville_fact'] == ' ' || $value['ville_fact'] == NULL) {
	    $mess.= 'Merci de fournir une ville<br />';
	    $fields[] = 'ville_fact';
	}*/

	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }
}
?>
