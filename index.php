<?php
require_once(dirname(__FILE__) . '/api-lib' . DIRECTORY_SEPARATOR . 'kernel' . DIRECTORY_SEPARATOR . 'loader.php');

Api::$nosqlApiBackend = '{
        "connection" : "mongodb://test:test@127.0.0.1:27017",
        "base" : "sxapi_test",
        "api_collection" : "sxapi.api"
    }';
$api = Api::getInstance('startx');
$api->load()->execute();
?>
