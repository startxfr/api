<?php

/**
 * This resource is to be used to sent message
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class createusermailFormationStartxResource extends linkableResource implements IResource {         

    static public $ConfDesc = '{
        "class_name":"createusermailFormationStartxResource",
        "desc" : "processing resource use to format mail",
        "properties":[]
    }';
    
    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3); 
        $prevOutput = $this->getPrevOutput();
        if ($this->getPrevBool() === false)
            return array(false, 930, $prevOutput);
        $body = "A new user was created in SXA_db with id:". $prevOutput[0][0] ."\n";        
        
        if (count($prevOutput[1][0]) !== 0) {  
            $body .= "There are duplicates of the same email address in SXA_db:\n";
            foreach ($prevOutput[1][0] as $cont) {
                $body .= " - " . $cont['id_cont'] . "\n";
            }
        }
        return array(true, 'processing complete', array($body));
    }
   
}

?>
