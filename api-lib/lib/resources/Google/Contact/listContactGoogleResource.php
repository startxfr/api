<?php

/**
 * This resource is used to get user contact's information's stored in google.
 *
 * @package  SXAPI.Resource.Google
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultGoogleResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class listContactGoogleResource extends defaultGoogleResource implements IResource {

    protected $services = array();

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);

            $req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full");
            $val = $this->client->getIo()->authenticatedRequest($req);
            $contacts = json_decode(json_encode(simplexml_load_string($val->getResponseBody())));
            if ($nextPath !== null) {
                // recherche d'une clef en particulier
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, $api->getInput('user')->get('_id'));
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return $nextPath=" . $user[$nextPath], $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $contacts->entry[$nextPath]);
            } else {
                //affichage de toutes les clefs
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($user), $api->getInput('user')->get('_id'));
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $contacts->entry);
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage());
        }
        return true;
    }


}

?>
