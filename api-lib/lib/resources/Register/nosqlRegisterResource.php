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

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $mode = $api->getInput()->getParam('mode');
            $data = $api->getInput()->getParam('data');           
            $sxa_store = $api->getStore($this->getConfig('store_sxa', "mysql"));
            $cont = $sxa_store->read('contact', array("mail_cont" => $data['email']));            
            $add_data = array();
            if ($data['enterprise']['nom_ent'] !== "") {
                $ent_data = $data['enterprise'];
                $id_ent = $sxa_store->create('entreprise', $ent_data);
                $add_data['entreprise_cont'] = $id_ent;
            }
            $cont_data = $this->_changeData($data, $add_data);
            $id_cont = $sxa_store->create('contact', $cont_data);            
            $return_data = array(array($id_cont), array($cont));
            if ($mode === 'oauth') {
                $data[$this->getConfig('id_field', "_id")] = $data['mail_cont'];
                $store = $api->getStore($this->getConfig('store', 'users'));
                $store->create($this->getConfig('collection', 'user'), $data);
                $api->getInput('session')->set('user', $data[$this->getConfig('id_field', "_id")]);
                $user = $api->getInput('user')->getAll();
                $return_data[0][] = $user;
                return array(true, sprintf($this->getConfig('message_service_create', 'message service create'), $data['_id']), $return_data);
            }
            else {
                $login = $api->getInput()->getParam($this->getConfig('id_param', "_id"));
                $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pass'));
                $user = $this->doAuthenticate($login, $pass, $data);
                $return_data[0][] = $user;
                return array(true, sprintf($this->getConfig('message_service_create', 'message service create'), $login), $return_data);
            }            
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

    public function updateAction() {
        return true;
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $login = $api->getInput()->getParam($this->getConfig('id_param', "_id"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pass'));
            $user = $this->doAuthenticate($login, $pass);
            return array(true, sprintf($this->getConfig('message_service_update', 'message service update'), $login), $user);
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

    private function doAuthenticate($login, $pass, $user_data) {
        $api = Api::getInstance();
        $store = $api->getStore($this->getConfig('store', 'users'));
                
        if ($login == '')
            throw new ResourceException(sprintf($this->getConfig('message_service_noid'), $this->getConfig('id_param', "_id")), 911);
        elseif ($pass == '')
            throw new ResourceException(sprintf($this->getConfig('message_service_nopwd'), $this->getConfig('pwd_param', 'pass')), 912);
        $data = $store->readOne($this->getConfig('collection', 'user'), array( $this->getConfig('id_field', "_id") => $login ));
        if (is_array($data) and $data[$this->getConfig('id_field', "_id")] == $login)
            throw new ResourceException(sprintf($this->getConfig('message_service_badid'), $this->getConfig('id_param', "_id")), 913);
        switch ($this->getConfig('pwd_encryption', 'none')) {
            case 'sha256':
                $pass = hash("sha256", $pass);
                break;
            default:
                break;
        }
        $user_data[$this->getConfig('id_field', "_id")] = $login;
        $user_data[$this->getConfig('pwd_field', 'pass')] = $pass;
        $store->create($this->getConfig('collection', 'user'), $user_data);       
        $api->logInfo(960, "User '" . $login . "' registered with '" . get_class($this) . "'", $this->getResourceTrace(__FUNCTION__, false), 1);
        $api->getInput('session')->set('user', $login);
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
            $new_data['nom_cont'] = "anon";
        return $new_data;
    }
}

?>
