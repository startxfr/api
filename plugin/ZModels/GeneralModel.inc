<?php


/**
 * Description of GeneralModel
 * Classe abstraite qui implémente des fonctionnalités communes
 * aux autres classes des différentes parties
 *
 * @author Nicolas Mannocci
 * @version 1.1
 */
abstract class GeneralModel extends Bdd_mysql_2 {

    protected $sqlTableName;
    protected $sqlIdField;
    protected $availableFields;

    public function __construct() {
	parent::__construct();
    }

    /**
     * Méthode qui récupère les modes de règlements de la base
     * @return array Les modes de règlements
     */
    public function getModeReglement() {
	$this->makeRequeteFree("SELECT id_modereg, nom_modereg from ref_modereglement");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_modereg']] = $v['nom_modereg'];
	return $countryList;
    }

    /**
     * Méthode qui récupère les modes de règlements de la base
     * @return array Les modes de règlements
     */
    public function getCondReglement() {
	$this->makeRequeteFree("SELECT id_condreg, nom_condreg from ref_condireglement;");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_condreg']] = $v['nom_condreg'];
	return $countryList;
    }

    /**
     * Méthode qui récupère les pays de la base
     * @return array La liste des pays
     */
    public function getPays() {
	$this->makeRequeteFree("SELECT id_pays, nom_pays from ref_pays ");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_pays']] = $v['nom_pays'];
	return $countryList;
    }

    /**
     * Méthode qui récupère les utilisateurs
     * @return array Les utilisateurs
     */
    public function getUser() {
	$this->makeRequeteFree("SELECT login, nom, prenom, civ from user ");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['login']] = $v['civ'].' '.$v['prenom'].' '.$v['nom'];
	return $countryList;
    }

    /**
     * Méthode qui liste les utilisateurs par niveau de droit
     * @param int $droit Le droit demandé
     * @return array Les utilisateurs correspondants
     */
    public function getUsersOfRight($droit) {
	$this->makeRequeteFree("SELECT login, civ, nom, prenom, mail from user where droit = ".$droit." and actif = '1' and isDelete = '0'");
	return $this->process2();
    }

    /**
     * Méthode qui retourne la liste des status d'un devis
     * @return array Les status
     */
    public function getStatutDevis() {
	$this->makeRequeteFree("SELECT * from ref_statusdevis ");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $l[$v['id_stdev']] = $v['nom_stdev'];
	return $l;
    }

    /**
     * Méthode qui retourne la liste des status d'une facture fournisseur
     * @return <type>
     */
    public function getStatutFactFourn() {
	$this->makeRequeteFree("SELECT * from ref_statusfacturefournisseur ");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $l[$v['id_stfactfourn']] = $v['nom_stfactfourn'];
	return $l;
    }

    /**
     * Méthode qui retourne la liste des status d'une facture
     * @return <type>
     */
    public function getStatutFact() {
	$this->makeRequeteFree("SELECT * from ref_statusfacture ");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $l[$v['id_stfact']] = $v['nom_stfact'];
	return $l;
    }

    /**
     * Méthode qui retourne la liste des types de projets
     * @return array Les projets
     */
    public function getTypesProj() {
	$this->makeRequeteFree("SELECT * from ref_typeproj");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_typro']] = $v['nom_typro'];
	return $countryList;
    }

    /**
     * Méthode qui récupère les fonctions de la base
     * @return array Les fonctions
     */
    public function getFonction() {
	$this->makeRequeteFree("SELECT id_fct, nom_fct FROM ref_fonction");
	$temp = $this->process2();
	$temp=$temp[1];
	foreach($temp as $k => $v)
	    $countryList[$v['id_fct']] = $v['nom_fct'];
	return $countryList;
    }

    /**
     * Méthode qui récupère les familles de la base
     * @return array Les familles de produit
     */
    public function getAllFamille() {
	$this->makeRequeteFree("SELECT * from ref_prodfamille order by treePathKey ASC, nom_prodfam ASC");
	$rs = $this->process2();
	if($rs[0]) {
	    $rs=$rs[1];
	    foreach($rs as $k => $v)
		$countryList[$v['id_prodfam']] = $v['treePathKey'].' '.$v['nom_prodfam'];
	    return $countryList;
	}
    }



    /**
     * Méthode qui récupère toutes les informations d'un fournisseur
     * @param string $id L'id du fournisseur
     * @return array Les informations du fournisseur
     */
    public function getAllFournisseurs() {
	$this->makeRequeteFree("SELECT *
		  FROM fournisseur
		  LEFT JOIN entreprise e ON e.id_ent =fournisseur.entreprise_fourn
		  LEFT JOIN contact c ON c.id_cont = fournisseur.contactComm_fourn ");
	$rs = $this->process2();
	if($rs[0]) {
	    $rs=$rs[1];
	    foreach($rs as $k => $v)
		$countryList[$v['id_fourn']] = $v['id_fourn'].' '.$v['nom_ent'];
	    return $countryList;
	}
    }


    /**
     * Méthode qui récupère les périodes de renouvellement de la base
     * @return array Les périodes de renouvellement
     */
    public function getRenews() {
	$this->makeRequeteFree("SELECT * from ref_renewperiode order by id_refrnw ASC ");
	$rs = $this->process2();
	if($rs[0]) {
	    $rs=$rs[1];
	    foreach($rs as $k => $v)
		$countryList[$v['id_refrnw']] = $v['nom_refrnw'];
	    return $countryList;
	}
    }

    /**
     * Méthode interne à Zuno qui permet de crypter une chaine de caractère dans un code déchiffrable
     * @param string $string La chaine à cryper
     * @param bool $unik Précise si on souhaite rajouter un microtime à la chaine pour la rendre unique
     * @return string La chaine cryptée
     */
    public function zEncrypt($string, $unik = false) {
	if($string == '')
	    return '';
	if($unik) {
	    $string .= "#-#".microtime(true);
	}

	$count = strlen($string);
	for($i=0; $i<strlen($string); $i++) {
	    $ascii = ord($string[$i]);
	    $temp = $ascii + $i - ($i*$count);
	    while($temp > 255)
		$temp -= 256;
	    while($temp < 0)
		$temp += 256;
	    $result .= chr($temp);
	}

	return strrev($result);
    }

    /**
     * Méthode qui décrypte une chaine cryptée avec zEncrypt
     * @param string $chaine La chaine à décrypter
     * @return string La chaine décryptée
     */
    public function zDecrypt($chaine) {
	if($chaine == '')
	    return '';

	$count = strlen($chaine);
	$compteur = 0;
	for($i=$count-1 ; $i >= 0; $i--) {
	    $ascii = ord($chaine[$i]);
	    $temp = $ascii - $compteur + ($compteur*$count);
	    while($temp > 255)
		$temp -= 256;
	    while($temp < 0)
		$temp += 256;
	    $result .= chr($temp);
	    $compteur ++;
	}

	return $result;
    }

    /**
     * Méthode pour rechercher le dernier identifiant utilisé
     * @return string Le dernier Id de la base
     */
    public function getLastId() {
	$this->makeRequeteFree("SELECT ".$this->sqlIdField." FROM ".$this->sqlTableName." ORDER BY ".$this->sqlIdField." DESC LIMIT 0, 1");
	$o = $this->process2();
	return ($o[0]) ? $o[1][0][$this->sqlIdField] : '0';
    }

    /**
     * Méthode utile pour transformer une chaine binaire en chaine de caractères
     * @param string $str La chaine binaire
     * @param int $len La taille de la chaine
     * @return String La chaine de caractère
     */
    public function unpack_str($str, $len = null) {
	if($len === null)
	    $len = strlen($str);
	$tmp_arr = unpack("c".$len."chars", $str);
	$out_str = "";
	foreach($tmp_arr as $v) {
	    if($v>0) {
		$out_str .= chr($v);
	    }
	}

	return $out_str;
    }

    /**
     * Méthode qui tranforme une chaine de caractère en chaine binaire
     * @param string $str La chaine de caractère
     * @param int $len La taille de la chaine
     * @return string La chaine binaire
     */
    public function pack_str($str, $len = null) {
	if($len === null)
	    $len = strlen($str);
	$out_str = "";
	for($i=0; $i<$len; $i++) {
	    $out_str .= pack("c", ord(substr($str, $i, 1)));
	}
	return $out_str;
    }

    /**
     * Filtre les clefs d'un tableau et ne retourne que les clefs corespondant à la description de table fournit
     * @param <array> $data les données brut
     * @param <array> $tableDesc la table des clefs autorisées
     * @return <array> le tableau filtré
     */

    public function bindVar($data,$tableDesc) {
	$out = array();
	if(is_array($tableDesc) and is_array($data))
	    foreach($data as $k => $v)
		if(in_array($k,$tableDesc))
		    $out[$k] = $v;
	return $out;
    }

    /**
     * Méthode qui insert une actualité correspondate à une action sur une fiche
     * @param string $id L'id de la fiche
     * @param string $action L'action réalisée [add/update/delete/clone/avoir/WS]
     * @param string $desc Précise une phrase de description optionnelle pour remplacer l'auto générée
     * @param string $precision Permet de rajouter quelque chose à la description
     * @param string $cloneID Permet de préciser l'id de la fiche d'origine du clone ou de l'avoir (le cas échéant)
     * @param string $isVisibleFilActu Permet de préciser si l'acualité doit appraitre dans le fil d'actualité
     * @param string $paramAdd Permet de pousser des parametres aditionnels qui seront envoyés vers la base de donnée
     * @return array Indique si la requête s'est bien déroulée
     */
    public function addActualite($id, $action, $desc = '', $precision = '', $cloneID = '', $isVisibleFilActu = true,$paramAdd = array()) {
	$param = $paramAdd;
	$e = "e";
	$pronom = "Elle";

	if($id != '') {
	    switch($this->sqlTableName) {
		case "affaire" :
		    $rs = $this->getDataFromID($id);
		    $param['type'] = 'affaire';
		    $param['id_ent'] = $rs[1][0]['entreprise_aff'];
		    $param['id_cont'] = $rs[1][0]['contact_aff'];
		    $param['id_aff'] = $rs[1][0]['id_aff'];
		    $param['status_aff'] = $rs[1][0]['status_aff'];
		    $type = "de l'affaire";
		    $type2 = "l'affaire";
		    $status = $rs[1][0]['nom_staff'];
		    if($rs[1][0]['budget_aff'] != '')
			$montant = "de ".prepareNombreAffichage($rs[1][0]['budget_aff'])."€";
		    else $montant = "indéterminé";
		    if($rs[1][0]['archived_aff'])
			$action = "archive";
		    break;
		case "devis" :
		    $rs = $this->getDataFromID($id);
		    $param['type'] = "devis";
		    $param['id_ent'] = $rs[1][0]['entreprise_dev'];
		    $param['id_cont'] = $rs[1][0]['contact_dev'];
		    $param['id_aff'] = $rs[1][0]['affaire_dev'];
		    $param['id_dev'] = $rs[1][0]['id_dev'];
		    $param['status_dev'] = $rs[1][0]['status_dev'];
		    $type = "du devis";
		    $type2 = "le devis";
		    $pronom = "Il";
		    $e = "";
		    $status = $rs[1][0]['nom_stdev'];
		    if($rs[1][0]['status_dev'] == '4')
			$action = 'send';
		    $montant = "de ".prepareNombreAffichage($rs[1][0]['sommeHT_dev'])."€ Hors Taxes";
		    break;
		case "commande" :
		    $rs = $this->getDataFromID($id);
		    $param['type'] = "commande";
		    $param['id_ent'] = $rs[1][0]['entreprise_cmd'];
		    $param['id_cont'] = $rs[1][0]['contact_cmd'];
		    $param['id_aff'] = $rs[1][0]['id_aff'];
		    $param['id_dev'] = $rs[1][0]['id_dev'];
		    $param['status_cmd'] = $rs[1][0]['status_cmd'];
		    $param['id_cmd'] = $rs[1][0]['id_cmd'];
		    $type = "de la commande";
		    $type2 = "la commande";
		    $status = $rs[1][0]['nom_stcmd'];
		    if($rs[1][0]['status_cmd'] == '4')
			$action = 'send';
		    $montant = "de ".prepareNombreAffichage($rs[1][0]['sommeHT_cmd'])."€ Hors taxes";
		    break;
		case "facture" :
		    $rs = $this->getDataFromID($id);
		    $param['type'] = "facture";
		    $param['id_ent'] = $rs[1][0]['entreprise_fact'];
		    $param['id_cont'] = $rs[1][0]['contact_fact'];
		    $param['id_aff'] = $rs[1][0]['affaire_dev'];
		    $param['id_dev'] = $rs[1][0]['id_dev'];
		    $param['id_cmd'] = $rs[1][0]['commande_fact'];
		    $param['status_fact'] = $rs[1][0]['status_fact'];
		    $param['id_fact'] = $rs[1][0]['id_fact'];
		    $montant = "de ".prepareNombreAffichage($rs[1][0]['sommeHT_fact'])."€ Hors Taxes";
		    if($rs[1][0]['type_fact'] == "Avoir") {
			$type = "de l'avoir";
			$type2 = "l'avoir";
			$e = "";
			$pronom = "Il";
		    }
		    else {
			$type = "de la facture";
			$type2 = "la facture";
		    }
		    $status = $rs[1][0]['nom_stfact'];
		    if($rs[1][0]['status_fact'] == '4')
			$action = 'send';
		    if(strlen($id) == 1)
			$id = "00".$id;
		    elseif(strlen($id) == 2)
			$id = "0".$id;
		    $id = substr($rs[1][0]['daterecord_fact'], 2, 2).substr(strtoupper($rs[1][0]['type_fact']),0,1).$id;
		    break;
		case "facture_fournisseur" :
		    $rs = $this->getDataFromID($id);
		    $param['type'] = "facturefournisseur";
		    $param['id_ent'] = $rs[1][0]['entreprise_factfourn'];
		    $param['id_cont'] = $rs[1][0]['contact_factfourn'];
		    $param['status_fact'] = $rs[1][0]['status_factfourn'];
		    $param['id_fact'] = $rs[1][0]['id_factfourn'];
		    $type = "de la facture fournisseur";
		    $type2 = "la facture fournisseur";
		    $status = $rs[1][0]['nom_stfactfourn'];
		    $montant = "de ".prepareNombreAffichage($rs[1][0]['montantTTC_factfourn'])."€ TTC";
		    break;
		default :
		    $param['type'] = 'general';
		    break;
	    }
	    $client = ($rs[1][0]['nom_ent'] != '') ? $rs[1][0]['nom_ent'] : $rs[1][0]['prenom_cont'].' '.$rs[1][0]['nom_cont'];
	}
	switch($action) {
	    case "add" :
		$param['titre'] = "Création ".$type." ".$id." pour le client ".$client;
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être généré".$e." par ".$_SESSION['user']['fullnom']." pour l'entreprise ".$rs[1][0]['nom_ent']." (".$rs[1][0]['cp_ent'].")." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "clone" :
		$param['titre'] = "Création ".$type." ".$id." à partir ".$type." ".$cloneID;
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être généré".$e." par ".$_SESSION['user']['fullnom']." pour l'entreprise ".$rs[1][0]['nom_ent']." (".$rs[1][0]['cp_ent'].")." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "avoir" :
		$param['titre'] = "Création ".$type." ".$id." à partir de la facture ".$cloneID;
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être généré".$e." par ".$_SESSION['user']['fullnom']." pour l'entreprise ".$rs[1][0]['nom_ent']." (".$rs[1][0]['cp_ent'].")." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "update" :
		$param['titre'] ="Modification ".$type." ".$id." pour le client ".$client;
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être modifié".$e." par ".$_SESSION['user']['fullnom'].". ".$pronom." a désormais le status ".$status." avec un montant ".$montant."." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "updateFree" :
		$param['titre'] =($desc == '') ? "Modification ".$type." ".$id." pour le client ".$client : $desc." ".$type." ".$id." pour le client ".$client;
		$param['desc'] = ($precision == '') ? ucfirst($type2)." ".$id." vient d'être modifié".$e." par ".$_SESSION['user']['fullnom'].". ".$pronom." a désormais le status ".$status." avec un montant ".$montant."." : $precision."\n".$type2." ".$id." est d'un montant ".$montant.".";
		break;
	    case "delete" :
		$param['titre'] ="Suppression ".$type." ".$id." pour le client ".$client;
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être supprimé".$e." par ".$_SESSION['user']['fullnom'].". ".$pronom." a désormais le status ".$status."." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "archive" :
		$param['titre'] ="Archivage ".$type." ".$id." pour le client ".$client;
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être archivé".$e." par ".$_SESSION['user']['fullnom']."." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "perdu" :
		$param['titre'] = ucfirst($type2)." ".$id." pour le client ".$client.' est perdu';
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être marqué".$e." comme perdu".$e." par ".$_SESSION['user']['fullnom']."." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "send" :
		$param['titre'] = "Envoi ".$type." ".$id." relative au client ".$client;
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être envoyé".$e." par ".$_SESSION['user']['fullnom']."." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "record" :
		$param['titre'] = 'Enregistrement dans le Disque Dur Virtuel '.$type.' '.$id." relative au client ".$client;
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être enregistré".$e." par ".$_SESSION['user']['fullnom']."." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    case "valid" :
		$param['titre'] = $type.' '.$id.' validée';
		$param['desc'] = ($desc == '') ? ucfirst($type2)." ".$id." vient d'être validé".$e." par ".$_SESSION['user']['fullnom']."." : $desc;
		$param['desc'] .= ($precision != '') ? " Complément d'informations : ".$precision : '';
		break;
	    default :
		$param['titre'] = ($desc == '') ? 'Information générale' : $desc;
		$param['desc'] = ($precision == '') ? '' : $precision;
		break;
	}
	$param['isVisibleFilActu'] = ($isVisibleFilActu !== true) ? 0 : 1;
	$this->addActualiteinDb($param);
	return true;

    }



    /**
     * Méthode qui insert une actualité correspondate à une action sur une fiche
     * @param string $id L'id de la fiche
     * @param string $action L'action réalisée [add/update/delete/clone/avoir]
     * @param string $desc Précise une phrase de description optionnelle pour remplacer l'auto générée
     * @param string $precision Permet de rajouter quelque chose à la description
     * @param string $cloneID Permet de préciser l'id de la fiche d'origine du clone ou de l'avoir (le cas échéant)
     * @return array Indique si la requête s'est bien déroulée
     */
    public function addActualiteinDb($param) {
	if($param['date'] == '')	$param['date'] = date('Y-m-d H:i:s');
	if($param['user'] == '')	$param['user'] = $_SESSION['user']['id'];
	if($param['type'] == '')	$param['type'] = 'general';
	if($param['titre'] == '')	$param['titre'] = 'Information générale';
	$param['isVisibleFilActu']	= ($param['isVisibleFilActu'] != true) ? 0 : 1;
	$param['isPublic']		= ($param['isPublic']) ? 1 : 0;
	$param['isPublieForClient']	= ($param['isPublieForClient']) ? 1 : 0;
	$this->makeRequeteInsert("actualite", $param);
	return $this->process2();
    }
}
?>
