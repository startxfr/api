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
class nosqlStoreResource extends defaultStoreResource implements IResource {

    public function init() {
        parent::init();
        if (!is_object($this->storage) or get_class($this->storage) != 'nosqlStore')
            throw new ResourceException("Could not " . __FUNCTION__ . " " . get_class($this) . " because the provided store is not of type nosql", 908);
        if ($this->getConfig('collection', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'collection' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'collection' attribute");
        }
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            if ($nextPath !== null) {
                // recherche d'une clef en particulier
                $return = $this->getModel()->readOne($nextPath);
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, 1, session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return);
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
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return, $countResult);
            }
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
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
            return array(true, $message, $newId);
        } catch (Exception $exc) {
            $api->logError(930, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
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
                return array(true, $message, $return);
            } else {
                throw new ResourceException("could not " . __FUNCTION__ . " on " . get_class($this) . " because no id found in path ", 911);
            }
        } catch (Exception $exc) {
            $api->logError(950, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
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
                return array(true, $message, $return);
            } else {
                throw new ResourceException("could not " . __FUNCTION__ . " on " . get_class($this) . " because no id found in path ", 911);
            }
        } catch (Exception $exc) {
            $api->logError(970, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

}

?>
