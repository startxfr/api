<?php

/**
 * This resource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class contactCeaStartxResource extends messageResource implements IResource {

    static public $ConfDesc = '{"class_name":"contactCeaStartxResource",
  "desc":"demande de contact pour le CEA",
  "properties": [  ]
}';

    public function init() {
        parent::init();
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $message = $this->getConfig('message_service_create', "You can't perform this action with '" . get_class($this) . "' resource.");
        $api->logError(910, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__, false));
        $this->recordContactHistory(false, $message);
        return array(false, 910, $message, $this->getResourceTrace(__FUNCTION__), 405);
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $in = $api->getInput();
            $i = $this->filterParams($in->getParams(), "input");
            $type = (array_key_exists('type', $i) and $i['type'] == "tel") ? "tel" : "mail";
            if ($type == "tel") {
                if (!array_key_exists('tel', $i) or $i['tel'] == "") {
                    return array(false, 931, $this->getConfig('message_error_notel', "merci de fournir votre nummero de téléphone"), array(), 500);
                }
                $contact = "par téléphone au " . $i['tel'];
            } else {
                if (!array_key_exists('mail', $i) or $i['mail'] == "") {
                    return array(false, 931, $this->getConfig('message_error_nomail', "merci de fournir votre adresse e-mail"), array(), 500);
                }
                $contact = "par mail sur " . $i['mail'];
            }
            // préparation de la ressource d'envoi de mail
            $sender = $api->getConfiguredResource($this->getConfig('resource_sendmail', 'sendmail'));
            $params = $sender->getConfig('default_params');

            // envoi de mail
            $to = $this->getConfig('sendmail_to', $params['to']);
            $sub = sprintf($this->getConfig('sendmail_subject', 'Demande d\'info'), @$i['name'], @$i['horaire'], @$i['subject']);
            $body = sprintf($this->getConfig('sendmail_body', 'contact %s %s %s %s %s'), @$i['name'], @$i['horaire'], $contact, @$i['subject'], @$i['message']);
            ;

            if (!$sender->sendMail($to, $sub, $body))
                throw new ApiException(" resource '" . $this->getConfig('resource_sendmail', 'sendmail') . "' could not send mail. Abort", 87);

            // préparation de la réponse
            $message = $this->getConfig('message_ok', 'your contact request %s is recorded');
            $this->recordContactHistory(true, $message);
            return array(true, $message, "OK", null);
        } catch (Exception $exc) {
            $message = sprintf($this->getConfig('message_error_exception', "An exception occured on %s : %s"), get_class($this), $exc->getMessage(), $exc->getCode());
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $this->recordContactHistory(false, $message);
            return array(false, $exc->getCode(), $message, array(), 500);
        }
        return true;
    }

    public function updateAction() {
        return $this->createAction();
    }

    public function deleteAction() {
        return $this->createAction();
    }

    protected function recordContactHistory($success, $message, $others = array()) {
        $api = Api::getInstance();
        $trace = array(
            'success' => $success,
            'date' => new MongoDate(),
            'session' => $api->getInput('session')->getId(),
            'user' => $api->getInput('user')->getId(),
            'http_query' => Toolkit::object2Array($api->getInput()->getContext()),
            'message' => $message
        );
        if (is_array($others)) {
            $trace = array_merge_recursive($others, $trace);
        }
        if ($this->getConfig('history_store') != '') {
            try {
                $store = $api->getStore($this->getConfig('history_store'));
                $store->create($this->getConfig('history_store_dataset', 'cea.history'), Toolkit::array2Object($trace));
            } catch (Exception $exc) {
                $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            }
        }
        try {
            
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return false;
        }
        return true;
    }

}

?>
