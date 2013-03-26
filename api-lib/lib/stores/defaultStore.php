<?php

/**
 * Class for logging detail into a file
 *
 * @author dev@startx.fr
 */
abstract class defaultStore extends Configurable implements IStorage {

    protected $connection = null;
    protected $isconnected = false;
    public $lastQuery = null;
    public $lastResult = null;

    public function __construct($config) {
        Api::logDebug(400, "Construct '" . $config["_id"] . "' " . get_class($this) . " connector ", $config, 5);
        parent::__construct($config);
    }

    function __destruct() {
        $this->disconnect();
    }

    public function init() {
        Api::logDebug(410, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector ", null, 5);
        if ($this->getConfig('server') == '')
            throw new StoreException("store config should contain the 'server' attribute");
        if ($this->getConfig('base') == '')
            throw new StoreException("store config should contain the 'base' attribute");
        return $this;
    }

    public function connect() {
        Api::getInstance()->logDebug(410, "'" . __FUNCTION__ . "' '" . get_class($this) . "' ", $this->getConfigs(), 5);
        $this->isconnected = false;
        return $this;
    }

    public function reconnect() {
        $this->disconnect()->connect();
        return $this;
    }

    public function disconnect() {
        Api::getInstance()->logDebug(415, "'" . __FUNCTION__ . "' '" . get_class($this) . "' ", $this->getConfigs(), 5);
        $this->connection = null;
        $this->isconnected = false;
        return $this;
    }

    public function getNativeConnection() {
        return $this->connection;
    }

    public function read($table, $criteria = array(), $order = array(), $start = 0, $stop = 30) {
        Api::getInstance()->logWarn(420, "'" . __FUNCTION__ . "' '" . get_class($this) . "' is not suppose to rely on defaultStore '" . __FUNCTION__ . "' method", array('table'=>$table, 'criteria'=>$criteria, 'order'=>$order, 'start'=>$start, 'stop'=>$stop),4);
        $table = $criteria = $order = $start = $stop = null;
        return array();
    }

    public function readOne($table, $criteria = array()) {
        Api::getInstance()->logWarn(421, "'" . __FUNCTION__ . "' '" . get_class($this) . "' is not suppose to rely on defaultStore '" . __FUNCTION__ . "' method", array('table'=>$table, 'criteria'=>$criteria),4);
        $table = $criteria = null;
        return array();
    }

    public function readCount($table, $criteria = array()) {
        Api::getInstance()->logWarn(422, "'" . __FUNCTION__ . "' '" . get_class($this) . "' is not suppose to rely on defaultStore '" . __FUNCTION__ . "' method", array('table'=>$table, 'criteria'=>$criteria),4);
        $table = $criteria = null;
        return 0;
    }

    public function create($table, $data) {
        Api::getInstance()->logWarn(430, "'" . __FUNCTION__ . "' '" . get_class($this) . "' is not suppose to rely on defaultStore '" . __FUNCTION__ . "' method", array('table'=>$table, 'data'=>$data),4);
        $table = $data = null;
        return false;
    }

    public function update($table, $key, $id, $data) {
        Api::getInstance()->logWarn(450, "'" . __FUNCTION__ . "' '" . get_class($this) . "' is not suppose to rely on defaultStore '" . __FUNCTION__ . "' method", array('table'=>$table, 'key'=>$key, 'id'=>$id, 'data'=>$data),4);
        $table = $key = $id = $data = null;
        return false;
    }

    public function delete($table, $key, $id) {
        Api::getInstance()->logWarn(470, "'" . __FUNCTION__ . "' '" . get_class($this) . "' is not suppose to rely on defaultStore '" . __FUNCTION__ . "' method", array('table'=>$table, 'key'=>$key, 'id'=>$id),4);
        $table = $key = $id = null;
        return false;
    }

}

?>