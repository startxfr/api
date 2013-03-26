<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class SessionInternalModel extends DefaultModel implements IModel {

    protected $table = 'session';
    protected $idkey = "_id";
    protected $keys = array("_id", 'user', 'state', 'dstart', 'dupdate', 'dend', 'data', 'app', 'trace_server', 'trace_cookie', 'trace_frequest', 'trace_lrequest');

}







?>