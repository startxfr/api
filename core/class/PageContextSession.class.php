<?php
/*#########################################################################
#
#   name :       PageContextSession.inc
#   desc :	 Process incomming var from client
#   categorie :  PageContext
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for interface between session management modules and
 * page context
 */
class PageContextSession {
    /** Channel to use for this Context. */
    var $channel;
    /** Does the session should be authenticated. */
    var $NeedUser;
    /** Does the page need a specific right. */
    var $NeedRight;

    /**
     * Process Session analyse initialisation
     * define channel and get Channel context
     */
    function __construct($channel='normal') {
	$this->channel = $channel;
	$this->NeedUser = FALSE;
	if($GLOBALS['CHANNEL_'.$this->channel]['requireSSL'] == 'TRUE') {
	    $this->NeedSecure = TRUE;
	}
	else {
	    $this->NeedSecure = FALSE;
	}
    }

    /**
     * Force use of User based session
     */
    function NeedSessionUser() {
	$this->NeedUser = TRUE;
    }

    /**
     * Force use of HTTPS connexion
     */
    function CheckSSLSession() {
	if($_SERVER['HTTPS'] != 'on') {
	    header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	    exit();
	}
    }

    /**
     * Force use of specific right for accessing this page
     */
    function NeedRightSession($right = 0) {
	$this->NeedRight = $right;
    }

    /**
     * Process Session search and return a good session regarding page
     * environement, existing session and channel configuration
     * If no session is found or a wrong session, automatic redirection
     * will be launch by Session or SessionUser class
     */
    function Process($return = '',$doRedirect = true) {
	if($this->NeedUser) {
	    $sess = new SessionUser($this->channel);
	    // get context if secure connexion is established
	    if($this->NeedSecure) {
		$this->CheckSSLSession();
	    }
	    // get context only if right is matched
	    if($this->NeedRight != '') {
		$sess->NeedRightSession($this->NeedRight);
	    }
	    return $sess->ProcessSessionAnalyse($doRedirect);
	}
	else {
	    $sess = new Session($this->channel);
	    if($this->NeedSecure) {
		$this->CheckSSLSession();
	    }
	    return $sess->Process(false,$doRedirect);
	}
    }
}
?>
