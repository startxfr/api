<?php

/**
 * This resource return informations about the session context
 *
 * @package  SXAPI.Resource.Session
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultSessionResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class infoSessionResource extends defaultSessionResource implements IResource {

    static public $ConfDesc = '{"class_name":"infoSessionsResource",
                                "desc":"access to session info",
                                "properties":
	[
		{
			"name":"exclude_data",
			"type":"array",
			"mandatory":"false",
			"desc":"data not to return"
		}
	]
}'
;
    
    public function readAction() {
        $api = Api::getInstance();
        $out = $api->getInput('session')->findOneSession(session_id());
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        if (is_array($out) and is_array($this->getConfig('excluded_data', array())))
            foreach ($this->getConfig('excluded_data', array()) as $key)
                unset($out[$key]);
        if (is_array($out))
            foreach ($out as $key => $val)
                if (is_object($val) and get_class($val) == 'MongoDate')
                    $out[$key] = date('Y-m-d H:i:s', (string) $val->sec);
        $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
        $nextPath = $api->getInput()->getElement($sessElPosition + 1);
        if ($nextPath !== null) {
            // recherche d'une clef en particulier
            $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, session_id());
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            return array(true, $message, $out[$nextPath]);
        } else {
            //affichage de toutes les clefs
            $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($out), session_id());
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            return array(true, $message, $out);
        }
        return true;
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        session_regenerate_id();
        $info = $api->getInput('session')->readHandler(session_id());
        $message = sprintf($this->getConfig('message_service_create', 'message service create'), session_id());
        $api->logInfo(930, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, $info);
        return true;
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $id = session_id();
        session_destroy();
        unset($_SESSION);
        setcookie(session_name(), '', time() - 3600, "/");
        $message = sprintf($this->getConfig('message_service_delete', 'message service delete'), $id);
        $api->logInfo(970, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, true);
        return true;
    }

}

?>
