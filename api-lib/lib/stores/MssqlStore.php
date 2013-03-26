<?php

/**
 * Class for logging detail into a file
 *
 * @author dev@startx.fr
 */
class MssqlStore extends MysqlStore implements IStorage {

    public function connect() {
        if (!$this->isconnected) {
            try {
                $this->connection = new PDO("odbc:Driver={" . $this->getConfig('driver','SQL Server Native Client 10.0') . "};Server=" . $this->getConfig('server','127.0.0.1') . ";Database=" . $this->getConfig('base','base') . ";Uid=" . $this->getConfig('username','') . ";Pwd=" . $this->getConfig('passwd',''));
            } catch (Exception $e) {
                throw new StoreException("we could not connect to mssql storage because " . $e->getMessage());
            }
        }
        return $this;
    }

}

?>