<?php

/**
 * Class for reading GET params
 *
 * @author dev@startx.fr
 */
class CookieInput extends DefaultInput implements IInput {

    public function get($key, $default = null) {
        if (array_key_exists($key, $_COOKIE))
            return $_COOKIE[$key];
        else
            return $default;
    }

    public function set($key, $data) {
        $_COOKIE[$key] = $data;
        return $this;
    }

    public function getAll() {
        return $_COOKIE;
    }

    public function setAll($data) {
        $_COOKIE = $data;
        return $this;
    }
}

?>