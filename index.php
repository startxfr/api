<?php

require_once('api-lib' . DIRECTORY_SEPARATOR .'kernel' . DIRECTORY_SEPARATOR . 'loader.php');

$api = Api::getInstance('prod-startx');
$api->load()->execute();
?>
