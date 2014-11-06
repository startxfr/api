<?php

/**
 * Alias Resource
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class aliasResource extends linkableResource implements IResource {

    static public $ConfDesc = '{"class_name":"aliasResource",
                                "desc":"alias for a resource, resource conf is overridden by alias conf",
                                "properties":
	[
		{
			"name":"alias_id",
			"type":"string",
			"mandatory":"true",
			"desc":"id of the resource to alias"
		},
		{
			"name":"collection",
			"type":"string",
			"mandatory":"true",
			"desc":"collection of the store to query for the resource conf"
		},
                {
			"name":"find_filter",
			"type":"array",
			"mandatory":"false",
			"desc":"optional array of config arguments not to import from resource config"
		}
	]
}'
;
    
    private $aliasObject;
    
    public function __construct($config) {
        parent::__construct($config);
        $api = Api::getInstance();
        if ($this->getConfig('alias_id', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'alias_id' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'alias_id' attribute");
        }
        if ($this->getConfig('collection', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'collection' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'collection' attribute");
        }        
        $criteria = array('_id' => $this->getConfig('alias_id'));
        $filter = $this->getConfig('find_filter', array());
        $collect = $this->getConfig('collection');
        $db_connect = $api->nosqlConnection;
        $alias_config = $db_connect->selectCollection($collect)->findOne($criteria, $filter);
        $alias_object = $alias_config['class'];       
        $new_config = array_replace($config, $alias_config);
        $this->aliasObject = new $alias_object($new_config);
        $this->aliasObject->init();
    }
    
    public function createAction() {
        Api::getInstance()->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->aliasObject->createAction();
    }

    public function readAction() {       
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);       
        return $this->aliasObject->readAction();
    }
    
    public function updateAction() {
        Api::getInstance()->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->aliasObject->updateAction();
    }

    public function deleteAction() {
        Api::getInstance()->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->aliasObject->deleteAction();
    }

}

?>
