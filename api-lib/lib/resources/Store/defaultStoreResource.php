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
        return $this;
    }

    public function getStorage() {
        return $this->storage;
    }

    protected function filterResults($results) {
        $out = array();
        foreach ($results as $elem) {
            $row = $this->filterParams($elem, "output");
            $out[] = $row;
        }
        return $out;
    }
    
}

?>
