<?php

/**
 * This ressource class is abstract and should not be used as it.
 * Developpers can create a new authentification ressource type by derivating from this class
 *
 * @package  SXAPI.Resource.Authenticate
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class defaultAuthenticateRessource extends defaultRessource implements IRessource {

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('message_service_read', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'message_service_read' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        if ($this->getConfig('message_service_delete', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'message_service_delete' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        return $this;
    }

    public function readAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource is not allowed", $this->getRessourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(910, $this->getConfig('message_service_read', 'could not read this node'), $this->getRessourceTrace(__FUNCTION__));
        return true;
    }

    public function deleteAction() {
        Api::getInstance()->logError(970, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource is not allowed", $this->getRessourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(910, $this->getConfig('message_service_delete', 'could not delete this node'), $this->getRessourceTrace(__FUNCTION__));
        return true;
    }

}

?>
