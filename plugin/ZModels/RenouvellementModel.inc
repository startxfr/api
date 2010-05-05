<?php
include_once('GeneralModel.inc');

/**
 * Description of RenouvellementModel
 *
 * @author Nicolas Mannocci
 * @version 1
 */
class RenouvellementModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'renouvellement';
	$this->sqlIdField = 'id_ren';
	$this->availableFields = array(
		'id_ren','type_ren','idChamp_ren','periode_ren',
		'fin_ren','actif_ren', 'mail_ren', 'statusChamp_ren');
	parent::__construct();
    }

    /**
     * Méthode qui gère l'insertion d'un nouveau renouvellement dans la base
     * @param array $datas Les données à insérer
     * @return array Indique si la requète s'est bien déroulée ou pas.
     */
    public function insert($datas) {
	$in = array();
	unset($datas['id_ren']);
	if(!array_key_exists('actif_ren', $datas))
	    $datas['actif_ren'] = 1;
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarRenouvellement($datas));
	Logg::loggerInfo('RenouvellementModel::insert() ~ ajout d\'un renouvellement de type '.$datas['type_ren'].' pour la ressource '.$datas['idChamp_ren'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui désactive un renouvellement
     * @param int $id L'id du renouvellement à désactiver
     * @return array Indique si la requète s'est bien déroulée
     */
    public function desactiver($id) {
	$sql = $this->makeRequeteUpdate($this->sqlTableName, $this->sqlIdField, $id, array("actif_ren" => "0"));
	Logg::loggerInfo('RenouvellementModel::desactiver() ~ désactivation du renouvellement '.$id,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui supprime un renouvellement de la base
     * @param int $id L'id du renouvellement à supprimer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function delete($id) {
	$sql = $this->makeRequeteDelete($this->sqlTableName, array($this->sqlIdField => $id));
	Logg::loggerInfo('RenouvellementModel::delete() ~ Suppression du renouvellement '.$id,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui met à jour un renouvellement
     * @param int $id L'id du renouvellement à mettre à jour
     * @param array $datas Les données mises à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function update($id, $datas) {
	unset($datas['id_ren']);
	$sql = $this->makeRequeteUpdate($this->sqlTableName, $this->sqlIdField, $id, $this->bindVarRenouvellement($datas));
	Logg::loggerInfo('RenouvellementModel::update() ~ Mise à jour du renouvellement '.$id,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations d'un renouvellement
     * @param int $id L'id du renouvellement
     * @return array Les infos du renouvellement
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("Select * from ".$this->sqlTableName." where ".$this->sqlIdField." = '".$id."' ");
	return $this->process2();
    }

    /**
     * Méthode qui retourne les éléments à renouveller ce jour.
     * @return array Les éléments à renouveller
     */
    public function getToDoToday() {
	$this->makeRequeteFree("Select r.*, a.id_aff, d.id_dev, c.id_cmd, f.id_fact, ff.id_factfourn, p.id_proj
                                from renouvellement r
                                left join affaire a on a.id_aff = r.idChamp_ren
                                left join devis d on d.id_dev = r.idChamp_ren
                                left join commande c on c.id_cmd = r.idChamp_ren
                                left join facture f on f.id_fact = r.idChamp_ren
                                left join facture_fournisseur ff on ff.id_factfourn = r.idChamp_ren
                                left join projet p on p.id_proj = r.idChamp_ren
                                where 
                                (DAYOFMONTH(r.fin_ren) = DAYOFMONTH(ADDDATE(CURRENT_DATE,1)) OR DAYOFMONTH(r.fin_ren) > LAST_DAY(ADDDATE(CURRENT_DATE,1)) )
                                AND (r.periode_ren != 12 OR MONTH(r.fin_ren) = MONTH(ADDDATE(CURRENT_DATE,1)) )
                                AND (r.periode_ren != 6 OR (MONTH(ADDDATE(CURRENT_DATE,1)) = 1 OR  MONTH(ADDDATE(CURRENT_DATE,1)) = 6) )
                                AND (r.periode_ren != 3 OR (  MOD((MONTH(ADDDATE(CURRENT_DATE,1))-MONTH(r.fin_ren)),3) = 0  ) )
                                AND r.actif_ren = '1' AND DATEDIFF(r.fin_ren, ADDDATE(CURRENT_DATE,1)) > -1 ORDER BY mail_ren ASC, type_ren ASC");
	return $this->process2();
    }

    /**
     * Méthode qui s'occupe de cloner une affaire
     * @param string $id l'id de l'affaire à cloner
     * @return array indique si la requête s'est bien passée
     */
    public function clonerAffaire($id, $status) {
	include_once('AffaireModel.inc');
	$model = new affaireModel();
	$datas = $model->getDataFromID($id);
	if($datas[0]) {
	    unset($datas[1][0]['id_aff']);
	    unset($datas[1][0]['modif_aff']);
	    $datas[1][0]['status_aff'] = $status;
	    Logg::loggerInfo('RenouvellementModel::clonerAffaire() ~ Clonnage de l\'affaire '.$id.' lors d\'un renouvellement',$datas[1][0],__FILE__.'@'.__LINE__);
	    $rs = $model->insert($datas[1][0],'cloner', $id);
	}
	return array(0);
    }

    /**
     * Méthode qui s'occupe de cloner un devis
     * @param string $id l'id du devis à cloner
     * @return array indique si la requête s'est bien passée
     */
    public function clonerDevis($id, $status) {
	include_once('DevisModel.inc');
	$model = new devisModel();
	$datas = $model->getDataFromID($id);
	if($datas[0]) {
	    $datas[1][0]['id_dev'] = $model->createId($datas[1][0]['affaire_dev']);
	    $prod = $model->getProduitsFromID($id);
	    $datas[1][0]['status_dev'] = $status;
	    Logg::loggerInfo('RenouvellementModel::clonerDevis() ~ Clonnage du devis '.$id.' lors d\'un renouvellement',$datas[1][0],__FILE__.'@'.__LINE__);
	    $res = $model->insert($datas[1][0], 'cloner', $prod[1]);
	    if($status > 2) {
		include_once('../ZDoc/DevisDoc.inc');
		$gnose = new devisGnose();
		$doc = $gnose->DevisGenerateDocument($datas[1][0]['id_dev']);
		$gnose->DevisSaveDocInGnose($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$doc,$doc,$datas[1][0]['affaire_dev'],"Renouvellement du devis ".$id);
	    }
	    if($res[0])
		$this->addActualite($datas[1][0]['id_dev'], 'clone', '', '', $id);
	    return $res;
	}
	return array(0);
    }

    /*
    public function clonerCommande($id, $status)
    {
        include_once('CommandeModel.inc');
        $model = new commandeModel();
        
    }
    */

    /**
     * Méthode qui s'occupe de cloner une facture
     * @param string $id l'id de la facture à cloner
     * @return array indique si la requête s'est bien passée
     */
    public function clonerFacture($id, $status) {
	include_once('FactureModel.inc');
	$model = new factureModel();
	$datas = $model->getDataFromID($id);
	if($datas[0]) {
	    $datas[1][0]['id_fact'] = $model->getLastId()+1;
	    $prod = $model->getProduitsFromID($id);
	    $datas[1][0]['status_fact'] = $status;
	    Logg::loggerInfo('RenouvellementModel::clonerFacture() ~ Clonnage de la facture '.$id.' lors d\'un renouvellement',$datas[1][0],__FILE__.'@'.__LINE__);
	    $res = $model->insert($datas[1][0], 'cloner', $prod[1]);
	    if($status > 2) {
		include_once('../ZDoc/FactureDoc.inc');
		$gnose = new factureGnose();
		$doc = $gnose->FactureGenerateDocument($datas[1][0]['id_fact']);
		$gnose->FactureSaveDocInGnose($doc, $datas[1][0]['id_fact'], "Renouvellement de la facture ".$id);
	    }
	    if($res[0])
		$this->addActualite($datas[1][0]['id_factfourn'], 'clone', '', '', $id);
	    return $res;
	}
	return array(0);
    }

    /**
     * Méthode qui s'occupe de cloner une facture fournisseur
     * @param string $id l'id de la facture fournisseur à cloner
     * @return array indique si la requête s'est bien passée
     */
    public function clonerFactureFournisseur($id, $status) {
	include_once('FactureFournisseurModel.inc');
	$model = new FactureFournisseurModel();
	$datas = $model->getDataFromID($id);
	if($datas[0]) {
	    $datas[1][0]['status_factfourn'] = $status;
	    $datas[1][0]['datePaye_factfourn'] = date('Y-m-d 00:00:00');
	    if($status > 3) {
		$datas[1][0]['dateReglement_factfourn'] = date('Y-m-d 00:00:00');
	    }
	    unset($datas[1][0]['id_factfourn']);
	    unset($datas[1][0]['fichier_factfourn']);
	    Logg::loggerInfo('RenouvellementModel::clonerFactureFournisseur() ~ Clonnage de la facture fournisseur '.$id.' lors d\'un renouvellement',$datas[1][0],__FILE__.'@'.__LINE__);
	    $rs = $model->insert($datas[1][0]);
	    if($rs[0])
		$this->addActualite($datas[1][0]['id_factfourn'], 'clone', '', '', $id);
	    return $rs;
	}
	return array(0);

    }

    /**
     * Méthode qui s'occupe de cloner un projet
     * @param string $id l'id du projet à cloner
     * @return array indique si la requête s'est bien passée
     */
    public function clonerProjet($id) {
	include_once('ContactModel.inc');
	$model = new projetModel();
	$datas = $model->getDataFromID($id);
	if($datas[0]) {
	    unset($datas[1][0]['id_proj']);
	    Logg::loggerInfo('RenouvellementModel::clonerProjet() ~ Clonnage du projet '.$id.' lors d\'un renouvellement',$datas[1][0],__FILE__.'@'.__LINE__);
	    return $model->insert($datas[1][0]);
	}
	return array(0);

    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table transaction
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarRenouvellement($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}
?>
