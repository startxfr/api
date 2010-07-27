<?php
/*#########################################################################
#
#   name :       Logg.inc
#   desc :       library for HTML form creation
#   categorie :  core module
#   ID :         $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/
error_reporting(E_ALL ^ E_NOTICE);
define('E_USER_INFO', 1500);

class Logg extends Singleton {
    protected $errortype = array (
            E_ERROR            => "Erreur PHP",
            E_WARNING          => "Alerte PHP",
            E_PARSE            => "Erreur PHP (parse)",
            E_NOTICE           => "Notice PHP",
            E_CORE_ERROR       => "Erreur PHP (core)",
            E_CORE_WARNING     => "Alerte PHP (core)",
            E_COMPILE_ERROR    => "Erreur PHP (compilation)",
            E_COMPILE_WARNING  => "Alerte PHP (compilation)",
            E_USER_ERROR       => "Erreur Zuno",
            E_USER_WARNING     => "Alerte Zuno",
            E_USER_NOTICE      => "Notice Zuno",
            E_USER_INFO        => "Info Zuno",
            E_STRICT           => "Notice PHP (syntax)"
    );
    public $conf = array ();
    public $confChannel = array ();

    /**
     * Initialise le singleton lors du premier appel
     * @see singleton::getInstance()
     */
    protected function initSingleton() {
	$this->conf = &$GLOBALS['LOG'];
	$this->confChannel['db']   = &$GLOBALS['LOG_DB'];
	$this->confChannel['file'] = &$GLOBALS['LOG_FILE'];
	$this->confChannel['sys']  = &$GLOBALS['LOG_SYS'];
	$this->confChannel['mail'] = &$GLOBALS['LOG_MAIL'];
	date_default_timezone_set('Europe/Paris');
    }

    /**
     * Constructeur rendu obligatoire pour rétro-compatibilité avec php < 5.3
     */
    public function __construct() {
        $this->initSingleton();
    }

    /**
     * destructeur pour des log plus clair (virable en prod)
     */
    public function  __destruct() {
	//Logg::loggerInfo('==============FIN================== ~ ',null,__FILE__.'@'.__LINE__);
    }

    /**
     * Méthode rendue obligatoire pour php < 5.3
     * @return self La classe
     */
    public static function getInstance () {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /**
     * LogSystem.
     *
     * Do log process into syslog
     *
     * @param $texte	String containing log information
     * @param $type		String containing type of information
     * @param $level	String containing level of information
     */
    protected function LogSystem($content) {
	if(in_array($content['level_log'],array(1,2,4,16,32,64,128,256,512,4096)))
	    $typlog = LOG_ERR;
	elseif(in_array($content['level_log'],array(8,1024,2048,8192,16384)))
	    $typlog = LOG_INFO;
	else $typlog = LOG_INFO;
	$texte = $this->conf['Name'].":";
	$texte.= $this->errortype[$content['level_log']]." : ";
	$texte.= $content['nom_log']." (";
	$texte.= $content['fichier_log']."-";
	$texte.= $content['component_log'].")";
	openlog($this->conf['Name'],LOG_PID | LOG_PERROR, LOG_LOCAL0);
	syslog($typlog,$texte);
	closelog();
    }

    /**
     * Log2File
     *
     * Do log process into file storage
     *
     * @param $texte	String containing log information
     * @param $type		String containing type of information
     */
    protected function Log2File($content) {
	$texte = date($this->confChannel['file']['file_ligneDate'],$content['date_log']).";";
	$texte.= $this->errortype[$content['level_log']].";";
	$texte.= $content['fichier_log'].";";
	$texte.= $content['component_log'].";";
	$texte.= $content['nom_log'].";";
	$texte.= $content['trace_log'].";";
	$type = 'log';
	if(in_array($content['level_log'],array(1,4,16,64,256,4096)))
	    $type.= '.error';
	elseif(in_array($content['level_log'],array(8,1024,2048,8192,16384)))
	    $type.= '.notice';
	elseif(in_array($content['level_log'],array(2,32,128,512)))
	    $type.= '.warning';
	else $type.= '.info';
	$URISuffix= ($this->confChannel['file']['file_path']{0} == '/') ? $this->confChannel['file']['file_path']:$GLOBALS['REP']['appli'].$this->confChannel['file']['file_path'];
	$fileName	= $type.".".date($this->confChannel['file']['file_nameDate'],time()).$this->confChannel['file']['file_extention'];
	$fileurl	= $URISuffix.$fileName;
	error_log($texte."\n", 3,$fileurl);

    }

    /**
     * Log2DB
     *
     * Do log process into database log storage
     *
     * @param $content	Array containing log information
     */
    protected function Log2Db($content) {
        $content['level_log'] = $this->errortype[$content['level_log']];
        $insert = new Bdd($this->confChannel['db']['base_pool']);
        $insert->makeRequeteInsert($this->confChannel['db']['base_table'],$content);
        $insert->process(false);
    }

    /**
     * Log2File
     *
     * Do log process into file storage
     *
     * @param $texte	String containing log information
     * @param $type		String containing type of information
     */
    protected function Log2Mail($content) {
	$titre = $this->conf['Name'].'-LOG : '.$this->errortype[$content['level_log']].' sur l\'instance '.$GLOBALS['zunoWebService']['instance_code'];
	$to    = $this->confChannel['mail']['addresse'];
	$texte = "-----------------------------------------------------------------------------
   channel:  ".$content['channel_log']."
      type:  ".$this->errortype[$content['level_log']]."
-------------------------+---------------------------------------------------
      date:  ".date('d/m/Y',time())."  |      Session:  ".$content['session_log']."
     heure:  ".date('h:i:s',time())."    |         user:  ".$_SESSION['user']['id']."
-------------------------+---------------------------------------------------
compostant:  ".$content['component_log']."
   fichier:  ".$content['fichier_log']."
     titre:  ".$content['nom_log']."
    detail:
-----------------------------------------------------------------------------
".str_replace(">",">\n",$content['trace_log'])."
-----------------------------------------------------------------------------";
        simple_mail($to,$texte,$titre,$to,'','text');
    }

    /**
     *
     * Log given information regarding criticity level and type of information
     * Analyse data respecting rules defined in log.ini
     *
     * @param $texte	information (must begin with information type: CORE,ERROR or INFO *
     * @param $level	criticity level. From 0 (high) to 2 (low)
     * @param $logchannel	Force to use the given channel instead of log.ini configuration
     */
    static function loggerAlert($texte, $vars = '', $filename = '', $logchannel = '') {
        $instance = self::getInstance();
        return $instance->processLog($texte,E_USER_WARNING,$vars,$filename,$logchannel);
    }

    /**
     *
     * Log given information regarding criticity level and type of information
     * Analyse data respecting rules defined in log.ini
     *
     * @param $texte	information (must begin with information type: CORE,ERROR or INFO *
     * @param $level	criticity level. From 0 (high) to 2 (low)
     * @param $logchannel	Force to use the given channel instead of log.ini configuration
     */
    static function loggerError($texte, $vars = '', $filename = '', $display = false, $logchannel = '') {
        $instance = self::getInstance();
        if(($display)or($instance->conf['DisplayError']))
            echo $instance->conf['Name'].' [ERROR] : '.$texte."<br>\r\n";
        return $instance->processLog($texte,E_USER_ERROR,$vars,$filename,$logchannel);
    }

    /**
     *
     * Log given information regarding criticity level and type of information
     * Analyse data respecting rules defined in log.ini
     *
     * @param $texte	information (must begin with information type: CORE,ERROR or INFO *
     * @param $level	criticity level. From 0 (high) to 2 (low)
     * @param $logchannel	Force to use the given channel instead of log.ini configuration
     */
    static function loggerNotice($texte, $vars = '', $filename = '', $logchannel = '') {
        $instance = self::getInstance();
        return $instance->processLog($texte,E_USER_NOTICE,$vars,$filename,$logchannel);
    }
    /**
     *
     * Log given information regarding criticity level and type of information
     * Analyse data respecting rules defined in log.ini
     *
     * @param $texte	information (must begin with information type: CORE,ERROR or INFO *
     * @param $level	criticity level. From 0 (high) to 2 (low)
     * @param $logchannel	Force to use the given channel instead of log.ini configuration
     */
    static function loggerInfo($texte, $vars = '', $filename = '', $logchannel = '') {
        $instance = self::getInstance();
        return $instance->processLog($texte,E_USER_INFO,$vars,$filename,$logchannel);
    }

    /**
     *
     * Log given information regarding criticity level and type of information
     * Analyse data respecting rules defined in log.ini
     *
     * @param $texte	information (must begin with information type: CORE,ERROR or INFO *
     * @param $level	criticity level. From 0 (high) to 2 (low)
     * @param $logchannel	Force to use the given channel instead of log.ini configuration
     */
    static function logger($texte, $level = 256,$vars = '', $filename = '', $logchannel = '') {
        $instance = self::getInstance();
        return $instance->processLog($texte,$level,$vars,$filename,$logchannel);
    }
    /**
     *
     * Log given information regarding criticity level and type of information
     * Analyse data respecting rules defined in log.ini
     *
     * @param $texte	information (must begin with information type: CORE,ERROR or INFO *
     * @param $level	criticity level. From 0 (high) to 2 (low)
     * @param $logchannel	Force to use the given channel instead of log.ini configuration
     */
    protected function processLog($texte, $level = 256,$vars = '', $filename = '', $logchannel = '') {

	if($this->confChannel['file']['activate'] or
		$this->confChannel['sys']['activate'] or
		$this->confChannel['db']['activate'] or
		$this->confChannel['mail']['activate'] or
		$logchannel != '') {
	    $out = array();
	    $out['date_log']   = microtime(true);
	    $out['fichier_log']= $filename;
	    $out['level_log']  = ($level == '') ? 256 : $level;
	    $out['session_log']= session_id();
	    $out['channel_log']= (array_key_exists('currentChannel',$GLOBALS)) ? $GLOBALS['currentChannel'] : '';
	    $r = explode("~", $texte,2);
	    if(count($r) == 2) {
		$out['component_log'] = $r[0];
		$out['nom_log'] = $r[1];
	    }
	    else $out['nom_log'] = $texte;
	    if (is_array($vars))
		$out['trace_log'] = wddx_serialize_value($vars,"trace");
	    elseif ($vars != '')
		$out['trace_log'] = $vars;

	    //Process log according to rules defined in log.ini or to the given log channel
	    if($logchannel == '') {
		if($this->confChannel['file']['activate'] and in_array($out['level_log'],explode(',',$this->confChannel['file']['level'])))
		    Logg::Log2File($out);
		if($this->confChannel['sys']['activate'] and in_array($out['level_log'],explode(',',$this->confChannel['sys']['level'])))
		    Logg::LogSystem($out);
		if($this->confChannel['db']['activate'] and in_array($out['level_log'],explode(',',$this->confChannel['db']['level'])))
		    Logg::Log2Db($out);
		if($this->confChannel['mail']['activate'] and in_array($out['level_log'],explode(',',$this->confChannel['mail']['level'])))
		    Logg::Log2Mail($out);
	    }
	    else {
		if($logchannel == 'file')
		    Logg::Log2File($out);
		elseif($logchannel == 'system')
		    Logg::LogSystem($out);
		elseif($logchannel == 'db')
		    Logg::Log2Db($out);
		elseif($logchannel == 'mail')
		    Logg::Log2Mail($out);
	    }
	}
    }

    /**
     * Enregistre un log depuis les erreur retournées par PHP
     * @param <type> $errno numero d'erreur
     * @param <type> $errmsg message d'erreur
     * @param <type> $filename fichie rayant généré l'erreur
     * @param <type> $linenum ligne à laquelle est survenue l'erreur
     * @param <type> $vars variables de contexte de l'erreur
     */
    public function logTriggeredError($errno,$errmsg,$filename,$linenum,$vars) {
        $this->processLog($errmsg,$errno,$vars,$filename.'@'.$linenum);
    }


    static function displayDebug() {
        $instance = self::getInstance();
        $DumpGlobals = $GLOBALS;
        $GLOBALS['variableSets'] = array("Get:" => $_GET,
                "Post:" => $_POST,
                "Fichier:" => $_FILES,
                "Cookies:" => $_COOKIE,
                "Server:" => $_SERVER);
        $GLOBALS['variableSets1'] = array("Get:" => $_GET,
                "Post:" => $_POST,
                "Fichier:" => $_FILES,
                "Cookies:" => $_COOKIE);

        echo '<div id="debug"><pre>';
        $generate_time_end = microtime(true);
        $time =$generate_time_end-$GLOBALS['generate_time_start'];
        echo "/*========================================================================+\r\n";
        echo "| GENERATED IN $time seconds                                   |\r\n";
        echo "+=========================================================================+\r\n\r\n";
        if (($instance->conf['DisplayDebugType'] == 'STAT')or
                ($instance->conf['DisplayDebugType'] == 'QUIET')or
                ($instance->conf['DisplayDebugType'] == 'NORMAL')or
                ($instance->conf['DisplayDebugType'] == 'TCHAT')or
                ($instance->conf['DisplayDebugType'] == 'VERBOSE')) {
            echo "/*========================================================================+\r\n";
            echo "| Treatment Statistics informations                                       |\r\n";
            echo "+=========================================================================+\r\n|\r\n";
            echo "| Incomming GET => ".count($_GET)."\r\n";
            echo "| Incomming POST => ".count($_POST)."\r\n";
            echo "| Incomming FILES => ".count($_FILES)."\r\n";
            echo "| COOKIE send => ".count($_COOKIE)."\r\n";
            echo "| SESSION data => ".count($_SESSION)."\r\n";
            echo "| XSL Transformations => ".@count($GLOBALS['LogXsltProcess'])."\r\n";
            echo "| Database Queries => ".@count($GLOBALS['LogBddProcess'])."\r\n";
            echo "| SVN Queries => ".@count($GLOBALS['LogSVNProcess'])."\r\n";
            echo "\r\n";
        }

        if (($instance->conf['DisplayDebugType'] == 'QUIET')or
                ($instance->conf['DisplayDebugType'] == 'NORMAL')or
                ($instance->conf['DisplayDebugType'] == 'TCHAT')or
                ($instance->conf['DisplayDebugType'] == 'VERBOSE')) {
            echo "/*========================================================================+\r\n";
            echo "| SESSION INFORMATION                                                     |\r\n";
            echo "+=========================================================================+\r\n";
            print_r ($_SESSION);
        }
        if (($instance->conf['DisplayDebugType'] == 'NORMAL')or
                ($instance->conf['DisplayDebugType'] == 'VERBOSE')) {
            echo "/*========================================================================+\r\n";
            echo "| XSLT Processing INFORMATION                                             |\r\n";
            echo "+=========================================================================+\r\n";
            if(isset($GLOBALS['LogXsltProcess'])) print_r($GLOBALS['LogXsltProcess']);
            else 'no XSL transformation raised during this execution';
            echo "Durée totale des requetes:".$GLOBALS['LogXsltProcessTime']."\r\n";
        }
        if (($instance->conf['DisplayDebugType'] == 'NORMAL')or
                ($instance->conf['DisplayDebugType'] == 'TCHAT')or
                ($instance->conf['DisplayDebugType'] == 'VERBOSE')) {
            echo "/*========================================================================+\r\n";
            echo "| Database Processing INFORMATION                                             |\r\n";
            echo "+=========================================================================+\r\n";
            if(isset($GLOBALS['LogBddProcess'])) print_r($GLOBALS['LogBddProcess']);
            else 'no Database query raised during this execution';
            echo "Durée totale des requetes:".$GLOBALS['LogBddProcessTime']."\r\n";

        }
        if (($instance->conf['DisplayDebugType'] == 'NORMAL')or
                ($instance->conf['DisplayDebugType'] == 'TCHAT')or
                ($instance->conf['DisplayDebugType'] == 'VERBOSE')) {
            echo "/*========================================================================+\r\n";
            echo "| SVN Command Processing INFORMATION                                      |\r\n";
            echo "+=========================================================================+\r\n";
            if(isset($GLOBALS['LogSVNProcess'])) print_r($GLOBALS['LogSVNProcess']);
            else 'no SVN Command raised during this execution';
        }
        if (($instance->conf['DisplayDebugType'] == 'TCHAT')or
                ($instance->conf['DisplayDebugType'] == 'VERBOSE')) {
            foreach ( $GLOBALS['variableSets'] as $setName => $GLOBALS['variableSets']) {
                if (isset($GLOBALS['variableSets'])) {
                    echo "/*========================================================================+\r\n";
                    echo "| ".$setName;
                    echo "\r\n+=========================================================================+\r\n";
                    array_walk($GLOBALS['variableSets'],'printElementHtml');
                    echo "\r\n";
                }
            }
        }
        elseif (($instance->conf['DisplayDebugType'] == 'QUIET')or
                ($instance->conf['DisplayDebugType'] == 'NORMAL')) {
            foreach ( $GLOBALS['variableSets1'] as $setName => $GLOBALS['variableSets1']) {
                if (isset( $GLOBALS['variableSets1'])) {
                    echo "/*========================================================================+\r\n";
                    echo "| ".$setName;
                    echo "\r\n+=========================================================================+\r\n\r\n";
                    array_walk($GLOBALS['variableSets1'],'printElementHtml');
                    echo "\r\n";
                }
            }
        }

        if ($instance->conf['DisplayDebugType'] == 'VERBOSE') {
            echo "/*========================================================================+\r\n";
            echo "| GLOBAL DUMP                                                             |\r\n";
            echo "+=========================================================================+\r\n\r\n";
            print_r ($DumpGlobals);
        }
        echo '</pre></div>';
    }
}






function printElementHtml( $value, $key ) {
    echo "\t| ".$key." => ";
    print_r( $value );
    echo "\r\n";
}







$GLOBALS['LogBddProcess'] =
        $GLOBALS['LogSVNProcess'] =
        $GLOBALS['LogXsltProcess'] = array();
$GLOBALS['LogBddProcessTime'] =
        $GLOBALS['LogXsltProcessTime'] = 0;


if(!$GLOBALS['LOG']['DisplayError'])
    error_reporting(0);

// Fonction spéciale de gestion des erreurs
function zunoErrorHandler($errno,$errmsg,$filename,$linenum,$vars) {
    if(class_exists('Logg')) {
	$logger= Logg::getInstance();
	$logger->logTriggeredError($errno,$errmsg,$filename,$linenum,$vars);
    }
}
$old_error_handler = set_error_handler("zunoErrorHandler");

?>
