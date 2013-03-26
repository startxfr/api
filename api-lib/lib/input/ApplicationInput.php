<?php

/**
 * Class for reading GET params
 *
 * @author dev@startx.fr
 */
class ApplicationInput extends DefaultInput implements IInput {

    private $applicationStorage = null;
    private $cachedData = null;

    public function __construct($config) {
        parent::__construct($config);
        $api = Api::getInstance();
        $this->applicationStorage = $api->nosqlConnection->selectCollection($this->getConfig("collection",'application'));
        if (is_null($this->applicationStorage))
            throw new InputException("could not get the application collection '" . $this->getConfig("collection",'application') . "' into nosql backend '" . $api->nosqlApiBackend->base . "' datadase.", 202);
    }

    public function init() {
        $this->setApp();
        Api::logDebug(210, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector  setting to '" . Api::getInstance()->getInput('session')->get('application') . "'", Api::getInstance()->getInput('session')->get('application'), 4);
        return $this;
    }

    public function setApp() {
        $sessionInput = Api::getInstance()->getInput('session');
        $defaultInput = Api::getInstance()->getInput();
        if ($defaultInput->get('app') != '') {
            if (is_array($this->getConfig('supported')) and !in_array($defaultInput->get('app'), $this->getConfig('supported')))
                throw new InputException("could not set application '" . $defaultInput->get('app') . "' as it is not a supported app for this API. See application section of your sxapi document", 220);
            $sessionInput->set('application', $defaultInput->get('app'));
        }
        elseif($sessionInput->get('application') == '') {
            $sessionInput->set('application', $this->getConfig('default_client'));
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