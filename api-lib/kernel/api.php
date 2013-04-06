<?php

/**
 * SXAPI Main class
 *
 * This Class is a singleton and the main entry class for all the SXAPI process.
 * Developper who want to create a new SXAPI instance server should instanciate it as follow
 *
 * Example:
 * <code>
 * Api::getInstance()->load()->execute();
 * </code>
 *
 * @see      Configurable
 * @link     https://github.com/startxfr/sxapi/wiki/API-Document
 *
 * @category SXAPI
 * @package  SXAPI
 * @author   Dev Team <dev@startx.fr>
 * @copyright Copyright (c) 2003-2013 startx.fr
 * @license https://github.com/startxfr/sxapi/blob/master/licence.txt
 */
class Api extends Configurable {

    /**
     * name of the default API name to use if no apiID is given when instanciating the Api Class. Should be a existing key recorded in the api_collection collections stored in nosql backend
     */
    public $defaultApiID = 'sample';

    /**
     * JSON string with various parameters used for basic functionning of the Api Object. This property is critical and should contain:
     * <ul>
     * <li>connection: mongodb connection string used for connecting to the nosql backend</li>
     * <li>base: name of the nosql database to use for retriving Api core elements (api documents, logs, app, session, models and resources)</li>
     * <li>api_collection : name of the nosql collection used to store API config document</li>
     * </ul>
     */
    public $nosqlApiBackend = '{
        "connection" : "mongodb://dev:dev@127.0.0.1:27017",
        "base" : "sxapi",
        "api_collection" : "sxapi.api"
    }';

    /**
     * static property to store unique instance of this singleton class
     */
    private static $_instance = null;

    /**
     * the nosql connection resource used to communicate with Api internal backend. Used for accessing documents used in core function (model, resource, input, output)
     */
    public $nosqlConnection = null;

    /**
     * list of all input connector. This property is populated when loading input connector as described in the Api Config document. 'id' config key is used as key identifier
     */
    private $inputs = array();

    /**
     * ID of the default input connector to use. This property is automaticaly set when the "default" property is set to 'true' into one of the input section of the Api Config document
     */
    private $inputDefault = '';

    /**
     * list of all output connector. This property is populated when loading output connector as described in the Api Config document. 'id' config key is used as key identifier
     */
    private $outputs = array();

    /**
     * ID of the default output connector to use. This property is automaticaly set when the "default" property is set to 'true' into one of the output section of the Api Config document
     */
    private $outputDefault = '';

    /**
     * the store manager. This property is used a a cache for all instanciated stores used into the Api. Only required store are dynamicaly loaded when needed. 'id' config key is used as key identifier
     */
    private $stores = array();

    /**
     * ID of the default store connector to use. This property is automaticaly set when the "default" property is set to 'true' into one of the store section of the Api Config document
     */
    private $storeDefault = '';

    /**
     * the model manager. This property is used a a cache for all instanciated models used into the Api. Only required models are dynamicaly loaded when needed. 'id' config key is used as key identifier
     */
    private $models = array();

    /**
     * the resource manager. This property is used a a cache for all instanciated resources used into the Api. Only required resources are dynamicaly loaded when needed. 'id' config key is used as key identifier
     */
    private $resources = array();

    /**
     * The Api constructor. Do not directly instanciate this object and prefer using the Api::getInstance() static method for creating and accessing the Api singleton object
     * This constructor will decode the $nosqlApiBackend and try to connect to the nosql backend.
     * If an exception is catched, call the exitOnError method for exiting program.
     *
     * @param string $defaultApiID with the api id to use for creation
     * @return void
     */
    public function __construct($defaultApiID = null) {
        if (!is_null($defaultApiID))
            $this->defaultApiID = $defaultApiID;
        // connect nosql backend immediately to get log storage support
        try {
            $this->nosqlApiBackend = json_decode($this->nosqlApiBackend);
            $nosqlConnection = new Mongo($this->nosqlApiBackend->connection);
            $this->nosqlConnection = $nosqlConnection->selectDB($this->nosqlApiBackend->base);
        } catch (Exception $exc) {
            echo "Error communicating with nosql backend : " . $exc->getMessage();
            exit;
        }
    }

    /**
     * Method used to create and access unique instance of this class
     * if exist return it, if not, create and then return it
     *
     * @param string $defaultApiID with the api id to use for creation
     * @return Api singleton instance of Api Class
     */
    public static function getInstance($defaultApiID = null) {
        if (is_null(self::$_instance))
            self::$_instance = new Api($defaultApiID);
        return self::$_instance;
    }

    /**
     * Method used to load the Api itself and init relyings connectors
     * This method will load the current Api configuration document, from the nosql backend, and store it.
     * It will then start the loading and initializing of Input, Output and Store connectors.
     * If an exception is catched, call the exitOnError method for exiting program.
     * @param string $defaultApiID with the api id to use for creation
     * @return \Api instance Api for chaining
     */
    public function load() {
        Event::trigger('api.begin');
        $this->loadApi();
        Event::trigger('api.load.begin');
        try {
            $this->loadPlugins();
            Event::trigger('api.load.plugins');
            $this->loadInputFactory()->initInputFactory();
            Event::trigger('api.load.input');
            $this->loadOutputFactory()->initOutputFactory();
            Event::trigger('api.load.output');
            $this->loadStoreFactory()->initStoreFactory();
            Event::trigger('api.load.store');
            Event::trigger('api.load.end');
            return $this;
        } catch (Exception $exc) {
            $this->exitOnError($exc->getCode(), "Error when loading api because " . $exc->getMessage(), $exc);
        }
        return $this;
    }

    /**
     * Load the Api configuration
     * If 'api' param is received form the client, it will set it as the $defaultApiID. Then it will retreive the API config document from the nosql backend and store it configuration into this Api instance
     * If an exception is catched, call the exitOnError method for exiting program.
     * @return \Api instance Api for chaining
     */
    private function loadApi() {
        try {
            if ($_REQUEST['api'] != "")
                $this->defaultApiID = $_REQUEST['api'];
            $api = $this->nosqlConnection->selectCollection($this->nosqlApiBackend->api_collection)->findOne(array("_id" => $this->defaultApiID));
            if (is_null($api))
                $this->exitOnError(3, "could not find '" . $this->defaultApiID . "' api document in '" . $this->nosqlApiBackend->api_collection . "' collection on '" . $this->nosqlApiBackend->base . "' database", $this->nosqlConnection);
            else
                $this->setConfigs($api);
            $this->logInfo(2, "Loaded version " . $this->getConfig('version', '0.0') . " of '" . $this->getConfig("_id", "_id") . "' API.");
            return $this;
        } catch (Exception $e) {
            $this->exitOnError(3, "could not get api document because noSql backend failure. " . $e->getMessage(), $e);
        }
    }

    /**
     * Load the plugins sections required by the Api config document
     * This method will loop throught the plugin section of the Api config document and try to load all the required plugins connectors
     * @return \Api instance Api for chaining
     * @throws ApiException if an error occur when instanciating plugin connector
     */
    private function loadPlugins() {
        if (is_array($this->getConfig('plugins'))) {
            $this->logDebug(20, "Loading " . count($this->getConfig('plugins')) . " plugin.", null, 4);
            $listPlugins = array();
            foreach ($this->getConfig('plugins') as $pluginconf) {
                if ($pluginconf['id'] == '')
                    $this->logError(16, " plugin config should contain the 'id' attribute", $pluginconf);
                else {
                    try {
                        $configPlugin = $this->nosqlConnection->selectCollection($this->getConfig("plugin_collection", "plugin"))->findOne(array("_id" => $pluginconf['id']));
                    } catch (Exception $e) {
                        $this->logWarn(13, "Could not load '" . $pluginconf["id"] . "' plugin. " . $e->getMessage(), $e->getTrace());
                    }
                    if (is_null($configPlugin) or $configPlugin["_id"] == '')
                        $this->logError(18, " plugin config could not be found in plugins instances", $pluginconf);
                    else {
                        $this->logDebug(17, "Plugin '" . $pluginconf['id'] . "' found in resource backend", $configPlugin, 5);
                        if ($configPlugin['class'] == '') {
                            $this->logError(17, " plugin '" . $pluginconf['id'] . "' config should contain the 'class' attribute", $configPlugin);
                        } else {
                            $pluginName = $configPlugin['class'];
                            if (class_exists($pluginName)) {
                                try {
                                    $configPlugin = array_merge($configPlugin, $pluginconf);
                                    $pluginName::getInstance($configPlugin);
                                    $listPlugins[] = $pluginName;
                                } catch (Exception $e) {
                                    $this->logWarn(13, "Could not load '" . $pluginconf["id"] . "' plugin. " . $e->getMessage(), $e->getTrace());
                                }
                            }
                            else
                                $this->logWarn(11, "Could not set '" . $pluginconf["id"] . "' plugin because class " . $pluginName . " doesn't exist");
                        }
                    }
                }
            }
            $this->logInfo(10, "Loaded " . count($listPlugins) . " plugin (" . implode(',', array_keys($listPlugins)) . ")", $listPlugins, 3);
        }
        return $this;
    }

    /**
     * Load the inputs sections required by the Api config document
     * This method will loop throught the input section of the Api config document and try to load all the required inputs connectors
     * @return \Api instance Api for chaining
     * @throws ApiException if an error occur when instanciating input connector
     */
    private function loadInputFactory() {
        if (is_array($this->getConfig('inputs'))) {
            $this->logDebug(20, "Loading " . count($this->getConfig('inputs')) . " input connector.", null, 4);
            $this->inputs = array();
            foreach ($this->getConfig('inputs') as $inputconf) {
                $inputName = $inputconf['class'];
                if (class_exists($inputName)) {
                    try {
                        $this->inputs[$inputconf["id"]] = new $inputName($inputconf);
                        if ($inputconf['default'] === true) {
                            $this->inputDefault = $inputconf["id"];
                        }
                    } catch (Exception $e) {
                        unset($this->inputs[$inputconf["id"]]);
                        $this->logWarn(23, "Could not load '" . $inputconf["id"] . "' input connector. " . $e->getMessage(), $e->getTrace());
                        throw new ApiException("we could not load '" . $inputconf["id"] . "' input connector. " . $e->getMessage(), 23);
                    }
                }
                else
                    $this->logWarn(21, "Could not set '" . $inputconf["id"] . "' input connector. because class " . $inputName . " doesn't exist");
            }
            $this->logInfo(20, "Loaded " . count($this->inputs) . " input connector (" . implode(',', array_keys($this->inputs)) . ") and default set to '" . $this->inputDefault . "'", array_keys($this->inputs), 3);
        }
        return $this;
    }

    /**
     * Initiate the loaded inputs sections
     * This method will loop throught the previously loaded input section and try to init each input connector
     * @return \Api instance Api for chaining
     * @throws ApiException if an error occur when initializing input connector
     */
    private function initInputFactory() {
        $this->logDebug(21, "initializing " . count($this->inputs) . " input connector", null, 4);
        foreach ($this->inputs as $iid => $input) {
            try {
                $input->init();
            } catch (Exception $e) {
                unset($this->inputs[$iid]);
                $this->logWarn(25, "Could not init '" . $iid . "' input connector. " . $e->getMessage(), $e->getTrace());
                throw new ApiException("we could not init '" . $iid . "' input connector. " . $e->getMessage(), 25);
            }
        }
        if (count($this->inputs) == 0)
            throw new ApiException("no input connector available (see logs for more detail)", 26);
        if (!array_key_exists('application', $this->inputs))
            throw new ApiException("application input connector is required. Please add it to your api document", 27);
        if (!array_key_exists('session', $this->inputs))
            throw new ApiException("session input connector is required. Please add it to your api document", 28);
        return $this;
    }

    /**
     * Get an input connector
     * return the input connector coresponding to the given $id. If no $id is given, or if $id = 'default', then the default input connector is returned. If $id doesn't exist, then also return the default connector and record a log warning trace.
     * @return \defaultInput the input connector instance coresponding to the requested $id
     */
    public function getInput($id = null) {
        if (is_null($id) or trim($id) == '' or trim($id) == 'default')
            return $this->inputs[$this->inputDefault];
        elseif (is_array($this->inputs) and array_key_exists($id, $this->inputs))
            return $this->inputs[$id];
        else {
            $this->logWarn(27, "the '" . $id . "' input connector could not be found in curently loaded input list. Check if it's declared on api config document ");
            return $this->inputs[$this->inputDefault];
        }
    }

    /**
     * Load the outputs sections required by the Api config document
     * This method will loop throught the output section of the Api config document and try to load all the required outputs connectors
     * @return \Api instance Api for chaining
     * @throws ApiException if an error occur when instanciating output connector
     */
    private function loadOutputFactory() {
        if (is_array($this->getConfig('outputs'))) {
            $this->logDebug(30, "Loading " . count($this->getConfig('outputs')) . " output connector", null, 4);
            $this->outputs = array();
            foreach ($this->getConfig('outputs') as $outputconf) {
                $outputName = $outputconf['class'];
                if (class_exists($outputName)) {
                    try {
                        $this->outputs[$outputconf["id"]] = new $outputName($outputconf);
                        if ($outputconf['default'] === true) {
                            $this->outputDefault = $outputconf["id"];
                        }
                    } catch (Exception $e) {
                        unset($this->outputs[$outputconf["id"]]);
                        $this->logWarn(33, "Could not load '" . $outputconf["id"] . "' output connector. " . $e->getMessage(), $e->getTrace());
                        throw new ApiException("we could not load '" . $outputconf["id"] . "' output connector. " . $e->getMessage(), 33);
                    }
                }
                else
                    $this->logWarn(31, "Could not load '" . $outputconf["id"] . "' output connector. because class " . $outputName . " doesn't exist");
            }
            if ($this->getInput()->getOutputFormat() != '' and array_key_exists($this->getInput()->getOutputFormat(), $this->outputs)) {
                $this->outputDefault = $this->getInput()->getOutputFormat();
                $this->logDebug(34, "default output connector set to '" . $this->outputDefault . "' by request input param ( ?format=" . $this->getInput()->getOutputFormat() . ' )');
            }
            $this->logInfo(30, "Loaded " . count($this->outputs) . " output connector (" . implode(',', array_keys($this->outputs)) . ") and default set to '" . $this->outputDefault . "'", array_keys($this->outputs), 3);
        }
        return $this;
    }

    /**
     * Initiate the loaded outputs sections
     * This method will loop throught the previously loaded output section and try to init each output connector
     * @return \Api instance Api for chaining
     * @throws ApiException if an error occur when initializing output connector
     */
    private function initOutputFactory() {
        $this->logDebug(31, "Initalizing " . count($this->outputs) . " output connector", null, 4);
        foreach ($this->outputs as $oid => $output) {
            try {
                $output->init();
            } catch (Exception $e) {
                unset($this->outputs[$oid]);
                $this->logWarn(35, "Could not init '" . $oid . "' output connector. " . $e->getMessage(), $e->getTrace());
                throw new ApiException("we could not init '" . $oid . "' output connector. " . $e->getMessage(), 35);
            }
        }
        if (count($this->outputs) == 0)
            throw new ApiException("no output connector available (see logs for more detail)", 36);
        return $this;
    }

    /**
     * Get an output connector
     * return the output connector coresponding to the given $id. If no $id is given, or if $id = 'default', then the default output connector is returned. If $id doesn't exist, then also return the default connector and record a log warning trace.
     * @return \defaultoutput the output connector instance coresponding to the requested $id
     */
    public function getOutput($id = null) {
        if (is_null($id) or trim($id) == '' or trim($id) == 'default')
            return $this->outputs[$this->outputDefault];
        elseif (is_array($this->outputs) and array_key_exists($id, $this->outputs))
            return $this->outputs[$id];
        else
            $this->logWarn(37, "the '" . $id . "' output connector could not be found in curently loaded output list. Check if it's declared on api config document ");
        return $this->outputs[$this->outputDefault];
    }

    public function setOutputDefault($id = null) {
        if (is_array($this->outputs) and array_key_exists($id, $this->outputs)) {
            $this->outputDefault = $id;
            $this->getInput()->format = $id;
        }
        return $this->outputDefault;
    }

    /**
     * Load the stores sections required by the Api config document
     * This method will loop throught the store section of the Api config document and try to load all the required stores connectors
     * @return \Api instance Api for chaining
     * @throws ApiException if an error occur when instanciating store connector
     */
    private function loadStoreFactory() {
        if (is_array($this->getConfig('stores'))) {
            $this->logDebug(40, "Loading " . count($this->getConfig('stores')) . " stores connector", null, 4);
            $this->stores = array();
            foreach ($this->getConfig('stores') as $storeconf) {
                $storeName = $storeconf['class'];
                if (class_exists($storeName)) {
                    try {
                        $this->stores[$storeconf["id"]] = new $storeName($storeconf);
                        if ($storeconf['default'] === true) {
                            $this->storeDefault = $storeconf["id"];
                        }
                    } catch (Exception $e) {
                        unset($this->stores[$storeconf["id"]]);
                        $this->logWarn(43, "Could not load '" . $storeconf["id"] . "' store connector. " . $e->getMessage(), $e->getTrace());
                        throw new ApiException("we could not load '" . $storeconf["id"] . "' store connector. " . $e->getMessage(), 43);
                    }
                }
                else
                    $this->logWarn(42, "Could not load '" . $storeconf["id"] . "' store connector. class '$storeName' doesn't exist", $storeconf);
            }
            $this->logInfo(40, "Loaded " . count($this->stores) . " store connector (" . implode(',', array_keys($this->stores)) . ") and default set to '" . $this->storeDefault . "'", array_keys($this->stores), 3);
        }
        return $this;
    }

    /**
     * Initiate the loaded stores sections
     * This method will loop throught the previously loaded store section and try to init each store connector
     * @return \Api instance Api for chaining
     * @throws ApiException if an error occur when initializing store connector
     */
    private function initStoreFactory() {
        $this->logDebug(41, "Initializing " . count($this->stores) . " stores connector", null, 4);
        foreach ($this->stores as $sid => $store) {
            try {
                $store->init();
            } catch (Exception $e) {
                unset($this->stores[$sid]);
                $this->logWarn(45, "Could not init '" . $sid . "' store connector. " . $e->getMessage(), $e->getTrace());
                throw new ApiException("we could not init '" . $sid . "' store connector. " . $e->getMessage(), 45);
            }
        }
        if (count($this->stores) == 0)
            throw new ApiException("no store connector available (see logs for more detail)", 46);
        return $this;
    }

    /**
     * Get a store connector
     * return the store connector coresponding to the given $id. If no $id is given, or if $id = 'default', then the default store connector is returned. If $id doesn't exist, then also return the default connector and record a log warning trace.
     * @return \defaultStore the store connector instance coresponding to the requested $id
     */
    public function getStore($id = null) {
        if (is_null($id) or trim((string) $id) == '' or trim((string) $id) == 'default')
            return $this->stores[$this->storeDefault];
        elseif (is_array($this->outputs) and array_key_exists((string) $id, $this->stores))
            return $this->stores[(string) $id];
        else
            $this->logWarn(47, "the '" . $id . "' store connector could not be found in curently loaded stores list. Check if it's declared on api config document ");
        return $this->stores[$this->storeDefault];
    }

    /**
     * Get a model connector
     * return the model connector coresponding to the given $id. Dynamicaly load it and cache it if not already required.
     * @return \defaultModel the model connector instance coresponding to the requested $id
     * @throws ApiException If no $id is given, or if $id is null. If $id doesn't exist, is not well configured (no 'class' or 'store' key) or is not instanciable.
     */
    public function getModel($id) {
        if (is_null($id) or trim($id) == '') {
            $this->logWarn(51, "trying to access model with a null id.");
            throw new ApiException("you must give a model name", 51);
        } elseif (is_array($this->models) and array_key_exists($id, $this->models)) {
            $this->logDebug(52, "Returning cached model '" . $id . "'");
            return $this->models[$id];
        } else {
            $config = $this->nosqlConnection->selectCollection($this->getConfig("model_collection", "models"))->findOne(array("_id" => $id));
            if (is_null($config) or $config["_id"] == '')
                throw new ApiException("Can't find the model config for '" . $id . "' in stored models", 50);
            $this->logDebug(53, "Model '" . $id . "' found in model backend", $config, 5);
            if ($config['class'] == '') {
                $this->logError(54, " model '$id' config should contain the 'class' attribute", $config);
                throw new ApiException(" model '$id' config should contain the 'class' attribute");
            }
            $modelName = $config['class'];
            if ($config['store'] == '') {
                $this->logError(54, " model '$id' config should contain the 'store' attribute", $config);
                throw new ApiException(" model '$id' config should contain the 'store' attribute");
            }
            if (class_exists($modelName)) {
                try {
                    $this->logDebug(55, "Adding model '" . $modelName . "'with id '" . $id . "' into api model cache.");
                    $this->models[$id] = new $modelName($config, $config['store']);
                    return $this->models[$id];
                } catch (Exception $e) {
                    $this->logWarn(56, "we could not load '" . $id . "' model instance. " . $e->getMessage(), $e->getTrace());
                    throw new ApiException("error when creating '$modelName' with store '" . $config['store'] . "'. See logs for more informations.", 56);
                }
            } else {
                $this->logWarn(55, "'" . $id . "' model connector could not be found in api model cache and class '" . $modelName . "' doesn't exist thus could not be created.");
                throw new ApiException("model '" . $id . "' doesn't exist and can't be created. Please add '" . $modelName . "' model class or change your model name", 55);
            }
        }
    }

    /**
     * Get a resource connector
     * return the resource connector coresponding to the given $id. Dynamicaly load it, initialize it and cache it if not already required.
     * @return \defaultResource the resource connector instance coresponding to the requested $id
     * @throws ApiException If no $id is given, or if $id is null. If $id doesn't exist, is not well configured (no 'class' or 'store' key) or is not instanciable.
     */
    public function getResource($id, $config = array()) {
        if (is_null($id) or trim($id) == '') {
            $this->logWarn(61, "trying to access resource with a null id.");
            throw new ApiException("you must give a resource name", 61);
        } elseif (is_array($this->resources) and array_key_exists($id, $this->resources)) {
            $this->logDebug(62, "Returning cached resource '" . $id . "'");
            if (is_array($config) and count($config) > 0)
                $this->resources[$id] = new $id();
            return $this->resources[$id];
        } else {
            if (class_exists($id) !== false) {
                $this->logDebug(63, "Start loading and caching resource '" . $id . "'");
                $this->resources[$id] = new $id($config);
                $this->logDebug(64, "Initializing resource '" . $id . "'");
                $this->resources[$id]->init();
                return $this->resources[$id];
            } else {
                $this->logWarn(66, "could not find resource class '$id'.");
                throw new ApiException("Resource class '$id' doesn't exist.", 66);
            }
        }
    }

    /**
     * execute the resource action, render it and exit;
     * @return void end of program. exit
     */

    /**
     * Execute the resource action, render it and exit program;
     * this is the main method to start the executing of the requested resource. Each resource is responsible for output delivery of its content. This method will:
     * - Find the requested resource (using the getResourceConfig method)
     * - Obtain and Start this resource (using the getResource method)
     * - Select the requested action to perform (according to the http method)
     * - Check if ACL rules apply to this resource node and check if session context is compliant to theses ACL rules
     * - Execute the requested action of the requested resource. This resource will then perform his task and manage to output to produce
     * @return \Api instance Api for chaining
     * @throws ApiException If resource is not acessible or controlled by ACL rules.
     */
    public function execute() {
        try {
            Event::trigger('api.execute.before');
            $config = $this->getResourceConfig($this->getInput()->getElements(), $this->getConfig('tree'));
            $resource = $this->getResource($config['class'], $config);
            $actionName = 'readAction';
            switch ($this->getInput()->getMethod()) {
                case 'post':
                    $actionName = 'createAction';
                    break;
                case 'put':
                    $actionName = 'updateAction';
                    break;
                case 'delete':
                    $actionName = 'deleteAction';
                    break;
                case 'options':
                    $actionName = 'optionsAction';
                    break;
                default:
                    $actionName = 'readAction';
                    break;
            }
            if (method_exists($resource, $actionName) === false)
                $actionName = 'readAction';
            if (method_exists($resource, $actionName) === false) {
                $this->logWarn(84, "action $actionName is not implemented in '" . $config['class'] . "' resource.", $config);
                throw new ApiException("action $actionName is not implemented in '" . $config['class'] . "' resource", 5);
            } else {
                if (array_key_exists('acl', $config) and is_array($config['acl'])) {
                    if (!is_array($config['acl']['user']) and ($config['acl']['user'] == '*' or $config['acl']['user'] == ''))
                        $users = '*';
                    elseif (is_array($config['acl']['user']))
                        $users = $config['acl']['user'];
                    else
                        $users = explode(',', $config['acl']['user']);
                    if (!is_array($config['acl']['application']) and ($config['acl']['application'] == '*' or $config['acl']['application'] == ''))
                        $applications = '*';
                    elseif (is_array($config['acl']['application']))
                        $applications = $config['acl']['application'];
                    else
                        $applications = explode(',', $config['acl']['application']);
                    $doExec = false;
                    $returnApp = $returnUser = true;
                    if ($users == '*' or in_array($this->getInput('user')->getId(), $users))
                        $returnUser = true;
                    else {
                        $this->logError(81, "execution of $actionName for " . $config['class'] . " '" . $config['path'] . "' is restricted by an 'ACL' rule. User '" . $this->getInput('user')->getId() . "' is not allowed to access this resource", $config);
                        throw new ApiException("execution of $actionName for " . $config['class'] . " '" . $config['path'] . "' is restricted by an 'ACL' rule. User '" . $this->getInput('user')->getId() . "' is not allowed to access this resource", 81);
                    }
                    if ($applications == '*' or in_array($this->getInput('application')->getId(), $applications))
                        $returnApp = true;
                    else {
                        $this->logError(82, "execution of $actionName for " . $config['class'] . " '" . $config['path'] . "' is restricted by an 'ACL' rule. Application '" . $this->getInput('application')->getId() . "' is not allowed to access this resource", $config);
                        throw new ApiException("execution of $actionName for " . $config['class'] . " '" . $config['path'] . "' is restricted by an 'ACL' rule. Application '" . $this->getInput('application')->getId() . "' is not allowed to access this resource", 82);
                    }
                    if ($returnApp and $returnUser)
                        $doExec = true;
                    if ($doExec) {
                        $this->logInfo(80, "Succesfully pass ACL strategy. User '" . $this->getInput('user')->getId() . "' with application '" . $this->getInput('application')->getId() . "' can perform  $actionName on " . $config['class'] . " '" . $config['path'] . "'.", $config['acl'], 3);
                        $resource->$actionName();
                    }
                } else {
                    $this->logInfo(80, "No ACL rules for $actionName on " . $config['class'] . " '" . $config['path'] . "'.", $config['acl'], 3);
                    $resource->$actionName();
                }
            }
            Event::trigger('api.execute.after');
            Event::trigger('api.end');
            session_write_close();
            exit;
        } catch (Exception $exc) {
            $this->getOutput()->renderError($exc->getCode(), "Error when executing api process because " . $exc->getMessage());
        }
        return $this;
    }

    /**
     * Scan the API resource tree according to the requested path and search for the requested resource.
     * This method also merge every resource config node matching this path for return a config array with all ascendant param merged with into the requested one
     * @return array containing the resource config to execute
     * @throws ApiException If tree node is malformed (missing 'resource' key) or if path and tree are not given.
     */
    private function getResourceConfig($elements, $configtree, $outputConfig = array()) {
        if (!is_array($elements) or !is_array($configtree))
            throw new ApiException("getResourceConfig could not work if both \$elements and \$configtree are not array", 85);
        $searchedPath = trim(array_shift($elements));
        // on traite le cas de la racine
        if ($searchedPath == '/' and $configtree['path'] == $searchedPath) {
            $outputConfig = $configtree;
            unset($outputConfig['children']);
            if ($configtree['resource'] == '') {
                $this->logError(86, " path '" . $searchedPath . "' config should contain the 'resource' attribute", $outputConfig);
                throw new ApiException(" path '" . $searchedPath . "' config should contain the 'resource' attribute");
            }
            $configResource = $this->nosqlConnection->selectCollection($this->getConfig("resource_collection", "resources"))->findOne(array("_id" => $configtree['resource']));
            if (is_null($configResource) or $configResource["_id"] == '')
                throw new ApiException("Can't find the resource config in stored resources", 87);
            $this->logDebug(87, "Resource '" . $configtree['resource'] . "' found in resource backend", $configResource, 5);
            if ($configResource['class'] == '') {
                $this->logError(87, " resource '" . $configtree['resource'] . "' config should contain the 'class' attribute", $configResource);
                throw new ApiException(" resource '" . $configtree['resource'] . "' config should contain the 'class' attribute", 87);
            }
            $outputConfig = Toolkit::array_merge_recursive_distinct($configResource, $outputConfig);
            if (count($elements) > 0)
                return $this->getResourceConfig($elements, $configtree['children'], $outputConfig);
            else
                return $outputConfig;
        } else {
            if ($searchedPath != '') {
                $selectedChild = null;
                $wildcardChild = null;
                foreach ($configtree as $child)
                    if ($child['path'] == $searchedPath)
                        $selectedChild = $child;
                    elseif ($child['path'] == '*')
                        $wildcardChild = $child;
                if ($selectedChild == null and ($wildcardChild != null or $outputConfig['children'] == "*")) {
                    $elements = array();
                    unset($outputConfig['children']);
                    unset($wildcardChild['path']);
                    if ($wildcardChild != null)
                        $outputConfig = Toolkit::array_merge_recursive_distinct($outputConfig, $wildcardChild);
                    $this->logWarn(85, " path '" . $searchedPath . "' could not be found in api tree but wildcard node is found. Use previous resource '" . $outputConfig['class'] . "' instead", $outputConfig);
                    return $outputConfig;
                }
                if ($selectedChild === null) {
                    throw new ApiException(" path '" . $searchedPath . "'  could not be found in api tree.");
                } else {
                    $addedConfig = $selectedChild;
                    unset($addedConfig['children']);
                    if ($addedConfig['resource'] == '') {
                        $this->logError(86, " path '" . $searchedPath . "' config should contain the 'resource' attribute", $addedConfig);
                        throw new ApiException(" path '" . $searchedPath . "' config should contain the 'resource' attribute");
                    }
                    $configResource = $this->nosqlConnection->selectCollection($this->getConfig("resource_collection", "resources"))->findOne(array("_id" => $addedConfig['resource']));
                    if (is_null($configResource) or $configResource["_id"] == '')
                        throw new ApiException("Can't find the resource config in stored resources", 87);
                    $this->logDebug(87, "Resource '" . $addedConfig['resource'] . "' found in resource backend", $configResource, 5);
                    if ($configResource['class'] == '') {
                        $this->logError(87, " resource '" . $addedConfig['resource'] . "' config should contain the 'class' attribute", $configResource);
                        throw new ApiException(" resource '" . $addedConfig['resource'] . "' config should contain the 'class' attribute", 87);
                    }
                    $addedConfig = Toolkit::array_merge_recursive_distinct($configResource, $addedConfig);
                    if (array_key_exists('children', $addedConfig) and !array_key_exists('children', $selectedChild))
                        $selectedChild['children'] = $addedConfig['children'];
                    if (!array_key_exists('children', $selectedChild))
                        $selectedChild['children'] = array();
                    $outputConfig = Toolkit::array_merge_recursive_distinct($outputConfig, $addedConfig);
                    if (count($elements) > 0) {
                        if ($selectedChild['children'] == "*")
                            return $outputConfig;
                        else
                            return $this->getResourceConfig($elements, $selectedChild['children'], $outputConfig);
                    }
                    else
                        return $outputConfig;
                }
            }
            else {
                if (count($elements) == 0)
                    return $this->getResourceConfig($elements, $configtree, $outputConfig);
                else
                    return $outputConfig;
            }
        }
    }

    /**
     * log informationnal message into log backend
     * Could be used as a static or instance method
     * @param int $code the code coresponding to this log entry
     * @param string $message a message describing the information
     * @param array $data additionnals data recorded to understand the context
     * @param int $level level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant
     * @return \Api instance Api for chaining
     */
    public static function logInfo($code, $message = null, $data = null, $level = 2) {
        if (isset($this) && get_class($this) == __CLASS__)
            $api = $this;
        else
            $api = Api::getInstance();
        if (LOG_VERBOSITY >= $level)
            $api->log('info', $code, $message, $data, $level);
        return $api;
    }

    /**
     * log warning message into log backend
     * Could be used as a static or instance method
     * @param int $code the code coresponding to this log entry
     * @param string $message a message describing the information
     * @param array $data additionnals data recorded to understand the context
     * @param int $level level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant
     * @return \Api instance Api for chaining
     */
    public static function logWarn($code, $message = null, $data = null, $level = 2) {
        if (isset($this) && get_class($this) == __CLASS__)
            $api = $this;
        else
            $api = Api::getInstance();
        if (LOG_VERBOSITY >= $level)
            $api->log('warn', $code, $message, $data, $level);
        return $api;
    }

    /**
     * log error message into log backend
     * Could be used as a static or instance method
     * @param int $code the code coresponding to this log entry
     * @param string $message a message describing the information
     * @param array $data additionnals data recorded to understand the context
     * @param int $level level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant
     * @return \Api instance Api for chaining
     */
    public static function logError($code, $message = null, $data = null, $level = 1) {
        if (isset($this) && get_class($this) == __CLASS__)
            $api = $this;
        else
            $api = Api::getInstance();
        if (LOG_VERBOSITY >= $level)
            $api->log('error', $code, $message, $data, $level);
        return $api;
    }

    /**
     * log debug message into log backend
     * Could be used as a static or instance method
     * @param int $code the code coresponding to this log entry
     * @param string $message a message describing the information
     * @param array $data additionnals data recorded to understand the context
     * @param int $level level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant
     * @return \Api instance Api for chaining
     */
    public static function logDebug($code, $message = null, $data = null, $level = 4) {
        if (isset($this) && get_class($this) == __CLASS__)
            $api = $this;
        else
            $api = Api::getInstance();
        if (DEBUG && LOG_VERBOSITY >= $level)
            $api->log('debug', $code, $message, $data, $level);
        return $api;
    }

    /**
     * Execute recording of every log message into log backend
     * @param string $type the type of log entry (generaly should be 'error', 'warn', 'info' or 'debug')
     * @param int $code the code coresponding to this log entry
     * @param string $message a message describing the information
     * @param array $data additionnals data recorded to understand the context
     * @param int $level level from 1 (important) to 5 (annecdotic) coresponding to the importance of this event. Is used to filter what to record according to the LOG_VERBOSITY constant
     * @return \Api instance Api for chaining
     */
    private function log($type = 'error', $code = 0, $message = null, $data = array(), $level = 1) {
        $obj = new stdClass();
        $obj->date = new MongoDate();
        $obj->type = $type;
        $obj->level = $level;
        $obj->code = $code;
        $obj->message = $message;
        $obj->ip = $_SERVER['REMOTE_ADDR'];
        if (is_array($this->inputs) and array_key_exists('session', $this->inputs) and !is_null($this->inputs['session']->getId()))
            $obj->session = $this->inputs['session']->getId();
        $obj->data = $data;
        if ($data instanceof Exception)
            $obj->data = $data->getTrace();
        else
            $obj->data = $data;
        try {
            $this->nosqlConnection->selectCollection($this->getConfig("logs_collection", "logs"))->insert($obj, array("w" => 0));
        } catch (Exception $e) {
//            var_dump($e);
            $e = null;
        }
        return $this;
    }

    public function getTrace() {
        $trace = array(
            'request_method' => $this->getInput()->getMethod(),
            'request_rooturl' => $this->getInput()->getRootUrl(),
            'request_path' => $this->getInput()->getPath(),
            'request_params' => $this->getInput()->getParams()
        );
        return $trace;
    }

    /**
     * Render an error and exit
     * This method will create a new output connector and use it to render this fatal error. If no connector is available will then echo the message. if output connector implement rendererror, it will use it, if not, use the render method.
     * @param int $code the code coresponding to this error
     * @param string $message a message describing the error
     * @param array $data additionnals data to send for understanding the error
     * @return void end of program. exit
     */
    public function exitOnError($code, $message, $data = array()) {
        $this->renderer = null;
        try {
            if (method_exists($this->getInput(), 'getOutputFormat'))
                $outputName = $this->getInput()->getOutputFormat();
            else
                $outputName = 'html';
        } catch (Exception $e) {
            $outputName = 'html';
        }
        try {
            $outputClassName = ucfirst($outputName) . 'Output';
            $this->renderer = new $outputClassName(array());
            if (method_exists($this->renderer, "renderError") !== false)
                $this->renderer->renderError(($code + 1000), $message, $data);
            elseif (method_exists($this->renderer, "render") !== false)
                $this->renderer->render($message);
            else {
                echo $message;
                if (DEBUG)
                    var_dump($data);
            }
        } catch (Exception $e) {
            echo $message;
            if (DEBUG)
                var_dump($data);
        }
        session_write_close();
        exit;
    }

}

?>