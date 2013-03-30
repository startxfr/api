<?php

/**
 * This ressource class is abstract and should not be used as it.
 * Developpers can create a new http ressource type by derivating from this class
 *
 * @package  SXAPI.Resource.Http
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultRessource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class defaultHttpRessource extends readonlyRessource implements IRessource {

}

?>
