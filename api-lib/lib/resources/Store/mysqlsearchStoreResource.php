<?php

/**
 * This resource is used to interact (read - write) with mysql data, recorded in a store.
 * Data are returned to the client using the output method.
 *
 * @package  SXAPI.Resource.Model
 * @author   Dev Team <dev@startx.fr>
 * @see      defaultModelResource
 * @link     https://github.com/startxfr/sxapi/wiki/Resource
 */
class mysqlsearchStoreResource extends mysqlStoreResource implements IResource {

    static public $ConfDesc = '{"class_name":"mysqlsearchStoreResource",
  "desc":"Resource to access mysql storage",
  "properties":
	[	
	]
}';
          
    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $search = $api->getInput()->getParam($this->getConfig('searchParam', '_search'), "");
            if ($search !== "") {           
                $store = $this->getStorage();
                $utf = "SET NAMES utf8 ; ";                  
                $store->execQuery($utf);                                                                                                                                               
                $countResult = $store->readCount($this->getConfig('dataset'));
                $start = (int) $api->getInput()->getParam($this->getConfig('startParam', '_start'), 0);
                $limit = (int) $api->getInput()->getParam($this->getConfig('limitParam', '_limit'), $countResult);                
                $customQuery = $this->composeSearchQuery( $search, $start, $limit );
                $data = $store->execQuery($customQuery);      
                var_dump($data);
                exit(0);
                $return = $this->filterResults($data);                                
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($return), $countResult, session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);                                
                return array(true, $message, $return, $countResult);                                                                                                                       
            }
            else
                return parent::readAction();
        } catch (Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(),array(),500);
        }
        return true;
    }

    private function composeSearchQuery( $search , $start, $limit ) {
        $lim = " LIMIT $start, $limit ";
        $join = "SELECT * "                                
                . "FROM formation_session AS f "
                . "LEFT JOIN formation_cours AS cours ON cours.name_cours = f.cours_id_session "
                . "LEFT JOIN formation_centre AS centre ON centre.id_centre = f.location_id_session "
                . "LEFT JOIN formateur ON formateur.id_formateur = f.trainer_id_session "             
                . "LEFT JOIN formation_cursus AS cursus ON cursus.name_cursus = cours.cursus_id_cours "       
                . "LEFT JOIN formation_partenaire AS partenaire ON partenaire.name_part = cours.partner_id_cours "         
                . "LEFT JOIN contact AS cf ON cf.id_cont = formateur.id_cont_formateur "
                . "LEFT JOIN contact AS cc ON cc.id_cont = centre.id_cont_centre "
                . "LEFT JOIN entreprise ON entreprise.id_ent = cc.entreprise_cont "
                ;         
        $where = "WHERE ";
        var_dump($search);
        $s1 = preg_replace(array('/ AND | ET /i', '/ OR | OU /i'), array('&&', '||'), $search);
        $s2 = preg_replace_callback("/[A-Za-z0-9 _-]*:[A-Za-z0-9 _-]*/", "self::convertTest", $s1);
        if ( $s2 === $search )
            $s2 = " f.* LIKE %$search% ";
        var_dump($s2);
        exit(0);
        $query = $join . $where . $s2 . $lim;
        return $query;
    }          
    
    private function convertTest( $str ) {
        var_dump($str);
        list($tag, $arg) = array_map('trim', explode(":", $str[0]));
        switch ( (string) $tag ) 
        {
            case "time":
            case "date":
                $s = $this->getTimeWhere($arg);
                break;
            case "training":
            case "cours":
                $s =  " ( cours.name_cours LIKE %$arg% OR cours.title_cours LIKE %$arg% ) ";                
                break;
            case "courses" :
            case "cursus":
                $s =  " ( cursus.name_cursus LIKE %$arg% OR cursus.title_cursus LIKE %$arg% ) ";
                break;
            case "partner" :
            case "partenaire":
                $s =  " ( partenaire.name_part LIKE %$arg% OR partenaire.title_part LIKE %$arg% ) ";
                break;
            case "trainer" :
            case "formateur":
                $s =  " ( formateur.initial_formateur LIKE %$arg% OR cf.nom_cont LIKE %$arg% OR cf.prenom_cont LIKE %$arg% ) ";
                break;
            case "location" :
            case "lieux":
                $s =  " ( cc.add1_cont LIKE %$arg% OR cc.add2_cont LIKE %$arg% OR cc.cp_cont LIKE %$arg% OR cc.ville_cont LIKE %$arg% ) ";
                break;
            default:
                $s =  " ";
                break;
        }
        return $s;
    }
    
    private function getTimeWhere($arg) {
        if (preg_match("/\d{4}/", $arg)) {
            
        }
        else if (preg_match("/\d{2}\/\d{2}/", $arg)) {
            
        }
        else if (preg_match("/(\d{2}\/\d{2}\/\d{2}( )*-)||(\d{2}\/\d{2}( )*-)||(\d{4}( )*-)/", $arg)) {
            
        }
        else if (preg_match("/(-*( )\d{2}\/\d{2}\/\d{2})||(-*( )\d{2}\/\d{2})||(-*( )\d{4})/", $arg)) {
            
        }
    }
    
}

?>
