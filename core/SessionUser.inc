<?php
/*#########################################################################
#
#   name :       Authentication.inc
#   desc :       library for authentication
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Create or catch a User-based session for the given web client according
 * to the channel configuration.
 * This class should be used via the PageContext class
 * witch provide automatic analyse and redirection relative
 * to the global page context
 */
class SessionUser {
    /** DB info for given user. */
    var $UserInfo;
    /** Channel to use for this Session. */
    var $Channel;
    /** Session object to use. */
    var $InitSession;
    /** Does the page need a specific right. */
    var $NeedRight;

    /**
     * Constructor.
     * set the channel context
     */
    function __construct($channel = 'normal') {
	Logg::Logger('CORE::SessionUser::__construct(channel="'.$channel.'")',0);
	$this->Channel = $channel;
	$this->NeedRight = $GLOBALS['CHANNEL_'.$this->Channel]['RequiredRight'];
	$this->InitSession = new Session($this->Channel);
    }

    /**
     * Control right limit for this page
     */
    function NeedRightSession($right) {
	$this->NeedRight = $right;
    }

    /**
     * Initialize an authenticated session for the given user
     * Normal initialized ONLY by Login.php page after authentication control
     * If a previous session is found, merge old session data with new one
     */
    function CreateSession($user,$doRedirect = true) {
	//On crée la session
	$this->InitSession->CreateSession($user);

	$tmpreq = new Bdd($GLOBALS['CHANNEL_'.$this->Channel]['SessDBPool']);
	$tps['user_sess'] = $user;
	$tmpreq->makeRequeteAuto('session',$tps,'AND backup_sess IS NOT NULL ORDER BY date_sess DESC LIMIT 0, 1');
	$lastSess = $tmpreq->process();
	if($lastSess[0]['backup_sess'] != '') {
	    $tmpsess = $_SESSION;
	    session_decode(stripslashs($lastSess[0]['backup_sess']));
	    $_SESSION = array_merge($_SESSION,$tmpsess);
	}

	//Enregistrement des infos sur l'utilisateur
	$_SESSION['user']['nom']	= $this->TestUser["nom"];
	$_SESSION['user']['prenom']	= $this->TestUser["prenom"];
	$_SESSION['user']['fullnom']= $this->TestUser["civ"].' '.$this->TestUser["prenom"].' '.$this->TestUser["nom"];
	$_SESSION['user']['id']		= $this->TestUser["login"];
	$_SESSION['user']['mail']	= $this->TestUser["mail"];

	//Changement de language selon les choix de l'utilisateur
	if($this->TestUser["lang"] != '')
	    Language::LanguageSwitch($this->TestUser["lang"]);

	//Attribution des droits
	if($this->TestUser["droit"] == '') {
	    $_SESSION['user']['right'] = 10;
	    $_SESSION['user']['rightDesc'] = 'default';
	}
	else {
	    $_SESSION['user']['right'] = $this->TestUser["droit"];
	    if(($_SESSION["language"] != $GLOBALS['LANGUE']['default'])and($_SESSION["language"] != ''))
		$ExtLang = "_".$_SESSION["language"];
	    $_SESSION['user']['rightDesc'] = $this->TestUser["nom_dt".$ExtLang];
	}

	$tmpreq->makeRequeteFree("SELECT * FROM user_droits WHERE login = '".$_SESSION['user']['id']."'");
	$r = $tmpreq->process2();
	if($r[0])
	    foreach($r[1] as $k => $d)
		$_SESSION['user']['permissions'][$d['droit']] = $d['droit'];
	$tmpreq->makeRequeteFree("SELECT * FROM module");
	$r = $tmpreq->process2();
	if($r[0])
	    foreach($r[1] as $k => $d)
		$_SESSION['user']['module'][$d['nom_mod']] = $d['acces_mod'];

	if($doRedirect) $this->InitSession->RedirectSession('USER_WELCOME');
    }

    /**
     * Return information about a given user from the database
     */
    function GetDBUser($user) {
	$toto = new Bdd();
	if($toto->baseType == 'pgsql') {
	    $query = "SELECT * FROM public.user u, droit d WHERE u.droit = d.id_dt AND u.login = '".$user."'";
	}
	else {
	    $query = "SELECT * FROM user, droit WHERE droit = id_dt AND login = '".$user."' ";
	}
	$toto->makeRequeteFree($query);
	$titi = $toto->process();
	$this->TestUser = $titi[0];
    }

    /**
     * Test actual session according to the configuration
     * of this object
     * This function should implement analyse of SSL and right management also
     */
    function TestUserRight() {
	$test = $this->InitSession->TestSession();
	if($test == 'OK') {
	    $test = $this->InitSession->CatchSession();
	    if($_SESSION['user']['id'] != '') {
		if ((strpos($this->NeedRight,',') !== false and
				in_array($_SESSION['user']['right'],explode(',',$this->NeedRight)))or
			(strpos($this->NeedRight,',') === false and
				$_SESSION['user']['right'] <= $this->NeedRight)) {
		    $result = 'OK';
		}
		else {
		    $result = 'BAD_RIGHT';
		}
	    }
	    else {
		$result = 'NO_USER_SESSION';
	    }
	    return $result;
	}
	else {
	    return $test;
	}
    }

    /**
     * Test given user authentication and return OK or error status
     */
    function TestUser($user,$pass, $md5 = 'non') {
	$pwd = ($md5 == 'non') ? md5($pass) : $pass;
	$this->GetDBUser($user);
	if($this->TestUser["login"] != '') {
	    if($this->TestUser["actif"] == '1') {
		if($this->TestUser["pwd"] == $pwd) {
		    if ((strpos($this->NeedRight,',') !== false and
				    in_array($this->TestUser["droit"],explode(',',$this->NeedRight)))or
			    (strpos($this->NeedRight,',') === false and
				    $this->TestUser["droit"] <= $this->NeedRight)) {
			$result = 'OK';
		    }
		    else {
			$result = 'BAD_RIGHT';
		    }
		}
		else {
		    $result = 'BAD_PWD';
		}
	    }
	    else {
		$result = 'INACTIVE_USER';
	    }
	}
	else {
	    $result = 'BAD_LOGIN';
	}
	return $result;
    }

    /**
     * Process Session search and return TRUE if a good session is found
     * regarding page environement, existing session and channel configuration
     * If no session is found or a wrong session, automatic redirection
     * will be launch by Session or SessionUser class
     */
    function ProcessSessionAnalyse($doRedirect = true) {
	Logg::Logger('CORE::SessionUser::ProcessSessionAnalyse()',0);
	$test = $this->TestUserRight();
	//	echo $test;exit;
	if($test != 'OK') {
	    if($doRedirect) $this->InitSession->RedirectSession($test);
	    return false;
	}
	else return $test;

    }

}
/**
 *Fonction qui regarde si tu es proprio et appelle correctement verfiDroits
 * @param string $partie
 * @param int $souspartie
 * @param string $proprio
 * @return bool
 */
function verifDroitsAuto($partie, $souspartie, $proprio) {
    if($proprio == $_SESSION['user']['id']) {
	return verifDroits($partie, $souspartie);
    }
    else {
        switch($souspartie) {
            case 13:
                return verifDroits($partie, 14);
                break;
            case 15:
                return verifDroits($partie, 17);
                break;
            case 25:
                return verifDroits($partie, 35);
                break;
            case 27:
                return verifDroits($partie, 37);
                break;
            case 30:
                return verifDroits($partie, 40);
                break;
            case 50:
                return verifDroits($partie, 51);
                break;
            case 52:
                return verifDroits($partie, 53);
                break;
            case 54:
                return verifDroits($partie, 55);
                break;
            case 60:
                return verifDroits($partie, 61);
                break;
            case 62:
                return verifDroits($partie, 63);
                break;
            default:
                return verifDroits($partie, $souspartie);
                break;
        }
    }
}

/**
 * Fonction qui gère les accès aux différentes parties de l'appli
 * @param string $partie Le nom de la partie / du module dans le quel on se trouve
 * @param int $souspartie Le code de l'action désirée
 * @return bool Indique si on a le droit ou pas
 */
function verifDroits($partie, $souspartie = 99 ) {
    switch ($partie) {
	case 'actualite':
	    $nbpartie = 1000;
	    break;
	case 'affaire':
	    $nbpartie = 1100;
	    break;
	case 'commande':
	    $nbpartie = 1200;
	    break;
	case 'contact':
	case 'contactParticulier':
	case 'contactEntreprise' :
	    $partie = 'contact';
	    $nbpartie = 1300;
	    break;
	case 'devis':
	    $nbpartie = 1400;
	    break;
	case 'facture':
	    $nbpartie = 1500;
	    break;
	case 'navigator':
	    $nbpartie = 2000;
	    break;
	case 'preference':
	    $nbpartie = 3000;
	    break;
	case 'search':
	    $nbpartie = 2100;
	    break;
	case 'send':
	    $nbpartie = 2200;
	    break;
	case 'avoir':
	    $nbpartie = 1600;
	    break;
	case 'produit':
	    $nbpartie = 1700;
	    break;
	case 'statistiques':
	    $nbpartie = 5000;
	    break;
	default:
	    $nbpartie = 9900;
	    break;
    }
    $ousuisje=$nbpartie+$souspartie;


    //insérer les cas particuliers ici, au début de la fonction
    if($_SESSION['user']['right'] == 0) {return 1;} // Utilisateur STARTX
    
    if($_SESSION['user']['right'] == 1) {return 1;} // Utilisateur STARTX bis

    //Là on va gérer le fait que le client a acheté les modules ou pas
    elseif($_SESSION['user']['module'][$partie] != 'oui') {return array('0', $partie);}
    elseif((($souspartie>=50 and $souspartie<=55) or $souspartie == 59)and $_SESSION['user']['module']['send'] != 'oui') {return array('0', 'Send');}
    elseif($souspartie == 45 and $_SESSION['user']['module']['statistiques'] != 'oui') {return array('0', 'statistiques');}

    // Ici on sait que le client a acheté le module, on vérifie s'il a le droit de faire l'action désirée
    if($_SESSION['user']['right'] == 2) {return 1;} // C'est le compte principal du client, il a donc le droit
    elseif(array_key_exists('permissions', $_SESSION['user'])) {
        if(array_key_exists($nbpartie, $_SESSION['user']['permissions'])) {return 1;} // L'utilisateur a le droit de tout faire dans cette section
        elseif(array_key_exists($nbpartie+99, $_SESSION['user']['permissions'])) {return 0;} // L'utilisateur est interdit d'action dans cette section

        elseif((array_key_exists($nbpartie+59, $_SESSION['user']['permissions'])) && (($souspartie < 59) && ($souspartie > 49))) {return 1;}
        elseif((array_key_exists($nbpartie+69, $_SESSION['user']['permissions'])) && (($souspartie < 69) && ($souspartie > 59))) {return 1;}
        elseif(array_key_exists($ousuisje, $_SESSION['user']['permissions'])) {return 1;} // L'utilisateur a le droit
        elseif($souspartie == 15 && array_key_exists($ousuisje+2, $_SESSION['user']['permissions'])) {return 1;}
        elseif(($souspartie == 25 xor $souspartie == 27 xor $souspartie == 30) && array_key_exists($ousuisje+10, $_SESSION['user']['permissions'])) {return 1;}
        elseif(($souspartie == 50 xor $souspartie == 52 xor $souspartie == 54 xor $souspartie == 60 xor $souspartie == 62 xor $souspartie == 13) && array_key_exists($ousuisje+1, $_SESSION['user']['permissions'])) {return 1;}
        
        else {return 0;} // Autre cas, donc il n'a pas le droit
    }
    else {return 0;} // Un problème avec la session car les droits ne sont pas dans la variable $_SESSION
}

/**
 * Fonction qui vérifie si on a le droit de faire quelque chose et retourne un message d'erreur le cas échéant
 * @param string $partie Le module / La partie de l'appli où nous sommes
 * @param int $souspartie Le code de l'action à effectuer
 * @param string $channel Précise si on est sur l'iphone ou sur le web
 * @return bool Retourne vrai si on a le droit. Dans le cas contraire stoppe l'appli avec un message d'erreur
 */
function aiJeLeDroit ($partie, $souspartie = 99 ,$channel = 'iphone') {
    $result = verifDroits($partie, $souspartie);
    if(is_array($result)) {
	if($channel == 'iphone') { ?>
<root><go to="waDroits"/>
    <title set="waDroits"><?php echo 'Droits insuffisants'; ?></title>
    <part><destination mode="replace" zone="waDroits" create="true"/>
        <data><![CDATA[ <?php echo generalView::droits($result[1]);?> ]]></data>
    </part>
</root>
	    <?php }
	else {
	    echo '<div class="important">Vous n\'avez pas acheté le module pour effectuer cette opération.<br/>
				Pour y accéder vous avez besoin du module <b>'.$result[1].'</b></div>';
	}
	exit;
    }
    elseif(!$result) {
	if($channel == 'iphone') { ?>
<root><go to="waDroits"/>
    <title set="waDroits"><?php echo 'Droits insuffisants'; ?></title>
    <part><destination mode="replace" zone="waDroits" create="true"/>
        <data><![CDATA[ <?php echo generalView::droitsAdmin();?> ]]></data>
    </part>
</root>
	    <?php }
	else {?>
<div class="important">Vous n'avez pas les droits suffisants pour effectuer cette opération</div>
	    <?php }
	exit;
    }
    else {
	return 1;
    }
}

?>
