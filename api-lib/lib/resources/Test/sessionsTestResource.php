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
class sessionsTestResource extends googlecalendarTestResource implements IResource {

    static public $ConfDesc = '{"class_name":"sessionsFormationStartxResource",
  "desc":"Resource to access session in nosql storage",
  "properties":
	[
		{
			"name":"dataset",
			"type":"string",
			"mandatory":"true",
			"desc":"name of the dataset in which to search"
		}
	]
}';
      
    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $sqlStore = $api->getStore($this->getConfig('external_store', "mysql")); 
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            if ($nextPath !== null) {
                // recherche d'une clef en particulier
                $session = $this->getStorage()->readOne($this->getConfig('dataset'), array($this->getConfig('id_key', '_id') => $this->convertMongoId($nextPath)));
                $data = $this->populateSession($sqlStore, $session);
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
                $data = $this->getStorage()->read($this->getConfig('dataset'), $search, $order, $start, $max);                
                $return =  $this->filterResults(iterator_to_array($data,false), "output");                   
                for ($i = 0 ; $i < count($return) ; $i++) {
                    $res = $this->populateSession($sqlStore, $return[$i]);
                    $return[$i]['location'] = $res['location']; 
                    $return[$i]['trainer'] = $res['trainer'];
                    $return[$i]['students'] = $res['students'];
                }
                $countResult = $this->getStorage()->readCount($this->getConfig('dataset'), $search);            
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
            $newId = $this->getStorage()->create($this->getConfig('dataset'), $this->filterParams($data, "input"));            
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
                $return = $this->getStorage()->update($this->getConfig('dataset'), $this->getConfig('id_key', '_id'), $this->convertMongoId($nextPath), $this->filterParams($data, "input"));                
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
                $return = $this->getStorage()->delete($this->getConfig('dataset'), $this->getConfig('id_key', '_id'), $this->convertMongoId($nextPath));                
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
