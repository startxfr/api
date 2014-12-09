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
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $ret = parent::readAction();
            if ($ret[0]) {
                $nb_lost = 0;
                $store = $api->getStore($this->getConfig('store_backend', "mysql"));
                $table = $this->getConfig('dataset_backend', "formation_session");
                $key = $this->getConfig('id_key', "_id");
                $calEvents = $this->transformArray($ret[2]);                
                $nosqlEvent = $store->read($table, array(), array(), 0, $store->readCount($table));
                foreach ($nosqlEvent as $session) {
                    if (($event = $calEvents[$session['calId']])) {
                        $session['start'] = $event['start'];
                        $session['end'] = $event['end'];
                        $store->update($table, $key, $session['_id'], $session);
                    }
                    else
                        $nb_lost++;
                }
            }
            else
                throw new ResourceException("No event found for given calendars");   
            return array(true, "Sync Google calendar to Api", $nb_lost);
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
            $ret = parent::readAction();
            if ($ret[0]) { 
                $store = $api->getStore($this->getConfig('store', "nosql"));
                $sqlStore = $api->getStore($this->getConfig('external_store', "mysql"));                 
                $table = $this->getConfig('dataset', "formation.sessions.test");               
                $calEvents = $this->transformArray($ret[2]);                
                $nosqlEvent = $store->read($table, array(), array(), 0, $store->readCount($table));  
                $return = [];
                foreach ($nosqlEvent as $session) {
                    $res = $this->populateSession($sqlStore, $session);                 
                    $desc = "";
                    foreach ($res['students'] as $student) {
                        $desc .= $student['prenom_cont']." ".$student['nom_cont']." ".$student['nom_ent']."\r\n";
                    }
                    $summary = $session['formation']." : ".$res['trainer']['prenom_cont']." ".$res['trainer']['nom_cont'];                        
                    $locus = $res['location']['nom_ent']." ".$res['location']['add1_ent']." ".$res['location']['cp_ent']." ".$res['location']['ville_ent'];
                    if (($event = $calEvents[$session['calId']])) {                                                                                                                        
                        $new_event = new Google_Event();                     
                        #$new_event->id = $session['calId'];                     
                        $this->recupDate($event['start'], $event['end'], $new_event);   
                        $new_event->setSequence($event['sequence']);
                        $new_event->summary = $summary;
                        $new_event->location = $locus;
                        $new_event->description = $desc;                                             
                        
                        $return[] = $this->calendar->events->update('startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com', $session['calId'], $new_event);                   
                    }
                    else {                        
                        $new_event = new Google_Event();  
                        $new_event->id = $session['calId'];
                        $this->recupDate($session['start'], $session['end'], $new_event);                                           
                        $new_event->summary = $summary;
                        $new_event->location = $locus;
                        $new_event->description = $desc;                                       
                                                                                          
                        $return[] = $this->calendar->events->insert('startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com', $new_event);                 
                    }
                }      
                return array(true, "Sync Google calendar to Api", $return);
            }
            else
                throw new ResourceException("No event found for given calendars");               
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
        return true;
    }
    
    private function transformArray( $array ) {
        $newArray = array();
        foreach ( $array as $elem ) {
            $newArray[$elem['id']] = $elem; 
        }
        return $newArray;
    }
    
    private function recupDate( $startArray, $endArray, $event ) {
        $start = new Google_EventDateTime();
        $end = new Google_EventDateTime();
        if (array_key_exists('date', $startArray)) {
            $start->setDate($startArray['date']);
        }
        if (array_key_exists('date', $endArray)) {
            $end->setDate($endArray['date']);
        }        
        if (array_key_exists('dateTime', $startArray)) {               
            $start->setDateTime($startArray['dateTime']);
        }
        if (array_key_exists('dateTime', $endArray)) {
            $end->setDateTime($endArray['dateTime']);
        }
        $event->start = $start;
        $event->end  = $end; 
        return true;
    }
    
    private function importCal() {
        $api = Api::getInstance();        
        try {
            $ret = parent::readAction();
            if ($ret[0]) {
                $store = $api->getStore($this->getConfig('store_backend', "mysql"));                           
                $new_ses = array();
                foreach ($ret[2] as $event) {             
                    if (preg_match('/[A-Z0-9]{2,7} : [A-Z]{2,5}/', $event['summary'])) 
                        list($tr_code, $trainer) = explode(':', $event['summary']);
                    else
                        continue ;
                    #var_dump($event);
                   
                    
                    if (array_key_exists('date', $event['start']) && array_key_exists('date', $event['end']) ) {
                        $start = strtotime($event['start']['date']);
                        $end = strtotime( $event['end']['date']);
                        $allday = 1;
                    }       
                    if (array_key_exists('dateTime', $event['start']) && array_key_exists('dateTime', $event['end'])) {               
                        $start = strtotime($event['start']['dateTime']);
                        $end = strtotime( $event['end']['dateTime']);
                        $allday = 0;
                    }       
//                    var_dump($start);
//                    var_dump($end);
//                   
                    $trainer = trim($trainer);
                    switch ($trainer) {
                        case 'MG':
                            $trainer = 1;
                            break;
                        case 'HQ':
                            $trainer = 2;
                            break;
                        case 'FM':
                            $trainer = 3;
                            break;
                        case 'JR':
                            $trainer = 4;
                            break;
                        default:
                            $trainer = 2;
                            break;
                    }
                    $new_session = array(
                        'cours_id_session' => preg_replace('/ /', '', $tr_code),
                        'trainer_id_session' => $trainer, 
                        'location_id_session'=> 1,
                        'event_id_session' => "startx.fr_4eo69kq9vpt75a813vtn3mfv3s@group.calendar.google.com".'#'.$event['id'],
                        'prix_session' => 2000,
                        'status_session' => 'none',
                        'allday_session' => $allday,
                        'start_session' => $start,
                        'end_session' => $end                        
                        );   
                    #var_dump($new_session);
                   $new_ses[] = $store->create($this->getConfig('dataset_backend', "formation_session"), $new_session);
                }
            }
            else
                throw new ResourceException("No event found for given calendars");              
            return array(true, 'ok', $new_ses);
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
    }
    
}

?>
