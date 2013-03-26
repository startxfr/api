<?php

/**
 * Class for reading GET params
 *
 * @author dev@startx.fr
 */
class PostInput extends DefaultInput implements IInput {

    public function get($key, $default = null) {
        if (array_key_exists($key, $_POST))
            return $_POST[$key];
        else
            return $default;
    }

    public function set($key, $data) {
        $_POST[$key] = $data;
        return $this;
    }

    public function getAll() {
        return $_POST;
    }

    public function setAll($data) {
        $_POST = $data;
        return $this;
    }
}

?>