<?php

/**
 * This resource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class recoverpwdFormationStartxResource extends nosqlStoreResource implements IResource {

    static public $ConfDesc = '{"class_name":"recoverpwdStoreResource",
  "desc":"generate and send new pwd",
  "properties":
        [
		{
			"name":"search_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the param containing the id with which to search"
		},
                {
			"name":"value_key",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the param containing the value with which to search"
		},
                {
			"name":"pwd_encryption",
			"type":"string",
			"mandatory":"false",
			"desc":"encrytption algorithm to use on passwd"
		}                
	]
}';
      
    public function init() {
        parent::init();        
        if ($this->getConfig('search_key', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'search_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'search_key' attribute");
        }
        if ($this->getConfig('value_key', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'value_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'value_key' attribute");
        }
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        
        try {
            $store = $api->getStore($this->getConfig('store', 'nosql'));
            $key = $api->getInput()->getParam($this->getConfig('search_key', "search_key"));
            $value = $api->getInput()->getParam($this->getConfig('value_key', "value_key"));

            $it = $store->read($this->getConfig('store_dataset', 'sxapi.users'), array(
                $key => $value
            ));       
            $data = iterator_to_array($it, false);
            
            if (count($data) === 0) {
                throw new ResourceException("no corespondance");   
            }
            if (count($data) === 1 && ($key === "email" || $key === $this->getConfig('id_key', '_id')) ) {
                $new_pass = $this->generateNewPwd();
                switch ($this->getConfig('pwd_encryption', 'none')) {
                    case 'sha256':
                        $pass = hash("sha256", $new_pass);
                        break;
                    default:
                        $pass = $new_pass;
                        break;
                }
                $new_data = array($this->getConfig('pwd_key', 'pwd') => $pass);
                $store->update($this->getConfig('store_dataset', 'sxapi.users'), $key, $value, $new_data);
                $this->sendMail($data[0][$this->getConfig('id_key', '_id')], $new_pass, $data[0]['email']);
                return array(true, "Un nouveaux mot de passe a ete generer et vous as ete envoyer par mail", $data);
            }
            if (count($data) > 1) {
                $ret = array();
                foreach ($data as $elem) {
                    $ret[] = $elem[$this->getConfig('id_key', '_id')];
                }
                return array(true, "the same email is used in different account", $ret);
            }
            return array(true, "ok", $data);
        } 
        catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

    public function createAction() {
        return $this->readAction();
    }

    public function updateAction() {
        return $this->readAction();
    }

    public function deleteAction() {
        return $this->readAction();
    }
    
    private function generateNewPwd() { 
        $chars = implode("", array_merge(range('#', '}')));
        $clean_pass = substr(str_shuffle(str_repeat($chars, mt_rand(3, 10))), 1, 10);
        return $clean_pass;                
    }
    
    private function sendMail( $id, $new_pass, $email ) {     
        $message = "We have generated a new password for your account.\nYou can change it in your personal zone.\nYour new credentials are :   -login  :  ".$id."   -pwd  :  ".$new_pass."\nBest regards\n";
        $conf = $this->getStorage()->readOne("startx.resources", array("_id" => "mail"));
        $config = array_merge($this->getConfigs(), $conf);
        $class = $config['class'];
        $input = Api::getInstance()->getInput();
        $input->setParam('to', $email);
        $input->setParam('body', $message);
        $input->setParam('subject', "New Startx credentials");
        $mailer = new $class($config);
        $mailer->init();       
        $mailer->readAction();
    }
}

?>
