<?php

/**
 * This resource class is abstract and should not be used as it.
 * Developpers can create a new session resource type by derivating from this class
 *
 * @package  SXAPI.Resource.Session
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 *
 *
 */
abstract class defaultSessionResource extends defaultResource implements IResource {

    static public $ConfDesc = '{"class_name":"defaultSessionsResource",
                                "desc":"abstract resource, access to session",
                                "propreties":
	[
		{
			"name":"session_name",
			"type":"string",
			"mandatory":"true",
			"desc":"id of the session"
		}
	]
}'
;
    
    public function __construct($config) {
        parent::__construct($config);
    }

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('session_name', '') == '')
            $api->logWarn(907, get_class($this) . " resource config should contain the 'session_name' attribute", $this->getResourceTrace(__FUNCTION__, false));
        return $this;
    }

    /**
     * This method is used for all GET request made to a sessionResource resource
     * according to the type, this resource can have multiple forms
     * type 'info' : return the session informations (default)
     * type 'data' : return the data stored in this session
     * type 'user' : return the user data stored for this session. Could be null if anonymous
     * type 'app'  : return the application data stored for this session.
     * type 'application'  : alias of the 'app' type
     *
     * @return boolean
     */
    public function readAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource is not allowed", $this->getResourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(910, $this->getConfig('message_service_read', 'could not read this node'), $this->getResourceTrace(__FUNCTION__), 405);
        return true;
    }

    public function createAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource is not allowed", $this->getResourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(930, $this->getConfig('message_service_create', 'could not create this node'), $this->getResourceTrace(__FUNCTION__), 405);
    }

    public function updateAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource is not allowed", $this->getResourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(950, $this->getConfig('message_service_update', 'could not update this node'), array(), 405);
        return true;
    }

    public function deleteAction() {
        Api::getInstance()->logError(910, "Performing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource is not allowed", $this->getResourceTrace(__FUNCTION__, false));
        Api::getInstance()->getOutput()->renderError(970, $this->getConfig('message_service_delete', 'could not delete this node'), array(), 405);
        return true;
    }

    public function optionsAction() {
        return parent::optionsAction();
    }

}

?>
