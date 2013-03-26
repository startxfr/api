<?php

/**
 * Class for reading GET params
 *
 * @author dev@startx.fr
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