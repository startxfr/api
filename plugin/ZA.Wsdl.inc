<?php
/*#########################################################################
#
#   name :       SXA.Instance
#   desc :       SXA Instance module
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

loadPlugin('nusoap/nusoap');

class zunoWsdlServer {
    public $server = null;
    public $serviceName = null;


    function __construct($serviceName) {
	$this->serviceName = $serviceName;
	$this->server = new nusoap_server;
	$this->server->configureWSDL($this->serviceName, 'urn:'.$this->serviceName);
    }

    function registerAnonymousAction($action,$in,$out,$desc = '',$style = 'rpc',$use = 'encoded') {
	return $this->registerAction($action,$in,$out,$desc,$style,$use);
    }

    function registerAuthenticatedAction($action,$in,$out,$desc = '',$style = 'rpc',$use = 'encoded') {
	$ina = array_merge(array('token' => 'xsd:string'),$in);
	return $this->registerAction($action,$ina,$out,$desc,$style,$use);
    }

    function service() {
	global $HTTP_RAW_POST_DATA;
	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$this->server->service($HTTP_RAW_POST_DATA);
    }

    private function registerAction($action,$in,$out,$desc = '',$style = 'rpc',$use = 'encoded') {
	return $this->server->register($action,     // method name
		$in,                                    // input parameters
		$out,                                   // output parameters
		'urn:'.$this->serviceName,              // namespace
		'urn:'.$this->serviceName.'#'.$action,  // soapaction
		$style,$use,                            // style, use
		$desc                                   // documentation
	);
    }

    static function checkServerCredentials($token) {
	global $zunoLastError;
	if ($GLOBALS['zunoManagerWebService']['superBase_token'] == $token)
	    return true;
	else $zunoLastError = "ERREUR : Le jeton présenté ne corespond pas au jeton autorisé. ";
	Logg::loggerError('zunoWsdlServer::checkServerCredentials() ~  Accès refusé à '.$managerName.' : '.$zunoLastError,array($managerName,$managerKey),$file.'@'.$line);
	return false;
    }

    static function raiseFault($code,$actor,$message,$params = '',$file = __FILE__,$line = __LINE__) {
	Logg::loggerAlert($actor.'() ~  '.$code.' : '.$message,serialize($params),$file.'@'.$line);
	return new soap_fault($code,$actor,$message,$params);
    }
}






class zunoWsdlClient {
    public $client = null;
    public $message = null;
    public $reponse = null;
    protected $params = array();
    protected $serviceName = null;
    protected $serviceHost = null;


    function __construct($serviceName,$serviceHost = '') {
	try {
	    ini_set("soap.wsdl_cache_enabled",$GLOBALS['zunoWebService']['wsdlCacheEnabled']);
	    $this->serviceHost = ($serviceHost != '') ? $serviceHost : $GLOBALS['zunoWebService']['managerWebServicePath'];
	    $this->changeService($serviceName,true);
	}
	catch (Exception $err) {
	    return $this->raiseError("Erreur technique : ".$err->getMessage());
	}
    }

    public function changeService($serviceName,$resetParam = false) {
	$this->serviceName = $serviceName;
	return $this->openService($resetParam);
    }

    public function changeServiceHost($serviceHost,$resetParam = false) {
	$this->serviceHost = $serviceHost;
	return $this->openService($resetParam);
    }

    protected function openService($resetParam) {
	try {
	    $this->client = new nusoap_client($this->serviceHost.$this->serviceName.'.php?wsdl',true);
	    if($resetParam)
		$this->resetParam();
	    return true;
	}
	catch (Exception $err) {
	    return $this->raiseError("Erreur à la connexion : ".$err->getMessage());
	}
    }

    protected function raiseError($message) {
	Logg::loggerError('zunoWsdlClient::raiseError() ~ '.$message,$this->client->getDebug(),__FILE__.'@'.__LINE__);
	$this->message  = $message."\n";
	return false;
    }

    public function addParams($param) {
	if(is_array($param))
	    foreach ($param as $key => $val)
		$this->setParam($key,$val);
    }

    public function setParam($key,$val,$overwrite = true) {
	if(array_key_exists($key,$this->params) and !$overwrite)
	    return false;
	else $this->params[$key] = $val;
	return true;
    }

    public function removeParam($key) {
	if(array_key_exists($key,$this->params))
	    unset($this->params[$key]);
	return true;
    }

    public function resetParam($autoAddCredentials = true,$autoAddHttpCredentials = true) {
	$this->params = array();
	if($autoAddHttpCredentials and $GLOBALS['zunoManagerWebService']['wsdlClientHttpAccess'])
	    $this->client->setCredentials($GLOBALS['zunoManagerWebService']['wsdlClientHttpAccessUser'],
		    $GLOBALS['zunoManagerWebService']['wsdlClientHttpAccessPass'],
		    $GLOBALS['zunoManagerWebService']['wsdlClientHttpAccesstype']);
	if($autoAddCredentials) {
	    $this->setParam('token',md5($GLOBALS['DBPool_1']['pass']));
	    $this->setParam('code_ins',$GLOBALS['zunoWebService']['instance_code']);
	}
	return true;
    }

    public function call($action) {
	try {
	    $this->reponse = $this->client->call($action,$this->params);
	    Logg::loggerInfo('zunoWsdlClient::call() ~ Appel au webservice '.$this->serviceName.'.'.$action.' sur '.$this->serviceHost,array($this->params,$this->reponse),__FILE__.'@'.__LINE__);
	    if ($this->reponse->fault)
		return $this->raiseError("Erreur métier : ".$this->reponse->faultcode.'-'.$this->reponse->faultactor." : ".$this->reponse->faultstring);
	    else {
		$err = $this->client->getError();
		if ($err)
		    return $this->raiseError("Erreur technique : ".$err);
		$this->message .= $this->reponse->return."\n";
		return true;
	    }
	}
	catch (Exception $err) {
	    return $this->raiseError("Erreur technique : ".$err->getMessage());
	}
    }

    public function displayDebugOnScreen() {
	if ($this->client->fault) {
	    echo '<h2>Fault</h2>';
	    var_dump($this->client->faultcode,$this->client->faultactor,$this->client->faultstring,$this->client->detail);
	} else {
	    $err = $this->client->getError();
	    if ($err) {
		echo '<h2>Error</h2><pre>' . $err . '</pre>';
	    } else {
		echo '<h2>Result</h2>';
		var_dump($this->reponse);
	    }
	}
	// Display the request and response
	echo '<h2>Request</h2>';
	echo '<pre>' . htmlspecialchars($this->client->request, ENT_QUOTES) . '</pre>';
	echo '<h2>Response</h2>';
	echo '<pre>' . htmlspecialchars($this->client->response, ENT_QUOTES) . '</pre>';
	// Display the debug messages
	echo '<h2>Debug</h2>';
	echo '<pre>' . htmlspecialchars($this->client->debug_str, ENT_QUOTES) . '</pre>';
    }
}
?>
