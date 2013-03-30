<?php

/**
 * This ressource return informations about the currently used  API Document
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
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
