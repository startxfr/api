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
class mysqlsearchSqlStoreResource extends mysqlSqlStoreResource implements IResource {

    static public $ConfDesc = '{
        "class_name":"mysqlsearchSqlStoreResource",
        "desc":"Resource to access mysql storage",
        "properties":
	[	
            {
                    "name":"searchParam",
                    "type":"string",
                    "mandatory":"false",
                    "desc":"input param used for search filter"
            }
	]
}';

    public function readAction() {
        $api = Api::getInstance();
        $api->logDebug(910, "Start executing '" . __FUNCTION__ . "' on '" . get_class($this) . "' resource", $this->getResourceTrace(__FUNCTION__, false), 3);
        try {
            $search = $api->getInput()->getParam($this->getConfig('searchParam', '_search'), "");
            if($search !== "") {
                $store = $this->getStorage();
                $utf = "SET NAMES utf8 ; ";
                $store->execQuery($utf);
                $countResult = $store->readCount($this->getConfig('dataset'));
                $start = (int) $api->getInput()->getParam($this->getConfig('startParam', '_start'), 0);
                $limit = (int) $api->getInput()->getParam($this->getConfig('limitParam', '_limit'), $countResult);
                $customQuery = $this->composeSearchQuery($search, $start, $limit);
                $data = $store->execQuery($customQuery);
//                var_dump($data);
//                exit(0);                
                $return = $this->filterResults($data);
                $size = count($return);
                $nRet = array_slice($return, $start, $limit);
                $message = sprintf($this->getConfig('message_service_read', 'message service read'), count($return), $countResult, session_id());
                $api->logInfo(910, "'" . __FUNCTION__ . "' in '" . get_class($this) . "' return : " . $message, $this->getResourceTrace(__FUNCTION__, false), 1);
                return array(true, $message, $nRet, $size);
            }
            else
                return parent::readAction();
        }
        catch(Exception $exc) {
            $api->logError(910, "Error on '" . __FUNCTION__ . "' for '" . get_class($this) . "' return : " . $exc->getMessage(), $exc);
            return array(false, $exc->getCode(), $exc->getMessage(), array(), 500);
        }
        return true;
    }

    private function composeSearchQuery($search) {
        $join = "SELECT f.* "
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
//        var_dump('compose query start');
//        var_dump($search);
        $s1 = preg_replace(array('/ AND | ET /i', '/ OR | OU /i'), array('&&', '||'), $search);
        $s2 = preg_replace_callback("/([^&|:()]*):([^&|:()]*)/", "self::convertTest", $s1);
        if($s2 === $search)
            $s2 = " f.* LIKE '%$search%' ";
//        var_dump($s2);
//        var_dump('compose query end');
//        exit(0);
        $query = $join . $where . $s2 . ';';
        return $query;
    }

    private function convertTest($str) {
//        var_dump('convert test start');
//        var_dump($str);
        #list($tag, $arg) = array_map('trim', explode(":", $str[0]));
        list(, $tag, $arg) = array_map('trim', $str);
        switch((string) $tag) {
            case "time":
            case "date":
                $s = $this->getTimeWhere($arg);
                break;
            case "training":
            case "cours":
                $s = " ( cours.name_cours LIKE '%$arg%' OR cours.title_cours LIKE '%$arg%' ) ";
                break;
            case "courses" :
            case "cursus":
                $s = " ( cursus.name_cursus LIKE '%$arg%' OR cursus.title_cursus LIKE '%$arg%' ) ";
                break;
            case "partner" :
            case "partenaire":
                $s = " ( partenaire.name_part LIKE '%$arg%' OR partenaire.title_part LIKE '%$arg%' ) ";
                break;
            case "trainer" :
            case "formateur":
                $s = " ( formateur.initial_formateur LIKE '%$arg%' OR cf.nom_cont LIKE '%$arg%' OR cf.prenom_cont LIKE '%$arg%' ) ";
                break;
            case "location" :
            case "lieux":
                $s = " ( cc.add1_cont LIKE '%$arg%' OR cc.add2_cont LIKE '%$arg%' OR cc.cp_cont LIKE '%$arg%' OR cc.ville_cont LIKE '%$arg%' ) ";
                break;
            default:
                $s = " ";
                break;
        }
//        var_dump('convert test end');
        return $s;
    }

    private function getTimeWhere($date) {
//        var_dump('getTimeWhere start');   
        $d2 = "([12][0-9]{3}|[0-1]?[0-9]\/[12][0-9]{3}|[0-3]?[0-9]\/[0-1]?[0-9]\/[12][0-9]{3})";
        if(preg_match("/^$d2$/", $date, $o)) {
//            var_dump('d');
//            var_dump($o);
            $gap = $this->getTime($o[1], true);
//            var_dump($gap);
            $q = " ( f.start_session BETWEEN " . $gap[0] . " AND " . $gap[1] . " ) ";
        }
        else if(preg_match("/^$d2\s+-$/", $date, $o)) {
//            var_dump('d -');
//            var_dump($o);  
            $q = " ( f.start_session >= " . $this->getTime($o[1]) . " ) ";
        }
        else if(preg_match("/^-\s+$d2$/", $date, $o)) {
//            var_dump('- d');
//            var_dump($o);
            $q = " ( f.start_session <= " . $this->getTime($o[1]) . " ) ";
        }
        else if(preg_match("/^$d2\s+-\s+$d2$/", $date, $o)) {
//            var_dump('d1 - d2');
//            var_dump($o);
            $q = " ( f.start_session BETWEEN " . $this->getTime($o[1]) . " AND " . $this->getTime($o[2]) . " ) ";
        }
//        var_dump('getTimeWhere end');
        return $q;
    }

    private function getTime($date, $gap = false) {
        $t = explode("/", $date);
        date_default_timezone_set('Europe/Berlin');
        switch(count($t)) {
            case 1:
                echo "1/1/" . $t[0] . PHP_EOL;
                if($gap) {
                    $time = [];
                    $time[] = strtotime("1/1/" . $t[0]);
                    $time[] = strtotime("1/1/" . ($t[0] + 1));
                }
                else
                    $time = strtotime("1/1/" . $t[0]);
                break;
            case 2:
                echo "1/" . $t[0] . "/" . $t[1] . PHP_EOL;
                if($gap) {
                    $time = [];
                    $time[] = strtotime("1/" . $t[0] . "/" . $t[1]);
                    $time[] = strtotime("1/" . ($t[0] + 1) . "/" . $t[1]);
                }
                else
                    $time = strtotime("1/" . $t[0] . "/" . $t[1]);
                break;
            case 3:
                echo $t[1] . "/" . $t[0] . "/" . $t[2] . PHP_EOL;
                if($gap) {
                    $time = [];
                    $time[] = strtotime($t[1] . "/" . $t[0] . "/" . $t[2]);
                    $time[] = strtotime(($t[1] + 1) . "/" . $t[0] . "/" . $t[2]);
                }
                else
                    $time = strtotime($t[1] . "/" . $t[0] . "/" . $t[2]);
                break;
            default:
                $time = 0;
                break;
        }
        return $time;
    }

}

?>
