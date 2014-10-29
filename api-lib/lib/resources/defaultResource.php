<?php

/**
 * This resource class is abstract and should not be used as it.
 * Developpers can create a new authentification resource type by derivating from this class
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      Configurable
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class defaultResource extends Configurable implements IResource {

    static public $ConfDesc = '{"class_name":"defaultResource",
  "desc":"desc defaultResource",
  "propreties":
	[
		{
			"name":"_id",
			"type":"int",
			"mandatory":"true",
			"desc":"desc _id"
		},
		{
			"name":"class",
			"type":"string",
			"mandatory":"true",
			"desc":"desc class"
		},
		{
			"name":"force_output",
			"type":"string",
			"mandatory":"false",
			"desc":"desc class"
		},
		{
			"name":"desc",
			"type":"string",
			"mandatory":"false",
			"desc":"desc desc"
		}
	]
}'
;
    
    public function __construct($config) {
        $id = (array_key_exists('_id', $config)) ? $config["_id"] : 'default';
        Api::logDebug(900, "Load '" . $id . "' " . get_class($this) . " resource ", $config, 5);
        parent::__construct($config);
        $this->prepareFilters();
    }

    public function init() {
        $api = Api::getInstance();
        if ($this->isConfig('force_output')) {
            $api->logDebug(907, get_class($this) . " resource config has 'force_output' attribute set to " . $this->getConfig('force_output'), $this->getResourceTrace(__FUNCTION__, false));
            if($this->getConfig('force_output',false))
                $api->setOutputDefault($this->getConfig('force_output'));
        }
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logError(910, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__, false));
        return array(false, 910, "You can't perform a readAction with '" . get_class($this) . "' resource. " . get_class($this) . " should implement readAction method before using it.", $this->getResourceTrace(__FUNCTION__), 405);        
    }

    public function createAction() {
        $api = Api::getInstance();
        $api->logError(930, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__, false));
        return array(false, 930, "You can't perform a createAction with '" . get_class($this) . "' resource. " . get_class($this) . " should implement createAction method before using it.", $this->getResourceTrace(__FUNCTION__), 405);       
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logError(950, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__, false));
        return array(false, 950, "You can't perform a updateAction with '" . get_class($this) . "' resource. " . get_class($this) . " should implement updateAction method before using it.", $this->getResourceTrace(__FUNCTION__), 405);       
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logError(970, "Performing '" . __FUNCTION__ . "' on 'defaultResource' resource is not allowed. '" . get_class($this) . "' must implement '" . __FUNCTION__ . "'", $this->getResourceTrace(__FUNCTION__, false));
        return array(false, 970, "You can't perform a deleteAction with '" . get_class($this) . "' resource. " . get_class($this) . " should implement deleteAction method before using it.", $this->getResourceTrace(__FUNCTION__), 405);        
    }

    public function optionsAction() {
        $list = "";
        Api::getInstance()->logDebug(900, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        if (method_exists($this, 'readAction') === false)
            $list .= 'GET, ';
        if (method_exists($this, 'createAction') === false)
            $list .= 'POST, ';
        if (method_exists($this, 'updateAction') === false)
            $list .= 'PUT, ';
        if (method_exists($this, 'deleteAction') === false)
            $list .= 'DELETE, ';
        $list .= 'OPTIONS';
        header('Allow: ' . $list);
        exit;
    }

    public function getResourceTrace($method = null, $forDisplay = true, $other = null) {
        $api = Api::getInstance();
        $trace = $api->getTrace();
        $trace['class'] = get_class($this);
        $trace['method'] = $method;
        if ($forDisplay === false)
            $trace['config'] = $this->getConfigs();
        if (!is_null($other) and !is_array($other))
            $other = array($other);
        if (is_array($other))
            $trace = array_merge($trace, $other);
        return $trace;
    }
    
    public function filterParams( $params, $way ) {
        if ($way !== "output" && $way !== "input")
            return $params;
        $out = array();
        if ( ($filter_inc = $this->getConfig($way."_include_paramfilter", null)) !== null ) {
            if (is_string($filter_inc)) {
                $tmp_array = explode(":", $filter_inc);
                if (count($tmp_array) === 1 && $tmp_array[0] === "*")
                    return $params;
                else {
                    foreach($params as $key => $value) {
                        $out[$key] = $tmp_array[1]($value);
                    }
                }
            }
            else {
                foreach ($params as $key => $value) {
                    if (array_key_exists($key, $filter_inc)) {
                        $filter_val = $filter_inc[$key];
                        if (strpos($filter_val, ":") !== false) {
                            $tmp_array = explode(":", $filter_val);
                            $out[$tmp_array[1]] = $tmp_array[0]($value);
                        }
                        else
                            $out[$filter_val] = $value;
                    }                   
                }
            }
        }
        else if ( ($filter_exc = $this->getConfig($way."_exclude_paramfilter", null)) !== null) {
            if (is_string($filter_exc) && $filter_exc === "*")
                return $out;
            else {
                foreach ($params as $key => $value) {
                    if (!array_key_exists($key, $filter_exc))
                         $out[$key] = $value;
                }
            }
        }
        return $out;
    }
    
    private function transformFilter($filter, $include = true) {
        $new_filter = array();        
        if (($include) && (($filter[0] === "{" || $filter[0] === "[") && ($tmp_array = json_decode($filter)) !== null)) {
            foreach ($tmp_array as $elem) {
                $process = "";
                if (array_key_exists("process", $elem))
                    $process = $elem['process'] . ":";
                $new_filter[$elem['input']] = $process . $elem['map'];
            }
        }        
        else if (is_string($filter)){
            $tmp_array = explode(",", $filter);
            if (count($tmp_array) === 1 && (preg_match('/^(all|\*)/', $tmp_array[0]))) {
                $new_filter = "*";
                if (strpos($tmp_array[0], ":") !== false) {
                   $keyval = explode (":", $tmp_array[0]);
                   $new_filter.= ":" . $keyval[1];
                }
            }
            else {
                foreach ($tmp_array as $value) {
                    if (strpos($value, ":") !== false) {
                       $keyval = explode (":", $value);
                       $new_filter[$keyval[0]] = $keyval[1];
                    }
                    else {
                        $new_filter[$value] = $value;
                    }
                }
            }
        }
        else if ($include){
            foreach ($filter as $elem) {
                $process = "";
                if (array_key_exists("process", $elem) && is_callable($elem['process']))
                    $process = $elem['process'] . ":";
                $new_filter[$elem['input']] = $process . $elem['map'];
            }           
        }
        return $new_filter;
    }
    
    private function prepareFilters() {
        if ( ($input_include_paramfilter = $this->getConfig("input_include_paramfilter", null)) !== null ) {
            $this->setConfig("input_include_paramfilter", $this->transformFilter($input_include_paramfilter));
        }
        if ( ($input_exclude_paramfilter = $this->getConfig("input_exclude_paramfilter", null)) !== null ) {
            $this->setConfig("input_exclude_paramfilter", $this->transformFilter($input_exclude_paramfilter, false));
        }
        if ( ($output_include_paramfilter = $this->getConfig("output_include_paramfilter", null)) !== null ) {
            $this->setConfig("output_include_paramfilter", $this->transformFilter($output_include_paramfilter));
        }
        if ( ($output_exclude_paramfilter = $this->getConfig("output_exclude_paramfilter", null)) !== null ) {
            $this->setConfig("output_exclude_paramfilter", $this->transformFilter($output_exclude_paramfilter, false));
        }        
    }
    
}

?>
