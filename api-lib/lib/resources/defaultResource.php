<?php

/**
 * This resource class is abstract and should not be used as it.
 * Developpers can create a new authentification resource type by derivating from this class
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      Configurable
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class defaultResource extends Configurable implements IResource {

    public function __construct($config) {
        parent::__construct($config);
    }

    public function init() {
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logError(910, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__,false));
        $api->getOutput()->renderError(910, "You can't perform a readAction with '" . get_class($this) . "' resource. " . get_class($this) . " should implement readAction method before using it.", $this->getResourceTrace(__FUNCTION__));
        return true;
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logError(930, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__,false));
        $api->getOutput()->renderError(930, "You can't perform a createAction with '" . get_class($this) . "' resource. " . get_class($this) . " should implement createAction method before using it.", $this->getResourceTrace(__FUNCTION__));
        return true;
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logError(950, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__,false));
        $api->getOutput()->renderError(950, "You can't perform a updateAction with '" . get_class($this) . "' resource. " . get_class($this) . " should implement updateAction method before using it.", $this->getResourceTrace(__FUNCTION__));
        return true;
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logError(970, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__,false));
        $api->getOutput()->renderError(970, "You can't perform a deleteAction with '" . get_class($this) . "' resource. " . get_class($this) . " should implement deleteAction method before using it.", $this->getResourceTrace(__FUNCTION__));
        return true;
    }

    public function optionsAction() {
        $list = "";
        Api::getInstance()->logDebug(900, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
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

    public function getResourceTrace($method = null, $forDisplay = true, $other = null) {
        $api = Api::getInstance();
        $trace = $api->getTrace();
        $trace['class'] = get_class($this);
        $trace['method'] = $method;
        if($forDisplay === false)
           $trace['config'] = $this->getConfigs();
        if(!is_null($other) and !is_array($other))
           $other = array($other);
        if(is_array($other))
           $trace = array_merge($trace,$other);
        return $trace;
    }

}

?>
