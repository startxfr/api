<?php

require_once('api-lib' . DIRECTORY_SEPARATOR .'kernel' . DIRECTORY_SEPARATOR . 'loader.php');

Api::$nosqlApiBackend = '{
        "connection" : "mongodb://startx:314159@127.0.0.1:27017",
        "base" : "sxapi",
        "api_collection" : "sxapi.api"
    }';
/*
print_r($_REQUEST);
exit;
*/
 
$api = Api::getInstance('startx');
$api->load()->execute();
?>