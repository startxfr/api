<?php

/**
 * Class for reading GET params
 *
 * @author dev@startx.fr
 */
class SessionInput extends DefaultInput implements IInput {

    private $sessionStorage = null;
    private $sessionId = null;

    public function __construct($config) {
        parent::__construct($config);
        $api = Api::getInstance();
        $this->sessionStorage = $api->nosqlConnection->selectCollection($this->getConfig("collection",'session'));
        if (is_null($this->sessionStorage))
            throw new InputException("could not get the session collection '" . $this->getConfig("collection",'session') . "' into nosql backend '" . $api->nosqlApiBackend->base . "' datadase.", 202);
        session_set_save_handler(
                array($this, 'openHandler'), array($this, 'closeHandler'), array($this, 'readHandler'), array($this, 'writeHandler'), array($this, 'destroyHandler'), array($this, 'gcHandler')
        );
    }

    public function init() {
        parent::init();
        session_name($this->getConfig('session_name'));
        $paramPassedToken = Api::getInstance()->getInput()->getParam($this->getConfig("session_name"));
        if ($paramPassedToken != '')
            session_id($paramPassedToken);
        session_start();
        $this->sessionId = session_id();
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
        if (is_array($_SESSION) and array_key_exists($key, $_SESSION))
            return $_SESSION[$key];
        else
            return $default;
    }

    public function set($key, $data) {
        $_SESSION[$key] = $data;
        return $this;
    }

    public function getAll() {
        return $_SESSION;
    }

    public function setAll($data) {
        if (is_array($data) and !array_key_exists('application', $data))
            $data['application'] = $_SESSION['application'];
        if (is_array($data) and !array_key_exists('user', $data))
            $data['user'] = $_SESSION['user'];
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
        $default = array(
            "_id" => $this->sessionId,
            'data' => $data,
            'state' => '0',
            'api' => Api::getInstance()->defaultApiID,
            'time_start' => new MongoDate(time()),
            'time_update' => new MongoDate(time()),
            'time_end' => new MongoDate(time() + $this->getConfig('timeout', 240 * 60)),
            'trace_server' => $_SERVER,
            'trace_cookie' => $_COOKIE,
            'trace_first_request' => $context,
            'trace_last_request' => $context
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
            $inDb = $this->createSession(array(), $id);
        else {
            if (time() > $inDb['time_end']->sec) {
                Api::logWarn(211, "Session '" . $id . "' is expired and supposed to be ended until '" . date('Y-m-d H:i:s', $inDb['time_end']->sec));
                if ($this->getConfig('auto_extend_timeout', true)) {
                    $this->sessionStorage->update(array("_id" => $id), array('$set' => array('time_end' => new MongoDate(time() + $this->getConfig('timeout', 240 * 60)))));
                    Api::logInfo(212, "Automaticaly extend session '" . $id . "' exiration date from '" . date('Y-m-d H:i:s', $inDb['time_end']->sec) . "' to '" . date('Y-m-d H:i:s', time() + $this->getConfig('timeout', 240 * 60)) . "' according to the auto_extend_timeout config property", null, 4);
                }
                else
                    throw new InputException("session is expired until " . date('Y-m-d H:i:s', $inDb['time_end']->sec));
            }
            else
                Api::logInfo(210, "Re-open session '" . $id . "' expiring on " . date('Y-m-d H:i:s', $inDb['time_end']->sec), 3);
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
            $inDb = $this->createSession($return_data, $id);
        else
            Api::logInfo(210, "Record session '" . $id . "' object in backend", null, 4);

        $data = array(
            'state' => '1',
            'data' => $return_data,
            'time_update' => new MongoDate(time()),
            'trace_last_request' => Api::getInstance()->getInput()->getContext()
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
                'trace_last_request' => Api::getInstance()->getInput()->getContext()
            )
        ));
        return true;
    }

    function gcHandler($maxlifetime) {
        $maxlifetime = null;
        $sessions = $this->sessionStorage->find(array('state' => '1'));
        foreach ($sessions as $data)
            if ($data['time'] < time())
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