<?php

/**
 * This resource class is abstract and should not be used as it.
 * Developpers can create a new authentification resource type by derivating from this class
 *
 * @package  SXAPI.Resource
 * @author   Dev Team <dev@startx.fr>
 * @see      Configurable
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
abstract class linkableResource extends defaultResource implements IResource {
    
    static public $ConfDesc = '{"class_name":"linkableResource",
  "desc":"abstract resource which enable communication between resource and make them sequencable",
  "properties":[]
}';
    
    private $prevOutput;
    private $prevBool;
    
    public function __construct($config) {       
        parent::__construct($config);
        $this->prevOutput = array();
        $this->prevBool = false;
    }
    
    public function setPrevOutput($prevOutput = array(false)) {
        $this->prevBool = $prevOutput[0];
        if ($this->prevBool === true) 
            $this->prevOutput = $prevOutput[2];
        else if (isset ($prevOutput[3]))
            $this->prevOutput = $prevOutput[3];
        return $this;
    }
    
    public function getPrevOutput() {
        return $this->prevOutput;
    }
    
    public function getPrevBool() {
        return $this->prevBool;
    }
}

?>
