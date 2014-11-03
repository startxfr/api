<?php

/**
 * This resource return data stored in session
 *
 * @package  SXAPI.Resource.Session
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultSessionResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class dataSessionResource extends defaultSessionResource implements IResource {

    static public $ConfDesc = '{"class_name":"dataSessionsResource",
                                "desc":"access to session data",
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
        $out = $api->getInput('session')->getAll();
        if (is_array($out) and is_array($this->getConfig('excluded_data', array()))) {
            foreach ($this->getConfig('excluded_data', array()) as $value) {
                unset($out[$value]);
            }
        }
        if (is_array($out)) {
            foreach ($out as $key => $val) {
                if (is_object($val) and get_class($val) == 'MongoDate')
                    $out[$key] = date('Y-m-d H:i:s', (string) $val->sec);
            }
        }
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
        $addCount = array();
        foreach ($api->getInput()->getParams() as $k => $v) {
            if (is_int($k) and is_object($v)) {
                if (!in_array($v->key, $this->getConfig('excluded_data', array()))) {
                    $api->getInput('session')->set($v->key, $v->value);
                    $addCount[$v->key] = $v->value;
                }
            } else {
                if (!in_array($k, $this->getConfig('excluded_data', array()))) {
                    $api->getInput('session')->set($k, $v);
                    $addCount[$k] = $v;
                }
            }
        }
        $message = sprintf($this->getConfig('message_service_create', 'message service create'), implode(',', array_keys($addCount)));
        $api->logInfo(930, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, $addCount);
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $updateCount = array();
        foreach ($api->getInput()->getParams() as $k => $v) {
            if (!in_array($k, $this->getConfig('excluded_data', array()))) {
                $api->getInput('session')->set($k, $v);
                $updateCount[$k] = $v;
            }
        }
        $message = sprintf($this->getConfig('message_service_update', 'message service update'), implode(',', array_keys($updateCount)));
        $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, $updateCount);
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
        $nextPath = $api->getInput()->getElement($sessElPosition + 1);
        if ($nextPath !== null)
            $key2Remove = $nextPath;
        else
            $key2Remove = $api->getInput()->getParam('key');
        if ($key2Remove != '' and !in_array($key2Remove, $this->getConfig('excluded_data', array()))) {
            $api->getInput('session')->clear($key2Remove);
        }
        $message = sprintf($this->getConfig('message_service_delete', 'message service delete'), $key2Remove);
        $api->logInfo(970, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        return array(true, $message, $key2Remove);
    }

}

?>
