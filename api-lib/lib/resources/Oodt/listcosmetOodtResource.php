<?php

/**
 * This resource return the server time
 *
 * @package  SXAPI.Resource.Test
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class listcosmetOodtResource extends nosqlModelResource implements IResource {

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        $api->setOutputDefault($this->getConfig('format'));
        //affichage de toutes les clefs
        $input = $api->getInput();
        $search = $this->filterSearchParams($input->getParams());
        $order = array('recorded' => -1);
        $start = $input->getParam($this->getConfig('startParam', 'start'), 0);
        $max = $input->getParam($this->getConfig('limitParam', 'limit'), 100000);
        $return = $this->getModel()->read($search, $order, $start, $max);
        $countResult = $this->getModel()->readCount($search);
        $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($return), $countResult, session_id());
        $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
        $api->getOutput()->renderOk($message, $return, $countResult);
        return true;
    }


    public function createAction() {
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        $message = "This webservice only allow GET method";
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
