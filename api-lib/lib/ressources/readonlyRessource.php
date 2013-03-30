<?php

/**
 * This ressource class is abstract and should not be used as it.
 * Developpers can create a new readonly ressource type by derivating from this class
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class readonlyRessource extends defaultRessource implements IRessource {

    public function createAction() {
        Api::getInstance()->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getConfigs(), 3);
        return $this->readAction();
    }

    public function updateAction() {
        Api::getInstance()->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getConfigs(), 3);
        return $this->readAction();
    }

    public function deleteAction() {
        Api::getInstance()->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' ressource", $this->getConfigs(), 3);
        return $this->readAction();
    }

}

?>
