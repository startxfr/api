<?php

/**
 * This resource is used to return an input message
 *
 * @package  SXAPI.Resource.Test
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class subscribecosmetOodtResource extends redirectHttpResource implements IResource {

    public function createAction() {
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        $input = Api::getInstance()->getInput();
        $errorMessage = array();
        if ($input->getParam('nom') == '')
            $errorMessage[] = "vous devez fournir votre nom de famille";
        if ($input->getParam('prenom') == '')
            $errorMessage[] = "vous devez fournir votre prenom";
        if ($input->getParam('email') == '')
            $errorMessage[] = "vous devez fournir votre adresse e-mail";
        elseif (filter_var($input->getParam('email'), FILTER_VALIDATE_EMAIL) == false)
            $errorMessage[] = "vous devez fournir une adresse e-mail valide";
        if ($input->getParam('entreprise') == '')
            $errorMessage[] = "vous devez fournir le nom de votre entreprise";
        if ($input->getParam('fonction') == '')
            $errorMessage[] = "vous devez fournir votre fonction au sein de l'entreprise";
        if ($input->getParam('departement') == '')
            $errorMessage[] = "vous devez fournir le département du siège de votre entreprise";
        if (count($errorMessage) > 0) {
            Api::getInstance()->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . implode(', ', $errorMessage), $this->getResourceTrace(__FUNCTION__, false), 1);
            Api::getInstance()->getOutput()->renderError(910, implode(', ', $errorMessage));
        } else {
            // enregistrement de la trace
            try {
                $configRecord = $this->getConfig('record');
                $store = Api::getInstance()->getStore($configRecord['store']);
                $trace = toolkit::array2Object($input->getParams());
                $trace->_id = new MongoId();
                $trace->recorded = new MongoDate();
                $trace->ip = $_SERVER['REMOTE_ADDR'];
                $trace->request = new stdClass();
                $trace->request->referer = $_SERVER['REFERER'];
                $trace->request->method = $input->getMethod();
                $trace->request->rooturl = $input->getRootUrl();
                $trace->request->path = $input->getPath();
                $store->create($configRecord['collection'], $trace);
                // faire envoi de mail ici pour la confirmation
                $messageMail = "<html><head><title>Bonjour</title></head><body>Bonjour " . ucfirst(strtolower($input->getParam('prenom'))) . ",<br/>
<p>Nous avons bien enregistré votre inscription comme visiteur pour le salon Cosmetagora 2014, et nous vous en remercions.<p>
<p>Pour telecharger votre carton d'invitation, merci de cliquer sur le lien suivant pour l'imprimer. Ce document vous sera demandé a l'entrée du salon.</p>
<p>invitation : rendez vous sur http://localhost/git/tribu-sfc/cosmet2014/badge.html?id=".$trace->_id." ou <a href=\"http://localhost/git/tribu-sfc/cosmet2014/badge.html\">cliquez ici</a></p>
<p>Cordialement</p>
<p>L'equipe Cosmetagora</p></body></html>";
                $headers.= "content-Type:text/html;charset=utf-8\r\n";
                $headers.="MIME-version: 1.0\r\n";
                $headers.="From: Cosmetagora <invitation@cosmetagora.com>\r\n";
                $headers.="X-Priority: 1";
                $headers.="Bcc: clarue@startx.fr\r\n";
                mail($input->getParam('email'), "Votre invitation Cosmetagora 2014", $messageMail, $headers);
                $message = $this->getConfig('message_service_create', 'Subscribed');
                Api::getInstance()->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                Api::getInstance()->getOutput()->renderOk($message, true, 1);
            } catch (Exception $exc) {
                Api::getInstance()->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' when trying to record redirect trace : " . $exc->getMessage(), $exc);
                Api::getInstance()->getOutput()->renderError(911, 'error in recording your details');
            }
        }
        return true;
    }

    public function readAction() {
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        $message = "This webservice only allow POST method";
        Api::getInstance()->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        Api::getInstance()->getOutput()->renderError(910, $message);
        return true;
    }

    public function updateAction() {
        return $this->readAction();
    }

    public function deleteAction() {
        return $this->readAction();
    }

    public function optionsAction() {
        return $this->readAction();
    }

}

?>
