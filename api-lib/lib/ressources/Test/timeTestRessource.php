<?php

/**
 * This ressource return the server time
 *
 * @package  SXAPI.Resource.Test
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class timeTestRessource extends readonlyRessource implements IRessource {

    public function readAction() {
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getConfigs(), 3);
        $message = $this->getConfig('message_service_read','message service read');
        Api::getInstance()->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
        Api::getInstance()->getOutput()->renderOk($message, date($this->getConfig('date_format')));
        return true;
    }

}

?>
