<?php

/**
 * Class used to access data comming from the HTTP GET method. Wrap the $_GET global variable.
 *
 * @package  SXAPI.Input
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
 */
class GetInput extends DefaultInput implements IInput {

    public function get($key, $default = null) {
        if (array_key_exists($key, $_GET))
            return $_GET[$key];
        else
            return $default;
    }

    public function set($key, $data) {
        $_GET[$key] = $data;
        return $this;
    }

    public function getAll() {
        return $_GET;
    }

    public function setAll($data) {
        $_GET = $data;
        return $this;
    }
}

?>