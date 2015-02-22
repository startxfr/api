<?php

/**
 * This resource is used to interact (read - write) with nosql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class defaultSxaStartxResource extends mysqlStoreResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"defaultSxaStartxResource",
        "desc":"get list of resources available for the SXA project",
        "properties": [
        
        ]
      }';

}
