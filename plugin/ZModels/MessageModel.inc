<?php
include_once('GeneralModel.inc');

/**
 * Classe qui gère la table des messages
 *
 * @author Nicolas Mannocci
 * @version 1
 */
class MessageModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'message';
	$this->sqlIdField = 'id_mess';
	$this->availableFields = array('id_mess', 'titre_mess', 'contenu_mess', 'debut_mess', 'fin_mess', 'user_mess');
	parent::__construct();
    }

    /**
     *Méthode qui permet de faire une recherche sur les messages
     * La clé date permet de récupérer les messages à un jour précis
     * @param array $criteres Un tableau à 2 dimension, la clé et la valeur
     * @return array Le résultat
     */
    public function searchMess($criteres = '') {
	$conditions = '';
	if(is_array($criteres)) {
	    $conditions = "where ";
	    foreach($criteres as $k => $v) {
		if($k == 'date')
		    $conditions .= " date_debut <= '".$v."' and date_fin >= '".$v."' ";
		else
		    $conditions .= $k." LIKE '%".$v."%' AND ";
	    }
	    $conditions = substr($conditions,0,(strlen($conditions)-4));
	}
	$this->makeRequeteFree("SELECT * from message ".$conditions);
	return $this->process2();
    }

    /**
     *Méthode qui récupère toutes les infos d'un seul message identifié par son ID
     * @param string $id l'identifiant du message
     * @return array Le résultat
     */
    public function getMessByID($id = '') {
	if($id != '') {
	    $this->makeRequeteFree("SELECT * from message where id_mess = '".$id."'");
	    $result = $this->process2();
	}
	else
	    $reuslt = array(0,'Aucun id en paramètre');
	return $result;
    }

    /**
     *Méthode qui récupère le message éventuel à afficher pour un utilisateur donné à la date du jour
     * @param string $user L'id de l'utilisateur
     * @return array Le résultat
     */
    public function getUserMess($user = '') {
	$result = array(0,'Aucun utilisateur !!');
	if($user != '') {
	    $this->makeRequeteFree("Select * from message where user_mess = '".$user."' and debut_mess <= CURRENT_DATE and fin_mess >= CURRENT_DATE");
	    $result = $this->process2();
	}
	return $result;
    }

    /**
     *Méthode qui récupère tous les messages ayant été potentiellement affichés à l'utilisateur
     * @param string $user l'Id de l'utilisateur
     * @return array Le résultat
     */
    public function getUserHistoMess($user = '') {
	$result = array(0, 'Aucun utilisateur !!');
	if($user != '') {
	    $this->makeRequeteFree("SELECT * from message where user_mess = '".$user."' and fin_mess >= CURRENT_DATE ");
	    $result = $this->process2();
	}
	return $result;
    }

    /**
     *Méthode qui récupère tous les futurs messages à afficher à un utilisateur donné
     * @param string $user L'id de l'utilisateur
     * @return array Le résultat
     */
    public function getUserFuturMess($user = '') {
	$result = array(0, 'Aucun utilisateur !!');
	if($user != '') {
	    $this->makeRequeteFree("SELECT * from message where user_mess = '".$user."' and debut_mess > CURRENT_DATE ");
	    $result = $this->process2();
	}
	return $result;
    }

    /**
     *Méthode qui retourne tous les messages à afficher aujourd'hui pour tous les utilisateurs
     * @return array Le résultat
     */
    public function getTodayMess() {
	$this->makeRequeteFree("SELECT * from message where debut_mess <= CURRENT_DATE and fin_mess >= CURRENT_DATE ");
	return $this->process2();
    }

    /**
     *Méthode qui ajoute un message pour l'afficher à un utilisateur donné.
     * @param array $data Les données à insérer
     * @return array Le résultat
     */
    public function addMess($data) {
	if(array_key_exists('id_mess', $data))
	    unset($data['id_mess']);
	if(array_key_exists('debut_mess', $data))
	    $data['debut_mess'] = substr($data['debut_mess'], 6,4).substr($data['debut_mess'], 3,2).substr($data['debut_mess'],0,2);
	if(array_key_exists('fin_mess', $data))
	    $data['fin_mess'] = substr($data['fin_mess'], 6,4).substr($data['fin_mess'], 3,2).substr($data['fin_mess'],0,2);
	$this->makeRequeteInsert('message', $this->bindVarMessage($data));
	Logg::loggerInfo('MessageModel::addMess() ~ ajout du message '.$data['titre_mess'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     *Méthode qui ajout un message à la date du jour, d'une durée d'une journée
     * Insertion à tous les users si non précisés.
     * @param string $titre
     * @param string $mess
     * @param array $user
     */
    public function addTodayMess($titre, $mess, $user = array()) {
	$data['titre_mess'] = $titre;
	$data['contenu_mess'] = $mess;
	$data['debut_mess'] = $data['fin_mess'] = date("Y-m-d");
	if($user == array()) {
	    $this->makeRequeteFree("SELECT login from user");
	    $users = $this->process2();
	    foreach($users[1] as $v)
		array_push($user, $v['login']);
	}
	foreach($user as $v) {
	    $data['user_mess'] = $v;
	    $sql = $this->makeRequeteInsert('message', $data);
	    $this->process2();
	}
	Logg::loggerInfo('MessageModel::addTodayMess() ~ ajout du message '.$data['titre_mess'],$sql,__FILE__.'@'.__LINE__);
    }

    /**
     *Méthode qui met à jour un message.
     * @param array $data
     * @return array Le résultat
     */
    public function updateMess($data) {
	if(array_key_exists('id_mess', $data)) {
	    $id = $data['id_mess'];
	    unset($data['id_mess']);
	}
	else return array(0,'Il manque l\'identifiant du message');
	$sql = $this->makeRequeteUpdate('message', 'id_mess', $id, $this->bindVarMessage($data));
	Logg::loggerInfo('MessageModel::updateMess() ~ modification du message n°'.$data['id_mess'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }


    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table historique_payline
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarMessage($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}
?>
