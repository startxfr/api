<?php

/**
 * Class used to access user informations of the curently logged user (see session)
 *
 * @package  SXAPI.Input
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
 */
class UserInput extends DefaultInput implements IInput {

    /**
     * store used for getting informations about the current user
     */
    private $userStorage = null;
    /**
     * cached data of the current user
     */
    private $cachedData = null;
    /**
     * boolean set to true when storage is connected
     */
    protected $isconnected = false;

    /**
     * construct the user input object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        parent::__construct($config);
    }

    /**
     * initialize this instance and make it available and usable
     * @return self
     */
    public function init() {
        Event::trigger('input.init.before');
        Api::logDebug(210, "Init '" . $this->getConfig("id", 'user') . "' " . get_class($this) . " connector  setting to '" . Api::getInstance()->getInput('session')->get('user') . "'", Api::getInstance()->getInput('session')->get('user'), 4);
        Event::trigger('input.init.after');
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

    public function setAll($data, $add = false) {
        $this->connect();
        $result = $this->userStorage->update(
                $this->getConfig('collection', 'users'),
                $this->getConfig('id_field', "_id"),
                Api::getInstance()->getInput('session')->get('user'),
                $data,
                $add
        );
        $this->loadCache();
        return $this;
    }

}

?>