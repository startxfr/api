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
                $table = $this->getConfig('store_dataset_sessions', "formation.sessions.test");
                $key = $this->getConfig('store_id_key', "_id");
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
                $SXAstore = $api->getStore($this->getConfig('external_store', "mysql")); 
                $SXAstore->connect();
                $table = $this->getConfig('store_dataset_sessions', "formation.sessions.test");               
                $calEvents = $this->transformArray($ret[2]);                
                $nosqlEvent = $store->read($table, array(), array(), 0, $store->readCount($table));
                foreach ($nosqlEvent as $session) {
                    if (($event = $calEvents[$session['calId']])) {
                        $connect = $SXAstore->getNativeConnection();
                        $sql = "SELECT * "
                                . "FROM formateur "
                                . "LEFT JOIN contact ON formateur.id_cont=contact.id_cont "
                                //. "LEFT JOIN entreprise ON contact.id_ent=entreprise.id_ent "
                                . "WHERE formateur.id_formateur=".$session['trainer']
                                . "AND "
                                . "FROM centre_formation "
                                . "LEFT JOIN contact ON centre_formation.id_cont=contact.id_cont "
                                . "LEFT JOIN entreprise ON contact.id_ent=entreprise.id_ent "
                                . "WHERE centre_formation.id_centre=".$session['location']
                                ;
                        $sql2 = "SELECT * "                                
                                . "FROM centre_formation "
                                . "LEFT JOIN contact ON centre_formation.id_cont=contact.id_cont "
                                . "LEFT JOIN entreprise ON contact.id_ent=entreprise.id_ent "
                                . "WHERE centre_formation.id_centre=".$session['location']
                                ;
                        $result = $connect->query($sql2, PDO::FETCH_ASSOC);
                        if ($result)
                            $result = $result->fetchAll();
                        var_dump($result);
                        exit(0);
                        $summary = "";
                        $desc = "";
                        $locus = "";
                        $event['summary'] = $summary;
                        $event['description']  = $desc;
                        $event['location'] = $locus;                                                
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
                    $new_ses[] = $store->create($this->getConfig('store_dataset_sessions', "formation.sessions.test"), $new_session);
                }
            }
            else
                throw new ResourceException("No event found for given calendars");   
            return array(true, 'ok', $new_ses);
        } catch (Exception $ex) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 401);
        }
    }
    
}

?>
