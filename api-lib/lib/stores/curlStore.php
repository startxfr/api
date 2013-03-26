<?php

/**
 * Class for logging detail into a file
 *
 * @author dev@startx.fr
 */
class curlStore extends DefaultStore implements IStorage {

    protected $connection = null;
    public $lastResult = null;
    protected $isconnected = false;

    public function __construct($config) {
        parent::__construct($config);
    }

    function __destruct() {
        $this->disconnect();
    }

    public function init() {
        if ($this->getConfig('url', '') == '')
            throw new StoreException("store config should contain the 'url' attribute");
        if (!is_null($this->getConfig('auth')) and $this->getConfig('auth') != false) {
            if ($this->getConfig('auth_user', '') == '')
                throw new StoreException("store config should contain the 'auth_user' attribute because 'auth' attribute is activated");
            if ($this->getConfig('auth_pwd', '') == '')
                throw new StoreException("store config should contain the 'auth_pwd' attribute because 'auth' attribute is activated");
        }
        return $this;
    }

    public function connect() {
        if (!$this->isconnected) {
            try {
                parent::connect();
                $this->connexion = curl_init();
                curl_setopt($this->connexion, CURLOPT_RETURNTRANSFER, true);
                if (!is_null($this->getConfig('auth')) and $this->getConfig('auth') != false) {
                    curl_setopt($this->connexion, CURLOPT_USERPWD, $this->getConfig('auth_user') . ':' . $this->getConfig('auth_pwd'));
                    switch ($this->getConfig('auth')) {
                        case "any":
                            curl_setopt($this->connexion, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                            break;
                        default:
                            curl_setopt($this->connexion, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                            break;
                    }
                }
                $this->isconnected = true;
            } catch (Exception $e) {
                throw new StoreException("could not connect to nosql storage because " . $e->getMessage());
            }
        }
        return $this;
    }

    public function reconnect() {
        $this->disconnect()->connect();
        return $this;
    }

    public function disconnect() {
        parent::disconnect();
        @curl_close($this->connection);
        $this->connection = null;
        $this->isconnected = false;
        return $this;
    }

    public function getNativeConnection() {
        return $this->connection;
    }

    public function readOne($table, $criteria = array()) {
        try {
            $this->connect();
            $url = $this->getConfig('url');
            $url .= (substr($url, -1) != '/') ? '/' . $table : $table;
            curl_setopt($this->connexion, CURLOPT_URL, $url);
            $this->lastResult = curl_exec($this->connexion);
            if ($this->lastResult === false)
                throw new StoreException("could not read entries from curl storage because " . htmlentities(curl_error($this->connexion)));
            Api::getInstance()->logInfo(421, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return 1 result", array('url' => $url, 'table' => $table, 'criteria' => $criteria, 'config' => $this->getConfigs()));
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not read one entry from curl storage because " . $e->getMessage());
        }
    }

    public function read($table, $criteria = array(), $order = array(), $start = 0, $stop = 30) {
        try {
            $this->connect();
            $url = $this->getConfig('url');
            $url .= (substr($url, -1) != '/') ? '/' . $table : $table;
            curl_setopt($this->connexion, CURLOPT_URL, $url);
            $this->lastResult = curl_exec($this->connexion);
            if ($this->lastResult === false)
                throw new StoreException("could not read entries from curl storage because " . htmlentities(curl_error($this->connexion)));
            Api::getInstance()->logInfo(420, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return " . @count($this->lastResult) . " results", array('url' => $url, 'table' => $table, 'criteria' => $criteria, 'order' => $order, 'start' => $start, 'stop' => $stop, 'config' => $this->getConfigs()));
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not read entries from curl storage because " . $e->getMessage());
        }
    }

    public function readCount($table, $criteria = array()) {
        try {
            $this->connect();
            $url = $this->getConfig('url');
            $url .= (substr($url, -1) != '/') ? '/' . $table : $table;
            curl_setopt($this->connexion, CURLOPT_URL, $url);
            $this->lastResult = curl_exec($this->connexion);
            if ($this->lastResult === false)
                throw new StoreException("could not read entries from curl storage because " . htmlentities(curl_error($this->connexion)));
            Api::getInstance()->logInfo(422, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return " . strlen($this->lastResult) . " results", array('url' => $url, 'table' => $table, 'criteria' => $criteria, 'config' => $this->getConfigs()));
            return strlen($this->lastResult);
        } catch (Exception $e) {
            throw new StoreException("could not count read entries from curl storage because " . $e->getMessage());
        }
    }

    public function create($table, $data) {
        try {
            $this->connect();
            $url = $this->getConfig('url');
            $url .= (substr($url, -1) != '/') ? '/' . $table : $table;
            curl_setopt($this->connexion, CURLOPT_URL, $url);
            curl_setopt($this->connexion, CURLOPT_POST, true);
            curl_setopt($this->connexion, CURLOPT_POSTFIELDS, $data);
            $this->lastResult = curl_exec($this->connexion);
            if ($this->lastResult === false)
                throw new StoreException("could not read entries from curl storage because " . htmlentities(curl_error($this->connexion)));
            Api::getInstance()->logInfo(430, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return " . $this->lastResult, array('url' => $url, 'table' => $table, 'data' => $data, 'config' => $this->getConfigs()));
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not create entries from curl storage because " . $e->getMessage());
        }
    }

    public function update($table, $key, $id, $data) {
        try {
            $this->connect();
            $url = $this->getConfig('url');
            $url .= (substr($url, -1) != '/') ? '/' . $table : $table;
            $url .= '/' . $key . '/' . $id;
            $data = (is_array($data)) ? http_build_query($data) : $data;
            curl_setopt($this->connexion, CURLOPT_URL, $url);
            curl_setopt($this->connexion, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($data)));
            curl_setopt($this->connexion, CURLOPT_PUT, true);
            curl_setopt($this->connexion, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->connexion, CURLOPT_CUSTOMREQUEST, "PUT");
            $this->lastResult = curl_exec($this->connexion);
            if ($this->lastResult === false)
                throw new StoreException("could not read entries from curl storage because " . htmlentities(curl_error($this->connexion)));
            Api::getInstance()->logInfo(450, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return " . @count($this->lastResult) . " results", array('url' => $url, 'table' => $table, 'data' => $data, 'key' => $key, 'id' => $id, 'config' => $this->getConfigs()));
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not update entry from curl storage because " . $e->getMessage());
        }
    }

    public function delete($table, $key, $id) {
        try {
            $this->connect();
            $url = $this->getConfig('url');
            $url .= (substr($url, -1) != '/') ? '/' . $table : $table;
            $url .= '/' . $key . '/' . $id;
            $data = http_build_query(array('key' => $key, 'value' => $id));
            curl_setopt($this->connexion, CURLOPT_URL, $url);
            curl_setopt($this->connexion, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($data)));
            curl_setopt($this->connexion, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($this->connexion, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->connexion, CURLOPT_CUSTOMREQUEST, "DELETE");
            $this->lastResult = curl_exec($this->connexion);
            if ($this->lastResult === false)
                throw new StoreException("could not read entries from curl storage because " . htmlentities(curl_error($this->connexion)));
            Api::getInstance()->logInfo(470, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return " . $this->lastResult, array('url' => $url, 'table' => $table, 'key' => $key, 'id' => $id, 'config' => $this->getConfigs()));
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not delete entry from curl storage because " . $e->getMessage());
        }
    }

}

?>