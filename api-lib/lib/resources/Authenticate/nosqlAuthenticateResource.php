<?php

/**
 * This resource is used to authenticate a user by using a nosql database backend.
 * 

  Method GET, PUT and DELETE are forbidden, consequently the request will be rejected. Use POST.
  The request should feature a Json object of the following type:
 ~~~
  {
   "type":"post"
   "url":" _BASE_URL_ . 'auth.basic'"       < _BASE_URL_ being the root url of the api
   "data":
       {
           "format": "json",
           "app": "www",
           "login": " _your_login_ ",
           "pwd": " _your_pwd_ "
       }
  }
~~~
 * @class    nosqlAuthenticateResource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultAuthenticateResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 * 
 */
class nosqlAuthenticateResource extends defaultAuthenticateResource implements IResource {

    static public $ConfDesc = '{"class_name":"nosqlAuthenticateResource",
                                "desc":"nosql Authentication mechanism",
                                "properties":
	[
                {
			"name":"store",
			"type":"string",
			"mandatory":"true",
			"desc":"nosql store"
		},                
                {
			"name":"store_dataset",
			"type":"string",
			"mandatory":"true",
			"desc":"nosql dataset to query"
		},
                {
			"name":"store_id_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the id use in nosql"
		},
                {
			"name":"store_pwd_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the pwd key use in nosql"
		},
                {
			"name":"pwd_encryption",
			"type":"string",
			"mandatory":"false",
			"desc":"encrytption algorithm to use on passwd"
		},
                {
			"name":"pwd_param",
			"type":"string",
			"mandatory":"true",
			"desc":"name of pwd field in Param"
		},
                {
			"name":"login_param",
			"type":"string",
			"mandatory":"true",
			"desc":"name of login field in Param"
		}                
	]
}'
;
    
    public function __construct($config) {
        parent::__construct($config);
        $api = Api::getInstance();        
        if ($this->getConfig('store', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'store' attribute");
        }
        if ($this->getConfig('store_dataset', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store_dataset' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'store_dataset' attribute");
        }
        if ($this->getConfig('store_id_key', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store_id_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'store_id_key' attribute");
        }
        if ($this->getConfig('store_pwd_key', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store_pwd_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'store_pwd_key' attribute");
        }
        if ($this->getConfig('pwd_param', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'pwd_param' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'pwd_param' attribute");
        }
        if ($this->getConfig('login_param', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'login_param' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'login_param' attribute");
        }        
    }
    
    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $login = $api->getInput()->getParam($this->getConfig('login_param', "login"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pwd'));
            $user = $this->doAuthenticate($login, $pass);
            return array(true, sprintf($this->getConfig('message_service_create', 'message service create'), $login), $user);
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $login = $api->getInput()->getParam($this->getConfig('login_param', "login"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pwd'));
            $user = $this->doAuthenticate($login, $pass);
            return array(true, sprintf($this->getConfig('message_service_update', 'message service update'), $login), $user);
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

    private function doAuthenticate($login, $pass) {
        $api = Api::getInstance();
        $store = $api->getStore($this->getConfig('store', 'nosql'));
        if ($login == '')
            throw new ResourceException(sprintf($this->getConfig('message_service_noid'), $this->getConfig('login_param', "login")), 911);
        elseif ($pass == '')
            throw new ResourceException(sprintf($this->getConfig('message_service_nopwd'), $this->getConfig('pwd_param', 'pwd')), 912);
        switch ($this->getConfig('pwd_encryption', 'none')) {
            case 'sha256':
                $pass = hash("sha256", $pass);
                break;
            default:
                break;
        }
        $data = $store->readOne($this->getConfig('store_dataset', 'sxapi.users'), array(
            $this->getConfig('store_id_key', "_id") => $login,
            $this->getConfig('store_pwd_key', 'pwd') => $pass
                ));
        if (is_array($data) and $data[$this->getConfig('store_id_key', "_id")] == $login) {
            $api->logInfo(960, "User '" . $login . "' authenticated with '" . get_class($this) . "'", $this->getResourceTrace(__FUNCTION__, false), 1);
            $api->getInput('session')->set('user', $login);
            return $api->getInput('user')->getAll();
        }
        else
            throw new ResourceException("either the login or the password you provided doesn't match an existing user");
    }

}

?>
