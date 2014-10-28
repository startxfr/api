<?php

/**
 * Sequencer Resource
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class defaultSequenceResource extends defaultResource implements IResource {
    
    private $seqObject;
    
    public function __construct($config) {
        parent::__construct($config);
        $api = Api::getInstance();
        $configs = array();
        $options = array();
        foreach ($config['seq_conf'] as $value) {
            $configs[] = array('_id' => $value['id']);
            $options[$value['id']] = $value['action_name'];
        }
        $criteria = array('$or' => $configs);
        $filter = $this->getConfig('find_filter', array());
        $collect = $this->getConfig('collection');
        $db_connect = $api->nosqlConnection;
        $it_on_conf = $db_connect->selectCollection($collect)->find($criteria, $filter);        
        $this->seqObject = array();
        foreach ($it_on_conf as $conf) {
            $conf = array_replace($config, $conf);
            $object = $conf['class'];
            $obj = new $object($conf);
            $obj->init();
            $this->seqObject[] = array($obj, $options[$conf['_id']]);
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
