<?php
/**
 * Classe de controle pour les informations communes à plusieurs partie
 * @author  Christophe LARUE <projet@zuno.fr>
 * @version 0.5
 * @see	 sendControl, factureControl, devisControl, contactControl, commandeControl, affaireControl, actualiteControl
 */
class generalControl {
    /**
     * Methode de contrôle d'une adresse e-mail
     * @author Dev ZUNO <dev@zuno.fr>
     * @param  string $mail adresse e-mail à controller
     * @return booleen. Vrai lorsque l'adresse e-mail est conforme, Faux dans tout les autres cas
     */
    static function mailControl( $mail = '' ) {
	$arobase = strrpos($mail, '@');
	if($arobase === false || $arobase == 0 || $mail[$arobase+1] == '.') {
	    return 0;
	}
	$arobase = substr_count($mail, '@');
	if($arobase != 1) {
	    return 0;
	}
	$espace = strrpos($mail, ' ');
	if($espace != false) {
	    return 0;
	}
	$taille = strlen($mail);
	$taille --;
	if($mail[$taille-2] != '.' && $mail[$taille-3] != '.') {
	    return 0;
	}
	return 1;
    }

    /**
     * Methode de contrôle d'un code postal
     * @author Dev ZUNO <dev@zuno.fr>
     * @param  int $cp Code postal de la ville
     * @return booleen. Vrai lorsque le code postal est conforme, Faux dans tout les autres cas
     */
    static function codePostalControl( $cp = '') {
	$taille = strlen($cp);
	if ($taille != 5) {
	    return 0;
	}
	if(!is_numeric($cp)) {
	    return 0;
	}
	return 1;
    }

    /**
     * Methode de contrôle d'un numero de tel
     * @author Dev ZUNO <dev@zuno.fr>
     * @param  string $telentrant numero de téléphone à controller
     * @return booleen. Vrai lorsque le numero de tel est conforme, Faux dans tout les autres cas
     */
    static function telephoneControl($telentrant = '') {
	$tel = str_replace(' ','',$telentrant);
	if(!is_numeric($tel)) {
	    if ($tel[0] != '+') {
		return 0;
	    }
	    if (!is_numeric(substr($tel, 1))) {
		return 0;
	    }
	}
	if($tel[0] == '0' && $tel[1] != 0) {
	    if(strlen($tel) != 10) {
		return 0;
	    }
	}
	if($tel[0] == '+' || ($tel[0] == 0 && $tel[1] == 0)) {
	    if($tel[1] == 3 && $tel[2] == 3) {
		if(strlen($tel) != 12) {
		    return 0;
		}
	    }
	    if($tel[2] == 3 && $tel[3] == 3) {
		if(strlen($tel) != 13) {
		    return 0;
		}
	    }
	}
	return 1;
    }

    /**
     * Méthode qui vérifie si une facture peut être supprimée ou non
     * @param int $id L'id de la facture
     * @param int $status Le status de la facture
     * @return bool Indique si la facture peut être supprimée ou non.
     */
    static function factureSuppControl($id = '', $status = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(id_fact) as total from facture ; ");
	$temp = $sqlConn->process2();
	$nbfact = $temp[1][0]['total'];
	if($status == '1' && $nbfact == $id) {
	    return 0;
	}
	else return 1;
	//Attention la fonction retourne 0 si c'est bon, 1 sinon
    }

    /**
     * Méthode qui vérifie si une commande peut être supprimée ou non
     * @param string $id L'id de la commande
     * @return bool Indique si la commande peut être supprimée ou non.
     */
    static function commandeSuppControl($id = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(id_fact) as total from facture where commande_fact = '".$id."' ; ");
	$temp = $sqlConn->process2();
	$totalfact = $temp[1][0]['total'];
	if($totalfact >= 1) {
	    $fact = 0;
	    $sqlConn->makeRequeteFree("select id_fact, status_fact from facture where commande_fact = '".$id."' ; ");
	    $temp = $sqlConn->process2();
	    foreach($temp[1] as $v) {
		$fact += self::factureSuppControl($v['id_fact'], $v['status_fact']);
	    }
	    if($fact == 0) {
		return 0;
	    }
	    else {
		return 1;
	    }
	}
	else {
	    return 0;
	}
	//Attention la fonction retourne 0 si c'est bon, 1 sinon
    }

    /**
     * Méthode qui vérifie si un devis peut être supprimé ou non
     * @param string $id L'id du devis
     * @return bool Indique si le devis peut être supprimé ou non.
     */
    static function devisSuppControl($id = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(id_cmd) as total from commande where devis_cmd = '".$id."' ; ");
	$temp = $sqlConn->process2();
	$totalcmd = $temp[1][0]['total'];
	if($totalcmd >= 1) {
	    $cmd = 0;
	    $sqlConn->makeRequeteFree("select id_cmd from commande where devis_cmd = '".$id."' ; ");
	    $temp = $sqlConn->process2();
	    foreach($temp[1] as $v) {
		$cmd += self::commandeSuppControl($v['id_cmd']);
	    }
	    if($cmd == 0) {
		return 0;
	    }
	    else {
		return 1;
	    }
	}
	else {
	    return 0;
	}
	//Attention la fonction retourne 0 si c'est bon, 1 sinon
    }

    /**
     * Méthode qui vérifie si une affaire peut être supprimée ou non
     * @param string $id L'id de l'affaire
     * @return bool Indique si l'affaire peut être supprimée ou non.
     */
    static function affaireSuppControl($id = '') {
	$sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$sqlConn->makeRequeteFree("select count(id_dev) as total from devis where affaire_dev = '".$id."' ; ");
	$temp = $sqlConn->process2();
	$totaldev = $temp[1][0]['total'];
	if($totaldev >= 1) {
	    $dev = 0;
	    $sqlConn->makeRequeteFree("select id_dev from devis where affaire_dev = '".$id."' ; ");
	    $temp = $sqlConn->process2();
	    foreach($temp[1] as $v) {
		$dev += self::devisSuppControl($v['id_dev']);
	    }
	    if($dev == 0) {
		return 0;
	    }
	    else {
		return 1;
	    }
	}
	else {
	    return 0;
	}
	//Attention la fonction retourne 0 si c'est bon, 1 sinon
    }
}
?>
