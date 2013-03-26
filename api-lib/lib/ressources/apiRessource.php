<?php

/**
 * This class deliver test method
 * availables url are
 * - http://api.startx.fr/v1/api/test         -> all test methods
 * - http://api.startx.fr/v1/api/test/echo    -> return the input given (use GET with message=xxxx params. POST or PUT)
 * - http://api.startx.fr/v1/api/test/time    -> return the time, only with GET
 * - http://api.startx.fr/v1/api/test/error   -> return a test error message, only with GET
 */
class apiRessource extends readonlyRessource implements IRessource {

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('exposed_keys', '') == '')
            $api->logWarn(907, get_class($this) . " ressource config should contain the 'exposed_keys' attribute", $this->getRessourceTrace(__FUNCTION__, false));
        else {
            if (is_array($this->getConfig('exposed_keys')))
                $this->setConfig('exposed_keys', $this->getConfig('exposed_keys'));
            elseif (is_string($this->getConfig('exposed_keys')))
                $this->setConfig('exposed_keys', explode(',', $this->getConfig('exposed_keys')));
            else
                $this->setConfig('exposed_keys', array());
        }
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        $out = array();
        foreach ($this->getConfig('exposed_keys') as $key)
            $out[$key] = $api->getConfig($key);
        $message = sprintf($this->getConfig('message_service_read', 'message service read'), session_id());
        $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
        $api->getOutput()->renderOk($message, $out);
        return true;
    }

}

?>
