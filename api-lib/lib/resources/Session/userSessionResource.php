<?php

/**
 * This resource return data about the user curently logged
 *
 * @package  SXAPI.Resource.Session
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultSessionResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class userSessionResource extends defaultSessionResource implements IResource {

    static public $ConfDesc = '{"class_name":"userSessionsResource",
                                "desc":"access to session user",
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
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
        $nextPath = $api->getInput()->getElement($sessElPosition + 1);
        $out = $api->getInput('user')->getAll();    
        if (is_array($out) and is_array($this->getConfig('excluded_data', array())))
            foreach ($this->getConfig('excluded_data', array()) as $key) {
                unset($out[$key]);
            }
        if (is_array($out))
            foreach ($out as $key => $val) {
                if (is_object($val) and get_class($val) == 'MongoDate')
                    $out[$key] = date('Y-m-d H:i:s', (string) $val->sec);
            }
        if ($nextPath !== null) {
            // recherche d'une clef en particulier
            $message = sprintf($this->getConfig('message_service_read','message service read'), 1, session_id());
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            return array(true, $message, $out[$nextPath]);
        } else {
            //affichage de toutes les clefs
            $message = sprintf($this->getConfig('message_service_read','message service read'), count($out), session_id());
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            return array(true, $message, $out);
        }
        return true;
    }

    public function deleteAction() {
        $api = API::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $user = $api->getInput('session')->get('user'); 
        $api->getInput('session')->clear("user");  
        $api->getInput('session')->clear("user_goauth_token");
        $message = sprintf($this->getConfig('message_service_delete', 'message service delete'), $user);
        $api->logInfo(970, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, $user);
        return true;
    }
    
}

?>
