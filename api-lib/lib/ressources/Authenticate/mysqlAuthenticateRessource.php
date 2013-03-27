<?php

/**
 * This class deliver default informations (homepage) and provide default ressource methods
 * availables url are
 * - http://api.startx.fr/v1/       -> informations about the default ressource
 */
class mysqlAuthenticateRessource extends defaultAuthenticateRessource implements IRessource {

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $login = $api->getInput()->getParam($this->getConfig('id_param', "_id"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pass'));
            $user = $this->doAuthenticate($login, $pass);
            $api->getOutput()->renderOk(sprintf($this->getConfig('message_service_create','message service create'), $login), $user);
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $login = $api->getInput()->getParam($this->getConfig('id_param', "_id"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pass'));
            $user = $this->doAuthenticate($login, $pass);
            $api->getOutput()->renderOk(sprintf($this->getConfig('message_service_update','message service update'), $login), $user);
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    private function doAuthenticate($login, $pass) {
        $api = Api::getInstance();
        $store = $api->getStore($this->getConfig('store', 'users'));
        if ($login == '')
            throw new RessourceException(sprintf($this->getConfig('message_service_noid'), $this->getConfig('id_param', "_id")), 911);
        elseif ($pass == '')
            throw new RessourceException(sprintf($this->getConfig('message_service_nopwd'), $this->getConfig('pwd_param', 'pass')), 912);
        switch ($this->getConfig('pwd_encryption', 'none')) {
            case 'md5':
                $pass = md5($pass);
                break;
            default:
                break;
        }
        $data = $store->readOne($this->getConfig('collection', 'user'), array(
            $this->getConfig('id_field', "_id") => $login,
            $this->getConfig('pwd_field', 'pass') => $pass
                ));
        if ($data[$this->getConfig('id_field', "_id")] != '') {
            $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : User '" . $login . "' authenticated with '" . get_class($this) . "'", $this->getRessourceTrace(__FUNCTION__, false), 1);
            $api->getInput('session')->set('user', $login);
            return $api->getInput('user')->getAll();
        }
        else
            throw new RessourceException("either the login or the password you provided doesn't match an existing user");
    }

}

?>