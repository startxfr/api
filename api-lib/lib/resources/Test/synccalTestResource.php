<?php

/**
 * This resource is used to return an input message
 *
 * @package  SXAPI.Resource.Test
 * @author   Dev Team <dev@startx.fr>
 * @see      readonlyResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class synccalTestResource extends googlecalendarTestResource implements IResource {

    public function readAction() {
        $api = Api::getInstance();
        $data = $this->filterParams($api->getInput()->getParams(), "input");
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $input = $api->getInput();
            $sessElPosition = $input->getElementPosition($this->getConfig('path'));
            $nextPath = $input->getElement($sessElPosition + 1);
            $calendars = ($data['calendar']) ? array($data['calendar']) : $this->getConfig('session_calendar_id');
            if ($nextPath === 'import')
                return $this->importCal();
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
        Api::getInstance()->logDebug(930, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getConfigs(), 3);
        return $this->importCal();
    }
    
    private function importCal() {
        $api = Api::getInstance();        
        try {
            $ret = parent::readAction();
            if ($ret[0]) {
                foreach ($ret[2] as $event) {
                    
                }
            }
            else
                throw new ResourceException("No event found for given calendars");
            return array(true, 'ok', $ret[2]);
        } catch (Exception $ex) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
    }
    
}

?>
