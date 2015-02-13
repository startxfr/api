<?php

/**
 * Class used to access data stored into the current session. If no session found, create one and use the API config to define the storage method.
 *
 * @class    SessionInput
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
 */
class SessionInput extends DefaultInput implements IInput {

    private $sessionStorage = null;
    private $sessionId = null;

    /**
     * construct the session input object
     *
     * @param array configuration of this object
     * @see Configurable
     * @return void
     */
    public function __construct($config) {
        parent::__construct($config);
        $api = Api::getInstance();
        $this->sessionStorage = $api->nosqlConnection->selectCollection($this->getConfig("collection",'session'));
        if (is_null($this->sessionStorage))
            throw new InputException("could not get the session collection '" . $this->getConfig("collection",'session') . "' into nosql backend '" . Api::$nosqlApiBackend->base . "' datadase.", 202);
        session_set_save_handler(
                array($this, 'openHandler'), array($this, 'closeHandler'), array($this, 'readHandler'), array($this, 'writeHandler'), array($this, 'destroyHandler'), array($this, 'gcHandler')
        );
    }

    /**
     * initialize this instance and make it available and usable
     * @return self
     */
    public function init() {
        Event::trigger('input.init.before');
        $inputParams = $this->getConfig('token',array());
        $sessionName = ($inputParams['name'] != '') ? $inputParams['name'] : 'session';
        if($inputParams['input'] == "xauth") {
            $paramPassedToken = $_SERVER['X-Auth-Token'];
        }
        else {
            $paramPassedToken = Api::getInstance()->getInput($inputParams['input'])->getParam($sessionName,'');
        }
        session_name($sessionName);
        if ($paramPassedToken != '') {
            session_id($paramPassedToken);
        }
        session_start();
        $this->sessionId = session_id();
        Api::logDebug(210, "Init '" . $this->getConfig("_id") . "' " . get_class($this) . " connector for session '".$sessionName."' with  id " . $this->sessionId, $this->getAll(), 5);
        Event::trigger('input.init.after');
        return $this;
    }

    public function clear($key) {
        unset($_SESSION[$key]);
        return $this;
    }

    public function getId() {
        return $this->sessionId;
    }

    public function get($key, $default = null) {
        if (is_array($_SESSION) and array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    public function set($key, $data) {
        $_SESSION[$key] = $data;
        return $this;
    }

    public function getAll() {
        return $_SESSION;
    }

    public function setAll($data) {
        $applicationParamName = $this->getConfig("application_key",'application');
        $userParamName = $this->getConfig("user_key",'user');
        if (is_array($data) and !array_key_exists($applicationParamName, $data))
            $data[$applicationParamName] = $_SESSION[$applicationParamName];
        if (is_array($data) and !array_key_exists($userParamName, $data))
            $data[$userParamName] = $_SESSION[$userParamName];
        $_SESSION = $data;
        return $this;
    }

    /**
     * get a fresh new session description filled with default values merged with the given data
     * @param array $data list of data to merge with default values
     * @return Array the data representing a session
     */
    private function createSession($data = array(), $id = null) {
        if (!is_array($data))
            $data = array($data);
        $descriptor = $this->generateSessionDescriptor($data, $id);
        Api::logInfo(220, "Start new session '" . $id . "' for " . $this->getConfig('timeout', 240 * 60) . 's', $descriptor, 3);
        $this->sessionStorage->insert($descriptor);
        return $this->findOneSession($id);
    }

    public function findOneSession($id = null) {
        if (is_null($id))
            $this->sessionId = session_id();
        else
            $this->sessionId = $id;
        return $this->sessionStorage->findOne(array("_id" => $this->sessionId));
    }

    private function generateSessionDescriptor($data = array(), $id = null) {
        $context = Api::getInstance()->getInput()->getContext();
        if (is_null($id))
            $this->sessionId = session_id();
        else
            $this->sessionId = $id;
        
        $request = array(
            'first' => $context,
            'last' => $context
        );
        $trace = array(
            'server' => $_SERVER,
            'cookie' => $_COOKIE,
            'request' => $request
        );
        $default = array(
            "_id" => $this->sessionId,
            'data' => $data,
            'state' => '0',
            'api' => Api::getInstance()->defaultApiID,
            'time_start' => new MongoDate(time()),
            'time_update' => new MongoDate(time()),
            'time_end' => new MongoDate(time() + $this->getConfig('timeout', 240 * 60)),
            'trace' => $trace
        );
        Api::logDebug(220, "Generate new session '" . $this->sessionId . "' descriptor", $default, 5);
        return $default;
    }

    function openHandler($savePath, $sessionName) {
        $savePath = $sessionName = null;
        return true;
    }

    function closeHandler() {
        return true;
    }

    function readHandler($id) {
        $inDb = $this->findOneSession($id);
        // first time this session is done, so we create a new record
        if (is_null($inDb) or $inDb["_id"] == '')
//              throw new InputException("session " . $id. " is not recorded. Please use another session token or renew your authentification");
             $inDb = $this->createSession(array(), $id);
        else {
            if (time() > $inDb['time_end']->sec) {
                Api::logWarn(211, "Session '" . $id . "' is expired and is closed until '" . date('Y-m-d H:i:s', $inDb['time_end']->sec));
                if ($this->getConfig('auto_extend_timeout', true)) {
                    $this->sessionStorage->update(array("_id" => $id), array('$set' => array('time_end' => new MongoDate(time() + $this->getConfig('timeout', 240 * 60)))));
                    Api::logInfo(212, "Automaticaly extend session '" . $id . "' expiration date from '" . date('Y-m-d H:i:s', $inDb['time_end']->sec) . "' to '" . date('Y-m-d H:i:s', time() + $this->getConfig('timeout', 240 * 60)) . "' according to the auto_extend_timeout config property", null, 4);
                }
                else
                    throw new InputException("session is expired until " . date('Y-m-d H:i:s', $inDb['time_end']->sec));
            }
            else
                Api::logInfo(210, "Continue session '" . $id . "' until " . date('Y-m-d H:i:s', $inDb['time_end']->sec), 3);
        }

        $_SESSION = $inDb['data'];
        return true;
    }

    function writeHandler($id, $session_data) {
        // decode la session (replace la fonction session_decode() qui marche pas ici)
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new InputException("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        $inDb = $this->findOneSession($id);
        if (is_null($inDb) or $inDb["_id"] == '')
              throw new InputException("session " . $id. " could not be recorded as it has to be recorded.");
//          $inDb = $this->createSession($return_data, $id);
        else
            Api::logInfo(210, "Record session '" . $id . "' object in backend", null, 4);

        $data = array(
            'state' => '1',
            'data' => $return_data,
            'time_update' => new MongoDate(time()),
            'trace.request.last' => Api::getInstance()->getInput()->getContext()
        );
        $this->sessionStorage->update(array("_id" => $id), array('$set' => $data));
        return true;
    }

    function destroyHandler($id) {
        $inDb = $this->findOneSession($id);
        if ($inDb['session_id'] == '')
            $this->createSession(array(), $id);
        $this->sessionStorage->update(array("_id" => $id), array(
            '$set' => array(
                'state' => '2',
                'time_update' => new MongoDate(time()),
                'time_end' => new MongoDate(time()),
                'trace.request.last' => Api::getInstance()->getInput()->getContext()
            )
        ));
        return true;
    }

    function gcHandler($maxlifetime) {
        $maxlifetime = null;
        $sessions = $this->sessionStorage->find(array('state' => '1'));
        foreach ($sessions as $data)
            if (array_key_exists ('time', $data) and $data['time'] < time())
                $this->sessionStorage->update(array("_id" => $id), array(
                    '$set' => array(
                        'state' => '2',
                        'time_update' => new MongoDate(time()),
                        'time_end' => new MongoDate(time())
                    )
                ));
        return true;
    }

}

?>