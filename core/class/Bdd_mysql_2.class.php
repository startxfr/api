<?php

/**
 * Classe qui gère les connexions au serveur MySQL
 * Classe codée objet, classe mère des *Model utilisés
 *
 * @author STARTX
 * @version 1.1
 */
class Bdd_mysql_2 {
    /** The pool used. */
    private $pool;
    /** The data base. */
    private $base;
    /** The serveur. */
    private $serveur;
    /** Username. */
    private $username;
    /** Password. */
    private $passwd;
    /** Define if the connection is persistant. */
    private $persistant;
    /** Connection. */
    private $connexion;
    /** Request to process */
    private $requete;

    /**
     * Constructor
     * @param int $pool BDatabase pool to use (Pool_1 by default). see bdd.ini for information.
     */
    function __construct( $pool = 1 ) {

	if($pool == '')
	    $this->setPool(1);
	else  $this->setPool($pool);
    }

    /**
     * Set if this connexion should be persistant.
     * @param bool $p TRUE for a persistant connection.
     */
    protected function setPersistant($p) {
	$this->persistant = $p;
    }

    /**
     * Set database connexion environment according to the given pool.
     * @param int $p number of database pool to use for this connexion.
     */
    protected function setPool($p) {
	if(!array_key_exists("DBPool_".$p,$GLOBALS))
	    $p = 1;
	$this->pool = "DBPool_".$p;
	$this->setPersistant($GLOBALS[$this->pool]['persistant']);
	$this->base = $GLOBALS[$this->pool]['base'];
	$this->serveur = $GLOBALS[$this->pool]['serveur'];
	$this->username = $GLOBALS[$this->pool]['login'];
	$this->passwd = $GLOBALS[$this->pool]['pass'];
    }

    /**
     * Set database to use.
     * @param string $b name of the database.
     */
    protected function setBase($b) {
	$this->base = $b;
    }

    /**
     * Set database to use.
     * @param string $s name of the server.
     */
    protected function setServeur($s) {
	$this->serveur = $s;
    }

    /**
     * Print error and exit.
     * @param string $txt description of the error
     * @param string $error error mysql
     */
    protected function dieAndPrint($txt, $error) {
	return "BDD::".$txt.$error;
    }

    /**
     * Execute connexion to database.
     * @param bool $reconnect TRUE to connect to the server if needed
     */
    protected function connect($reconnect = FALSE) {
	if(!is_resource($this->connexion) or ($reconnect == TRUE)) {
	    if($this->persistant)
		$this->connexion = mysql_pconnect($this->serveur, $this->username, $this->passwd);
	    else  $this->connexion = mysql_connect($this->serveur, $this->username, $this->passwd);
	}
	mysql_select_db($this->base, $this->connexion) or $this->dieAndPrint("WRONG_GIVEN_BASE::".$this->base, mysql_error());
	mysql_query("SET NAMES 'UTF8'");
    }

    /**
     * Create user request.
     * @param string $req	User request
     * @return string The request
     */
    public function makeRequeteFree($req) {
	return $this->requete = $req;
    }

    /**
     * Create insert request.
     * @param string $table table to fill
     * @param array $liste data to fill
     * @return string The request
     */
    public function makeRequeteInsert($table,$liste) {
	$top = "INSERT INTO `".$table."` ( ";
	foreach ($liste as $key => $val) {
	    $valclean1 = addslashes(trim($val));
	    if($valclean1 == '')
		$bottom .= ", NULL ";
	    else  $bottom .= ", '".$valclean1."' ";
	    $head   .= ", `".$key."` ";
	}
	$head   = substr($head, 1);
	$bottom = substr($bottom, 1);

	$this->requete = $top.$head.") VALUES (".$bottom.")";
	return $this->requete;
    }


    /**
     * Create update request.
     * @param string $table Table to fill
     * @param string $col_id Name of ID column
     * @param string $id Value of ID column
     * @param array $liste Values to update
     * @param string $autre More SQL
     * @return string The request
     */
    public function makeRequeteUpdate($table,$col_id,$id,$liste,$autre = "") {
	$top = "UPDATE `".$table."` SET ";
	foreach ($liste as $key => $val) {
	    $valclean1 = addslashes($val);
	    if($valclean1 == '')
		$head   .= " `".$key."` = NULL, ";
	    else  $head   .= " `".$key."` = '".$valclean1."', ";
	}
	$head   = substr($head, 0, -2);

	$this->requete = $top.$head." WHERE ".$col_id." = '".$id."' ".$autre;
	return $this->requete;
    }

    /**
     * Create Select request.
     * @param string $table Table to fill
     * @param string $col_id Name of ID column
     * @param string $id Value of ID column
     * @return string The Request
     */
    public function makeRequeteSelect($table,$col_id,$id) {
	$top = "SELECT * FROM `".$table."` WHERE ";

	$this->requete = $top.$col_id." = '".$id."'";
	return $this->requete;
    }

    /**
     * Create delete request.
     * @param string $table Table to fill
     * @param array $liste Column and values where to delete
     * @return string The request
     */
    public function makeRequeteDelete($table,$liste) {
	if(is_array($liste)) {
	    foreach ($liste as $key => $val)
		$head   .= " `".$key."` = '".$val."' AND";
	    $head   = substr($head, 0, -3);
	    $this->requete = "DELETE FROM `".$table."` WHERE ".$head;
	    return $this->requete;
	}
    }

    /**
     * Create select request.
     * @param array $table Table to fill
     * @param array $liste Columns and values for restrictions
     * @param string $other More SQL
     * @return string The request
     */
    public function makeRequeteAuto($table,$liste = "",$other = "") {
	if(is_array($table)) {
	    foreach ($table as $key => $val)
		$lestables   .= " `".$val."`,";
	    $lestables   = substr($lestables, 0, -1);
	}
	else  $lestables   = " `".$table."`";

	if(is_array($liste)) {
	    $lescrit = '';
	    foreach ($liste as $key => $val) {
		if ($val{0} == '`')
		    $lescrit   .= " `".$key."` = ".$val."` AND";
		else  $lescrit   .= " `".$key."` = '".$val."' AND";
	    }
	    $lescrit   = substr($lescrit, 0, -3);
	    $lescrit   = " WHERE ".$lescrit;
	}
	else  $lescrit   = " ";

	$this->requete = "SELECT * FROM ".$lestables." ".$lescrit.' '.$other;
	return $this->requete;
    }


    /**
     * Analyse table structure and return Array of data
     * @param array $table Table to fill
     * @param string $type Type of display to output
     * @param string $detail Rrow to get particular informations from
     * @return array Structure of this table
     */
    protected function AnalyseTableStructure($table,$type = "total",$detail = "") {
	if($table == "")
	    $table[]   = "page";
	if(!is_array($table)) {
	    $tmp = $table;
	    unset($table);
	    $table[]   = $tmp;
	}

	$this->connect();
	foreach($table as $key => $TabName) {
	    $this->requete = "SHOW COLUMNS FROM ".$TabName;
	    $resultat = $this->process();
	    $count = 0;
	    $nbre_chmp = count($resultat);
	    foreach($resultat as $id => $data) {
		$rowID = $data['Field'];
		$dataOut[$rowID]['nom'] = $data['Field'];
		$typeTmp = explode("(",$data['Type']);
		$dataOut[$rowID]['type'] = $typeTmp[0];
		$dataOut[$rowID]['taille'] = substr($typeTmp[1],0,-1);
		$dataOut[$rowID]['flag'] = $data['Extra'];
		if ($data['Key'] == 'PRI') {
		    $nom_chmp_key	= $dataOut[$rowID]['nom'];
		    $suf 		= explode("_",$nom_chmp_key);
		    $suffixe 	= $suf[1];
		}
		if ($data['Field'] == "nom_$suffixe")
		    $nom_chmp_titre = $dataOut[$rowID]['nom'];
		elseif ($data['Field'] == "titre_$suffixe")
		    $nom_chmp_titre = $dataOut[$rowID]['nom'];
		if ($data['Field'] == "color_$suffixe")
		    $nom_chmp_color = $dataOut[$rowID]['nom'];
		if (($type == "detail")and($rowID == $detail)) {
		    $suf 	= explode("_",$rowID);
		    $titre 	= $suf[0];
		    $suffixe = $suf[1].$suf[2];

		    $detail_champ['nom']    = $rowID;
		    $detail_champ['titre']  = $titre;
		    $detail_champ['type']   = $dataOut[$rowID]['type'];
		    $detail_champ['taille'] = $dataOut[$rowID]['taille'];
		    $detail_champ['flag']   = $dataOut[$rowID]['flag'];
		    $detail_champ['id_tab'] = $nom_chmp_key;
		    $detail_champ['suffixe']= $suffixe;
		}
		$count++;
	    }


	    if($type == "total") {
		$result[$TabName][0] = $nbre_chmp;
		$result[$TabName][1] = $dataOut;
	    }
	    elseif($type == "detail")
		$result[$TabName] = $detail_champ;
	    elseif($type == "") {
		if ($nom_chmp_titre  == "")
		    $nom_chmp_titre = "nom_$suffixe";
		$result[$TabName]['key']     = $nom_chmp_key;
		$result[$TabName]['titre']   = $nom_chmp_titre;
		$result[$TabName]['color']   = $nom_chmp_color;
		$result[$TabName]['suffixe'] = $suffixe;
	    }
	}

	if(count($table) == 1)
	    return $result[$table[0]];
	else  return $result;
    }


    /**
     * Analyse database structure and return Array of data
     * @param string $type Type of display to output
     * @return array Tables in the database
     */
    protected function AnalyseDatabaseStructure($type = "total") {
	$this->connect(FALSE);

	$this->requete = "SHOW TABLES FROM ".$this->base;
	$resultat = $this->process();
	$count = 0;
	$nbre_chmp = count($resultat);
	foreach($resultat as $id => $data)
	    $result[$id] = $data['Tables_in_'.$this->base];

	return $result;
    }


    /**
     * Make a sql request and return result.
     * @param bool $log FALSE for no log command (prevent for continious loop when DBlog is activated (only used in Logg::)
     * @return array result of sql request
     */

    public function process($log = true) {
	$this->connect();
	$generate_time_begin = microtime(true);
	$this->requete = trim($this->requete);
	$pos = strpos($this->requete, ';');
	$taille = strlen($this->requete);
	if(($pos+1 != $taille) && ($pos !== false) ) {
	    return $this->dieAndPrint('deux requètes en une !', 'Impossible de lancer plusieurs requètes à la fois : / '.$this->requete.' /');
	}
	$resultat = mysql_query($this->requete);
	$generate_time_end = microtime(true);
	$time = $generate_time_end-$generate_time_begin;
	if ($GLOBALS['LOG']['DisplayDebug']) {
	    $GLOBALS['LogBddProcess'][] = "Mysql::".$this->pool.":: ". $this->requete;
	    $GLOBALS['LogBddProcessTime'] = $GLOBALS['LogBddProcessTime']+$time;
	}

	if(!$this->persistant)
	    mysql_close($this->connexion);

	if (!$resultat)
	    echo $this->dieAndPrint("WRONG_GIVEN_REQUETE::".$this->requete, mysql_error());
	else {
	    if (is_resource($resultat))
		while($childresult = mysql_fetch_array($resultat,MYSQL_ASSOC))
		    $output[] = $childresult;

	    if(!isset($output))
		$output = array();

	    return $output;
	}
    }
    /**
     * Make a sql request and return result.
     * @param bool $log FALSE for no log command (prevent for continious loop when DBlog is activated (only used in Logg::)
     * @return array result of sql request
     */

    public function process2($log = true) {
	$this->connect();
	$generate_time_begin = microtime(true);
	$this->requete = trim($this->requete);
	$pos = strpos($this->requete, ';');
	$taille = strlen($this->requete);
	if(($pos+1 != $taille) && ($pos !== false)) {
	    return $this->dieAndPrint('deux requètes en une !', 'Impossible de lancer plusieurs requètes à la fois : / '.$this->requete.' /');
	}
	$resultat = mysql_query($this->requete);
	$generate_time_end = microtime(true);
	$time = $generate_time_end-$generate_time_begin;
	if ($GLOBALS['LOG']['DisplayDebug']) {
	    $GLOBALS['LogBddProcess'][] = "Mysql::".$this->pool.":: ". $this->requete;
	    $GLOBALS['LogBddProcessTime'] = $GLOBALS['LogBddProcessTime']+$time;
	}

	if (!$resultat)
	    return array(false,$this->dieAndPrint("WRONG_GIVEN_REQUETE::".$this->requete, mysql_error()));
	else {
	    if (is_resource($resultat))
		while($childresult = mysql_fetch_array($resultat,MYSQL_ASSOC))
		    $output[] = $childresult;

	    if(!isset($output))
		$output = array();

	    if(!$this->persistant)
		mysql_close($this->connexion);

	    return array(true,$output);
	}
    }
}
?>
