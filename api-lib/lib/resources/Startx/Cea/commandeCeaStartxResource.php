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
        $api->logError(910, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__, false));
        return array(false, 910, "You can't perform this action with '" . get_class($this) . "' resource.", $this->getResourceTrace(__FUNCTION__), 405);
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $dataPOST = trim(file_get_contents('php://input'));
            if ($dataPOST == "") {
                $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' : input has no XML document", $dataPOST);
                return array(false, 935, "Aucun message cXML n'as été trouvé dans la requête. Traitement de la commande impossible.", $dataPOST, 500);
            }
            libxml_use_internal_errors(true);
            $api->logDebug(931, "Start parsing xml input in '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $dataPOST, 3);
            $xmlData = simplexml_load_string($dataPOST);
            if (!$xmlData) {
                $errors = libxml_get_errors();
                if (count($errors) == 0) {
                    $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' : cXML document has is empty (only root node)", array());
                    return array(false, 935, "Le document cXML est vide. Traitement de la commande impossible.", array(), 500);
                } else {
                    $errorMsg = array();
                    foreach ($errors as $error) {
                        $errorMsg[] = $error->message . ' in file ' . $error->file . ' on line ' . $error->line . ':' . $error->column;
                    }
                    libxml_clear_errors();
                    $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . implode(", ", $errorMsg), libxml_get_errors());
                    return array(false, 935, "Le document cXML contient " . count($errors) . " erreur(s). Traitement de la commande impossible. Voici les erreurs reportées : " . implode(", ", $errorMsg), $errorMsg, 500);
                }
            } else {
                $api->logDebug(932, "XML Commande parsed in '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $dataPOST, 2);





                // ici pour la recuperation des info depuis $xmlData
                $payload = (string) $xmlData['payloadid'];
                $api->getInput()->setParam('payload', $payload);


                // préparation de la ressource d'envoi de mail
                $senderResourceId = $this->getConfig('resource_sendmail', 'sendmail');
                $resourceCollection = $api->getConfig("resource_collection", "resources");
                $configResource = $api->nosqlConnection->selectCollection($resourceCollection)->findOne(array("_id" => $senderResourceId));
                if (is_null($configResource) or $configResource["_id"] == '')
                    throw new ApiException("Can't find the resource '" . $senderResourceId . "' in resources collection '" . $resourceCollection . "'", 87);
                $api->logDebug(87, "Resource '" . $senderResourceId . "' found in resource backend", $senderResourceId, 5);
                if ($configResource['class'] == '') {
                    $api->logError(87, " resource '" . $senderResourceId . "' config should contain the 'class' attribute", $senderResourceId);
                    throw new ApiException(" resource '" . $senderResourceId . "' config should contain the 'class' attribute", 87);
                }
                $sender = $api->getResource($configResource['class'], $configResource);
                $params = $sender->getConfig('default_params');
                
                // envoi de mail
                $to = $this->getConfig('sendmail_to', $params['to']);
                $sub = $this->getConfig('sendmail_subject', $params['subject']);
                $body = $this->getConfig('sendmail_body', $params['body']).$dataPOST;
                
                if (!$sender->sendMail($to, $sub, $body))
                    throw new ApiException(" resource '" . $senderResourceId . "' could not send order mail. Abort", 87);
                
                // préparation de la réponse
                $message = sprintf($this->getConfig('message_service_commandeok', 'votre commande %s est maintenant enregistrée. Nous vous en remercions et allons la traiter au plus vite.'), $payload);
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, "OK", null);
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

    public function updateAction() {
        return $this->createAction();
    }

    public function deleteAction() {
        return $this->readAction();
    }

}

?>
