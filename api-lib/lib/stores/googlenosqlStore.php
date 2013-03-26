<?php

/**
 * Class for logging detail into a file
 *
 * @author dev@startx.fr
 */
class googlenosqlStore extends NosqlStore implements IStorage {


    public function __construct($config) {
        parent::__construct($config);
    }
}

?>