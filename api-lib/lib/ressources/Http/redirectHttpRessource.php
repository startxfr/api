<?php

/**
 * This class deliver default informations (homepage) and provide default ressource methods
 * availables url are
 * - http://api.startx.fr/v1/       -> informations about the default ressource
 */
class redirectHttpRessource extends defaultHttpRessource implements IRessource {

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('url', '') == '') {
            $api->logError(906, get_class($this) . " ressource config should contain the 'store' attribute", $this->getRessourceTrace(__FUNCTION__, false));
            throw new RessourceException(get_class($this) . " ressource config should contain the 'url' attribute");
        }
        if ($this->getConfig('message_service_read', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'message_service_read' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $configRecord = $this->getConfig('record');
            if (is_array($configRecord)) {
                try {
                    $store = $api->getStore($configRecord['store']);
                    $obj = new stdClass();
                    $obj->date = new MongoDate();
                    $obj->ip = $_SERVER['REMOTE_ADDR'];
                    $obj->redirect_to = $this->getConfig('url', $_SERVER['REFERER']);
                    $obj->request_referer = $_SERVER['REFERER'];
                    $obj->request_method = $api->getInput()->getMethod();
                    $obj->request_method = $api->getInput()->getMethod();
                    $obj->request_rooturl = $api->getInput()->getRootUrl();
                    $obj->request_path = $api->getInput()->getPath();
                    $obj->request_params = $api->getInput()->getParams();
                    $store->create($configRecord['collection'], $obj);
                } catch (Exception $exc) {
                    $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' when trying to record redirect trace : " . $exc->getMessage(), $exc);
                    if ($configRecord['fatal'] === true)
                        $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
                }
            }
            $message = sprintf($this->getConfig('message_service_read', 'message service read'), $this->getConfig('url'));
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
            if ($this->getConfig('redirect301', false))
                header("Location: " . $this->getConfig('url', $_SERVER['REFERER']), false, 301);
            else
                header("Location: " . $this->getConfig('url', $_SERVER['REFERER']));
            exit;
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

}

?>
