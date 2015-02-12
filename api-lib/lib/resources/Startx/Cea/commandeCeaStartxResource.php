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
class commandeCeaStartxResource extends messageResource implements IResource {

    static public $ConfDesc = '{"class_name":"commandeCeaStartxResource",
  "desc":"commande dematerialisée avec le CEA",
  "properties": [  ]
}';

    public function init() {
        parent::init();
        $api = Api::getInstance();
        $api->setOutputDefault($this->getConfig('output', 'cxml'));
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $message = $this->getConfig('message_service_create', "You can't perform this action with '" . get_class($this) . "' resource.");
        $api->logError(910, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__, false));
        $this->recordCommandeHistory(false, $message);
        return array(false, 910, $message, $this->getResourceTrace(__FUNCTION__), 405);
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            if ($this->isConfig('authorized_ip')) {
                $api->logInfo(910, "Activate IP control for '" . get_class($this) . "'", null);
                $iplist = explode(',', $this->getConfig('authorized_ip', "127.0.0.1"));
                $rip = $api->getInput('server')->get('REMOTE_ADDR');
                if (!in_array($rip, $iplist)) {
                    $message = sprintf($this->getConfig('message_error_badip', "You IP (%s) is not listed as an authorized IP for this action"), $rip);
                    $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' : IP $rip is not autorized", $rip);
                    $this->recordCommandeHistory(false, $message);
                    return array(false, 935, $message, $rip, 419);
                }
            } else {
                $api->logInfo(910, "Disable IP control for '" . get_class($this) . "'", null);
            }
            $this->cxmldoc = trim(file_get_contents('php://input'));
            if ($this->cxmldoc == "") {
                $message = $this->getConfig('message_error_noinputdoc', "could not read an input document.");
                $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' : input has no XML document", $this->cxmldoc);
                $this->recordCommandeHistory(false, $message);
                return array(false, 935, $message, $this->cxmldoc, 415);
            }
            libxml_use_internal_errors(true);
            $api->logDebug(931, "Start parsing xml input in '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->cxmldoc, 3);
            $this->cxml = simplexml_load_string($this->cxmldoc);
            if (!$this->cxml) {
                $errors = libxml_get_errors();
                if (count($errors) == 0) {
                    $message = $this->getConfig('message_error_emptyinputdoc', "cXML document is empty (only root node)");
                    $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' : cXML document is empty (only root node)", array());
                    $this->recordCommandeHistory(false, $message);
                    return array(false, 935, $message, array(), 416);
                } else {
                    $errorMsg = array();
                    foreach ($errors as $error) {
                        $errorMsg[] = $error->message . ' in file ' . $error->file . ' on line ' . $error->line . ':' . $error->column;
                    }
                    libxml_clear_errors();
                    $message = sprintf($this->getConfig('message_error_xmlload', "cXml document contain %s XML error. See following : %s", count($errors), implode(", ", $errorMsg)));
                    $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . implode(", ", $errorMsg), libxml_get_errors());
                    $this->recordCommandeHistory(false, $message);
                    return array(false, 935, $message, $errorMsg, 417);
                }
            } else {
                $api->logDebug(932, "XML Commande parsed in '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->cxmldoc, 2);

                $cmd = new stdClass();
                $cmd->items = array();
                if ($this->isConfig('extract_commande')) {
                    foreach ($this->getConfig('extract_commande') as $key => $xquery) {
                        $cmd->$key = $this->extractCxmlValue($xquery);
                    }
                }
                if ($this->isConfig('extract_items_tree') and $this->isConfig('extract_item')) {
                    foreach ($this->extractCxmlValue($this->getConfig('extract_items_tree'), 'xml') as $key => $item) {
                        $it = new stdClass();
                        foreach ($this->getConfig('extract_item') as $key => $xquery) {
                            $it->$key = $this->extractCxmlValue($xquery, null, $item);
                        }
                        $cmd->items[] = $it;
                    }
                }

                $api->getInput()->setParam('payload', $cmd->payload);

                // préparation de la ressource d'envoi de mail
                $sender = $api->getConfiguredResource($this->getConfig('resource_sendmail', 'sendmail'));
                $params = $sender->getConfig('default_params');

                // récuparation d'une vue html des données pour ajouter dans le mail
                ob_start();
                var_dump($cmd);
                $vardump = ob_get_contents();
                ob_end_clean();
                
                // envoi de mail
                $to = $this->getConfig('sendmail_to', $params['to']);
                $sub = $this->getConfig('sendmail_subject', $params['subject']);
                $body = $this->getConfig('sendmail_body', $params['body']) . $vardump . $this->cxmldoc;
                $sender->mail->IsHTML(true);
                if (!$sender->sendMail($to, $sub, $body))
                    throw new ApiException(" resource '" . $this->getConfig('resource_sendmail', 'sendmail') . "' could not send order mail. Abort", 87);

                // préparation de la réponse
                $message = sprintf($this->getConfig('message_service_commandeok', 'your order %s is recorded'), $cmd->payload);
                $this->recordCommandeHistory(true, $message);
                return array(true, $message, "OK", null);
            }
        } catch (Exception $exc) {
            $message = sprintf($this->getConfig('message_error_exception', "An exception occured on %s : %s"), get_class($this), $exc->getMessage(), $exc->getCode());
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $this->recordCommandeHistory(false, $message);
            return array(false, $exc->getCode(), $message, array(), 418);
        }
        return true;
    }

    public function updateAction() {
        return $this->createAction();
    }

    public function deleteAction() {
        return $this->createAction();
    }

    private function extractCxmlValue($xpath, $output = null, $context = null) {
        if (substr($xpath, 0, 4) === 'val:') {
            return substr($xpath, 4);
        }
        $ctx = ($context !== null) ? $context : $this->cxml;
        $val = @$ctx->xpath($xpath);
        if ($val !== false) {
            if ($output == 'xml') {
                return $val;
            } else {
                return implode(', ', $val);
            }
        } else {
            return '';
        }
    }

    protected function recordCommandeHistory($success, $message, $others = array()) {
        $api = Api::getInstance();
        $trace = array(
            'success' => $success,
            'date' => new MongoDate(),
            'session' => $api->getInput('session')->getId(),
            'user' => $api->getInput('user')->getId(),
            'http_query' => Toolkit::object2Array($api->getInput()->getContext()),
            'http_body' => trim(file_get_contents('php://input')),
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
