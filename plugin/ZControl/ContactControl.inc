<?php

include_once('GeneralControl.inc');
/**
 * Classe de controle des informations des contacts
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class contactControl {
    /**
     * Méthode statique de controle avant l'ajout/modification d'un particulier
     * @param array $value Les données à controler
     * @param bool $entreprise Précise si le contact est lié à une entreprise ou non
     * @return array Le résultat du controle
     */
    static function addParticulier($value, $entreprise = false) {
	$mess = '';
	$fields = array();
	if(!is_array($value) or count($value) == 0)
	    $mess.= 'Aucune valeur saisie. Contrôle impossible. Veuillez re-commencer votre enregistrement.<br/>';
	else {
	    if(!array_key_exists('nom_cont',$value) or $value['nom_cont'] == '') {
		$mess.= 'Merci de fournir le nom de ce contact<br/>';
		$fields[] = 'nom_cont';
	    }
	    if(!array_key_exists('civ_cont',$value) or $value['civ_cont'] == '') {
		$mess.= 'Merci de fournir la civilité de ce contact<br/>';
		$fields[] = 'civ_cont';
	    }
	    if(!$entreprise) {
		if(!array_key_exists('mail_cont',$value) or $value['mail_cont'] == '' or !generalControl::mailControl($value['mail_cont'])) {
		    $mess.= 'Merci de fournir un e-mail valide pour ce contact<br/>';
		    $fields[] = 'mail_cont';
		}
		if(!array_key_exists('tel_cont',$value) or $value['tel_cont'] == '' or generalControl::telephoneControl($value['tel_cont']) == '0') {
		    $mess.= 'Merci de fournir un numéro de téléphone valide pour ce contact<br/>';
		    $fields[] = 'tel_cont';
		}
	    }
	    if($value['mob_cont'] != '' and !generalControl::telephoneControl($value['mob_cont'])) {
		$mess.='Merci de donner un numéro de portable valide<br/>';
		$field[] = 'mob_cont';
	    }
	    if ($entreprise) {
		if (($value['entreprise_cont']==0) or ($value['entreprise_cont'] == NULL )) {
		    $mess.='problème de lien avec l\'entreprise<br />';
		}
	    }
	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }

    /**
     * Méthode de controle avant d'ajouter / modifier une entreprise
     * @param array $value Les données à vérifier
     * @return array Le résultat du controle.
     */
    static function addEntreprise($value) {
	$mess = '';
	$fields = array();
	if(!is_array($value) or count($value) == 0)
	    $mess.= 'Aucune valeur saisie. Contrôle impossible. Veuillez re-commencer votre enregistrement.<br/>';
	else {
	    if(!array_key_exists('nom_ent',$value) or $value['nom_ent'] == '') {
		$mess.= 'Merci de fournir le nom de cette établissement<br/>';
		$fields[] = 'nom_ent';
	    }
	    if(!array_key_exists('type_ent',$value) or $value['type_ent'] == '') {
		$mess.= 'merci de fournir la relation avec cet établissement<br/>';
		$fields[] = 'type_ent';
	    }
//      if(!array_key_exists('tel_ent',$value) or $value['tel_ent'] == '' or generalControl::telephoneControl($value['tel_ent']) == '0')
//      { $mess.= 'merci de fournir un numéro de téléphone pour cet établissement<br/>'; $fields[] = 'tel_ent'; }
	}

	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }
}

?>
