<?php

/**
 * This model is used to manipulate data stored in a mysql Store.
 *
 * @package  SXAPI.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModel, mysqlModelRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Model
 */
class mysqlModel extends defaultModel implements IModel {

    public function __construct($config = array(), $storageID = 'default') {
        parent::__construct($config, $storageID);
        $api = Api::getInstance();
        if (!is_object($this->getStore()) or get_class($this->getStore()) != 'mysqlStore')
            throw new ModelException("Could not " . __FUNCTION__ . " " . get_class($this) . " because '" . $storageID . "' store is not of type mysqlStore", 508);
        if ($this->getConfig('table', '') == '') {
            $api->logError(506, get_class($this) . " ressource config should contain the 'table' attribute", $this->getRessourceTrace(__FUNCTION__, false));
            throw new ModelException(get_class($this) . " ressource config should contain the 'table' attribute");
        }
    }

}

?>