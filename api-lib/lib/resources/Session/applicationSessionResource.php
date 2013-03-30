<?php

/**
 * This resource return data about the application curently used
 *
 * @package  SXAPI.Resource.Session
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultSessionResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class applicationSessionResource extends defaultSessionResource implements IResource {

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
        $nextPath = $api->getInput()->getElement($sessElPosition + 1);
        $out = $api->getInput('application')->getAll();
        if (is_array($out) and is_array($this->getConfig('excluded_data', array())))
            foreach ($this->getConfig('excluded_data', array()) as $key)
                unset($out[$key]);
        if (is_array($out))
            foreach ($out as $key => $val)
                if (is_object($val) and get_class($val) == 'MongoDate')
                    $out[$key] = date('Y-m-d H:i:s', (string) $val->sec);
        if ($nextPath !== null) {
            // recherche d'une clef en particulier
            $message = sprintf($this->getConfig('message_service_read','message service read'), 1, session_id());
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $out[$nextPath]);
        } else {
            //affichage de toutes les clefs
            $message = sprintf($this->getConfig('message_service_read','message service read'), count($out), session_id());
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $out);
        }
        return true;
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
        $newAppName = $api->getInput()->getElement($sessElPosition + 1);
        if ($newAppName !== null)
            $api->getInput()->setParam('app', $newAppName);
        try {
            $api->getInput('application')->setApp()->loadCache();
            $message = sprintf($this->getConfig('message_service_create','message service create'), session_id(), $api->getInput('session')->get('application'));
            $api->logInfo(930, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $api->getInput('application')->getAll());
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError(930, sprintf($this->getConfig('message_service_errorcreate'), session_id(), $api->getInput('session')->get('application'), $exc->getMessage()), $exc);
        }
        return true;
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
        $newAppName = $api->getInput()->getElement($sessElPosition + 1);
        if ($newAppName !== null)
            $api->getInput()->setParam('app', $newAppName);
        try {
            $api->getInput('application')->setApp()->loadCache();
            $message = sprintf($this->getConfig('message_service_update','message service update'), session_id(), $api->getInput('session')->get('application'));
            $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $api->getInput('application')->getAll());
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError(950, sprintf($this->getConfig('message_service_errorupdate'), session_id(), $api->getInput('session')->get('application'), $exc->getMessage()), $exc);
        }
        return true;
    }

}

?>
