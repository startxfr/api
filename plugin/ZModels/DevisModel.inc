<?php
loadPlugin('ZModels/GeneralModel');
/**
 * Classe gérant toutes les requètes SQL en rapport avec les devis.
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class devisModel extends GeneralModel {
    private $availableFieldsProduit;

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'devis';
	$this->sqlIdField = 'id_dev';
	$this->availableFields = array(
		'id_dev','entreprise_dev','contact_dev','affaire_dev',
		'sommeHT_dev','BDCclient_dev','titre_dev',
		'datemodif_dev','daterecord_dev','status_dev',
		'nomdelivery_dev','adressedelivery_dev','adresse1delivery_dev',
		'villedelivery_dev','cpdelivery_dev','paysdelivery_dev',
		'maildelivery_dev','complementdelivery_dev',
		'commercial_dev','contact_achat_dev', 'tva_dev', 'ren_dev', 'commentaire_dev');
	$this->availableFieldsProduit = array(
		'id_devis', 'id_produit', 'desc', 'quantite', 'remise',
		'prix', 'id');
	parent::__construct();
    }

    /**
     * Méthode qui recherche un devis dans la base
     * @param string $search Ce que l'on recherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @param string $plus Un complément à la requête SQL
     * @return array Les résultats
     */
    public function getDataForSearch($search,$limit = 10, $from = 0, $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	if($limit == 'ALL') {
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter " .
		    "FROM devis " .
		    "LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
			 LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			 LEFT JOIN contact ON contact.id_cont = devis.contact_dev " .
		    "WHERE ( nom_ent $qTag OR ville_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag " .
		    "OR mail_cont $qTag OR id_dev $qTag OR titre_dev $qTag OR affaire_dev $qTag )  " .
		    "AND status_dev != '2' ".$plus);
	}
	else {
	    $this->makeRequeteFree("SELECT titre_dev, nom_ent, nom_cont, prenom_cont, civ_cont, id_dev, ville_ent, sommeHT_dev, nom_stdev, color_stdev " .
		    "FROM devis " .
		    "LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
			 LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			 LEFT JOIN contact ON contact.id_cont = devis.contact_dev " .
		    "LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev ".
		    "WHERE ( nom_ent $qTag OR ville_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag " .
		    "OR mail_cont $qTag OR id_dev $qTag OR titre_dev $qTag OR affaire_dev $qTag )  " .
		    "AND status_dev != '2' ".$plus.
		    " ORDER BY id_dev DESC " .
		    "LIMIT $from , $limit");
	}
	return $this->process2();
    }

    /**
     * Méthode qui effectue une recherche selon critères d'un devis dans la base
     * @param string $search Paramètre inutile
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param string $order L'ordre de tri des résultats
     * @param array $conditions Les critères de recherche
     * @return array
     */
    public function getDataForSearchWeb($search, $from = '0', $limit ='30', $order='ORDER BY id_dev DESC', $conditions = '') {
	$where = " WHERE ";
	$archives = 0;
	if(is_array($conditions)) {
	    foreach($conditions as $k => $v) {
		if($k == 'cp_ent')
		    $where .= "$k LIKE '".$v."%' AND ";
		elseif($k == 'sommeHT_dev')
		    $where .= " $k >= '$v' AND ";
		elseif($k == 'sommeHT_dev2')
		    $where .= " sommeHT_dev < '$v' AND ";
		elseif($k == 'status_dev' and ($v == '7' or $v == '2' )) {
		    $where .= "$k = '".$v."' AND ";
		    $archives = $v;
		}
		elseif($k == 'status_dev')
		    $where .= "$k = '".$v."' AND ";
		elseif($k == 'entreprise_dev')
		    $where .= "$k = '".$v."' AND ";
		else
		    $where .= "$k LIKE '%".$v."%' AND ";
	    }
	}
	if($archives == '2')
	    $where .= " status_dev != '7' ";
	elseif($archives == '7')
	    $where .= " status_dev != '2' ";
	else
	    $where .= " status_dev != '2' AND status_dev != '7' ";

	if($limit != 'ALL')
	    $this->makeRequeteFree("SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
			FROM devis
			LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
			LEFT JOIN ref_activite ON ref_activite.id_act = entreprise.activite_ent
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
			LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
			LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
			LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
			LEFT JOIN facture ON facture.commande_fact = commande.id_cmd
			LEFT JOIN ref_statusfacture ON ref_statusfacture.id_stfact = facture.status_fact
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statusaffaire ON ref_statusaffaire.id_staff = affaire.status_aff
			LEFT JOIN ref_typeproj ON ref_typeproj.id_typro = affaire.projet_aff
			LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
			LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
			LEFT JOIN user ON user.login = devis.commercial_dev $where GROUP BY id_dev $order LIMIT $from , $limit ");
	else $this->makeRequeteFree("SELECT COUNT(DISTINCT id_dev) AS counter
			FROM devis
			LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
			LEFT JOIN ref_activite ON ref_activite.id_act = entreprise.activite_ent
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
			LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
			LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
			LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
			LEFT JOIN facture ON facture.commande_fact = commande.id_cmd
			LEFT JOIN ref_statusfacture ON ref_statusfacture.id_stfact = facture.status_fact
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statusaffaire ON ref_statusaffaire.id_staff = affaire.status_aff
			LEFT JOIN ref_typeproj ON ref_typeproj.id_typro = affaire.projet_aff
			LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
			LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
			LEFT JOIN user ON user.login = devis.commercial_dev $where $order ");
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

	$this->makeRequeteFree(
		"SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
			FROM devis
			LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
			LEFT JOIN ref_activite ON ref_activite.id_act = entreprise.activite_ent
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
			LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
			LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
			LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
			LEFT JOIN facture ON facture.commande_fact = commande.id_cmd
			LEFT JOIN ref_statusfacture ON ref_statusfacture.id_stfact = facture.status_fact
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statusaffaire ON ref_statusaffaire.id_staff = affaire.status_aff
			LEFT JOIN ref_typeproj ON ref_typeproj.id_typro = affaire.projet_aff
			LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
			LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
			LEFT JOIN user ON user.login = devis.commercial_dev
		WHERE id_dev IN $sqlList
		ORDER BY id_dev ASC,nom_ent ASC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère l'historique des visites sur les fihces devis
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param string $type Précise si on veut un COUNT ou tous les résultats
     * @param string $order Précise l'ordre de tri des résultats
     * @return array Les résultats
     */
    public function getDataForHistoriqueVisit($limit ='10', $type = '', $order='ORDER BY id_dev DESC') {
	if(array_key_exists('historiqueVisite',$_SESSION) and
		array_key_exists('devis',$_SESSION['historiqueVisite']) and
		count($_SESSION['historiqueVisite']['devis']) > 0) {
	    $list = '';
	    foreach($_SESSION['historiqueVisite']['devis'] as $id)
		$list .= '\''.$id.'\', ';
	    $list = '('.substr($list,0,-2).')';
	}
	else $list = '(\'fakeNull\')';
	if($type == 'COUNT')
	    $select = 'SELECT COUNT(*) AS counter';
	else $select = 'SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat';
	$this->makeRequeteFree($select." FROM devis
			LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
			LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
			LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
			LEFT JOIN user ON user.login = devis.commercial_dev WHERE id_dev IN $list $order LIMIT 0, $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui recherche des commandes
     * @param string $search Ce que l'on recherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @return array Les résultats
     */
    public function getDataForSearchCommande($search,$limit = 10, $from = 0) {
	$qTag = " LIKE '%".$search."%' ";
	$this->makeRequeteFree("SELECT titre_dev, nom_ent, nom_cont, prenom_cont, civ_cont, id_dev " .
		"FROM devis " .
		"LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
			 LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			 LEFT JOIN contact ON contact.id_cont = devis.contact_dev " .
		"LEFT JOIN commande ON commande.devis_cmd = id_dev ".
		"WHERE (nom_ent $qTag OR ville_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag " .
		"OR mail_cont $qTag OR id_dev $qTag OR titre_dev $qTag OR affaire_dev $qTag) " .
		"AND status_dev = '4' AND commande.id_cmd IS NULL AND sommeHT_dev > '0' ".
		"ORDER BY id_dev DESC " .
		"LIMIT $from , $limit ");
	return $this->process2();
    }


    /**
     * Méthode qui recherche des produits
     * @param string $search Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @return array Les résultats
     */
    public function getDataForSearchProd($search,$limit = 0, $from = 0) {
	$qTag = " LIKE '%".$search."%' ";
	if($limit == 0) {
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter " .
		    "FROM produit " .
		    "LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod ".
		    "WHERE ( nom_prod $qTag OR id_prod $qTag OR nom_prodfam $qTag OR treePathKey $qTag ) AND stillAvailable_prod = 1 " .
		    "ORDER BY id_prod ASC ");
	}
	else {
	    $this->makeRequeteFree("SELECT nom_prod, id_prod, nom_prodfam, treePathKey, prix_prod " .
		    "FROM produit " .
		    "LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod ".
		    "WHERE ( nom_prod $qTag OR id_prod $qTag OR nom_prodfam $qTag OR treePathKey $qTag ) AND stillAvailable_prod = 1 " .
		    "ORDER BY id_prod ASC " .
		    "LIMIT $from , $limit");
	}
	return $this->process2();
    }

    /**
     * Méthode qui s'occupe de faire des statistiques sur les devis
     * @return array les statistiques
     */
    public function getDataForStats() {
	$this->makeRequeteFree("SELECT COUNT(id_dev) as N, avg(sommeHT_dev) as X, VARIANCE(sommeHT_dev) as variance, SUM(sommeHT_dev) as somme from devis");
	$result = $this->process2();
	$this->makeRequeteFree("SELECT AVG(DISTINCT sommeHT_dev) AS MEDIANE
						FROM   (SELECT sommeHT_dev
        							FROM   (SELECT *
                							FROM    devis
               								UNION ALL
                							SELECT *
               								FROM    devis) STT
       								WHERE (SELECT COUNT(*)
                						FROM    devis)
                    			<= (SELECT COUNT(*)
              						FROM   (SELECT *
                      						FROM    devis
                     						UNION ALL
                      						SELECT *
                      						FROM    devis) AS SOU
              						WHERE  SOU.sommeHT_dev <= STT.sommeHT_dev)
								AND (SELECT COUNT(*)
    									FROM    devis)
          						<= (SELECT COUNT(*)
              						FROM   (SELECT *
                     						FROM    devis
                     						UNION ALL
                      						SELECT *
                      						FROM    devis) AS SUR
              			WHERE SUR.sommeHT_dev >= STT.sommeHT_dev) ) AS T");
	$result['mediane'] = $this->process2();
	return $result;
    }

    /**
     * Méthode qui récupère la valeur des x devis (x étant le numéro d'ordre des devis triés par HT croissant)
     * @param array $ids Les devis à considérer
     * @return array Les valeurs demandées
     */
    public function getValues($ids = array()) {
	$this->setPersistant(1);
	$preums = 'oui';
	$this->makeRequeteFree("set @count=0;");
	$this->process2();
	$requete = "select D.sommeHT_dev as val from ".
		"(select sommeHT_dev, @count:=@count+1 as num from devis order by sommeHT_dev) as D ";
	foreach($ids as $v) {
	    if($preums == 'oui') {
		$requete .= "where num = $v ";
		$preums = 'non';
	    }
	    else {
		$requete .= "or num = $v ";
	    }
	}
	$this->makeRequeteFree($requete);
	$result = $this->process2();
	$this->setPersistant(0);
	return $result;
    }

    /**
     * Méthode qui récupère toutes les informations d'un devis
     * @param string $id L'id du devis
     * @return array Les inforamtions sur le devis
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
		FROM devis
		LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
		LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
		LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
		LEFT JOIN user ON user.login = devis.commercial_dev
		LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
		LEFT JOIN facture f ON f.commande_fact = commande.id_cmd
		WHERE id_dev = '".trim($id)."'");
	return $this->process2();
	//Fonction appelée pour récupérer toutes les infos sur un devis. (pour l'affichage de sa fiche)
    }

    /**
     * Méthode qui récupère les produits liés à un devis
     * @param string $id L'id du devis
     * @param int $nbprod Précise si on veut un ou tous les produits
     * @param string $prod Id du produit désiré (si on en veut un seul)
     * @return array Le(s) produit(s) du devis
     */
    public function getProduitsFromID($id, $nbprod = '0', $prod = '') {
	$prod=urldecode($prod);
	if($nbprod == '0') {
	    $this->makeRequeteFree("SELECT *,
			(select prixF*(1-remiseF/100) as PFourn from produit_fournisseur where produit_id = id_prod order by PFourn ASC limit 0,1) as PF 
			FROM devis_produit
			LEFT JOIN produit ON produit.id_prod = devis_produit.id_produit
			LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
			WHERE id_devis = '".trim($id)."' ;");
	}
	else {	//Si je ne veux qu'un seul produit d'un devis...
	    $this->makeRequeteFree("SELECT *
			FROM devis_produit
			LEFT JOIN produit ON produit.id_prod = devis_produit.id_produit
			LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
			WHERE id_devis = '".trim($id)."' AND id_produit = '".trim($prod)."' ");
	}
	return $this->process2();
    }

    /**
     * Méthode qui récupère le statut d'un devis
     * @param string $id L'id du devis dont on veut le statut
     * @return array Le statut du devis
     */
    public function getStatusDevis($id) {
	$this->makeRequeteFree("SELECT status_dev from devis where id_dev = '".$id."' ; ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le statut d'un devis
     * @param string $id L'id du devis dont on veut le statut
     * @return array Le statut du devis
     */
    public function getAllStatusDevis() {
	$out = array();
	$this->makeRequeteFree("SELECT * from ref_statusdevis;");
	$status = $this->process2();
	if(is_array($status[1]))
	    foreach($status[1] as $v)
		$out[$v['id_stdev']] = $v['nom_stdev'];
	return $out;
    }

    /**
     * Méthode qui récupère les informations d'un produit
     * @param string $id L'id du produit
     * @return array Les informations du produit
     */
    public function getInfoProduits($id) {
	$this->makeRequeteFree("SELECT *
		FROM produit
		WHERE id_prod = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations d'un produit d'un devis
     * @param string $id L'id du produit
     * @return array Les infos du produit
     */
    public function getInfoProduitsPlus($id) {
	$this->makeRequeteFree( "SELECT *
		FROM devis_produit
		LEFT JOIN produit ON produit.id_prod = devis_produit.id_produit
		WHERE id_produit = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations de l'entreprise liée à une affaire
     * @param string $id L'id de l'affaire dont on veut les infos entreprise
     * @return array Les informations de l'entreprise de notre affaire
     */
    public function getEntrepriseData($id) {
	$this->makeRequeteFree("SELECT *
		FROM affaire
		LEFT JOIN entreprise on entreprise.id_ent = affaire.entreprise_aff
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		WHERE id_aff = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations d'un contact
     * @param int $id L'id du contact
     * @return array Les informations du contact
     */
    public function getContactData($id) {
	$this->makeRequeteFree("SELECT *
		FROM contact
		WHERE id_cont = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations des devis liés à une affaire
     * @param string $id L'id de l'affaire
     * @return array Les devis liés à l'affaire
     */
    public function getDataFromAffaire($id) {
	$this->makeRequeteFree("SELECT *
		FROM devis
		WHERE affaire_dev = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui insert un produit dans la base
     * @param array $data Les données à insérer
     * @return array Indique si la requète s'est bien déroulée
     */
    public function addProduit($data) {
	$sql = $this->makeRequeteInsert('produit',$data);
	Logg::loggerInfo('devisModel::addProduit() ~ ajout du produit '.$data['id_prod'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	return $rs;
    }

    /**
     * Méthode qui insert un produit dans un devis
     * @param array $data Les données à insérer
     * @param bool $actu Précise si on veut mettre un entré d'actualite
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insertProduits($data, $actu = true) {
	if($data['quantite'] == NULL)
	    $data['quantite'] = 1;
	if($data['remise'] == NULL)
	    $data['remise'] = 0;
	$data['quantite'] = prepareNombreTraitement($data['quantite']);
	$data['prix']=prepareNombreTraitement($data['prix']);
	$data['remise']=prepareNombreTraitement($data['remise']);
	$sql = $this->makeRequeteInsert('devis_produit', $this->bindVarDevisProduit($data));
	Logg::loggerInfo('devisModel::insertProduits() ~ ajout du produit '.$data['id_produit'].' pour le devis '.$data['id_devis'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	$sommeHT = $this->updateMontant($data['id_devis']);
	if($rs[0] and $actu)
	    $this->addActualite($data['id_devis'], 'free','Ajout du produit '.$data['id_produit'].' pour le devis '.$data['id_devis'],'Produit '.$data['id_produit'].' x '.$data['quantite'].' x '.formatCurencyDisplay($data['prix']).' ajouté.<br/>Le devis à maintenant une valeur de '.formatCurencyDisplay($sommeHT),'',false);
	return $rs;
    }

    /**
     * Méthode qui supprime un/des produit(s) d'un devis
     * @param array $data L'id du devis dans un tableau
     * @param string $id_produit L'id du produit si on en enlève un seul
     * @return array Indique si la requête s'est bien déroulée
     */
    public function deleteProduits($data, $id_produit = NULL) {
	if($id_produit != NULL)
	    $sql = $this->makeRequeteFree("delete from devis_produit where id_devis='".$data['id_devis']."' AND id_produit='".$id_produit."';");
	else $sql = $this->makeRequeteFree("delete from devis_produit where id='".$data['id']."';");
	Logg::loggerInfo('devisModel::deleteProduits() ~ suppression du produit '.$id_produit.' pour le devis '.$data['id_devis'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	$sommeHT = $this->updateMontant($data['id_devis']);
	if($rs[0])
	    $this->addActualite($data['id_devis'], 'free','Suppression du produit '.$data['id_produit'].' pour le devis '.$data['id_devis'],'Le devis à maintenant une valeur de '.formatCurencyDisplay($sommeHT),'',false);
	return $rs;
    }

    /**
     * Méthode qui met à jour un produit d'un devis
     * @param array $data Les données mises à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function updateProduits($data) {
	if($data['quantite'] == NULL)
	    $data['quantite'] = 1;
	if($data['remise'] == NULL)
	    $data['remise'] = 0;
	$data['quantite'] = prepareNombreTraitement($data['quantite']);
	$data['prix']=prepareNombreTraitement($data['prix']);
	$data['remise']=prepareNombreTraitement($data['remise']);
	$sql = $this->makeRequeteUpdate('devis_produit', 'id', $data['id'], $this->bindVarDevisProduit($data));
	Logg::loggerInfo('devisModel::updateProduits() ~ modification du produit pour le devis (id='.$data['id_devis'].')',$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	$sommeHT = $this->updateMontant($data['id_devis']);
	if($rs[0] and $actu)
	    $this->addActualite($data['id_devis'], 'free','Modification du produit '.$data['id_produit'].' pour le devis '.$data['id_devis'],'Produit '.$data['id_produit'].' x '.$data['quantite'].' x '.formatCurencyDisplay($data['prix']).' modifié.<br/>Le devis à maintenant une valeur de '.formatCurencyDisplay($sommeHT),'',false);
	return $rs;
    }

    /**
     * Méthode qui met à jour le montant d'un devis
     * @param string $id L'id du devis
     */
    private function updateMontant($id) {
	$prod = $this->getProduitsFromID($id);
	$sommeHT = 0;
	foreach($prod[1] as $v) {
	    $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
	}
	$this->update(array('sommeHT_dev' => $sommeHT), $id);
	return $sommeHT;
    }

    /**
     * Méthode qui insert un devis (et ses produits) dans la base
     * @param array $data Les données à insérer
     * @param string $fait Précise l'origine de la demande
     * @param array $prod Les produits éventuels à ajouter au devis (utile dans un clonage)
     * @param string $id Id du devis original en cas de clonage
     * @return array Indique si la/les requête(s) s'est/se sont biend éroulée(s)
     */
    public function insert($data, $fait = 'creation', $prod = array(), $id='') {
	$data['datemodif_dev']=date('Y-m-d');
	$data['daterecord_dev']=date('Y-m-d');
	$data['tva_dev']=formatCurencyDatabase($data['tva_dev'],1);
	if($fait == 'creation') {
	    $sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarDevis($data));
	    Logg::loggerInfo('devisModel::insert() ~ ajout du devis '.$data['id_dev'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0])
		$this->addActualite($data['id_dev'], 'add');
	    return $rs;
	}
	elseif($fait == 'cloner') {
	    $sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarDevis($data));
	    Logg::loggerInfo('devisModel::insert() ~ ajout du devis '.$data['id_dev'].' par clonage',$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    foreach($prod as $v) {
		$temp['id_devis'] = $data['id_dev'];
		$temp['id_produit'] = $v['id_produit'];
		$temp['desc'] = $v['desc'];
		$temp['quantite'] = $v['quantite'];
		$temp['remise'] = $v['remise'];
		$temp['prix'] = $v['prix'];
		$this->insertProduits($temp, false);
	    }
	    if($rs[0])
		$this->addActualite($data['id_dev'], 'clone', '', '', $id);
	    return $rs;
	}
	elseif($fait == 'express') {

	    $temp = affaireModel::affaireGenerateID();
	    if(!array_key_exists($this->sqlIdField,$data)) $data[$this->sqlIdField] = self::createId($temp);
	    $_SESSION['devisExpress']['id'] = $data[$this->sqlIdField];
	    $data['affaire_dev'] = $temp;
	    $sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarDevis($data));
	    Logg::loggerInfo('devisModel::insert() ~ ajout du devis '.$data['id_dev'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    $aff['id_aff'] = $temp;
	    $aff['entreprise_aff'] = $data['entreprise_dev'];
	    $aff['contact_aff'] = $data['contact_dev'];
	    $aff['actif_aff'] = '1';
	    $aff['archived_aff'] = '0';
	    $aff['titre_aff'] = 'Affaire';
	    $aff['modif_aff'] = date('Y-m-d');
	    $aff['detect_aff'] = date('Y-m-d');
	    $aff['status_aff'] = '3';
	    $aff['typeproj_aff'] = '1';
	    $aff['commercial_aff'] = $data['commercial_dev'];
	    $aff['technique_aff']  = $data['commercial_dev'];
	    affaireModel::createNewAffaireInDB($aff,TRUE, 'creation', '');
	    foreach($prod as $v) {
		$tempon['id_devis'] = $data['id_dev'];
		$tempon['id_produit'] = $v['id_produit'];
		$tempon['desc'] = $v['desc'];
		$tempon['quantite'] = $v['quantite'];
		$tempon['remise'] = $v['remise'];
		$tempon['prix'] = $v['prix'];
		$resultat = $this->insertProduits($tempon, false);
	    }
	    if($rs[0])
		$this->addActualite($data['id_dev'], 'add');
	    return $rs;
	}
    }

    /**
     * Méthode qui met à jour un devis
     * @param array $data Les données mises à jour
     * @param string $id_dev L'id du devis à mettre à jour
     * @param boolean $addActu Précise si on ajoute une actualité ou pas
     * @param string $cplmt Permet d'ajouter un complément à l'actualité
     * @return array Indique si la requête s'est bien déroulée
     */
    public function update($data,$id_dev,$addActu = true, $cplmt = '') {
	$data['datemodif_dev']=date('Y-m-d');
	if(array_key_exists('tva_dev', $data))
	    $data['tva_dev']=formatCurencyDatabase($data['tva_dev'],1);
	if(array_key_exists('sommeHT_dev', $data))
	    $data['sommeHT_dev']=prepareNombreTraitement($data['sommeHT_dev']);
	if(!array_key_exists($this->sqlIdField,$data)) {
	    $sql = $this->makeRequeteUpdate($this->sqlTableName,"id_dev",$id_dev,$this->bindVarDevis($data));
	    Logg::loggerInfo('devisModel::update() ~ modification du devis '.$id_dev,$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0] and $data['status_dev'] != '' and $addActu)
		$this->addActualite($id_dev, 'update', '', $cplmt,'',false);
	    return $rs;
	}
	else {
	    $sql = $this->makeRequeteUpdate($this->sqlTableName,"id_dev",$data['id_dev'],$this->bindVarDevis($data));
	    Logg::loggerInfo('devisModel::update() ~ modification du devis '.$data['id_dev'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0] and $data['status_dev'] != '')
		$this->addActualite($din['id_dev'], 'update', '', $cplmt,'',false);
	    return $rs;
	}
    }

    /**
     * Méthode qui supprime un devis de la base
     * @param string $id_dev L'id du devis à supprimer
     * @return array Indique si la / les requête(s) s'est / se sont bien déroulée(s)
     */
    public function delete($id_dev) {
	$sql = $this->makeRequeteFree("DELETE FROM $this->sqlTableName WHERE id_dev = '".$id_dev."'");
	Logg::loggerInfo('devisModel::delete() ~ suppression du devis '.$id_dev,$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0])
	    $this->addActualite($data['id_devis'], 'delete');
	return $rs;
    }

    /**
     * Méthode qui créé un id de devis depuis un id d'affaire
     * @param string $id_aff L'id de l'affaire du devis
     * @return string L'id du devis
     */
    public function createId($id_aff) {
	$this->makeRequeteFree("SELECT COUNT(id_dev) AS NOW FROM `devis` WHERE id_dev LIKE '%".$id_aff."%' ");
	$res = $this->process();
	$lastid = $res[0]['NOW'];
	if($lastid > 0) {
	    $lastid++;
	    if($lastid < 10)
		$lastid = "-0".$lastid;
	    else $lastid = "-".$lastid;
	}
	else $lastid = "-01";
	$id_aff .= $lastid;
	return $id_aff;
    }

    /**
     * Méthode qui récupère des informations utiles à la création express de devis
     * @param string $lettre Le début/morceau d'un nom d'entreprise de la base
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultat
     * @return array La liste des entreprises et contacts correspondant à la recherche
     */
    public function getDataForExpress($lettre, $from = 0, $limit = 5) {
	$req = "SELECT nom_ent, id_ent, add1_ent, add2_ent, cp_ent, ville_ent, pays_ent, nom_tyent from entreprise
				LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent WHERE nom_ent LIKE '".$lettre."%' ORDER BY nom_ent LIMIT $from , $limit ";
	$this->makeRequeteFree($req);
	$res['ent'] = $this->process();
	$req2 = "SELECT nom_cont, mail_cont, prenom_cont, id_cont, entreprise_cont from contact LEFT JOIN entreprise ON id_ent = entreprise_cont WHERE nom_ent LIKE '".$lettre."%' ORDER BY nom_cont ";
	$this->makeRequeteFree($req2);
	$res['cont'] = $this->process();
	return $res;
    }

    /**
     * Méthode qui récupère des devis triés par montant
     * @param string $maxi Précise si on veut un COUNT ou donne le nombre maximum de résultats souhaités
     * @param int $from L'index du premier résultat
     * @param string $plus Un complément à la requête SQL
     * @return array Les devis triés par montant
     */
    public function getDataByMontant($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY sommeHT_dev DESC ".$plus);
	else $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ".$plus." ORDER BY sommeHT_dev DESC, id_dev DESC LIMIT $from , $maxi");
	return $this->process2();
    }

    /**
     * Méthode qui récupère des devis triés par date de création
     * @param string $maxi Précise si on veut un COUNT ou donne le nombre maximum de résultats souhaités
     * @param int $from L'index du premier résultat
     * @param string $plus Un complément à la requête SQL
     * @return array Les devis triés par date de création
     */
    public function getDataByCreation($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY daterecord_dev DESC ".$plus);
	else $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ".$plus." ORDER BY daterecord_dev DESC, id_dev DESC LIMIT $from , $maxi");
	return $this->process2();
    }

    /**
     * Méthode qui récupère des devis triés par entreprise
     * @param string $maxi Précise si on veut un COUNT ou donne le nombre maximum de résultats souhaités
     * @param int $from L'index du premier résultat
     * @param string $plus Un complément à la requête SQL
     * @return array Les devis triés par entreprise
     */
    public function getDataByEntreprise($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY entreprise_dev ASC ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (entreprise_dev IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName LEFT JOIN entreprise ON entreprise.id_ent=devis.entreprise_dev ".$plus." ORDER BY temp DESC, entreprise.nom_ent ASC, id_dev DESC LIMIT $from , $maxi");
	return $this->process2();
    }

    /**
     * Méthode qui récupère des devis triés par contact
     * @param string $maxi Précise si on veut un COUNT ou donne le nombre maximum de résultats souhaités
     * @param int $from L'index du premier résultat
     * @param string $plus Un complément à la requête SQL
     * @return array Les devis triés par mcontact
     */
    public function getDataByContact($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) AS counter FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY contact_dev ASC ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (contact_dev IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName LEFT JOIN contact ON contact.id_cont=devis.contact_dev ".$plus." ORDER BY temp DESC, contact.nom_cont ASC, id_dev DESC LIMIT $from , $maxi");
	return $this->process2();
    }

    /**
     * Méthode qui met à jour le montant du devis
     * @param string $id_dev L'id du devis à mettre à jour
     */
    static function updateTotal($id_dev) {
	// on récupére les info de devis
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT * FROM `devis_produit` WHERE id_devis = '".$id_dev."'");
	$produits = $bddtmp->process();

	if (count($produits) > 0) {
	    // On prépare les tableaux pour le JS produits
	    $total = 0;
	    foreach ($produits as $key => $produit) {
		$Sum=($produit['quantite']*$produit['prix'])-((($produit['quantite']*$produit['prix'])*$produit['remise'])/100);
		$total = $total+round($Sum,2);

	    }
	    $var_out['sommeHT_dev'] = round($total,2);
	    $sql = $bddtmp->makeRequeteUpdate('devis','id_dev',$id_dev,$var_out);
	    Logg::loggerInfo('devisModel::updateTotal() ~ mise à jour du total pour le devis '.$id_dev,$sql,__FILE__.'@'.__LINE__);
	    $bddtmp->process();
	}
    }

    /**
     * Méthode statique de création d'un id de devis à partir d'un id d'affaire
     * @param string $id_aff L'id d'affaire servant de base
     * @return string L'id du devis
     */
    static function DevisGenerateID($id_aff) {
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT COUNT(id_dev) AS NOW FROM `devis` WHERE affaire_dev = '".$id_aff."'");
	$res = $bddtmp->process();
	$lastid = $res[0]['NOW'];
	if($lastid > 0) {
	    $lastid++;
	    if($lastid < 10) {
		$lastid = "-0".$lastid;
	    }
	    else {
		$lastid = "-".$lastid;
	    }
	}
	else {
	    $lastid = "-01";
	}
	$id_aff .= $lastid;
	return $id_aff;
    }

    /**
     * Méthode qui récupère le nombre total de devis à renouveller
     * @return int Le nombre de devis à renouveller
     */
    public function getTotalDevisToRenew() {
	$this->makeRequeteFree("SELECT COUNT(*) AS counter FROM devis
						  LEFT JOIN entreprise ON entreprise_dev = id_ent ,
						  devis_renew
						  LEFT JOIN ref_renewperiode ON ref_renewperiode.id_refrnw = devis_renew.cyclerenew
						  WHERE  AND id_dev = id_devis");
	$r = $this->process2();
	if($r[0])	return $r[1][0]['counter'];
	else 		return 0;
    }

    /**
     * Méthode qui récupère la liste des devis à renouveller
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultats souhaités
     * @param string $order Précise l'ordre de tri des résultats
     * @return array Les devis à renouveller
     */
    public function getDevisToRenew($from = '0', $limit ='30', $order='ORDER BY id_dev DESC') {
	$this->makeRequeteFree("SELECT  *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
						  FROM devis
						  LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
						  LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
						  LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
						  LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
						  LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
						  LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
						  LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
						  LEFT JOIN user ON user.login = devis.commercial_dev ,
						  devis_renew
						  LEFT JOIN ref_renewperiode ON ref_renewperiode.id_refrnw = devis_renew.cyclerenew
						  WHERE devis.id_dev = devis_renew.id_devis LIMIT $from , $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le nombre total de devis gagnés
     * @return int le nombre de devis gagnés
     */
    public function getTotalDevisGagne() {
	$this->makeRequeteFree("SELECT COUNT(*) AS counter FROM devis WHERE status_dev = 6");
	$r = $this->process2();
	if($r[0])	return $r[1][0]['counter'];
	else		return 0;
    }

    /**
     * Méthode qui récupère la liste des devis gagnés
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultats
     * @param string $order Précise l'ordre de tri
     * @return array Les devis gagnés
     */
    public function getDevisGagne($from = '0', $limit ='30', $order='ORDER BY id_dev DESC') {
	$this->makeRequeteFree("SELECT  *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
						  FROM devis
						  LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
						  LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
						  LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
						  LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
						  LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
						  LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
						  LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
						  LEFT JOIN user ON user.login = devis.commercial_dev
						  WHERE status_dev = 6 LIMIT $from , $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le nombre total de devis perdu
     * @return int le nombre de devis perdu
     */
    public function getTotalDevisPerdu() {
	$this->makeRequeteFree("SELECT COUNT(*) AS counter FROM devis WHERE status_dev = 5");
	$r = $this->process2();
	if($r[0])	return $r[1][0]['counter'];
	else		return 0;
    }

    /**
     * Méthode qui récupère la liste des devis perdu
     * @param int $from L'index du premier résultat
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param string $order Précise l'ordre de tri des résultats
     * @return array Les devis perdus
     */
    public function getDevisPerdu($from = '0', $limit ='30', $order='ORDER BY id_dev DESC') {
	$this->makeRequeteFree("SELECT  *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
						  FROM devis
						  LEFT JOIN entreprise ON entreprise.id_ent = devis.entreprise_dev
						  LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
						  LEFT JOIN contact c2 ON c2.id_cont = devis.contact_achat_dev
						  LEFT JOIN contact c1 ON c1.id_cont = devis.contact_dev
						  LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
						  LEFT JOIN ref_statusdevis rsd ON rsd.id_stdev = devis.status_dev
						  LEFT JOIN ref_pays rfp ON rfp.id_pays = devis.paysdelivery_dev
						  LEFT JOIN user ON user.login = devis.commercial_dev
						  WHERE status_dev = 5 LIMIT $from , $limit ");
	return $this->process2();
    }



    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributeDevisInDB($id_dev,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('titre_dev', $newAttribList) or
		array_key_exists('tva_dev', $newAttribList) or
		array_key_exists('commercial_dev', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_dev != '' and $hasChange) {
	    $bddtmp = new DevisModel();
	    $bddtmp->makeRequeteSelect('devis','id_dev',$id_dev);
	    $dev = $bddtmp->process();
	    $dev = $dev[0];
	    $propMess = '';
	    if($newAttribList['titre_dev'] != '') {
		$propMess .= "<br/>titre du devis: \"".$newAttribList['titre_dev'].'", ';
		$newData['titre_dev'] = $newAttribList['titre_dev'];
	    }
	    if($newAttribList['tva_dev'] != '') {
		$propMess .= "<br/>Taux de TVA: \"".$newAttribList['tva_dev'].'", ';
		$newData['tva_dev'] = $newAttribList['tva_dev'];
	    }
	    if($newAttribList['commercial_dev'] != '') {
		$propMess .= "<br/>Commercial: \"".$newAttribList['commercial_dev'].'", ';
		$newData['commercial_dev'] = $newAttribList['commercial_dev'];
	    }
	    $propMess = substr($propMess, 0, -2);
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField, $id_dev, $bddtmp->bindVarDevis($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_dev, 'free', 'Changement d\'attribut', 'changement par lot des propriétés suivantes : '.$propMess, '',false);
	    Logg::loggerInfo('devisModel::changeAttributeDevisInDB() ~ changement d\'attribut du devis '.$id_dev,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('devisModel::changeAttributeDevisInDB() ~ impossible de changer les attributs du devis '.$id_dev,'',__FILE__.'@'.__LINE__);
    }


    static function archivateDevisInDB($id_dev,$newAttribList = array()) {
	if ($id_dev != '') {
	    $bddtmp = new DevisModel();
	    $bddtmp->makeRequeteSelect('devis','id_dev',$id_dev);
	    $dev = $bddtmp->process();
	    $dev = $dev[0];
	    $newData['status_dev'] = '7';

	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField, $id_dev, $bddtmp->bindVarDevis($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_dev, 'archive', '', 'changement par lot', '',false);
	    Logg::loggerInfo('devisModel::markEnvoyeDevisInDB() ~ Devis '.$id_dev.' marqué comme archivé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('devisModel::markEnvoyeDevisInDB() ~ impossible de marqué comme archivé le devis '.$id_dev,'',__FILE__.'@'.__LINE__);
    }
    static function markDeleteDevisInDB($id_dev,$newAttribList = array()) {
	if ($id_dev != '') {
	    $bddtmp = new DevisModel();
	    $bddtmp->makeRequeteSelect('devis','id_dev',$id_dev);
	    $dev = $bddtmp->process();
	    $dev = $dev[0];
	    if($newAttribList['commentaireDelete'] != '')
		$newData['commentaire_dev'] = ($dev['commentaire_dev'] != '') ? $dev['commentaire_dev'].'. '.$newAttribList['commentaireDelete'] : $newAttribList['commentaireDelete'];
	    $newData['status_dev'] = '2';

	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField, $id_dev, $bddtmp->bindVarDevis($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_dev, 'delete', '', 'suppression par lot', '',false);
	    Logg::loggerInfo('devisModel::markDeleteDevisInDB() ~ Devis '.$id_dev.' marqué comme supprimé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('devisModel::markDeleteDevisInDB() ~ impossible de marqué comme supprimé le devis '.$id_dev,'',__FILE__.'@'.__LINE__);
    }

    static function markEnvoyeDevisInDB($id_dev,$newAttribList = array()) {
	if ($id_dev != '') {
	    $bddtmp = new DevisModel();
	    $bddtmp->makeRequeteSelect('devis','id_dev',$id_dev);
	    $dev = $bddtmp->process();
	    $dev = $dev[0];
	    if($newAttribList['complementdeliveryEnvoye'] != '')
		$newData['complementdelivery_dev'] = ($dev['complementdelivery_dev'] != '') ? $dev['complementdelivery_dev'].'. '.$newAttribList['complementdeliveryEnvoye'] : $newAttribList['complementdeliveryEnvoye'];
	    if($newAttribList['commentaireEnvoye'] != '')
		$newData['commentaire_dev'] = ($dev['commentaire_dev'] != '') ? $dev['commentaire_dev'].'. '.$newAttribList['commentaireEnvoye'] : $newAttribList['commentaireEnvoye'];
	    $newData['status_dev'] = '4';

	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField, $id_dev, $bddtmp->bindVarDevis($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_dev, 'send', '', 'traitement par lot', '',false);
	    Logg::loggerInfo('devisModel::markEnvoyeDevisInDB() ~ Devis '.$id_dev.' marqué comme envoyé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('devisModel::markEnvoyeDevisInDB() ~ impossible de marqué comme envoyé le devis '.$id_dev,'',__FILE__.'@'.__LINE__);
    }


    static function markPerduDevisInDB($id_dev,$newAttribList = array()) {
	if ($id_dev != '') {
	    $bddtmp = new DevisModel();
	    $bddtmp->makeRequeteSelect('devis','id_dev',$id_dev);
	    $dev = $bddtmp->process();
	    $dev = $dev[0];
	    if($newAttribList['commentairePerdu'] != '')
		$newData['commentaire_dev'] = ($dev['commentaire_dev'] != '') ? $dev['commentaire_dev'].'. '.$newAttribList['commentairePerdu'] : $newAttribList['commentairePerdu'];
	    $newData['status_dev'] = '5';

	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField, $id_dev, $bddtmp->bindVarDevis($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_dev, 'free', 'Devis '.$id_dev.' perdu', 'Marqué comme perdu lors d\'un traitement par lot', '',false);
	    Logg::loggerInfo('devisModel::markPerduDevisInDB() ~ Devis '.$id_dev.' marqué comme perdu',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('devisModel::markPerduDevisInDB() ~ impossible de marqué comme perdu le devis '.$id_dev,'',__FILE__.'@'.__LINE__);
    }

    static function markReinitDevisInDB($id_dev,$newAttribList = array()) {
	if ($id_dev != '') {
	    $bddtmp = new DevisModel();
	    $bddtmp->makeRequeteSelect('devis','id_dev',$id_dev);
	    $dev = $bddtmp->process();
	    $dev = $dev[0];
	    $newData['status_dev'] = '1';

	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField, $id_dev, $bddtmp->bindVarDevis($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_dev, 'free', 'Devis '.$id_dev.' reinitialisé', 'Re-initialisé lors d\'un traitement par lot', '',false);
	    Logg::loggerInfo('devisModel::markReinitDevisInDB() ~ Re-initialisation du devis '.$id_dev,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('devisModel::markReinitDevisInDB() ~ impossible de Re-initialiser le devis '.$id_dev,'',__FILE__.'@'.__LINE__);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table devis
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarDevis($data) {
	return $this->bindVar($data,$this->availableFields);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table devis_produit
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFieldsProduit
     */
    public function bindVarDevisProduit($data) {
	return $this->bindVar($data,$this->availableFieldsProduit);
    }
}
?>