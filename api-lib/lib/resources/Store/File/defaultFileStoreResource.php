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
abstract class defaultFileStoreResource extends defaultStoreResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"defaultFileStoreResource",
        "desc":"Resource to access data in store",
        "properties":
	[
		{
			"name":"path",
			"type":"string",
			"mandatory":"true",
			"desc":"Path to local directory exposed by this resource"
		},{
			"name":"file",
			"type":"string",
			"mandatory":"false",
			"desc":"default filename used"
		}
	]
}'
    ;
    protected $storage;

    public function init() {
        parent::init();
        $api = Api::getInstance();
        if($this->getConfig('path', '') == '') {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'path' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'path' attribute");
        }
        $this->storage = $api->getStore($this->getConfig('store'));
        return $this;
    }

}

?>
