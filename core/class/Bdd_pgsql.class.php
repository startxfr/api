<?php
/*#########################################################################
#
#   name :       Bdd_pgsql.inc
#   desc :       library for HTML form creation
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


class Bdd_pgsql {
    /** The pool used. */
    var $pool;
    /** The data base. */
    var $base;
    /** The serveur. */
    var $serveur;
    /** Username. */
    var $username;
    /** Password. */
    var $passwd;
    /** Define if the connection is persistant. */
    var $persistant;
    /** Connection. */
    var $connexion;
    /** Request to process */
    var $requete;

    /**
     * Constructor.
     * @param $pool BDatabase pool to use (Pool_1 by default). see bdd.ini for information.
     *
     */
    function __construct( $pool = 1 ) {

	if($pool == '') {
	    $this->setPool(1);
	}
	else {
	    $this->setPool($pool);
	}
    }

    /**
     * Set if this connexion should be persistant.
     * @param $p Boolean, TRUE for a persistant connection.
     */
    function setPersistant($p) {
	$this->persistant = $p;
    }

    /**
     * Set database connexion environment according to the given pool.
     * @param $p Int: number of database pool to use for this connexion.
     */
    function setPool($p) {
	$this->pool = "DBPool_".$p;
	$this->setPersistant($GLOBALS[$this->pool]['persistant']);
	$this->base = $GLOBALS[$this->pool]['base'];
	$this->serveur = $GLOBALS[$this->pool]['serveur'];
	$this->username = $GLOBALS[$this->pool]['login'];
	$this->passwd = $GLOBALS[$this->pool]['pass'];
    }

    /**
     * Set database to use.
     * @param $b string: name of the database.
     */
    function setBase($b) {
	$this->base = $b;
    }

    /**
     * Set database to use.
     * @param $s string: name of the server.
     */
    function setServeur($s) {
	$this->serveur = $s;
    }

    /**
     * Print error and exit.
     * @param $txt String: description of the error
     * @param $error String: error mysql
     */
    function dieAndPrint($txt, $error) {
	return "BDD::".$txt.$error;
    }

    /** Execute connexion to server. */
    function connect() {
	if($this->persistant) {
	    $this->connexion = pg_pconnect("host=".$this->serveur." dbname=".$this->base." user=".$this->username." password=".$this->passwd)
		    or $this->dieAndPrint("WRONG_GIVEN_BASE::".$this->base, pg_last_error());
	}
	else {
	    $this->connexion = pg_connect("host=".$this->serveur." dbname=".$this->base." user=".$this->username." password=".$this->passwd)
		    or $this->dieAndPrint("WRONG_GIVEN_BASE::".$this->base, pg_last_error());
	}
    }

    /**
     * Create user request.
     * @param $req	String: User request
     */
    function makeRequeteFree($req) {
	return $this->requete = $req;
    }

    /**
     * Create insert request.
     * @param $table string,Array: table to fill *
     * @param $liste Array: array with data to fill
     */
    function makeRequeteInsert($table,$liste) {
	$top = "INSERT INTO \"".$table."\" ( ";
	foreach ($liste as $key => $val) {
	    $valclean1 = addslashes(stripslashes(trim($val)));
	    if($valclean1 == '') {
		$bottom .= ", NULL ";
	    }
	    else {
		$bottom .= ", '".$valclean1."' ";
	    }
	    $head   .= ", \"".$key."\" ";
	}
	$head   = substr($head, 1);
	$bottom = substr($bottom, 1);

	$this->requete = $top.$head.") VALUES (".$bottom.");";
	return $this->requete;
    }


    /**
     * Create update request.
     * @param $table String: table to fill *
     * @param $id Array: with row name and value:: array('row_name','value') *
     * @param $liste Array: with col_name to fill
     * @param $col_id String: name of ID column
     */
    function makeRequeteUpdate($table,$col_id,$id,$liste) {
	$top = "UPDATE \"".$table."\" SET ";
	foreach ($liste as $key => $val) {
	    $valclean1 = addslashes(stripslashes(trim($val)));
	    if($valclean1 == '') {
		$head   .= " \"".$key."\" = NULL, ";
	    }
	    else {
		$head   .= " ".$key." = '".$valclean1."', ";
	    }
	}
	$head   = substr($head, 0, -2);

	$this->requete = $top.$head." WHERE ".$col_id." = '".$id."'";
	return $this->requete;
    }

    /**
     * Create Select request.
     * @param $table String: table to fill
     * @param $id String: col ID
     * @param $col_id Sring: Column name
     */
    function makeRequeteSelect($table,$col_id,$id) {
	$top = "SELECT * FROM \"".$table."\" WHERE ";

	$this->requete = $top.$col_id." = '".$id."'";
	return $this->requete;
    }

    /**
     * Create delete request.
     * @param $table String: table to fill
     * @param $liste Array: with col_name to fill
     */
    function makeRequeteDelete($table,$liste) {
	if(is_array($liste)) {
	    foreach ($liste as $key => $val) {
		$head   .= " ".$key." = \"".$val."\" AND";
	    }
	    $head   = substr($head, 0, -3);
	    $this->requete = "DELETE FROM \"".$table."\" WHERE ".$head;
	    return $this->requete;
	}
    }

    /**
     * Create delete request.
     * @param $table String,Array: table to fill
     * @param $liste Array: with col_name to fill
     */
    function makeRequeteAuto($table,$liste = "",$other = "") {
	if(is_array($table)) {
	    foreach ($table as $key => $val) {
		$lestables   .= " \"".$val."\",";
	    }
	    $lestables   = substr($lestables, 0, -1);
	}
	else {
	    $lestables   = " \"".$table."\"";
	}
	if(is_array($liste)) {
	    foreach ($liste as $key => $val) {
		if ($val{0} == '`') {
		    $lescrit   .= " \"".$key."\" = '".$val."' AND";
		}
		else {
		    $lescrit   .= " ".$key." = '".$val."' AND";
		}
	    }
	    $lescrit   = substr($lescrit, 0, -3);
	    $lescrit   = " WHERE ".$lescrit;
	}
	else {
	    $lescrit   = " ";
	}

	$this->requete = "SELECT * FROM ".$lestables." ".$lescrit.' '.$other;
	return $this->requete;
    }


    /**
     * Analyse table structure and return Array of data
     * @param $table String,Array: table to fill
     * @param $type String: type of display to output
     */
    function AnalyseTableStructure($table,$type = "total") {
	if($table == "") {
	    $table[]   = "ref_page";
	}
	if(!is_array($table)) {
	    $tmp = $table;
	    unset($table);
	    $table[]   = $tmp;
	}

	$this->connect();
	foreach($table as $key => $TabName) {
	    $this->requete = "SHOW COLUMNS FROM ".$TabName;
	    $resultat = $this->process();
	    foreach($resultat as $id => $data) {
		if(count($table) == 1) {
		    $list[$data['Field']] = $data;
		}
		else {
		    $list[$TabName][$data['Field']] = $data;
		}
	    }
	}
	return $list;
    }

    /**
     * Make a sql request and return result.
     * @param $log Boolean: FALSE for no log command (prevent for continious loop when DBlog is activated (only used in Logg::)
     * @return result of sql requete
     */

    function process($log = true) {
	$this->connect();
	$generate_time_begin = microtime(true);
	$resultat = pg_query($this->requete);
	$generate_time_end = microtime(true);
	$time = $generate_time_end-$generate_time_begin;
	if ($GLOBALS['LOG']['DisplayDebug']) {
	    $GLOBALS['LogBddProcess'][] = "Mysql::".$this->pool.":: ". $this->requete;
	    $GLOBALS['LogBddProcessTime'] = $GLOBALS['LogBddProcessTime']+$time;
	}

	if(!$this->persistant)
	    pg_close($this->connexion);

	if (!$resultat)
	    echo $this->dieAndPrint("WRONG_GIVEN_REQUETE::".$this->requete, pg_last_error());
	else {
	    if (is_resource($resultat))
		while($childresult = pg_fetch_array($resultat,null,PGSQL_ASSOC))
		    $output[] = $childresult;

	    if(!isset($output))
		$output = array();

	    return $output;
	}
    }

    /**
     * Make a sql request and return result.
     * @param $log Boolean: FALSE for no log command (prevent for continious loop when DBlog is activated (only used in Logg::)
     * @return result of sql requete
     */

    function process2($log = true) {
	$this->connect();
	$generate_time_begin = microtime(true);
	$resultat = pg_query($this->requete);
	$generate_time_end = microtime(true);
	$time = $generate_time_end-$generate_time_begin;
	if ($GLOBALS['LOG']['DisplayDebug']) {
	    $GLOBALS['LogBddProcess'][] = "Mysql::".$this->pool.":: ". $this->requete;
	    $GLOBALS['LogBddProcessTime'] = $GLOBALS['LogBddProcessTime']+$time;
	}

	if (!$resultat)
	    return array(false,$this->dieAndPrint("WRONG_GIVEN_REQUETE::".$this->requete, pg_last_error()));
	else {
	    if (is_resource($resultat))
		while($childresult = pg_fetch_array($resultat,null,PGSQL_ASSOC))
		    $output[] = $childresult;

	    if(!isset($output))
		$output = array();

	    if(!$this->persistant)
		pg_close($this->connexion);

	    return array(true,$output);
	}
    }

}
?>
