<?php

/**
 * This resource class is abstract and should not be used as it.
 * Developpers can create a new authentification resource type by derivating from this class
 *
 * @package  SXAPI.Resource.Authenticate
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class defaultAuthenticateResource extends defaultResource implements IResource {

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('message_service_read', '') == '')
            $api->logWarn(907, get_class($this) . " resource config should contain the 'message_service_read' attribute", $this->getResourceTrace(__FUNCTION__, false));
        if ($this->getConfig('message_service_delete', '') == '')
            $api->logWarn(907, get_class($this) . " resource config should contain the 'message_service_delete' attribute", $this->getResourceTrace(__FUNCTION__, false));
        return $this;
    }

    public function readAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource is not allowed", $this->getResourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(910, $this->getConfig('message_service_read', 'could not read this node'), $this->getResourceTrace(__FUNCTION__));
        return true;
    }

    public function deleteAction() {
        Api::getInstance()->logError(970, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource is not allowed", $this->getResourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(910, $this->getConfig('message_service_delete', 'could not delete this node'), $this->getResourceTrace(__FUNCTION__));
        return true;
    }

}

?>
