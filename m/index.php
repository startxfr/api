<?php

require_once('../api-lib' . DIRECTORY_SEPARATOR .'kernel' . DIRECTORY_SEPARATOR . 'loader.php');

$api = Api::getInstance('manager');
$api->load()->execute();
?>