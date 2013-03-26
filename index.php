<?php

require_once('api-lib' . DIRECTORY_SEPARATOR .'kernel' . DIRECTORY_SEPARATOR . 'loader.php');

$api = Api::getInstance('sxapi-sample');
$api->load()->execute();
?>