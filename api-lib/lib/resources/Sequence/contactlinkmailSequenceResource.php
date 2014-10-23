<?php

/**
 * This resource is to be used to sent message
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class contactlinkmailSequenceResource extends linkableResource implements IResource {         

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);       
        $prevOutput = $this->getPrevOutput();           
        if (count($prevOutput) !== 0) {
            $result  = array();
            foreach ($prevOutput as $array) {               
                $result[] = implode (", ", $array);               
            }
            $body = implode ("\n", $result);           
        }
        else
            $body = "";
        return array(true, 'processing complete', array($body));
    }
   
}

?>
