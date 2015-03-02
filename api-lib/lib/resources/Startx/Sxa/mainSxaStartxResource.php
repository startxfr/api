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
class mainSxaStartxResource extends defaultResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"mainSxaStartxResource",
        "desc":"get list of resources available for the SXA project",
        "properties": [
        
        ]
      }';
    private $modules = array();
    
    
    public function init() {
        parent::init();
        if(!$this->isConfig('modules')) {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'modules' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'modules' attribute");
        }
        foreach($this->getConfig('modules', array()) as $key => $resourceID) {
            $this->addModule($key, $resourceID);
        }
        if(!$this->hasModule('actualite')) {
            throw new ResourceException(" resource '" . $this->getConfig('_id') . "' should have 'actualite' declared on his module list", 87);
        }
        return $this;
    }

    public function hasModule($key) {
        if(strpos($key, ',') !== false) {
            $r = true;
            $list = explode(',', $key);
            foreach($list as $k) {
                if(!array_key_exists($k, $this->modules)) {
                    $r = false;
                }
            }
            return $r;
        }
        else {
            return array_key_exists($key, $this->modules);
        }
    }

    public function getModule($key) {
        if($this->hasModule($key)) {
            return $this->modules[$key];
        }
        else {
            throw new ResourceException("SXA module '" . $key . "' is not declared in resource '" . $this->getConfig('_id') . "'", 85);
        }
    }

    public function addModule($key, $resource) {
        $api = Api::getInstance();
        if(is_string($resource)) {
            $this->modules[$key] = $api->getConfiguredResource($resource);
        }
        else {
            $this->modules[$key] = $resource;
        }
        return true;
    }

}
