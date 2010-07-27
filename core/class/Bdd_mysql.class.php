<?php
/*#########################################################################
#
#   name :       Bdd_mysql.inc
#   desc :       library for HTML form creation
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/


class Bdd_mysql {
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

        if($pool == '')
            $this->setPool(1);
        else  $this->setPool($pool);
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
    function connect($reconnect = FALSE) {
        if(!is_resource($this->connexion) or ($reconnect == TRUE)) {
            if($this->persistant)
                $this->connexion = mysql_pconnect($this->serveur, $this->username, $this->passwd);
            else  $this->connexion = mysql_connect($this->serveur, $this->username, $this->passwd);
        }
        $out = mysql_select_db($this->base, $this->connexion) or $this->dieAndPrint("WRONG_GIVEN_BASE::".$this->base, mysql_error());
        mysql_query("SET NAMES 'UTF8'");
        if(is_string($out))
            return false;
        return true;
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
        $top = "INSERT INTO `".$table."` ( ";
	$head = $bottom = '';
	foreach ($liste as $key => $val) {
	    $valclean1 = @mysql_real_escape_string(stripslashes(trim($val)));
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
     * @param $table String: table to fill *
     * @param $id Array: with row name and value:: array('row_name','value') *
     * @param $liste Array: with col_name to fill
     * @param $col_id String: name of ID column
     */
    function makeRequeteUpdate($table,$col_id,$id,$liste,$autre = "") {
        $top = "UPDATE `".$table."` SET ";
	$head = '';
	foreach ($liste as $key => $val) {
	    $valclean1 = @mysql_real_escape_string(stripslashes(trim($val)));
	    if($valclean1 == '')
		$head   .= " `".$key."` = NULL, ";
	    else $head   .= " `".$key."` = '".$valclean1."', ";
	}
	$head   = substr($head, 0, -2);

	$this->requete = $top.$head." WHERE ".$col_id." = '".@mysql_real_escape_string($id)."' ".$autre;
	return $this->requete;
    }

    /**
     * Create Select request.
     * @param $table String: table to fill
     * @param $id String: col ID
     * @param $col_id Sring: Column name
     */
    function makeRequeteSelect($table,$col_id,$id) {
        $top = "SELECT * FROM `".$table."` WHERE ";

	$this->requete = $top.$col_id." = '".@mysql_real_escape_string($id)."'";
	return $this->requete;
    }

    /**
     * Create delete request.
     * @param $table String: table to fill
     * @param $liste Array: with col_name to fill
     */
    function makeRequeteDelete($table,$liste) {
       if(is_array($liste)) {
	    $head = '';
	    foreach ($liste as $key => $val)
		$head   .= " `".$key."` = '".@mysql_real_escape_string($val)."' AND";
	    $head   = substr($head, 0, -3);
	    $this->requete = "DELETE FROM `".$table."` WHERE ".$head;
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
	    $lestables = '';
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
		else  $lescrit   .= " `".$key."` = '".@mysql_real_escape_string($val)."' AND";
	    }
	    $lescrit   = " WHERE ".substr($lescrit, 0, -3);
	}
	else  $lescrit   = " ";

	$this->requete = "SELECT * FROM ".$lestables." ".$lescrit.' '.$other;
	return $this->requete;
    }


    /**
     * Analyse table structure and return Array of data
     * @param $table String,Array: table to fill
     * @param $type String: type of display to output
     * @param $detail String: row to get particular informations from
     */
    function AnalyseTableStructure($table,$type = "total",$detail = "") {
        if($table == "")
            $table[]   = "ref_page";
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
     * @param $type String: type of display to output
     */
    function AnalyseDatabaseStructure($type = "total") {
        $this->connect(FALSE,FALSE);

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
     * @param $log Boolean: FALSE for no log command (prevent for continious loop when DBlog is activated (only used in Logg::)
     * @return result of sql requete
     */

    function process($log = true) {
        $this->connect();
        $generate_time_begin = microtime(true);
        $this->requete = trim($this->requete);
        $pos = strpos($this->requete, ';');
        $taille = strlen($this->requete);
//		if(($pos+1 != $taille) && ($pos !== false) )
//		{
//			return $this->dieAndPrint('deux requètes en une !', 'Impossible de lancer plusieurs requètes à la fois : / '.$this->requete.' / pos : '.$pos.' taille : '.$taille);
//		}
        $resultat = mysql_query($this->requete);
        $generate_time_end = microtime(true);
        $time = $generate_time_end-$generate_time_begin;
        if ($GLOBALS['LOG']['DisplayDebug']) {
            $GLOBALS['LogBddProcess'][] = "Mysql::".$this->pool.":: ". $this->requete;
            $GLOBALS['LogBddProcessTime'] = $GLOBALS['LogBddProcessTime']+$time;
        }

        if(!$this->persistant)
            mysql_close($this->connexion);

        if (!$resultat) {
            if($log)
                Logg::loggerError('Bdd_mysql::process() ~ erreur SQL '.$this->requete,mysql_error(),__FILE__.'@'.__LINE__);
            return $this->dieAndPrint("WRONG_GIVEN_REQUETE::".$this->requete, mysql_error());
        }
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
     * @param $log Boolean: FALSE for no log command (prevent for continious loop when DBlog is activated (only used in Logg::)
     * @return result of sql requete
     */

    function process2($log = true) {
        $this->connect();
        $generate_time_begin = microtime(true);
        $this->requete = trim($this->requete);
        if(substr($this->requete,-1, 1) == ';')
            $this->requete = substr($this->requete,0,-1);
        if(strpos($this->requete, '; SELECT') !== false or strpos($this->requete, ';SELECT') !== false or
                strpos($this->requete, '; INSERT') !== false or strpos($this->requete, ';INSERT') !== false or
                strpos($this->requete, '; UPDATE') !== false or strpos($this->requete, ';UPDATE') !== false or
                strpos($this->requete, '; DELETE') !== false or strpos($this->requete, ';DELETE') !== false or
                strpos($this->requete, '; ALTER')  !== false or strpos($this->requete, ';ALTER')  !== false or
                strpos($this->requete, '; DROP')   !== false or strpos($this->requete, ';DROP')   !== false) {
            if($log)
                Logg::loggerError('Bdd_mysql::process2() ~ protection anti injection SQL '.$this->requete,'Plusieurs requêtes SQL en une',__FILE__.'@'.__LINE__);
            return array(false,$this->dieAndPrint('deux requètes en une !', 'Impossible de lancer plusieurs requètes à la fois : / '.$this->requete).' /');
        }
        $resultat = mysql_query($this->requete);
        $generate_time_end = microtime(true);
        $time = $generate_time_end-$generate_time_begin;
        if ($GLOBALS['LOG']['DisplayDebug']) {
            $GLOBALS['LogBddProcess'][] = "Mysql::".$this->pool.":: ". $this->requete;
            $GLOBALS['LogBddProcessTime'] = $GLOBALS['LogBddProcessTime']+$time;
        }

        if (!$resultat) {
            if($log)
                Logg::loggerError('Bdd_mysql::process2() ~ erreur SQL '.$this->requete,mysql_error(),__FILE__.'@'.__LINE__);
            return array(false,$this->dieAndPrint("WRONG_GIVEN_REQUETE::".$this->requete, mysql_error()));
        }
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
