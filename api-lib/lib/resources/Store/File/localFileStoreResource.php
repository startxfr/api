<?php

/**
 * This resource is used to interact (read - write) with local data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class localFileStoreResource extends defaultFileStoreResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"localFileStoreResource",
        "desc":"Resource to access local storage",
        "properties":
	[
	]
}';

    public function init() {
        parent::init();
        if(!is_object($this->storage) or substr(get_class($this->storage), 0, 5) != 'local')
            throw new ResourceException("Could not " . __FUNCTION__ . " " . get_class($this) . " because the provided store is not of type local", 908);
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
            $nextPath = $api->getInput()->getElement($sessElPosition + 1);
            if($nextPath !== null) {
                // recherche d'une entrée
                $return = $this->getStorage()->read($nextPath);
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, 1, $nextPath);
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return, 1);
            }
            else {
                //affichage de la liste des résultats
                $return = $this->getStorage()->getLs();
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($return) . ' file(s)');
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return, count($return));
            }
        }
        catch(Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
            $nextPath = $api->getInput()->getElement($sessElPosition + 1);
            if($nextPath !== null) {
                // recherche d'une entrée
                $content = file_get_contents("php://input");
                $this->getStorage()->create($nextPath, $content);
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), $nextPath, strlen($content));
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $nextPath, strlen($content));
            }
            else {
                throw new ResourceException('you must provide a filename (last part of API URL) ', 911);
            }
        }
        catch(Exception $exc) {
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
            if($nextPath !== null) {
                $content = file_get_contents("php://input");
                $this->getStorage()->update($nextPath, null, null, $content);
                $message = sprintf($this->getConfig('message_service_update', 'message service update'), $nextPath, strlen($content));
                $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $nextPath, strlen($content));
            }
            else {
                throw new ResourceException('you must provide a filename (last part of API URL) ', 951);
            }
        }
        catch(Exception $exc) {
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
            if($nextPath !== null) {
                $this->getStorage()->delete($nextPath);
                $message = sprintf($this->getConfig('message_service_delete', 'message service delete'), $nextPath);
                $api->logInfo(970, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, true);
            }
            else {
                throw new ResourceException('you must provide a filename (last part of API URL) ', 971);
            }
        }
        catch(Exception $exc) {
            $api->logError(970, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

}
?>
