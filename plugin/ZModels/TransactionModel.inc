<?php
include_once('GeneralModel.inc');

/**
 * Description of TransactionModel
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class TransactionModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'transaction';
	$this->sqlIdField = 'id_trans';
	$this->availableFields = array('id_trans', 'payline_trans', 'contact_trans', 'nom_trans', 'prenom_trans',
		'facture_trans', 'devise_trans', 'montant_trans');

	parent::__construct();
    }

    /**
     * Méthode qui insert les données dans table
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insert($data) {
	foreach($data as $k => $v)
	    if(in_array($k,$this->availableFields)) $in[$k] = stripslashes($v);
	$this->makeRequeteInsert($this->sqlTableName,$in);
	return $this->process2();
    }

    /**
     * Méthode qui récupère les infos depuis un id de transaction zuno
     * @param int $id L'id zuno de transaction
     * @return array Les données
     */
    public function getDatasFromId($id) {
	$this->makeRequeteSelect($this->sqlTableName, $this->sqlIdField, $id);
	return $this->process2();
    }

    /**
     * Méthode qui récupère les infos depuis un id de transaction payline
     * @param int $id L'id d'historique_payline
     * @return array Les données
     */
    public function getDatasFromPayline($id) {
	$this->makeRequeteSelect($this->sqlTableName, "payline_trans", $id);
	return $this->process2();
    }

    /**
     * Méthode qui récupère les infos depuis une facture
     * @param int $id L'id de la facture
     * @return array Les données
     */
    public function getDatasFromFacture($id) {
	$this->makeRequeteSelect($this->sqlTableName, "facture_trans", $id);
	return $this->process2();
    }

    /**
     * Méthode qui récupère les infos depuis un id de contact
     * @param int $id L'id du contact
     * @return array Les données
     */
    public function getDatasFromContact($id) {
	$this->makeRequeteSelect($this->sqlTableName, "contact_trans", $id);
	return $this->process2();
    }
}
?>
