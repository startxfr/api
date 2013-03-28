<?php

/**
 * @package  SXAPI.Input
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
 */
class ServerInput extends DefaultInput implements IInput {

    public function get($key, $default = null) {
        if (array_key_exists($key, $_SERVER))
            return $_SERVER[$key];
        else
            return $default;
    }

    public function set($key, $data) {
        $_SERVER[$key] = $data;
        return $this;
    }

    public function getAll() {
        return $_SERVER;
    }

    public function setAll($data) {
        $_SERVER = $data;
        return $this;
    }
}

?>