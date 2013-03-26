<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class AppModel extends DefaultModel implements IModel {

    protected $table = 'api_app';
    protected $idkey = 'app_id';
    protected $keys = array('app_id', 'app_config', 'app_name', 'app_desc');

}




?>