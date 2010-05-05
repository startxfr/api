<?php

include_once('GeneralControl.inc');
/**
 * Classe servant à vérifier la conformité d'un ajout d'actualité
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class actualiteControl {
    /**
     * Méthode statique qui effectue le controle et retourne le résultat
     * avec si nécessaire un message à afficher.
     * @param array $value Les données à controler
     * @return array Indique le résultat du controle
     */
    static function generalControl($value) {
	$mess = '';
	$fields = array();
	if(!array_key_exists('titre',$value) or $value['titre'] == '') {
	    $mess.= 'merci de fournir un nom pour cette actualité<br/>';
	    $fields[] = 'titre';
	}
	if(!array_key_exists('desc',$value) or $value['desc'] == '') {
	    $mess.= 'merci de fournir un contenu à cette actualité<br/>';
	    $fields[] = 'desc';
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }
}

?>