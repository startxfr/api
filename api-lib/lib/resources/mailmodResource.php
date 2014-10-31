<?php

/**
 * This resource is to be used to sent message
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class mailmodResource extends linkableResource implements IResource {         

    static public $ConfDesc = '{"class_name":"mailmodResource",
  "desc":"processing resource use to format mail",
  "propreties":[]
}';
    
    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3); 
        $prevOutput = $this->getPrevOutput();
//        var_dump($prevOutput);
//        exit;
        if ($this->getPrevBool() === false)
            return array(false, 930, $prevOutput);
               
        
        if (count($prevOutput) !== 0) {  
            $body = "those are the users\n";
            foreach ($prevOutput as $user) {
                $body .= implode(" - ", $user) . "\n";
            }
        }
        return array(true, 'processing complete', array($body));
    }
   
}

?>
