<?php

/**
 * Class used to access application informations of the application associated to the current session
 * == Config options ==
 * -------------------------------------------
 * param          | default       | description 
 * -------------------------------------------
 * collection     | 'application' | nosql collection name used for retriving application details
 * supported      | null          | array or comma separated string list of supported application
 * default_client | none          | default client to use when no application is declared
 * param_name     | app           | Name of the parameter used to pass application identity
 * param_input    | null          | identifier of the input method used to retrieve application name. Default use the default input object defined in running API
 * default_client | default       | 
 * 
 * @package  SXAPI.Input
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
 * @
 */
class ApplicationInput extends DefaultInput implements IInput {

    private $applicationStorage = null;
    private $cachedData = null;

    /**
     * construct the application input object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        parent::__construct($config);
        $api = Api::getInstance();
        $this->applicationStorage = $api->nosqlConnection->selectCollection($this->getConfig("collection",'application'));
        $this->setConfig('supported',Toolkit::string2Array($this->getConfig('supported')));
        if (is_null($this->applicationStorage))
            throw new InputException("could not get the application collection '" . $this->getConfig("collection",'application') . "' into nosql backend '" . Api::$nosqlApiBackend->base . "' datadase.", 202);
    }

    /**
     * initialize this instance and make it available and usable
     * @return self
     */
    public function init() {
        Event::trigger('input.init.before');
        $this->setApp();
        Api::logDebug(210, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector  setting to '" . Api::getInstance()->getInput('session')->get('application') . "'", Api::getInstance()->getInput('session')->get('application'), 4);
        Event::trigger('input.init.after');
        return $this;
    }

    public function setApp() {
        $sessionInput = Api::getInstance()->getInput('session');
        $defaultInput = Api::getInstance()->getInput($this->getConfig("param_input",''));
        $appParamName = $this->getConfig("param_name",'app');
        if ($defaultInput->get($appParamName) != '') {
            if (is_array($this->getConfig('supported')) and !in_array($defaultInput->get($appParamName), $this->getConfig('supported')))
                throw new InputException("could not set application '" . $defaultInput->get($appParamName) . "' as it is not a supported app for this API. See application section of your sxapi document", 220);
            $sessionInput->set('application', $defaultInput->get($appParamName));
        }
        elseif($sessionInput->get('application') == '') {
            $sessionInput->set('application', $this->getConfig('default_client','default'));
        }
        return $this;
    }

    public function loadCache() {
        $api = Api::getInstance();
        $this->cachedData = $this->applicationStorage->findOne(array("_id" => $api->getInput('session')->get('application')));
        if (is_null($this->cachedData))
            throw new InputException("could not find any '" . $api->getInput('session')->get('application') . "' application document in collection '" . $this->getConfig("collection",'application') . "'", 201);
        return $this;
    }

    public function getId() {
        return Api::getInstance()->getInput('session')->get('application');
    }

    public function get($key, $default = null) {
        if (is_null($this->cachedData))
            $this->loadCache();
        if (array_key_exists($key, $this->cachedData))
            return $this->cachedData[$key];
        else
            return $default;
    }

    public function set($key, $data) {
        if (is_null($this->cachedData))
            $this->loadCache();
        $this->applicationStorage->update(array("_id" => Api::getInstance()->getInput('session')->get('application')), array('$set' => array($key => $data)));
        $this->loadCache();
        return $this;
    }

    public function getAll() {
        if (is_null($this->cachedData))
            $this->loadCache();
        return $this->cachedData;
    }

    public function setAll($data) {
        if (is_null($this->cachedData))
            $this->loadCache();
        $this->applicationStorage->update(array("_id" => Api::getInstance()->getInput('session')->get('application')), array('$set' => $data));
        $this->loadCache();
        return $this;
    }

}

?>