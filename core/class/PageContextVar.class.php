<?php
/*#########################################################################
#
#   name :       PageContextVar.inc
#   desc :	 Process incomming var from client
#   categorie :  PageContext
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Class for geting detailed information about a incoming variables
 * This class control and filter incomming GET, POST and FILE var from
 * the client browser.
 * This class is not yet fully implemented because control operation
 * are missing
 */
class PageContextVar {
    /** incoming GET data. */
    public $rcvG;
    /** incoming POST data. */
    public $rcvP;
    /** incoming FILE data. */
    public $rcvF;

    /**
     * Constructor
     * @return nothing but public varible are filed with appropriate data.
     */
    function __construct() {
	Logg::loggerInfo('PageContextVar::__construct() ~ Paramètres en entrées : GET="'.count($_GET).'"; POST="'.count($_POST).'"; FILES="'.count($_FILES).'")',array($_GET,$_POST,$_FILES),__FILE__.'@'.__LINE__);
	// Netoyage des variables POST reçues
	foreach($_POST as $key => $val) {
	    if(!is_array($val)) {
		$this->rcvP[$key] = addslashes(stripslashes($val));
	    }
	    else {
		$this->rcvP[$key] = $val;
	    }
	}
	// Netoyage des variables GET reçues
	foreach($_GET as $key => $val) {
	    if(!is_array($val)) {
		$this->rcvG[$key] = addslashes(stripslashes($val));
	    }
	}
	// Netoyage des fichiers reçus
	$this->rcvF = $_FILES;
	if (is_array($this->rcvF)) {
	    foreach ($this->rcvF as $key => $val) {
		if ($this->rcvF[$key]['tmp_name'] != '') {
		    $cleanedname = FileCleanFileName($this->rcvF[$key]['name']);
		    move_uploaded_file( $this->rcvF[$key]['tmp_name'], $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$cleanedname);
		    $this->rcvF[$key]['tmp_name'] = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$cleanedname;
		    $this->rcvF[$key]['name'] = $cleanedname;
		}
	    }
	}

    }
}

function PageContextVarCleanInput($item,$key) {
    $toto[$key] = addslashes(stripslashs($item));
    return $toto;
}

?>
