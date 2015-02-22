<?php

/**
 * This resource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      commandeModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class commandeSxaStartxResource extends defaultSxaStartxResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"commandeSxaStartxResource",
        "desc":"get list of deals available for the SXA project",
        "properties": [
        
        ]
      }';

}
