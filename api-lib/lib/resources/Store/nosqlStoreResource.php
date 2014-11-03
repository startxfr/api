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

    static public $ConfDesc = '{"class_name":"nosqlStoreResource",
  "desc":"Resource to access nosql storage",
  "properties":
	[
		{
			"name":"collection",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the collection in which to search"
		},
                {
			"name":"filter_mongoid",
			"type":"bool",
			"mandatory":"false",
			"desc":"if true id_key will be converted to a MongoId object"
		},
                {
			"name":"filter_mongodate",
			"type":"string",
			"mandatory":"false",
			"desc":"list of parameters to be converted in MongoDate object"
		}
	]
}';
      
    public function init() {
        parent::init();
        if (!is_object($this->storage) or get_class($this->storage) != 'nosqlStore')
            throw new ResourceException("Could not " . __FUNCTION__ . " " . get_class($this) . " because the provided store is not of type nosql", 908);
        if ($this->getConfig('collection', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'collection' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'collection' attribute");
        }
        $this->prepareMongoDateFilter();
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
                $data = $this->getStorage()->readOne($this->getConfig('collection'), array($this->getConfig('id_key', '_id') => $this->convertMongoId($nextPath)));
                $return = $this->filterParams($data, "output");
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, 1, session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return);
            } else {
                //affichage de toutes les clefs
                $search = $this->filterParams($input->getParams(), "input");
                $sort = $input->getJsonParam($this->getConfig('sortParam', 'sort'), '[]');
                $order = array();
                if (is_array($sort)) {
                    foreach ($sort as $k => $val) {
                        $order[$val['property']] = (strtoupper(trim($val['direction'])) == 'DESC') ? -1 : 1;
                    }
                }
                else
                    $order['_id'] = 1;
                $start = $input->getParam($this->getConfig('startParam', 'start'), 0);
                $max = $input->getParam($this->getConfig('limitParam', 'limit'), 30);                                                                
                $data = $this->getStorage()->read($this->getConfig('collection'), $search, $order, $start, $max);
                $return =  $this->filterResults(iterator_to_array($data,false));            
                $countResult = $this->getStorage()->readCount($this->getConfig('collection'), $search);            
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
            $data = $api->getInput()->getParams();
            if (!array_key_exists($this->getConfig("id_key", "_id"), $data)) {
                if ($this->getConfig("filter_mongoid", false) !== false)
                    $data[$this->getConfig("id_key", "_id")] = new MongoId();
                else
                    throw new ResourceException("Could not create new entry without 'id_key' key.");
            }
            $newId = $this->getStorage()->create($this->getConfig('collection'), $this->filterParams($data, "input"));            
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
                $data = $api->getInput()->getParams();        
                unset($data[$this->getConfig('id_key', '_id')]);
                $return = $this->getStorage()->update($this->getConfig('collection'), $this->getConfig('id_key', '_id'), $this->convertMongoId($nextPath), $this->filterParams($data, "input"));                
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
                $return = $this->getStorage()->delete($this->getConfig('collection'), $this->getConfig('id_key', '_id'), $this->convertMongoId($nextPath));                
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

    protected function filterResults($results) {
        if (is_array($results))
            foreach ($results as $k => $v) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $this->getConfig('id_key', '_id') and is_object($v2) and get_class($v2) == 'MongoId')
                        $results[$k][$k2] = (string) $v2;
                    elseif (is_object($v2) and get_class($v2) == 'MongoDate')
                        $results[$k][$k2] = date('Y-m-d H:i:s', (string) $v2->sec);
                }
            }
        return parent::filterResults($results);
    }
    
    protected function convertMongoId($id) {
        if ($this->getConfig("filter_mongoid", false) !== false && get_class($id) != 'MongoId') {
            if (strlen($id) === 24 && ctype_xdigit($id))
                return new MongoId($id);        
            return new MongoId();
        }
        return $id;
    }
    
    protected function convertMongoDate($params) {
        if (($date_filter = $this->getConfig("filter_mongodate", null)) !== null) {
            foreach ($params as $key => $val) {
                foreach ($date_filter as $elem) {
                    if ($key === $elem && (ctype_digit($val) || ($val = strtotime($val)) !== false))
                        $params[$key] = new MongoDate($val);
                }
            }
        }
    }
    
    public function filterParams($params, $way) {
        if ($way === "input") {
            if (array_key_exists("_id", $params)) {
                $params['_id'] = $this->convertMongoId($params['_id']);                
            }
            $this->convertMongoDate($params);
        }
        else if ($way === "output") {
            foreach ($params as $k => $v) {
                if ($k == $this->getConfig('id_key', '_id') and is_object($v) and get_class($v) == 'MongoId')
                    $params[$k] = (string) $v;
                elseif (is_object($v) and get_class($v) == 'MongoDate')
                    $params[$k] = date('Y-m-d H:i:s', $v->sec);
            }    
        }
        return parent::filterParams($params, $way);
    }
    
    private function prepareMongoDateFilter() {
        if (($date_filter = $this->getConfig("filter_mongodate", null)) !== null) {
            if (is_string($date_filter)) {
                $this->setConfig("filter_mongodate", explode(",", $date_filter));
            }
            else
                $this->setConfig("filter_mongodate", null);
        }
    }
    
}

?>
