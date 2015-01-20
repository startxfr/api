<?php

/**
 * This class allow you to interact with a mysql database.
 *
 * @package  SXAPI.Store
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultStore
 * @link     https://github.com/startxfr/sxapi/wiki/Store
 */
class mysqliStore extends defaultStore implements IStorage {

    public function connect() {
        if (!$this->isconnected) {
            try {
                Event::trigger('store.connect.before');
                parent::connect();
                $this->connection = @new mysqli($this->getConfig('server', '127.0.0.1'), $this->getConfig('username', ''), $this->getConfig('passwd', ''), $this->getConfig('base', 'base'));
                if ($this->connection->connect_errno) {
                    throw new StoreException("connection error " . $this->connection->connect_error);
                }
                $this->isconnected = true;
                Event::trigger('store.connect.after');
            } catch (Exception $e) {
                throw new StoreException("we could not connect to mysql storage because " . $e->getMessage());
            }
        }
        return $this;
    }

    public function readOne($table, $criteria = array()) {
        try {
            $this->connect();
            $result = $this->read($table, $criteria);
            Api::getInstance()->logDebug(421, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return 1 result", array('table' => $table, 'criteria' => $criteria), 4);
            if (is_array($result) and count($result) > 0 and is_array($result[0])) {
                return $result[0];
            } else
                return array();
        } catch (Exception $e) {
            throw new StoreException($e->getMessage());
        }
    }

    public function read($table, $criteria = array(), $order = array(), $start = 0, $stop = 30) {
        try {
            $this->connect();
            $sql = "SELECT * FROM `" . $table . "` ";
            $sql.= $this->_sqlWhere($criteria);
            $sql.= $this->_sqlOrder($order);
            $sql.= $this->_sqlLimit($start, $stop);
            $this->_setQuery($sql);
            $this->lastResult = $this->connection->query($this->_getQuery());
            if ($this->lastResult !== false) {
                $output = array();
                if ($this->lastResult !== true) {
                    while ($row = $this->lastResult->fetch_array(MYSQLI_ASSOC)) {
                        $output[] = $row;
                    }
                }
                Api::getInstance()->logDebug(420, "'" . __FUNCTION__ . "' '" . get_class($this) . "' '" . @count($output) . "' result", array('table' => $table, 'criteria' => $criteria, 'order' => $order, 'start' => $start, 'stop' => $stop), 4);
                return $output;
            } else {
                $error = $this->connection->connect_error;
                throw new StoreException($error);
            }
        } catch (Exception $e) {
            throw new StoreException("could not read entries from mysql storage because " . $e->getMessage());
        }
        return $this;
    }

    public function readCount($table, $criteria = array()) {
        try {
            $this->connect();
            $sql = "SELECT COUNT(*) AS counter FROM `" . $table . "` ";
            $sql.= $this->_sqlWhere($criteria);
            $this->_setQuery($sql);
            $this->lastResult = $this->connection->query($this->_getQuery());
            if ($this->lastResult !== false) {
                while ($row = $this->lastResult->fetch_assoc()) {
                    $output = $row;
                }
                $output = $this->lastResult->fetch();
                Api::getInstance()->logDebug(422, "'" . __FUNCTION__ . "' '" . get_class($this) . "' return " . $output["counter"] . "' counter", array('table' => $table, 'criteria' => $criteria), 4);
                return $output["counter"];
            } else {
                $error = $this->connection->connect_error;
                throw new StoreException($error);
            }
        } catch (Exception $e) {
            throw new StoreException("could not count read entries from mysql storage because " . $e->getMessage());
        }
        return $this;
    }

    public function create($table, $data) {
        try {
            $this->connect();
            $action = 'INSERT';
            $top = $action . " INTO `" . $table . "` ( ";
            foreach ($data as $k => $val) {
                if ($val == '')
                    $bottom .= ", NULL ";
                else
                    $bottom .= ", " . $this->_sqlQuote($val) . " ";
                $head .= ", `" . $k . "` ";
            }
            $head = substr($head, 1);
            $bottom = substr($bottom, 1);
            $sql = $top . $head . ") VALUES (" . $bottom . ") ";
            $this->_setQuery($sql);
            $this->lastResult = $this->connection->query($this->_getQuery());
            if ($this->lastResult !== false) {
                Api::getInstance()->logDebug(430, "'" . __FUNCTION__ . "' '" . get_class($this) . "' created id '" . $this->connection->lastInsertId() . "' entry", array('table' => $table, 'data' => $data), 4);
                return $this->lastResult;
            } else {
                $error = $this->connection->connect_error;
                throw new StoreException($error);
            }
        } catch (Exception $e) {
            throw new StoreException("could not create entries from mysql storage because " . $e->getMessage());
        }
        return $this;
    }

    public function update($table, $key, $id, $data) {
        try {
            if (empty($data))
                throw new StoreException("no data");
            $this->connect();
            $action = 'UPDATE';
            $top = "$action `" . $table . "` SET ";
            foreach ($data as $k => $val) {
                if ($val == '')
                    $head .= " `" . $k . "` = NULL, ";
                else
                    $head .= " `" . $k . "` = " . $this->_sqlQuote($val) . ", ";
            }
            $head = substr($head, 0, -2);
            $sql = $top . $head . " WHERE `" . $key . "` = '" . $id . "' ";
            $this->_setQuery($sql);
            $this->lastResult = $this->connection->query($this->_getQuery());
            if ($this->lastResult !== false) {
                Api::getInstance()->logDebug(450, "'" . __FUNCTION__ . "' '" . get_class($this) . "' updated id '" . $id . "' entry", array('table' => $table, 'key' => $key, 'id' => $id, 'data' => $data), 4);
                return $this->lastResult;
            } else {
                $error = $this->connection->connect_error;
                throw new StoreException($error);
            }
        } catch (Exception $e) {
            throw new StoreException("could not update entry from mysql storage because " . $e->getMessage());
        }
        return $this;
    }

    public function delete($table, $key, $id) {
        try {
            $this->connect();
            $sql = "DELETE FROM `" . $table . "` WHERE `" . $key . "` = " . $this->_sqlQuote($id) . " ";
            $this->_setQuery($sql);
            $this->lastResult = $this->connection->query($this->_getQuery());
            if ($this->lastResult !== false) {
                Api::getInstance()->logDebug(470, "'" . __FUNCTION__ . "' '" . get_class($this) . "' deleted id '" . $id . "' entry", array('table' => $table, 'key' => $key, 'id' => $id), 4);
                return $this->lastResult;
            } else {
                $error = $this->connection->connect_error;
                throw new StoreException($error);
            }
        } catch (Exception $e) {
            throw new StoreException("could not delete entry from mysql storage because " . $e->getMessage());
        }
        return $this;
    }

    public function execQuery($sql) {
        try {
            $this->connect();
            $this->_setQuery($sql);
            $this->lastResult = $this->connection->query($this->_getQuery());
            if ($this->lastResult !== false) {
                $output = array();
                if ($this->lastResult !== true) {
                    while ($row = $this->lastResult->fetch_array(MYSQLI_ASSOC)) {
                        $output[] = $row;
                    }
                }
                Api::getInstance()->logDebug(420, "'" . __FUNCTION__ . "' '" . get_class($this) . "' '" . @count($this->lastResult) . "' result", array('sql' => $sql), 4);
                return $output;
            } else {
                $error = $this->connection->connect_error;
                throw new StoreException($error);
            }
        } catch (Exception $e) {
            throw new StoreException("could not read entries from mysql storage because " . $e->getMessage());
        }
        return $this;
    }

    private function _setQuery($sqlQuery) {
        $this->lastQuery = $sqlQuery;
        return $this;
    }

    private function _getQuery() {
        return $this->lastQuery;
    }

    private function _sqlLimit($start = 0, $limit = 30) {
        if ($start == '')
            $start = 0;
        if ($limit == '')
            $limit = 30;
        return "LIMIT $start, $limit ";
    }

    private function _sqlOrder($criteria = array()) {
        if (is_object($criteria))
            $criteria = Tools::object2Array($criteria);
        if (is_null($criteria))
            $criteria = array();
        if (!is_array($criteria))
            $criteria = array((string) $criteria);
        $listOrder = array();
        foreach ($criteria as $key => $rule) {
            if (is_array($rule)) {
                $dir = (strtoupper($rule['direction']) == 'DESC') ? 'DESC' : 'ASC';
                if (strtoupper($rule['property']) != '')
                    $listOrder[$rule['property']] = $dir;
            }
            elseif (is_string($rule)) {
                if (strtoupper($rule) == 'ASC' or strtoupper($rule) == 'DESC')
                    $listOrder[$key] = strtoupper($rule);
                else
                    $listOrder[$rule] = 'ASC';
            }
        }
        $sql = '';
        foreach ($listOrder as $field => $sens)
            $sql.= $field . ' ' . $sens . ', ';
        if ($sql != '')
            $sql = ' ORDER BY ' . substr($sql, 0, -2);
        return $sql . ' ';
    }

    private function _sqlWhere($criteria) {
        $sql = ' ';
        if (is_object($criteria))
            $criteria = Tools::object2Array($criteria);
        if (is_null($criteria))
            $criteria = array();
        if (!is_array($criteria))
            $criteria = array($criteria);
        foreach ($criteria as $field => $crit) {
            if (is_array($crit)) {
                $operator = ($crit['operator'] != '') ? $crit['operator'] : 'LIKE';
                $value = ($crit['value'] != '') ? $crit['value'] : '%';
                $field = ($crit['property'] != '') ? $crit['property'] : $field;
                $sql .= ' AND  ' . $field . ' ' . $operator . ' ' . $this->_sqlQuote($value);
            } else
                $sql .= ' AND  ' . $field . ' = ' . $this->_sqlQuote($crit);
        }
        if (strlen($sql) > 2)
            $sql = ' WHERE ' . substr($sql, 5);
        return $sql . ' ';
    }

    private function _escapeString($word) {
        return str_replace("'", "''", $word);
    }

    private function _sqlQuote($data, $useNull = true, $escape = false) {
        return ($useNull and $data == '') ? 'NULL' : (($escape) ? "'" . $this->_escapeString($data) . "'" : "'" . $data . "'");
    }

}

?>