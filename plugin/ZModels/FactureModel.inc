<?php
include_once('GeneralModel.inc');
/**
 * Classe gérant toutes les requètes SQL en rapport avec les factures.
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class factureModel extends GeneralModel {

    private $availableFieldsProduit;

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'facture';
	$this->sqlIdField = 'id_fact';
	$this->availableFields = array(
		'id_fact','entreprise_fact','contact_fact','commande_fact',
		'sommeHT_fact','commentreglement_fact','modereglement_fact','condireglement_fact','BDCclient_fact','titre_fact',
		'datemodif_fact','daterecord_fact','dateenvoi_fact','datereglement_fact','status_fact',
		'nomentreprise_fact','add1_fact','add2_fact',
		'ville_fact','cp_fact','pays_fact','numeroTVA_fact',
		'envoi_fact','commercial_fact','contact_achat_fact', 'tauxTVA_fact', 'type_fact', 'ren_fact', 'commentaire_fact');
	$this->availableFieldsProduit = array('id_facture', 'id_produit', 'desc', 'quantite', 'remise', 'prix', 'id');

	parent::__construct();
    }

    /**
     * Méthode de recherchedes factures
     * @param string $search Le contenu de la recherche
     * @param int $limit Le nombre maximum de résultats souhaités
     * @param int $from L'index du premier résultat
     * @param string $type Le type Facture ou Avoir (défaut Facture)
     * @param string $plus D'éventuel(s) complément(s) à la requête SQL
     * @return array Les résultats
     */
    public function getDataForSearch($search,$limit = 10, $from = 0, $type = 'Facture', $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	if($limit == 'ALL') {
	    $this->makeRequeteFree("SELECT COUNT(*) " .
		    "FROM facture f " .
		    "LEFT JOIN entreprise ON entreprise.id_ent = f.entreprise_fact
			 LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			 LEFT JOIN contact ON contact.id_cont = f.contact_fact " .
		    "WHERE ( nom_ent $qTag OR ville_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag " .
		    "OR mail_cont $qTag OR id_fact $qTag OR titre_fact $qTag OR commande_fact $qTag )" .
		    "AND type_fact LIKE '%".$type."%' ".$plus);
	}
	else {
	    $this->makeRequeteFree("SELECT titre_fact, nom_ent, nom_cont, prenom_cont, civ_cont, id_fact, type_fact " .
		    "FROM facture f " .
		    "LEFT JOIN entreprise ON entreprise.id_ent = f.entreprise_fact
			 LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			 LEFT JOIN contact ON contact.id_cont = f.contact_fact " .
		    "WHERE ( nom_ent $qTag OR ville_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag " .
		    "OR mail_cont $qTag OR id_fact $qTag OR titre_fact $qTag OR commande_fact $qTag )" .
		    "AND type_fact LIKE '%".$type."%' ".$plus.
		    "ORDER BY id_fact DESC " .
		    "LIMIT $from , $limit");
	}
	return $this->process2();
    }

    /**
     * Méthode qui permet de faire une recherche avec le moteur du web
     * @param string $search Le champ recherché (inutilisé dans cette méthode)
     * @param int $from L'index du premier résultat désiré
     * @param int $limit Le nombre maximum de résultats désiré
     * @param string $order L'ordre de retour des résultats
     * @param array $conditions Les champs et les valeurs recherchés
     * @return array Les données recherchées
     */
    public function getDataForSearchWeb($search, $from = '0', $limit ='30', $order='ORDER BY id_fact DESC', $conditions = '') {

	if(is_array($conditions)) {
	    $where = " WHERE ";
	    foreach($conditions as $k => $v) {
		if($k == 'cp_ent')
		    $where .= "$k LIKE '".$v."%' AND ";
		elseif($k == 'sommeHT_fact')
		    $where .= " $k >= '$v' AND ";
		elseif($k == 'sommeHT_fact2')
		    $where .= " sommeHT_fact < '$v' AND ";
		elseif($k == 'status_fact') {
		    $where .= "$k = '".$v."' AND ";
		    $archives = $v;
		}
		elseif($k == 'status_fact')
		    $where .= "$k = '".$v."' AND ";
		elseif($k == 'entreprise_fact')
		    $where .= "$k = '".$v."' AND ";
		else
		    $where .= "$k LIKE '%".$v."%' AND ";
	    }
	    $where = substr($where, 0, (strlen($where)-4));
	}

	if($limit != 'ALL')
	    $this->makeRequeteFree("SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat
			FROM facture
			LEFT JOIN entreprise ON entreprise.id_ent = facture.entreprise_fact
			LEFT JOIN ref_activite ON ref_activite.id_act = entreprise.activite_ent
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact c2 ON c2.id_cont = facture.contact_achat_fact
			LEFT JOIN contact c1 ON c1.id_cont = facture.contact_fact
			LEFT JOIN ref_condireglement ON ref_condireglement.id_condreg = facture.condireglement_fact
			LEFT JOIN ref_modereglement ON ref_modereglement. id_modereg = facture.modereglement_fact
			LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
			LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
			LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
			LEFT JOIN ref_statusdevis ON ref_statusdevis.id_stdev = devis.status_dev
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statusaffaire ON ref_statusaffaire.id_staff = affaire.status_aff
			LEFT JOIN ref_typeproj ON ref_typeproj.id_typro = affaire.projet_aff
			LEFT JOIN ref_statusfacture rsd ON rsd.id_stfact = facture.status_fact
			LEFT JOIN ref_pays rfp ON rfp.id_pays = facture.pays_fact
			LEFT JOIN user ON user.login = facture.commercial_fact $where $order LIMIT $from , $limit ");
	else
	    $this->makeRequeteFree("SELECT COUNT(*)
			FROM facture
			LEFT JOIN entreprise ON entreprise.id_ent = facture.entreprise_fact
			LEFT JOIN ref_activite ON ref_activite.id_act = entreprise.activite_ent
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact c2 ON c2.id_cont = facture.contact_achat_fact
			LEFT JOIN contact c1 ON c1.id_cont = facture.contact_fact
			LEFT JOIN ref_condireglement ON ref_condireglement.id_condreg = facture.condireglement_fact
			LEFT JOIN ref_modereglement ON ref_modereglement. id_modereg = facture.modereglement_fact
			LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
			LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
			LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
			LEFT JOIN ref_statusdevis ON ref_statusdevis.id_stdev = devis.status_dev
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statusaffaire ON ref_statusaffaire.id_staff = affaire.status_aff
			LEFT JOIN ref_typeproj ON ref_typeproj.id_typro = affaire.projet_aff
			LEFT JOIN ref_statusfacture rsd ON rsd.id_stfact = facture.status_fact
			LEFT JOIN ref_pays rfp ON rfp.id_pays = facture.pays_fact
			LEFT JOIN user ON user.login = facture.commercial_fact $where $order ");
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
			FROM facture
			LEFT JOIN entreprise ON entreprise.id_ent = facture.entreprise_fact
			LEFT JOIN ref_activite ON ref_activite.id_act = entreprise.activite_ent
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact c2 ON c2.id_cont = facture.contact_achat_fact
			LEFT JOIN contact c1 ON c1.id_cont = facture.contact_fact
			LEFT JOIN ref_condireglement ON ref_condireglement.id_condreg = facture.condireglement_fact
			LEFT JOIN ref_modereglement ON ref_modereglement. id_modereg = facture.modereglement_fact
			LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
			LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
			LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
			LEFT JOIN ref_statusdevis ON ref_statusdevis.id_stdev = devis.status_dev
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statusaffaire ON ref_statusaffaire.id_staff = affaire.status_aff
			LEFT JOIN ref_typeproj ON ref_typeproj.id_typro = affaire.projet_aff
			LEFT JOIN ref_statusfacture rsd ON rsd.id_stfact = facture.status_fact
			LEFT JOIN ref_pays rfp ON rfp.id_pays = facture.pays_fact
			LEFT JOIN user ON user.login = facture.commercial_fact
		WHERE id_fact IN $sqlList
		ORDER BY id_fact ASC,nom_ent ASC");
	return $this->process2();
    }


    /**
     * Méthode qui s'occupe de l'historique des factures
     * @param int $limit Le nombre maximum de résultat
     * @param string $type Le type des données souhaités (COUNT/*)
     * @param string $order L'ordre de tri
     * @return array les données correspondantes
     */
    public function getDataForHistoriqueVisit($limit ='10', $type = '', $order='ORDER BY id_fact DESC') {
	if(array_key_exists('historiqueVisite',$_SESSION) and
		array_key_exists('facture',$_SESSION['historiqueVisite']) and
		count($_SESSION['historiqueVisite']['facture']) > 0) {
	    $list = '';
	    foreach($_SESSION['historiqueVisite']['facture'] as $id)
		$list .= '\''.$id.'\', ';
	    $list = '('.substr($list,0,-2).')';
	}
	else $list = '(\'fakeNull\')';
	if($type == 'COUNT')
	    $select = 'SELECT COUNT(*) AS counter';
	else $select = 'SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat, c2.mail_cont AS mail_achat, c2.fax_cont AS fax_achat';
	$this->makeRequeteFree($select." FROM facture
			LEFT JOIN entreprise ON entreprise.id_ent = facture.entreprise_fact
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact c2 ON c2.id_cont = facture.contact_achat_fact
			LEFT JOIN contact c1 ON c1.id_cont = facture.contact_fact
			LEFT JOIN commande ON commande.id_cmd = facture.commande_fact
			LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
			LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
			LEFT JOIN ref_statusfacture rsd ON rsd.id_stfact = facture.status_fact
			LEFT JOIN ref_pays rfp ON rfp.id_pays = facture.pays_fact
			LEFT JOIN user ON user.login = facture.commercial_fact WHERE id_fact IN $list $order LIMIT 0, $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le status d'une facture
     * @param int $id L'id de al facture
     * @return array Les informations demandées
     */
    public function getStatusFacture($id) {
	$this->makeRequeteFree("select status_fact, type_fact, tauxTVA_fact from facture where id_fact = '".$id."' ; ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le statut d'une facture
     * @param string $id L'id de la facture dont on veut le statut
     * @return array Le statut de la facture
     */
    public function getAllStatusFacture() {
	$out = array();
	$this->makeRequeteFree("SELECT * from ref_statusfacture;");
	$status = $this->process2();
	if(is_array($status[1]))
	    foreach($status[1] as $v)
		$out[$v['id_stfact']] = $v['nom_stfact'];
	return $out;
    }

    /**
     * Méthode qui recherche des produits
     * @param string $search Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat
     * @param int $from L'index du premier résultat
     * @return array Les données
     */
    public function getDataForSearchProd($search,$limit = 10, $from = 0) {
	$qTag = " LIKE '%".$search."%' ";
	$this->makeRequeteFree("SELECT  nom_prod, id_prod, nom_prodfam, treePathKey, prix_prod " .
		"FROM produit " .
		"LEFT JOIN ref_prodfamille rpf ON rpf.id_prodfam = produit.famille_prod ".
		"WHERE nom_prod $qTag OR id_prod $qTag OR nom_prodfam $qTag OR treePathKey $qTag " .
		"ORDER BY id_prod ASC " .
		"LIMIT $from , $limit");
	return $this->process2();
    }

    /**
     * Méthode qui récupère des statistiques sur les factures / avoir
     * @param string $type Précise le type facture ou avoir
     * @return array Les stats
     */
    public function getDataForStats($type = '') {
	$this->makeRequeteFree("SELECT COUNT(id_fact) as N, avg(sommeHT_fact) as X, VARIANCE(sommeHT_fact) as variance, SUM(sommeHT_fact) as somme from facture $type");
	$result = $this->process2();
	$this->makeRequeteFree("SELECT AVG(DISTINCT sommeHT_fact) AS MEDIANE
						FROM   (SELECT sommeHT_fact
        							FROM   (SELECT *
                							FROM    facture $type 
               								UNION ALL
                							SELECT *
               								FROM    facture $type ) STT
       								WHERE (SELECT COUNT(*)
                						FROM    facture $type )
                    			<= (SELECT COUNT(*)
              						FROM   (SELECT *
                      						FROM    facture $type 
                     						UNION ALL
                      						SELECT *
                      						FROM    facture $type ) AS SOU
              						WHERE  SOU.sommeHT_fact <= STT.sommeHT_fact)
								AND (SELECT COUNT(*)
    									FROM    facture $type )
          						<= (SELECT COUNT(*)
              						FROM   (SELECT *
                     						FROM    facture $type 
                     						UNION ALL
                      						SELECT *
                      						FROM    facture $type ) AS SUR
              			WHERE SUR.sommeHT_fact >= STT.sommeHT_fact) ) AS T");
	$result['mediane'] = $this->process2();
	return $result;
    }

    /**
     * Méthode qui récupère les valeurs HT de certaines factures
     * @param array $ids Les numéro d'ordre des factures triées par montant croissant
     * @param string $type Précise facture / avoir
     * @return array Les données
     */
    public function getValues($ids = array(), $type = '') {
	$this->setPersistant(1);
	$preums = 'oui';
	$this->makeRequeteFree("set @count=0;");
	$this->process2();
	$requete = "select F.sommeHT_fact as val from ".
		"(select id_fact, sommeHT_fact, @count:=@count+1 as num from facture $type order by sommeHT_fact) as F ";
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
	$out = $this->process2();
	$this->setPersistant(0);
	return $out;
    }

    /**
     * Fonction de recherche de données à partir d'un identifiant
     * @param int $id L'id de la facture
     * @return array Les données de la facture
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT *, c2.id_cont AS id_achat, c2.civ_cont AS civ_achat, c2.prenom_cont AS prenom_achat, c2.nom_cont AS nom_achat
		FROM facture f
		LEFT JOIN entreprise ON entreprise.id_ent = f.entreprise_fact
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		LEFT JOIN contact c2 ON c2.id_cont = f.contact_achat_fact
		LEFT JOIN contact c1 ON c1.id_cont = f.contact_fact
		LEFT JOIN commande ON commande.id_cmd = f.commande_fact
		LEFT JOIN devis ON devis.id_dev = commande.devis_cmd
		LEFT JOIN affaire ON affaire.id_aff = devis.affaire_dev
		LEFT JOIN ref_statusfacture rsd ON rsd.id_stfact = f.status_fact
		LEFT JOIN user ON user.login = f.commercial_fact
		LEFT JOIN ref_condireglement rcr ON rcr.id_condreg = f.condireglement_fact
		LEFT JOIN ref_modereglement rmr ON rmr.id_modereg = f.modereglement_fact
		LEFT JOIN ref_pays rf ON rf.id_pays = f.pays_fact
		WHERE id_fact = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère un/les produit(s) d'une facture
     * @param int $id L'id de la facture sur laquelle on fait la recherche
     * @param int $nbprod Précise si on ne veut qu'un seul produit ou tous
     * @param string $prod L'id du produit
     * @return array Les données demandées
     */
    public function getProduitsFromID($id, $nbprod = '0', $prod = '') {
	$prod=urldecode($prod);
	if($nbprod == '0') {
	    $this->makeRequeteFree("SELECT *
			FROM facture_produit fp
			LEFT JOIN produit ON produit.id_prod = fp.id_produit
			LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
			WHERE id_facture = '".trim($id)."'");
	}
	else {	//Si je ne veux qu'un seul produit d'une commande...
	    $this->makeRequeteFree("SELECT *
			FROM facture_produit fp
			LEFT JOIN produit ON produit.id_prod = fp.id_produit
			LEFT JOIN ref_prodfamille ON ref_prodfamille.id_prodfam = produit.famille_prod
			WHERE id_facture = '".trim($id)."' AND id_produit = '".trim($prod)."' ");
	}
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les infos des produits d'une facture
     * @param int $id L'id de la facture
     * @return array Les données demandées
     */
    public function getProduitsFromFacture($id) {
	$this->makeRequeteFree("SELECT id_prod, stock_prod, quantite, facture_prod, fournisseur_id
	 	FROM produit
	 	LEFT JOIN facture_produit fp ON fp.id_produit = produit.id_prod
	 	LEFT JOIN produit_fournisseur pf ON pf.produit_id = produit.id_prod
	 	WHERE id_facture = '".trim($id)."' ");
	return $this->process2();
    }

    /**
     * Méthode qui récupères les infos d'un produit
     * @param string $id L'id d'un produit
     * @return array Les infos du produits
     */
    public function getInfoProduits($id) {
	$this->makeRequeteFree("SELECT *
		FROM produit
		WHERE id_prod = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les infos d'un produit qui est déjà lié à au moins une facture
     * @param string $id L'id du produit
     * @return array Les infos sur ce produit
     */
    public function getInfoProduitsPlus($id) {
	$this->makeRequeteFree("SELECT *
		FROM facture_produit fp
		LEFT JOIN produit ON produit.id_prod = fp.id_produit
		WHERE id_produit = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupères les produits d'une commande
     * @param string $id L'i de la commande
     * @return array Les produits de la commande
     */
    public function getProduitsFacture($id) {
	$this->makeRequeteFree("SELECT *
		FROM commande_produit dp
		left join produit ON produit.id_prod = dp.id_produit
		WHERE id_commande = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupères les informations d'entreprise d'une fiche commande
     * @param string $id L'id de la commande
     * @return array Les données demandées
     */
    public function getEntrepriseData($id) {
	$this->makeRequeteFree("SELECT *
		FROM commande
		LEFT JOIN entreprise on entreprise.id_ent = commande.entreprise_cmd
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		WHERE id_cmd = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui retourne la liste des contacts liés à une entreprise liée à une facture
     * @param int $id L'id de la facture
     * @return array La liste des contacts
     */
    public function getContactsFromFacture($id) {
	$this->makeRequeteFree("SELECT * from contact c
                                LEFT JOIN entreprise e ON e.id_ent = c.entreprise_cont
                                LEFT JOIN facture f ON f.entreprise_fact = e.id_ent
                                WHERE f.id_fact = '".$id."'");
	return $this->process2();
    }

    /**
     * Méthode qui retourne la liste des contacts liés à une entreprise liée à la facture demandée
     * @param int $id L'id de la facture
     * @return array La liste des contacts
     */
    public function getContactsForCB($id) {
	$this->makeRequeteFree("SELECT *, wallet_cp as wallet_cont, date_cp as dateCarte_cont, cvv_cp as cvvCarte_cont, fin_cp as finCarte_cont
                                from contact c
                                LEFT JOIN contact_payline cp ON cp.contact_cp = c.id_cont
                                LEFT JOIN entreprise e ON e.id_ent = c.entreprise_cont
                                LEFT JOIN facture f ON f.entreprise_fact = e.id_ent
                                WHERE f.id_fact = '".$id."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère toutes les informations d'un contact
     * @param int $id L'id d'un contact
     * @return array Les données du contact
     */
    public function getContactData($id) {
	$this->makeRequeteFree("SELECT *
		FROM contact
		WHERE id_cont = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère l'id du contact de la facture
     * @param int $id l'id de la facture
     * @return string L'id du contact
     */
    public function getContactFromFacture($id) {
	$this->makeRequeteFree("select contact_fact from facture where id_fact = '".$id."'");
	$rs = $this->process2();
	if($rs[0])
	    return $rs[1][0]['contact_fact'];
	else
	    return '';
    }

    /**
     * Méthode qui récupère les infos de facture à partir d'un id de commande
     * @param string $id L'id d'une commande
     * @return array Les données demandées
     */
    public function getDataFromCommande($id) {
	$this->makeRequeteFree("SELECT *
		FROM facture
		WHERE commande_fact = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui gère l'ajout d'un produit dans la table produit
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function addProduit($data) {
	$sql = $this->makeRequeteInsert('produit',$data);
	Logg::loggerInfo('factureModel::addProduit() ~ ajout du produit '.$data['id_prod'],$sql,__FILE__.'@'.__LINE__);
	return $this->process2();
    }

    /**
     * Méthode qui ajoute un produit à une facture
     * @param array $data Les données du produit à insérer
     * @param bool $actu Précise si on veut une entrée d'actualité
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insertProduits($data, $actu = true) {
	if($data['quantite'] == NULL)
	    $data['quantite'] = 1;
	if($data['remise'] == NULL)
	    $data['remise'] = 0;
	$data['prix']=number_format($data['prix'],2,'.','');
	$sql = $this->makeRequeteInsert('facture_produit', $this->bindVarFactureProduit($data));
	Logg::loggerInfo('factureModel::insertProduits() ~ ajout du produit '.$data['id_produit'].' pour la facture '.$data['id_facture'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	$this->updateMontant($data['id_facture']);
	if($rs[0] and $actu)
	    $this->addActualite($data['id_facture'], 'update');
	return $rs;
    }

    /**
     * Méthode qui supprime un produit d'une facture
     * @param array $data Les données à supprimer
     * @param string $id_produit Permet de préciser l'id du produit si cette info n'est pas dans $data
     * @param string $tous Précie si on supprime tous les produits d'une facture
     * @return array Indique si la requête s'est bien déroulée
     */
    public function deleteProduits($data, $id_produit = NULL, $tous = 'non') {
	if($tous == 'all')
	    $sql = $this->makeRequeteFree("DELETE FROM facture_produit WHERE id_facture='".$data['id_facture']."';");
	elseif($id_produit != NULL)
	    $sql = $this->makeRequeteFree("DELETE FROM facture_produit WHERE id_facture='".$data['id_facture']."' AND id_produit='".$id_produit."';");
	else $sql = $this->makeRequeteFree("DELETE FROM facture_produit WHERE id='".$data['id']."';");
	Logg::loggerInfo('factureModel::deleteProduits() ~ suppression d\'un produit sur la facture '.$data['id_facture'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0]) {
	    $this->updateMontant($data['id_facture']);
	    $this->addActualite($data['id_facture'], 'update');
	}
	return $rs;
    }

    /**
     * Méthode qui permet de mettre à jour un produit d'une facture
     * @param array $data Les données à mettre à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function updateProduits($data) {
	$sql = $this->makeRequeteUpdate('facture_produit', 'id', $data['id'], $this->bindVarFactureProduit($data));
	Logg::loggerInfo('factureModel::updateProduits() ~ modification d\'un produit sur la facture '.$data['id_facture'],$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0]) {
	    $this->updateMontant($data['id_facture']);
	    $this->addActualite($data['id_facture'], 'update');
	}
	return $rs;
    }

    private function updateMontant($id) {
	$prod = $this->getProduitsFromID($id);
	$sommeHT = 0;
	foreach($prod[1] as $v) {
	    $sommeHT += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
	}
	Logg::loggerInfo('factureModel::updateMontant() ~ modification de la valeur de la facture '.$data['id_facture'],$sql,__FILE__.'@'.__LINE__);
	$this->update(array('sommeHT_fact' => $sommeHT), $id);
    }

    /**
     * Méthode qui s'occupe d'insérer un tuple dans la table facture
     * @param array $data Les données à insérer
     * @param string $fait Précise si on créé si on clone, etc
     * @param array $prod Les produits à insérer
     * @param string $id L'id de la facture originale en cas de clonage
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insert($data, $fait = 'creation', $prod = array(), $id = '') {
	$data['datemodif_fact']=date('Y-m-d');
	$data['daterecord_fact']=date('Y-m-d');
	switch($data['condireglement_fact']) {
	    case '4':
		$jour = time()+(60*60*24*30);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    case '5':
		$jour = time()+(60*60*24*30);
		$data['datereglement_fact']=finDeMois($jour);
		break;
	    case '6':
		$jour = time()+(60*60*24*30);
		$data['datereglement_fact']=le10DuMois($jour);
		break;
	    case '7':
		$jour = time()+(60*60*24*60);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    case '8':
		$jour = time()+(60*60*24*60);
		$data['datereglement_fact']=finDeMois($jour);
		break;
	    case '9':
		$jour = time()+(60*60*24*60);
		$data['datereglement_fact']=le10DuMois($jour);
		break;
	    case '10':
		$jour = time()+(60*60*24*90);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    case '11':
		$jour = time()+(60*60*24*90);
		$data['datereglement_fact']=finDeMois($jour);
		break;
	    case '12':
		$jour = time()+(60*60*24*90);
		$data['datereglement_fact']=le10DuMois($jour);
		break;
	    case '13':
		$jour = time()+(60*60*24*120);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    case '14':
		$jour = time()+(60*60*24*45);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    default:
		$data['datereglement_fact'] = date('Y-m-d');
		break;
	}
	$data['tauxTVA_fact']=number_format($data['tauxTVA_fact'], 1, '.', '');
	if($fait == 'creation') {
	    $sql = $this->makeRequeteInsert($this->sqlTableName, $this->bindVarFacture($data));
	    Logg::loggerInfo('factureModel::insert() ~ ajout de la facture '.$data['id_fact'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0])
		$this->addActualite($data['id_fact'], 'add');
	    return $rs;
	}
	elseif($fait == 'cloner') {
	    $sql = $this->makeRequeteInsert($this->sqlTableName, $this->bindVarFacture($data));
	    Logg::loggerInfo('factureModel::insert() ~ ajout de la facture '.$data['id_fact'].' par clonnage',$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    $a = 2;
	    foreach($prod as $v) {
		$temp['id_facture'] = $data['id_fact'];
		$temp['id_produit'] = $v['id_produit'];
		$temp['desc'] = $v['desc'];
		$temp['quantite'] = $v['quantite'];
		$temp['remise'] = $v['remise'];
		$temp['prix'] = $v['prix'];
		$resultprod = $this->insertProduits($temp, false);
		$rs[$a]=$resultprod;
		$a++;
	    }
	    if($rs[0] and $id == '')
		$this->addActualite($data['id_fact'], 'add');
	    elseif($rs[0] and $id != '')
		$this->addActualite($data['id_fact'], 'clone', '', '', $id);
	    return $rs;
	}
	elseif($fait == 'toAvoir') {
	    $sql = $this->makeRequeteInsert($this->sqlTableName, $this->bindVarFacture($data));
	    Logg::loggerInfo('factureModel::insert() ~ ajout de l\'avoir '.$data['id_fact'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    $a = 2;
	    foreach($prod as $v) {
		$temp['id_facture'] = $data['id_fact'];
		$temp['id_produit'] = $v['id_produit'];
		$temp['desc'] = $v['desc'];
		$temp['quantite'] = $v['quantite'];
		$temp['remise'] = $v['remise'];
		$temp['prix'] = (-1)*abs($v['prix']);
		$resultprod = $this->insertProduits($temp, false);
		$rs[$a]=$resultprod;
		$a++;
	    }
	    if($rs[0] and $id == '')
		$this->addActualite($data['id_fact'], 'add');
	    elseif($rs[0] and $id != '')
		$this->addActualite($data['id_fact'], 'avoir', '', '', $id);
	    return $rs;
	}
	elseif($fait == 'express') {
	    $temp = $this->getLastId();
	    $temp ++;
	    $_SESSION['factureExpress']['id'] = $temp;
	    $data['id_fact'] = $temp;
	    $sql = $this->makeRequeteInsert($this->sqlTableName, $this->bindVarFacture($data));
	    Logg::loggerInfo('factureModel::insert() ~ ajout de la facture '.$data['id_fact'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    foreach($prod as $v) {
		$tempon['id_facture'] = $data['id_fact'];
		$tempon['id_produit'] = $v['id_produit'];
		$tempon['desc'] = $v['desc'];
		$tempon['quantite'] = $v['quantite'];
		$tempon['remise'] = $v['remise'];
		$tempon['prix'] = $v['prix'];
		$resultat = $this->insertProduits($tempon, false);
	    }
	    if($rs[0])
		$this->addActualite($data['id_fact'], 'add');
	    return $rs;
	}
	elseif($fait == 'WS') {
	    $sql = $this->makeRequeteInsert($this->sqlTableName, $this->bindVarFacture($data));
	    Logg::loggerInfo('factureModel::insert() ~ ajout de la facture '.$data['id_fact'].' par web service',$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    $a = 2;
	    foreach($prod as $v) {
		$temp['id_facture'] = $data['id_fact'];
		$temp['id_produit'] = $v['id_produit'];
		$temp['desc'] = $v['desc'];
		$temp['quantite'] = $v['quantite'];
		$temp['remise'] = $v['remise'];
		$temp['prix'] = $v['prix'];
		$resultprod = $this->insertProduits($temp, false);
		$rs[$a]=$resultprod;
		$a++;
	    }
	    if($rs[0])
		$this->addActualite($data['id_fact'], 'WS');
	    return $rs;
	}
    }

    /**
     * Méthode qui s'occuper de mettre à jour les factures
     * @param array $data Les données à jour
     * @param int $id_fact L'id de la facture à mettre à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function update($data,$id_fact) {
	$data['datemodif_fact']=date('Y-m-d');
	$data['status_fact']=($data['status_fact'] == NULL || $data['status_fact'] == '') ? '2': $data['status_fact'];
	switch($data['condireglement_fact']) {
	    case '4':
		$jour = time()+(60*60*24*30);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    case '5':
		$jour = time()+(60*60*24*30);
		$data['datereglement_fact']=finDeMois($jour);
		break;
	    case '6':
		$jour = time()+(60*60*24*30);
		$data['datereglement_fact']=le10DuMois($jour);
		break;
	    case '7':
		$jour = time()+(60*60*24*60);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    case '8':
		$jour = time()+(60*60*24*60);
		$data['datereglement_fact']=finDeMois($jour);
		break;
	    case '9':
		$jour = time()+(60*60*24*60);
		$data['datereglement_fact']=le10DuMois($jour);
		break;
	    case '10':
		$jour = time()+(60*60*24*90);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    case '11':
		$jour = time()+(60*60*24*90);
		$data['datereglement_fact']=finDeMois($jour);
		break;
	    case '12':
		$jour = time()+(60*60*24*90);
		$data['datereglement_fact']=le10DuMois($jour);
		break;
	    case '13':
		$jour = time()+(60*60*24*120);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    case '14':
		$jour = time()+(60*60*24*45);
		$data['datereglement_fact']=date('Y-m-d',$jour);
		break;
	    default:
		break;
	}
	if($data['tauxTVA_fact'] != NULL and $data['tauxTVA_fact'] != '')
	    $data['tauxTVA_fact']=prepareNombreTraitement($data['tauxTVA_fact']);
	if($data['sommeHT_fact'] != NULL and $data['sommeHT_fact'] != '')
	    $data['sommeHT_fact']=prepareNombreTraitement($data['sommeHT_fact']);
	if(!array_key_exists($this->sqlIdField,$data)) {
	    $sql = $this->makeRequeteUpdate($this->sqlTableName,'id_fact',$id_fact, $this->bindVarFacture($data));
	    Logg::loggerInfo('factureModel::updateProduits() ~ modification de la facture '.$id_fact,$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0] and $data['status_fact'] != '')
		$this->addActualite($id_fact, 'update');
	    return $rs;
	}
	else {
	    $sql = $this->makeRequeteUpdate($this->sqlTableName,'id_fact',$data['id_fact'], $this->bindVarFacture($data));
	    Logg::loggerInfo('factureModel::updateProduits() ~ modification de la facture '.$data['id_fact'],$sql,__FILE__.'@'.__LINE__);
	    $rs = $this->process2();
	    if($rs[0] and $data['id_fact'] != '')
		$this->addActualite($data['id_fact'], 'update');
	    return $rs;
	}
    }

    /**
     * Méthode qui supprime une facture de la base
     * @param int $id_fact L'id de la facture à supprimer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function delete($id_fact) {
	$sql = $this->makeRequeteFree("DELETE FROM $this->sqlTableName WHERE id_fact = '".$id_fact."'");
	Logg::loggerInfo('factureModel::delete() ~ suppression de la facture '.$id_fact,$sql,__FILE__.'@'.__LINE__);
	$rs = $this->process2();
	if($rs[0])
	    $this->addActualite($data['id_fact'], 'delete');
	return $rs;
    }

    /**
     * Méthode qui récupère des factures triées par montant
     * @param string $maxi Le nombre maximum de résultats
     * @param int $from L'index de la première facture
     * @param string $plus Un complément de requête SQL
     * @return array
     */
    public function getDataByMontant($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY sommeHT_fact DESC ".$plus);
	else $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ".$plus." ORDER BY sommeHT_fact DESC, id_fact DESC LIMIT $from , $maxi");
	return $this->process2();
    }

    /**
     * Méthode qui récupère des factures triées par date de création
     * @param string $maxi Le nombre maximum de résultats
     * @param int $from L'index de la première facture
     * @param string $plus Un complément de requête SQL
     * @return array
     */
    public function getDataByCreation($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY daterecord_fact DESC ".$plus);
	else $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ".$plus." ORDER BY daterecord_fact DESC, id_fact DESC LIMIT $from , $maxi");
	return $this->process2();
    }

    /**
     * Méthode qui récupère des factures triées par entreprise
     * @param string $maxi Le nombre maximum de résultats
     * @param int $from L'index de la première facture
     * @param string $plus Un complément de requête SQL
     * @return array
     */
    public function getDataByEntreprise($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY entreprise_fact ASC ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (entreprise_fact IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName LEFT JOIN entreprise ON entreprise.id_ent=facture.entreprise_fact ".$plus." ORDER BY temp DESC, entreprise.nom_ent ASC, id_fact DESC LIMIT $from , $maxi");
	return $this->process2();
    }

    /**
     * Méthode qui récupère des factures triées par contact
     * @param string $maxi Le nombre maximum de résultats
     * @param int $from L'index de la première facture
     * @param string $plus Un complément de requête SQL
     * @return array
     */
    public function getDataByContact($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY contact_fact ASC ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (contact_fact IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName LEFT JOIN contact ON contact.id_cont=facture.contact_fact ".$plus." ORDER BY temp DESC, contact.nom_cont ASC, id_fact DESC LIMIT $from , $maxi");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le type facture/avoir depuis un id
     * @param int $id L'id dont on veut le type
     * @return string Le type
     */
    public function getType($id) {
	$this->makeRequeteFree("SELECT type_fact FROM facture WHERE id_fact = '$id'");
	$rs = $this->process2();
	return ($rs[0]) ? strtolower($rs[1][0]['type_fact']) : 'facture';
    }

    /**
     * Méthode qui retourne les infos sur le renouvellement d'une facture fournisseur
     * @param int $id L'id de la facture fournisseur
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

    /**
     * Méthode qui récupère le montant TTC d'une facture
     * @param int $id L'id de la facture
     * @return float Le montant TTC
     */
    public function getMontantTTC($id) {
	$this->makeRequeteFree("SELECT sommeHT_fact, tauxTVA_fact from facture where id_fact = '".$id."'");
	$rs = $this->process2();
	if($rs[0]) {
	    return round(prepareNombreTraitement($rs[1][0]['sommeHT_fact']*(1+$rs[1][0]['tauxTVA_fact']/100)),2);
	}
    }

    /**
     * Méthode qui récupère la liste des factures liées à un contact
     * @param int $id L'id du contact dont on veut les factures
     * @return array Les factures du contact
     */
    public function getListeFactureFromContact($id) {
	$this->makeRequeteFree("Select * from facture where contact_fact = '".$id."'");
	return $this->process2();
    }

    /**
     * Méthode qui récupère l'id d'une facture envoyée à un client à une date précise
     * @param int $id L'id du client
     * @param string $date La date d'envoi
     * @return array L'id de la facture
     */
    public function getIdFromContactDate($id, $date) {
	$this->makeRequeteFree("Select id_fact from facture where contact_fact = '".$id."' and dateenvoi_fact = '".$date."'");
	return $this->process2();
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributeFactureInDB($id_fact,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('titre_fact', $newAttribList) or
		array_key_exists('tauxTVA_fact', $newAttribList) or
		array_key_exists('commentaire_fact', $newAttribList) or
		array_key_exists('modereglement_fact', $newAttribList) or
		array_key_exists('condireglement_fact', $newAttribList) or
		array_key_exists('commercial_fact', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_fact != '' and $hasChange) {
	    $bddtmp = new FactureModel();
	    $bddtmp->makeRequeteSelect('facture','id_fact',$id_fact);
	    $fact = $bddtmp->process();
	    $fact = $fact[0];
	    if($newAttribList['titre_fact'] != '')
		$newData['titre_fact'] = $newAttribList['titre_fact'];
	    if($newAttribList['tauxTVA_fact'] != '')
		$newData['tauxTVA_fact'] = $newAttribList['tauxTVA_fact'];
	    if($newAttribList['commentaire_fact'] != '')
		$newData['commentaire_fact'] = $newAttribList['commentaire_fact'];
	    if($newAttribList['modereglement_fact'] != '')
		$newData['modereglement_fact'] = $newAttribList['modereglement_fact'];
	    if($newAttribList['condireglement_fact'] != '')
		$newData['condireglement_fact'] = $newAttribList['condireglement_fact'];
	    if($newAttribList['commercial_fact'] != '')
		$newData['commercial_fact'] = $newAttribList['commercial_fact'];
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_fact, $bddtmp->bindVarFacture($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_fact, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('factureModel::changeAttributeFactureInDB() ~ changement d\'attribut de la facture '.$id_fact,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerInfo('factureModel::changeAttributeFactureInDB() ~ impossible de changer les attributs de la facture '.$id_fact,'',__FILE__.'@'.__LINE__);
    }


    static function archivateFactureInDB($id_fact,$newAttribList = array()) {
	if ($id_fact != '') {
	    $bddtmp = new FactureModel();
	    $bddtmp->makeRequeteSelect('facture','id_fact',$id_fact);
	    $fact = $bddtmp->process();
	    $fact = $fact[0];
	    $newData['status_fact'] = '7';
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_fact, $bddtmp->bindVarFacture($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_fact, 'archive', '', 'changement par lot', '',false);
	    Logg::loggerInfo('factureModel::markEnvoyeFactureInDB() ~ Facture '.$id_fact.' marqué comme archivé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerInfo('factureModel::markEnvoyeFactureInDB() ~ impossible de marquer comme archivé la facture '.$id_fact,'',__FILE__.'@'.__LINE__);
    }
    static function markValideFactureInDB($id_fact,$newAttribList = array()) {
	if ($id_fact != '') {
	    $bddtmp = new FactureModel();
	    $bddtmp->makeRequeteSelect('facture','id_fact',$id_fact);
	    $fact = $bddtmp->process();
	    $fact = $fact[0];
	    if($newAttribList['commentaireValid'] != '')
		$newData['commentaire_fact'] = ($fact['commentaire_fact'] != '') ? $fact['commentaire_fact'].'. '.$newAttribList['commentaireValid'] : $newAttribList['commentaireValid'];
	    $newData['status_fact'] = '2';
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_fact, $bddtmp->bindVarFacture($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_fact, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('factureModel::markValideFactureInDB() ~ Facture '.$id_fact.' marquée comme validée',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerInfo('factureModel::markValideFactureInDB() ~ impossible de marquer comme validée la facture '.$id_fact,'',__FILE__.'@'.__LINE__);
    }

    static function markEnvoyeFactureInDB($id_fact,$newAttribList = array()) {
	if ($id_fact != '') {
	    $bddtmp = new FactureModel();
	    $bddtmp->makeRequeteSelect('facture','id_fact',$id_fact);
	    $fact = $bddtmp->process();
	    $fact = $fact[0];
	    if($newAttribList['commentaireEnvoye'] != '')
		$newData['commentaire_fact'] = ($fact['commentaire_fact'] != '') ? $fact['commentaire_fact'].'. '.$newAttribList['commentaireEnvoye'] : $newAttribList['commentaireEnvoye'];
	    $newData['status_fact'] = '4';
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_fact, $bddtmp->bindVarFacture($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_fact, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('factureModel::markEnvoyeFactureInDB() ~ Facture '.$id_fact.' marqué comme envoyée',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerInfo('factureModel::markEnvoyeFactureInDB() ~ impossible de marquer comme envoyée la facture '.$id_fact,'',__FILE__.'@'.__LINE__);
    }


    static function markRegleFactureInDB($id_fact,$newAttribList = array()) {
	if ($id_fact != '') {
	    $bddtmp = new FactureModel();
	    $bddtmp->makeRequeteSelect('facture','id_fact',$id_fact);
	    $fact = $bddtmp->process();
	    $fact = $fact[0];
	    if($newAttribList['commentreglement_fact'] != '')
		$newData['commentreglement_fact'] = ($fact['commentreglement_fact'] != '') ? $fact['commentreglement_fact'].'. '.$newAttribList['commentreglement_fact'] : $newAttribList['commentreglement_fact'];
	    if($newAttribList['commentaireRegle'] != '')
		$newData['commentaire_fact'] = ($fact['commentaire_fact'] != '') ? $fact['commentaire_fact'].'. '.$newAttribList['commentaireRegle'] : $newAttribList['commentaireRegle'];
	    $newData['status_fact'] = '6';
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_fact, $bddtmp->bindVarFacture($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_fact, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('factureModel::markRegleFactureInDB() ~ Facture '.$id_fact.' marquée comme réglée',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerInfo('factureModel::markRegleFactureInDB() ~ impossible de marquer comme réglée la facture '.$id_fact,'',__FILE__.'@'.__LINE__);
    }

    static function markReinitFactureInDB($id_fact,$newAttribList = array()) {
	if ($id_fact != '') {
	    $bddtmp = new FactureModel();
	    $bddtmp->makeRequeteSelect('facture','id_fact',$id_fact);
	    $fact = $bddtmp->process();
	    $fact = $fact[0];
	    $newData['status_fact'] = '1';
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField, $id_fact, $bddtmp->bindVarFacture($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_fact, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('factureModel::markReinitFactureInDB() ~ Re-initialisation de la facture '.$id_fact,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerInfo('factureModel::markReinitFactureInDB() ~ impossible de Re-initialiser la facture '.$id_fact,'',__FILE__.'@'.__LINE__);
    }


    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table facture
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarFacture($data) {
	return $this->bindVar($data,$this->availableFields);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table facture_produit
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFieldsProduit
     */
    public function bindVarFactureProduit($data) {
	return $this->bindVar($data,$this->availableFieldsProduit);
    }
}
?>
