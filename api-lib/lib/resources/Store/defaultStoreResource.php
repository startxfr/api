<?php

/**
 * This resource class is abstract and should not be used as it.
 * Developpers can create a new model resource type by derivating from this class
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class defaultStoreResource extends linkableResource implements IResource {

    static public $ConfDesc = '{"class_name":"defaultModelResource",
                                "desc":"desc defaultModelResource",
                                "propreties":
	[
		{
			"name":"model",
			"type":"string",
			"mandatory":"true",
			"desc":"desc model"
		},
		{
			"name":"search_params",
			"type":"string",
			"mandatory":"false",
			"desc":"desc search_params"
		}
	]
}'
;
    
    protected $storage;

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('store', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'store' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'store' attribute");
        }
        if ($this->getConfig('id_key', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'id_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'id_key' attribute");
        }
        $this->storage = $api->getStore($this->getConfig('store'));
        if (is_string($this->getConfig('bind_vars', null)) and $this->getConfig('bind_vars') != '*' and $this->getConfig('bind_vars') != 'all')
            $this->setConfig('bind_vars', Toolkit::string2Array($this->getConfig('bind_vars')));
        if ($this->getConfig('output_filter', null) != null)
            $this->setConfig('output_filter',Toolkit::string2Array($this->getConfig('output_filter')));
        if ($this->getConfig('output_security_filter', null) != null)
            $this->setConfig('output_security_filter',Toolkit::string2Array($this->getConfig('output_security_filter')));
        return $this;
    }

    public function getStorage() {
        return $this->storage;
    }

    public function filterSearchParams($params) {
        $search = array();
        if ($this->getConfig('search_params') == '*' or $this->getConfig('search_params') == 'all' or is_null($this->getConfig('search_params')))
            return $params;
        if (is_string($this->getConfig('search_params')))
            $this->setConfig('search_params', Toolkit::string2Array($this->getConfig('search_params')));
        foreach ($this->getConfig('search_params', array()) as $key) {
            if ($params[$key] != null)
                $search[$key] = $params[$key];
        }
        return $search;
    }

    protected function filterResults($results, $outputFilter = true) {
        $out = array();
        if (is_array($this->getConfig('output_security_filter', null)) and is_array($results)) {
            foreach ($results as $k => $v) {
                foreach ($v as $k2 => $v2) {
                    if (!in_array($k2, $this->getConfig('output_security_filter', array())))
                        $out[$k][$k2] = $v2;
                }
            }
            $results = $out;
            $out = array();
        }
        if ($outputFilter and is_array($this->getConfig('output_filter', null)) and is_array($results)) {
            foreach ($results as $k => $v) {
                foreach ($v as $k2 => $v2) {
                    if (in_array($k2, $this->getConfig('output_filter', array())))
                        $out[$k][$k2] = $v2;
                }
            }
        }
        else
            $out = $results;
        return $out;
    }

    protected function filterResult($result, $outputFilter = true) {
        $out = array();                
        if (is_array($this->getConfig('output_security_filter', null)) and is_array($result)) {            
            foreach ($result as $k => $v) {
                if (!in_array($k, $this->getConfig('output_security_filter', array())))
                    $out[$k] = $v;
            }
            $result = $out;
            $out = array();
        }
        if ($outputFilter and is_array($this->getConfig('output_filter', null)) and is_array($result)) {            
            foreach ($result as $k => $v) {
                if (in_array($k, $this->getConfig('output_filter', array())))
                    $out[$k] = $v;
            }
        }
        else
            $out = $result;
        return $out;
    }
    
    public function bindVars($vars) {
        $out = array();
        if ($this->getConfig('bind_vars') == false or $this->getConfig('bind_vars') == '*' or $this->getConfig('bind_vars') == 'all')
            $out = $vars;
        elseif (is_array($this->getConfig('bind_vars', array())) and is_array($vars)) {
            foreach ($vars as $k => $v) {
                if (in_array($k, $this->getConfig('bind_vars', array())))
                    $out[$k] = $v;
            }
        }
        return $out;
    }
    
}

?>
