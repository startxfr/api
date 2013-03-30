<?php

/**
 * This ressource always return a fake error
 *
 * @package  SXAPI.Resource.Test
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class errorTestRessource extends readonlyRessource implements IRessource {

    public function readAction() {
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getConfigs(), 3);
        $input = Api::getInstance()->getInput();
        $message = $this->getConfig('message_service_read','message service read');
        Api::getInstance()->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : fake error with message " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
        Api::getInstance()->getOutput()->renderError(
                $this->getConfig('error_code'), $message, array(
            'code' => $this->getConfig('error_code'),
            'message' => $this->getConfig('message'),
            'params' => $input->getParams(),
            'path' => $input->getPath()
                )
        );
        return true;
    }

}

?>
