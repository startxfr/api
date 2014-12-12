<?php

/**
 * This resource is used to interact (read - write) with mysql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class mysqlsearchStoreResource extends mysqlStoreResource implements IResource {

    static public $ConfDesc = '{"class_name":"mysqlsearchStoreResource",
  "desc":"Resource to access mysql storage",
  "properties":
	[	
	]
}';
          
    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $search = $api->getInput()->getParam($this->getConfig('searchParam', '_search'), "");
            if ($search !== "") {
                var_dump('toto');
                exit(0);
            }
            else
                return parent::readAction();
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),500);
        }
        return true;
    }

}

?>
