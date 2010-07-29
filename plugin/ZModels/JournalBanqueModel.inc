<?php
loadPlugin('ZModels/GeneralModel');
/**
 * Classe qui gère les connexions à la base de données
 * pour tout ce qui concerne les journalBanques.
 *
 * @author Nicolas Mannocci
 * @version 1
 */
class journalBanqueModel extends GeneralModel {

    //Champs dispos de la table journal_banque
    protected $availableFields;

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'journal_banque';
	$this->sqlIdField = 'id_jb';
	$this->availableFields = array('id_jb','date_record_jb','date_effet_jb','banque_jb','modereglement_jb','file_jb','facture_jb','entreprise_jb','libelle_jb','montant_jb','commentaire_jb');
	parent::__construct();
    }


    /**
     * Méthode qui effectue une recherche sur la table journal_banque
     * @param string $search Ce que l'on cherche (inutilisé)
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultat(s) désiré
     * @param string $ordre Précise l'ordre de tri des résultats
     * @param array $conditions Les critères de recherche
     * @return array Les résultats
     */
    public function getDataForSearchWeb($search = '', $from = 0, $limit = 10, $ordre = 'ORDER BY libelle_jb', $conditions = array()) {
	if(is_array($conditions)) {
	    $where = " WHERE ";
	    foreach($conditions as $k => $v) {
		if($k == 'dateStart_jb')	$where .= " date_effet_jb >= '$v' AND ";
		elseif($k == 'dateEnd_jb')	$where .= " date_effet_jb <= '$v' AND ";
		elseif($k == 'montantMin_jb')	$where .= " montant_jb >= '$v' AND ";
		elseif($k == 'montantMax_jb')	$where .= " montant_jb <= '$v' AND ";
		elseif($k == 'libelle_jb')	$where .= "libelle_jb LIKE '%".$v."%' AND ";
		elseif($k == 'sens') {
		    if($v == 'C')		$where .= "montant_jb >= 0 AND ";
		    elseif($v == 'D')		$where .= "montant_jb <= 0 AND ";
		}
		elseif(in_array($k, $this->availableFields))
		    $where .= "$k LIKE '%".$v."%' AND ";
	    }
	    $where .= " id_jb IS NOT NULL ";
	}
	if($limit != 'ALL')
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName
				    LEFT JOIN entreprise e ON e.id_ent = $this->sqlTableName.entreprise_jb
				    LEFT JOIN facture f ON f.id_fact = $this->sqlTableName.facture_jb
				    LEFT JOIN ref_modereglement rmr ON rmr.id_modereg = $this->sqlTableName.modereglement_jb
		    $where $ordre LIMIT $from , $limit");
	else $this->makeRequeteFree("SELECT COUNT(DISTINCT id_jb) AS counter FROM $this->sqlTableName $where");
	return $this->process2();
    }

    /**
     * Méthode qui insert une entrée de la table journal_banque
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function insertJournalEcriture($data, $tmpFile = '') {
	$data[$this->sqlIdField] = $this->getLastId()+1;
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarJournalBanqueHisto($data));
	Logg::loggerAlert('JournalBanqueModel::insertJournalBanque() ~ ajout d\'une écriture bancaire '.$data['libelle_jb'],$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	if($tmpFile != '')
	    $this->addJournalEcritureFile($data,$tmpFile);
	return $result;
    }

    /**
     * Méthode qui supprime une entrée de la table journal_banque
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function addJournalEcritureFile($data,$tmpFile, $svncp = false) {
	$addCommitCmd = '';
	$fileName = $GLOBALS['zunoJournalBanque']['pieceFilePrefix'].$data[$this->sqlIdField].'.'.FileGetExtention($tmpFile);
	$Path = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['zunoJournalBanque']['pieceDir'];
	$action = (is_file($Path.$fileName)) ? 'modification' : 'ajout';
	if($svncp)
	    shell_exec('svn cp '.$tmpFile.' '.$Path.$fileName);
	else
	    shell_exec('cp '.$tmpFile.' '.$Path.$fileName);
	if($action == 'ajout') {
	    $message = escapeshellcmd("Ajout du justificatif de l\'écriture bancaire numero ".$data[$this->sqlIdField]." par ".$_SESSION['user']['id']);
	    if(!$svncp)
		shell_exec('svn add '.$Path.$fileName);
	}
	else {
	    $message = escapeshellcmd("Modification du justificatif de l\'écriture bancaire numero ".$data[$this->sqlIdField]." par ".$_SESSION['user']['id']);
	    if($data['file_jb'] != '' and $data['file_jb'] != $fileName) {
		shell_exec('svn rm --force '.$Path.$data['file_jb']);
		$addCommitCmd = ' '.$Path.$data['file_jb'];
	    }
	}
	$uri	= " -m \"".$message."\" ".$Path.$fileName.$addCommitCmd;
	$CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
	if ($GLOBALS['LOG']['DisplayDebug'])
	    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
	shell_exec($CmdBase.$uri);
	Logg::loggerInfo('JournalBanqueModel::addJournalEcritureFile() ~ '.$action.' du justificatif de l\'écriture bancaire '.$fileName,array($fileName,$message),__FILE__.'@'.__LINE__);
	return $fileName;
    }

    /**
     * Méthode qui met à jour une entrée de la table journal_banque
     * @param array $data Les données mises à jour
     * @param string $id L'id du journalBanque à mettre à jour
     * @return array Indique si la requête s'est bien exécutée
     */
    public function updateJournalEcriture($data, $id = null,$updateFile = true,$file = '') {
	$sql = $this->makeRequeteUpdate($this->sqlTableName,$this->sqlIdField, $id, $this->bindVarJournalBanqueHisto($data));
	Logg::loggerAlert('journalBanqueModel::updateJournalEcriture() ~ mise à jour d\'une écriture bancaire '.$id,$sql,__FILE__.'@'.__LINE__);
	$out = $this->process2();
	if($updateFile)
	    $this->updateJournalEcritureFile($data,$file);
	return $out;
    }

    /**
     * Méthode qui supprime une entrée de la table journal_banque
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function updateJournalEcritureFile($data,$tmpFile) {
	$fileName = $this->addJournalEcritureFile($data,$tmpFile);
	$this->makeRequeteUpdate($this->sqlTableName,$this->sqlIdField, $data[$this->sqlIdField], array('file_jb' => $fileName));
	$this->process2();
	return $fileName;
    }

    /**
     * Méthode qui supprime une entrée de la table journal_banque
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function deleteJournalEcriture($id = null,$deleteFile = true) {
	if($deleteFile)
	    $this->deleteJournalEcritureFile($id);
	$sql = $this->makeRequeteDelete($this->sqlTableName,array($this->sqlIdField => $id));
	Logg::loggerAlert('JournalBanqueModel::deleteJournalEcriture() ~ suppression du pont comptable '.$id,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui supprime une entrée de la table journal_banque
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function deleteJournalEcritureFile($id = null) {
	$fileData = $this->getJournalEcritureByID($id);
	$message = escapeshellcmd("Suppression de l'écriture bancaire numero ".$id." par ".$_SESSION['user']['id']);
	$Path = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['zunoJournalBanque']['pieceDir'];
	$fileName = $fileData['file_jb'];
	if(file_exists($Path.$fileName)) {
	    $CmdBase = "svn rm --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." ";
	    if ($GLOBALS['LOG']['DisplayDebug'])
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$Path.$fileName;
	    shell_exec($CmdBase.$Path.$fileName);
	    $uri	= " -m \"".$message."\" ".$Path.$fileName;
	    $CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
	    if ($GLOBALS['LOG']['DisplayDebug'])
		$GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
	    shell_exec($CmdBase.$uri);
	    Logg::loggerInfo('JournalBanqueModel::deleteJournalEcritureFile() ~ Suppression du justificatif de l\'écriture bancaire '.$fileName,array($fileName,$message),__FILE__.'@'.__LINE__);
	    return true;
	}
	else {
	    Logg::loggerError('JournalBanqueModel::deleteJournalEcritureFile() ~ Erreur lors de la suppression du justificatif de l\'écriture bancaire '.$fileName,array($fileName,$message),__FILE__.'@'.__LINE__);
	    return false;
	}
    }

    /**
     * Méthode qui récupère toutes les informations de la table journal_banque
     * @param string $id L'id du journalBanque dont on veut les infos
     * @return array Les informaitons du journalBanque
     */
    public function getJournalEcritureByID($id = "") {
	$result = $this->getDataFromID($id);
	if($result[0])
	    return $result[1][0];
	else return array();
    }

    /**
     * Méthode qui récupère toutes les informations de la table journal_banque
     * @param string $id L'id du journalBanque dont on veut les infos
     * @return array Les informaitons du journalBanque
     */
    public function getDataFromID($id = "") {
	$this->makeRequeteFree("SELECT * FROM $this->sqlTableName
		LEFT JOIN entreprise e ON e.id_ent = $this->sqlTableName.entreprise_jb
		LEFT JOIN facture f ON f.id_fact = $this->sqlTableName.facture_jb
		LEFT JOIN ref_modereglement rmr ON rmr.id_modereg = $this->sqlTableName.modereglement_jb
		WHERE $this->sqlIdField  = '$id'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les périodes de renouvellement de la base
     * @return array Les périodes de renouvellement
     */
    public function getAllBanques() {
	$this->makeRequeteFree("SELECT * from banque WHERE isExportablePontComptable_bq = '1' ORDER BY id_bq ASC ");
	$rs = $this->process2();
	$statutList = array();
	if($rs[0])
	    foreach($rs[1] as $k => $v)
		$statutList[$v['id_bq']] = $v['nom_bq'];
	return $statutList;
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributeJournalBanqueInDB($id_jb,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('libelle_jb', $newAttribList)) {
	    $newData['libelle_jb'] = $newAttribList['libelle_jb'];
	    $hasChange = true;
	}
	if ($id_jb != '' and $hasChange) {
	    $model = new JournalBanqueModel();
	    $sql = $model->updateJournalEcriture($newData,$id_jb);
	    Logg::loggerInfo('journalBanqueModel::changeAttributeJournalBanqueInDB() ~ changement d\'attribut d\'une écriture bancaire '.$id_jb,$newData,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('journalBanqueModel::changeAttributeJournalBanqueInDB() ~ impossible de changer les attributs d\'une écriture bancaire '.$id_jb,$newAttribList,__FILE__.'@'.__LINE__);
    }

    static function markDeleteJournalBanqueInDB($id_jb,$params = array()) {
	if ($id_jb != '') {
	    $model = new JournalBanqueModel();
	    $deleteFile = ($params['deleteFile'] == '1') ? true : false;
	    $result = $model->deleteJournalEcriture($id_jb,$deleteFile);
	    Logg::loggerInfo('journalBanqueModel::markDeleteJournalBanqueInDB() ~ Ecriture bancaire '.$id_jb.'supprimé',$result,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('journalBanqueModel::markDeleteJournalBanqueInDB() ~ impossible de supprimer l\'écriture bancaire '.$id_jb,$id_jb,__FILE__.'@'.__LINE__);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table journal_banque
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarJournalBanqueHisto($data) {
	return $this->bindVar($data,$this->availableFields);
    }

    public function insertFromFacture($id_fact) {
	$this->makeRequeteSelect("facture", "id_fact", $id_fact);
	$fact = $this->process2();
	if($fact[0]) {
	    $datas[$this->sqlIdField] = $this->getLastId()+1;
	    $datas['date_record_jb'] = strftime('%Y-%m-%d %H:%M:%S',time());
	    $datas['date_effet_jb'] = strftime('%Y-%m-%d %H:%M:%S',time());
	    $datas['banque_jb'] = '1';
	    $datas['modereglement_jb'] = $fact[1][0]['modereglement_fact'];
	    $datas['libelle_jb'] = $fact[1][0]['titre_fact'];
	    $datas['montant_jb'] = prepareNombreTraitement(prepareNombreAffichage($fact[1][0]['sommeHT_fact']*(1+($fact[1][0]['tauxTVA_fact']/100))));
	    $datas['commentaire_jb'] = $fact[1][0]['commentaire_fact'];
	    $datas['file_jb'] = "Piece-".$datas[$this->sqlIdField].'.'.FileGetExtention($fact[1][0]['file_fact']);
	    $datas['facture_jb'] = $fact[1][0]['id_fact'];
	    $datas['entreprise_jb'] = $fact[1][0]['entreprise_fact'];
	    if($fact[1][0]['type_fact'] != 'Facture')
		$datas['montant_jb'] = -1*$datas['montant_jb'];
	    $datas = $this->bindVarJournalBanqueHisto($datas);
	    $this->makeRequeteInsert($this->sqlTableName, $datas);
	    $rs =$this->process2();
	    if($rs[0] and $datas['file_jb'] != '') {
		$this->addJournalEcritureFile($datas, $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].$fact[1][0]['file_fact'], true);
	    }
	    return $rs;
	}
	else {
	    return $fact;
	}
    }

    public function insertFromFactureFournisseur($id_fact) {
	$this->makeRequeteSelect("facture_fournisseur", "id_factfourn", $id_fact);
	$fact = $this->process2();
	if($fact[0]) {
	    $datas[$this->sqlIdField] = $this->getLastId()+1;
	    $datas['date_record_jb'] = strftime('%Y-%m-%d %H:%M:%S',time());
	    $datas['date_effet_jb'] = strftime('%Y-%m-%d %H:%M:%S',time());
	    $datas['banque_jb'] = '1';
	    $datas['modereglement_jb'] = $fact[1][0]['modeReglement_factfourn'];
	    $datas['libelle_jb'] = $fact[1][0]['titre_factfourn'];
	    $datas['montant_jb'] = -1*$fact[1][0]['montantTTC_factfourn'];
	    $datas['commentaire_jb'] = $fact[1][0]['desc_factfourn'];
	    if($fact[1][0]['file_factfourn'] != '')
		$datas['file_jb'] = "Piece-".$datas[$this->sqlIdField].'.'.FileGetExtention($fact[1][0]['file_factfourn']);
	    $datas['facture_jb'] = $fact[1][0]['id_factfourn'];
	    $datas['entreprise_jb'] = $fact[1][0]['entreprise_factfourn'];
	    $datas = $this->bindVarJournalBanqueHisto($datas);
	    $this->makeRequeteInsert($this->sqlTableName, $datas);
	    $rs =$this->process2();
	    if($rs[0] and $datas['file_jb'] != '') {
		$this->addJournalEcritureFile($datas, $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.factureFournisseur'].$fact[1][0]['file_factfourn'], true);
	    }
	    return $rs;
	}
	else {
	    return $fact;
	}
    }

}
?>
