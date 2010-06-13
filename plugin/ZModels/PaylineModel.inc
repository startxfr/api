<?php
loadPlugin('ZModels/GeneralModel');

/**
 * Description of PaylineModel
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class PaylineModel extends GeneralModel {

    /**
     * Constructeur qui initialise les variables
     */
    public function __construct() {
	$this->sqlTableName = 'historique_payline';
	$this->sqlIdField = 'id_hp';
	$this->availableFields = array(
		'id_hp', 'transaction_hp', 'date_hp', 'type_hp', 'montant_hp', 'devise_hp', 'typeCb_hp',
		'differe_hp', 'contact_hp', 'codeRetour_hp', 'shortRetour_hp', 'longRetour_hp',
		'fraude_hp', 'doublon_hp', 'autorisation_hp', 'dateAutorisation_hp');
	parent::__construct();
    }

    /**
     * Méthode qui insert les données dans table
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insert($data) {
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarPayline($data));
	Logg::loggerInfo('PaylineModel::insert() ~ ajout de la transaction Payline '.$data['transaction_hp'].' de type '.$data['type_hp'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations sur un contact
     * @param int $id L'id du contact
     * @return array Les informations
     */
    public function getInfosContact($id) {
	$this->makeRequeteFree("select contact.*, nom_cp as nom_cont, prenom_cp as prenom_cont, wallet_cp as wallet_cont, date_cp as dateCarte_cont, cvv_cp as cvvCarte_cont, fin_cp as finCarte_cont, type_cp as typeCarte_cont, entreprise.*
				from contact
				left join contact_payline on contact_cp = id_cont
				left join entreprise on entreprise_cont = id_ent
				where id_cont = '".$id."'");
	$rs = $this->process2();
	if($rs[0])
	    return $rs[1][0];
	else return $rs;
    }

    /**
     * Méthode qui sauvegarde les informations de payement d'un client
     * @param array $array Le tableau des éléments à sauvegarder
     * @return array Indique si la requête s'est bien exécutée
     */
    public function saveInfosContact($array) {
	$this->makeRequeteFree("select COUNT(*) from contact_payline where contact_cp = '".$array['id']."'");
	$c = $this->process2();
	if(array_key_exists("nom_cont", $array) and $array["nom_cont"] != "")
	    $in["nom_cp"] = $array['nom_cont'];
	if(array_key_exists("prenom_cont", $array))
	    $in["prenom_cp"] = $array['prenom_cont'];
	if(array_key_exists("wallet_cont", $array) and $array["wallet_cont"] != "")
	    $in['wallet_cp'] = $this->pack_str($this->zEncrypt($array['wallet_cont']));
	if(array_key_exists("dateCarte_cont", $array) and $array["dateCarte_cont"] != "")
	    $in["date_cp"] = $array['dateCarte_cont'];
	if(array_key_exists("typeCarte_cont", $array) and $array["typeCarte_cont"] != "")
	    $in["type_cp"] = $array['typeCarte_cont'];
	if(array_key_exists("cvvCarte_cont", $array) and $array["cvvCarte_cont"] != "")
	    $in['cvv_cp'] = $this->pack_str($this->zEncrypt($array['cvvCarte_cont']));
	if(array_key_exists("carte", $array) and $array["carte"] != "")
	    $in['fin_cp'] = substr($array['carte'], strlen($array['carte'])-6, 6);
	if($c[1][0]['COUNT(*)'] == 0) {
	    $in['contact_cp'] = $array['id'];
	    $sql = $this->makeRequeteInsert('contact_payline', $in);
	}
	else $sql = $this->makeRequeteUpdate("contact_payline", "contact_cp", $array['id'], $in);
	Logg::loggerInfo('PaylineModel::saveInfosContact() ~ mise à jour des informations de paiement pour le contact '.$array['id'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations depuis l'id de transaction de Payline
     * @param string $transaction L'id Payline de la transaction
     * @return array Les données recherchées
     */
    public function getInfosByTransaction($transaction) {
	$this->makeRequeteFree("select * from ".$this->sqlTableName." where transaction_hp = '".$transaction."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations depuis un ID
     * @param int $id L'id du tuple recherché
     * @return array Les données recherchées
     */
    public function getDatasFromId($id) {
	$this->makeRequeteSelect($this->sqlTableName, $this->sqlIdField, $id);
	return $this->process2();
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table historique_payline
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarPayline($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}
?>
