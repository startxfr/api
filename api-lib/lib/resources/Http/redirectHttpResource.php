<?php

/**
 * This resource is used to redirect client to a new HTTP resource
 *
 * @package  SXAPI.Resource.Http
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultHttpResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class redirectHttpResource extends defaultHttpResource implements IResource {

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('url', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'url' attribute");
        }
        if ($this->getConfig('message_service_read', '') == '')
            $api->logWarn(907, get_class($this) . " resource config should contain the 'message_service_read' attribute", $this->getResourceTrace(__FUNCTION__, false));
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $configRecord = $this->getConfig('record');
            if (is_array($configRecord)) {
                try {
                    $store = $api->getStore($configRecord['store']);
                    $trace = $this->createHitTrace();
                    $store->create($configRecord['collection'], $trace);
                } catch (Exception $exc) {
                    $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' when trying to record redirect trace : " . $exc->getMessage(), $exc);
                    if ($configRecord['fatal'] === true)
                        return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
                }
            }
            $message = sprintf($this->getConfig('message_service_read', 'message service read'), $this->getConfig('url'));
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            if ($this->getConfig('redirect301', false))
                header("Location: " . $this->getConfig('url', $_SERVER['REFERER']), false, 301);
            else
                header("Location: " . $this->getConfig('url', $_SERVER['REFERER']));
            exit;
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

    protected function createHitTrace() {
        $input = Api::getInstance()->getInput();
        $trace = new stdClass();
        $trace->date = new MongoDate();
        $trace->ip = $_SERVER['REMOTE_ADDR'];
        $trace->redirect_to = $this->getConfig('url', $_SERVER['REFERER']);
        $trace->request_referer = $_SERVER['REFERER'];
        $trace->request_method = $input->getMethod();
        $trace->request_rooturl = $input->getRootUrl();
        $trace->request_path = $input->getPath();
        $trace->request_params = $input->getParams();
        return $trace;
    }

}

?>
