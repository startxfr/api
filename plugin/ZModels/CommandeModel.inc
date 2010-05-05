<?php
include_once('GeneralModel.inc');
/**
 * Classe gérant toutes les requètes SQL en rapport avec les commande.
 * @author Nicolas Mannocci
 * @version 1.0
 */
class commandeModel extends GeneralModel {

    private $availableFieldsProduit;


    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'commande';
	$this->sqlIdField = 'id_cmd';
	$this->availableFields = array(
		'id_cmd','entreprise_cmd','contact_cmd','devis_cmd',
		'sommeHT_cmd','sommeFHT_cmd','modereglement_cmd','condireglement_cmd','BDCclient_cmd','titre_cmd',
		'datemodif_cmd','daterecord_cmd','status_cmd', 'prixF',
		'nomdelivery_cmd','adressedelivery_cmd','adresse1delivery_cmd',
		'villedelivery_cmd','cpdelivery_cmd','paysdelivery_cmd',
		'maildelivery_cmd','complement_delivery_cmd',
		'commercial_cmd','contact_achat_cmd', 'tva_cmd', 'commentaire_cmd');
	$this->availableFieldsProduit = array(
		'id_commande', 'id_produit', 'desc', 'quantite', 'quantite_cmd', 'remise',
		'prix', 'fournisseur', 'remiseF', 'prixF', 'comment', 'id');
	parent::__construct();
    }

    /**
     * Fonction de recherche de données à partir d'une donnée quelconque
     * @param string $search Ce qui est recherché
     * @param int $limit Le nombre maxi de résultat
     * @pâram int $from L'index du premier résultat
     * @param string $plus Des restrictions supplémentaires
     * @return array Le résultat
     */
    public function getDataForSearch($search,$limit = 10, $from = 0, $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	if($limit == 'ALL') {
	    $this->makeRequeteFree("SELECT COUNT(*)
		    FROM commande
		    LEFT JOIN entreprise ON entreprise.id_ent = commande.entreprise_cmd
		    LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		    LEFT JOIN contact ON contact.id_cont = commande.contact_cmd
		    WHERE (nom_ent $qTag OR ville_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag
		    OR mail_cont $qTag OR id_cmd $qTag OR titre_cmd $qTag OR devis_cmd $qTag )" .$plus);
	}
	else {
	    $this->makeRequeteFree("SELECT titre_cmd, nom_ent, type_ent, nom_cont, prenom_cont, civ_cont, id_cmd
		    FROM commande
		    LEFT JOIN entreprise ON entreprise.id_ent = commande.entreprise_cmd
		    LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		    LEFT JOIN contact ON contact.id_cont = commande.contact_cmd
		    WHERE (nom_ent $qTag OR ville_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag
		    OR mail_cont $qTag OR id_cmd $qTag OR titre_cmd $qTag OR devis_cmd $qTag ) $plus
		    ORDER BY id_cmd DESC
		    LIMIT $from , $limit");
	}
	return $this->process2();
    }

    /**
     * Méthode utilisée par le moteur de recherche du web, une sorte de moteur de recherche avancé
     * @param string $search Paramètre non utilisé
     * @param int $from L'index du premier élément des résultat
     * @param int $limit Le nombre maximum de résultat
     * @param string $order L'odre de classement des résultats. (optionnel)
     * @param array $conditions Les champs et valeurs recherchées
     * @return array Le résultat
     */
    public function getDataForSearchWeb($search, $from = '0', $limit ='30', $order='ORDER BY id_cmd DESC', $conditions = '') {
	$where = " WHERE ";
	$archives = 0;
	if(is_array($conditions)) {
	    foreach($conditions as $k => $v) {
		if($k == 'cp_ent')		$where .= "$k LIKE '".$v."%' AND ";
		elseif($k == 'sommeFHT_cmd')	$where .= " $k >= '$v' AND ";
		elseif($k == 'sommeFHT_cmd2')	$where .= " sommeFHT_cmd < '$v' AND ";
		elseif($k == 'status_cmd' and $v == '9') {
		    $where .= "$k = '".$v."' AND ";
		    $archives = $v;
		}
		elseif($k == 'status_cmd')	$where .= "$k = '".$v."' AND ";
		elseif($k == 'entreprise_cmd')	$where .= "$k = '".$v."' AND ";
		else				$where .= "$k LIKE '%".$v."%' AND ";
	    }
	}
	if($archives != '9')			$where .= " status_cmd != '10' ";


	if($limit != 'ALL')
	    $this->makeRequeteFree("SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
			FROM commande c
			LEFT JOIN entreprise ON entreprise.id_ent = c.entreprise_cmd
			LEFT JOIN contact c2 ON c2.id_cont = c.contact_achat_cmd
			LEFT JOIN contact c1 ON c1.id_cont = c.contact_cmd
			LEFT JOIN devis ON devis.id_dev = c.devis_cmd 
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statuscommande rsd ON rsd.id_stcmd = c.status_cmd
			LEFT JOIN ref_pays rfp ON rfp.id_pays = c.paysdelivery_cmd
			LEFT JOIN user ON user.login = c.commercial_cmd $where $order LIMIT $from , $limit ");
	else
	    $this->makeRequeteFree("SELECT COUNT(*)
			FROM commande c
			LEFT JOIN entreprise ON entreprise.id_ent = c.entreprise_cmd
			LEFT JOIN contact c2 ON c2.id_cont = c.contact_achat_cmd
			LEFT JOIN contact c1 ON c1.id_cont = c.contact_cmd
			LEFT JOIN devis ON devis.id_dev = c.devis_cmd 
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statuscommande rsd ON rsd.id_stcmd = c.status_cmd
			LEFT JOIN ref_pays rfp ON rfp.id_pays = c.paysdelivery_cmd
			LEFT JOIN user ON user.login = c.commercial_cmd $where $order ");
	return $this->process2();
    }



    /**
     * Fonction de recherche de données à partir d'un identifiant
     * @param string $search le champ de recherche
     * @param int $limit Le nombre maximum de résultat (défaut 10, ALL permet de faire un COUNT(*))
     * @param int $from Le numéro d'index du premier résultat (défaut 0)
     * @param string $plus D'éventuelles restrictions supplémentaires
     * @return array Le résultat
     */
    public function getDataForExportTableur($list) {
	if (!is_array($list))
	    $list=array($list);
	foreach($list as $k => $v)
	    $listSql .= '\''.$v.'\',';
	$sqlList = '('.substr($listSql,0,-1).')';

	$this->makeRequeteFree("SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
				FROM commande c
				LEFT JOIN entreprise ON entreprise.id_ent = c.entreprise_cmd
				LEFT JOIN contact c2 ON c2.id_cont = c.contact_achat_cmd
				LEFT JOIN contact c1 ON c1.id_cont = c.contact_cmd
				LEFT JOIN devis ON devis.id_dev = c.devis_cmd
				LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
				LEFT JOIN ref_statuscommande rsd ON rsd.id_stcmd = c.status_cmd
				LEFT JOIN ref_pays rfp ON rfp.id_pays = c.paysdelivery_cmd
				LEFT JOIN user ON user.login = c.commercial_cmd
				LEFT JOIN ref_condireglement rc ON rc.id_condreg = c.condireglement_cmd
				LEFT JOIN ref_modereglement rm ON rm.id_modereg = c.modereglement_cmd
				WHERE id_cmd IN $sqlList
				ORDER BY id_cmd ASC,nom_ent ASC");
	return $this->process2();
    }

    /**
     * Méthode qui historise les commande vues.
     * @param int $limit Le nombre max de résultat
     * @param string $type Précise si on veut le total ou pas
     * @param string $order Précise l'ordre de tri des résultats
     * @return array Le résultat
     */
    public function getDataForHistoriqueVisit($limit ='10', $type = '', $order='ORDER BY id_cmd DESC') {
	if(array_key_exists('historiqueVisite',$_SESSION) and
		array_key_exists('commande',$_SESSION['historiqueVisite']) and
		count($_SESSION['historiqueVisite']['commande']) > 0) {
	    $list = '';
	    foreach($_SESSION['historiqueVisite']['commande'] as $id)
		$list .= '\''.$id.'\', ';
	    $list = '('.substr($list,0,-2).')';
	}
	else $list = '(\'fakeNull\')';
	if($type == 'COUNT')
	    $select = 'SELECT COUNT(*) AS counter';
	else $select = 'SELECT titre_cmd, nom_ent, nom_tyent, type_ent, nom_cont, prenom_cont, civ_cont, id_cmd ';
	$this->makeRequeteFree($select." FROM commande
			LEFT JOIN entreprise ON entreprise.id_ent = commande.entreprise_cmd 
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact ON contact.id_cont = commande.contact_cmd
			WHERE id_cmd IN $list $order LIMIT 0, $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le status d'une commande
     * @param string $id L'id de la commande
     * @return array Le status
     */
    public function getStatusCommande($id) {
	$this->makeRequeteFree("SELECT status_cmd, tva_cmd FROM commande WHERE id_cmd = '".$id."' ; ");
	return $this->process2();
    }

    /**
     * Fonction de recherche de données produit à partir d'un nom, un id ou une famille de produits.
     * @param string $search Ce que l'on cherche
     * @param int $limit Le nombre max de résultat (defaut 10)
     * @param int $from L'index du premier résultat (defaut 0)
     * @return array Le résultat
     */
    public function getDataForSearchProd($search,$limit = 10, $from = 0) {
	$qTag = " LIKE '%".$search."%' ";
	$this->makeRequeteFree("SELECT  nom_prod, id_prod, nom_prodfam, prix_prod
				FROM produit
				LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod
				WHERE nom_prod $qTag OR id_prod $qTag OR nom_prodfam $qTag
				ORDER BY id_prod ASC
				LIMIT $from , $limit");
	//Fonction appelée lors d'une recherche simple.
	return $this->process2();
    }

    /**
     * Méthode qui recherche des factures
     * @param string $search Ce que l'on cherche
     * @param int $limit Le nombre max de résultats
     * @param int $from L'index du premier résultat
     * @param string $plus Des restrictions supplémentaires
     * @return array Le résultat
     */
    public function getDataForSearchFacture($search,$limit = 10, $from = 0, $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	$this->makeRequeteFree("SELECT titre_cmd, nom_ent, nom_cont, prenom_cont, civ_cont, id_cmd
				FROM commande c
				LEFT JOIN entreprise ON entreprise.id_ent = c.entreprise_cmd
				LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
				LEFT JOIN contact ON contact.id_cont = c.contact_cmd
				LEFT JOIN facture ON facture.commande_fact = id_cmd
				WHERE (nom_ent $qTag OR ville_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag
				OR mail_cont $qTag OR id_cmd $qTag OR titre_cmd $qTag OR devis_cmd $qTag)
				AND facture.id_fact IS NULL AND sommeHT_cmd > '0' $plus
				ORDER BY id_cmd DESC
				LIMIT $from , $limit ");
	//Fonction appelée lors d'une recherche simple.
	return $this->process2();
    }

    /**
     * Méthode qui retourne plusieurs Infos pour statistiques
     * @return array Des infos de stats
     */
    public function getDataForStats() {
	$this->makeRequeteFree("SELECT COUNT(id_cmd) as N, avg(sommeFHT_cmd) as X, VARIANCE(sommeFHT_cmd) as variance, SUM(sommeFHT_cmd) as somme from commande");
	$result = $this->process2();
	$this->makeRequeteFree("SELECT AVG(DISTINCT sommeFHT_cmd) AS MEDIANE
						FROM   (SELECT sommeFHT_cmd
        							FROM   (SELECT *
                							FROM    commande
               								UNION ALL
                							SELECT *
               								FROM    commande) STT
       								WHERE (SELECT COUNT(*)
                						FROM    commande)
                    			<= (SELECT COUNT(*)
              						FROM   (SELECT *
                      						FROM    commande
                     						UNION ALL
                      						SELECT *
                      						FROM    commande) AS SOU
              						WHERE  SOU.sommeFHT_cmd <= STT.sommeFHT_cmd)
								AND (SELECT COUNT(*)
    									FROM    commande)
          						<= (SELECT COUNT(*)
              						FROM   (SELECT *
                     						FROM    commande
                     						UNION ALL
                      						SELECT *
                      						FROM    commande) AS SUR
              			WHERE SUR.sommeFHT_cmd >= STT.sommeFHT_cmd) ) AS T");
	$result['mediane'] = $this->process2();
	return $result;
    }

    /**
     * Méthode qui récupère les valeurs des commande
     * @deprecated
     * @param array $ids Les ids des commandes
     * @return array Le résultat
     */
    public function getValues($ids = array()) {
	$this->setPersistant(1);
	$preums = 'oui';
	$this->makeRequeteFree("set @count=0;");
	$this->process2();
	$requete = "select C.sommeFHT_cmd as val from ".
		"(select sommeFHT_cmd, @count:=@count+1 as num from commande order by sommeFHT_cmd) as C ";
	foreach($ids as $v) {
	    if($preums == 'oui') {
		$requete .= "where num = $v ";
		$preums = 'non';
	    }
	    else $requete .= "or num = $v ";
	}
	$this->makeRequeteFree($requete);
	$result = $this->process2();
	$this->setPersistant(0);
	return $result;
    }

    /**
     * Fonction de recherche de données à partir d'un identifiant
     * @param string $id L'id de la commande
     * @return array Le résultat
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat
		FROM commande
		LEFT JOIN entreprise ON entreprise.id_ent = commande.entreprise_cmd
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		LEFT JOIN contact c2 ON c2.id_cont = commande.contact_achat_cmd
		LEFT JOIN contact c1 ON c1.id_cont = commande.contact_cmd
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		LEFT JOIN ref_statuscommande rsd ON rsd.id_stcmd = commande.status_cmd
		LEFT JOIN ref_condireglement rc ON rc.id_condreg = commande.condireglement_cmd
		LEFT JOIN user ON user.login = commande.commercial_cmd
		WHERE id_cmd = '".trim($id)."'");
	return $this->process2();
	//Fonction appelée pour récupérer toutes les infos sur une commande. (pour l'affichage de sa fiche)
    }

    /**
     * Fonction qui récupère la liste des produits liés à une commande.
     * @param string $id L'id de la commande
     * @param int $nbprod Précise si on ne souhaite qu'un seul produit
     * @param string $prod Contient l'id d'un produit si nécessaire
     * @return array Le résultat
     */
    public function getProduitsFromID($id, $nbprod = '0', $prod = '') {
	$prod=urldecode($prod);
	if($nbprod == '0') {
	    $this->makeRequeteFree("SELECT *
			FROM commande_produit
			LEFT JOIN produit ON produit.id_prod = commande_produit.id_produit
			LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
			LEFT JOIN fournisseur ON fournisseur.id_fourn = commande_produit.fournisseur
			LEFT JOIN entreprise ON entreprise.id_ent = fournisseur.entreprise_fourn
			WHERE id_commande = '".trim($id)."'");
	}
	else {
	    //Si je ne veux qu'un seul produit d'une commande...
	    $this->makeRequeteFree("SELECT *
			FROM commande_produit
			LEFT JOIN produit ON produit.id_prod = commande_produit.id_produit
			LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
			WHERE id_commande = '".trim($id)."' AND id_produit = '".trim($prod)."' ");
	}
	return $this->process2();
    }

    /**
     * Méthode de récupération des produits liés à une commande ayant un fournisseur
     * @param string $id L'id de la commande
     * @return array Le résultat
     */
    public function getProduitsFromCommande($id) {
	$this->makeRequeteFree("SELECT id_prod, stock_prod, quantite, commande_prod
	 	FROM produit
	 	LEFT JOIN commande_produit cp ON cp.id_produit = produit.id_prod
	 	WHERE id_commande = '".trim($id)."' AND fournisseur is not null ");
	return $this->process2();
    }

    /**
     * Méthode qui permet de récupérer la liste des fournisseurs liés à une commande
     * @param string $id L'id de al commande
     * @return array  Le résultat
     */
    public function getFournisseurFromID($id) {
	$this->makeRequeteFree("SELECT fournisseur FROM commande_produit cp LEFT JOIN fournisseur ON fournisseur.id_fourn = cp.fournisseur " .
		"WHERE id_commande = '".trim($id)."' GROUP BY fournisseur ");
	$temp = $this->process2();
	return $temp;
    }

    /**
     * Fonction qui récupère les informations détaillées sur un produit.
     * @param string $id L'id d'un produit
     * @return array Le résultat
     */
    public function getInfoProduits($id) {
	$this->makeRequeteFree("SELECT *
		FROM produit
		WHERE id_prod = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les infos d'un produits vis à vis des commandes
     * @param string $id L'id d'un produit
     * @return array le résultat
     */
    public function getInfoProduitsPlus($id) {
	$this->makeRequeteFree("SELECT *
		FROM commande_produit
		LEFT JOIN produit ON produit.id_prod = commande_produit.id_produit
		WHERE id_produit = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère la liste des produits d'un devis
     * @param string $id L'id d'un devis
     * @return array Les produits
     */
    public function getProduitsDevis($id) {
	$this->makeRequeteFree("SELECT *
		FROM devis_produit dp
		LEFT JOIN produit ON produit.id_prod = dp.id_produit
		WHERE id_devis = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Fonction qui récupère toutes les infos sur
     * le devis et l'entreprise liée(s) à un commande.
     * @param string $id L'id d'un devis
     * @return array Les infos
     */
    public function getEntrepriseData($id) {
	$this->makeRequeteFree("SELECT *
		FROM devis
		LEFT JOIN entreprise on entreprise.id_ent = devis.entreprise_dev
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		WHERE id_dev = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Fonction qui récupère toutes les infos sur un contact lié à un commande.
     * @param int $id L'id d'un contact
     * @return Les infos du contact
     */
    public function getContactData($id) {
	$this->makeRequeteFree("SELECT * FROM contact WHERE id_cont = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations d'un devis
     * @param string $id L'id du devis
     * @return array Les données
     */
    public function getDataFromDevis($id) {
	$this->makeRequeteFree("SELECT * FROM devis WHERE id_dev = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui ajoute un produit dans la table produit
     * @param array $data Les données à insérer
     * @return array Indique si la requète s'est bien passée
     */
    public function addProduit($data) {
	$sql = $this->makeRequeteInsert('produit',$data);
	Logg::loggerInfo('commandeModel::addProduit() ~ ajout du produit '.$data['id_prod'].' intitulé : '.$data['nom_prod'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Fonction gérant l'insertion dans la BDD d'un lien produit-commande.
     * @param array $data Les données à insérer
     * @param bool $actu Précise si on veut une entré d'actualité
     * @return array Indique si la requète s'est bien exécutée
     */
    public function insertProduits($data, $actu = true) {
	if($data['quantite'] == NULL)
	    $data['quantite'] = 1;
	if($data['remise'] == NULL)
	    $data['remise'] = 0;
	if($data['remiseF'] == NULL)
	    $data['remiseF'] = 0;
	$data['prix']=number_format($data['prix'],2,'.','');
	$sql = $this->makeRequeteInsert('commande_produit',$this->bindVarCommandeProduit($data));
	Logg::loggerInfo('commandeModel::insertProduits() ~ ajout du produit '.$data['id_produit'].' pour la commande '.$data['id_commande'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	$this->updateMontant($data['id_commande']);
	if($rs[0] and $actu)
	    $this->addActualite($data['id_commande'], 'update');
	return $rs;
    }

    /**
     * Fonction gérant la suppression dans la BDD d'un lien produit-commande.
     * @param array $data Contient les infos à supprimer
     * @param string $id_produit L'id du produit à supprimer
     * @param string $tous Précise si on doit enlever tous les produits de la commande ou non
     * @return array Indique si la requète s'est bien déroulée
     */
    public function deleteProduits($data, $id_produit = NULL, $tous = 'non') {
	$temp=$data;
	if($tous == 'all')
	    $sql = $this->makeRequeteFree("delete from commande_produit where id_commande='".$data['id_commande']."';");
	elseif($id_produit != NULL)
	    $sql = $this->makeRequeteFree("delete from commande_produit where id_commande='".$data['id_commande']."' AND id_produit='".$id_produit."';");
	else $sql = $this->makeRequeteFree("delete from commande_produit where id='".$data['id']."';");
	Logg::loggerInfo('commandeModel::deleteProduits() ~ suppression de produits sur la commande '.$data['id_commande'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0]) {
	    $this->updateMontant($data['id_commande']);
	    $this->addActualite($data['id_commande'], 'update');
	}
	return $rs;
    }

    /**
     * Fonction qui gère la MAJ d'un lien produit-commande.
     * @param array $data Les données à mettre à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function updateProduits($data) {
	$sql = $this->makeRequeteUpdate('commande_produit', 'id', $data['id'],$this->bindVarCommandeProduit($data));
	Logg::loggerInfo('commandeModel::updateProduits() ~ mise à jour du produit '.$data['id'].' sur la commande',$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0]) {
	    $this->updateMontant($data['id_commande']);
	    $this->addActualite($data['id_commande'], 'update');
	}
	return $rs;
    }

    /**
     * Méthode qui met à jour les montants de la commande
     * @param string $id L'id de la commande
     */
    private function updateMontant($id) {
	$prod = $this->getProduitsFromID($id);
	$sommeFHT = $sommeHT = 0;
	foreach($prod[1] as $v) {
	    $sommeFHT += $v['prixF']*(1-$v['remiseF']/100)*$v['quantite_cmd'];
	    $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
	}
	$this->update(array('sommeHT_cmd' => $sommeHT, 'sommeFHT_cmd' => $sommeFHT), $id);
    }

    /**
     * Fonction d'execution de la requete d'insertion
     * @param array $data Les données à insérer
     * @param string $fait Précise si on créer, clone ou fait en mode express
     * @param array $prod Contient les produits éventuels
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insert($data, $fait = 'creation', $prod = array()) {
	$data['datemodif_cmd']=date('Y-m-d');
	$data['daterecord_cmd']=date('Y-m-d');
	$data['tva_cmd']=number_format($data['tva_cmd'], 1, '.', '');
	if($fait == 'creation') {
	    $sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarCommande($data));
	    Logg::loggerInfo('commandeModel::insert() ~ ajout de la commande '.$data['id_cmd'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0])
		$this->addActualite($data['id_cmd'], 'add');
	    return $rs;
	}
	elseif($fait == 'cloner') {
	    $sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarCommande($data));
	    Logg::loggerInfo('commandeModel::insert() ~ ajout de la commande '.$data['id_cmd'].' par clonnage',$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    $a = 2;
	    foreach($prod as $v) {
		$temp['id_commande'] = $data['id_cmd'];
		$temp['id_produit'] = $v['id_produit'];
		$temp['desc'] = $v['desc'];
		$temp['quantite'] = $v['quantite'];
		$temp['quantite_cmd'] = $v['quantite_cmd'];
		$temp['remise'] = $v['remise'];
		$temp['prix'] = $v['prix'];
		$temp['prixF'] = $v['prixF'];
		$temp['fournisseur'] = $v['fournisseur'];
		$temp['remiseF'] = $v['remiseF'];
		$resultprod = $this->insertProduits($temp, false);
		$result[$a]=$resultprod;
		$a++;
	    }
	    if($rs[0])
		$this->addActualite($data['id_cmd'], 'add');
	    return $rs;
	}
    }

    /**
     * Fonction d'exécution de la requète SQL de modification
     * @param array $data Les données à mettre à jour
     * @param string $id_cmd L'id de al commande à mettre à jour
     * @return array Indique si la requête s'est bien passée.
     */
    public function update($data,$id_cmd) {
	$data['datemodif_cmd']=date('Y-m-d');
	if(array_key_exists('tva_cmd', $data))
	    $data['tva_cmd']=number_format($data['tva_cmd'], 1, '.', '');
	if(array_key_exists('sommeHT_cmd', $data))
	    $data['sommeHT_cmd']=prepareNombreTraitement($data['sommeHT_cmd']);
	if(array_key_exists('sommeFHT_cmd', $data))
	    $data['sommeFHT_cmd']=prepareNombreTraitement($data['sommeFHT_cmd']);
	if(!array_key_exists($this->sqlIdField,$data)) {
	    $sql = $this->makeRequeteUpdate($this->sqlTableName,"id_cmd",$id_cmd,$this->bindVarCommande($data));
	    Logg::loggerInfo('commandeModel::update() ~ mise à jour de la commande '.$id_cmd,$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0] and $data['status_cmd'] != '')
		$this->addActualite($id_cmd, 'update');
	    return $rs;
	}
	else {
	    $sql = $this->makeRequeteUpdate($this->sqlTableName,"id_cmd",$data['id_cmd'],$this->bindVarCommande($data));
	    Logg::loggerInfo('commandeModel::update() ~ mise à jour de la commande '.$data['id_cmd'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0] and $data['status_cmd'] != '')
		$this->addActualite($data['id_cmd'], 'update');
	    return $rs;
	}
    }

    /**
     * Fonction de suppression dans la BDD d'un commande.
     * @param string $id_cmd L'id de la commande à supprimer
     * @param string $facture Précise si une facture est liée ou non.
     * @return array Indique si la suppression s'est bien passée
     */
    public function delete($id_cmd, $facture = 'no') {
	$sql = $this->makeRequeteFree("DELETE FROM $this->sqlTableName Where id_cmd = '".$id_cmd."'");
	Logg::loggerInfo('commandeModel::delete() ~ suppression de la commande '.$id_cmd,$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($facture == 'yes') {
	    $this->makeRequeteFree("select id_fact FROM facture Where commande_fact = '".$id_cmd."'");
	    $facture = $this->process2();
	    $sql = '';
	    foreach($facture[1] as $v) {
		$fact = $v['id_fact'];
		$sql.= $this->makeRequeteFree("DELETE FROM facture_produit Where id_facture = '".$fact."' ");
		$result = $this->process2();
	    }
	    $sql.= $this->makeRequeteFree("DELETE FROM facture Where commande_fact = '".$id_cmd."'");
	    $result = $this->process2();
	    Logg::loggerInfo('commandeModel::delete() ~ suppression des ressources liées à la commande '.$id_cmd,$sql,__FILE__.'@'.__LINE__);
	}
	if($rs[0])
	    $this->addActualite($id_cmd, 'delete');
	return $rs;
    }

    /**
     * Méthode qui trie les commandes par montant
     * @param int $maxi Le nombre max de résultats
     * @param int $from L'index du premier résultat
     * @param string $plus D'éventuelles restrictions supplémentaires
     * @return array Le résultat
     */
    public function getDataByMontant($maxi = NULL, $from = 0, $plus = '') {

	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY sommeFHT_cmd DESC ".$plus);
	else $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ".$plus." ORDER BY sommeFHT_cmd DESC, id_cmd DESC LIMIT $from , $maxi");
	$result = $this->process2();

	return $result;
    }

    /**
     * Méthode qui trie les commandes par date de création
     * @param int $maxi Le nombre max de résultats
     * @param int $from L'index du premier résultat
     * @param string $plus D'éventuelles restrictions supplémentaires
     * @return array Le résultat
     */
    public function getDataByCreation($maxi = NULL, $from = 0, $plus = '') {

	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY daterecord_cmd DESC ".$plus);
	else $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ".$plus." ORDER BY daterecord_cmd DESC, id_cmd DESC LIMIT $from , $maxi");
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui trie les commandes par entreprise
     * @param int $maxi Le nombre max de résultats
     * @param int $from L'index du premier résultat
     * @param string $plus D'éventuelles restrictions supplémentaires
     * @return array Le résultat
     */
    public function getDataByEntreprise($maxi = NULL, $from = 0, $plus = '') {

	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY entreprise_cmd ASC ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (entreprise_cmd IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName LEFT JOIN entreprise ON entreprise.id_ent=commande.entreprise_cmd ".$plus." ORDER BY temp DESC, entreprise.nom_ent ASC, id_cmd DESC LIMIT $from , $maxi");
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui trie les commandes par contact
     * @param int $maxi Le nombre max de résultats
     * @param int $from L'index du premier résultat
     * @param string $plus D'éventuelles restrictions supplémentaires
     * @return array Le résultat
     */
    public function getDataByContact($maxi = NULL, $from = 0, $plus = '') {

	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY contact_cmd ASC ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (contact_cmd IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName LEFT JOIN contact ON contact.id_cont=commande.contact_cmd ".$plus." ORDER BY temp DESC, contact.nom_cont ASC, id_cmd DESC LIMIT $from , $maxi");
	$result = $this->process2();

	return $result;
    }

    /**
     * Méthode qui récupère les actualités liés à une commande
     * @param string $id L'id de la commande
     * @return array Les actus
     */
    public function getActualites($id) {
	$this->makeRequeteFree("SELECT * from actualite where id_cmd = '$id' ");
	$result = $this->process2();
	if($result[0]) return $result[1];
    }

    /**
     * Méthode qui récupère tous les fournisseurs avec un id de commande
     * @param string $id L'id d'une commande
     * @param string $quoi Précise le type de demande
     * @return array Le résultat
     */
    public function getAllFournisseursFromID($id, $quoi = "simple") {
	if($quoi == "simple") {
	    $this->makeRequeteFree("select *, dp.prixF as prixF2, dp.remiseF as remiseF2 from commande_produit dp left join produit p on p.id_prod=dp.id_produit left join produit_fournisseur pf on pf.produit_id = dp.id_produit left join fournisseur ON fournisseur.id_fourn = pf.fournisseur_id left join entreprise e ON e.id_ent = fournisseur.entreprise_fourn left join contact c on c.id_cont=fournisseur.contactComm_fourn where dp.id_commande = '".trim($id)."' order by id_produit ASC ;");
	    $result = $this->process2();
	    $old = null;
	    $increment = 0;
	    foreach($result[1] as $v) {
		if($v['id_produit'] != $old) {
		    if($v['id_fourn'] != null) {
			$v['fournisseurs'][$v['id_fourn']] = $v['nom_ent'].' ('.$v['cp_ent'].')';
			$v['PF'][$v['id_fourn']] = $v['prixF'];
			$v['RF'][$v['id_fourn']] = $v['remiseF'];
		    }
		    $data[$increment]=$v;
		    $old = $v['id_produit'];
		    $increment++;
		    continue;
		}
		else {
		    $data[$increment-1]['fournisseurs'][$v['id_fourn']] = $v['nom_ent'].' ('.$v['cp_ent'].')';
		    $data[$increment-1]['PF'][$v['id_fourn']] = $v['prixF'];
		    $data[$increment-1]['RF'][$v['id_fourn']] = $v['remiseF'];
		    continue;
		}
	    }
	}
	elseif($quoi == 2) {
	    $this->makeRequeteFree("SELECT *
					  FROM `commande_produit`, fournisseur
					  LEFT JOIN entreprise ON entreprise.id_ent = fournisseur.entreprise_fourn
					  LEFT JOIN contact ON contact.id_cont = fournisseur.contactComm_fourn
					  WHERE id_fourn = fournisseur AND `id_commande` = '".$id."'");
	    $fournisseurs = $this->process();
	    if (count($fournisseurs) > 0) {
		foreach ($fournisseurs as $key => $fournisseur) {
		    if($fournisseur['id_fourn'] != '') {
			$data[$fournisseur['id_fourn']][$fournisseur['id_produit']] = $fournisseur['nom_ent']." (".
				$fournisseur['prenom_cont']." ".
				$fournisseur['nom_cont'].")";
		    }
		}
	    }
	}
	elseif($quoi == '3') {
	    $this->makeRequeteFree("SELECT *
						  FROM  fournisseur
						  LEFT JOIN entreprise ON entreprise.id_ent = fournisseur.entreprise_fourn
						  LEFT JOIN contact ON contact.id_cont = fournisseur.contactComm_fourn, `commande_produit`
						  LEFT JOIN produit ON produit.id_prod = commande_produit.id_produit
						  LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
						  WHERE id_fourn = fournisseur AND `id_commande` = '".$id."'");
	    $data = $this->process();
	}
	return $data;
    }

    /**
     * Méthode qui récupère les fournisseurs via une commande
     * @param string $id L'id d'une commande
     * @return array Le résultat
     */
    private function getFournisseurs($id) {
	$this->makeRequeteFree("SELECT *
						  FROM  fournisseur
						  LEFT JOIN entreprise ON entreprise.id_ent = fournisseur.entreprise_fourn
						  LEFT JOIN contact ON contact.id_cont = fournisseur.contactComm_fourn, `commande_produit`
						  LEFT JOIN produit ON produit.id_prod = commande_produit.id_produit
						  LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
						  WHERE id_fourn = fournisseur AND `id_commande` = '".$id."'");
	return $this->process();
    }

    /**
     * Create ScoreCommande element
     * @param string $id Affaire ID
     * @param bool $bureau Précise si on est sur le bureau ou pas
     * @return array with various score elements
     */
    static function ScoreCommande($id, $bureau = false) {

	$req = 'SELECT  * FROM commande,ref_statuscommande WHERE id_stcmd = status_cmd AND id_cmd = \''.$id.'\'';
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	$cmd = $res[0];
	// Scoring projet difficulty
	if((int) $cmd['pourcent_stcmd'] == 100) {
	    $score = 100;
	    $desc = "Commande terminée. Derniére action éffectuée :".$cmd['nom_stcmd'];
	    $color = '';
	}
	elseif((int) $cmd['pourcent_stcmd'] == 0) {
	    $score = 0;
	    $desc = "Commande cloturée. Pas de scoring possible";
	    $color = '';
	}
	else {
	    $score = $cmd['pourcent_stcmd'];
	    $desc = $cmd['nom_stcmd'];
	    $color = $cmd['color_stcmd'];
	}

	$out['score'] = $score;
	if($bureau)
	    $out['img'] = imageTag('Img.CommandeScore.php?score='.$score,$score.'%');
	else $out['img'] = imageTag('../Img.CommandeScore.php?score='.$score,$score.'%');
	$out['color'] = $color;
	$out['desc'] = $desc;
	$out['LinkBox'] = ContextBox($out['img'],$desc,"Scoring de la commande ".$cmd['id_cmd']);
	return $out;
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function CommandeGenerateID($id_dev) {
	return $id_dev."BC";
    }

    /**
     * Calculate sum of product for commande
     * @param $id_cmd String: commande ID
     */
    static function  updateTotalInDB($id_cmd) {
	// on récupére les info de commande
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT * FROM `commande_produit` WHERE id_commande = '".$id_cmd."'");
	$produits = $bddtmp->process();

	if (count($produits) > 0) {
	    // On prépare les tableaux pour le JS produits
	    $total = 0;
	    foreach ($produits as $key => $produit) {
		if($produit['fournisseur'] == '')
		    $produit['remiseF'] = 100;
		$Sum=($produit['quantite']*$produit['prix'])-((($produit['quantite']*$produit['prix'])*$produit['remise'])/100);
		$total = $total+round($Sum,2);
		$Sum1=($produit['quantite']*$produit['prixF'])-((($produit['quantite']*$produit['prixF'])*$produit['remiseF'])/100);
		$total1 = $total1+round($Sum1,2);
	    }
	    $var_out['sommeHT_cmd'] = round($total,2);
	    $var_out['sommeFHT_cmd'] = round($total1,2);
	}
	else {
	    $var_out['sommeHT_cmd'] = 0.00;
	    $var_out['sommeFHT_cmd'] = 0.00;
	}
	$sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$var_out);
	Logg::loggerInfo('commandeModel::updateTotalInDB() ~ mise à jour du total de la commande '.$id_cmd,$sql,__FILE__.'@'.__LINE__);
	$bddtmp->process();
    }




    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributeCommandeInDB($id_cmd,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('titre_cmd', $newAttribList) or
		array_key_exists('tva_cmd', $newAttribList) or
		array_key_exists('commercial_cmd', $newAttribList) or
		array_key_exists('modereglement_cmd', $newAttribList) or
		array_key_exists('condireglement_cmd', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_cmd != '' and $hasChange) {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    if($newAttribList['titre_cmd'] != '')
		$newData['titre_cmd'] = $newAttribList['titre_cmd'];
	    if($newAttribList['tva_cmd'] != '')
		$newData['tva_cmd'] = $newAttribList['tva_cmd'];
	    if($newAttribList['commercial_cmd'] != '')
		$newData['commercial_cmd'] = $newAttribList['commercial_cmd'];
	    if($newAttribList['modereglement_cmd'] != '')
		$newData['modereglement_cmd'] = $newAttribList['modereglement_cmd'];
	    if($newAttribList['condireglement_cmd'] != '')
		$newData['condireglement_cmd'] = $newAttribList['condireglement_cmd'];

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::changeAttributeCommandeInDB() ~ changement d\'attribut de la commande '.$id_cmd,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::changeAttributeCommandeInDB() ~ impossible de changer les attributs de la commande '.$id_cmd,'',__FILE__.'@'.__LINE__);
    }


    static function archivateCommandeInDB($id_cmd,$newAttribList = array()) {
	if ($id_cmd != '') {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    $newData['status_cmd'] = '10';

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'archive', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::archivateCommandeInDB() ~ Commande '.$id_cmd.' marquée comme archivée',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::archivateCommandeInDB() ~ impossible de marquer comme archivée la commande '.$id_cmd,'',__FILE__.'@'.__LINE__);
    }

    static function markRecuCommandeInDB($id_cmd,$newAttribList = array()) {
	if ($id_cmd != '') {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    if($newAttribList['commentaireRecu'] != '')
		$newData['commentaire_cmd'] = $cmd['commentaire_cmd'].'. '.$newAttribList['commentaireRecu'];
	    $newData['status_cmd'] = '2';

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::markRecuCommandeInDB() ~ Commande '.$id_cmd.' marquée comme supprimée',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::markRecuCommandeInDB() ~ impossible de marquer comme supprimée la commande '.$id_cmd,'',__FILE__.'@'.__LINE__);
    }

    static function markBDCFEnvoyeCommandeInDB($id_cmd,$newAttribList = array()) {
	if ($id_cmd != '') {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    if($newAttribList['complementdeliveryBDCFEnvoye'] != '')
		$newData['complementdelivery_cmd'] = $cmd['complementdelivery_cmd'].'. '.$newAttribList['complementdeliveryBDCFEnvoye'];
	    if($newAttribList['commentaireBDCFEnvoye'] != '')
		$newData['commentaire_cmd'] = $cmd['commentaire_cmd'].'. '.$newAttribList['commentaireBDCFEnvoye'];
	    $newData['status_cmd'] = '4';

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::markBDCFEnvoyeCommandeInDB() ~ Bon de commande '.$id_cmd.' marquée comme envoyé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::markBDCFEnvoyeCommandeInDB() ~ impossible de marquer le bon de commande fournisseur '.$id_cmd.' comme envoyé','',__FILE__.'@'.__LINE__);
    }

    static function markBDCFRecuCommandeInDB($id_cmd,$newAttribList = array()) {
	if ($id_cmd != '') {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    if($newAttribList['commentaireBDCFRecu'] != '')
		$newData['commentaire_cmd'] = $cmd['commentaire_cmd'].'. '.$newAttribList['commentaireBDCFRecu'];
	    $newData['status_cmd'] = '5';

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::markBDCFRecuCommandeInDB() ~ Bon de commande '.$id_cmd.' marquée comme reçu',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::markBDCFRecuCommandeInDB() ~ impossible de marquer le bon de commande fournisseur '.$id_cmd.' comme reçu','',__FILE__.'@'.__LINE__);
    }

    static function markBDCFValidCommandeInDB($id_cmd,$newAttribList = array()) {
	if ($id_cmd != '') {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    if($newAttribList['commentaireBDCFValid'] != '')
		$newData['commentaire_cmd'] = $cmd['commentaire_cmd'].'. '.$newAttribList['commentaireBDCFValid'];
	    $newData['status_cmd'] = '6';

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::markBDCFEnvoyeCommandeInDB() ~ Bon de commande '.$id_cmd.' marquée comme validé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::markBDCFEnvoyeCommandeInDB() ~ impossible de marquer le bon de commande fournisseur '.$id_cmd.' comme validé','',__FILE__.'@'.__LINE__);
    }

    static function markExpedieCommandeInDB($id_cmd,$newAttribList = array()) {
	if ($id_cmd != '') {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    if($newAttribList['commentaireExpedie'] != '')
		$newData['commentaire_cmd'] = $cmd['commentaire_cmd'].'. '.$newAttribList['commentaireExpedie'];
	    $newData['status_cmd'] = '7';

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::markExpedieCommandeInDB() ~ Commande '.$id_cmd.' marquée comme expédiée',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::markExpedieCommandeInDB() ~ impossible de marquer la commande '.$id_cmd.' comme expédiée','',__FILE__.'@'.__LINE__);
    }

    static function markReceptionneCommandeInDB($id_cmd,$newAttribList = array()) {
	if ($id_cmd != '') {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    if($newAttribList['commentaireReceptionne'] != '')
		$newData['commentaire_cmd'] = $cmd['commentaire_cmd'].'. '.$newAttribList['commentaireReceptionne'];
	    $newData['status_cmd'] = '8';

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::markReceptionneCommandeInDB() ~ Commande '.$id_cmd.' marquée comme réceptionnée',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::markReceptionneCommandeInDB() ~ impossible de marquer la commande '.$id_cmd.' comme réceptionnée','',__FILE__.'@'.__LINE__);
    }

    static function markTermineCommandeInDB($id_cmd,$newAttribList = array()) {
	if ($id_cmd != '') {
	    $bddtmp = new CommandeModel();
	    $bddtmp->makeRequeteSelect('commande','id_cmd',$id_cmd);
	    $cmd = $bddtmp->process();
	    $cmd = $cmd[0];
	    if($newAttribList['commentaireTermine'] != '')
		$newData['commentaire_cmd'] = $cmd['commentaire_cmd'].'. '.$newAttribList['commentaireTermine'];
	    $newData['status_cmd'] = '9';

	    $sql = $bddtmp->makeRequeteUpdate('commande','id_cmd',$id_cmd,$newData);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_cmd, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('commandeModel::markTermineCommandeInDB() ~ Commande '.$id_cmd.' marquée comme terminée',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('commandeModel::markTermineCommandeInDB() ~ impossible de marquer la commande '.$id_cmd.' comme terminée','',__FILE__.'@'.__LINE__);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table commande
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarCommande($data) {
	return $this->bindVar($data,$this->availableFields);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table commande_produit
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFieldsProduit
     */
    public function bindVarCommandeProduit($data) {
	return $this->bindVar($data,$this->availableFieldsProduit);
    }
}
?>