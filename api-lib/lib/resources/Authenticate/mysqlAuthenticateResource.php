<?php

/**
 * This resource is used to authenticate a user by using a mysql database backend.
 *
 * @package  SXAPI.Resource.Authenticate
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultAuthenticateResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class mysqlAuthenticateResource extends defaultAuthenticateResource implements IResource {

    static public $ConfDesc = '{"class_name":"mysqlAuthenticateResource",
                                "desc":"mysql Authentication mechanism",
                                "propreties":
	[
                {
			"name":"mysql_store",
			"type":"string",
			"mandatory":"true",
			"desc":"mysql store"
		},                
                {
			"name":"mysql_table",
			"type":"string",
			"mandatory":"true",
			"desc":"mysql table to query"
		},
                {
			"name":"mysql_id_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the id key use in mysql"
		},
                {
			"name":"mysql_pwd_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the pwd key use in mysql"
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
        if ($this->getConfig('mysql_store', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'mysql_store' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'mysql_store' attribute");
        }
        if ($this->getConfig('mysql_collection', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'mysql_collection' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'mysql_collection' attribute");
        }
        if ($this->getConfig('mysql_id_key', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'mysql_id_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'mysql_id_key' attribute");
        }
        if ($this->getConfig('mysql_pwd_key', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'mysql_pwd_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'mysql_pwd_key' attribute");
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
            return array(true, sprintf($this->getConfig('message_service_create','message service create'), $login), $user);
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
            $login = $api->getInput()->getParam($this->getConfig('id_param', "login"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pwd'));
            $user = $this->doAuthenticate($login, $pass);
            return array(true, sprintf($this->getConfig('message_service_update','message service update'), $login), $user);
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

    private function doAuthenticate($login, $pass) {
        $api = Api::getInstance();
        $store = $api->getStore($this->getConfig('mysql_store', 'users'));
        if ($login == '')
            throw new ResourceException(sprintf($this->getConfig('message_service_noid'), $this->getConfig('id_param', "_id")), 911);
        elseif ($pass == '')
            throw new ResourceException(sprintf($this->getConfig('message_service_nopwd'), $this->getConfig('pwd_param', 'pass')), 912);
        switch ($this->getConfig('pwd_encryption', 'none')) {
            case 'sha256':
                $pass = hash("sha256", $pass);
                break;
            default:
                break;
        }
        $data = $store->readOne($this->getConfig('mysql_table', 'user'), array(
            $this->getConfig('mysql_id_key', "_id") => $login,
            $this->getConfig('mysql_pwd_key', 'pass') => $pass
                ));
        if ($data[$this->getConfig('mysql_id_key', "_id")] != '') {
            $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : User '" . $login . "' authenticated with '" . get_class($this) . "'", $this->getResourceTrace(__FUNCTION__, false), 1);
            $api->getInput('session')->set('user', $login);
            return $api->getInput('user')->getAll();
        }
        else
            throw new ResourceException("either the login or the password you provided doesn't match an existing user");
    }

}

?>
