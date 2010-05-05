<?php

include_once('GeneralControl.inc');
/**
 * Classe gérant le controle des données pour les produits d'une commande
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class produitControl {
    /**
     * Méthode statique de controle simple de la validité du contenu d'un formulaire produit
     * @param array $value Les données à vérifier
     * @return array Le résultat de la vérification.
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
	return array($out, $mess);
    }
}



/**
 * Classe gérant le control des données pour un commande.
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class commandeControl {
    /**
     * Méthode statique de controle avant modification d'une commande
     * @param array $value Les données à controler
     * @return array Le résultat du controle.
     */
    static function control($value = array()) {
	if(!array_key_exists('commercial_cmd',$value) or $value['commercial_cmd'] == '') {
	    $mess.= 'merci de fournir un nom de commercial<br/>';
	    $fields[] = 'commercial_cmd';
	}
	if(!array_key_exists('contact_cmd',$value) or $value['contact_cmd'] == '') {
	    $mess.= 'merci de fournir un contact<br/>';
	    $fields[] = 'contact_cmd';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }

    /**
     * Méthode statique de controle avant ajout d'une commande
     * @param array $value Les données à controler
     * @return array Le résultat du controle
     */
    static function controlAdd($value = array()) {
	if(!array_key_exists('commercial_cmd',$value) or $value['commercial_cmd'] == '') {
	    $mess.= 'merci de fournir un nom de commercial<br/>';
	    $fields[] = 'commercial_cmd';
	}
	if(!array_key_exists('devis_cmd',$value) or $value['devis_cmd'] == '') {
	    $mess.= 'merci d\'indiquer le devis lié<br/>';
	    $fields[] = 'devis_cmd';
	}
	if(!array_key_exists('contact_cmd',$value) or $value['contact_cmd'] == '') {
	    $mess.= 'merci de fournir un contact<br/>';
	    $fields[] = 'contact_cmd';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }
}
?>
