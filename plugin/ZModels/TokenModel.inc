<?php
loadPlugin('ZModels/GeneralModel');

/**
 * Description of TokenModel
 *
 * @version 1
 * @author Nicolas Mannocci
 */
class TokenModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'token';
	$this->sqlIdField = 'id_token';
	parent::__construct();
    }


    /**
     * Méthode qui gère l'insertion d'un token dans la base
     * @param string $user L'utilisateur concerné
     * @param string $action L'action a effectuée une fois le token utilisé
     * @return array Indique si la requète s'est bien déroulée
     */
    public function insert($user, $action) {
	$data['id_token'] = md5($user.microtime(true));
	$data['user_token'] = $user;
	$data['action_token'] = $action;
	$data['used_token'] = 0;
	$sql = $this->makeRequeteInsert($this->sqlTableName, $data);
	Logg::loggerInfo('tokenModel::insert() ~ ajout du token n°'.$data['id_token'],$sql,__FILE__.'@'.__LINE__);
	$res = $this->process2();
	if($res[0]) {
	    return array(1, $data['id_token']);
	}
	return array(0);
    }

    /**
     * Méthode qui récupère les informations à partir d'un token
     * @param string $id Le token dont on veut les infos
     * @return array Les informations
     */
    public function getInfos($id) {
	$this->makeRequeteFree("Select * from ".$this->sqlTableName." where ".$this->sqlIdField." = '".$id."' ");
	return $this->process2();
    }

    /**
     * Méthode qui marque un token comme utilisé
     * @param string $id Le token à marquer
     * @return array Indique si la requète s'est bien passée
     */
    public function used($id) {
	$this->makeRequeteUpdate($this->sqlTableName, $this->sqlIdField, $id, array('used_token' => '1'));
	return $this->process2();
    }

    /**
     * Méthode qui supprime un token de la base
     * @param string $id Le token à supprimer
     * @return array Indique si la requète s'est bien déroulée
     */
    public function delete($id) {
	$sql = $this->makeRequeteDelete($this->sqlTableName, array($this->sqlIdField => $id));
	Logg::loggerInfo('tokenModel::delete() ~ suppression du token n°'.$id,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }
}
?>
