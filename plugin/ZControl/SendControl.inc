<?php
/**
 * Classe de controle pour les informations relatives à l'envoi d'information par mail, fax, courrier
 * @author Christophe LARUE <projet@zuno.fr>
 * @version 0.5
 */
class sendControl {
    /**
     * Methode de contrôle lors de l'envoi par mail
     * @author Christophe LARUE <projet@zuno.fr>
     * @param  array $value tableau avec les différentes valeurs à controller
     * @return array Tableau avec un booléen pour le resultat, un message d'erreur,
     * 		et un autre tableau avec la liste des champs ayant généré une erreur
     */
    static function sendMail($value) {
	$mess = '';
	$fields = array();
	if(!is_array($value) or count($value) == 0)
	    $mess.= 'Aucune valeur saisie. Contrôle impossible. Veuillez re-commencer votre enregistrement.<br/>';
	else {
	    if(!array_key_exists('titre',$value) or $value['titre'] == '') {
		$mess.= 'merci de fournir un titre pour cet e-mail<br/>';
		$fields[] = 'titre';
	    }
	    if(!array_key_exists('email',$value) or $value['email'] == '') {
		$mess.= 'merci d\'indiquer un déstinataire pour cet e-mail<br/>';
		$fields[] = 'email';
	    }
	}
	if($mess != '') 	return array(false,$mess,$fields);
	else 			return array(true);
    }


    /**
     * Methode de contrôle lors de l'envoi d'un courrier
     * @author Christophe LARUE <projet@zuno.fr>
     * @param  array $value Tableau avec les différentes valeurs à controller
     * @return array Tableau avec un booléen pour le resultat, un message d'erreur,
     * 		et un autre tableau avec la liste des champs ayant généré une erreur
     */
    static function sendCourrier($value) {
	$mess = '';
	$fields = array();
	if(!is_array($value) or count($value) == 0)
	    $mess.= 'Aucune valeur saisie. Contrôle impossible. Veuillez re-commencer votre enregistrement.<br/>';
	else {
	    if(!array_key_exists('nom',$value) or $value['nom'] == '') {
		$mess.= 'merci de fournir le nom du contact de ce courrier<br/>';
		$fields[] = 'nom';
	    }
	    if(!array_key_exists('add1',$value) or $value['add1'] == '') {
		$mess.= 'merci d\'indiquer une adresse pour ce courrier<br/>';
		$fields[] = 'add1';
	    }
	    if(!array_key_exists('cp',$value) or $value['cp'] == '') {
		$mess.= 'merci d\'indiquer un code postal pour ce courrier<br/>';
		$fields[] = 'cp';
	    }
	    if(!array_key_exists('ville',$value) or $value['ville'] == '') {
		$mess.= 'merci d\'indiquer une ville pour ce courrier<br/>';
		$fields[] = 'ville';
	    }
	    if(!array_key_exists('cpays',$value) or $value['cpays'] == '') {
		$mess.= 'merci d\'indiquer un pays pour ce courrier<br/>';
		$fields[] = 'cpays';
	    }
	    if(!array_key_exists('file',$value)) {
		$mess.= 'merci de fournir un document<br/>';
		$fields[] = 'file';
	    }
	    elseif(!file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.pj'].$value['file'])) {
		$mess.= 'Le document choisi n\'existe plus<br/>';
		$fields[] = 'file';
	    }
	}
	if($mess != '') 	return array(false,$mess,$fields);
	else 			return array(true);
    }


    /**
     * Methode de contrôle lors de l'envoi d'un fax
     * @author Christophe LARUE <projet@zuno.fr>
     * @param  array $value Tableau avec les différentes valeurs à controller
     * @return array Tableau avec un booléen pour le resultat, un message d'erreur,
     * 		et un autre tableau avec la liste des champs ayant généré une erreur
     */
    static function sendFax($value) {
	$mess = '';
	$fields = array();
	if(!is_array($value) or count($value) == 0)
	    $mess.= 'Aucune valeur saisie. Contrôle impossible. Veuillez re-commencer votre enregistrement.<br/>';
	else {
	    if(!array_key_exists('nom',$value) or $value['nom'] == '') {
		$mess.= 'merci de fournir le nom du contact de ce fax<br/>';
		$fields[] = 'nom';
	    }
	    if(!array_key_exists('fax',$value) or $value['fax'] == '') {
		$mess.= 'merci d\'indiquer le numéro de fax<br/>';
		$fields[] = 'fax';
	    }
	    if(!array_key_exists('file',$value)) {
		$mess.= 'merci de fournir un document<br/>';
		$fields[] = 'file';
	    }
	    elseif(!file_exists($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoSendMail']['dir.pj'].$value['file'])) {
		$mess.= 'Le document choisi n\'existe plus<br/>';
		$fields[] = 'file';
	    }
	}
	if($mess != '') 	return array(false,$mess,$fields);
	else 			return array(true);
    }
}
?>
