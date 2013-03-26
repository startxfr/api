<?php

/**
 * Class for reading GET params
 *
 * @author dev@startx.fr
 */
class RequestInput extends DefaultInput implements IInput {

    public function get($key, $default = null) {
        if (array_key_exists($key, $_REQUEST))
            return $_REQUEST[$key];
        else
            return $default;
    }

    public function set($key, $data) {
        $_REQUEST[$key] = $data;
        return $this;
    }

    public function getAll() {
        return $_REQUEST;
    }

    public function setAll($data) {
        $_REQUEST = $data;
        return $this;
    }
}

?>