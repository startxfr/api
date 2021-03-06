<?php

/**
 * Base class used for binding specific process to the API event. All plugin class should be derivated form this class or one of its descendant
 *
 * @class    defaultPlugin
 * @author   Mallowtek <mallowtek@gmail.com>
 * @link      https://github.com/startxfr/sxapi/wiki/Plugins
 */
class defaultPlugin extends Configurable implements IPlugin {

    /**
     * @var Singleton
     * @access private
     * @static
     */
    protected static $_instance = null;

    /**
     * The Plugin constructor. Do not directly instanciate this object and prefer using the DefaultPlugin::getInstance() static method for creating and accessing the plugin singleton object
     * This constructor will configure the config properties.
     *
     * @param mixed $config the plugin config stored in api backend
     * @return void
     */
    public function __construct($config) {
        $id = (array_key_exists('_id', $config)) ? $config["_id"] : 'default';
        Api::logDebug(100, "Load '" . $id . "' " . get_class($this) . " plugin ", $config, 5);
        parent::__construct($config);
    }

    /**
     * Method used to create and access unique instance of this class
     * if exist return it, if not, create and then return it
     *
     * @param string $config with the plugin config
     * @return DefaultPlugin singleton instance of DefaultPlugin Class
     */
    public static function getInstance($config = null) {
        $classname = static::getClass(); 
        if(!isset(static::$_instance[$classname])) { 
            static::$_instance[$classname] = new $classname($config); 
        } 
        return static::$_instance[$classname]; 
    } 
        
    public static function getClass() {
        return get_called_class();
    }
        
    /**
     * init the rendering object
     *
     * @return self
     */
    public function init() {
        Api::logDebug(310, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector", null, 5);
        return $this;
    }

}

?>
