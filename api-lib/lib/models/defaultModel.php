<?php

/**
 * This resource class is abstract and should not be used as it.
 * Developpers can create a new model type by derivating from this class
 *
 * @class    defaultModel
 * @author   Dev Team <dev@startx.fr>
 * @see      Configurable
 * @link     https://github.com/startxfr/sxapi/wiki/Model
 */
abstract class defaultModel extends Configurable implements IModel {

    protected $storage = null;

    public function __construct($config = array(), $storageID = 'default') {
        $id = (array_key_exists('_id', $config)) ? $config["_id"] : 'default';
        Api::logDebug(500, "Construct '" . $id . "' " . get_class($this) . " model ", $config, 5);
        parent::__construct($config);
        $api = Api::getInstance();
        $this->storage = Api::getInstance()->getStore($storageID);
        if ($this->getStore() == false)
            throw new ModelException(get_class($this) . " require '" . $storageID . "' storage witch is unvailable from the storage factory.");
        $this->setConfig('output_filter',Toolkit::string2Array($this->getConfig('output_filter')));
        $this->setConfig('output_security_filter',Toolkit::string2Array($this->getConfig('output_security_filter')));
        if (is_string($this->getConfig('bind_vars')) and $this->getConfig('bind_vars') != '*' and $this->getConfig('bind_vars') != 'all')
            $this->setConfig('bind_vars', Toolkit::string2Array($this->getConfig('bind_vars')));
        if ($this->getConfig('id_key', '') == '') {
            $api->logError(506, get_class($this) . " resource config should contain the 'id_key' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ModelException(get_class($this) . " resource config should contain the 'id_key' attribute");
        }
    }

    public function getStore() {
        return $this->storage;
    }

    public function readOne($id) {
        $data = $this->readDetail(array($this->getConfig('id_key', "_id") => $id));
        if (is_array($data) and count($data) > 0)
            return $this->filterResult($data[0], false);
        else
            return array();
    }

    public function read($criteria = array(), $order = array(), $from = 0, $max = 30) {
        $result = $this->getStore()->read($this->getConfig('table'), $criteria, $order, $from, $max);
        if (is_null($result) or $result == false)
            return array();
        else
            return $this->filterResults($result);
    }

    public function readDetail($criteria = array(), $order = array(), $from = 0, $max = 30) {
        return $this->read($criteria, $order, $from, $max);
    }

    public function readCount($criteria = array()) {
        return $this->getStore()->readCount($this->getConfig('table'), $criteria);
    }

    public function create($data) {
        return $this->getStore()->create($this->getConfig('table'), $this->bindVars($data));
    }

    public function update($id, $data) {
        unset($data[$this->getConfig('id_key', '_id')]);
        return $this->getStore()->update($this->getConfig('table'), $this->getConfig('id_key', "_id"), $id, $this->bindVars($data));
    }

    public function delete($id) {
        return $this->getStore()->delete($this->getConfig('table'), $this->getConfig('id_key', "_id"), $id);
    }

    public function bindVars($vars) {
        $out = array();
        if ($this->getConfig('bind_vars') == false or $this->getConfig('bind_vars') == '*' or $this->getConfig('bind_vars') == 'all')
            $out = $vars;
        elseif (is_array($this->getConfig('bind_vars', array())) and is_array($vars))
            foreach ($vars as $k => $v)
                if (in_array($k, $this->getConfig('bind_vars', array())))
                    $out[$k] = $v;
        return $out;
    }

    public function bindFilter($filter) {
        return $filter;
    }

    public function getIDKey() {
        return $this->getConfig('id_key', null);
    }

    public function getKeys() {
        return $this->setConfig('output_filter',array());
    }

    public function getTable() {
        return $this->getConfig('table', null);
    }

    protected function filterResults($results, $outputFilter = true) {
        $out = array();
        if (is_array($this->getConfig('output_security_filter', null)) and is_array($results)) {
            foreach ($results as $k => $v)
                foreach ($v as $k2 => $v2)
                    if (!in_array($k2, $this->getConfig('output_security_filter', array())))
                        $out[$k][$k2] = $v2;
            $results = $out;
            $out = array();
        }
        if ($outputFilter and is_array($this->getConfig('output_filter', null)) and is_array($results)) {
            foreach ($results as $k => $v)
                foreach ($v as $k2 => $v2)
                    if (in_array($k2, $this->getConfig('output_filter', array())))
                        $out[$k][$k2] = $v2;
        }
        else
            $out = $results;
        return $out;
    }

    protected function filterResult($result, $outputFilter = true) {
        $out = array();
        if (is_array($this->getConfig('output_security_filter', null)) and is_array($result)) {
            foreach ($result as $k => $v)
                if (!in_array($k, $this->getConfig('output_security_filter', array())))
                    $out[$k] = $v;
            $result = $out;
            $out = array();
        }
        if ($outputFilter and is_array($this->getConfig('output_filter', null)) and is_array($result)) {
            foreach ($result as $k => $v)
                if (in_array($k, $this->getConfig('output_filter', array())))
                    $out[$k] = $v;
        }
        else
            $out = $result;
        return $out;
    }

}

?>