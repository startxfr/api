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
        return $this->createAction();
        $api = Api::getInstance();        
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $ret = parent::readAction();
            if ($ret[0]) {
                $nb_lost = 0;
                $store = $api->getStore($this->getConfig('store', "nosql"));
                $table = $this->getConfig('dataset', "formation.sessions.test");
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
                $nb_lost = 0;
                $store = $api->getStore($this->getConfig('store', "nosql"));
                $sqlStore = $api->getStore($this->getConfig('external_store', "mysql"));                 
                $table = $this->getConfig('dataset', "formation.sessions.test");               
                $calEvents = $this->transformArray($ret[2]);                
                $nosqlEvent = $store->read($table, array(), array(), 0, $store->readCount($table));
                $i = 0;
                foreach ($nosqlEvent as $session) {
                    if (($event = $calEvents[$session['calId']])) {                                                
                        $res = $this->populateSession($sqlStore, $session);                        
                        $desc = "";
                        foreach ($res['students'] as $student) {
                            $desc .= $student['prenom_cont']." ".$student['nom_cont']." ".$student['nom_ent']."\r\n";
                        }
                        $summary = $session['formation']." : ".$res['trainer']['prenom_cont']." ".$res['trainer']['nom_cont'];                        
                        $locus = $res['location']['nom_ent']." ".$res['location']['add1_ent']." ".$res['location']['cp_ent']." ".$res['location']['ville_ent'];                                                
                        //var_dump($event);                        
                        $new_event = new Google_Event();  
                        $data = [];                        
                        $data['event']['start'] = "1415873600";
                        $data['event']['end']   = "1416960000";
                        $this->getGoogleDate($data, $new_event);
                        $new_event->id = $session['calId'];
                        #$new_event->etag = $event['etag'];
                        //$new_event->start = $event['start'];
                        //$new_event->end  = $event['end'];                                         
                        #$new_event->setSequence($event['sequence']);
                        $new_event->summary = $summary;
                        $new_event->location = $desc;
                        $new_event->description = $locus;                      
//                         var_dump($event);                       
//                        var_dump($new_event);
//                        exit(0);
                        if ($i === 1)
                            exit(0);
                        $i++;
                        $return = $this->calendar->events->insert('jr@startx.fr', $new_event);
                        #$res = $this->calendar->events->update('jr@startx.fr', $session['calId'], $new_event);                   
                    }
                    else
                        $nb_lost++;
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
    
    private function importCal() {
        $api = Api::getInstance();        
        try {
            $ret = parent::readAction();
            if ($ret[0]) {
                $store = $api->getStore($this->getConfig('store', "nosql"));                           
                $new_ses = array();
                foreach ($ret[2] as $event) {             
                    if (preg_match('/[A-Z0-9]{2,7} : [A-Z]{2,5}/', $event['summary'])) 
                        list($tr_code, $trainer) = explode(':', $event['summary']);
                    else
                        continue ;
                    $start = $event['start'];
                    $end = $event['end'];                                         
                    $new_session = array('calId' => $event['id'], 'start' => $start, 'end' => $end, 'formation' => trim($tr_code), 'trainer'=> trim($trainer));                    
                    $new_ses[] = $store->create($this->getConfig('dataset', "formation.sessions.test"), $new_session);
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
