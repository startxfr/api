<?php

/**
 * This resource return a simple message
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class messageResource extends readonlyResource implements IResource {

    public function readAction() {
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        $message = sprintf($this->getConfig('message_service_read','message service read'), @strlen($this->getConfig('message')));
        Api::getInstance()->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        Api::getInstance()->getOutput()->renderOk($message, $this->getConfig('message'));
        return true;
    }

}

?>
