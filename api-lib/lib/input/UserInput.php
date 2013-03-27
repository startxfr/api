<?php

/**
 * Class for reading GET params
 *
 * @author dev@startx.fr
 */
class UserInput extends DefaultInput implements IInput {

    private $userStorage = null;
    private $cachedData = null;
    protected $isconnected = false;

    public function __construct($config) {
        parent::__construct($config);
    }

    public function init() {
        Api::logDebug(210, "Init '" . $this->getConfig("_id", 'user') . "' " . get_class($this) . " connector  setting to '" . Api::getInstance()->getInput('session')->get('user') . "'", Api::getInstance()->getInput('session')->get('user'), 4);
        return $this;
    }

    private function connect() {
        if (!$this->isconnected) {
            $api = Api::getInstance();
            try {
                $this->userStorage = $api->getStore($this->getConfig('store', 'sxapi'));
            } catch (Exception $e) {
                throw new InputException("could not connect to nosql storage because " . $e->getMessage(), 215);
            }
            if (is_null($this->userStorage))
                throw new InputException("could not get the user store '" . $this->getConfig('store', 'sxapi') . "'", 216);
            $this->isconnected = true;
        }
        return $this;
    }

    private function loadCache() {
        $api = Api::getInstance();
        $this->connect();
        $this->cachedData = $this->userStorage->readOne($this->getConfig('collection', 'users'), array($this->getConfig('id_field', "_id") => $api->getInput('session')->get('user')));
        if (is_null($this->cachedData))
            $this->cachedData = array();
        return $this;
    }

    public function getId() {
        return Api::getInstance()->getInput('session')->get('user');
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
        $this->connect();
        $this->userStorage->update(
                $this->getConfig('collection', 'users'), $this->getConfig('id_field', "_id"), Api::getInstance()->getInput('session')->get('user'), array($key => $data)
        );
        $this->loadCache();
        return $this;
    }

    public function getAll() {
        if (is_null($this->cachedData))
            $this->loadCache();
        return $this->cachedData;
    }

    public function setAll($data) {
        $this->connect();
        $this->userStorage->update(
                $this->getConfig('collection', 'users'), $this->getConfig('id_field', "_id"), Api::getInstance()->getInput('session')->get('user'), $data
        );
        $this->loadCache();
        return $this;
    }

}

?>