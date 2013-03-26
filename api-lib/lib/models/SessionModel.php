<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class SessionModel extends DefaultModel implements IModel {

    protected $table = 'api_session';
    protected $idkey = 'session_id';
    protected $keys = array('session_id','user_id','state','time_start','time_update','time_end','data','app_id','trace_server','trace_cookie','trace_first_request','trace_last_request');

    public function create($data) {
        return $this->storage->create($this->getTable(), $this->bindVars($data));
    }
}







?>