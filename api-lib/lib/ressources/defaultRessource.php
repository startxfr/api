<?php

/**
 * This ressource class is abstract and should not be used as it.
 * Developpers can create a new authentification ressource type by derivating from this class
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      Configurable
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class defaultRessource extends Configurable implements IRessource {

    public function __construct($config) {
        parent::__construct($config);
    }

    public function init() {
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logError(910, "Performing '" . __FUNCTION__ . "' on 'defaultRessource' ressource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getRessourceTrace(__FUNCTION__,false));
        $api->getOutput()->renderError(910, "You can't perform a readAction with '" . get_class($this) . "' ressource. " . get_class($this) . " should implement readAction method before using it.", $this->getRessourceTrace(__FUNCTION__));
        return true;
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logError(930, "Performing '" . __FUNCTION__ . "' on 'defaultRessource' ressource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getRessourceTrace(__FUNCTION__,false));
        $api->getOutput()->renderError(930, "You can't perform a createAction with '" . get_class($this) . "' ressource. " . get_class($this) . " should implement createAction method before using it.", $this->getRessourceTrace(__FUNCTION__));
        return true;
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logError(950, "Performing '" . __FUNCTION__ . "' on 'defaultRessource' ressource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getRessourceTrace(__FUNCTION__,false));
        $api->getOutput()->renderError(950, "You can't perform a updateAction with '" . get_class($this) . "' ressource. " . get_class($this) . " should implement updateAction method before using it.", $this->getRessourceTrace(__FUNCTION__));
        return true;
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logError(970, "Performing '" . __FUNCTION__ . "' on 'defaultRessource' ressource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getRessourceTrace(__FUNCTION__,false));
        $api->getOutput()->renderError(970, "You can't perform a deleteAction with '" . get_class($this) . "' ressource. " . get_class($this) . " should implement deleteAction method before using it.", $this->getRessourceTrace(__FUNCTION__));
        return true;
    }

    public function optionsAction() {
        $list = "";
        Api::getInstance()->logDebug(900, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getConfigs(), 3);
        if (method_exists($this, 'readAction') === false)
            $list .= 'GET, ';
        if (method_exists($this, 'createAction') === false)
            $list .= 'POST, ';
        if (method_exists($this, 'updateAction') === false)
            $list .= 'PUT, ';
        if (method_exists($this, 'deleteAction') === false)
            $list .= 'DELETE, ';
        $list .= 'OPTIONS';
        header('Allow: ' . $list);
        exit;
    }

    public function getRessourceTrace($method = null, $forDisplay = true) {
        $api = Api::getInstance();
        $trace = $api->getTrace();
        $trace['class'] = get_class($this);
        $trace['method'] = $method;
        if($forDisplay === false)
           $trace['config'] = $this->getConfigs();
        return $trace;
    }

}

?>
