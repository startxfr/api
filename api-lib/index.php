<?php

require_once('..' . DIRECTORY_SEPARATOR .'api-lib' . DIRECTORY_SEPARATOR .'kernel' . DIRECTORY_SEPARATOR . 'loader.php');

Api::getInstance()
        ->load()
        ->logError(999,"SECURITY: User is trying to explore path ".Api::getInstance()->getInput()->getRootUrl()." witch is a system directory.", Api::getInstance()->getTrace())
        ->exitOnError(999, "this directory is not accessible");
?>