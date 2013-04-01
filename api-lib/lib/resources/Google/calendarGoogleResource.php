<?php

/**
 * This resource is used to authenticate using google, and obtaining access to google's services.
 *
 * @package  SXAPI.Resource.Authenticate
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultAuthenticateResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class calendarGoogleResource extends defaultGoogleResource implements IResource {

    protected $services = array();

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            $tokens = $api->getInput('user')->get('google_token');
            // request data from google API
            $this->client->setAccessToken(json_encode($tokens));
            $this->addService("Calendar");
            $calendars = $this->getService("Calendar")->calendarList->listCalendarList();
            if ($nextPath !== null) {
                // recherche d'une clef en particulier
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, $api->getInput('user')->get('_id'));
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return $nextPath=" . $calendars[$nextPath], $this->getResourceTrace(__FUNCTION__, false), 1);
                $api->getOutput()->renderOk($message, $calendars[$nextPath]);
            } else {
                //affichage de toutes les clefs
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($calendars), $api->getInput('user')->get('_id'));
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                $api->getOutput()->renderOk($message, $calendars);
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $newId = $this->getModel()->create($api->getInput()->getParams());
            $message = sprintf($this->getConfig('message_service_create', 'message service create'), $newId);
            $api->logInfo(930, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $newId);
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
            $nextPath = $api->getInput()->getElement($sessElPosition + 1);
            if ($nextPath !== null) {
                $return = $this->getModel()->update($nextPath, $api->getInput()->getParams());
                $message = sprintf($this->getConfig('message_service_update', 'message service update'), $nextPath);
                $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                $api->getOutput()->renderOk($message, $return);
            } else {
                throw new ResourceException("could not " . __FUNCTION__ . " on " . get_class($this) . " because no id found in path ", 911);
            }
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
            $nextPath = $api->getInput()->getElement($sessElPosition + 1);
            if ($nextPath !== null) {
                $return = $this->getModel()->delete($nextPath);
                $message = sprintf($this->getConfig('message_service_delete', 'message service delete'), $nextPath);
                $api->logInfo(970, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                $api->getOutput()->renderOk($message, $return);
            } else {
                throw new ResourceException("could not " . __FUNCTION__ . " on " . get_class($this) . " because no id found in path ", 911);
            }
        } catch (Exception $exc) {
            $api->logError(970, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

}

?>
