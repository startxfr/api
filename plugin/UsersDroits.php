<?php
include('inc/conf.inc');
include('inc/core.inc');
loadPlugin('ZControl/GeneralControl');

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of UsersDroits
 * Cette classe est dépendant du module de controle des emails et de la connexion à la BDD
 *
 * @author Nicolas Mannocci
 * @version 0.1
 */
class UsersDroits {
//put your code here
    private $type;
    private $nom;
    private $prenom;
    private $login;
    private $mdp;
    private $civ;
    private $mail;
    private $actif;
    private $pin;
    private $droit1;
    private $droits;
    private $bdd;

    /**
     *Constructeur de la classe.
     * @param string $type Définit le type d'utilisateur, utile seulement en création
     */
    public function __construct($type = "mini") {
	$this->type = $type;
	$this->bdd = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    }
    /**
     *Fonction appelée pour créer un nouvel utilisateur
     * @param string $login le login du nouvel utilisateur
     * @param string $mdp le mot de passe du nouvel utilisateur non crypté
     * @param string $civ la civilité de l'utilisateur
     * @param string $nom le nom de l'utilisateur
     * @param string $prenom le prénom de l'utilisateur
     * @param string $mail le mail de l'utilisateur
     * @param int $pin le code pin de l'utilisateur, forcément numérique et à 4 chiffres, par défaut 0000
     * @return bool Indique si la création s'est bien passée ou pas
     */
    public function creerUser($login, $mdp, $civ, $nom, $prenom, $mail, $pin = '0000') {
	if($login == '' or $prenom == '' or $nom == '' or $civ == '' or $mdp == '' or $mail == '' or $pin == '')
	    return array(false, 'Des infos sont manquantes');
	$this->bdd->makeRequeteFree("Select login from user where login = '".$login."'");
	$rs = $this->bdd->process2();
	if($rs[0] and !array_key_exists('0', $rs[1]))
	    $this->login = $login;
	else
	    return array(false, 'Login déjà utilisé');
	$this->mdp = md5($mdp);
	if(is_numeric($pin) and strlen($pin) == 4)
	    $this->pin = md5($pin);
	else
	    return array(false, 'Mauvais pin');
	$this->civ = $civ;
	$this->prenom = $prenom;
	$this->nom = $nom;
	if(generalControl::mailControl($mail) == 1)
	    $this->mail = $mail;
	else
	    return array(false, 'Mauvais email');

	switch($this->type) {
	    case 'boss':
		return $this->boss();
		break;
	    case 'mini':
		return $this->lecteur();
		break;
	    case 'classic':
		return $this->classic();
		break;
	    case 'classicFree':
		return $this->classicSansPayer();
		break;
	    case 'chef':
		return $this->sousBoss();
		break;
	    default:
		return array(false, 'mauvais type');
		break;
	}

    }
    /**
     *Fonction qui insert un droit dans la table user_droits
     * @param int $droit le droit à insérer
     * @return bool Indique si ça s'est bien passé ou pas
     */
    private function insert($droit) {
	if(!is_numeric($droit))
	    return array(false, 'le droit n\'est pas sous forme numérique');
	$liste['login'] = $this->login;
	$liste['droit'] = $droit;
	$this->bdd->makeRequeteInsert('user_droits', $liste);
	$rs2 = $this->bdd->process2();
	if(!$rs2[0])
	    return array(false, $rs2);
	else
	    return true;
    }

    /**
     *Fonction qui insert dans la base un utilisateur de type boss, admin client, compte principal
     * @return bool Indique si ça s'est bien passé ou pas
     */
    private function boss() {
	$data['droit'] = $this->droit1 = 1;
	$data['civ'] = $this->civ;
	$data['prenom'] = $this->prenom;
	$data['nom'] = $this->nom;
	$data['login'] = $this->login;
	$data['pwd'] = $this->mdp;
	$data['mail'] = $this->mail;
	$data['actif'] = 1;
	$data['pin'] = $this->pin;
	$this->bdd->makeRequeteInsert('user', $data);
	return $this->bdd->process2();
    }
    /**
     *Fonction qui insert dans la base un utilisateur avec seulement les droits de lectures et de recherche
     * @return bool Indique si ça s'est bien passé ou pas
     */
    private function lecteur() {
	$data['droit'] = $this->droit1 = 3;
	$data['civ'] = $this->civ;
	$data['prenom'] = $this->prenom;
	$data['nom'] = $this->nom;
	$data['login'] = $this->login;
	$data['pwd'] = $this->mdp;
	$data['mail'] = $this->mail;
	$data['actif'] = 1;
	$data['pin'] = $this->pin;
	$this->bdd->makeRequeteInsert('user', $data);
	$rs = $this->bdd->process2();
	if($rs[0]) {
	    $droit = 1005;
	    while($droit <= 1710) {
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit +=5;
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit +=95;
	    }
	    return array(true);
	}
	else {
	    return array(false, $rs);
	}
    }
    /**
     *Fonction qui insert dans la base un utilisateur avec tous les droits, comme le compte principal.
     * @return bool Indique si ça s'est bien passé
     */
    private function sousBoss() {
	$data['droit'] = $this->droit1 = 2;
	$data['civ'] = $this->civ;
	$data['prenom'] = $this->prenom;
	$data['nom'] = $this->nom;
	$data['login'] = $this->login;
	$data['pwd'] = $this->mdp;
	$data['mail'] = $this->mail;
	$data['actif'] = 1;
	$data['pin'] = $this->pin;
	$this->bdd->makeRequeteInsert('user', $data);
	$rs = $this->bdd->process2();
	if($rs[0]) {
	    $droit = 1000;
	    while($droit <= 1700) {
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit += 100;
	    }
	    $droit = 2000;
	    while($droit <= 2200) {
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit += 100;
	    }

	    $droit = 3000;
	    if(!$this->insert($droit))
		return array(false, $droit);

	    $droit = 5000;
	    if(!$this->insert($droit))
		return array(false, $droit);

	    return array(true);
	}
	else {
	    return array(false, $rs);
	}

    }
    /**
     *Fonction qui insert dans la base un utilisateur avec les droits classic
     * @return bool Indique si ça s'est bien passé ou pas
     */
    private function classic() {
	$data['droit'] = $this->droit1 = 2;
	$data['civ'] = $this->civ;
	$data['prenom'] = $this->prenom;
	$data['nom'] = $this->nom;
	$data['login'] = $this->login;
	$data['pwd'] = $this->mdp;
	$data['mail'] = $this->mail;
	$data['actif'] = 1;
	$data['pin'] = $this->pin;
	$this->bdd->makeRequeteInsert('user', $data);
	$rs = $this->bdd->process2();
	if($rs[0]) {
	    $droit = 1005;
	    $compteur = 1030;
	    while($droit < 1800) {
		while($droit <= $compteur) {
		    if(!$this->insert($droit))
			return array(false, $droit);
		    $droit +=5;
		}
		$droit -= 22;
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit += 14;
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit += 23;
		$compteur += 25;
		while($droit <= $compteur) {
		    if(!$this->insert($droit))
			return array(false, $droit);
		    $droit +=2;
		}
		$compteur +=10;
		$droit +=4;
		while($droit <= $compteur) {
		    if(!$this->insert($droit))
			return array(false, $droit);
		    $droit +=2;
		}
		$compteur += 65;
		$droit += 39;
	    }

	    $droit = 2000;
	    while($droit <= 2200) {
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit += 100;
	    }

	    $droit = 3000;
	    if(!$this->insert($droit))
		return array(false, $droit);

	    $droit = 5000;
	    if(!$this->insert($droit))
		return array(false, $droit);
	    return array(true);
	}
	else {
	    return array(false, $rs);
	}
    }
    /**
     *Fonction qui insert dans la base un utilisateur avec les droits classic sans les droits d'envois de fax et courrier payant
     * @return bool Indique si ça s'est bien passé.
     */
    private function classicSansPayer() {
	$data['droit'] = $this->droit1 = 2;
	$data['civ'] = $this->civ;
	$data['prenom'] = $this->prenom;
	$data['nom'] = $this->nom;
	$data['login'] = $this->login;
	$data['pwd'] = $this->mdp;
	$data['mail'] = $this->mail;
	$data['actif'] = 1;
	$data['pin'] = $this->pin;
	$this->bdd->makeRequeteInsert('user', $data);
	$rs = $this->bdd->process2();
	if($rs[0]) {
	    $droit = 1005;
	    $compteur = 1030;
	    while($droit < 1800) {
		while($droit <= $compteur) {
		    if(!$this->insert($droit))
			return array(false, $droit);
		    $droit +=5;
		}
		$droit -= 22;
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit += 14;
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit += 23;
		if(!$this->insert($droit))
		    return array(false, $droit);
		$compteur +=35;
		$droit +=10;
		while($droit <= $compteur) {
		    if(!$this->insert($droit))
			return array(false, $droit);
		    $droit +=2;
		}
		$compteur += 65;
		$droit += 39;
	    }

	    $droit = 2000;
	    while($droit <= 2200) {
		if(!$this->insert($droit))
		    return array(false, $droit);
		$droit += 100;
	    }

	    $droit = 3000;
	    if(!$this->insert($droit))
		return array(false, $droit);

	    $droit = 5000;
	    if(!$this->insert($droit))
		return array(false, $droit);
	    return array(true);
	}
	else {
	    return array(false, $rs);
	}
    }
}
?>
