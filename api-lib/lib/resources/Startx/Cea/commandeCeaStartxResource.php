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
    private $cmd = array();
    private $cxmldoc = "";
    private $cxml = array();
    private $additionalMessage = '';

    public function init() {
        parent::init();
        Api::getInstance()->setOutputDefault($this->getConfig('output', 'cxml'));
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
            // controle de l'origine de la demande
            $this->checkSourceIP();
            // Chargement des info de la commande
            $this->cmd = $this->loadRawInput()->parseInput()->extractCxmlData();

            // on transfert le payload vers le cxmlOutput
            $api->getInput()->setParam('payload', $this->cmd->payload);

            // Export de la commande dans SXA
            $this->exportCommande2Sxa();

            // Envoi du mail d'alerte sur la commande
            $this->sendCommandeAlertMail();
            // préparation de la réponse
            $message = sprintf($this->getConfig('message_service_commandeok', 'your order %s is recorded'), $this->cmd->payload);
            $this->recordCommandeHistory(true, $message);
            return array(true, $message, "OK", null);
        }
        catch(Exception $exc) {
            $api->logError($exc->getCode(), "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            switch($exc->getCode()) {
                // erreur du document entrant (document POSTé)
                case 935:
                case 936:
                    $message = $exc->getMessage();
                    break;
                // erreur du parsing XML
                case 937:
                case 938:
                    $message = $exc->getMessage();
                    break;
                default:
                    $message = sprintf($this->getConfig('message_error_exception', "An exception occured on %s : %s"), get_class($this), $exc->getMessage(), $exc->getCode());
                    break;
            }
            $this->recordCommandeHistory(false, $message);
            return array(false, $exc->getCode(), $message, array(), 418);
        }
    }

    public function updateAction() {
        return $this->createAction();
    }

    public function deleteAction() {
        return $this->createAction();
    }

    private function checkSourceIP() {
        $api = Api::getInstance();
        if($this->isConfig('authorized_ip')) {
            $api->logInfo(910, "Activate IP control for '" . get_class($this) . "'", null);
            $iplist = explode(',', $this->getConfig('authorized_ip', "127.0.0.1"));
            $rip = $api->getInput('server')->get('REMOTE_ADDR');
            if(!in_array($rip, $iplist)) {
                throw new ResourceException(sprintf($this->getConfig('message_error_badip', "You IP (%s) is not listed as an authorized IP for this action"), $rip), 936);
            }
        }
        else {
            $api->logInfo(910, "Disable IP control for '" . get_class($this) . "'", null);
        }
        return $this;
    }

    private function loadRawInput() {
        $this->cxmldoc = trim(file_get_contents('php://input'));
        if($this->cxmldoc == "") {
            throw new ResourceException($this->getConfig('message_error_noinputdoc', "No input document  using POST method."), 935);
        }
        return $this;
    }

    private function parseInput() {
        $api = Api::getInstance();
        libxml_use_internal_errors(true);
        $api->logDebug(931, "Start parsing xml input in '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->cxmldoc, 3);
        $this->cxml = simplexml_load_string($this->cxmldoc);
        if(!$this->cxml) {
            $errors = libxml_get_errors();
            if(count($errors) == 0) {
                throw new ResourceException($this->getConfig('message_error_emptyinputdoc', "cXML document is empty (only root node)"), 937);
            }
            else {
                $errorMsg = array();
                foreach($errors as $error) {
                    $errorMsg[] = $error->message . ' in file ' . $error->file . ' on line ' . $error->line . ':' . $error->column;
                }
                libxml_clear_errors();
                $message = sprintf($this->getConfig('message_error_xmlload', "cXml document contain %s XML error. See following : %s", count($errors), implode(", ", $errorMsg)));
                throw new ResourceException($message, 938);
            }
        }
        else {
            $api->logDebug(932, "XML Commande parsed in '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->cxmldoc, 2);
        }
        return $this;
    }

    private function extractCxmlData() {
        $cmd = new stdClass();
        $cmd->items = array();
        if($this->isConfig('extract_commande')) {
            foreach($this->getConfig('extract_commande') as $key => $xquery) {
                $cmd->$key = $this->extractCxmlValue($xquery);
            }
        }
        if($this->isConfig('extract_items_tree') and $this->isConfig('extract_item')) {
            foreach($this->extractCxmlValue($this->getConfig('extract_items_tree'), 'xml') as $key => $item) {
                $it = new stdClass();
                foreach($this->getConfig('extract_item') as $key => $xquery) {
                    $it->$key = $this->extractCxmlValue($xquery, null, $item);
                }
                $cmd->items[] = $it;
            }
        }
        return $cmd;
    }

    private function extractCxmlValue($xpath, $output = null, $context = null) {
        if(substr($xpath, 0, 4) === 'val:') {
            return substr($xpath, 4);
        }
        $ctx = ($context !== null) ? $context : $this->cxml;
        $val = @$ctx->xpath($xpath);
        if($val !== false) {
            if($output == 'xml') {
                return $val;
            }
            else {
                return trim(implode(', ', $val));
            }
        }
        else {
            return '';
        }
    }

    private function exportCommande2Sxa() {
        $api = Api::getInstance();
        try {
            $this->exportCommande2SxaInitResources();
            $affaireID = $this->exportCommande2SxaFindAffaire();

            $affaire = $this->sxa->getModule('affaire')->getDataFromID($affaireID);
            if(count($affaire) === 0) {
                throw new ResourceException(" could not find affaire '" . $affaireID . "' into sxa store. This affaire is associated to the billable ID '" . $billtoID . "' ", 87);
            }
            $devisID = $this->sxa->getModule('devis')->createId($affaireID);
            $this->exportCommande2SxaInsertDevis($devisID, $affaire);
            $this->exportCommande2SxaInsertDevisItems($devisID);
            // Demander l'enregistrement d'une actualité
            // Demander la generation du fichier pdf
            // Faire la creation de la commande

            $this->additionalMessage = "<b>Cette commande a été exportée vers le devis " . $devisID . "</b><br>";
            $this->additionalMessage.= "<a href=\"https://sxa.startx.fr/draco/Devis.php?id_dev=" . $devisID . "\">voir le devis " . $devisID . " sur SXA</a><br><br>";
            return true;
        }
        catch(Exception $exc) {
            print_r($exc);
            exit;
            $this->additionalMessage = "<b>Cette commande n'a pas pu être exportée vers SXA car " . $exc->getMessage() . "</b><br><br>";
            $api->logWarn($exc->getCode(), "Could not create SXA entry because : " . $exc->getMessage(), $exc);
            return false;
        }
        return $this;
    }

    private function exportCommande2SxaInitResources() {
        $api = Api::getInstance();
        if($this->isConfig('exportsxa_resource')) {
            $this->sxa = $api->getConfiguredResource($this->getConfig('exportsxa_resource'));
            if(!$this->sxa->hasModule('affaire,devis,commande')) {
                throw new ResourceException(" resource '" . $this->getConfig('exportsxa_resource') . "' should have modules 'affaire,devis,commande' when used in '" . $this->getConfig('_id') . "' resource", 87);
            }
            return $this;
        }
        else {
            throw new ResourceException(" resource '" . $this->getConfig('_id') . "' doesn't have a 'exportsxa_resource' property", 87);
        }
    }

    private function exportCommande2SxaFindAffaire() {
        if($this->isConfig('exportsxa_affaires')) {
            $billtoID = $this->cmd->billto;
            $affMatrice = $this->getConfig('exportsxa_affaires');
            if(array_key_exists($billtoID, $affMatrice)) {
                $affaireID = $affMatrice[$billtoID];
            }
            else {
                $affaireID = $affMatrice['default'];
            }
        }
        else {
            throw new ResourceException(" resource '" . $this->getConfig('_id') . "' doesn't have a 'exportsxa_affaires' property", 87);
        }
        return $affaireID;
    }

    private function exportCommande2SxaInsertDevis($devisID, $affaire) {
        $this->sxa->getModule('devis')->insert(array(
            'id_dev' => $devisID,
            'affaire_dev' => $affaire['id_aff'],
            'status_dev' => 3,
            'titre_dev' => 'cmd ' . $this->cmd->id,
            'commercial_dev' => 'cl',
            'sommeHT_dev' => $this->cmd->total,
            'BDCclient_dev' => $this->cmd->id . ' (' . $this->cmd->agreement . ')',
            'entreprise_dev' => $affaire['id_ent'],
            'contact_dev' => $affaire['id_cont'],
            'contact_achat_dev' => $affaire['id_cont'],
            'datemodif_dev' => date('Y-m-d'),
            'daterecord_dev' => date('Y-m-d'),
            'nomdelivery_dev' => $affaire['nom_ent'],
            'adressedelivery_dev' => $affaire['add1_ent'],
            'adresse1delivery_dev' => $affaire['add2_ent'],
            'villedelivery_dev' => $affaire['ville_ent'],
            'cpdelivery_dev' => $affaire['cp_ent'],
            'paysdelivery_dev' => $affaire['pays_ent'],
            'maildelivery_dev' => $this->cmd->shipto_email,
            'complementdelivery_dev' => $this->cmd->shipto_email,
            'tva_dev' => $affaire['tauxTVA_ent'],
            'commentaire_dev' => 'payload : ' . $this->cmd->payload
        ));
        return $this;
    }

    private function exportCommande2SxaInsertDevisItems($devisID) {
        foreach($this->cmd->items as $k => $item) {
            $this->sxa->getModule('devis')->insertProduit(array(
                'id_devis' => $devisID,
                'id_produit' => $item->id_prod,
                'desc' => $item->desc . ' ' . $item->commentaire,
                'quantite' => $item->qte,
                'remise' => '0.00',
                'prix' => $item->prix
            ));
        }
        return $this;
    }

    private function recordCommandeHistory($success, $message, $others = array()) {
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
        if(is_array($others)) {
            $trace = array_merge_recursive($others, $trace);
        }
        if($this->getConfig('history_store') != '') {
            try {
                $store = $api->getStore($this->getConfig('history_store'));
                $store->create($this->getConfig('history_store_dataset', 'cea.history'), Toolkit::array2Object($trace));
            }
            catch(Exception $exc) {
                $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            }
        }
        try {
            
        }
        catch(Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return false;
        }
        return true;
    }

    private function sendCommandeAlertMail() {
        $api = Api::getInstance();
        // préparation de la ressource d'envoi de mail
        $sender = $api->getConfiguredResource($this->getConfig('resource_sendmail', 'sendmail'));
        $params = $sender->getConfig('default_params');
        // récuparation d'une vue html des données pour ajouter dans le mail
        ob_start();
        var_dump($this->cmd);
        $vardump = ob_get_contents();
        ob_end_clean();
        // envoi de mail
        $to = $this->getConfig('sendmail_to', $params['to']);
        $sub = $this->getConfig('sendmail_subject', $params['subject']);
        $body = $this->additionalMessage . $this->getConfig('sendmail_body', $params['body']) . $vardump . $this->cxmldoc;
        $sender->mail->IsHTML(true);
        if(!$sender->sendMail($to, $sub, $body))
            throw new ResourceException(" resource '" . $this->getConfig('resource_sendmail', 'sendmail') . "' could not send order mail. Abort", 87);
        return $this;
    }

}

?>
