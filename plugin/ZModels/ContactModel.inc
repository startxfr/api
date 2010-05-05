<?php
include_once('GeneralModel.inc');

/**
 * Classe d'accès aux information d'un particulier
 *
 * @author Nicolas Mannocci
 * @version 1
 */
class contactParticulierModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'contact';
	$this->sqlIdField = 'id_cont';
	$this->availableFields = array(
		'id_cont','civ_cont','prenom_cont','nom_cont',
		'add1_cont','add2_cont','cp_cont',
		'ville_cont','pays_cont','tel_cont','mob_cont',
		'fax_cont','www_cont','fonction_cont',
		'comm_cont','mail_cont','relactive_cont', 'LD_cont',
		'entreprise_cont');
	parent::__construct();
    }

    /**
     * Méthode qui récupère les informations d'un contact depuis son id.
     * @param int $id L'id du contact
     * @return array Les données du contact
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT *
		FROM contact
		LEFT JOIN ref_pays ON ref_pays.id_pays = contact.pays_cont
		LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
		LEFT JOIN entreprise ON entreprise.id_ent = contact.entreprise_cont
		LEFT JOIN ref_typeentreprise rte ON rte.id_tyent = entreprise.type_ent
		WHERE id_cont = '".trim($id)."'");
	return $this->process2();
    }

    /**
     * Méthode qui s'occupe de faire une recherche de contact
     * @param string $search La chaine recherchée
     * @param int $limit Le nombre maximum de résultats
     * @param int $from L'index du premier résultat
     * @return array Les résultats
     */
    public function getDataForSearch($search,$limit = 10, $from = 0) {
	$qTag = " LIKE '%".trim($search)."%' ";
	if($limit == 'ALL') {
	    $this->makeRequeteFree("SELECT COUNT(*) " .
		    "FROM contact " .
		    "LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont " .
		    "WHERE nom_cont $qTag OR prenom_cont $qTag OR mail_cont $qTag");
	}
	else {
	    $this->makeRequeteFree("SELECT id_cont,civ_cont,nom_cont,prenom_cont,nom_fct " .
		    "FROM contact " .
		    "LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont " .
		    "WHERE nom_cont $qTag OR prenom_cont $qTag OR mail_cont $qTag" .
		    "ORDER BY nom_cont ASC LIMIT $from , $limit");
	}

	return $this->process2();
    }

    /**
     * Méthode qui récupère les dernières fiches pour historique
     * @param int $limit Le nombre maximum de résultats
     * @param string $type Précise si on veut un COUNT ou les résultats
     * @param string $order L'ordre de tri
     * @return array Les résultats
     */
    public function getDataForHistoriqueVisit($limit ='10', $type = '', $order='ORDER BY id_cont DESC') {
	if(array_key_exists('historiqueVisite',$_SESSION) and
		array_key_exists('contactParticulier',$_SESSION['historiqueVisite']) and
		count($_SESSION['historiqueVisite']['contactParticulier']) > 0) {
	    $list = '';
	    foreach($_SESSION['historiqueVisite']['contactParticulier'] as $id)
		$list .= '\''.$id.'\', ';
	    $list = '('.substr($list,0,-2).')';
	}
	else $list = '(\'fakeNull\')';
	if($type == 'COUNT')
	    $select = 'SELECT COUNT(*) AS counter';
	else $select = 'SELECT *';
	$this->makeRequeteFree($select." FROM contact
			LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
		LEFT JOIN entreprise ON entreprise.id_ent = contact.entreprise_cont
		LEFT JOIN ref_typeentreprise rte ON rte.id_tyent = entreprise.type_ent
			WHERE id_cont IN $list $order LIMIT 0, $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui récupère le nombre total de fournisseurs liés à un contact
     * @param int $id L'id du contact
     * @return string Le nombre de fournisseurs
     */
    public function getTotalFournisseurFromID($id) {
	$this->makeRequeteFree("select count(*) from fournisseur where contactComm_fourn = ".$id.";");
	$o = $this->process2();
	return $o[1][0]['count(*)'];
    }

    /**
     * Méthode qui s'occupe de faire les insertions de contacts dans la base
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insert($data) {
	if(!array_key_exists($this->sqlIdField,$data)) $data[$this->sqlIdField] = ($this->getLastId()+1);
	$data['nom_cont'] = strtoupper($data['nom_cont']);
	$data['ville_cont'] = strtoupper($data['ville_cont']);
	$data['pays_cont'] = ($data['pays_cont'] != '') ? $data['pays_cont'] : '1';
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarContact($data));
	Logg::loggerInfo('contactParticulierModel::insert() ~ ajout du contact '.$data['id_cont'].' : '.$data['civ_cont'].' '.$data['prenom_cont'].' '.$data['nom_cont'],$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui s'occupe de mettre à jour les infos d'un contact
     * @param array $data Les données mises à jour
     * @param int $id_cont L'id du contact à mettre à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function update($data,$id_cont) {
	if(array_key_exists($this->sqlIdField,$data))
	    unset($data['id_cont']);
	if(array_key_exists('nom_cont', $data))
	    $data['nom_cont'] = strtoupper($data['nom_cont']);
	if(array_key_exists('ville_cont', $data))
	    $data['ville_cont'] = strtoupper($data['ville_cont']);
	if(array_key_exists('pays_cont', $data))
	    $data['pays_cont'] = ($data['pays_cont'] != '') ? $data['pays_cont'] : '1';

	$sql = $this->makeRequeteUpdate($this->sqlTableName,"id_cont",$id_cont,$this->bindVarContact($data));
	Logg::loggerInfo('contactParticulierModel::update() ~ mise à jour du contact '.$id_cont,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;

    }

    /**
     * Méthode qui supprime un contact de la base
     * @param int $id_cont L'id du contact à supprimer
     * @return array Indique si la requête s'est déroulée correctement
     */
    public function delete($id_cont) {
	$sql = $this->makeRequeteFree("DELETE FROM $this->sqlTableName Where id_cont = $id_cont");
	Logg::loggerInfo('contactParticulierModel::delete() ~ suppression du contact '.$id_cont,$sql,__FILE__.'@'.__LINE__);
	return $this->process2();

    }

    /**
     * Méthode qui récupère les informations des affaires liées à un contact
     * @param int $id L'id du contact dont on veut les affaires
     * @param string $plus Un complément à la requête sql
     * @return array Les affaires du contact
     */
    public function getData4Affaire($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM affaire WHERE contact_aff = \''.$id.' '.$plus."' ORDER BY id_aff DESC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations des devis liés à un contact
     * @param int $id L'id du contact dont on veut les devis
     * @param string $plus Un complément à la requête sql
     * @return array Les devis du contact
     */
    public function getData4Devis($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM devis WHERE contact_dev = \''.$id.' '.$plus."' ORDER BY id_dev DESC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations des commandes liées à un contact
     * @param int $id L'id du contact dont on veut les commandes
     * @param string $plus Un complément à la requête sql
     * @return array Les commandes du contact
     */
    public function getData4Commande($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM commande WHERE contact_cmd = \''.$id.' '.$plus."' ORDER BY id_cmd DESC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations des factures liées à un contact
     * @param int $id L'id du contact dont on veut les factures
     * @param string $plus Un complément à la requête SQL
     * @return array Les factures du contact
     */
    public function getData4Facture($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM facture WHERE contact_fact = \''.$id.' '.$plus."' ORDER BY id_fact DESC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les informations sur les projets liés à une entreprise d'un contact
     * @param int $id L'id du contact dont on veut les projets de l'entreprise
     * @return array Les projets de l'entreprise du contact
     */
    public function getProjets($id = '') {
	$this->makeRequeteFree("SELECT * from projet LEFT JOIN contact on contact.id_cont = projet.contact_proj LEFT JOIN ref_typeproj rtp ON rtp.id_typro = projet.typeproj_proj where entreprise_cont = (SELECT entreprise_cont from contact where id_cont = $id) AND entreprise_cont IS NOT NULL ;");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
    }

    /**
     * Méthode qui récupère les informations sur les affaires liées à une entreprise d'un contact
     * @param int $id L'id du contact dont on veut les affaires de l'entreprise
     * @returnarray Les affaires de l'entreprise du contact
     */
    public function getAffaires($id = '') {
	$this->makeRequeteFree("SELECT * from affaire LEFT JOIN contact ON contact.id_cont = affaire.contact_aff LEFT JOIN ref_statusaffaire rsa on rsa.id_staff = affaire.status_aff where entreprise_aff = (SELECT entreprise_cont from contact where id_cont = $id) AND entreprise_aff IS NOT NULL ;");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
    }

    /**
     * Méthode qui récupère les informations sur les appels à une entreprise d'un contact
     * @param int $id L'id du contact de l'entreprise dont on veut les appels
     * @return array Les appels de l'entreprise du contact
     */
    public function getAppels($id = '') {
	$this->makeRequeteFree("SELECT * from appel LEFT JOIN contact on contact.id_cont = appel.contact_app where entreprise_cont = (SELECT entreprise_cont from contact where id_cont = $id) AND entreprise_cont IS NOT NULL ;");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
    }

    /**
     * Méthode qui récupère la liste des collègues d'un contact (contact dans la même entreprise)
     * @param int $id L'id du contact dont on veut les collègues
     * @return array Les collègues du contact
     */
    public function getCollegue($id = '') {
	$this->makeRequeteFree("SELECT * FROM contact LEFT JOIN entreprise e ON e.id_ent = contact.entreprise_cont WHERE e.id_ent = (SELECT c2.entreprise_cont from contact c2 where c2.id_cont = $id) and id_cont != $id ORDER BY nom_cont ");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
    }



    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributeInDB($id_cont,$newAttribList = array()) {
	$hasChange = false;

	if(array_key_exists('fonction_cont', $newAttribList) or
		array_key_exists('comm_cont', $newAttribList) or
		array_key_exists('ville_cont', $newAttribList) or
		array_key_exists('cp_cont', $newAttribList) or
		array_key_exists('relactive_cont', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_cont != '' and $hasChange) {
	    $bddtmp = new ContactParticulierModel();
	    $bddtmp->makeRequeteSelect('contact','id_cont',$id_cont);
	    $contact = $bddtmp->process();
	    $contact = $contact[0];
	    if($newAttribList['fonction_cont'] != '')
		$newData['fonction_cont'] = $newAttribList['fonction_cont'];
	    if($newAttribList['comm_cont'] != '')
		$newData['comm_cont'] = $newAttribList['comm_cont'];
	    if($newAttribList['ville_cont'] != '')
		$newData['ville_cont'] = $newAttribList['ville_cont'];
	    if($newAttribList['cp_cont'] != '')
		$newData['cp_cont'] = $newAttribList['cp_cont'];
	    if($newAttribList['relactive_cont'] != '')
		$newData['relactive_cont'] = $newAttribList['relactive_cont'];

	    $sql = $bddtmp->makeRequeteUpdate('contact','id_cont',$id_cont,$newData);
	    $bddtmp->process();
	    Logg::loggerInfo('contactParticulierModel::changeAttributeInDB() ~ changement d\'attribut du contact '.$id_cont,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('contactParticulierModel::changeAttributeInDB() ~ impossible de changer les attributs du contact '.$id_cont,'',__FILE__.'@'.__LINE__);
    }


    static function deleteInDB($id_cont,$newAttribList = array()) {
	if ($id_cont != '') {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $sql = $bddtmp->makeRequeteDelete('contact',array('id_cont'=>$id_cont));
	    $bddtmp->process();
	    Logg::loggerInfo('contactParticulierModel::deleteInDB() ~ Contact '.$id_cont.'supprimé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('contactParticulierModel::deleteInDB() ~ impossible de supprimer le contact '.$id_cont,'',__FILE__.'@'.__LINE__);
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

	$this->makeRequeteFree("SELECT *
			FROM contact
			LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
			LEFT JOIN entreprise ON entreprise.id_ent = contact.entreprise_cont
			LEFT JOIN ref_pays rfp ON rfp.id_pays = contact.pays_cont
			LEFT JOIN ref_activite ON ref_activite.id_act = entreprise.activite_ent
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
		WHERE id_cont IN $sqlList
		ORDER BY nom_cont ASC,id_cont ASC");
	return $this->process2();
    }


    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table contact
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarContact($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}




/**
 * Class d'accès aux information d'une entreprise
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class contactEntrepriseModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'entreprise';
	$this->sqlIdField = 'id_ent';
	$this->availableFields = array(
		'id_ent','type_ent','nom_ent','fax_ent',
		'add1_ent','add2_ent','cp_ent',
		'ville_ent','pays_ent','tel_ent',
		'telsi_ent','www_ent','activite_ent',
		'effectif_ent','SIRET_ent','numeroTVA_ent',
		'tauxTVA_ent','codefourn_ent', 'remise_ent',
		'groupe_ent','siege_ent',
		'commsociete_ent','codeAPE_ent');
	parent::__construct();
    }

    /**
     * Méthode qui récupère toutes les informations liées à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les infos
     * @return array Les informations de l'entreprise
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT *,MD5(CONCAT(entreprise.id_ent,'-',entreprise.cp_ent))as tokenRss
		FROM entreprise
		LEFT JOIN ref_pays ON ref_pays.id_pays = entreprise.pays_ent
		LEFT JOIN ref_typeentreprise ON ref_typeentreprise.id_tyent = entreprise.type_ent
		LEFT JOIN ref_activite ON ref_activite.id_act = entreprise.activite_ent
		WHERE id_ent = '".trim($id)."'");
	$o = $this->process2();

	if($o[0]) {
	    $this->makeRequeteFree("SELECT * FROM contact
			LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
			WHERE entreprise_cont = '".trim($id)."' ORDER BY nom_cont ASC");
	    $p = $this->process2();
	    if($p[0] and count($p[1]) > 0) $o[1][0]['contact'] = $p[1];
	}
	return $o;
    }

    /**
     * Méthode qui récupère le nombre total de contacts liés à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les contacts
     * @return string Le nombre de contacts de l'entreprise
     */
    public function getTotalParticuliersFromID($id) {
	$this->makeRequeteFree("select count(*) from contact where entreprise_cont = ".$id.";");
	$o = $this->process2();
	return $o[1][0]['count(*)'];
    }

    /**
     * Méthode qui récupère la liste des fournisseurs liés à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les fournisseurs
     * @return string Le nombre de fournisseurs liés à l'entreprise
     */
    public function getTotalFournisseurFromID($id) {
	$this->makeRequeteFree("select count(*) from fournisseur where entreprise_fourn = ".$id.";");
	$o = $this->process2();
	return $o[1][0]['count(*)'];
    }

    /**
     * Méthode qui effectue une recherche sur les entreprises
     * @param string $search Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @return array Le résultat de la recherche
     */
    public function getDataForSearch($search,$limit = 10, $from = 0) {
	$qTag = " LIKE '%".trim($search)."%' ";
	if($limit == 'ALL') {
	    $this->makeRequeteFree("SELECT COUNT(*) " .
		    "FROM entreprise " .
		    "WHERE nom_ent $qTag OR ville_ent $qTag OR cp_ent $qTag");
	}
	else {
	    $this->makeRequeteFree("SELECT * " .
		    "FROM entreprise " .
		    "WHERE nom_ent $qTag OR ville_ent $qTag OR cp_ent $qTag" .
		    "ORDER BY nom_ent ASC
			 LIMIT $from , $limit");
	}
	return $this->process2();
    }

    /**
     * Méthode qui effectue une recherche avancée sur les entreprises, contacts, appels, projets et affaires liées à une entreprise
     * @param string $recherche Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @return array Les résultats
     */
    public function getDataForSearchGlobal($recherche = '', $limit = '30', $from = '0', $order = 'id_ent', $orderSens = 'ASC') {
	$search = " nom_ent LIKE '%".$recherche."%'";
	$search .= " OR ville_ent LIKE '%".$recherche."%'";
	$search .= " OR nom_cont LIKE '%".$recherche."%'";
	$search .= " OR prenom_cont LIKE '%".$recherche."%'";
	$search .= " OR tel_cont LIKE '%".$recherche."%'";
	$search .= " OR cp_ent LIKE '%".$recherche."%'";
	$search .= " OR cp_cont LIKE '%".$recherche."%'";
	//$requete = "(select id_cont, id_ent, nom_ent, ville_ent, tel_ent, type_ent, civ_cont, prenom_cont, nom_cont, mail_cont, tel_cont from contact left join entreprise on id_ent = entreprise_cont where $search ) union (select id_cont, id_ent, nom_ent, ville_ent, tel_ent, type_ent, civ_cont, prenom_cont, nom_cont, mail_cont, tel_cont  from entreprise left join contact on entreprise_cont = id_ent where $search) ";
	$requete = "select id_cont, id_ent, nom_ent, ville_ent, tel_ent, type_ent, civ_cont, prenom_cont, nom_cont, mail_cont, tel_cont from contact left join entreprise on id_ent = entreprise_cont where $search";

	if($order != '')
	    $endSql = ' ORDER BY '.$order.' '.((strtolower($orderSens) == 'asc') ? 'ASC' : 'DESC');

	if($limit == 'ALL') {
	    $this->makeRequeteFree("SELECT COUNT(*) from ( ".$requete." ) as virtual ");
	}
	else {
	    $this->makeRequeteFree("$requete $endSql LIMIT $from, $limit ");
	}
	return $this->process2();
    }

    /**
     * Méthode qui effectue une recherche avancée sur les entreprises et les contacts liés
     * @param string $recherche Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @return array Les résultats
     */
    public function getDataForSearchEntWeb($recherche = '', $limit = '30', $from = '0', $order = 'id_ent', $orderSens = 'ASC') {
	$search = " nom_ent LIKE '%".$recherche."%'";
	$search .= " OR ville_ent LIKE '%".$recherche."%'";
	$search .= " OR nom_cont LIKE '%".$recherche."%'";
	$search .= " OR prenom_cont LIKE '%".$recherche."%'";
	$search .= " OR tel_cont LIKE '%".$recherche."%'";
	$search .= " OR cp_ent LIKE '%".$recherche."%'";
	$search .= " OR cp_cont LIKE '%".$recherche."%'";

	if($order != '')
	    $endSql = $order.' '.((strtolower($orderSens) == 'asc') ? 'ASC' : 'DESC').', ';

	if($limit == 'ALL') {
	    $this->makeRequeteFree("select COUNT(*) from entreprise " .
		    "left join contact on entreprise_cont = id_ent " .
		    "where id_ent is not null and $search ");
	}
	else {
	    $this->makeRequeteFree("select id_ent, id_cont, nom_ent, ville_ent, tel_ent, type_ent, civ_cont, prenom_cont, nom_cont, mail_cont from entreprise " .
		    "left join contact on entreprise_cont = id_ent " .
		    "where id_ent is not null and $search order by $endSql nom_ent ASC, id_ent ASC, nom_cont ASC ");
	}
	return $this->process2();
    }

    /**
     * Méthode qui permet de rechercher des particuliers (contacts non entreprise, mais potentiellement liés à une entreprise)
     * @param string $recherche Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @return array Les résultats
     */
    public function getDataForSearchContWeb($recherche = '', $limit = '30', $from = '0', $order = 'id_ent', $orderSens = 'ASC') {
	$search = " nom_ent LIKE '%".$recherche."%'";
	$search .= " OR ville_ent LIKE '%".$recherche."%'";
	$search .= " OR nom_cont LIKE '%".$recherche."%'";
	$search .= " OR prenom_cont LIKE '%".$recherche."%'";
	$search .= " OR tel_cont LIKE '%".$recherche."%'";
	$search .= " OR cp_ent LIKE '%".$recherche."%'";
	$search .= " OR cp_cont LIKE '%".$recherche."%'";

	if($order != '')
	    $endSql = $order.' '.((strtolower($orderSens) == 'asc') ? 'ASC' : 'DESC').', ';

	if($limit == 'ALL') {
	    $this->makeRequeteFree("select COUNT(*) from contact " .
		    "left join entreprise on id_ent = entreprise_cont " .
		    "where id_cont is not null and $search ");
	}
	else {
	    $this->makeRequeteFree("select id_ent, id_cont, nom_ent, ville_ent, tel_ent, type_ent, civ_cont, prenom_cont, nom_cont, mail_cont from contact " .
		    "left join entreprise on id_ent = entreprise_cont " .
		    "where id_cont is not null and $search order by $endSql nom_ent ASC, id_ent ASC, nom_cont ASC ");
	}
	return $this->process2();
    }

    /**
     * Méthode qui recherche des appels
     * @param string $recherche Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param int $from L'index du premier résultat
     * @return array Les résultats
     */
    public function getDataForSearchAppWeb($recherche = '', $limit = '30', $from = '0', $order = 'id_ent', $orderSens = 'ASC') {
	$search = " nom_ent LIKE '%".$recherche."%'";
	$search .= " OR ville_ent LIKE '%".$recherche."%'";
	$search .= " OR nom_cont LIKE '%".$recherche."%'";
	$search .= " OR prenom_cont LIKE '%".$recherche."%'";
	$search .= " OR tel_cont LIKE '%".$recherche."%'";
	$search .= " OR cp_ent LIKE '%".$recherche."%'";
	$search .= " OR cp_cont LIKE '%".$recherche."%'";
	$search .= " OR affaire_app LIKE '%".$recherche."%' ";
	$search .= " OR comm_app LIKE '%".$recherche."%' ";

	if($order != '')
	    $endSql = $order.' '.((strtolower($orderSens) == 'asc') ? 'ASC' : 'DESC').', ';

	if($limit == 'ALL') {
	    $this->makeRequeteFree("select COUNT(*) from appel " .
		    "left join contact on id_cont = contact_app left join entreprise on id_ent = entreprise_cont " .
		    "where id_app is not null and $search ");
	}
	else {
	    $this->makeRequeteFree("select id_app, id_ent, id_cont, nom_ent, ville_ent, tel_ent, type_ent, civ_cont, prenom_cont, nom_cont, mail_cont from appel " .
		    "left join contact on id_cont =contact_app left join entreprise on id_ent = entreprise_cont " .
		    "where id_app is not null and $search order by $endSql nom_ent ASC, id_ent ASC, nom_cont ASC, id_app ASC ");
	}
	return $this->process2();
    }

    /**
     * Méthode qui récupère l'historique des visites sur les fiches entreprises
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param string $type Précise si on veut un COUNT ou toutes les infos
     * @param string $order Précise l'ordre de tri des résultats
     * @return array Les données
     */
    public function getDataForHistoriqueVisit($limit ='10', $type = '', $order='ORDER BY id_ent DESC') {
	if(array_key_exists('historiqueVisite',$_SESSION) and
		array_key_exists('contactEntreprise',$_SESSION['historiqueVisite']) and
		count($_SESSION['historiqueVisite']['contactEntreprise']) > 0) {
	    $list = '';
	    foreach($_SESSION['historiqueVisite']['contactEntreprise'] as $id)
		$list .= '\''.$id.'\', ';
	    $list = '('.substr($list,0,-2).')';
	}
	else $list = '(\'fakeNull\')';
	if($type == 'COUNT')
	    $select = 'SELECT COUNT(*) AS counter';
	else $select = 'SELECT *';
	$this->makeRequeteFree($select." FROM entreprise
			LEFT JOIN ref_typeentreprise ON ref_typeentreprise.id_tyent = entreprise.type_ent
			WHERE id_ent IN $list $order LIMIT 0, $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui s'occupe de l'insertion d'entreprise dans la base
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insert($data) {
	if($data['www_ent'] == "http://")
	    unset($data['www_ent']);
	if(!array_key_exists($this->sqlIdField,$data)) $data[$this->sqlIdField] = ($this->getLastId()+1);
	$data['nom_ent'] = strtoupper(str_replace(" ","",trim($data['nom_ent'])));
	$data['ville_ent'] = strtoupper($data['ville_ent']);
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarEntreprise($data));
	Logg::loggerInfo('contactEntrepriseModel::insert() ~ ajout de l\'entreprise '.$data['id_ent'].' : '.$data['nom_ent'],$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui s'occupe de mettre à jour une entreprise dans la base
     * @param array $data Les donnéesmises à jour
     * @param int $id_ent L'id de l'entreprise à mettre à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function update($data,$id_ent) {
	$data['nom_ent'] = strtoupper(str_replace(" ","",trim($data['nom_ent'])));
	$data['ville_ent'] = strtoupper($data['ville_ent']);
	$sql = $this->makeRequeteUpdate($this->sqlTableName,"id_ent",$id_ent,$this->bindVarEntreprise($data));
	Logg::loggerInfo('contactEntrepriseModel::update() ~ mise à jour de l\'entreprise '.$id_ent,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui supprime une entreprise de la base
     * @param int $id_ent L'id de l'entreprise à supprimer
     * @param bool $recursif Précise si on supprime els contacts liés ou pas
     * @return array Indique si la requête s'est bien déroulée
     */
    public function delete($id_ent, $recursif = false) {
	$sql = $this->makeRequeteFree("DELETE FROM $this->sqlTableName Where id_ent = $id_ent");
	Logg::loggerInfo('contactEntrepriseModel::delete() ~ suppression de l\'entreprise '.$id_ent,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	if($result[0] and $recursif) {
	    $sql = $this->makeRequeteFree("Delete from contact where entreprise_cont = ".$id_ent.";");
	    Logg::loggerInfo('contactEntrepriseModel::delete() ~ suppression des contacts de l\'entreprise '.$id_ent,$sql,__FILE__.'@'.__LINE__);
	    $result2 = $this->process2();
	}
	return $result;
    }

    /**
     * Méthode qui récupère les affaires liées à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les affaires
     * @param string $plus Un complément à la requête SQL
     * @return array les affaires liées à une entreprise
     */
    public function getData4Affaire($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM affaire WHERE entreprise_aff = \''.$id.' '.$plus."' ORDER BY id_aff DESC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les devis liés à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les devis
     * @param string $plus Un complément à la requête SQL
     * @return array Les devis liés à l'entreprise
     */
    public function getData4Devis($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM devis WHERE entreprise_dev = \''.$id.' '.$plus."' ORDER BY id_dev DESC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les commandes liées à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les commandes
     * @param string $plus Un complément à la requête SQL
     * @return array Les commandes liées à l'entreprise
     */
    public function getData4Commande($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM commande WHERE entreprise_cmd = \''.$id.' '.$plus."' ORDER BY id_cmd DESC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les factures liées à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les factures
     * @param string $plus Un complément à la requête SQL
     * @return array Les facture liées à l'entreprise
     */
    public function getData4Facture($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM facture WHERE entreprise_fact = \''.$id.' '.$plus."' ORDER BY id_fact DESC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère les produits liés à un fournisseur
     * @param int $id L'id du fournisseur
     * @param string $plus Un complément à la requête SQL
     * @return array Les produits liés au fournisseur
     */
    public function getData4Produit($id, $plus = '') {
	$this->makeRequeteFree('SELECT id_prod, nom_prod, prix_prod, prixF FROM produit LEFT JOIN produit_fournisseur on produit.id_prod = produit_fournisseur.produit_id WHERE fournisseur_id = \''.$id.'\' AND actif = \'1\' ORDER BY produit_id ');
	return $this->process2();
    }

    /**
     * Méthode qui récupère les contacts liés à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les contacts
     * @param string $plus Un complément à la requête SQL
     * @return array Les contacts liés à l'entreprise
     */
    public function getData4Contact($id, $plus = '') {
	$this->makeRequeteFree('SELECT * FROM contact WHERE entreprise_cont = \''.$id.' '.$plus."' ORDER BY nom_cont ASC");
	return $this->process2();
    }

    /**
     * Méthode qui récupère la liste des types possibles d'entreprise
     * @return array La liste des type possibles d'entreprise
     */
    public function getTypesEnt() {
	$this->makeRequeteFree("SELECT * from ref_typeentreprise ;");
	$temp = $this->process2();
	if($temp[0] and count($temp[1]) > 0)
	    foreach($temp[1] as $k => $v)
		$countryList[$v['id_tyent']] = $v['nom_tyent'];
	return $countryList;
    }

    /**
     * Méthode qui récupère la liste des entreprises déclarées comme siège sauf l'entreprise sélectionnée
     * @param int $id L'id de l'entreprise que l'on ne veut pas dans les résultats
     * @return array La liste des entreprises déclarées comme siège
     */
    public function getGroupesEnt($id = '') {
	$this->makeRequeteFree("SELECT nom_ent from entreprise where siege_ent = '1' and id_ent != $id ;");
	$temp = $this->process2();
	if($temp[0] and count($temp[1]) > 0)
	    foreach($temp[1] as $k => $v)
		$countryList[$v['id_ent']] = $v['nom_ent'];
	else
	    $countryList[0] = 'Aucun groupe';
	return $countryList;
    }

    /**
     * Méthode qui récupère la liste des activités possibles d'une entreprise
     * @return array La liste des activités possibles d'une entreprise
     */
    public function getActivitesEnt() {
	$this->makeRequeteFree("SELECT * from ref_activite ;");
	$temp = $this->process2();
	if($temp[0] and count($temp[1]) > 0)
	    foreach($temp[1] as $k => $v)
		$countryList[$v['id_act']] = $v['nom_act'];
	return $countryList;
    }

    /**
     * Méthode qui récupère la liste des projets liés à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les projets
     * @return array Les projets liés à une entreprise
     */
    public function getProjets($id = '') {
	$this->makeRequeteFree("SELECT * from projet LEFT JOIN contact on contact.id_cont = projet.contact_proj LEFT JOIN ref_typeproj rtp ON rtp.id_typro = projet.typeproj_proj where entreprise_cont = $id ;");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
    }

    /**
     * Méthode qui récupère la liste des affaires liées à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les affaires
     * @return array Les affaires liées à une entreprise
     */
    public function getAffaires($id = '') {
	$this->makeRequeteFree("SELECT * from affaire LEFT JOIN contact ON contact.id_cont = affaire.contact_aff LEFT JOIN ref_statusaffaire rsa on rsa.id_staff = affaire.status_aff where entreprise_aff = $id ;");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
    }

    /**
     * Méthode qui récupère les appels liés à une entreprise
     * @param int $id L'id de l'entreprise dont on veut les appels
     * @return array Les appels liés à une entreprise
     */
    public function getAppels($id = '') {
	$this->makeRequeteFree("SELECT * from appel LEFT JOIN contact on contact.id_cont = appel.contact_app where entreprise_cont = $id ;");
	$result = $this->process2();
	if($result[0])
	    return $result[1];
    }




    /**
     * Create portlet for calling list
     * @return Next affaire ID according to last inserted Affaire
     */
    static function changeAttributeInDB($id_ent,$newAttribList = array()) {
	$hasChange = false;
	if(array_key_exists('activite_ent', $newAttribList) or
		array_key_exists('type_ent', $newAttribList) or
		array_key_exists('remise_ent', $newAttribList) or
		array_key_exists('effectif_ent', $newAttribList) or
		array_key_exists('ville_ent', $newAttribList) or
		array_key_exists('cp_ent', $newAttribList) or
		array_key_exists('commsociete_ent', $newAttribList) or
		array_key_exists('tauxTVA_ent', $newAttribList) or
		array_key_exists('siege_ent', $newAttribList)) {
	    $hasChange = true;
	}
	if ($id_ent != '' and $hasChange) {
	    $bddtmp = new ContactEntrepriseModel();
	    $bddtmp->makeRequeteSelect('entreprise','id_ent',$id_ent);
	    $entreprise = $bddtmp->process();
	    $entreprise = $entreprise[0];
	    if($newAttribList['activite_ent'] != '')
		$newData['activite_ent'] = $newAttribList['activite_ent'];
	    if($newAttribList['type_ent'] != '')
		$newData['type_ent'] = $newAttribList['type_ent'];
	    if($newAttribList['remise_ent'] != '')
		$newData['remise_ent'] = $newAttribList['remise_ent'];
	    if($newAttribList['effectif_ent'] != '')
		$newData['effectif_ent'] = $newAttribList['effectif_ent'];
	    if($newAttribList['ville_ent'] != '')
		$newData['ville_ent'] = $newAttribList['ville_ent'];
	    if($newAttribList['cp_ent'] != '')
		$newData['cp_ent'] = $newAttribList['cp_ent'];
	    if($newAttribList['commsociete_ent'] != '')
		$newData['commsociete_ent'] = $newAttribList['commsociete_ent'];
	    if($newAttribList['tauxTVA_ent'] != '')
		$newData['tauxTVA_ent'] = $newAttribList['tauxTVA_ent'];
	    if(trim($newAttribList['siege_ent']) != '')
		$newData['siege_ent'] = $newAttribList['siege_ent'];

	    $sql = $bddtmp->makeRequeteUpdate('entreprise','id_ent',$id_ent,$newData);
	    $bddtmp->process();
	    Logg::loggerInfo('contactEntrepriseModel::changeAttributeInDB() ~ changement d\'attribut de l\'entreprise '.$id_ent,$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('contactEntrepriseModel::changeAttributeInDB() ~ impossible de changer les attributs de l\'entreprise '.$id_ent,'',__FILE__.'@'.__LINE__);
    }




    static function deleteInDB($id_ent,$newAttribList = array()) {
	if ($id_ent != '') {
	    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	    $sql = $bddtmp->makeRequeteDelete('entreprise',array('id_ent'=>$id_ent));
	    $bddtmp->process();
	    Logg::loggerInfo('contactEntrepriseModel::deleteInDB() ~ Entreprise '.$id_ent.'supprimé',$sql,__FILE__.'@'.__LINE__);
	    return true;
	}
	Logg::loggerAlert('contactEntrepriseModel::deleteInDB() ~ impossible de supprimer l\'entreprise '.$id_ent,'',__FILE__.'@'.__LINE__);
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table entreprise
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarEntreprise($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}

/**
 * Classe qui gère les appels dans la base
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class appelModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'appel';
	$this->sqlIdField = 'id_app';
	$this->availableFields = array(
		'id_app','contact_app', 'appel_app',
		'rappel_app', 'heure_app', 'comm_app',
		'utilisateur_app', 'affaire_app', 'projet_app');
	parent::__construct();
    }

    /**
     * Méthode qui gère l'insertion d'un appel dans la base
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insert($data) {
	if(!array_key_exists($this->sqlIdField,$data)) $data[$this->sqlIdField] = ($this->getLastId()+1);
	if(array_key_exists('appel_app', $data))
	    $data['appel_app'] = substr($data['appel_app'], 6,4).substr($data['appel_app'], 3,2).substr($data['appel_app'],0,2);
	if(array_key_exists('rappel_app', $data))
	    $data['rappel_app'] = substr($data['rappel_app'], 6,4).substr($data['rappel_app'], 3,2).substr($data['rappel_app'],0,2);
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarAppel($data));
	Logg::loggerInfo('appelModel::insert() ~ ajout d\'un appel avec le contact '.$data['contact_app'],$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui met à jour un appel dans la base
     * @param array $data Les données mises à jour
     * @param int $id L'id de l'appel à mettre à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function update($data, $id) {
	if(array_key_exists($this->sqlIdField,$data))
	    unset($data['id_app']);
	if(array_key_exists('appel_app', $data))
	    $data['appel_app'] = substr($data['appel_app'], 6,4).substr($data['appel_app'], 3,2).substr($data['appel_app'],0,2);
	if(array_key_exists('rappel_app', $data))
	    $data['rappel_app'] = substr($data['rappel_app'], 6,4).substr($data['rappel_app'], 3,2).substr($data['rappel_app'],0,2);
	$sql = $this->makeRequeteUpdate($this->sqlTableName,$this->sqlIdField,$id,$this->bindVarAppel($data));
	Logg::loggerInfo('appelModel::update() ~ mise à jour de l\'appel '.$id,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui s'occupe de la suppression d'un appel
     * @param int $id L'id de l'appel à supprimer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function delete($id) {
	$sql = $this->makeRequeteFree("DELETE FROM $this->sqlTableName Where $this->sqlIdField = $id");
	Logg::loggerInfo('appelModel::delete() ~ suppression de l\'appel '.$id,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui récupère les informations d'un appel
     * @param int $id L'id de l'appel dont on veut les informations
     * @return array Les informations de l'appel
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT * from appel LEFT JOIN contact c on c.id_cont = appel.contact_app LEFT JOIN entreprise e ON e.id_ent = c.entreprise_cont LEFT JOIN projet ON projet.id_proj = appel.projet_app LEFT JOIN affaire a ON a.id_aff = appel.affaire_app where id_app = $id ;");
	return $this->process2();
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table appel
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarAppel($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}

/**
 * Classe qui gère les projets dans la base
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class projetModel extends GeneralModel {

    /**
     * Constructeur de la classe qui instancie les variables
     */
    public function __construct() {
	$this->sqlTableName = 'projet';
	$this->sqlIdField = 'id_proj';
	$this->availableFields = array(
		'id_proj','contact_proj', 'titre_proj',
		'detect_proj', 'rdv_proj', 'heure_proj',
		'actif_proj', 'affaire_proj', 'desc_proj',
		'echeance_proj', 'budget_proj', 'decid_proj',
		'rdvavec_proj', 'comm_proj', 'typeproj_proj', 'utilisateur_proj');
	parent::__construct();
    }

    /**
     * Méthode qui recherche un projet
     * @param string $value Ce que l'on cherche
     * @param int $limit Le nombre maximum de résultats souhaités
     * @param int $from L'index du premier résultat
     * @param string $plus Un complément de requête
     * @return array Les résultats
     */
    public function getDataForSearch($value, $limit = '10', $from = '0', $plus='') {
	$search = " LIKE '%".trim($value)."%' ";
	$this->makeRequeteFree("SELECT * FROM $this->sqlTableName left join contact on id_cont = contact_proj where ($this->sqlIdField $search OR titre_proj $search OR affaire_proj $search) $plus Limit $from, $limit ");
	return $this->process2();
    }

    /**
     * Méthode récupère les contacts d'une entreprise
     * @param int $ident L'id de l'entreprise dont on veut les contacts
     * @return array Les contacts de l'entreprise
     */
    public function getContactEntreprise($ident) {
	$this->makeRequeteFree("SELECT * FROM contact WHERE entreprise_cont = '$ident'");
	$result = $this->process2();
	if($result[0]) {
	    foreach($result[1] as $v)
		$out[$v['id_cont']] = $v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'];
	    return $out;
	}
	else return array();
    }

    /**
     * Méthode qui récupère l'historique des visites sur les fiches projets
     * @param int $limit Le nombre maximum de résultat(s) souhaité(s)
     * @param string $type Précise si on veut un COUNT ou toutes les infos
     * @param string $order Précise l'ordre de tri des résultats
     * @return array Les résultats
     */
    public function getDataForHistoriqueVisit($limit ='10', $type = '', $order='ORDER BY id_proj DESC') {
	if(array_key_exists('historiqueVisite',$_SESSION) and
		array_key_exists('projet',$_SESSION['historiqueVisite']) and
		count($_SESSION['historiqueVisite']['projet']) > 0) {
	    $list = '';
	    foreach($_SESSION['historiqueVisite']['projet'] as $id)
		$list .= '\''.$id.'\', ';
	    $list = '('.substr($list,0,-2).')';
	}
	else $list = '(\'fakeNull\')';
	if($type == 'COUNT')
	    $select = 'SELECT COUNT(*) AS counter';
	else $select = 'SELECT *';
	$this->makeRequeteFree("$select FROM projet
			LEFT JOIN contact ON contact.id_cont = projet.contact_proj
			LEFT JOIN entreprise ON entreprise.id_ent = contact.entreprise_cont
			LEFT JOIN ref_typeentreprise te ON te.id_tyent = entreprise.type_ent
			LEFT JOIN ref_typeproj ON ref_typeproj.id_typro = projet.typeproj_proj
			WHERE id_proj IN $list $order LIMIT 0, $limit ");
	return $this->process2();
    }

    /**
     * Méthode qui s'occupe d'insérer une projet dans la base
     * @param array $data Les données à insérer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function insert($data) {
	if(!array_key_exists($this->sqlIdField,$data)) $data[$this->sqlIdField] = ($this->getLastId()+1);
	if(array_key_exists('rdv_proj', $data))
	    $data['rdv_proj'] = substr($data['rdv_proj'], 6,4).substr($data['rdv_proj'], 3,2).substr($data['rdv_proj'],0,2);
	$data['detect_proj'] = date('Y-m-d');
	$sql = $this->makeRequeteInsert($this->sqlTableName,$this->bindVarProjet($data));
	Logg::loggerInfo('projetModel::insert() ~ ajout du projet '.$data['id_proj'].' intitulé : '.$data['titre_proj'],$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui met à jour les projets dans la base
     * @param array $data Les données mises à jour
     * @param int $id L'id du projet à mettre à jour
     * @return array Indique si la requête s'est bien déroulée
     */
    public function update($data, $id) {
	if(array_key_exists($this->sqlIdField,$data)) unset($data['id_proj']);
	if(array_key_exists('rdv_proj', $data))
	    $data['rdv_proj'] = substr($data['rdv_proj'], 6,4).substr($data['rdv_proj'], 3,2).substr($data['rdv_proj'],0,2);
	$sql = $this->makeRequeteUpdate($this->sqlTableName,$this->sqlIdField,$id,$this->bindVarProjet($data));
	Logg::loggerInfo('projetModel::update() ~ mise à jour du projet '.$id,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui supprime un projet de la base
     * @param int $id L'id du projet à supprimer
     * @return array Indique si la requête s'est bien déroulée
     */
    public function delete($id) {
	$sql = $this->makeRequeteFree("DELETE FROM $this->sqlTableName Where $this->sqlIdField = $id");
	Logg::loggerInfo('projetModel::delete() ~ suppression du projet '.$id,$sql,__FILE__.'@'.__LINE__);
	$result = $this->process2();
	return $result;
    }

    /**
     * Méthode qui récupère les informations d'un projet
     * @param int $id L'id du projet dont on veut les informations
     * @return array Les informations du projet
     */
    public function getDataFromID($id) {
	$this->makeRequeteFree("SELECT * from projet LEFT JOIN contact c on c.id_cont = projet.contact_proj LEFT JOIN entreprise e ON e.id_ent = c.entreprise_cont LEFT JOIN affaire a ON a.id_aff = projet.affaire_proj where id_proj = $id ;");
	return $this->process2();
    }

    /**
     * Fonction qui filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la table projet
     * @param <array> $data tableau avec les données brut
     * @return <array> données filtrées selon la propriété $this->availableFields
     */
    public function bindVarProjet($data) {
	return $this->bindVar($data,$this->availableFields);
    }
}

?>
