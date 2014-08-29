<?php
loadPlugin('ZModels/GeneralModel');
/**
 * Classe qui gère les connexions à la base de données
 * pour tout ce qui concerne les produits.
 *
 * @author Nicolas Mannocci
 * @version 1
 */
// class produitModel extends GeneralModel {
// Pour SXA la class produitView se trouve dans le plugin ZunoSxa
class produitModelOriginal extends GeneralModel {

    //Champs dispos de la table produit
    protected $availableFields;
    //Champs dispos de la table ref_prodfamille
    private $availableFieldsFamille;
    //Champs dispos de la table fournisseur
    private $availableFieldsFournisseur;
    //Champs dispos de la table produit_fournisseur
    private $availableFieldsProdFourn;

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'produit';
	$this->sqlIdField = 'id_prod';
	$this->availableFields = array(
		'id_prod','nom_prod','famille_prod','description_prod',
		'dureeRenouvellement_prod','prix_prod', 'stock_prod',
		'remisefournisseur_prod','bestsell_prod','stillAvailable_prod');

	$this->availableFieldsFamille = array(
		'id_prodfam', 'nom_prodfam', 'livrable', 'revente', 'treePathKey');
	$this->availableFieldsFournisseur = array(
		'id_fourn', 'entreprise_fourn', 'contactComm_fourn', 'ContactADV_fourn', 'contactFact_fourn',
		'BDCCannevas_fourn', 'remise_fourn', 'actif');
	$this->availableFieldsProdFourn = array(
		'produit_id', 'fournisseur_id', 'remiseF', 'prixF', 'actif');
	parent::__construct();
    }

    /**
     * Méthode qui recherche un produit dans la base
     * @param string $search Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @return array Le(s) résultat(s)
     */
    public function getDataForSearchProduit($search,$limit = 10, $from = 0) {
	$qTag = " LIKE '%".$search."%' ";
	if($limit != "ALL") {
	    $this->makeRequeteFree("SELECT id_prod, nom_prod, prix_prod, treePathKey, nom_prodfam, description_prod, stock_prod, "
		    ."(select prixF*(1-remiseF/100) as PFourn from produit_fournisseur where produit_id = id_prod order by PFourn ASC limit 0,1) as PF "
		    ." FROM produit "
		    ."LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod "
		    ."WHERE (nom_prod $qTag OR nom_prodfam $qTag OR treePathKey $qTag OR description_prod $qTag OR id_prod $qTag) AND stillAvailable_prod != '0' "
		    ."Order by nom_prod "
		    ."LIMIT $from , $limit");
	}
	else {
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter FROM produit "
		    ."LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod "
		    ."WHERE (nom_prod $qTag OR nom_prodfam $qTag OR treePathKey $qTag OR description_prod $qTag OR id_prod $qTag) AND stillAvailable_prod != '0' ");
	}
	return $this->process2();
    }

    /**
     * Méthode qui effectue une recherche de produit par critères
     * @param string $search Ce que l'on cherche (inutilisé)
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultat(s) désiré
     * @param string $ordre Précise l'ordre de tri des résultats
     * @param array $conditions Les critères de recherche
     * @return array Les résultats
     */
    public function getDataForSearchProduitWeb($search = '', $from = 0, $limit = 10, $ordre = 'ORDER BY nom_prod', $conditions = array()) {
	$where = " where ";
	if(is_array($conditions)) {
	    foreach($conditions as $k => $v) {
		if($k == 'pxmin_prod')
		    $where .= " prix_prod >= '$v' AND ";
		elseif($k == 'pxmax_prod')
		    $where .= " prix_prod < '$v' AND ";
		elseif($k == 'fournisseur_id')
		    $where .= " fournisseur_id = '$v' AND ";
		else
		    $where .= "$k LIKE '%".$v."%' AND ";
	    }
	}
	$where .= " id_prod is not null ";
	if($limit != 'ALL')
	    $this->makeRequeteFree("SELECT * FROM produit "
		    ."LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod "
		    ."LEFT JOIN produit_fournisseur pf ON pf.produit_id = produit.id_prod "
		    ."$where GROUP BY id_prod $ordre "
		    ."LIMIT $from , $limit");
	else
	    $this->makeRequeteFree("SELECT COUNT(DISTINCT id_prod) AS counter FROM produit "
		    ."LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod "
		    ."LEFT JOIN produit_fournisseur pf ON pf.produit_id = produit.id_prod "
		    ."$where ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère l'historique des visites sur les fiches produits
     * @param int $limit Le nombre maximum de résultat(s)s souhaité(s)
     * @param string $type Précise si on veut un COUNT ou tous les résultats
     * @param string $order Précise l'ordre de tri des résultats
     * @return array Les résultats
     */
    public function getDataForHistoriqueVisit($limit ='10', $type = '', $order='ORDER BY id_prod DESC') {
	if(array_key_exists('historiqueVisite',$_SESSION) and
		array_key_exists('produit',$_SESSION['historiqueVisite']) and
		count($_SESSION['historiqueVisite']['produit']) > 0) {
	    $list = '';
	    foreach($_SESSION['historiqueVisite']['produit'] as $id)
		$list .= '\''.$id.'\', ';
	    $list = '('.substr($list,0,-2).')';
	}
	else $list = '(\'fakeNull\')';
	if($type == 'COUNT')
	    $select = 'SELECT COUNT(*) AS counter';
	else $select = 'SELECT *';
	$this->makeRequeteFree("$select FROM produit
			LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod
			WHERE id_prod IN $list $order LIMIT 0, $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui recherche des fournisseurs
     * @param string $search Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat(s) désiré(s)
     * @param int $from L'index du premier résultat
     * @return array Les résultats
     */
    public function getDataForSearchFournisseur($search, $limit = 10, $from = 0) {
	$qTag = " LIKE '%".$search."%' ";
	if($limit != 'ALL') {
	    $this->makeRequeteFree("SELECT nom_ent, nom_cont, prenom_cont, civ_cont, cp_ent, ville_ent, id_fourn "
		    ."From fournisseur f "
		    ."Left join entreprise e on e.id_ent =f.entreprise_fourn "
		    ."Left Join contact c on c.id_cont = f.contactComm_fourn "
		    ."WHERE nom_ent $qTag OR nom_cont $qTag OR id_fourn $qTag "
		    ."Order by nom_ent "
		    ."Limit $from, $limit");
	}
	else {
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter "
		    ."From fournisseur f "
		    ."Left join entreprise e on e.id_ent =f.entreprise_fourn "
		    ."Left Join contact c on c.id_cont = f.contactComm_fourn "
		    ."WHERE nom_ent $qTag OR nom_cont $qTag OR id_fourn $qTag ");
	}
	return $this->process2();
    }

    /**
     * Méthode de recherche de fournisseurs par critères
     * @param string $search Ce que l'on cherche (inutilisé)
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param string $ordre Précise l'ordre de tri
     * @param array $conditions Les critères de recherche
     * @return array Les résultats
     */
    public function getDataForSearchFournisseurWeb($search ='', $from = '0', $limit = 30, $ordre = '', $conditions = array()) {
	$where = " where ";
	if(is_array($conditions)) {
	    foreach($conditions as $k => $v) {
		$where .= "$k LIKE '%".$v."%' AND ";
	    }
	}
	$where .= " id_fourn is not null ";
	if($limit != 'ALL')
	    $this->makeRequeteFree("SELECT id_fourn, e.nom_ent as nom_ent, c.civ_cont as civ_cont1, c.prenom_cont as prenom_cont1, c.nom_cont as nom_cont1, e.cp_ent as cp_ent, e.ville_ent as ville_ent, " .
		    "c2.civ_cont as civ_cont2, c2.prenom_cont as prenom_cont2, c2.nom_cont as nom_cont2, ".
		    "c3.civ_cont as civ_cont3, c3.prenom_cont as prenom_cont3, c3.nom_cont as nom_cont3 ".
		    "FROM fournisseur ".
		    "LEFT JOIN entreprise e ON e.id_ent = entreprise_fourn " .
		    "LEFT JOIN contact c ON c.id_cont = contactComm_fourn " .
		    "LEFT JOIN contact c2 ON c2.id_cont = contactADV_fourn " .
		    "LEFT JOIN contact c3 ON c3.id_cont = contactFact_fourn " .
		    "$where $ordre ".
		    "Limit $from, $limit");
	else
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter FROM fournisseur " .
		    "LEFT JOIN entreprise e ON e.id_ent = entreprise_fourn " .
		    "LEFT JOIN contact c ON c.id_cont = contactComm_fourn " .
		    "LEFT JOIN contact c2 ON c2.id_cont = contactADV_fourn " .
		    "LEFT JOIN contact c3 ON c3.id_cont = contactFact_fourn " .
		    "$where");
	return $this->process2();

    }

    /**
     * Méthode qui insert un produit dans la base
     * @param array $data Les données à insérer
     * @param string $fourn Précise si on doit associer le produit à un/des fournisseurs
     * @return array Indique si la requête s'est bien exécutée
     */
    public function insertProduit($data, $fourn = 'non') {
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarProduit($data));
	Logg::loggerAlert('ProduitModel::insertProduit() ~ ajout du produit '.$data['id_prod'].' '.$data['nom_prod'],$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	if($result[0] && $fourn == 'oui') {
	    foreach($data['fourn'] as $key => $datas)
		$result = $this->insertProduitFournisseur($datas);
	}
	return $result;
    }

    /**
     * Méthode qui insert un fournisseur dans la base
     * @param array $data Les données à insérer
     * @param string $prod Précise s'il faut lier le fournisseur à un/des produit(s)
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insertFournisseur($data, $prod = 'non') {
	$sql = $this->makeRequeteInsert('fournisseur',$this->bindVarFournisseur($data));
	Logg::loggerAlert('ProduitModel::insertFournisseur() ~ ajout du fournisseur '.$data['id_fourn'],$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	if($result[0] && $prod == 'oui') {
	    foreach($data['prod'] as $key => $datas)
		$result = $this->insertProduitFournisseur($datas);
	}
	return $result;
    }

    /**
     * Méthode qui met à jour un produit dans la base
     * @param array $data Les données mises à jour
     * @param string $fourn Précise si on met à jour également des liens avec les fournisseurs
     * @param string $id L'id du produit à mettre à jour
     * @return array Indique si la requête s'est bien exécutée
     */
    public function updateProduit($data, $fourn = 'non', $id = null) {
	$sql = $this->makeRequeteUpdate($this->sqlTableName,"id_prod", $id, $this->bindVarProduit($data));
	Logg::loggerAlert('produitModel::updateProduit() ~ mise à jour du produit '.$id,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	if($result[0] && $fourn != 'non') {
	    $sql = $this->makeRequeteFree("DELETE FROM produit_fournisseur WHERE produit_id = '".$id."' ");
	    Logg::loggerAlert('produitModel::updateProduit() ~ suppression des fournisseurs pour le produit '.$id,$sql,__FILE__.'@'.__LINE__);
	    $result = $this->process2();
	    foreach($data['fourn'] as $key => $datas)
		$result = $this->insertProduitFournisseur($datas);
	}
	return $result;
    }

    /**
     * Méthode qui met à jour un fournisseur dans la base
     * @param array $data Les données mises à jour
     * @param string $prod Précise si on met à jour également des liens avec les produits
     * @param string $id L'id du fournisseur à mettre à jour
     * @return array Indique si la requête s'est bien exécutée
     */
    public function updateFournisseur($data, $prod = 'non', $id = null) {
	$sql = $this->makeRequeteUpdate('fournisseur', "id_fourn", $id, $this->bindVarFournisseur($data));
	Logg::loggerAlert('produitModel::updateFournisseur() ~ mise à jour du fournisseur '.$id,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	if($result[0] && $prod != 'non') {
	    $sql = $this->makeRequeteFree("DELETE FROM produit_fournisseur WHERE fournisseur_id = '".$id."'");
	    Logg::loggerAlert('produitModel::updateFournisseur() ~ suppression des produits pour le fournisseur '.$id,$sql,__FILE__.'@'.__LINE__);
	    $result = $this->process2();
	    foreach($data['prod'] as $key => $datas)
		$result = $this->insertProduitFournisseur($datas);
	}
	return $result;
    }

    /**
     * Méthode qui insert un lien entre un produit et un fournisseur
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insertProduitFournisseur($data = array()) {
	$sql = $this->makeRequeteInsert('produit_fournisseur',$this->bindVarProdFourn($data));
	Logg::loggerAlert('ProduitModel::insertProduitFournisseur() ~ ajout du fournisseur '.$data['fournisseur_id'].' pour le produit '.$data['produit_id'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui met à jour un lien produit - fournisseur
     * @param array $data Les données à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function updateProduitFournisseur($data = array()) {
	$sql = $this->makeRequeteUpdate('produit_fournisseur','produit_id',$data['produit_id'],$this->bindVarProdFourn($data), " AND fournisseur_id = '".$data['fournisseur_id']."'");
	Logg::loggerAlert('produitModel::updateProduitFournisseur() ~ mise à jour des conditions d\'achat  du produit '.$data['produit_id'].' avec le fournisseur '.$data['fournisseur_id'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui modifie le prixF et/ou la remiseF de plusieurs produits d'un même fournisseur
     * @param string $id_fourn L'id du fournisseur
     * @param float $prixF Le prix fournisseur
     * @param float $remiseF La remise fournisseur
     * @param array $idProds Les produits sur lesquels appliquer le prix et la remise précédents
     */
    public function updateProduitFournisseurMasse($id_fourn, $prixF, $remiseF, $idProds) {
	if($prixF != '')
	    $data['prixF'] = prepareNombreTraitement($prixF);
	if($remiseF != '')
	    $data['remiseF'] = prepareNombreTraitement($remiseF);
	if(is_array($idProds)) {
	    foreach($idProds as $v) {
		$sql .= $this->makeRequeteUpdate('produit_fournisseur','produit_id',$v,$data, " AND fournisseur_id = '".$id_fourn."'")."\n";
		$this->process2();
	    }
	    Logg::loggerAlert('produitModel::updateProduitFournisseurMasse() ~ mise à jour des conditions d\'achat  avec le fournisseur '.$id_fourn.' pour '.count($idProds).' produits',$sql,__FILE__.'@'.__LINE__);
	}
    }

    /**
     * Méthode qui supprime un lien entre un produit et un fournisseur
     * @param string $fourn L'id du fournisseur
     * @param string $id L'id du produit
     * @return array Indique si la requête s'est bien déroulée
     */
    public function supprimerProduitFournisseur($fourn = '', $id = '') {
	$sql = $this->makeRequeteFree("DELETE FROM produit_fournisseur WHERE fournisseur_id = '".$fourn."' AND produit_id = '".$id."' ");
	Logg::loggerAlert('ProduitModel::supprimerProduitFournisseur() ~ suppression des conditions d\'achat du produit '.$id.' par le fournisseur '.$fourn,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui désactive un lien produit - fournisseur
     * @param string $fourn L'id du fournisseur
     * @param string $id L'id du produit
     * @return array Indique si la requête s'est bien déroulée
     */
    public function enleverProduitFournisseur($fourn = '', $id = '') {
	$sql = $this->makeRequeteFree("UPDATE produit_fournisseur set actif = '0' WHERE fournisseur_id = '".$fourn."' AND produit_id = '".$id."' ");
	Logg::loggerAlert('ProduitModel::enleverProduitFournisseur() ~ désactivation des conditions d\'achat du produit '.$id.' par le fournisseur '.$fourn,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui désactive tous les produits d'un fournisseur
     * @param string $fourn L'id du fournisseur
     * @return array Indique si la requête s'est bien exécutée
     */
    public function desactiverProduitFournisseur($fourn = '') {
	$sql = $this->makeRequeteFree("UPDATE produit_fournisseur set actif = '0' WHERE fournisseur_id = '".$fourn."' ");
	Logg::loggerAlert('ProduitModel::desactiverProduitFournisseur() ~ désactivation de toutes les conditions d\'achat avec le fournisseur '.$fourn,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui supprime un fournisseur de la base
     * @param string $fourn L'id du fournisseur à supprimer
     * @return array Indique si la requête s'est bien exécutée
     */
    public function supprimerFournisseur($fourn = '') {
	$sql = $this->makeRequeteFree("DELETE FROM produit_fournisseur WHERE fournisseur_id = '".$fourn."' ");
	Logg::loggerAlert('ProduitModel::supprimerFournisseur() ~ suppression des conditions d\'achat avec le fournisseur '.$fourn,$sql,__FILE__.'@'.__LINE__);
	$this->process2();
	$sql = $this->makeRequeteFree("DELETE FROM fournisseur WHERE id_fourn = '".$fourn."' ");
	Logg::loggerAlert('ProduitModel::supprimerFournisseur() ~ suppression du fournisseur '.$fourn,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations d'un produit
     * @param string $id L'id du produit dont on veut les infos
     * @return array Les informaitons du produit
     */
    public function getProduitByID($id = "") {
	$this->makeRequeteFree("SELECT *,
					      (SELECT COUNT(id_produit) FROM facture_produit fp WHERE fp.id_produit = '".$id."') AS nbreFacture, 
					      (SELECT COUNT(id_produit) FROM commande_produit cp WHERE cp.id_produit = '".$id."') AS nbreCommande, 
					      (SELECT COUNT(id_produit) FROM devis_produit dp WHERE dp.id_produit = '".$id."') AS nbreDevis
					      FROM produit p LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam =p.famille_prod  WHERE p.id_prod = '".$id."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations d'un fournisseur
     * @param string $id L'id du fournisseur
     * @return array Les informations du fournisseur
     */
    public function getFournisseurByID($id = "") {
	$this->makeRequeteFree("SELECT * "
		."From fournisseur f "
		."Left join entreprise e on e.id_ent =f.entreprise_fourn "
		."Left Join contact c on c.id_cont = f.contactComm_fourn "
		."WHERE id_fourn = '$id' ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère tous les fournisseurs actifs d'un produit
     * @param string $id L'id du produit
     * @return array Les fournisseurs du produit
     */
    public function getFournisseurByProduitID($id = "") {
	$this->makeRequeteFree("Select * from produit_fournisseur pf LEFT JOIN fournisseur f ON f.id_fourn = pf.fournisseur_id LEFT JOIN entreprise e ON e.id_ent = f.entreprise_fourn where pf.produit_id = '".$id."' AND pf.actif = '1' ORDER BY pf.actif DESC, f.id_fourn ASC ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les anciens fournisseurs d'un produit
     * @param string $id L'id du produit
     * @return array Les fournisseurs désactivés du produit
     */
    public function getExFournisseurByProduitID($id = "") {
	$this->makeRequeteFree("Select * from produit_fournisseur pf LEFT JOIN fournisseur f ON f.id_fourn = pf.fournisseur_id LEFT JOIN entreprise e ON e.id_ent = f.entreprise_fourn where pf.produit_id = '".$id."' AND pf.actif = '0' ORDER BY pf.actif DESC, f.id_fourn ASC ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le nombre total de produits liés à un fournisseur.
     * @param string $id L'id d'un fournisseur
     * @return array Le nombre de produits liés au fournisseur
     */
    public function getProduitByFournisseurID($id = "") {
	$this->makeRequeteFree("SELECT COUNT(*) AS counter FROM produit_fournisseur pf WHERE pf.fournisseur_id = '".$id."' ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les produits liés à un fournisseur
     * @param string $id L'id du fournisseur
     * @return array Les produits du fournisseur
     */
    public function getProduitsByFournisseurID($id = "") {
	$this->makeRequeteFree("Select * from produit_fournisseur  left join produit on id_prod = produit_id left join ref_prodfamille on id_prodfam = famille_prod where fournisseur_id = '".$id."' ");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
	else
	    return $result;
    }

    /**
     * Méthode qui liste tous les fournisseurs
     * @param string $tous Permet de préciser si on ne veut que les fournisseurs actifs
     * @return  array La liste des fournisseurs
     */
    public function getAllFournisseur($tous = 'non') {
	$restriction = ($tous == 'non') ? "where f.actif = '1'" : "";
	$this->makeRequeteFree("Select * from fournisseur f left join entreprise e on e.id_ent = f.entreprise_fourn left join produit_fournisseur pf ON pf.fournisseur_id = f.id_fourn $restriction GROUP BY id_fourn ");
	return $this->process2();
    }

    /**
     * Méthode qui recherche parmi les familles de produit
     * @param string $search Ce que l'on cherche
     * @return array Les résultats
     */
    public function getDataForFamille($search) {
	$qTag = " LIKE '".$search."%' ";
	$this->makeRequeteFree("SELECT * from ref_prodfamille where id_prodfam $qTag OR nom_prodfam $qTag OR treePathKey $qTag ORDER BY nom_prodfam LIMIT 0, 5 ");
	return $this->process2();
    }

    /**
     * Méthode qui insert une famille dans la base
     * @param string $nom_prodfam Le nom de la famille à insérer
     * @return string Le nombre de famille présent dans la table avant l'insertion
     */
    public function insertFamille($nom_prodfam) {
	$this->makeRequeteFree("SELECT COUNT(*) AS counter FROM ref_prodfamille ");
	$total = $this->process2();
	$sql = $this->makeRequeteInsert('ref_prodfamille', array('nom_prodfam' => $nom_prodfam, 'treePathKey' => $GLOBALS['zunoProduit']['defautNewFamilleCode']));
	Logg::loggerAlert('ProduitModel::insertFamille() ~ ajout de la famille produit '.$nom_prodfam,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $total['1']['0']['counter'];
    }

    /**
     * Méthode qui retourne le dernier ID de famille
     * @return int l'Id de la dernière famille
     */
    public function getLastFamille() {
	$this->makeRequeteFree("SELECT id_prodfam FROM ref_prodfamille ORDER BY id_prodfam DESC LIMIT 0, 1");
	$o = $this->process2();
	return ($o[0]) ? $o[1][0]['id_prodfam'] : '0';
    }

    /**
     * Méthode qui récupère la liste des entreprises déclarées comme potentiels fournisseurs
     * @return array La liste des entreprises
     */
    public function getEntrepriseFournisseur() {
	$this->makeRequeteFree("SELECT id_ent, nom_ent, cp_ent, ville_ent from entreprise left join fournisseur on entreprise.id_ent = fournisseur.entreprise_fourn where entreprise.type_ent = '6' and fournisseur.entreprise_fourn is null ");
	return $this->process2();
    }

    /**
     * Méthode qui met à jour le stock d'un produit
     * @param string $id L'id du produit à mettre à jour
     * @param int $stock Le nouveau stock
     * @return array Indique si la requête s'est bien déroulée
     */
    public function updateStock($id, $stock) {
	$sql = $this->makeRequeteUpdate('produit', 'id_prod', $id, array('stock_prod'=> $stock));
	Logg::loggerAlert('ProduitModel::updateStock() ~ mise à jour du stock pour le produit '.$id,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui liste les nouveau fournisseurs potentiel d'un produit donné
     * @param string $id Le produit pour lequel on cherche un nouveau fournisseur
     * @return array La liste des fournisseurs potentiels
     */
    public function getPotentielFournisseur($id) {
	$this->makeRequeteFree("SELECT id_fourn, produit_id, nom_ent, remise_fourn
				FROM fournisseur
				LEFT JOIN entreprise e ON e.id_ent = entreprise_fourn
				LEFT JOIN produit_fournisseur pf ON pf.fournisseur_id = id_fourn AND pf.produit_id = '$id'
				WHERE  pf.produit_id IS NULL");
	$rs = $this->process2();
	if($rs[0]) {
	    $rs=$rs[1];
	    if(is_array($rs))
		foreach($rs as $k => $v) {
		    $countryList['select'][$v['id_fourn']] = '['.$v['id_fourn'].'] '.$v['nom_ent'];
		    $countryList['remise'][$v['id_fourn']] = $v['remise_fourn'];
		}
	    return $countryList;
	}
    }

    /**
     * Méthode qui récupère la liste des contacts potentiels d'un fournisseur
     * @param string $id L'id du fournisseur
     * @return array La liste des contacts potentiels
     */
    public function getContactsPotentiels($id) {
	$this->makeRequeteFree("select id_cont, civ_cont, prenom_cont, nom_cont from contact where entreprise_cont = (select entreprise_fourn from fournisseur where id_fourn = '$id')");
	$rs = $this->process2();
	if($rs[0]) {
	    $rs=$rs[1];
	    if(is_array($rs))
		foreach($rs as $k => $v)
		    $countryList[$v['id_cont']] = $v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'];
	    return $countryList;
	}
    }

    /**
     * Méthode qui récupère la liste des entreprises potentiellement fournisseur ne l'étant pas déjà
     * @return array La liste des entreprises
     */
    public function getEntreprisePotentiels() {
	$this->makeRequeteFree("select id_ent, nom_ent, cp_ent from entreprise left join fournisseur f on f.entreprise_fourn = entreprise.id_ent where type_ent = 5 and id_fourn is null");
	$rs = $this->process2();
	if($rs[0]) {
	    $rs=$rs[1];
	    if(is_array($rs))
		foreach($rs as $k => $v)
		    $countryList[$v['id_ent']] = '['.$v['cp_ent'].'] '.$v['nom_ent'];
	    return $countryList;
	}
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributeProduitInDB($id_prod,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('nom_prod', $newAttribList) or
		array_key_exists('famille_prod', $newAttribList) or
		array_key_exists('description_prod', $newAttribList) or
		array_key_exists('dureeRenouvellement_prod', $newAttribList) or
		array_key_exists('prix_prod', $newAttribList) or
		array_key_exists('stock_prod', $newAttribList) or
		array_key_exists('remisefournisseur_prod', $newAttribList) or
		array_key_exists('bestsell_prod', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_prod != '' and $hasChange) {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $bddtmp->makeRequeteSelect('produit','id_prod',$id_prod);
	    $prod = $bddtmp->process();
	    $prod = $prod[0];
	    if($newAttribList['nom_prod'] != '')
		$newData['nom_prod'] = $newAttribList['nom_prod'];
	    if($newAttribList['famille_prod'] != '')
		$newData['famille_prod'] = $newAttribList['famille_prod'];
	    if($newAttribList['description_prod'] != '')
		$newData['description_prod'] = $newAttribList['description_prod'];
	    if($newAttribList['dureeRenouvellement_prod'] != '')
		$newData['dureeRenouvellement_prod'] = $newAttribList['dureeRenouvellement_prod'];
	    if($newAttribList['prix_prod'] != '')
		$newData['prix_prod'] = $newAttribList['prix_prod'];
	    if($newAttribList['stock_prod'] != '')
		$newData['stock_prod'] = $newAttribList['stock_prod'];
	    if($newAttribList['remisefournisseur_prod'] != '')
		$newData['remisefournisseur_prod'] = $newAttribList['remisefournisseur_prod'];
	    if(trim($newAttribList['bestsell_prod']) != '')
		$newData['bestsell_prod'] = $newAttribList['bestsell_prod'];
	    $sql = $bddtmp->makeRequeteUpdate('produit','id_prod',$id_prod,$newData);
	    $bddtmp->process();
	    Logg::loggerInfo('produitModel::changeAttributeProduitInDB() ~ changement d\'attribut du produit '.$id_prod,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('produitModel::changeAttributeProduitInDB() ~ impossible de changer les attributs du produit '.$id_prod,'',__FILE__.'@'.__LINE__);
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeFournisseurProduitProduitInDB($id_prod,$id_fourn,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('remiseF', $newAttribList) or
		array_key_exists('prixF', $newAttribList) or
		array_key_exists('actifF', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_prod != '' and $hasChange) {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $bddtmp->makeRequeteSelect('produit','id_prod',$id_prod);
	    $prod = $bddtmp->process();
	    $prod = $prod[0];
	    if($newAttribList['remiseF'] != '')
		$newData['remiseF'] = $newAttribList['remiseF'];
	    if($newAttribList['prixF'] != '')
		$newData['prixF'] = $newAttribList['prixF'];
	    if(trim($newAttribList['actifF']) != '')
		$newData['actif'] = $newAttribList['actifF'];
	    $sql = $bddtmp->makeRequeteUpdate('produit_fournisseur','produit_id',$id_prod,$newData," AND fournisseur_id = '".$id_fourn."'");
	    $bddtmp->process();
	    Logg::loggerInfo('produitModel::changeFournisseurProduitProduitInDB() ~ changement d\'attribut du produit '.$id_prod.' avec le fournisseur '.$id_fourn,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('produitModel::changeFournisseurProduitProduitInDB() ~ impossible de changer les attributs du produit '.$id_prod.' avec le fournisseur '.$id_fourn,'',__FILE__.'@'.__LINE__);
    }



    static function markDeleteProduitInDB($id_prod,$newAttribList = array()) {
	if ($id_prod != '') {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $sql = $bddtmp->makeRequeteDelete('produit',array('id_prod'=>$id_prod));
	    $bddtmp->process();
	    Logg::loggerInfo('produitModel::markDeleteProduitInDB() ~ Produit '.$id_prod.'supprimé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('produitModel::markDeleteProduitInDB() ~ impossible de supprimer le produit '.$id_prod,'',__FILE__.'@'.__LINE__);
    }

    static function markActivateProduitInDB($id_prod,$newAttribList = array()) {
	if ($id_prod != '') {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $bddtmp->makeRequeteSelect('produit','id_prod',$id_prod);
	    $prod = $bddtmp->process();
	    $prod = $prod[0];
	    $newData['stillAvailable_prod'] = '1';
	    $sql = $bddtmp->makeRequeteUpdate('produit','id_prod',$id_prod,$newData);
	    $bddtmp->process();
	    Logg::loggerInfo('produitModel::markActivateProduitInDB() ~ Produit '.$id_prod.' marqué comme activé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('produitModel::markActivateProduitInDB() ~ impossible de marquer comme activé le produit '.$id_prod,'',__FILE__.'@'.__LINE__);
    }

    static function markDesactivateProduitInDB($id_prod,$newAttribList = array()) {
	if ($id_prod != '') {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $bddtmp->makeRequeteSelect('produit','id_prod',$id_prod);
	    $prod = $bddtmp->process();
	    $prod = $prod[0];
	    $newData['stillAvailable_prod'] = '0';
	    $sql = $bddtmp->makeRequeteUpdate('produit','id_prod',$id_prod,$newData);
	    $bddtmp->process();
	    Logg::loggerInfo('produitModel::markDesactivateProduitInDB() ~ Produit '.$id_prod.' marqué comme désactivé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('produitModel::markDesactivateProduitInDB() ~ impossible de marquer comme désactivé le produit '.$id_prod,'',__FILE__.'@'.__LINE__);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table produit
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarProduit($data) {
	return $this->bindVar($data,$this->availableFields);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table ref_prodfamille
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFieldsFamille
     */
    public function bindVarFamille($data) {
	return $this->bindVar($data,$this->availableFieldsFamille);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table fournisseur
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFieldsFournisseur
     */
    public function bindVarFournisseur($data) {
	return $this->bindVar($data,$this->availableFieldsFournisseur);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table produit_fournisseur
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFieldsProdFourn
     */
    public function bindVarProdFourn($data) {
	return $this->bindVar($data,$this->availableFieldsProdFourn);
    }
}
?>
