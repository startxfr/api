<?php

/**
 * Class used to access data comming from the HTTP POST method. Wrap the $_POST global variable.
 *
 * @class    PostInput
 * @author   Dev Team <dev@startx.fr>
 * @see      DefaultInput
 * @link     https://github.com/startxfr/sxapi/wiki/Inputs
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