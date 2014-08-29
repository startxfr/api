<?php

include_once('GeneralControl.inc');
/**
 * Classe de controle avant requète SQL
 * Cette classe gère les affaires.
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class affaireControl {
    /**
     * Méthode statique qui effectue le controle
     * @param array $value Les données à valider
     * @return array Le résultat du controle
     */
    static function affaire($value) {
	$mess = '';
	$fields = array();
	if(!is_array($value) or count($value) == 0)
	    $mess.= 'Aucune valeur saisie. Contrôle impossible. Veuillez re-commencer votre enregistrement.<br/>';
	else {
	    if(!array_key_exists('titre_aff',$value) or $value['titre_aff'] == '') {
		$mess.= 'merci de fournir un nom pour cette affaire<br/>';
		$fields[] = 'titre_aff';
	    }
	    if(!array_key_exists('typeproj_aff',$value) or $value['typeproj_aff'] == '') {
		$mess.= 'merci d\'indiquer le type de cette affaire<br/>';
		$fields[] = 'typeproj_aff';
	    }
	    if(!array_key_exists('contact_aff',$value) or $value['contact_aff'] == '') {
		$mess.= 'merci de fournir un contact pour cette affaire<br/>';
		$fields[] = 'contact_aff';
	    }
	    if(!array_key_exists('commercial_aff',$value) or $value['commercial_aff'] == '') {
		$mess.= 'merci de fournir le responsable commercial de cette affaire<br/>';
		$fields[] = 'commercial_aff';
	    }
	    if(!array_key_exists('technique_aff',$value) or $value['technique_aff'] == '') {
		$mess.= 'merci de fournir le responsable technique de cette affaire<br/>';
		$fields[] = 'tiechnique_aff';
	    }

	}
	if($mess != '') return array(false,$mess,$fields);
	else 		return array(true);
    }
}
/*
 * A controler : le titre, le typeproj, l'état, le status, les dates, 
 * les contacts, le commercial et le technico
*/
?>
