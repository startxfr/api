<?php

/**
 * Base class used for exposing inputs to the API. All inputs class should be derivated form this class or one of its descendant
 *
 * @class    DefaultInput
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
 */
class DefaultInput extends Configurable implements IInput {

    /**
     * used to store data exposed by this input component
     */
    protected $data = array();

    /**
     * construct the input object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        $id = (array_key_exists('id', $config)) ? $config["id"] : 'default';
        Api::logDebug(200, "Construct '" . $id . "' " . get_class($this) . " connector ", $config, 5);
        parent::__construct($config);
    }

    /**
     * initialize this instance and make it available and usable
     * @return self
     */
    public function init($doEvent =true) {
        if($doEvent)
            Event::trigger('input.init.before');
        Api::logDebug(210, "Init '" . $this->getConfig("id") . "' " . get_class($this) . " connector  with " . count($this->getAll()) . " params", $this->getAll(), 5);
        if($doEvent)
            Event::trigger('input.init.after');
        return $this;
    }

    /**
     * return value coresponding to the given key stored in self::$data. If no key found, the default value is returned.
     * @param   string  the searched key
     * @param   mixed   the default value to return if no key is found
     * @return  mixed
     */
    public function get($key, $default = null) {
        if (array_key_exists($key, $this->data))
            return $this->data[$key];
        else
            return $default;
    }

    /**
     * associate a value to the given key and store it in self::$data. If key already exist, value is replaced.
     * @param   string  the searched key
     * @param   mixed   the default value to return if no key is found
     * @return  self
     */
    public function set($key, $data) {
        $this->data[$key] = $data;
        return $this;
    }

    /**
     * return all values stored in self::$data
     * @return  array
     */
    public function getAll() {
        return $this->data;
    }

    /**
     * replace all values stored in self::$data by the new $data
     * @param   mixed   new content used to replace old one
     * @return  array
     */
    public function setAll($data) {
        $this->data = $data;
        return $this;
    }

}

?>