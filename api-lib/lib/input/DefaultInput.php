<?php

/**
 * Class for logging detail into a file
 *
 * @author dev@startx.fr
 */
class DefaultInput extends Configurable implements IInput {

    /**
     * input data to store
     * @var array data to store
     */
    protected $data = array();

    public function __construct($config) {
        Api::logDebug(200, "Construct '" . $config["_id"] . "' " . get_class($this) . " connector ", $config, 5);
        parent::__construct($config);
    }

    public function init() {
        Api::logDebug(210, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector  with " . count($this->getAll()) . " params", $this->getAll(), 5);
        return $this;
    }

    public function get($key, $default = null) {
        if (array_key_exists($key, $this->data))
            return $this->data[$key];
        else
            return $default;
    }

    public function set($key, $data) {
        $this->data[$key] = $data;
        return $this;
    }

    public function getAll() {
        return $this->data;
    }

    public function setAll($data) {
        $this->data = $data;
        return $this;
    }

}

?>