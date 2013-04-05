<?php

/**
 * Configurable objects
 *
 * This class could be used every time you need to handle configuration data for a given object.
 *
 * Example:
 * <code>
 * $config = new Configurable(array('key'=>'value'));
 * echo $config->getConfig('key');
 * // return 'value'
 * </code>
 *
 * @category SXAPI
 * @package  SXAPI
 * @author   Dev Team <dev@startx.fr>
 * @copyright Copyright (c) 2003-2013 startx.fr
 * @license https://github.com/startxfr/sxapi/blob/master/licence.txt
 */
class Configurable {

    /**
     * information about the application
     * @var object stdClass
     */
    private $config;

    /**
     * Create a new object representing the request
     */
    public function __construct($config = null) {
        $this->setConfigs($config);
    }

    /**
     * return the full config array or only part of it
     * @return array full config or fragment
     */
    public function getConfig($key = null, $default = null) {
        if ($key != null and is_array($this->config) and array_key_exists($key, $this->config))
            return $this->config[$key];
        else
            return $default;
    }

    /**
     * return the full config array or only part of it
     * @return array full config or fragment
     */
    public function isConfig($key = null) {
        if ($key != null and is_array($this->config) and array_key_exists($key, $this->config))
            return true;
        else
            return false;
    }

    /**
     * return the full config array or only part of it
     * @return array full config or fragment
     */
    public function getConfigs() {
        return $this->config;
    }

    /**
     * insert or update config elements in config
     * @return object itself return $this to chain methods
     */
    public function setConfig($key, $value = null) {
        $this->config[$key] = $value;
        return $this;
    }


    /**
     * insert or update config elements in config
     * @return object itself return $this to chain methods
     */
    public function setConfigs($config = null, $convert = false) {
        if ($convert)
            $config = Toolkit::array2Object($config);
        $this->config = $config;
        return $this;
    }

    /**
     * render a json string representing the configuration
     * @return string json formated string with the config data
     */
    public function serialize() {
        return json_encode($this->config);
    }

}

?>