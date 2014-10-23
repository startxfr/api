<?php

/**
 * This resource is used to get user calendar's information's stored in google.
 *
 * @package  SXAPI.Resource.Google
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultGoogleResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class listEventGoogleResource extends proxyHttpResource implements IResource {


    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $store = $api->getStore($this->getConfig('store', 'curl'));
            if ($this->isConfig('url'))
                $store->setConfig('url', $this->getConfig('url'));
            $return = $store->read($this->getConfig('url_path', ''), $api->getInput()->getParams());
            $message = sprintf($this->getConfig('message_service_read', 'message service read'), $this->getConfig('url'));
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            return array(true, $message, $return);
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage());
        }
        return true;
    }


}

?>
