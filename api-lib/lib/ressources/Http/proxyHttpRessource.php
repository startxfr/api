<?php

/**
 * This class deliver default informations (homepage) and provide default ressource methods
 * availables url are
 * - http://api.startx.fr/v1/       -> informations about the default ressource
 */
class proxyHttpRessource extends defaultHttpRessource implements IRessource {

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('store', '') == '') {
            $api->logError(906, get_class($this) . " ressource config should contain the 'store' attribute", $this->getRessourceTrace(__FUNCTION__, false));
            throw new RessourceException(get_class($this) . " ressource config should contain the 'store' attribute");
        }
        if ($this->getConfig('url', '') == '') {
            $api->logError(906, get_class($this) . " ressource config should contain the 'store' attribute", $this->getRessourceTrace(__FUNCTION__, false));
            throw new RessourceException(get_class($this) . " ressource config should contain the 'url' attribute");
        }
        if ($this->getConfig('message_service_read', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'message_service_read' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        if ($this->getConfig('message_service_create', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'message_service_read' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        if ($this->getConfig('message_service_update', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'message_service_update' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        if ($this->getConfig('message_service_delete', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'message_service_delete' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $store = $api->getStore($this->getConfig('store', 'curl'));
            $store->setConfig('url', $this->getConfig('url'));
            $return = $store->read($this->getConfig('url_path', ''), $api->getInput()->getParams());
            $message = sprintf($this->getConfig('message_service_read', 'message service read'), $this->getConfig('url'));
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $return);
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $store = $api->getStore($this->getConfig('store', 'curl'));
            $store->setConfig('url', $this->getConfig('url'));
            $return = $store->create($this->getConfig('url_path', ''), $api->getInput()->getParams());
            $message = sprintf($this->getConfig('message_service_create', 'message service create'), $this->getConfig('url'));
            $api->logInfo(930, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $return);
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
            $store = $api->getStore($this->getConfig('store', 'curl'));
            $store->setConfig('url', $this->getConfig('url'));
            $return = $store->update($this->getConfig('url_path', ''), $this->getConfig('key', ''), $api->getInput()->getParam('val', ''), $api->getInput()->getParams());
            $message = sprintf($this->getConfig('message_service_update', 'message service update'), $this->getConfig('url'));
            $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $return);
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $store = $api->getStore($this->getConfig('store', 'curl'));
            $store->setConfig('url', $this->getConfig('url'));
            $return = $store->delete($this->getConfig('url_path', ''), $this->getConfig('key', ''), $api->getInput()->getParam('val', ''));
            $message = sprintf($this->getConfig('message_service_delete', 'message service delete'), $this->getConfig('url'));
            $api->logInfo(970, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $return);
        } catch (Exception $exc) {
            $api->logError(970, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

}

?>
