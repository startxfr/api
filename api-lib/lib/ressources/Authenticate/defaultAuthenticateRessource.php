<?php

/**
 * This class deliver default informations (homepage) and provide default ressource methods
 * availables url are
 * - http://api.startx.fr/v1/       -> informations about the default ressource
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
