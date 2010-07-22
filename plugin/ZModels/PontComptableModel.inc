<?php
loadPlugin('ZModels/GeneralModel');
/**
 * Classe qui gère les connexions à la base de données
 * pour tout ce qui concerne les pontComptables.
 *
 * @author Nicolas Mannocci
 * @version 1
 */
class pontComptableModel extends GeneralModel {

    //Champs dispos de la table pontcomptable_histo
    protected $availableFields;

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'pontcomptable_histo';
	$this->sqlIdField = 'id_pcth';
	$this->availableFields = array('id_pcth','nom_pcth','date_pcth','fichier_pcth','config_dateDebut_pcth','config_dateFin_pcth','config_statutFact_pcth','config_statutFactFourn_pcth','config_hasFactureClient_pcth','config_hasFactureFourn_pcth');
	parent::__construct();
    }



    /**
     * Méthode qui effectue une recherche sur la table pontcomptable_histo
     * @param string $search Ce que l'on cherche (inutilisé)
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultat(s) désiré
     * @param string $ordre Précise l'ordre de tri des résultats
     * @param array $conditions Les critères de recherche
     * @return array Les résultats
     */
    public function getDataForSearchPontComptableHistoWeb($search = '', $from = 0, $limit = 10, $ordre = 'ORDER BY nom_pcth', $conditions = array()) {
	$where = " WHERE ";
	if(is_array($conditions)) {
	    foreach($conditions as $k => $v) {
		if($k == 'dateStart_pcth')
		    $where .= " date_pcth >= '$v' AND ";
		elseif($k == 'dateEnd_pcth')
		    $where .= " date_pcth <= '$v' AND ";
		elseif($k == 'nom_pcth')
		    $where .= "nom_pcth LIKE '%".$v."%' AND ";
		else $where .= "$k LIKE '%".$v."%' AND ";
	    }
	}
	$where .= " id_pcth is not null ";
	if($limit != 'ALL')
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName $where $ordre LIMIT $from , $limit");
	else $this->makeRequeteFree("SELECT COUNT(distinct id_pcth) AS counter FROM $this->sqlTableName $where");
	return $this->process2();
    }

    /**
     * Méthode qui insert une entrée de la table pontcomptable_histo
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function insertPontComptableHisto($data, $withFile = true) {
	$this->makeRequeteFree("SELECT $this->sqlIdField FROM $this->sqlTableName ORDER BY $this->sqlIdField DESC LIMIT 0 , 1");
	$last = $this->process2();
	$data[$this->sqlIdField] = $last[1][0][$this->sqlIdField]+1;
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarPontComptableHisto($data));
	Logg::loggerAlert('PontComptableModel::insertPontComptable() ~ ajout du fichier d\'export comptable '.$data['nom_pcth'],$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	if($withFile)
	    $this->addPontComptableHistoFile($data);
	return $result;
    }

    /**
     * Méthode qui supprime une entrée de la table pontcomptable_histo
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function addPontComptableHistoFile($data) {
	$filePrefix =$GLOBALS['zunoPontComptable']['filePrefix'].$data[$this->sqlIdField];
	$tmpFile = $this->genererPontComptableHistoTmpFileWithData($data,$filePrefix);
	$tmpFilePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$tmpFile;
	$fileName = $filePrefix.$GLOBALS['zunoPontComptable']['fileExt'];
	$message = escapeshellcmd("Ajout du fichier d'export comptable numero ".$data[$this->sqlIdField]." par ".$_SESSION['user']['id']);
	$Path = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['zunoPontComptable']['histoDir'];

	shell_exec('cp '.$tmpFilePath.' '.$Path.$fileName);
	$CmdBase = "svn add --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." ";
	if ($GLOBALS['LOG']['DisplayDebug'])
	    $GLOBALS['LogSVNProcess'][] = $CmdBase.$Path.$fileName;
	shell_exec($CmdBase.$Path.$fileName);
	$uri	= " -m \"".$message."\" ".$Path.$fileName;
	$CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
	if ($GLOBALS['LOG']['DisplayDebug'])
	    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
	shell_exec($CmdBase.$uri);
	Logg::loggerInfo('PontComptableModel::addPontComptableHistoFile() ~ Ajout du fichier d\'export comptable '.$fileName,array($fileName,$message),__FILE__.'@'.__LINE__);
	$this->updatePontComptableHisto(array('fichier_pcth' => $fileName),$data[$this->sqlIdField],false);
	return true;
    }

    /**
     * Méthode qui met à jour une entrée de la table pontcomptable_histo
     * @param array $data Les données mises à jour
     * @param string $id L'id du pontComptable à mettre à jour
     * @return array Indique si la requête s'est bien exécutée
     */
    public function updatePontComptableHisto($data, $id = null,$updateFile = true) {
	$sql = $this->makeRequeteUpdate($this->sqlTableName,$this->sqlIdField, $id, $this->bindVarPontComptableHisto($data));
	Logg::loggerAlert('pontComptableModel::updatePontComptableHisto() ~ mise à jour du fichier d\'export comptable '.$id,$sql,__FILE__.'@'.__LINE__);
	$out = $this->process2();
	if($updateFile)
	    $this->updatePontComptableHistoFile($data);
	return $out;
    }

    /**
     * Méthode qui supprime une entrée de la table pontcomptable_histo
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function updatePontComptableHistoFile($data) {
	$tmpFile = $this->genererPontComptableHistoTmpFileWithData($data);
	$tmpFilePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$tmpFile;
	$fileName =$data[$this->sqlIdField].'-'.FileCleanFileName(str_replace(':','',str_replace('-','',$data['date_pcth']))).$GLOBALS['zunoPontComptable']['fileExt'];

	$message = escapeshellcmd("Modification du fichier d'export comptable numero ".$data[$this->sqlIdField]." par ".$_SESSION['user']['id']);
	$Path = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['zunoPontComptable']['histoDir'];

	shell_exec('cp '.$tmpFilePath.' '.$Path.$fileName);
	$uri	= " -m \"".$message."\" ".$Path.$fileName;
	$CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
	if ($GLOBALS['LOG']['DisplayDebug'])
	    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
	shell_exec($CmdBase.$uri);
	Logg::loggerInfo('PontComptableModel::updatePontComptableHistoFile() ~ Modification du fichier d\'export comptable '.$fileName,array($fileName,$message),__FILE__.'@'.__LINE__);
	return true;
    }

    /**
     * Méthode qui supprime une entrée de la table pontcomptable_histo
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function deletePontComptableHisto($id = null,$deleteFile = true) {
	if($deleteFile)
	    $this->deletePontComptableHistoFile($id);
	$sql = $this->makeRequeteDelete($this->sqlTableName,array($this->sqlIdField => $id));
	Logg::loggerAlert('PontComptableModel::deletePontComptableHisto() ~ suppression du pont comptable '.$id,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui supprime une entrée de la table pontcomptable_histo
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function deletePontComptableHistoFile($id = null) {
	$fileData = $this->getPontComptableHistoByID($id);
	$message = escapeshellcmd("Suppression du fichier d'export comptable numero ".$id." par ".$_SESSION['user']['id']);
	$Path = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['zunoPontComptable']['histoDir'];
	$fileName = $fileData['fichier_pcth'];
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
	    Logg::loggerInfo('PontComptableModel::deletePontComptableHistoFile() ~ Suppression du fichier d\'export comptable '.$fileName,array($fileName,$message),__FILE__.'@'.__LINE__);
	    return true;
	}
	else {
	    Logg::loggerError('PontComptableModel::deletePontComptableHistoFile() ~ Erreur lors de la suppression du fichier d\'export comptable '.$fileName,array($fileName,$message),__FILE__.'@'.__LINE__);
	    return false;
	}
    }

    /**
     * Méthode qui supprime une entrée de la table pontcomptable_histo
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function genererPontComptableHistoTmpFileWithData($data,$fileName = "") {
	$Path = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	if($fileName == "")
	    $fileName = substr($GLOBALS['zunoPontComptable']['filePrefix'],0,-1);
	$sageObj = new SageCsvGenerator($fileName);
	if($data['config_hasFactureClient_pcth'] == '1') {
	    $infoBrutFact = $this->getFactureClientData($data);
	    if($infoBrutFact[0]) {
		$factures =array();
		foreach ($infoBrutFact[1] as $row)
		    $factures[$row['id_fact']][] = $row;
		unset($infoBrutFact);
		foreach($factures as $idFact => $produits) {
		    $infoFacture = $produits[0];
		    $tauxTVA = $infoFacture['tauxTVA_fact'];
		    if($infoFacture['dateenvoi_fact'] == '')
			$infoFacture['dateenvoi_fact'] =$infoFacture['daterecord_fact'];
		    $dateFacture = DateUniv2Human( $infoFacture['dateenvoi_fact'], 'simpleLong');
		    $numeroFacture = (($infoFacture['type_fact'] == 'Avoir') ? 'A' : 'F').$infoFacture['id_fact'];
		    $numeroCompteTiers = $infoFacture['id_ent'];
		    $libelle = strtoupper(FileCleanFileName($infoFacture['nom_ent'],"TOLOWER"));

		    $totalHT = 0;
		    $produitArray = array();
		    foreach($produits as $produit) {
			$totalRow = round(($produit['quantite']*$produit['prix'])-((($produit['quantite']*$produit['prix'])*$produit['remise'])/100),2);
			if($produit['compteComptable_prod'] == '')
			    $produitArray[$GLOBALS['zunoPontComptable']['FCCompteProduitDefaut']] += $totalRow;
			else $produitArray[$produit['compteComptable_prod']] += $totalRow;
			$totalHT += $totalRow;
		    }
		    // ajout des lignes de credit produit
		    foreach($produitArray as $compte => $montant)
			$sageObj->quickAddFCLigneCredit($dateFacture,$numeroFacture,$compte,$montant,$libelle);
		    $totalTVA = round(($totalHT*$tauxTVA/100),2);
		    // ajout de la ligne de credit TVA
		    $sageObj->quickAddFCLigneCredit($dateFacture,$numeroFacture,$GLOBALS['zunoPontComptable']['FCCompteTVADefaut'],$totalTVA,$libelle);
		    // ajout de la ligne de debit
		    $sageObj->quickAddFCLigneDebit($dateFacture,$numeroFacture,$GLOBALS['zunoPontComptable']['FCCompteDebitDefaut'],($totalTVA+$totalHT),$libelle,$numeroCompteTiers);
		}
	    }
	}
	if($data['config_hasFactureFourn_pcth'] == '1') {
	    $infoBrutFact = $this->getFactureFournData($data);
	    if($infoBrutFact[0]) {
		foreach($infoBrutFact[1] as $idFact => $infoFacture) {
		    $idFact = $infoFacture['id_factfourn'];
		    $tauxTVA = $infoFacture['tauxTVA_factfourn'];
		    if($infoFacture['dateFact_factfourn'] == '')
			$infoFacture['dateFact_factfourn'] =$infoFacture['datePaye_factfourn'];
		    $dateFacture = DateUniv2Human( $infoFacture['dateFact_factfourn'], 'simpleLong');
		    $numeroCompteTiers = $infoFacture['id_ent'];
		    $libelle = strtoupper(FileCleanFileName($infoFacture['nom_ent'],"TOLOWER"));

		    $totalHT = $infoFacture['montantTTC_factfourn']*(1-($tauxTVA/100));
		    $sageObj->quickAddFFLigne($dateFacture,$idFact,$GLOBALS['zunoPontComptable']['FFCompteProduitDefaut'],round($totalHT,2),'D',$numeroCompteTiers,$libelle);
		    $totalTVA = round(($totalHT*$tauxTVA/100),2);
		    // ajout de la ligne de credit TVA
		    $sageObj->quickAddFFLigne($dateFacture,$idFact,$GLOBALS['zunoPontComptable']['FFCompteTVADefaut'],$totalTVA,'D',$numeroCompteTiers,$libelle);
		    // ajout de la ligne de debit
		    $sageObj->quickAddFFLigne($dateFacture,$idFact,$GLOBALS['zunoPontComptable']['FFCompteDebitDefaut'],round($totalTVA+$totalHT,2),'C',$numeroCompteTiers,$libelle);
		}
	    }
	}
	$sageObj->saveFile();
	return $sageObj->getFileFullName();
    }

    /**
     * Méthode qui récupère toutes les informations de la table pontcomptable_histo
     * @param string $id L'id du pontComptable dont on veut les infos
     * @return array Les informaitons du pontComptable
     */
    public function getPontComptableHistoByID($id = "") {
	$result = $this->getDataFromID($id);
	if($result[0])
	    return $result[1][0];
	else return array();
    }

    /**
     * Méthode qui récupère toutes les informations de la table pontcomptable_histo
     * @param string $id L'id du pontComptable dont on veut les infos
     * @return array Les informaitons du pontComptable
     */
    public function getDataFromID($id = "") {
	$this->makeRequeteFree("SELECT * FROM $this->sqlTableName WHERE $this->sqlIdField  = '$id'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations de la table pontcomptable_histo
     * @param string $id L'id du pontComptable dont on veut les infos
     * @return array Les informaitons du pontComptable
     */
    public function getFactureClientData($data) {
	if(is_array($data)) {
	    if($data['config_dateDebut_pcth'] != '')
		$where .= " daterecord_fact >= '".$data['config_dateDebut_pcth']."' AND ";
	    if($data['config_dateFin_pcth'] != '')
		$where .= " daterecord_fact <= '".$data['config_dateFin_pcth']."' AND ";
	    if($data['config_statutFact_pcth'] != '')
		 $where .= " status_fact IN (".$data['config_statutFact_pcth'].") AND ";
	    else $where .= " status_fact = '4' ";
	}
	else $where .= " status_fact = '4' ";
	$where .= " id_fact IS NOT NULL ";
	$this->makeRequeteFree("SELECT facture.*, facture_produit.*, produit.*, affaire.id_aff, entreprise.id_ent, entreprise.nom_ent
		FROM facture
		LEFT JOIN entreprise ON entreprise.id_ent = facture.entreprise_fact
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		, facture_produit
	 	LEFT JOIN produit ON facture_produit.id_produit = produit.id_prod
	 	WHERE facture_produit.id_facture = facture.id_fact
		AND ".$where);
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations de la table pontcomptable_histo
     * @param string $id L'id du pontComptable dont on veut les infos
     * @return array Les informaitons du pontComptable
     */
    public function getFactureFournData($data) {
	if(is_array($data)) {
	    if($data['config_dateDebut_pcth'] != '')
		$where .= " dateFact_factfourn >= '".$data['config_dateDebut_pcth']."' AND ";
	    if($data['config_dateFin_pcth'] != '')
		$where .= " dateFact_factfourn <= '".$data['config_dateFin_pcth']."' AND ";
	    if($data['config_statutFactFourn_pcth'] != '')
		 $where .= " status_factfourn IN (".$data['config_statutFactFourn_pcth'].") AND ";
	    else $where .= " status_factfourn = '2' ";
	}
	else $where .= " status_factfourn = '2' ";
	$where .= " id_factfourn IS NOT NULL ";
	$this->makeRequeteFree("SELECT facture_fournisseur.*, entreprise.id_ent, entreprise.nom_ent
		FROM facture_fournisseur
		LEFT JOIN entreprise ON entreprise.id_ent = facture_fournisseur.entreprise_factfourn
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		WHERE ".$where);
	return $this->process2();
    }

    /**
     * Méthode qui récupère les périodes de renouvellement de la base
     * @return array Les périodes de renouvellement
     */
    public function getAllFactureClientStatut() {
	$this->makeRequeteFree("SELECT * from ref_statusfacture WHERE pontComptableExportable_stfact = '1' ORDER BY id_stfact ASC ");
	$rs = $this->process2();
	$statutList = array();
	if($rs[0])
	    foreach($rs[1] as $k => $v)
		$statutList[$v['id_stfact']] = $v['nom_stfact'];
	return $statutList;
    }

    /**
     * Méthode qui récupère les périodes de renouvellement de la base
     * @return array Les périodes de renouvellement
     */
    public function getAllFactureFournStatut() {
	$this->makeRequeteFree("SELECT * from ref_statusfacturefournisseur WHERE pontComptableExportable_stfactfourn = '1' ORDER BY id_stfactfourn ASC ");
	$rs = $this->process2();
	$statutList = array();
	if($rs[0])
	    foreach($rs[1] as $k => $v)
		$statutList[$v['id_stfactfourn']] = $v['nom_stfactfourn'];
	return $statutList;
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributePontComptableInDB($id_pcth,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('nom_pcth', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_pcth != '' and $hasChange) {
	    $model = new PontComptableModel();
	    if($newAttribList['nom_pcth'] != '')
		$newData['nom_pcth'] = $newAttribList['nom_pcth'];
	    $sql = $model->updatePontComptableHisto($newData,$id_pcth);
	    Logg::loggerInfo('pontComptableModel::changeAttributePontComptableInDB() ~ changement d\'attribut du fichier d\'export comptable '.$id_pcth,$newData,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('pontComptableModel::changeAttributePontComptableInDB() ~ impossible de changer les attributs du fichier d\'export comptable '.$id_pcth,$newAttribList,__FILE__.'@'.__LINE__);
    }

    static function markDeletePontComptableInDB($id_pcth,$params = array()) {
	if ($id_pcth != '') {
	    $model = new PontComptableModel();
	    $deleteFile = ($params['deleteFile'] == '1') ? true : false;
	    $result = $model->deletePontComptableHisto($id_pcth,$deleteFile);
	    Logg::loggerInfo('pontComptableModel::markDeletePontComptableInDB() ~ Fichier d\'export comptable '.$id_pcth.'supprimé',$result,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('pontComptableModel::markDeletePontComptableInDB() ~ impossible de supprimer le fichier d\'export comptable '.$id_pcth,$id_pcth,__FILE__.'@'.__LINE__);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table pontcomptable_histo
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarPontComptableHisto($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}
?>
