<?php

/**
 * This resource return informations about the currently used  API Document
 *
 * @class    apiResource
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class apiResource extends readonlyResource implements IResource {

    static public $ConfDesc = '{"class_name":"apiResource",
                                "desc":"non renseignee",
                                "properties":
	[
		{
			"name":"exposed_keys",
			"type":"string",
			"mandatory":"true",
			"desc":"non renseignee"
		}
	]
}'
;
    
    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('exposed_keys', '') == '')
            $api->logWarn(907, get_class($this) . " resource config should contain the 'exposed_keys' attribute", $this->getResourceTrace(__FUNCTION__, false));
        else {
            $this->setConfig('exposed_keys', Toolkit::string2Array($this->getConfig('exposed_keys')));
        }
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $out = array();
        foreach ($this->getConfig('exposed_keys') as $key) {
            $out[$key] = $api->getConfig($key);
        }
        $message = sprintf($this->getConfig('message_service_read', 'message service read'), session_id());
        $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, $out);
    }

}

?>
