<?php

/**
 * This resource is used to authenticate a user by using a nosql database backend.
 *
 * @package  SXAPI.Resource.Authenticate
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultAuthenticateResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class nosqlAuthenticateResource extends defaultAuthenticateResource implements IResource {

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $login = $api->getInput()->getParam($this->getConfig('id_param', "_id"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pass'));
            $user = $this->doAuthenticate($login, $pass);
            $api->getOutput()->renderOk(sprintf($this->getConfig('message_service_create', 'message service create'), $login), $user);
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $login = $api->getInput()->getParam($this->getConfig('id_param', "_id"));
            $pass = $api->getInput()->getParam($this->getConfig('pwd_param', 'pass'));
            $user = $this->doAuthenticate($login, $pass);
            $api->getOutput()->renderOk(sprintf($this->getConfig('message_service_update', 'message service update'), $login), $user);
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage(),array(),401);
        }
        return true;
    }

    private function doAuthenticate($login, $pass) {
        $api = Api::getInstance();
        $store = $api->getStore($this->getConfig('store', 'users'));
        if ($login == '')
            throw new ResourceException(sprintf($this->getConfig('message_service_noid'), $this->getConfig('id_param', "_id")), 911);
        elseif ($pass == '')
            throw new ResourceException(sprintf($this->getConfig('message_service_nopwd'), $this->getConfig('pwd_param', 'pass')), 912);
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
        if (is_array($data) and $data[$this->getConfig('id_field', "_id")] == $login) {
            $api->logInfo(960, "User '" . $login . "' authenticated with '" . get_class($this) . "'", $this->getResourceTrace(__FUNCTION__, false), 1);
            $api->getInput('session')->set('user', $login);
            return $api->getInput('user')->getAll();
        }
        else
            throw new ResourceException("either the login or the password you provided doesn't match an existing user");
    }

}

?>
