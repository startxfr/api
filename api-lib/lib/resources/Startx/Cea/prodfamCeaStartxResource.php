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
class prodfamCeaStartxResource extends mysqlStoreResource implements IResource {

    static public $ConfDesc = '{"class_name":"prodfamCeaStartxResource",
  "desc":"get list of product categories available for the STARTX-CEA project",
  "properties": [  ]
}';

    public function init() {
        parent::init();
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        $query = $this->getConfig('query', "SELECT * FROM ref_prodfamille ORDER BY treePathKey ASC");
        try {
            $utf = "SET NAMES utf8 ; ";
            $this->getStorage()->execQuery($utf);
            $data = $this->getStorage()->execQuery($query);
            $return = $this->filterResults($data);
            $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($return));
            $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
            return array(true, $message, $return, count($return));
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

    public function createAction() {
        return $this->readAction();
    }

    public function updateAction() {
        return $this->readAction();
    }

    public function deleteAction() {
        return $this->readAction();
    }

}

?>
