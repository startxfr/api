<?php

/**
 * This ressource node deliver information about the current session. According to the configuration of this ressource node instance, you could
 * expose various kind of
 * availables url are
 * - http://api.startx.fr/v1/api/test         -> all test methods
 * - http://api.startx.fr/v1/api/test/echo    -> return the input given (use GET with message=xxxx params. POST or PUT)
 * - http://api.startx.fr/v1/api/test/time    -> return the time, only with GET
 * - http://api.startx.fr/v1/api/test/error   -> return a test error message, only with GET
 */
abstract class defaultSessionRessource extends defaultRessource implements IRessource {

    public function __construct($config) {
        parent::__construct($config);
    }

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('session_name', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'session_name' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        return $this;
    }

    /**
     * This method is used for all GET request made to a sessionRessource ressource
     * accoridng to the type, this ressource can have multiple forms
     * type 'info' : return the session informations (default)
     * type 'data' : return the data stored in this session
     * type 'user' : return the user data stored for this session. Could be null if anonymous
     * type 'app'  : return the application data stored for this session.
     * type 'application'  : alias of the 'app' type
     *
     * @return boolean
     */
    public function readAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource is not allowed", $this->getRessourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(910, $this->getConfig('message_service_read', 'could not read this node'), $this->getRessourceTrace(__FUNCTION__));
        return true;
    }

    public function createAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource is not allowed", $this->getRessourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(930, $this->getConfig('message_service_create', 'could not create this node'), $this->getRessourceTrace(__FUNCTION__));
    }

    public function updateAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource is not allowed", $this->getRessourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(950, $this->getConfig('message_service_update', 'could not update this node'));
        return true;
    }

    public function deleteAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource is not allowed", $this->getRessourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(970, $this->getConfig('message_service_delete', 'could not delete this node'));
        return true;
    }

    public function optionsAction() {
        return parent::optionsAction();
    }

}

?>
