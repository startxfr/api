<?php

/**
 * This resource is used to return an input message
 *
 * @package  SXAPI.Resource.Test
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class googlecalendarTestResource extends linkableResource implements IResource {

    protected $client = null;
    protected $calendar = null;
    protected $services = array();

    public function __construct($config) {
        parent::__construct($config);        
        require_once LIBPATHEXT . 'google-api-php-client' . DS . 'src' . DS . 'Google_Client.php';        
        $this->client = new Google_Client();  
    }

    public function init() {
        parent::init();
        $api = Api::getInstance();
        $this->client->setApplicationName($this->getConfig('application_name')); 
        $this->client->setClientId($this->getConfig('client_id'));       
        $this->client->setClientSecret($this->getConfig('client_secret'));
        $this->client->setRedirectUri("http://localhost/startx/api/calendar");           
        $store = $api->getStore($this->getConfig('store'));
        $data = $store->readOne($this->getConfig('store_dataset'), array($this->getConfig('store_id_key') => $this->getConfig('user_id')));
        $refreshToken = $data['refresh_token'];
        $this->calendar = $this->loadServices();
        $this->client->refreshToken($refreshToken);
        return $this;
    }

    public function readAction() {
        $api = Api::getInstance();
        $data = $this->filterParams($api->getInput()->getParams(), "input");
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            $calendars = ($data['calendar']) ? array($data['calendar']) : $this->getConfig('session_calendar_id');
            if ($nextPath !== null) {                
                foreach ($calendars as $cal) {
                    try {
                        $calEvent = $this->calendar->events->get($cal, $nextPath);
                    } catch (Exception $exc) {
                        $exc->getMessage();
                    }
                }
                $message = sprintf($this->getConfig('message_service_read', 'Calendar testing get event-%s'), $nextPath);
                return array(true, $message, $calEvent);
            } 
            else {
                $calEvents = array(); 
                $timeMin = ($data['start']) ? date(DateTime::ATOM, $data['start']) : null ;
                $timeMax = ($data['end']) ? date(DateTime::ATOM, $data['end']) : null ;
                $optParam = array("timeMin" => $timeMin, "timeMax" => $timeMax);
                foreach ($calendars as $cal) {                    
                    $calEvents = array_merge($calEvents, $this->calendar->events->listEvents($cal, $optParam)['items']);
                }
                $message = $this->getConfig('message_service_read', 'Calendar testing get all events');
                return array(true, "Calendar testing get all events", $calEvents); 
            }                                    
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
        return true;
    }
    
    public function createAction() {
        $api = Api::getInstance();
        $api->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);              
        try {
            $event = new Google_Event();            
            $data = $this->filterParams($api->getInput()->getParams(), "input");
            $this->getGoogleDate($data, $event);
            $event->summary = ($data['event']['summary']) ? $data['event']['summary'] : "";
            $event->location = ($data['event']['location']) ? $data['event']['location'] : "";
            $event->description = ($data['event']['description']) ? $data['event']['description'] : "";
            $calId = ($data['calId']) ? $data['calId'] : null;
            $newEvent = $this->calendar->events->insert($calId, $event);
            return array(true, "Event created with id:".$newEvent['id'], $newEvent); 
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
        return true;    
    }

    public function updateAction() {
        $api = Api::getInstance();
        $api->logDebug(950, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);      
        try {
            $data = $this->filterParams($api->getInput()->getParams(), "input");
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            $calendars = $this->getConfig('session_calendar_id');
            if ($nextPath !== null) {                
                foreach ($calendars as $cal) {
                    try {
                        $calEvent = $this->calendar->events->get($cal, $nextPath);
                        $calId = $cal;
                    } catch (Exception $exc) {
                        $exc->getMessage();
                    }
                }
                if ($calEvent !== null) {                               
                    $event = new Google_Event();                     
                    if (!$this->getGoogleDate($data, $event)) {
                        $event->start = $calEvent['start'];
                        $event->end  = $calEvent['end'];                     
                    }
                    $event->setSequence($calEvent['sequence']);
                    $event->summary = ($data['event']['summary']) ? $data['event']['summary'] : $calEvent['summary'];
                    $event->location = ($data['event']['location']) ? $data['event']['location'] : $calEvent['location'];
                    $event->description = ($data['event']['description']) ? $data['event']['description'] : $calEvent['description'];                                        
                    $res = $this->calendar->events->update($calId, $nextPath, $event);                                        
                    $message = sprintf($this->getConfig('message_service_delete', 'Calendar testing get event-%s'), $nextPath);
                    return array(true, $message, $res);
                }
                else
                    throw new ResourceException("No Event with id:$nextPath found.");                
            } 
            else {
                throw new ResourceException("Event_id of the event to update is required");
            }                                     
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
        return true;
    }

    public function deleteAction() {
        $api = Api::getInstance();
        $api->logDebug(970, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);      
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            $calendars = $this->getConfig('session_calendar_id');
            if ($nextPath !== null) {               
                foreach ($calendars as $cal) {
                    try {
                        $calEvent = $this->calendar->events->get($cal, $nextPath);
                        $calId = $cal;
                    } catch (Exception $exc) {
                        $exc->getMessage();
                    }
                }
                if ($calEvent !== null)
                    $res = $this->calendar->events->delete($calId, $nextPath);
                else
                    throw new ResourceException("No Event with id:$nextPath found.");
                $message = sprintf($this->getConfig('message_service_delete', 'Calendar testing get event-%s'), $nextPath);
                return array(true, $message, $res);
            } 
            else {
                throw new ResourceException("Event_id of the event to delete is required");
            }                                    
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
        return true;
    }
    
    public function loadServices() {
        $api = Api::getInstance();
        $serviceName = $this->getConfig('google_service', "Google_CalendarService");
        try {
            $serviceClass = 'Google_' . ucfirst($serviceName) . 'Service';            
            require_once LIBPATHEXT . 'google-api-php-client' . DS . 'src' . DS . 'contrib' . DS . $serviceClass . '.php';            
            $this->services[$serviceName] = new $serviceClass($this->client);           
        } catch (Exception $exc) {
            $api->logWarn(910, "Warning on '" . __FUNCTION__ . "' for '" . get_class($this) . "' : " . $exc->getMessage(), $exc);
        }
        return $this->services[$serviceName];
    }
   
    private function getGoogleDate( $data, $event ) {
        if (!$data['event']['start'] || !$data['event']['end'])
            return false;
        $start = new Google_EventDateTime();
        $end = new Google_EventDateTime();
        $tpStart = $data['event']['start'];
        $tpEnd = $data['event']['end'];
        $allDay = ($data['event']['allday']) ? $data['event']['allday'] : false;
        if ($allDay) {
            $start->setDate(date("Y-m-d", $tpStart));
            $end->setDate(date("Y-m-d", $tpEnd + 86400));
        }
        else {
            $start->setDateTime(date(DateTime::ATOM, $tpStart));
            $end->setDateTime(date(DateTime::ATOM, $tpEnd));
        }
        $event->start = $start;
        $event->end  = $end; 
        return true;
    }
    
}

?>
