<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class UserModel extends DefaultModel implements IModel {

    protected $table = 'api_user';
    protected $idkey = 'login';
    protected $keys = array('login', 'pwd', 'lastname', 'firstname', 'civ', 'mail', 'right', 'isActive', 'data');

    public function create($data) {
        return $this->storage->create($this->getTable(), $this->bindVars($data),false);
    }
}

?>