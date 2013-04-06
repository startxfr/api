<?php

/**
 * This class allow you to interact with a MongoDB database.
 *
 * @package  SXAPI.Store
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultStore
 * @link     https://github.com/startxfr/sxapi/wiki/Store
 */
class nosqlStore extends defaultStore implements IStorage {

    public function connect() {
        if (!$this->isconnected) {
            $server = (string) $this->getConfig('server', '127.0.0.1') . ":" . (string) $this->getConfig('port', '27017');
            $database = (string) $this->getConfig('base', 'base');
            $password = ((string) $this->getConfig('passwd', '') != '') ? ':' . (string) $this->getConfig('passwd') : '';
            $username = ((string) $this->getConfig('username', '') != '') ? (string) $this->getConfig('username') . $password . '@' : '';
            try {
        Event::trigger('store.connect.before');
                parent::connect();
                $connection = new Mongo("mongodb://" . $username . $server);
                $this->connection = $connection->selectDB($database);
                $this->isconnected = true;
        Event::trigger('store.connect.after');
            } catch (Exception $e) {
                throw new StoreException("could not connect to nosql storage because " . $e->getMessage());
            }
        }
        return $this;
    }

    public function readOne($table, $criteria = array()) {
        try {
            $this->connect();
            $this->lastResult = $this->connection->selectCollection($table)->findOne($criteria, $this->getConfig('find_filter', array()));
            Api::getInstance()->logDebug(421, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return 1 result", array('table' => $table, 'criteria' => $criteria), 4);
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not read one entry from nosql storage because " . $e->getMessage());
        }
    }

    public function read($table, $criteria = array(), $order = array(), $start = 0, $stop = 30) {
        try {
            $this->connect();
            $this->lastResult = $this->connection->selectCollection($table)->find($criteria, $this->getConfig('find_filter', array()))->sort($order)->skip($start)->limit($stop);
            Api::getInstance()->logDebug(420, "'" . __FUNCTION__ . "' '" . get_class($this) . "' '" . $this->lastResult->count() . "' results", array('table' => $table, 'criteria' => $criteria, 'order' => $order, 'start' => $start, 'stop' => $stop), 4);
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not read entries from nosql storage because " . $e->getMessage());
        }
    }

    public function readCount($table, $criteria = array()) {
        try {
            $this->connect();
            $this->lastResult = $this->connection->selectCollection($table)->find($criteria)->count();
            Api::getInstance()->logDebug(422, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return " . $this->lastResult . "' counter", array('table' => $table, 'criteria' => $criteria), 4);
            return (int) $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not count read entries from nosql storage because " . $e->getMessage());
        }
    }

    public function create($table, $data) {
        try {
            $this->connect();
            $this->lastResult = $this->connection->selectCollection($table)->insert($data);
            Api::getInstance()->logDebug(430, "'" . __FUNCTION__ . "' '" . get_class($this) . "' created id '" . $data->_id . "' entry", array('table' => $table, 'data' => $data), 4);
            return (string) $data->_id;
        } catch (Exception $e) {
            throw new StoreException("could not create entries from nosql storage because " . $e->getMessage());
        }
    }

    public function update($table, $key, $id, $data, $upsert = false) {
        try {
            $this->connect();
            if($upsert)
                $this->lastResult = $this->connection->selectCollection($table)->update(array($key => $id), array('$set' => $data), array('upsert' => true));
            else
                $this->lastResult = $this->connection->selectCollection($table)->update(array($key => $id), array('$set' => $data));
            Api::getInstance()->logDebug(450, "'" . __FUNCTION__ . "' '" . get_class($this) . "' updated id '" . $id . "' entry", array('table' => $table, 'key' => $key, 'id' => $id, 'data' => $data), 4);
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not update entry from nosql storage because " . $e->getMessage());
        }
    }

    public function delete($table, $key, $id) {
        try {
            $this->connect();
            $this->lastResult = $this->connection->selectCollection($table)->remove(array($key => $id), array('justOne' => true));
            Api::getInstance()->logDebug(470, "'" . __FUNCTION__ . "' '" . get_class($this) . "' deleted id '" . $id . "' entry", array('table' => $table, 'key' => $key, 'id' => $id), 4);
            return $this->lastResult;
        } catch (Exception $e) {
            throw new StoreException("could not delete entry from nosql storage because " . $e->getMessage());
        }
    }

}

?>