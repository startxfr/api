<?php
include_once('GeneralModel.inc');
/**
 * Classe gérant toutes les requètes SQL en rapport avec les factures fournisseur.
 * @author Nicolas Mannocci
 * @version 1.0
 */
class FactureFournisseurModel extends GeneralModel {


    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'facture_fournisseur';
	$this->sqlIdField = 'id_factfourn';
	$this->availableFields = array(
		'id_factfourn','entreprise_factfourn','contact_factfourn','titre_factfourn', 'desc_factfourn',
		'montantTTC_factfourn', 'tauxTVA_factfourn', 'dateFact_factfourn', 'datePaye_factfourn', 'dateReglement_factfourn', 'modeReglement_factfourn',
		'status_factfourn', 'user_factfourn', 'fichier_factfourn', 'ren_factfourn');
	parent::__construct();
    }

    /**
     * Méthode qui réalise l'insertion d'un tuple dans la table facture_fournisseur
     * @param array $datas Les données à insérer
     * @return array Indique si la requète s'est bien déroulée ou non.
     */
    public function insert($datas) {
	$in = array();

	$datas['id_factfourn'] = "FF".date('ym');
	$this->makeRequeteFree("SELECT id_factfourn FROM facture_fournisseur WHERE id_factfourn LIKE '".$datas['id_factfourn']."%' ORDER BY id_factfourn DESC LIMIT 1");
	$res = $this->process();
	$lastid = $res[0];
	if($lastid['id_factfourn'] != '') {
	    $lastDigit = (int) substr($lastid['id_factfourn'],6);
	    $lastDigit++;
	    if($lastDigit < 10) $lastDigit = "0".$lastDigit;
	}
	else $lastDigit = "01";
	$datas['id_factfourn'] .= $lastDigit;

	$datas['montantTTC_factfourn'] = prepareNombreTraitement($datas['montantTTC_factfourn']);
	if($datas['tauxTVA_factfourn'] != '')
	    $datas['tauxTVA_factfourn'] = prepareNombreTraitement($datas['tauxTVA_factfourn']);
	foreach($datas as $k => $v)
	    if(in_array($k,$this->availableFields)) $in[$k] = stripslashes($v);

	$in['user_factfourn'] = ($_SESSION['user']['id'] != '') ? $_SESSION['user']['id'] : $in['user_factfourn'];
	$sql = $this->makeRequeteInsert($this->sqlTableName, $in);
	Logg::loggerAlert('FactureFournisseurModel::insert() ~ ajout de la facture fournisseur '.$data['id_factfourn'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0])
	    $this->addActualite($data['id_factfourn'], 'add');
	return $rs;
    }

    /**
     * Méthode qui gère la mise à jour des tuples dans la base
     * @param string $id L'id du champ à mettre à jour
     * @param array $datas Les données à mettres à jour
     * @return array Indique si la requète s'est bien passée ou non.
     */
    public function update($id, $datas) {
	if(array_key_exists('id_factfourn', $datas))
	    unset($datas['id_factfourn']);
	if($datas['tauxTVA_factfourn'] != '')
	    $datas['tauxTVA_factfourn'] = prepareNombreTraitement($datas['tauxTVA_factfourn']);
	if($datas['montantTTC_factfourn'] != '')
	    $datas['montantTTC_factfourn'] = prepareNombreTraitement($datas['montantTTC_factfourn']);
	foreach($datas as $k => $v)
	    if(in_array($k,$this->availableFields)) $in[$k] = stripslashes($v);
	$sql = $this->makeRequeteUpdate($this->sqlTableName, $this->sqlIdField, $id, $in);
	Logg::loggerAlert('FactureFournisseurModel::update() ~ modification de la facture fournisseur '.$id,$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0] and ($datas['montantTTC_factfourn'] != '' or $datas['tauxTVA_factfourn'] != ''))
	    $this->addActualite($id, 'update');
	return $rs;
    }

    /**
     * Méthode qui gère la suppression d'un tuple
     * @param string $id L'id du tuple à supprimer
     * @return array Indique si la requète s'est bien déroulée
     */
    public function delete($id) {
	$liste[$this->sqlIdField] = $id;
	$sql = $this->makeRequeteDelete($this->sqlTableName, $liste);
	Logg::loggerAlert('FactureFournisseurModel::delete() ~ suppression de la facture fournisseur '.$id,$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0])
	    $this->addActualite($id, 'delete');
	return $rs;
    }

    /**
     * Méthode qui récupère toutes les infos d'un tuple
     * @param string $id L'id de la facture dont on veut les infos
     * @return array Les données demandées
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("Select * from ".$this->sqlTableName.
		" Left join entreprise e on e.id_ent = entreprise_factfourn ".
		" Left join contact c on c.id_cont = contact_factfourn ".
		" Left join ref_statusfacturefournisseur rf on rf.id_stfactfourn = status_factfourn ".
		" where ".$this->sqlIdField." = '".$id."'");
	return $this->process2();
    }

    /**
     * Méthode qui effectue une recherche
     * @param string $search Un champ de recherche unique
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre de résultats (ALL permettant d'obtenir le COUNT(*)
     * @param string $order Indique l'ordre de classement des résultats
     * @param array $conditions D'éventuelels conditions pour une recherche avancée
     * @return array Les données
     */
    public function getDataForSearch($search ='', $from = '0', $limit ='30', $order='ORDER BY id_factfourn DESC', $conditions = '') {
	$where = " WHERE ";
	$archive = false;
	if(is_array($conditions) and $search == '') {
	    foreach($conditions as $k => $v) {
		if($k == 'cp_ent')
		    $where .= "$k LIKE '".$v."%' AND ";
		elseif($k == 'montantHT_factfourn')
		    $where .= " $k >= '$v' AND ";
		elseif($k == 'montantHT_factfourn2')
		    $where .= " montantHT_factfourn < '$v' AND ";
		elseif($k == 'status_factfourn') {
		    $where .= "$k = '".$v."' AND ";
		    $archive = true;
		}
		elseif($k == 'entreprise_factfourn')
		    $where .= "$k = '".$v."' AND ";
		else
		    $where .= "$k LIKE '%".$v."%' AND ";
	    }
	    if(!$archive)
		$where = substr($where, 0, (strlen($where)-5));
	    else
		$where .= " status_factfourn != 5 ";
	}
	else {
	    $where .= "titre_factfourn LIKE'%".$search."%' OR desc_factfourn LIKE'%".$search."%' OR nom_ent LIKE'%".$search."%' OR nom_cont LIKE'%".$search."%' or cp_ent LIKE'%".$search."%' ";
	}

	if($limit != "ALL") {
	    $this->makeRequeteFree("Select id_factfourn, titre_factfourn, ren_factfourn, nom_stfactfourn, desc_factfourn, nom_ent, cp_ent, ville_ent, montantTTC_factfourn, civ_cont, prenom_cont, nom_cont, datePaye_factfourn ".
		    " from facture_fournisseur
                left join entreprise on id_ent = entreprise_factfourn
                left join contact on id_cont = contact_factfourn
                left join ref_statusfacturefournisseur on id_stfactfourn = status_factfourn ".$where. " ".$order." Limit ".$from." , ".$limit);
	}
	else {
	    $this->makeRequeteFree("Select COUNT(*)".
		    " from facture_fournisseur
                left join entreprise on id_ent = entreprise_factfourn
                left join contact on id_cont = contact_factfourn
                left join ref_statusfacturefournisseur on id_stfactfourn = status_factfourn ".$where);
	}
	return $this->process2();
    }

    /**
     * Méthode qui retourne le nom du fichier lié à une facture
     * @param string $id l'id de la facture
     * @return string Le nom du fichier
     */
    public function getFichier($id) {
	$this->makeRequeteFree("Select fichier_factfourn as fichier from ".$this->sqlTableName." where ".$this->sqlIdField." = '".$id."'");
	$temp = $this->process2();
	if($temp[0])
	    return $temp[1][0]['fichier'];
	else
	    return '';
    }

    /**
     * Méthode qui retourne les infos sur le renouvellement d'une facture fournisseur
     * @param int $id L'id du renouvellement
     * @return array les données correspondantes
     */
    public function getRenouvellement($id) {
	$this->makeRequeteFree("Select * from renouvellement where id_ren = '".$id."' ");
	$temp = $this->process2();
	if($temp[0]) {
	    return $temp[1][0];
	}
	else return $temp;
    }
}
?>