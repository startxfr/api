<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class AppUserModel extends DefaultModel implements IModel {

    protected $table = 'api_app_user';
    protected $idkey = 'app_user_id';
    protected $keys = array('app_user_id', 'app_id', 'user_id', 'app_user_token', 'app_user_config');


    public function readDetail($criteria = array(), $order = array(), $from = 0, $max = 30) {
        $sql = "SELECT api_user.*, " . $this->getTable() . ".*, api_app.* FROM `" . $this->getTable() . "`
                LEFT JOIN api_app ON api_app.app_id = api_app_user.app_id
                LEFT JOIN api_user ON api_user.login = api_app_user.user_id";
        $sql.= $this->storage->_sqlWhere($criteria);
        $sql.= $this->storage->_sqlOrder($order);
        $sql.= $this->storage->_sqlLimit($from, $max);
        $this->storage->setQuery($sql);
        try {
            $ctBrut = $this->storage->conn->query($this->storage->getQuery(), PDO::FETCH_ASSOC);
            if ($ctBrut !== false) {
                $output = array();
                foreach ($ctBrut as $row)
                    $output[] = $row;
                return $output;
            } else {
                $error = $this->storage->conn->errorInfo();
                throw new ModelException($error[2]);
            }
        } catch (Exception $e) {
            throw new ModelException("we could not execute SQL SELECT because " . $e->getMessage());
        }
        return array();
    }
}








?>