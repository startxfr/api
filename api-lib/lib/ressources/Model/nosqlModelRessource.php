<?php

/**
 * This ressource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class nosqlModelRessource extends defaultModelRessource implements IRessource {

    public function init() {
        parent::init();
        if (!is_object($this->model) or get_class($this->model) != 'nosqlModel')
            throw new RessourceException("Could not " . __FUNCTION__ . " " . get_class($this) . " because the provided model is not of type nosqlModel", 908);
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            if ($nextPath !== null) {
                // recherche d'une clef en particulier
                $return = $this->getModel()->readOne($nextPath);
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, 1, session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
                $api->getOutput()->renderOk($message, $return);
            } else {
                //affichage de toutes les clefs
                $search = $this->filterSearchParams($input->getParams());
                $sort = $input->getJsonParam($this->getConfig('sortParam', 'sort'), '[]');
                $order = array();
                if (is_array($sort))
                    foreach ($sort as $k => $val)
                        $order[$val['property']] = (strtoupper(trim($val['direction'])) == 'DESC') ? -1 : 1;
                else
                    $order['_id'] = 1;
                $start = $input->getParam($this->getConfig('startParam', 'start'), 0);
                $max = $input->getParam($this->getConfig('limitParam', 'limit'), 30);
                $return = $this->getModel()->read($search, $order, $start, $max);
                $countResult = $this->getModel()->readCount($search);
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($return), $countResult, session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
                $api->getOutput()->renderOk($message, $return, $countResult);
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $newId = $this->getModel()->create($api->getInput()->getParams());
            $message = sprintf($this->getConfig('message_service_create', 'message service create'), $newId);
            $api->logInfo(930, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
            $api->getOutput()->renderOk($message, $newId);
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
            $nextPath = $api->getInput()->getElement($sessElPosition + 1);
            if ($nextPath !== null) {
                $return = $this->getModel()->update($nextPath, $api->getInput()->getParams());
                $message = sprintf($this->getConfig('message_service_update', 'message service update'), $nextPath);
                $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
                $api->getOutput()->renderOk($message, $return);
            } else {
                throw new RessourceException("could not " . __FUNCTION__ . " on " . get_class($this) . " because no id found in path ", 911);
            }
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getRessourceTrace(__FUNCTION__, false), 3);
        try {
            $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
            $nextPath = $api->getInput()->getElement($sessElPosition + 1);
            if ($nextPath !== null) {
                $return = $this->getModel()->delete($nextPath);
                $message = sprintf($this->getConfig('message_service_delete', 'message service delete'), $nextPath);
                $api->logInfo(970, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getRessourceTrace(__FUNCTION__, false), 1);
                $api->getOutput()->renderOk($message, $return);
            } else {
                throw new RessourceException("could not " . __FUNCTION__ . " on " . get_class($this) . " because no id found in path ", 911);
            }
        } catch (Exception $exc) {
            $api->logError(970, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            $api->getOutput()->renderError($exc->getCode(), $exc->getMessage());
        }
        return true;
    }

}

?>
