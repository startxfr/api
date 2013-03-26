<?php

/**
 * This class deliver test method
 * availables url are
 * - http://api.startx.fr/v1/api/test         -> all test methods
 * - http://api.startx.fr/v1/api/test/echo    -> return the input given (use GET with message=xxxx params. POST or PUT)
 * - http://api.startx.fr/v1/api/test/time    -> return the time, only with GET
 * - http://api.startx.fr/v1/api/test/error   -> return a test error message, only with GET
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
