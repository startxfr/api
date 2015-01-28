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
class produitsCeaStartxResource extends mysqlStoreResource implements IResource {

    static public $ConfDesc = '{"class_name":"produitsCeaStartxResource",
        "desc":"get list of products available for the STARTX-CEA project",
        "properties": [  ]
      }';

    public function init() {
        parent::init();
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $sessElPosition = $api->getInput()->getElementPosition($this->getConfig('path'));
            $nextPath = $api->getInput()->getElement($sessElPosition + 1);
            if ($nextPath !== null) {
                // recherche d'une clef en particulier 

                $query = $this->getConfig('query_readone', "SELECT * FROM produit WHERE id_prod = '%s'");
                $query = sprintf($query, $nextPath);
                $utf = "SET NAMES utf8 ; ";
                $this->getStorage()->execQuery($utf);
                $data = $this->getStorage()->execQuery($query);
                if (is_array($data) and count($data) > 0 and is_array($data[0])) {
                    $data = $data[0];
                }
                $return = $this->filterParams($data, "output");
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), 1, 1, session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return);
            } else {
                $query = $this->getConfig('query', "SELECT * FROM produit WHERE stillAvailable_prod = '1' AND prodredhat_prod = '1' ORDER BY id_prod ASC LIMIT 0,100");
                $utf = "SET NAMES utf8 ; ";
                $this->getStorage()->execQuery($utf);
                $data = $this->getStorage()->execQuery($query);
                $return = $this->filterResults($data);
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($return));
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $return, count($return));
            }
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
