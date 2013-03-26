<?php

/**
 * Class for recording session using filesystem
 *
 * @author dev@startx.fr
 */
class RightModel extends DefaultModel implements IModel {

    protected $table = 'api_right';
    protected $idkey = "_id";
    protected $keys = array("_id", 'name', 'desc', 'order');

}







?>