<?php

/**
 * Interface to define an input descriptor
 */
interface IInput {

    /**
     * Check and control the given configuration. Called by the constructor
     */
    public function __construct($config);

    /**
     * Initialize content into input description
     */
    public function init();

    /**
     * get a key param
     */
    public function get($key, $default = null);

    /**
     * set a key param
     */
    public function set($key, $array);

    /**
     * get all data (raw)
     */
    public function getAll();

    /**
     * Set all data (raw)
     */
    public function setAll($array);
}

/**
 * Interface to define a renderer object
 */
interface IOutput {

    /**
     * Check and control the given configuration. Called by the constructor
     */
    public function __construct($config);

    /**
     * Initialize content into input description
     */
    public function init();

    /**
     * Render the view
     *
     * @param array $content data to be rendered
     *
     */
    public function render($content);

    /**
     * Render the content exiting normally
     *
     * @param array $content data to be rendered
     *
     * @return bool
     */
    public function renderOk($message, $data);

    /**
     * Render the content exiting with error
     *
     * @param array $content data to be rendered
     *
     * @return bool
     */
    public function renderError($code, $message = '', $other = array());
}

/**
 * Interface to define a ressource object
 */
interface IRessource {

    /**
     * Check and control the given configuration. Called by the constructor
     */
    public function __construct($config);

    public function init();

    public function createAction();

    public function readAction();

    public function updateAction();

    public function deleteAction();

    public function optionsAction();
}

/**
 * Interface to define a Storage handler for connecting and read/write to various storage
 */
interface IStorage {

    /**
     * Check and control the given configuration. Called by the constructor
     */
    public function init();

    /**
     * Connect to the storage backend
     */
    public function connect();

    /**
     * Disconnect and reconnect from the storage backend
     */
    public function reconnect();

    /**
     * Disconnect from the storage backend
     */
    public function disconnect();
    /**
     * Disconnect from the storage backend
     */
    public function getNativeConnection();

    /**
     * Execute an insert action on this storage (INSERT on SQL) and return a boolean
     */
    public function create($table, $data);

    /**
     * Execute a search action on this storage (SELECT on SQL) and return resultSet
     */
    public function read($table, $criteria = array(), $order = array(), $start = 0, $stop = 30);

    /**
     * Execute a search action on this storage (SELECT on SQL) and return resultSet
     */
    public function readOne($table, $criteria = array());

    /**
     * Execute a search action on this storage (SELECT on SQL) and return resultSet
     */
    public function readCount($table, $criteria = array());

    /**
     * Execute an update action on this storage (UPDATE on SQL) and return a boolean
     */
    public function update($table, $key, $id, $data);

    /**
     * Execute a delete action on this storage (DELETE on SQL) and return a boolean
     */
    public function delete($table, $key, $id);

    /**
     * Should implement a destructor to perform automatic disconnect at the end of process
     */
    public function __destruct();
}

/**
 * Interface to define a Model container with generics methods to access data
 */
interface IModel {

    /**
     * Constructor of a model need a storage to access data
     */
    public function __construct($storage);

    /**
     * Access the storage object
     */
    public function getStore();

    /**
     * Control given list of data and filter out only data described by this model
     */
    public function bindVars($vars);

    /**
     * Execute a search action and get the total result, excluding pagging
     */
    public function readCount($criteria = array());

    /**
     * Execute a search action on this model and retrive unique row by ID
     */
    public function readOne($id);

    /**
     * Execute a search action on this model (SELECT on SQL) and return resultSet
     */
    public function read($criteria = array(), $order = array(), $start = 0, $stop = 30);

    /**
     * Execute a search action on this model (SELECT on SQL) and return resultSet with detail information (LEFT JOIN)
     */
    public function readDetail($criteria = array(), $order = array(), $start = 0, $stop = 30);

    /**
     * Execute an insert action on this model (INSERT on SQL) and return a boolean
     */
    public function create($data);

    /**
     * Execute an update action on this model (UPDATE on SQL) and return a boolean
     */
    public function update($id, $data);

    /**
     * Execute a delete action on this model (DELETE on SQL) and return a boolean
     */
    public function delete($id);
}

?>