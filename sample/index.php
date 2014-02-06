<?php

require_once('../api-lib' . DIRECTORY_SEPARATOR .'kernel' . DIRECTORY_SEPARATOR . 'loader.php');
Api::$nosqlApiBackend = '{
        "connection" : "mongodb://__user__:__password__@127.0.0.1:27017",
        "base" : "__dbname__",
        "api_collection" : "__collection__"
    }';
$api = Api::getInstance('sample');
$api->load()->execute();
?>