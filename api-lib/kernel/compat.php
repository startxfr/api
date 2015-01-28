<?php

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}



/**
 * Compatibility toolkit with older php version
 *
 * @author   Dev Team <dev@startx.fr>
 * @copyright Copyright (c) 2003-2013 startx.fr
 * @license https://github.com/startxfr/sxapi/blob/master/licence.txt
 */
if (!function_exists('http_response_code')) {

    function http_response_code($newcode = NULL) {
        static $code = 200;
        if ($newcode !== NULL) {
            header('X-PHP-Response-Code: ' . $newcode, true, $newcode);
            if (!headers_sent())
                $code = $newcode;
        }
        return $code;
    }

}