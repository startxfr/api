<?php

/**
 * Alias Resource
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class conditionalSequenceResource extends defaultSequenceResource implements IResource {

    static public $ConfDesc = '{"class_name":"defaultSequenceResource",
                                "desc":"Enable sequencing of resources",
                                "properties":
	[
		{
			"name":"dataset",
			"type":"string",
			"mandatory":"true",
			"desc":"dataset to query"
		},
		{
			"name":"seq_conf",
			"type":"array",
			"mandatory":"true",
			"desc":"array of object containing the id of the resource and the name of the method to use. 
                        Can contain an optional argument \'conf_override\' which is an array of config arguments to override the ones of the ressource."
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
    
    private $seqObject;
    
    public function __construct($config) {
        parent::__construct($config);
        $api = Api::getInstance();
        if ($this->getConfig('dataset', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'dataset' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'dataset' attribute");
        }
        if ($this->getConfig('seq_conf', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'seq_conf' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'seq_conf' attribute");
        }
        $configs = array();
        $options = array();
        foreach ($config['seq_conf'] as $value) {
            $configs[] = array('_id' => $value['id']);            
            $options[$value['id']]['action_name'] = (array_key_exists("action_name", $value) ? $value['action_name'] : "readAction");
            $options[$value['id']]['over_conf'] = (array_key_exists("conf_override", $value) ? $value['conf_override'] : array());            
        }
        $criteria = array('$or' => $configs);
        $filter = $this->getConfig('find_filter', array());
        $collect = $this->getConfig('dataset');
        $db_connect = $api->nosqlConnection;
        $it_on_conf = $db_connect->selectCollection($collect)->find($criteria, $filter);        
        $this->seqObject = array(); 
        foreach ($it_on_conf as $conf) {
            $conf = array_replace($config, $conf, $options[$conf['_id']]['over_conf']);
            $object = $conf['class'];
            $obj = new $object($conf);
            $obj->init();
            $this->seqObject[] = array($obj, $options[$conf['_id']]['action_name']);
        }
    }
    
    public function createAction() {
        Api::getInstance()->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);       
        $output = array();
        $i = 0;
        $message = "";
        $data = array();
        $result = array();      
        foreach ($this->seqObject as $resource ) {
            $resource[0]->setPrevOutput($result);
            $output[] = $resource[0]->$resource[1]();
            $message .= $output[$i][1] . " ";
            $result = $output[$i];
            if ($output[$i][0] === true)
                $data = array_merge($data, $output[$i][2]);
            else if (isset($output[$i][3]))
                $data = array_merge($data, $output[$i][3]);               
            $i++;
        }        
        return array(true, $message, $data );
    }

    public function readAction() {  
        return $this->createAction();        
    }
    
    public function updateAction() {
        Api::getInstance()->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->createAction();
    }

    public function deleteAction() {
        Api::getInstance()->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->createAction();
    }

}

?>
