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
            $data['nom_cont'] = strtoupper($data['nom_cont']);
            $data['prenom_cont'] = ucfirst(strtolower($data['prenom_cont']));
            $data['enterprise']['nom_ent'] = strtoupper($data['enterprise']['nom_ent']);
            $sxa_store = $api->getStore($this->getConfig('store_sxa', "mysql"));
            $cont = $sxa_store->read('contact', array("mail_cont" => $data['mail_cont']));  
            $add_data = array();
            if ($data['enterprise']['nom_ent'] !== "") {
                $ent_data = $data['enterprise'];
                $id_ent = $sxa_store->create('entreprise', $ent_data);
                $add_data['entreprise_cont'] = $id_ent;
            }            
            $cont_data = $this->_changeData($data, $add_data);
            $id_cont = $sxa_store->create('contact', $cont_data);            
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
            $user_data[$this->getConfig('id_field', "_id")] = $data['mail_cont'];
            $user_data['refresh_token'] = $data['refresh_token'];
        }
        else {
            $login = $api->getInput()->getParam($this->getConfig('id_param', "_id"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pass'));
            switch ($this->getConfig('pwd_encryption', 'none')) {
                case 'sha256':
                    $pass = hash("sha256", $pass);
                    break;
                default:
                    break;
            }
            $user_data[$this->getConfig('id_field', "_id")] = $login;
            $user_data[$this->getConfig('pwd_field', 'pass')] = $pass;
        }
        $user_data['role'] = $data['role'];
        $user_data['email'] = $data['mail_cont'];
        $user_data['id_cont'] = $id_cont;
        $user_name = $data['prenom_cont'] . " " . $data['nom_cont'];
        if ($user_name === " ")
            $user_name = $user_data[$this->getConfig('id_field', "_id")];
        $user_data['username'] = $user_name;
        $store = $api->getStore($this->getConfig('store', 'users'));
        $store->create($this->getConfig('collection', 'user'), $user_data);         
        $api->getInput('session')->set('user', $user_data[$this->getConfig('id_field', "_id")]);                                   
        $api->logInfo(960, "User '" . $user_data[$this->getConfig('id_field', "_id")] . "' registered with '" . get_class($this) . "'", $this->getResourceTrace(__FUNCTION__, false), 1);        
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
