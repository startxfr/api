<?php

/**
 * @brief Class used to access data stored into the cookies. Wrap the $_COOKIE global variable.
 *
 * @class    CookieInput
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
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