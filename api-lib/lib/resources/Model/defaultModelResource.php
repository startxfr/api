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
abstract class defaultModelResource extends linkableResource implements IResource {

    static public $ConfDesc = '{"class_name":"defaultModelResource",
                                "desc":"desc defaultModelResource",
                                "properties":
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
    
    protected $model;

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if ($this->getConfig('model', '') == '') {
            $api->logError(906, get_class($this) . " resource config should contain the 'model' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'model' attribute");
        }
        $this->model = $api->getModel($this->getConfig('model'));
        $this->setConfig('search_params', Toolkit::string2Array($this->getConfig('search_params')));
        return $this;
    }

    public function getModel() {
        return $this->model;
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

}

?>
