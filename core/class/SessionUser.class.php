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
	    $_SESSION['user']['right'] = 21;
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
	    $query = "SELECT * FROM public.user u, ref_droit d WHERE u.droit = d.id_dt AND u.login = '".$user."'";
	}
	else {
	    $query = "SELECT * FROM user, ref_droit WHERE droit = id_dt AND login = '".$user."' ";
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
	$test = $this->TestUserRight();
	//echo $test;exit;
	if($test != 'OK') {
	    if($doRedirect) $this->InitSession->RedirectSession($test);
	    return false;
	}
	else return $test;

    }

}

?>
