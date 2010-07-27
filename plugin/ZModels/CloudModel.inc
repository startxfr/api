<?php
loadPlugin('ZModels/GeneralModel');


/**
 * Class d'accès aux informations du cloud
 * @author Nicolas Mannocci
 * @version 1.0
 */
class CloudModel extends GeneralModel {


    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
        $this->sqlTableName = 'cloud';
        $this->sqlIdField = 'recherche_cloud';
        $this->availableFields = array(
                'recherche_cloud', 'total_cloud', 'module_cloud', 'user_cloud');
        parent::__construct();
    }

    /**
     * Méthode qui supprime des infos du clouds par module et apr user
     * @param string/array $module Le ou les modules dont on veut supprimer les tags
     * @return array Indique si tout s'est bien passé
     */
    public function dropToCloud($module, $user = ''){
        $where = "";
        $user = ($user == '') ? $_SESSION['user']['id'] : $user;
        if(is_array($module)){
            foreach ($module as $v) {
                $where .= "module_cloud = '".$v."' OR ";
            }
            $where = substr($where, 0, strlen($where)-4);
        }
        else{
            $where .= "module_cloud = '".$module."'";
        }
        $this->makeRequeteFree("DELETE FROM cloud WHERE (".$where.") AND user_cloud = '".$user."'");
        return $this->process2();
    }

    /**
     * Méthode qui ajoute un élément au cloud
     * @param string $recherche
     */
    public function addToCloud($recherche, $module) {
        $recherche = ucfirst(strtolower($recherche));
        $this->makeRequeteFree("SELECT recherche_cloud, total_cloud FROM ".$this->sqlTableName." WHERE recherche_cloud = '".$recherche."' AND module_cloud = '".$module."' AND user_cloud = '".$_SESSION['user']['id']."'");
        $already = $this->process2();
	if($already[0]) {
            if(is_array($already[1]) and count($already[1]) > 0 and array_key_exists('recherche_cloud', $already[1][0]) and ucfirst(strtolower($already[1][0]['recherche_cloud'])) == $recherche) {
                $total = (array_key_exists('total_cloud', $already[1][0])) ? ($already[1][0]['total_cloud']+1) : 1;
                $this->makeRequeteFree("UPDATE ".$this->sqlTableName." SET total_cloud = ".$total." WHERE recherche_cloud = '".$recherche."' AND module_cloud = '".$module."' AND user_cloud = '".$_SESSION['user']['id']."'");
	    }
            else {
                $liste['recherche_cloud'] = $recherche;
                $liste['module_cloud'] = $module;
                $liste['user_cloud'] = $_SESSION['user']['id'];
                $this->makeRequeteInsert($this->sqlTableName, $liste);
            }
        }

        $this->process2();
    }


    /**
     * Méthode qui récupère les données pour générer un cloud
     * @param string $module Le module dans lequel on fait le cloud (si vide on prend tout le monde
     * @return array Les données pour faire le cloud.
     */
    public function getCloud($module = "") {
        if(is_array($module)) {
            $mod = "";
            foreach($module as $v) {
                $mod .= "module_cloud = '".$v."' OR ";
            }
            $mod = substr($mod, 0, strlen($mod)-4);
            $this->makeRequeteFree("SELECT * FROM ".$this->sqlTableName." WHERE (".$mod.") AND user_cloud = '".$_SESSION['user']['id']."' ORDER BY total_cloud DESC LIMIT 0,15");
        }
        elseif($module != "")
            $this->makeRequeteFree("SELECT * FROM ".$this->sqlTableName." WHERE module_cloud = '".$module."' AND user_cloud = '".$_SESSION['user']['id']."' ORDER BY total_cloud DESC LIMIT 0,15");
        else
            $this->makeRequeteFree("SELECT * FROM ".$this->sqlTableName." WHERE user_cloud = '".$_SESSION['user']['id']."' ORDER BY total_cloud DESC LIMIT 0,40");
        return $this->process2();
    }
}
?>
