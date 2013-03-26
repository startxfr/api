<?php

require_once('../api-lib' . DIRECTORY_SEPARATOR .'kernel' . DIRECTORY_SEPARATOR . 'loader.php');

$api = Api::getInstance('sxapi-0.5.2');
$api->load()->execute();
?>