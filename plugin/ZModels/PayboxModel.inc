<?php
loadPlugin('ZModels/GeneralModel');

/**
 * Description of PayboxModel
 * Sitôt finie, sitôt oubliée pour Payline
 *
 * @author Nicolas Mannocci
 * @version 1
 */
class PayboxModel extends GeneralModel {

    /**
     * Constructeur qui initialise les variables
     */
    public function __construct() {
	$this->sqlTableName = 'paybox';
	$this->sqlIdField = 'id_pb';
	$this->availableFields = array(
		'id_pb', 'date_pb', 'type_pb', 'numQuestion_pb', 'devise_pb', 'reference_pb', 'abonne_pb', 'activite_pb',
		'archivage_pb', 'differe_pb', 'numAppel_pb', 'numTrans_pb', 'carte_pb', 'codeReponse_pb',
		'autorisation_pb', 'status_pb', 'remise_pb', 'commentaire_pb', 'montant_pb');
	parent::__construct();
    }

    /**
     * Méthode qui récupère la liste des types d'actions demandés à Paybox
     * @return array La liste des types disponibles
     */
    public function getTypes() {
	$this->makeRequeteFree("SELECT * from ref_typePaybox");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_typb']] = $v['nom_typb'];
	return $countryList;
    }

    /**
     * Méthode qui retourne la liste des types d'activités possible à déclarer
     * @return array La liste des types d'activités
     */
    public function getTypesAct() {
	$this->makeRequeteFree("SELECT * from ref_typeActPaybox");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_tyactpb']] = $v['nom_tyactpb'];
	return $countryList;
    }

    /**
     * Méthode qui génère un nouveau numéro de question.
     * @return string Le numéro de question
     */
    public function getNewNumQuestion() {
	$this->makeRequeteFree("SELECT COUNT(id_pb) AS counter FROM paybox WHERE DAYOFYEAR(date_pb) = DAYOFYEAR(NOW())");
	$rs = $this->process2();
	if($rs[0])
	    $rs = $rs[1][0]['counter']+1;
	else $rs = "0123654789";
	while (strlen($rs) != 10)
	    $rs = "0".$rs;
	return $rs;
    }

    /**
     * Méthode qui insert les données dans la base
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function insert($data) {
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarPaybox($data));
	Logg::loggerInfo('PayboxModel::insert() ~ ajout de la transaction Paybox '.$data['numTrans_pb'].' de type '.$data['type_pb'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui insert les coordonnées bancaires d'un contact
     * @param int $contact Le contact
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insertCoordonnesBancaires($contact, $data) {
	if(!is_numeric($contact))
	    return array('0', '1' => 'Aucun contact');
	$in['refAbonne_cb'] = $this->pack_str($this->zEncrypt($data['REFABONNE']), strlen($this->zEncrypt($data['REFABONNE'])));
	$in['porteur_cb'] = $this->pack_str($this->zEncrypt($data['PORTEUR']), strlen($this->zEncrypt($data['PORTEUR'])));
	$in['dateval_cb'] = $this->pack_str($this->zEncrypt($data['DATEVAL']), strlen($this->zEncrypt($data['DATEVAL'])));
	$in['cvv_cb'] = $this->pack_str($this->zEncrypt($data['CVV']), strlen($this->zEncrypt($data['CVV'])));
	$in['contact_cb'] = $contact;
	$sql = $this->makeRequeteInsert("contact_banque", $in);
	Logg::loggerInfo('PayboxModel::insertCoordonnesBancaires() ~ ajout du portefeuille client '.$data['refAbonne_cb'].' pour le porteur '.$data['porteur_cb'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui met à jour les coordonnées bancaires d'un contact
     * @param int $contact Le contact
     * @param array $data Les données à mettre à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function updateCoordonnesBancaires($contact, $data) {
	if(!is_numeric($contact))
	    return array('0', '1' => 'Aucun contact');
	$in['porteur_cb'] = $this->pack_str($this->zEncrypt($data['PORTEUR']), strlen($this->zEncrypt($data['PORTEUR'])));
	$in['dateval_cb'] = $this->pack_str($this->zEncrypt($data['DATEVAL']), strlen($this->zEncrypt($data['DATEVAL'])));
	$in['cvv_cb'] = $this->pack_str($this->zEncrypt($data['CVV']), strlen($this->zEncrypt($data['CVV'])));

	$sql = $this->makeRequeteUpdate("contact_banque", "contact_cb", $contact, $in, " AND refAbonne_cb = '".$data['REFABONNE']."'");
	Logg::loggerInfo('PayboxModel::updateCoordonnesBancaires() ~ mise à jour des coordonnées bancaires pour le contact '.$contact,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui récupère les coordonnées bancaires d'un contact
     * @param int $contact Le contact dont on veut les données
     * @return array Les données du contact
     */
    public function getCoordonnesBancaires($contact) {
	$this->makeRequeteFree("SELECT refAbonne_cb, porteur_cb, dateval_cb, cvv_cb from contact_banque where contact_cb = '".$contact."'");
	$rs = $this->process2();
	if($rs[0]) {
	    $retour['PORTEUR'] = $this->unpack_str($this->zDecrypt($rs[1][0]['porteur_cb']), strlen($this->zDecrypt($rs[1][0]['porteur_cb'])));
	    $retour['DATEVAL'] = $this->unpack_str($this->zDecrypt($rs[1][0]['dateval_cb']), strlen($this->zDecrypt($rs[1][0]['dateval_cb'])));
	    $retour['CVV'] = $this->unpack_str($this->zDecrypt($rs[1][0]['cvv_cb']), strlen($this->zDecrypt($rs[1][0]['cvv_cb'])));
	    $retour['REFABONNE'] = $this->unpack_str($this->zDecrypt($rs[1][0]['refAbonne_cb']), strlen($this->zDecrypt($rs[1][0]['refAbonne_cb'])));
	}
	else {
	    Logg::error('PayboxModel::getCoordonnesBancaires() ~ Erreur lors de la récupération des coordonnées bancaires du contact '.$contact,$rs[1],__FILE__.'@'.__LINE__);
	    $retour['ERROR'] = $rs[1];
	}
	return $retour;
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table historique_payline
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarPaybox($data) {
	return $this->bindVar($data,$this->availableFields);
    }


}
?>
