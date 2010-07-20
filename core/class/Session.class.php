<?php
/*#########################################################################
#
#   name :       session.inc
#   desc :       library for session management
#   categorie :  core module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Create or catch a session for the given web client according
 * to the channel configuration.
 * This class should be used via the PageContext class
 * witch provide automatic analyse and redirection relative
 * to the global page context
 */

session_set_save_handler(array('SessionBDD', 'open'),
	array('SessionBDD', 'close'),
	array('SessionBDD', 'read'),
	array('SessionBDD', 'write'),
	array('SessionBDD', 'destroy'),
	array('SessionBDD', 'gc')
);
register_shutdown_function('session_write_close');


class Session {
    /** Channel to use for this Session. */
    var $Channel;

    /**
     * Constructor.
     * set the channel context
     */
    function __construct($channel = 'normal') {
	$this->Channel = $channel;
    }

    /**
     * Capture a session for the channel
     */
    function CatchSession() {
	session_name($GLOBALS['CHANNEL_'.$this->Channel]['SessName']);
	session_start();
	Logg::loggerNotice('Session::CatchSession() ~ capture de la session '.session_id(),'',__FILE__.'@'.__LINE__);
    }

    /**
     * Capture a session for the channel
     */
    function getSessionCookie() {
	if(isset($_COOKIE[$GLOBALS['CHANNEL_'.$this->Channel]['SessName']])) return $_COOKIE[$GLOBALS['CHANNEL_'.$this->Channel]['SessName']];
	else return false;
    }

    /**
     * Redirect user to a given page
     * Used when session is wrong or doesn't exist
     */
    function RedirectSession($type = '', $to1 = "") {
	$relPath = ($this->Channel != 'normal') ? '../' : './';
	if($type == 'TIME_OUT') {
	    $this->Deconnect();
	    $to = $relPath."Login.php?mess=timeout&from=".$_SERVER['REQUEST_URI'];
	}
	elseif($type == 'USER_ENDED') {
	    $this->Deconnect();
	    $to = $relPath."Login.php?mess=close&from=".$_SERVER['REQUEST_URI'];
	}
	elseif(($type == 'NO_SESSION_STORED')or
		($type == 'NO_SESSION')or
		($type == 'CORRUPTED_ID')or
		($type == 'SESSION_NOT_SX'))
	    $to = $relPath."Login.php?mess=badsess&from=".$_SERVER['REQUEST_URI'];
	elseif($type == 'BAD_RIGHT')
	    $to = $relPath."Login.php?mess=badright&from=".$_SERVER['REQUEST_URI'];
	elseif($type == 'NO_USER_SESSION') {
	    $this->Deconnect();
	    $to = $relPath."Login.php?mess=nousersess&from=".$_SERVER['REQUEST_URI'];
	}
	elseif($type == 'USER_WELCOME') {
	    $tmp = new Bdd($GLOBALS['CHANNEL_'.$this->Channel]['SessDBPool']);
	    $tmp->makeRequeteFree("SELECT id_mess, titre_mess, contenu_mess from message where user_mess = '".$_SESSION['user']['id']."' and debut_mess <= CURRENT_DATE and (fin_mess >= CURRENT_DATE or fin_mess is null ) and lu_mess = 0 ");
	    $resultat = $tmp->process2();
	    if($resultat[0] and count($resultat[1]) > 0) {
		$_SESSION['message'] = $resultat[1];
                $_SESSION['message']['dejaVu'] = false;
		$to = "index.php?mess=message";
		$req = "update message set lu_mess = '1' where (";
		foreach($resultat[1] as $v)
		    $req .= " id_mess = '".$v['id_mess']."' or";
		$req = substr($req,0,strlen($req)-2);
		$req .= ")";
		$tmp->makeRequeteFree($req);
		$tmp->process2();
	    }
	    else $to = "index.php?mess=welcome";
	}
	else  $to = "index.php";
	Logg::loggerNotice('Session::RedirectSession() ~  redirection de session de type '.$type.' pour la session '.session_id(),'',__FILE__.'@'.__LINE__);
	if($to1 == "")
	    $to1 = $to;
	header("Location: ".$to1);
	exit();
    }

    /**
     * Test actual session according to the configuration
     * of this object
     */
    function TestSession() {
	$sess_id = $this->getSessionCookie();
	if($sess_id === false) $code = 'NO_SESSION';
	else {
	    if(strlen($sess_id) == $GLOBALS['CHANNEL_'.$this->Channel]['SessKeyLenght']) {
		$var['id_sess'] = $sess_id;
		$tmp = new Bdd($GLOBALS['CHANNEL_'.$this->Channel]['SessDBPool']);
		$tmp->makeRequeteAuto('session',$var);
		$result1 = $tmp->process();
		$result = $result1[0];
		if($result['date_sess'] != '') {
		    $restos = (DateUniv2Timestamp($result['datefin_sess'])-DateUniv2Timestamp(''));
		    if($restos > 0)
			$code = 'OK';
		    else  $code = 'TIME_OUT';
		}
		else  $code = 'NO_SESSION_STORED';
	    }
	    else  $code = 'SESSION_NOT_SX';
	}
	return $code;
    }


    /* Create the session in database and set session cookie
    */
    function CreateSession($user = "anonymous") {
	if ($this->Channel != '') {
	    $debuter    = time();
	    $date_debut = date('Y-m-d H:i:s',$debuter);
	    $date_fin_tmp= $debuter + $GLOBALS['CHANNEL_'.$this->Channel]['SessTimeOut'];
	    $date_fin   = date('Y-m-d H:i:s',$date_fin_tmp);
	    $id_session = substr (md5(rand(1,999)*time()*microtime(true)),0,$GLOBALS['CHANNEL_'.$this->Channel]['SessKeyLenght']);

	    $REQHD['id_sess']		= $id_session;
	    $REQHD['date_sess']	= $date_debut;
	    $REQHD['datefin_sess']	= $date_fin;
	    $REQHD['user_sess']	= $user;
	    $REQHD['channel_sess']	= $this->Channel;
	    $REQHD['secure_sess']	= (array_key_exists('SSL_PROTOCOL',$_SERVER)) ? $_SERVER['SSL_PROTOCOL'] : '';
	    $InfoClient			= GetClientBrowserInfo();
	    $REQHD['OS_sess']		= $InfoClient[0];
	    $REQHD['browser_sess']	= $InfoClient[1];
	    $REQHD['ip_sess']		= $InfoClient[2];
	    $REQHD['host_sess']	= $InfoClient[3];

	    $insert = new Bdd($GLOBALS['CHANNEL_'.$this->Channel]['SessDBPool']);
	    if($user != 'anonymous') {
		$insert->makeRequeteFree("SELECT UNIX_TIMESTAMP(datefin_sess) as date, id_sess from session where user_sess = '".$user."' order by datefin_sess DESC limit 0,1");
		$date = $insert->process2();
		if($debuter <= $date[1][0]['date']) {
		    //Une session est déjà en cours
		    $insert->makeRequeteFree("UPDATE session set datefin_sess = '".$date_debut."' where id_sess = '".$date[1][0]['id_sess']."'");
		    $insert->process();
		}
	    }

	    $insert->makeRequeteInsert('session',$REQHD);
	    $insert->process();

	    session_set_cookie_params($GLOBALS['CHANNEL_'.$this->Channel]['SessTimeOut']);
	    session_name ($GLOBALS['CHANNEL_'.$this->Channel]['SessName']);
	    session_id ($id_session);
	    session_cache_expire ($GLOBALS['CHANNEL_'.$this->Channel]['SessTimeOut']);

	    //setcookie(session_name(),session_id(), $GLOBALS['CHANNEL_'.$this->Channel]['SessTimeOut']);
	    session_start();
	    $_SESSION['fin'] = date('d/m/Y H:i',$date_fin_tmp);
	    Language::LanguageDetect();

	    Logg::loggerAlert('Session::CreateSession() ~  création de la session '.session_id()." pour l'utilisateur ".$user,'',__FILE__.'@'.__LINE__);
	    setcookie('login',$user,(time()+1296000));
	}
	else  Logg::loggerError('Session::CreateSession() ~ création de session impossible. Aucun channel fournit','',__FILE__.'@'.__LINE__);
    }


    /**
     * Backup data stored in $_SESSION in database
     */
    function recordSessionData() {
	$sess_id = $this->getSessionCookie();
	if($sess_id !== false) {
	    $tmpreq = new Bdd($GLOBALS['CHANNEL_'.$this->Channel]['SessDBPool']);
	    unset($_SESSION['user']['permissions']);
	    unset($_SESSION['user']['module']);
	    $tps['backup_sess'] = addslashes(@session_encode());
	    $tmpreq->makeRequeteUpdate('session', 'id_sess', $sess_id, $tps );
	    $tmpreq->process();
	    return true;
	}
    }

    /**
     * Process Session destroy and trash the session data after
     * save them in the database
     */
    function Deconnect() {
	Logg::loggerAlert('Session::Deconnect() ~ deconnexion de la session '.session_id(),'',__FILE__.'@'.__LINE__);
	$tmpreq = new Bdd($GLOBALS['CHANNEL_'.$this->Channel]['SessDBPool']);
	$tps['datefin_sess'] = date("Y-m-d H:i:s",time());
	$tmpreq->makeRequeteUpdate('session', 'id_sess', session_id(), $tps );
	$tmpreq->process();
	$_SESSION = array();
	setcookie(session_name(), '',time()-42000);
	@session_destroy();
    }


    /**
     * Process Session search and return TRUE if a good session is found
     * regarding page environement, existing session and channel configuration
     * If no session is found or a wrong session, automatic redirection
     * will be launch by Session or SessionUser class
     */
    function Process($CreateSess = FALSE,$doRedirect = TRUE) {
	$test = $this->TestSession();
	if($test != 'OK') {
	    // On crée une session si la session est corrompue,
	    // si la session est arrivée à expiration et que le channel est public
	    //ou si $CreateSess est explicitement demandé
	    if(($test == 'SESSION_NOT_SX')or
		    ($CreateSess)or
		    (($test == 'TIME_OUT')and(!$GLOBALS['CHANNEL_'.$this->Channel]['private']))or
		    (($test == 'NO_SESSION')and(!$GLOBALS['CHANNEL_'.$this->Channel]['private'])))
		$this->CreateSession();
	    elseif($doRedirect) $this->RedirectSession($test);
	    else return false;
	}
	else  $this->CatchSession();
    }
}

?>
