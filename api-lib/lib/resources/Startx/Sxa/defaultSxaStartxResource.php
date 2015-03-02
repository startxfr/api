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
    private $sxamain = null;

    public function init() {
        parent::init();
        $api = Api::getInstance();
        $sxamainID = $this->getConfig('sxa_main');
        if(!$this->isConfig('sxa_main')) {
            Api::getInstance()->logError(906, get_class($this) . " resource config should contain the 'sxa_main' attribute", $this->getResourceTrace(__FUNCTION__, false));
            throw new ResourceException(get_class($this) . " resource config should contain the 'sxa_main' attribute");
        }
        $this->sxamain = ($api->isLoadedResource($sxamainID)) ? $api->getResource($sxamainID) : $api->getConfiguredResource($sxamainID);
        return $this;
    }

    public function sxa() {
        return $this->sxamain;
    }

    public function addActualite($actu) {
        $this->sxa()->getModule('actualite')->insert(
                Toolkit::array_merge_recursive_distinct(array(
                    'date' => date('Y-m-d H:i:s'),
                    'user' => 'anonymous',
                    'type' => 'general',
                    'titre' => 'actu',
                    'desc' => '',
                    'isPublic' => '0',
                    'isPublieForClient' => '0',
                    'isVisibleFilActu' => '1'), $actu));
        return $this;
    }

}
