<?php

/**
 * This resource is used to interact (read - write) with mysql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class mysqlSqlStoreResource extends defaultSqlStoreResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"mysqlSqlStoreResource",
        "desc":"Resource to access mysql storage",
        "properties":
	[
		{
			"name":"dataset",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the dataset in which to search. corespond to a table in SQL world, or a colllection in NoSQL."
		},{
			"name":"sortParam",
			"type":"string",
			"mandatory":"false",
			"desc":"input param used for sorting search"
		},{
			"name":"startParam",
			"type":"string",
			"mandatory":"false",
			"desc":"input param used for pagging"
		},{
			"name":"limitParam",
			"type":"string",
			"mandatory":"false",
			"desc":"input param used for limiting search result number"
		}
	]
}';

    public function init() {
        parent::init();
        if(!is_object($this->storage) or substr(get_class($this->storage), 0, 5) != 'mysql')
            throw new ResourceException("Could not " . __FUNCTION__ . " " . get_class($this) . " because the provided store is not of type mysql", 908);
        if($this->getConfig('dataset', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'dataset' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'dataset' attribute");
        }
        $utf = "SET NAMES utf8 ; ";
        $this->getStorage()->execQuery($utf);
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
                $return = $this->readOne($nextPath);
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, 1, session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return);
            }
            else {
                //affichage de la liste des résultats
                $return = $this->readList($api->getInput()->getParams());
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($return), $return[0], session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return[1], $return[0]);
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
            $newId = $this->getStorage()->create($this->getConfig('dataset'), $this->filterParams($api->getInput()->getParams(), "input"));
            $message = sprintf($this->getConfig('message_service_create', 'message service create'), $newId);
            $api->logInfo(930, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            return array(true, $message, $newId);
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
                $data = $api->getInput()->getParams();
                unset($data[$this->getConfig('id_key', '_id')]);
                $return = $this->getStorage()->update($this->getConfig('dataset'), $this->getConfig('id_key', "_id"), $nextPath, $this->filterParams($data, "input"));
                $message = sprintf($this->getConfig('message_service_update', 'message service update'), $nextPath);
                $api->logInfo(950, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return);
            }
            else {
                throw new ResourceException("could not " . __FUNCTION__ . " on " . get_class($this) . " because no id found in path ", 911);
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
                $return = $this->getStorage()->delete($this->getConfig('dataset'), $this->getConfig('id_key', "_id"), $nextPath);
                $message = sprintf($this->getConfig('message_service_delete', 'message service delete'), $nextPath);
                $api->logInfo(970, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return);
            }
            else {
                throw new ResourceException("could not " . __FUNCTION__ . " on " . get_class($this) . " because no id found in path ", 911);
            }
        }
        catch(Exception $exc) {
            $api->logError(970, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

    protected function readOne($id) {
        $data = $this->getStorage()->readOne($this->getConfig('dataset'), array($this->getConfig('id_key', "_id") => $id));
        return $this->filterParams($data, "output");
    }

    protected function readList($criteria) {
        $api = Api::getInstance();
        $search = $this->filterParams($criteria, "input");
        $sort = $api->getInput()->getJsonParam($this->getConfig('sortParam', '_sort'), '[]');
        $order = array();
        if(is_array($sort)) {
            foreach($sort as $k => $val) {
                $name = (is_array($val) and array_key_exists('property', $val)) ? $val['property'] : $val;
                $order[$name] = (strtoupper(trim($val['direction'])) == 'DESC') ? 'DESC' : 'ASC';
            }
        }
        else {
            $order[$this->getConfig('id_key', "id")] = 'ASC';
        }
        $countResult = $this->getStorage()->readCount($this->getConfig('dataset'));
        $start = (int) $api->getInput()->getParam($this->getConfig('startParam', '_start'), 0);
        $max = (int) $api->getInput()->getParam($this->getConfig('limitParam', '_limit'), $countResult);
        $data = $this->getStorage()->read($this->getConfig('dataset'), $search, $order, $start, $max);
        $return = $this->filterResults($data);
        return array($countResult, $return);
    }

}

?>
