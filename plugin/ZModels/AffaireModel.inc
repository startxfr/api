<?php
loadPlugin('ZModels/GeneralModel');
/**
 * Classe qui gère toutes les requètes Base en rapport avec les affaires
 * @author Nicolas Mannocci
 * @version 1.0
 */
class affaireModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'affaire';
	$this->sqlIdField = 'id_aff';
	$this->availableFields = array(
		'id_aff','entreprise_aff','contact_aff','actif_aff',
		'archived_aff','projet_aff','titre_aff',
		'modif_aff','detect_aff','status_aff',
		'desc_aff','echeance_aff','budget_aff',
		'decid_aff','comm_aff','typeproj_aff',
		'commercial_aff','technique_aff','dir_aff');
	parent::__construct();
    }


    /**
     * Fonction de recherche de données à partir d'un identifiant
     * @param string $search le champ de recherche
     * @param int $limit Le nombre maximum de résultat (défaut 10, ALL permet de faire un COUNT(*))
     * @param int $from Le numéro d'index du premier résultat (défaut 0)
     * @param string $plus D'éventuelles restrictions supplémentaires
     * @return array Le résultat
     */
    public function getDataForSearchWeb($search,$limit = 10, $from = 0, $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	if ($search['echeance_aff'] != '')	$reqAdd .= "AND echeance_aff <= '".DateHuman2Univ($search['echeance_aff'])."' ";
	if ($search['commercial_aff'] != '')	$reqAdd .= "AND commercial_aff = '".$search['commercial_aff']."' ";
	if ($search['typeproj_aff'] != '')	$reqAdd .= "AND typeproj_aff = '".$search['typeproj_aff']."' ";
	if ($search['status_aff'] != '')	$reqAdd .= "AND status_aff = '".$search['status_aff']."' ";
	if ($search['archived_aff'] == '1') {
	    $reqAdd .= 'AND archived_aff = \'1\' ';
	    $search['actif_aff'] = '0';
	}
	else $reqAdd .= 'AND archived_aff = \'0\' ';
	if ($search['actif_aff'] == '1')
	    $reqAdd .= 'AND actif_aff = \'1\' ';
	else $reqAdd .= 'AND actif_aff = \'0\' ';
	if ($search['cp_ent'] != '')		$reqAdd .= 'AND cp_ent LIKE \''.$search['cp_ent'].'%\' ';
	if ($search['titre_aff'] != '')		$reqAdd .= 'AND titre_aff LIKE \'%'.$search['titre_aff'].'%\' ';
	if ($search['id_aff'] != '')		$reqAdd .= 'AND id_aff LIKE \''.$search['id_aff'].'%\' ';
	if ($search['nom_ent'] != '')		$reqAdd .= 'AND nom_ent LIKE \'%'.$search['nom_ent'].'%\' ';
	if ($search['status_aff'] != '')	$reqAdd .= 'AND status_aff = \''.$search['status_aff'].'\' ';

	if($search['order'] != '')		$reqOrder = $search['order'].' '.((strtolower($search['orderSens']) == 'asc') ? 'ASC' : 'DESC').', ';

	if($limit == 'ALL') {
	    $this->makeRequeteFree("SELECT COUNT(*)
			FROM entreprise
			LEFT JOIN ref_activite ON entreprise.activite_ent = ref_activite.id_act
			LEFT JOIN ref_typeentreprise ON ref_typeentreprise.id_tyent = entreprise.type_ent
			LEFT JOIN ref_pays ON entreprise.pays_ent = ref_pays.id_pays
			,affaire
			LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
			LEFT JOIN ref_statusaffaire ON affaire.status_aff = ref_statusaffaire.id_staff
			LEFT JOIN ref_typeproj ON affaire.typeproj_aff = ref_typeproj.id_typro
			LEFT JOIN devis ON devis.affaire_dev = affaire.id_aff
			LEFT JOIN ref_statusdevis ON ref_statusdevis.id_stdev = devis.status_dev
			LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
			LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
			LEFT JOIN facture ON commande.id_cmd = facture.commande_fact
			LEFT JOIN ref_statusfacture ON ref_statusfacture.id_stfact = facture.status_fact
			LEFT JOIN ref_modereglement ON ref_modereglement.id_modereg = facture.modereglement_fact
			LEFT JOIN ref_condireglement ON ref_condireglement.id_condreg = facture.condireglement_fact
			WHERE entreprise_aff = id_ent
			".$reqAdd.$plus."
			ORDER BY ".$reqOrder." detect_aff ASC,nom_ent ASC");
	}
	else {
	    $this->makeRequeteFree("SELECT *
                        FROM entreprise
			LEFT JOIN ref_activite ON entreprise.activite_ent = ref_activite.id_act
			LEFT JOIN ref_typeentreprise ON ref_typeentreprise.id_tyent = entreprise.type_ent
			LEFT JOIN ref_pays ON entreprise.pays_ent = ref_pays.id_pays
			,affaire
			LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
			LEFT JOIN ref_statusaffaire ON affaire.status_aff = ref_statusaffaire.id_staff
			LEFT JOIN ref_typeproj ON affaire.typeproj_aff = ref_typeproj.id_typro
			LEFT JOIN devis ON devis.affaire_dev = affaire.id_aff
			LEFT JOIN ref_statusdevis ON ref_statusdevis.id_stdev = devis.status_dev
			LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
			LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
			LEFT JOIN facture ON commande.id_cmd = facture.commande_fact
			LEFT JOIN ref_statusfacture ON ref_statusfacture.id_stfact = facture.status_fact
			LEFT JOIN ref_modereglement ON ref_modereglement.id_modereg = facture.modereglement_fact
			LEFT JOIN ref_condireglement ON ref_condireglement.id_condreg = facture.condireglement_fact
			WHERE entreprise_aff = id_ent
			".$reqAdd.$plus."
			ORDER BY ".$reqOrder." detect_aff ASC,nom_ent ASC
                        LIMIT $from , $limit");
	}
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
		"SELECT * FROM entreprise
		LEFT JOIN ref_activite ON entreprise.activite_ent = ref_activite.id_act
		LEFT JOIN ref_typeentreprise ON ref_typeentreprise.id_tyent = entreprise.type_ent
		LEFT JOIN ref_pays ON entreprise.pays_ent = ref_pays.id_pays
		,affaire
		LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
		LEFT JOIN ref_statusaffaire ON affaire.status_aff = ref_statusaffaire.id_staff
		LEFT JOIN ref_typeproj ON affaire.typeproj_aff = ref_typeproj.id_typro
		LEFT JOIN devis ON devis.affaire_dev = affaire.id_aff
		LEFT JOIN ref_statusdevis ON ref_statusdevis.id_stdev = devis.status_dev
		LEFT JOIN commande ON commande.devis_cmd = devis.id_dev
		LEFT JOIN ref_statuscommande ON ref_statuscommande.id_stcmd = commande.status_cmd
		LEFT JOIN facture ON commande.id_cmd = facture.commande_fact
		LEFT JOIN ref_statusfacture ON ref_statusfacture.id_stfact = facture.status_fact
		LEFT JOIN ref_modereglement ON ref_modereglement.id_modereg = facture.modereglement_fact
		LEFT JOIN ref_condireglement ON ref_condireglement.id_condreg = facture.condireglement_fact
		WHERE entreprise_aff = id_ent
		AND id_aff IN $sqlList
		ORDER BY id_aff ASC, detect_aff ASC,nom_ent ASC");
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
    public function getDataForSearch($search,$limit = 10, $from = 0, $plus = '') {
	$qTag = " LIKE '%".$search."%' ";
	if($limit == 'ALL') {
	    $this->makeRequeteFree(
		    "SELECT COUNT(*) " .
		    "FROM affaire " .
		    "LEFT JOIN entreprise ON entreprise.id_ent = affaire.entreprise_aff
			 LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			 LEFT JOIN contact ON contact.id_cont = affaire.contact_aff " .
		    "WHERE (nom_ent $qTag OR ville_ent $qTag OR cp_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag " .
		    "OR mail_cont $qTag OR id_aff $qTag OR titre_aff $qTag) AND actif_aff = '1' " .
		    "AND status_aff != '17' ".$plus);
	}
	else {
	    $this->makeRequeteFree("SELECT titre_aff, nom_ent,type_ent, nom_cont, prenom_cont, civ_cont, id_aff " .
		    "FROM affaire " .
		    "LEFT JOIN entreprise ON entreprise.id_ent = affaire.entreprise_aff
			 LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			 LEFT JOIN contact ON contact.id_cont = affaire.contact_aff " .
		    "WHERE (nom_ent $qTag OR ville_ent $qTag OR cp_ent $qTag OR nom_cont $qTag OR prenom_cont $qTag " .
		    "OR mail_cont $qTag OR id_aff $qTag OR titre_aff $qTag) AND actif_aff = '1' " .
		    "AND status_aff != '17' ".$plus.
		    "ORDER BY id_aff DESC " .
		    "LIMIT $from , $limit");
	}

	return $this->process2();
    }

    /**
     * Méthode qui permet d'historiser les affichage d'affaires
     * @param int $limit Indique le nombre max de résultat souhaité (défaut 10)
     * @param string $type Précise si on veut les résultat ou un COUNT(*)
     * @param string $order Précise l'ordre de classement des résultats (optionnel, defaut 'ORDER BY id_aff DESC')
     * @return array Le résultat
     */
    public function getDataForHistoriqueVisit($limit ='10', $type = '', $order='ORDER BY id_aff DESC') {
	if(array_key_exists('historiqueVisite',$_SESSION) and
		array_key_exists('affaire',$_SESSION['historiqueVisite']) and
		count($_SESSION['historiqueVisite']['affaire']) > 0) {
	    $list = '';
	    foreach($_SESSION['historiqueVisite']['affaire'] as $id)
		$list .= '\''.$id.'\', ';
	    $list = '('.substr($list,0,-2).')';
	}
	else $list = '(\'fakeNull\')';
	if($type == 'COUNT')
	    $select = 'SELECT COUNT(*) AS counter';
	else $select = 'SELECT  titre_aff, nom_ent, nom_tyent, type_ent, nom_cont, prenom_cont, civ_cont, id_aff, rsa.*';
	$this->makeRequeteFree($select." FROM affaire
			LEFT JOIN entreprise ON entreprise.id_ent = affaire.entreprise_aff
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
			LEFT JOIN ref_statusaffaire rsa ON rsa.id_staff = affaire.status_aff
			WHERE id_aff IN $list $order LIMIT 0, $limit ");
	return $this->process2();
    }

    /**
     * Fonction de recherche de données à partir d'un identifiant
     * @param string $id L'Id de l'affaire
     * @return array Le résultat
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT *, c1.civ_cont as civ_cont, c1.prenom_cont as prenom_cont, c1.nom_cont as nom_cont, c2.civ_cont as civ_cont2, c2.prenom_cont as prenom_cont2, c2.nom_cont as nom_cont2
		FROM affaire
		LEFT JOIN entreprise ON entreprise.id_ent = affaire.entreprise_aff
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		LEFT JOIN contact c1 ON c1.id_cont = affaire.contact_aff
		LEFT JOIN contact c2 ON c2.id_cont = affaire.decid_aff
		LEFT JOIN user u1 ON u1.login = affaire.commercial_aff
		LEFT JOIN user u2 ON u2.login = affaire.technique_aff
		LEFT JOIN ref_statusaffaire rsa ON rsa.id_staff = affaire.status_aff
		LEFT JOIN ref_typeproj rtp ON rtp.id_typro = affaire.typeproj_aff
		WHERE id_aff = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui permet de générer quelques informations pour stats sur les affaires, devis, commandes et factures
     * @param string $id_aff Ce paramètre est inutilisé
     * @return array Le résultat
     */
    public function getDataForStats($id_aff = '') {
	$this->makeRequeteFree("SELECT COUNT(id_aff) as aff from affaire ");
	$nbaff = $this->process2();
	$this->makeRequeteFree("SELECT COUNT(id_dev) as dev, avg(sommeHT_dev) as pxdev, sum(sommeHT_dev) as sumdev from devis where affaire_dev is not null ");
	$nbdev = $this->process2();
	$this->makeRequeteFree("SELECT COUNT(id_cmd) as cmd, avg(sommeFHT_cmd) as pxcmd, sum(sommeFHT_cmd) as sumcmd from commande LEFT JOIN devis on devis.id_dev = commande.devis_cmd where affaire_dev is not null ");
	$nbcmd = $this->process2();
	$this->makeRequeteFree("SELECT COUNT(id_fact) as fact, avg(sommeHT_fact) as pxfact, sum(sommeHT_fact) as sumfact from facture LEFT JOIN commande on commande.id_cmd = facture.commande_fact LEFT JOIN devis on devis.id_dev = commande.devis_cmd where affaire_dev is not null and type_fact = 'Facture' ");
	$nbfact = $this->process2();
	return array($nbaff[1][0], $nbdev[1][0], $nbcmd[1][0], $nbfact[1][0]);
    }

    /**
     * Méthode qui donne la médiane des infos d'une colonne d'une table
     * Ceci est une méthode très générique
     * @param string $table La table dans laquelle travailler
     * @param string $colonne La colonne concernée (eg : sommeHT, totalTTC, etc...)
     * @return array La médiane (sous le nom MEDIANE)
     */
    public function getMediane($table, $colonne) {
	$this->makeRequeteFree("SELECT AVG(DISTINCT $colonne) AS MEDIANE
						FROM   (SELECT $colonne
        							FROM   (SELECT *
                							FROM    $table
               								UNION ALL
                							SELECT *
               								FROM    $table) STT
       								WHERE (SELECT COUNT(*)
                						FROM    $table)
                    			<= (SELECT COUNT(*)
              						FROM   (SELECT *
                      						FROM    $table
                     						UNION ALL
                      						SELECT *
                      						FROM    $table) AS SOU
              						WHERE  SOU.$colonne <= STT.$colonne)
								AND (SELECT COUNT(*)
    									FROM    $table)
          						<= (SELECT COUNT(*)
              						FROM   (SELECT *
                     						FROM    $table
                     						UNION ALL
                      						SELECT *
                      						FROM    $table) AS SUR
              			WHERE SUR.$colonne >= STT.$colonne) ) AS T");

	return $this->process2();
    }

    /**
     * Méthode qui gère les insertions en Base des affaires
     * @param array $data Les données à insérer
     * @param string $fait Précisait ce qui était fait (non utilisé désormais)
     * @param string $origine Indique d'où provient cette requète
     * @return array
     */
    public function insert($data, $fait = 'creation', $origine = '') {
	$data = $this->bindVarAffaire($data);
	if(!array_key_exists($this->sqlIdField,$data)) $data[$this->sqlIdField] = affaireModel::affaireGenerateID();
	Logg::loggerInfo('affaireModel::insert() ~ ajout de l\'affaire '.$data['id_aff'].' intitulé : '.$data['titre_aff'],$data,__FILE__.'@'.__LINE__);
	affaireModel::createNewAffaireInDB($data,TRUE, $fait, $origine);
	return $data;
    }

    /**
     * Méthode qui gère les Mises à jour des affaires en Base
     * @param array $data Les données à mettre à jour
     * @param string $id_aff L'id de l'affaire à mettre à jour
     * @param string $entreprise_aff Précise l'entreprise liée (optionnel)
     * @return array Indique si la requète est bien passée
     */
    public function update($data,$id_aff, $entreprise_aff = "") {
	if($entreprise_aff != "")
	    $data['entreprise_aff']=$entreprise_aff;
	$data['modif_aff']=date('Y-m-d');
	if(array_key_exists('echeance_aff', $data))
	    $data['echeance_aff'] = substr($data['echeance_aff'], 6,4).substr($data['echeance_aff'], 3,2).substr($data['echeance_aff'],0,2);
	if(!array_key_exists($this->sqlIdField,$data)) {
	    $sql = $this->makeRequeteUpdate($this->sqlTableName,"id_aff",$id_aff,$this->bindVarAffaire($data));
	    Logg::loggerInfo('affaireModel::update() ~ mise à jour de l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	    $result = $this->process2();
	    if($result[0] and $data['status_aff'] != '')
		$this->addActualite($id_aff, 'update');
	    return $result;
	}
    }

    /**
     * Méthode qui gère la suppression d'une affaire de la Base
     * @param string $id_aff L'id de l'affaire
     * @return array Indique si la requète s'est bien exécutée
     */
    public function delete($id_aff) {
	$sql = $this->makeRequeteFree("DELETE FROM $this->sqlTableName WHERE id_aff = $id_aff");
	Logg::loggerInfo('affaireModel::delete() ~ suppression de l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	if($result[0])
	    $this->addActualite($id_aff, 'delete');
	$this->makeRequeteFree("select id_dev FROM devis WHERE affaire_dev = $id_aff");
	$dev = $this->process2();
	foreach($dev[1] as $v) {
	    $id_dev = $v['id_dev'];
	    $sql = $this->makeRequeteFree("DELETE FROM devis WHERE id_dev = '".$id_dev."'");
	    $result = $this->process2();
	    $sql.= $this->makeRequeteFree("DELETE FROM devis_produit WHERE id_devis = '".$id_dev."'");
	    $result = $this->process2();
	    $sql.= $this->makeRequeteFree("DELETE FROM commande WHERE devis_cmd = '".$id_dev."'");
	    $result = $this->process2();
	    $sql.= $this->makeRequeteFree("DELETE FROM commande_produit WHERE id_commande = '".$id_dev."BC"."'");
	    $result = $this->process2();
	    $sql.= $this->makeRequeteFree("select id_fact FROM facture WHERE commande_fact = '".$id_dev."BC"."'");
	    $facture = $this->process2();
	    foreach($facture[1] as $v) {
		$fact = $v['id_fact'];
		$sql.= $this->makeRequeteFree("DELETE FROM facture_produit WHERE id_facture = '".$fact."' ");
		$result = $this->process2();
	    }
	    $sql.= $this->makeRequeteFree("DELETE FROM facture WHERE commande_fact = '".$id_dev."BC"."'");
	    $result = $this->process2();
	    Logg::loggerInfo('affaireModel::delete() ~ suppression de toutes les ressources associées à l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	}
	return $result;

    }

    /**
     * Méthode qui trie les affaires par date d'échéance
     * @param string $maxi Permet de préciser que l'on veut le COUNT(*) (Préciser NULL)
     * @param int $from L'index de la première affaire
     * @param string $plus Une restriction supplémentaire
     * @return array Les affaires triées
     */
    public function getDataByEcheance($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName WHERE actif_aff = '1' ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY echeance_aff ASC WHERE actif_aff = '1' ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (echeance_aff IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName where actif_aff = '1' ".$plus." ORDER BY temp DESC, echeance_aff ASC, id_aff DESC LIMIT $from , $maxi");
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui trie les affaires par date de création
     * @param string $maxi Permet de préciser que l'on veut le COUNT(*) (Préciser NULL)
     * @param int $from L'index de la première affaire
     * @param string $plus Une restriction supplémentaire
     * @return array Les affaires triées
     */
    public function getDataByCreation($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName WHERE actif_aff = '1' ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY detect_aff DESC WHERE actif_aff = '1' ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (detect_aff IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName where actif_aff = '1' ".$plus." ORDER BY temp DESC, detect_aff DESC, id_aff DESC LIMIT $from , $maxi");
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui trie les affaires par nom d'entreprise
     * @param string $maxi Permet de préciser que l'on veut le COUNT(*) (Préciser NULL)
     * @param int $from L'index de la première affaire
     * @param string $plus Une restriction supplémentaire
     * @return array Les affaires triées
     */
    public function getDataByEntreprise($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName WHERE actif_aff = '1' ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY entreprise_aff ASC WHERE actif_aff = '1' ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (entreprise_aff IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName LEFT JOIN entreprise ON entreprise.id_ent=affaire.entreprise_aff where actif_aff = '1' ".$plus." ORDER BY temp DESC, entreprise.nom_ent ASC, id_aff DESC LIMIT $from , $maxi");
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui trie les affaires par nom de contact
     * @param string $maxi Permet de préciser que l'on veut le COUNT(*) (Préciser NULL)
     * @param int $from L'index de la première affaire
     * @param string $plus Une restriction supplémentaire
     * @return array Les affaires triées
     */
    public function getDataByContact($maxi = NULL, $from = 0, $plus = '') {
	if($maxi == 'total')
	    $this->makeRequeteFree("SELECT COUNT(*) FROM $this->sqlTableName WHERE actif_aff = '1' ".$plus);
	elseif($maxi == NULL)
	    $this->makeRequeteFree("SELECT * FROM $this->sqlTableName ORDER BY contact_aff ASC WHERE actif_aff = '1' ".$plus);
	else $this->makeRequeteFree("SELECT *, CASE WHEN (contact_aff IS NOT NULL) THEN '1' ELSE '0' END AS temp FROM $this->sqlTableName LEFT JOIN contact ON contact.id_cont=affaire.contact_aff where actif_aff = '1' ".$plus." ORDER BY temp DESC, contact.nom_cont ASC, id_aff DESC LIMIT $from , $maxi");
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui est utilisée pour la recherche avancée d'affaires
     * @param array $value Les critères de recherche
     * @param string $plus Une restriction supplémentaire
     * @return array Le résultat de la recherche
     */
    public function getDataForRA($value, $plus = '') {
	if($value['echeance_aff_debut'] != '') {
	    $jour = substr($value['echeance_aff_debut'],0,2);
	    $mois = substr($value['echeance_aff_debut'], 3, 2);
	    $année = substr($value['echeance_aff_debut'], 6, 4);
	    $value['echeance_aff_debut'] = $année.'-'.$mois.'-'.$jour.' 00:01';
	}
	if($value['echeance_aff_fin'] != '') {
	    $jour = substr($value['echeance_aff_fin'],0,2);
	    $mois = substr($value['echeance_aff_fin'], 3, 2);
	    $année = substr($value['echeance_aff_fin'], 6, 4);
	    $value['echeance_aff_fin'] = $année.'-'.$mois.'-'.$jour.' 23:59';
	}
	if($value['detect_aff_debut'] != '') {
	    $jour = substr($value['detect_aff_debut'],0,2);
	    $mois = substr($value['detect_aff_debut'], 3, 2);
	    $année = substr($value['detect_aff_debut'], 6, 4);
	    $value['detect_aff_debut'] = $année.'-'.$mois.'-'.$jour.' 00:01';
	}
	if($value['detect_aff_fin'] != '') {
	    $jour = substr($value['detect_aff_fin'],0,2);
	    $mois = substr($value['detect_aff_fin'], 3, 2);
	    $année = substr($value['detect_aff_fin'], 6, 4);
	    $value['detect_aff_fin'] = $année.'-'.$mois.'-'.$jour.' 23:59';
	}
	if($value['modif_aff_debut'] != '') {
	    $jour = substr($value['modif_aff_debut'],0,2);
	    $mois = substr($value['modif_aff_debut'], 3, 2);
	    $année = substr($value['modif_aff_debut'], 6, 4);
	    $value['modif_aff_debut'] = $année.'-'.$mois.'-'.$jour.' 00:01';
	}
	if($value['modif_aff_fin'] != '') {
	    $jour = substr($value['modif_aff_fin'],0,2);
	    $mois = substr($value['modif_aff_fin'], 3, 2);
	    $année = substr($value['modif_aff_fin'], 6, 4);
	    $value['modif_aff_fin'] = $année.'-'.$mois.'-'.$jour.' 23:59';
	}
	if($value['id_aff'] != '') {
	    $id_aff = " AND id_aff LIKE '%".$value['id_aff']."%'";
	} else {
	    $id_aff = "";
	}
	if($value['titre_aff'] != '') {
	    $titre_aff = " AND titre_aff LIKE '%".$value['titre_aff']."%'";
	} else {
	    $titre_aff = "";
	}
	if($value['nom_ent'] != '') {
	    $nom_ent = " AND entreprise.nom_ent LIKE '%".$value['nom_ent']."%'";
	} else {
	    $nom_ent = "";
	}
	if($value['nom_cont'] != '') {
	    $nom_cont = " AND contact1.nom_cont LIKE '%".$value['nom_cont']."%'";
	} else {
	    $nom_cont = "";
	}
	if($value['nom_decid'] != '') {
	    $nom_decid = " AND contact2.nom_cont LIKE '%".$value['nom_decid']."%'";
	} else {
	    $nom_decid = "";
	}

	if($value['login1'] != '') {
	    $login1 = " AND user1.login LIKE '%".$value['login1']."%'";
	} else {
	    $login1 = "";
	}
	if($value['login2'] != '') {
	    $login2 = " AND user2.login LIKE '%".$value['login2']."%'";
	} else {
	    $login2 = "";
	}

	if($value['desc_aff'] != '') {
	    $desc_aff = " AND desc_aff LIKE '%".$value['desc_aff']."%'";
	} else {
	    $desc_aff = "";
	}
	if($value['budget_aff'] != '') {
	    $budget_aff = " AND budget_aff LIKE '%".$value['budget_aff']."%'";
	} else {
	    $budget_aff = "";
	}

	if($value['echeance_aff_debut'] != '' && $value['echeance_aff_fin'] != '') {
	    $echeance = " AND echeance_aff <= '".$value['echeance_aff_fin']."' AND echeance_aff >= '".$value['echeance_aff_debut']."'";
	}
	elseif($value['echeance_aff_debut'] != '' && $value['echeance_aff_fin'] == '') {
	    $echeance = " AND echeance_aff >= '".$value['echeance_aff_debut']."'";
	}
	elseif($value['echeance_aff_debut'] == '' && $value['echeance_aff_fin'] != '') {
	    $echeance = " AND echeance_aff <= '".$value['echeance_aff_fin']."'";
	}
	else {
	    $echeance = "";
	}

	if($value['detect_aff_debut'] != '' && $value['detect_aff_fin'] != '') {
	    $detect = " AND detect_aff <= '".$value['detect_aff_fin']."' AND detect_aff >= '".$value['detect_aff_debut']."'";
	}
	elseif($value['detect_aff_debut'] != '' && $value['detect_aff_fin'] == '') {
	    $detect = " AND detect_aff >= '".$value['detect_aff_debut']."'";
	}
	elseif($value['detect_aff_debut'] == '' && $value['detect_aff_fin'] != '') {
	    $detect = " AND detect_aff <= '".$value['detect_aff_fin']."'";
	}
	else {
	    $detect = "";
	}

	if($value['modif_aff_debut'] != '' && $value['modif_aff_fin'] != '') {
	    $modif = " AND modif_aff <= '".$value['modif_aff_fin']."' AND modif_aff >= '".$value['modif_aff_debut']."'";
	}
	elseif($value['modif_aff_debut'] != '' && $value['modif_aff_fin'] == '') {
	    $modif = " AND modif_aff >= '".$value['modif_aff_debut']."'";
	}
	elseif($value['modif_aff_debut'] == '' && $value['modif_aff_fin'] != '') {
	    $modif = " AND modif_aff <= '".$value['modif_aff_fin']."'";
	}
	else {
	    $modif = "";
	}

	if($value['typeproj_aff'] != '') {
	    $typeproj = " AND typeproj_aff = '".$value['typeproj_aff']."'";
	} else {
	    $typeproj ="";
	}
	if($value['status_aff'] != '') {
	    $status = " AND status_aff = '".$value['status_aff']."'";
	} else {
	    $status ="";
	}
	$etat ="";
	if($value['etat'] == '1') {
	    $etat = " AND actif_aff = '1'";
	}
	if($value['etat'] == '2') {
	    $etat = " AND actif_aff = '0' AND archived_aff = '0'";
	}
	if($value['etat'] == '3') {
	    $etat = " AND actif_aff = '0' AND archived_aff = '1'";
	}

	switch($value['affichage']) {
	    case 1:
		$ordre = "ORDER BY titre_aff";
		break;
	    case 2:
		$ordre = "ORDER BY id_aff";
		break;
	    case 3:
		$ordre = "ORDER BY entreprise.nom_ent";
		break;
	    case 4:
		$ordre = "ORDER BY contact1.nom_cont";
		break;
	    case 5:
		$ordre = "ORDER BY echeance_aff";
		break;
	    case 6:
		$ordre = "ORDER BY detect_aff";
		break;
	    case 7:
		$ordre = "ORDER BY modif_aff";
		break;
	    default:
		$ordre = "ORDER BY id_aff";
		break;

	}

	$and = $id_aff.$titre_aff.$nom_ent.$nom_cont.$nom_decid.$login1.$login2.$desc_aff.$budget_aff.$echeance.$detect.$modif.$typeproj.$status.$etat;
	$this->makeRequeteFree("SELECT * FROM affaire
		LEFT JOIN entreprise ON entreprise.id_ent = affaire.entreprise_aff
		LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		LEFT JOIN contact contact2 ON contact2.id_cont = affaire.decid_aff
		LEFT JOIN contact contact1 ON contact1.id_cont = affaire.contact_aff
		LEFT JOIN user u2 ON u2.login = affaire.technique_aff
		LEFT JOIN user u1 ON u1.login = affaire.commercial_aff
		LEFT JOIN ref_statusaffaire rsa ON rsa.id_staff = affaire.status_aff
		LEFT JOIN ref_typeproj rtp ON rtp.id_typro = affaire.typeproj_aff
		WHERE id_aff IS NOT NULL".$and." ".$plus." ".$ordre);

	$_SESSION['user']['ordre'] = $value['affichage'];
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui récupères les devis liées à une affaire
     * @param string $id L'id de l'affaire
     * @return array Les devis
     */
    public function getDevisForAffaire($id) {
	$this->makeRequeteFree("SELECT * FROM devis WHERE affaire_dev = '".$id."'");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
	else return $result;
    }

    /**
     * Méthode qui récupères les commandes liées à une affaire
     * @param string $id L'id de l'affaire
     * @return array Les commandes
     */
    public function getCommandeForAffaire($id) {
	$this->makeRequeteFree("SELECT commande.* FROM devis,commande WHERE affaire_dev = '".$id."' AND devis_cmd = id_dev");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
	else return $result;
    }

    /**
     * Méthode qui récupères les factures liées à une affaire
     * @param string $id L'id de l'affaire
     * @return array Les factures
     */
    public function getFactureForAffaire($id) {
	$this->makeRequeteFree("SELECT facture.* FROM devis,commande,facture WHERE affaire_dev = '".$id."' AND devis_cmd = id_dev AND commande_fact = id_cmd");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
	else return $result;
    }

    /**
     * Méthode qui récupères les factures liées à une affaire
     * @param string $id L'id de l'affaire
     * @return array Les factures
     */
    public function getRelatedRessourcesForAffaire($id) {
	$out['devis'] = $this->getDevisForAffaire($id);
	$out['commandes'] = $this->getCommandeForAffaire($id);
	$out['factures'] = $this->getFactureForAffaire($id);
	return $out;
    }

    /**
     * Méthode qui récupères les actualités liées à une affaire
     * @param string $id L'id de l'affaire
     * @return array Les actus
     */
    public function getActusByID($id) {
	$this->makeRequeteFree("SELECT * FROM actualite WHERE id_aff = '".$id."'");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
	else
	    return $result;
    }

    /**
     * Create affaireModel::ScoreAffaire element
     * @param $id String: Affaire ID
     * @return array with various score elements
     */
    static function ScoreAffaire($id) {

	$req = 'SELECT  * FROM affaire,contact,entreprise,ref_typeproj,ref_statusaffaire
			WHERE id_cont = contact_aff
			AND id_ent = entreprise_cont
			AND id_staff = status_aff
			AND typeproj_aff = id_typro AND id_aff = '.$id;
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	$aff = $res[0];

	return affaireModel::ScoreAffaireDisplay($aff);
    }

    /**
     * Create affaireModel::ScoreAffaire Content
     * @param $data Array: Array containing all information about an affaire
     * @return array with various score elements
     */
    static function ScoreAffaireDisplay($aff) {
	$desc = "Dernière action éffectuée :".$aff['nom_staff'];
	$out['img'] = imageTag('../Img.AffaireScore.php?score='.$aff['score_staff'],$aff['score_staff'].'%');
	$out['color'] = $aff['color_staff'];
	$out['desc'] = $desc;
	$out['LinkBox'] = '<a href="" title="'.$desc.'">'.$out['img'].'</a>';
	$out['LinkTextBox'] = '<a href="" title="'.$desc.'">'.$aff['score_staff'].'%</a>';
	return $out;
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function createNewAffaireInDB($data,$withGnose = TRUE, $fait = 'creation', $origine = '') {
	if (is_array($data) and isset($data['id_aff'])) {
	    $bddtmp = new AffaireModel();
	    $data['detect_aff'] = DateTimestamp2Univ('');
	    $id_aff = $data['id_aff'];
	    if ($data['entreprise_aff'] != '') {
		$bddtmp->makeRequeteSelect('entreprise','id_ent',$data['entreprise_aff']);
		$ent = $bddtmp->process();
		$ent = $ent[0];
		$data['dir_aff'] = $data['id_aff'].'-'.FileCleanFileName(ucfirst(strtolower($ent['nom_ent'])),'FILE_PATH_LOWER').'/';
	    }
	    else {
		$bddtmp->makeRequeteSelect('contact','id_cont',$data['contact_aff']);
		$cont = $bddtmp->process();
		$cont = $cont[0];
		$data['dir_aff'] = $data['id_aff'].'-'.FileCleanFileName(ucfirst(strtolower($cont['nom_cont'])),'FILE_PATH_LOWER').'/';

	    }
	    if ($data['status_aff'] != '') $data['status_aff'] = '1';

	    if($fait == 'creation') {
		$action = 'Ouverture de l\'affaire ';
	    } else {
		$action = 'Clonage de l\'affaire '.$origine.' en l\'affaire ';
	    }
	    $sql = $bddtmp->makeRequeteInsert($bddtmp->sqlTableName, $bddtmp->bindVarAffaire($data));
	    Logg::loggerInfo('affaireModel::createNewAffaireInDB() ~ ajout de l\'affaire '.$data['id_aff'],$sql,__FILE__.'@'.__LINE__);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_aff, 'add');

	    if ($withGnose and $data['dir_aff'] != '') {
		//On créer le projet dans l'entrepot gnose
		$uri = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$data['dir_aff'];
		$CmdBase = "svn mkdir --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." ";
		if ($GLOBALS['LOG']['DisplayDebug']) {
		    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
		}
		Logg::loggerInfo('affaireModel::createNewAffaireInDB() ~ création du répertoire de travail de l\'affaire '.$data['id_aff'],$CmdBase.$uri,__FILE__.'@'.__LINE__);
		shell_exec($CmdBase.$uri);
		//chmod($uri,0770);
		$uri		= " -m \"Création de l'affaire ".$id_aff." réalisée par ".$_SESSION["user"]["fullnom"]."\" ".$uri;
		$CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
		$CmdBase1	= "export LANG=\"en_US.UTF-8\"; export LC_CTYPE=\"en_US.UTF-8\"; export EDITOR=\"vi\"; svn update --non-interactive --no-auth-cache --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
		if ($GLOBALS['LOG']['DisplayDebug']) {
		    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
		    $GLOBALS['LogSVNProcess'][] = $CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &";
		}
		shell_exec($CmdBase.$uri);
		shell_exec($CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &");
	    }
	}
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function deleteAffaireInDB($id_aff,$deleteGnose = TRUE) {
	if ($id_aff != '') {
	    $bddtmp = new AffaireModel();
	    $bddtmp->makeRequeteSelect('affaire','id_aff',$id_aff);
	    $aff = $bddtmp->process();
	    $aff = $aff[0];
	    $sql = $bddtmp->makeRequeteDelete('affaire',array('id_aff' => $id_aff));
	    Logg::loggerInfo('affaireModel::deleteAffaireInDB() ~ suppression de l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	    $bddtmp->process();
	    $sql = $bddtmp->makeRequeteUpdate('projet','affaire_proj',$id_aff,array('affaire_proj' => ''));
	    Logg::loggerInfo('affaireModel::deleteAffaireInDB() ~ suppression des projets liées à l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_aff, 'delete');

	    if ($deleteGnose) {
		//On créer le projet dans l'entrepot gnose
		$uri   = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$aff['dir_aff'];
		$CmdBase = "svn rm --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." ";
		if ($GLOBALS['LOG']['DisplayDebug']) {
		    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
		}
		Logg::loggerInfo('affaireModel::deleteAffaireInDB() ~ suppression du répertoire de travail de l\'affaire '.$id_aff,$CmdBase.$uri,__FILE__.'@'.__LINE__);
		shell_exec($CmdBase.$uri);
		$uri		= " -m \"Suppression de l'affaire ".$id_aff." réalisée par ".$_SESSION["user"]["fullnom"]."\" ".$uri;
		$CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
		$CmdBase1	= "export LANG=\"en_US.UTF-8\"; export LC_CTYPE=\"en_US.UTF-8\"; export EDITOR=\"vi\"; svn update --non-interactive --no-auth-cache --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
		if ($GLOBALS['LOG']['DisplayDebug']) {
		    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri;
		    $GLOBALS['LogSVNProcess'][] = $CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &";
		}
		shell_exec($CmdBase.$uri);
		shell_exec($CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &");
	    }
	}
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function archivateAffaireInDB($id_aff,$moveGnose = TRUE) {
	if ($id_aff != '') {
	    $bddtmp = new AffaireModel();
	    $bddtmp->makeRequeteSelect('affaire','id_aff',$id_aff);
	    $aff = $bddtmp->process();
	    $aff = $aff[0];
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_aff, $bddtmp->bindVarAffaire(array('archived_aff' => '1','actif_aff' => '0','status_aff' => '18')));
	    Logg::loggerInfo('affaireModel::archivateAffaireInDB() ~ archivage de l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_aff, 'archive', 'L\'affaire '.$id_aff.' est maintenant archivée. Cette affaire ne pleut plus être modifiée. Les documents sont dorénavant consultables dans le répertoire '.$aff['dir_aff'].' des affaires archivés.', 'changement par lot', '',false);

	    if ($moveGnose) {
		$uri   = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$aff['dir_aff'];
		$uriTo = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['ArchivesDir'].$GLOBALS['ZunoAffaire']['dir.archives'].$aff['dir_aff'];
		$CmdBase = "svn mv --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." ";
		if ($GLOBALS['LOG']['DisplayDebug']) {
		    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri." ".$uriTo;
		}
		Logg::loggerInfo('affaireModel::deleteAffaireInDB() ~ archivage du répertoire de travail de l\'affaire '.$id_aff,$CmdBase." ".$uriTo,__FILE__.'@'.__LINE__);
		shell_exec($CmdBase.$uri." ".$uriTo);
		$uri		= " -m \"Archivage de l'affaire ".$id_aff." réalisée par ".$_SESSION["user"]["fullnom"]."\" ".$uri." ".$uriTo;
		$CmdBase	= "export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
		$CmdBase1	= "export LANG=\"en_US.UTF-8\"; export LC_CTYPE=\"en_US.UTF-8\"; export EDITOR=\"vi\"; svn update --non-interactive --no-auth-cache --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION["user"]["id"]." ";
		if ($GLOBALS['LOG']['DisplayDebug']) {
		    $GLOBALS['LogSVNProcess'][] = $CmdBase.$uri." ".$uriTo;
		    $GLOBALS['LogSVNProcess'][] = $CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &";
		}
		shell_exec($CmdBase.$uri." ".$uriTo);
		shell_exec($CmdBase1.$GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir']." &");
	    }
	}
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function activateAffaireInDB($id_aff) {
	if ($id_aff != '') {
	    $bddtmp = new AffaireModel();
	    $bddtmp->makeRequeteSelect('affaire','id_aff',$id_aff);
	    $aff = $bddtmp->process();
	    $aff = $aff[0];
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_aff, $bddtmp->bindVarAffaire(array('actif_aff' => '1','status_aff' => '20')));
	    Logg::loggerInfo('affaireModel::activateAffaireInDB() ~ activation de l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_aff, 'update', 'L\'affaire '.$id_aff.' est maintenant activée. Vous pouvez de nouveau travailler sur cette affaire', 'changement par lot', '',false);
	    return true;
	}
	Logg::loggerAlert('affaireModel::activateAffaireInDB() ~ impossible d\'activer l\'affaire '.$id_aff,'',__FILE__.'@'.__LINE__);
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function desactivateAffaireInDB($id_aff) {
	if ($id_aff != '') {
	    $bddtmp = new AffaireModel();
	    $bddtmp->makeRequeteSelect('affaire','id_aff',$id_aff);
	    $aff = $bddtmp->process();
	    $aff = $aff[0];
	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_aff, $bddtmp->bindVarAffaire(array('actif_aff' => '0','status_aff' => '19')));
	    Logg::loggerInfo('affaireModel::activateAffaireInDB() ~ désactivation de l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	    $bddtmp->process();
	    $bddtmp->addActualite($id_aff, 'update', 'L\'affaire '.$id_aff.' est maintenant désactivée. Vous ne pouvez plus travailler sur cette affaire', 'changement par lot', '',false);
	    return true;
	}
	Logg::loggerAlert('affaireModel::activateAffaireInDB() ~ impossible de désactiver l\'affaire '.$id_aff,'',__FILE__.'@'.__LINE__);
    }

    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributeAffaireInDB($id_aff,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('titre_aff', $newAttribList) or
		array_key_exists('necheance_aff', $newAttribList) or
		array_key_exists('typeproj_aff', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_aff != '' and $hasChange) {
	    $bddtmp = new AffaireModel();
	    $bddtmp->makeRequeteSelect('affaire','id_aff',$id_aff);
	    $aff = $bddtmp->process();
	    $aff = $aff[0];
	    if($newAttribList['titre_aff'] != '')
		$newData['titre_aff'] = $newAttribList['titre_aff'];
	    if($newAttribList['necheance_aff'] != '')
		$newData['echeance_aff'] = $newAttribList['necheance_aff'];
	    if($newAttribList['typeproj_aff'] != '')
		$newData['typeproj_aff'] = $newAttribList['typeproj_aff'];

	    $sql = $bddtmp->makeRequeteUpdate($bddtmp->sqlTableName, $bddtmp->sqlIdField,$id_aff, $bddtmp->bindVarAffaire($newData));
	    $bddtmp->process();
	    $bddtmp->addActualite($id_dev, 'update', '', 'changement par lot', '',false);
	    Logg::loggerInfo('affaireModel::changeAttributeAffaireInDB() ~ changement d\'attribut de l\'affaire '.$id_aff,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('affaireModel::changeAttributeAffaireInDB() ~ impossible de changer les attributs de l\'affaire '.$id_aff,'',__FILE__.'@'.__LINE__);
    }


    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function affaireGenerateID() {
	$id_aff = date('ym');
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT id_aff FROM affaire WHERE id_aff LIKE '".$id_aff."%' ORDER BY id_aff DESC LIMIT 1");
	$res = $bddtmp->process();
	$lastid = $res[0];
	if($lastid['id_aff'] != '') {
	    $lastDigit = (int) substr($lastid['id_aff'],4);
	    $lastDigit++;
	    if($lastDigit < 10) $lastDigit = "0".$lastDigit;
	}
	else $lastDigit = "01";
	$id_aff .= $lastDigit;
	return $id_aff;
    }

    /**
     * CPortlet de visualisation d'une affaire
     * @param $id_ent String: company ID
     * @return HTML portlet ready to insert
     */
    static function affaireExportTableurConverter($req,$format = '') {
	loadPlugin(array('OOConverter'));
	$availableFormat = OOConverterAvailable('spreadsheet');
	if(!array_key_exists($format,$availableFormat)) $format = 'pdf';
	$fileFrom = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].affaireModel::affaireExportTableur($req);
	$formatExt = ($availableFormat[$format] != '') ? substr($availableFormat[$format],strpos($availableFormat[$format], '[')+2,-1) : $format;
	$fromFormat  = FileGetExtention($fileFrom);
	$fileTo = substr($fileFrom,0, (strlen($fromFormat)+1)*-1).'.'.$formatExt;
	if($format == 'ods') return $fileTo;
	$return = OOConverter($fileFrom,$fileTo,$format);
	if($return === true) return $fileTo;
	else			   return $return;
    }

    /**
     * Create portlet for Company detail
     * @param $id_ent String: company ID
     * @return HTML portlet ready to insert
     */
    static function affaireExportTableur($req) {
	loadPlugin(array('OOConverter'));
	$cannevasPath = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['PropsecConf']['dir.cannevas.exportTableur'];
	$cannevasFile = $cannevasPath.$GLOBALS['ZunoAffaire']['cannevas.exportTableur'];

	if(is_file($cannevasFile)) {
	    $extIn 	= FileGetExtention($cannevasFile);
	    $TmpPath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $ExportTmpPath = $TmpPath.'ExportTmp/';
	    @rm($ExportTmpPath);
	    mkdir($ExportTmpPath);
	    loadPlugin(array('pclzip'));
	    $zip = new PclZip($cannevasFile);
	    $list = $zip->extract(PCLZIP_OPT_PATH,$ExportTmpPath);

	    $contentFileUri = $ExportTmpPath.'content.xml';
	    $xmlContent = file_get_contents($ExportTmpPath.'content.xml');

	    //config des convertion de champs
	    $TrimFrom = array("\n", "\t", "&", "\\", "\'", "<",">");
	    $TrimTo  = array(" ", " ", " et ", "", "'", "&lt;","&gt;");

	    //config de la base de donnée
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);

	    // On recherche Les infos de l'affaire
	    $affaire = $req;

	    $pos1 = strpos($xmlContent,'{id_aff}');
	    $debut = substr($xmlContent,0,$pos1);
	    $fin = substr($xmlContent,$pos1);
	    $from = strrpos($debut,'<table:table-row');
	    $to = (strlen($fin)-strpos($fin,'</table:table-row>')-18)*-1;
	    $exportDebutDocument = substr($xmlContent,0,$from);
	    $exportFinDocument = substr($xmlContent,$to);
	    $GLOBALS['TmpTemplate']['exportMiddleDocument'] = substr($xmlContent,$from,$to);
	    if (count($affaire) > 0) {
		foreach ($affaire as $kk => $aff) {
		    $aff = str_replace($TrimFrom,$TrimTo,$aff);
		    $aff['modif_aff'] 	= ($aff['modif_aff'] == '') 		? '' : strftime("%d/%m/%y",strtotime($aff['modif_aff']));
		    $aff['detect_aff'] 	= ($aff['detect_aff'] == '') 		? '' : strftime("%d/%m/%y",strtotime($aff['detect_aff']));
		    $aff['echeance_aff'] 	= ($aff['echeance_aff'] == '') 	? '' : strftime("%d/%m/%y",strtotime($aff['echeance_aff']));
		    $aff['daterecord_dev']	= ($aff['daterecord_dev'] == '') 	? '' : strftime("%d/%m/%y",strtotime($aff['daterecord_dev']));
		    $aff['datemodif_dev']	= ($aff['datemodif_dev'] == '') 	? '' : strftime("%d/%m/%y",strtotime($aff['datemodif_dev']));
		    $aff['daterecord_cmd']	= ($aff['daterecord_cmd'] == '') 	? '' : strftime("%d/%m/%y",strtotime($aff['daterecord_cmd']));
		    $aff['datemodif_cmd']	= ($aff['datemodif_cmd'] == '') 	? '' : strftime("%d/%m/%y",strtotime($aff['datemodif_cmd']));
		    $aff['daterecord_fact'] = ($aff['daterecord_fact'] == '') 	? '' : strftime('%d/%m/%y',strtotime($aff['daterecord_fact']));
		    $aff['dateenvoi_fact']	= ($aff['dateenvoi_fact'] == '') 	? '' : strftime("%d/%m/%y",strtotime($aff['dateenvoi_fact']));
		    $aff['datereglement_fact'] = ($aff['datereglement_fact'] == '') ? '' : strftime("%d/%m/%y",strtotime($aff['datereglement_fact']));
		    $aff['budget_aff'] 	= ($aff['budget_aff'] == '') 		? '' : formatCurencyDisplay($aff['budget_aff'],0);
		    $aff['sommeHT_dev'] 	= ($aff['sommeHT_dev'] == '') 	? '' : formatCurencyDisplay($aff['sommeHT_dev'],2);
		    $aff['sommeHT_cmd'] 	= ($aff['sommeHT_cmd'] == '') 	? '' : formatCurencyDisplay($aff['sommeHT_cmd'],2);
		    $aff['sommeHT_fact'] 	= ($aff['sommeHT_fact'] == '') 	? '' : formatCurencyDisplay($aff['sommeHT_fact'],2);
		    foreach ($aff as $in => $out) {
			$input[] = "/\{".$in."\}/";
			$output[] = $out;
		    }
		    $affaires .= ProcessTemplating('exportMiddleDocument',$input,$output);
		    unset($input);
		    unset($output);
		}
	    }
	    $xmlContent = $exportDebutDocument.$affaires.$exportFinDocument;
	    unset($affaires);

	    File_Add2File($ExportTmpPath.'content.xml',$xmlContent,TRUE);
	    $ArchivePath = $GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'];
	    $ODTName = 'AffaireExport.ods';

	    @rm($ArchivePath.$ODTName);
	    $archive = new PclZip($ArchivePath.$ODTName);
	    $archive->add($ExportTmpPath,PCLZIP_OPT_REMOVE_PATH,$ExportTmpPath.'/');
	    $outputFile = $ODTName;
	    return $outputFile;
	}
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table affaire
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarAffaire($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}
?>
