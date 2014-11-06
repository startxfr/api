<?php

/**
 * This resource is used to register a user by using a nosql database backend.
 * 

  Method GET, PUT and DELETE are forbidden, consequently the request will be rejected. Use POST.
  The request should feature a Json object of the following type:
 ~~~
  {
   "type":"post"
   "url":" _BASE_URL_ . 'auth.register'"       < _BASE_URL_ being the root url of the api
   "data":
       {
           "format": "json",
           "app": "www",
           "login": " _your_login_ ",
           "pwd": " _your_pwd_ "
       }
  }
~~~
 * @class    nosqlRegisterResource
 * @author   Dev Team <dev@startx.fr>
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 * 
 */
class nosqlRegisterResource extends defaultAuthenticateResource implements IResource {

    static public $ConfDesc = '{"class_name":"nosqlRegisterResource",
                                "desc":"create user in external store and create a link on it in backend database",
                                "properties":
	[
		{
			"name":"external_store",
			"type":"string",
			"mandatory":"true",
			"desc":"external store"
		},
                {
			"name":"external_store_dataset",
			"type":"string",
			"mandatory":"true",
			"desc":"external dataset to query"
		},
                {
			"name":"backend_store",
			"type":"string",
			"mandatory":"true",
			"desc":"backend store"
		},                
                {
			"name":"backend_store_dataset",
			"type":"string",
			"mandatory":"true",
			"desc":"backend dataset to query"
		},
                {
			"name":"backend_store_id_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the id use in backend store"
		},
                {
			"name":"backend_store_pwd_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the pwd key use in backend store"
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
        if ($this->getConfig('external_store', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'external_store' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'external_store' attribute");
        }
        if ($this->getConfig('external_store_dataset', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'external_store_dataset' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'external_store_dataset' attribute");
        }
        if ($this->getConfig('backend_store', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'backend_store' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'backend_store' attribute");
        }
        if ($this->getConfig('backend_store_dataset', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'backend_store_dataset' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'backend_store_dataset' attribute");
        }
        if ($this->getConfig('backend_store_id_key', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'backend_store_id_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'backend_store_id_key' attribute");
        }
        if ($this->getConfig('backend_store_pwd_key', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'backend_store_pwd_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'backend_store_pwd_key' attribute");
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
            $mode = $api->getInput()->getParam('mode');
            $data = $api->getInput()->getParam('data');  
            $data['nom_cont'] = strtoupper($data['nom_cont']);
            $data['prenom_cont'] = ucfirst(strtolower($data['prenom_cont']));
            $data['enterprise']['nom_ent'] = strtoupper($data['enterprise']['nom_ent']);
            $sxa_store = $api->getStore($this->getConfig('external_store', "mysql"));
            $cont = $sxa_store->read($this->getConfig('external_store_dataset', "contact"), array("mail_cont" => $data['mail_cont']));  
            $add_data = array();
            if ($data['enterprise']['nom_ent'] !== "") {
                $ent_data = $data['enterprise'];
                $id_ent = $sxa_store->create('entreprise', $ent_data);
                $add_data['entreprise_cont'] = $id_ent;
            }            
            $cont_data = $this->_changeData($data, $add_data);
            $id_cont = $sxa_store->create($this->getConfig('external_store_dataset', "contact"), $cont_data);            
            $return_data = array(array($id_cont), array($cont));            
            $user = $this->doNosqlRegister($data, $mode, $id_cont);
            $return_data[0][] = $user;
            return array(true, sprintf($this->getConfig('message_service_create', 'message service create'), $user['_id']), $return_data);                       
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

    private function doNosqlRegister($data, $mode, $id_cont) {
        $api = Api::getInstance();
        $user_data = array();
        if ($mode === 'oauth') {
            $user_data[$this->getConfig('backend_store_id_key', "_id")] = $data['mail_cont'];
            $user_data['refresh_token'] = $data['refresh_token'];
        }
        else {
            $login = $api->getInput()->getParam($this->getConfig('login_param', "login"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pwd'));
            switch ($this->getConfig('pwd_encryption', 'none')) {
                case 'sha256':
                    $pass = hash("sha256", $pass);
                    break;
                default:
                    break;
            }
            $user_data[$this->getConfig('backend_store_id_key', "_id")] = $login;
            $user_data[$this->getConfig('backend_store_pwd_key', 'pwd')] = $pass;
        }
        $user_data['role'] = $data['role'];
        $user_data['email'] = $data['mail_cont'];
        $user_data['id_cont'] = $id_cont;
        $user_name = $data['prenom_cont'] . " " . $data['nom_cont'];
        if ($user_name === " ")
            $user_name = $user_data[$this->getConfig('backend_store_id_key', "_id")];
        $user_data['username'] = $user_name;
        $store = $api->getStore($this->getConfig('backend_store', 'users'));
        $store->create($this->getConfig('backend_store_dataset', 'user'), $user_data);         
        $api->getInput('session')->set('user', $user_data[$this->getConfig('backend_store_id_key', "_id")]);                                   
        $api->logInfo(960, "User '" . $user_data[$this->getConfig('backend_store_id_key', "_id")] . "' registered with '" . get_class($this) . "'", $this->getResourceTrace(__FUNCTION__, false), 1);        
        return $api->getInput('user')->getAll();                
    }
    
    private function _changeData($data, $add_data = array()) {
        $new_data = array();
        foreach($data as $key => $value) {
            if ($key !== "refresh_token" && $key !== "role" && $key !== "enterprise")
            $new_data[$key] = $value;
        }
        foreach ($add_data as $key => $value) {
            $new_data[$key] = $value;
        }
        if ($new_data['nom_cont'] === "")
            $new_data['nom_cont'] = Api::getInstance()->getInput()->getParam($this->getConfig('login_param', "login"));
        return $new_data;
    }
}

?>
