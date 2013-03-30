<?php

/**
 * This resource return informations about the currently used  API Document
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class apiResource extends readonlyResource implements IResource {

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('exposed_keys', '') == '')
            $api->logWarn(907, get_class($this) . " resource config should contain the 'exposed_keys' attribute", $this->getResourceTrace(__FUNCTION__, false));
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
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $out = array();
        foreach ($this->getConfig('exposed_keys') as $key)
            $out[$key] = $api->getConfig($key);
        $message = sprintf($this->getConfig('message_service_read', 'message service read'), session_id());
        $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        $api->getOutput()->renderOk($message, $out);
        return true;
    }

}

?>
