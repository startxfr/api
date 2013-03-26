<?php

class DefaultModel implements IModel {

    protected $storage = null;
    protected $table = 'default';
    protected $idkey = "_id";
    protected $keys = array("_id", 'name');

    public function __construct($storageID = 'default') {
        $this->storage = Api::getInstance()->getStore($storageID);
        if($this->storage == false)
                throw new ModelException(get_class($this)." require '".$storageID."' storage witch is unvailable from the storage manager.");
    }

    public function getStore() {
        return $this->storage;
    }

    public function getIDKey() {
        return $this->idkey;
    }

    public function getKeys() {
        return $this->keys;
    }

    public function getTable() {
        return $this->table;
    }

    public function bindVars($vars) {
        $out = array();
        if (is_array($this->keys) and is_array($vars))
            foreach ($vars as $k => $v)
                if (in_array($k, $this->keys))
                    $out[$k] = $v;
        return $out;
    }

    public function bindFilter($filter) {
        return $filter;
    }

    public function readOne($id) {
        $data = $this->readDetail(array($this->getIDKey() => $id));
        if (is_array($data) and count($data) > 0)
            return $data[0];
        else
            return array();
    }

    public function read($criteria = array(), $order = array(), $from = 0, $max = 30) {
        return $this->storage->read($this->getTable(), $criteria, $order, $from, $max);
    }

    public function readDetail($criteria = array(), $order = array(), $from = 0, $max = 30) {
        return $this->read($criteria, $order, $from, $max);
    }

    public function readCount($criteria = array()) {
        return $this->storage->searchCount($this->getTable(), $criteria);
    }

    public function create($data) {
        unset($data[$this->getIDKey()]);
        return $this->storage->create($this->getTable(), $this->bindVars($data));
    }

    public function update($id, $data) {
        unset($data[$this->getIDKey()]);
        return $this->storage->update($this->getTable(), $this->getIDKey(), $id, $this->bindVars($data));
    }

    public function delete($id) {
        return $this->storage->delete($this->getTable(), $this->getIDKey(), $id);
    }


}

?>