<?php

/**
 * This ressource is used to return an input message
 *
 * @package  SXAPI.Resource.Test
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class echoTestRessource extends readonlyRessource implements IRessource {

    public function readAction() {
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getConfigs(), 3);
        if ($this->getConfig('message') == '')
            $this->setConfig('message', 'your message : %s');
        $message = $this->getConfig('message_service_read','message service read');
        Api::getInstance()->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
        Api::getInstance()->getOutput()->renderOk($message, sprintf($this->getConfig('message'), Api::getInstance()->getInput()->getParam('message')));
        return true;
    }

}

?>
