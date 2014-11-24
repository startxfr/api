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
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            $calendars = $this->getConfig('session_calendar_id');
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
                $start = $input->getParam('start', null);
                $end = $input->getParam('end', null);  
                $timeMin = ($start !== null) ? date(DateTime::ATOM, $start) : null ;
                $timeMax = ($end !== null) ? date(DateTime::ATOM, $end) : null ;
                $optParam = array("timeMin" => $timeMin, "timeMax" => $timeMax);
                foreach ($calendars as $cal) {
                    #$calEvents[] = $this->calendar->events->listEvents($cal)['items'];                    
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
        $params =$api->getInput()->getParams();
        try {
            $event = new Google_Event();
            $start = new Google_EventDateTime();
            $end = new Google_EventDateTime();
            $start->setDateTime(($params['event']['start']) ? date(DateTime::ATOM, $params['event']['start']) : null);
            $end->setDateTime(($params['event']['end']) ? date(DateTime::ATOM, $params['event']['end']) : null);
            $event->start = $start;
            $event->end  = $end;
            $event->summary = ($params['event']['title']) ? $params['event']['title'] : "";
            $calId = ($params['calId']) ? $params['calId'] : null;
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
            $input = $api->getInput();
            $calendars = $this->getConfig('session_calendar_id');
                                                
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
                    $res = $this->calendar->events->delete($calId, $calEvent);
                $message = sprintf($this->getConfig('message_service_read', 'Calendar testing get event-%s'), $nextPath);
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
   
}

?>
