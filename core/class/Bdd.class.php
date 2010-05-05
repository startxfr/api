<?php
/*#########################################################################
#
#   name :       Bdd.inc
#   desc :       library for HTML form creation
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


class Bdd {
    /** The pool used. */
    var $pool;
    /** Type of database. */
    var $baseType;
    /** Connection to database interface. */
    var $DBIMap;
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
	$this->DBIMap->persistant = $p;
    }

    /**
     * Set database connexion environment according to the given pool.
     * @param $p Int: number of database pool to use for this connexion.
     */
    function setPool($p) {
	$this->pool = $p;
	$pool = "DBPool_".$this->pool;
	$this->baseType = $GLOBALS["DBPool_".$p]['type'];
	if($this->baseType == 'pgsql') {
	    $this->DBIMap = new Bdd_pgsql($p);
	}
	else {
	    $this->DBIMap = new Bdd_mysql($p);
	}
    }

    /**
     * Set database to use.
     * @param $b string: name of the database.
     */
    function setBase($b) {
	$this->DBIMap->base = $b;
    }

    /**
     * Set server to use.
     * @param $s string: name of the server.
     */
    function setServeur($s) {
	$this->DBIMap->serveur = $s;
    }

    /**
     * Create user request.
     * @param $req	String: User request
     */
    function makeRequeteFree($req) {
	return $this->requete = $this->DBIMap->makeRequeteFree($req);
    }

    /**
     * Create insert request.
     * @param $table string,Array: table to fill *
     * @param $liste Array: array with data to fill
     */
    function makeRequeteInsert($table,$liste) {
	return $this->requete = $this->DBIMap->makeRequeteInsert($table,$liste);
    }


    /**
     * Create update request.
     * @param $table String: table to fill *
     * @param $id Array: with row name and value:: array('row_name','value') *
     * @param $liste Array: with col_name to fill
     * @param $col_id String: name of ID column
     */
    function makeRequeteUpdate($table,$col_id,$id,$liste,$autre = "") {
	return $this->requete = $this->DBIMap->makeRequeteUpdate($table,$col_id,$id,$liste,$autre);
    }

    /**
     * Create Select request.
     * @param $table String: table to fill
     * @param $id String: col ID
     * @param $col_id Sring: Column name
     */
    function makeRequeteSelect($table,$col_id,$id) {
	return $this->requete = $this->DBIMap->makeRequeteSelect($table,$col_id,$id);
    }

    /**
     * Create delete request.
     * @param $table String: table to fill
     * @param $liste Array: with col_name to fill
     */
    function makeRequeteDelete($table,$liste) {
	return $this->requete = $this->DBIMap->makeRequeteDelete($table,$liste);
    }

    /**
     * Create delete request.
     * @param $table String,Array: table to fill
     * @param $liste Array: with col_name to fill
     */
    function makeRequeteAuto($table,$liste = "",$other = "") {
	return $this->requete = $this->DBIMap->makeRequeteAuto($table,$liste,$other);
    }

    /**
     * Analyse table structure and return Array of data
     * @param $table String,Array: table to fill
     * @param $type String: type of display to output
     */
    function AnalyseTableStructure($table,$type = "total") {
	return $this->DBIMap->AnalyseTableStructure($table,$type);
    }

    /**
     * Analyse database structure and return Array of data
     * @param $type String: type of display to output
     */
    function AnalyseDatabaseStructure($type = "total") {
	return $this->DBIMap->AnalyseDatabaseStructure($type);
    }

    /**
     * Make a sql request and return result.
     * @param $log Boolean: FALSE for no log command (prevent for continious loop when DBlog is activated (only used in Logg::)
     * @return result of sql requete
     */

    function process($log = true) {
	return $this->DBIMap->process($log);
    }

    /**
     * Make a sql request and return result.
     * @param $log Boolean: FALSE for no log command (prevent for continious loop when DBlog is activated (only used in Logg::)
     * @return result of sql requete
     */

    function process2($log = true) {
	return $this->DBIMap->process2($log);
    }
}
?>
