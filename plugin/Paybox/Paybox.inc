<?php

//require_once '../ZModels/PayboxModel.inc';
require_once 'MyCurl.inc';

/**
 * Description of Paybox
 * Classe qui permet d'effectuer un dialogue avec les services de PayBox
 * via curl
 *
 * @author Nicolas Mannocci
 * @version 0.5
 */
class Paybox {

    private $version; //Version de PayBox utilisée, par défaut 00104
    private $dateq; //Date heure de l'envoi de la trame format jjMMAAAAhhmmss
    private $type; // Type de la transaction demandée
    private $numQuestion; //Numéro interne identifiant la question, unique par jour. Maxi 2 147 483 647
    private $site; //Numéro d'adhérent fourni par la banque du commerçant
    private $rang; // Numéro de rang fourni par la banque du commerçant
    private $cle; //Obligatoire, 8 caractères mais d'infos supplémentaires
    private $identifiant; // Champ vide à ne pas utiliser (avec la version 00104)
    private $montant; //Montant EN CENTIMES de la transaction (aucun autre caractère que les 10 chiffres arabes)
    private $devise; // Code de la devise utilisée (978 pour l'euro)
    private $reference; //Référence du commerçant pour identifier la commande liée à la transaction
    private $refAbonne; //Référence du commerçant pour identifier l'abonné
    private $porteur; //Numéro de la carte banquaire OU numéro PORTEUR rendu par Paybox après création d'un abonné
    private $dateval; //Date de fin de validité de la carte
    private $cvv; //Cryptogramme visuel au dos de la carte
    private $activite; //Permet à la banque d'identifier le type de transaction. (024 / internet, 027 / récurrent)
    private $archivage; //Valeur transmise à la banque, UNIQUE, permet de retrouver le payement en cas de litige
    private $differe; //Nombre de jours d'attente avant mise à disposition de la transaction pour envoi à la banque
    private $numAppel; //Numéro généré par Paybox à renseigner pour capturer ou annuler une demande précédente
    private $numTrans; //Numéro généré par Paybox lors de la gestion d'un payement transmissible à la banque. Nécessaire pour demander annulation ou capturation
    private $autorisation; //Numéro d'autorisation fourni par le commerçant suite à un appel à sa banque
    private $pays; //Indique que le pays doit être retourné à la norme ISO3166
    private $priv_codeTraitement; //Valeur transmise par le commerçant pour préciser l'option de payement de cartes SOFINCO ou COFINOGA
    private $datenaiss; //Date de naissance du proteur de carte COFINOGA (jjMMAAAA)
    private $acquereur; //Valeur à indiquer pour annulation et capture de payement via paypal
    private $typeCarte; //Indique que le type de carte doit être retourné.
    private $sha1; //Indique que le SHA-1 de la carte doit être retourné

    private $codeReponse; //Code de réponse de la requête
    private $commentaire; //Commentaires retournés par Paybox
    private $status; //Etat de la transaction, si demandée via requête type 17
    private $remise; //Identifiant PAYBOX de la remise télécollectée

    private $url; //L'URL à qui envoyer la requête
    private $urlSecours; //L'URL de secours à utiliser si la première est indisponible

    private $availableTypes; //Les types de requêtes disponibles
    private $availableActivites; //Les types d'activités disponibles
    private $sql; //Connexions à la base
    private $curl; // Le module Curl qui effectue l'envoi de la requête à Paybox
    private $tableau; //Le tableau qui sera envoyé par Curl en POST
    private $retour; //Le tableau retour retourné par PayBox
    private $contact; //L'id du contact dans la base de données


    /**
     * Constructeur, il initialise les variables principales qui ne devraient pas
     * être modifiée en utilisation normale de la classe
     */
    public function __construct() {

	$this->version = "00104"; //Version actuelle de Paybox
	$this->site = "1999888"; //valeur pour TEST
	$this->rang = "99"; //Valeur TEST
	$this->cle = "1999888I"; //Valeur TEST
	$this->devise = "978"; //Valeur EURO
	$this->activite = "024"; //Valeur payement Internet
	$this->pays = ""; //Pour récupérer le pays
	$this->typeCarte = ""; //Pour récupérer le type de carte
	$this->sha1 = ""; //Pour récupérer l'emprunte sha1

	$this->url = "https://ppps.paybox.com/PPPS.php"; //URL du serveur Paybox
	$this->urlSecours = "https://ppps1.paybox.com/PPPS.php"; //URL de secours

	$this->sql = new PayboxModel();
	$this->availableTypes = $this->sql->getTypes();
	$this->availableActivites = $this->sql->getTypesAct();

	$this->curl = new MyCurl();

    }

    /**
     * Méthode qui initialise le type de demande
     * @param string $type Le type demandé
     */
    public function setType($type) {
	if(array_key_exists($type, $this->availableTypes))
	    $this->type = $type;
    }

    /**
     * Méthode qui initialise le numéro de la question
     * @param string $numQuestion Le numéro de la question
     */
    public function setNumQuestion($numQuestion) {
	if(strlen($numQuestion) == 10 and is_numeric($numQuestion))
	    $this->numQuestion = $numQuestion;
    }

    /**
     * Méthode qui initialise le montant
     * @param string $montant Le montant (en euro)
     */
    public function setMontant($montant) {
	$montant = str_replace(",", ".", $montant);
	$montant = round($montant, 2);
	$montant = $montant * 100;
	if($montant <= 9999999999)
	    $this->montant = $montant;
    }

    /**
     * Méthode qui initialise la référence
     * @param string $reference La référence
     */
    public function setReference($reference) {
	if(strlen($reference) <= 250)
	    $this->reference = $reference;
    }

    /**
     * Méthode qui initialise la référence de l'abonné
     * @param string $refAbonne La référence de l'abonné
     */
    public function setRefAbonne($refAbonne) {
	if(strlen($refAbonne) <= 250)
	    $this->refAbonne = $refAbonne;
    }

    /**
     * Méthode qui initialise le porteur (numéro de carte banquaire ou numéro rendu par Paybox)
     * @param string $porteur Le numéro de carte banquaire ou le numéro fourni par Paybox
     */
    public function setPorteur($porteur) {
	if(strlen($porteur) <= 19)
	    $this->porteur = $porteur;
    }

    /**
     * Méthode qui initialise la date de fin de validité de la carte
     * @param string $dateval La date de fin de validité de la carte
     */
    public function setDateval($dateval) {
	if(strlen($dateval) == 4 and is_numeric($dateval))
	    $this->dateval = $dateval;
    }

    /**
     * Méthode qui initialise le cryptogramme
     * @param string $cvv Le cryptogramme
     */
    public function setCvv($cvv) {
	if(strlen($cvv) == 3 or strlen($cvv) == 4)
	    $this->cvv = $cvv;
    }

    /**
     * Méthode qui initialise l'e montant'activité à déclarer
     * @param string $activite Type d'activité transmit à la banque
     */
    public function setActivite($activite) {
	if(array_key_exists($activite, $this->availableActivites))
	    $this->activite = $activite;
    }

    /**
     * Méthode qui initialise l'identifiant d'archivage
     * @param string $archivage L'identifiant unique d'archivage
     */
    public function setArchivage($archivage) {
	if(strlen($archivage) <= 12)
	    $this->archivage = $archivage;
    }

    /**
     * Méthode qui initialise la durée du différé
     * @param string $differe La durée en jours du différé
     */
    public function setDiffere($differe) {
	if(strlen($differe) <= 3 and is_numeric($differe))
	    $this->differe = $differe;
    }

    /**
     * Méthode qui initialise le numéro d'appel généré par Paybox
     * @param string $numAppel Le numéro d'appel
     */
    public function setNumAppel($numAppel) {
	if(strlen($numAppel) <= 10 and is_numeric($numAppel))
	    $this->numAppel = $numAppel;
    }

    /**
     * Méthode qui initialise le numéro de transaction généré par Paybox
     * @param string $numTrans Le numéro de transaction
     */
    public function setNumTrans($numTrans) {
	if(strlen($numTrans) <= 10 and is_numeric($numTrans))
	    $this->numTrans = $numTrans;
    }

    /**
     * Méthode qui initialise le numéro d'autorisation
     * @param string $autorisation Le numéro d'autorisation de la banque
     */
    public function setAutorisation($autorisation) {
	if(strlen($autorisation) <= 10)
	    $this->autorisation = $autorisation;
    }

    /**
     * Méthode qui initialise l'option de payement pour cartes SOFINCO COFINOGA
     * @param string $priv_CodeTraitement L'option de payement
     */
    public function setPriv_codeTraitement($priv_CodeTraitement) {
	if(strlen($priv_CodeTraitement) == 3 and is_numeric($priv_CodeTraitement))
	    $this->priv_codeTraitement = $priv_CodeTraitement;
    }

    /**
     * Méthode qui initialise la date de naissance du porteur d'une carte COFINOGA
     * @param string $dateNaiss La date de naissance (JJMMAAAA)
     */
    public function setDateNaiss($dateNaiss) {
	if(strlen($dateNaiss) == 8 and is_numeric($dateNaiss))
	    $this->datenaiss = $dateNaiss;
    }

    /**
     * Méthode qui initialise l'identifiant de l'acquéreur PAYPAL
     * @param string $acquereur L'identifiant de l'acquéreur
     */
    public function setAcquereur($acquereur) {
	if(strlen($acquereur) <= 16)
	    $this->acquereur = $acquereur;
    }

    /**
     * Méthode qui initialise l'id du contact concerné
     * @param int $contact L'id du contact
     * @param bool $auto Permet de faire charger les informations automatiquement depuis la base
     */
    public function setContact($contact, $auto = false) {
	if(is_numeric($contact)) {
	    $this->contact = $contact;
	    if($auto)
		$this->loadDatasFromContact();
	}
    }

    /**
     * Méthode qui génère le tableau POST à envoyer à partir des informations précédement récoltées
     * @return array Le tableau contenant les informations à envoyer
     */
    private function initioPostValues() {
	if(is_null($this->version))
	    return "version";
	else
	    $this->tableau['VERSION'] = $this->version;

	$this->dateq = date('d').date('m').date('Y').date('H').date('i').date('s');
	$this->tableau['DATEQ'] = $this->dateq;

	if(is_null($this->type))
	    return "type";
	else
	    $this->tableau['TYPE'] = $this->type;

	if(is_null($this->numQuestion))
	    $this->numQuestion = $this->sql->getNewNumQuestion();
	$this->tableau['NUMQUESTION'] = $this->numQuestion;

	$this->tableau['SITE'] = $this->site;

	$this->tableau['RANG'] = $this->rang;

	$this->tableau['CLE'] = $this->cle;

	if(is_null($this->montant))
	    return "montant";
	else
	    $this->tableau['MONTANT'] = $this->montant;

	$this->tableau['DEVISE'] = $this->devise;

	if(is_null($this->reference))
	    return "reference";
	else
	    $this->tableau['REFERENCE'] = $this->reference;

	if(is_null($this->refAbonne) and $this->type > 50)
	    return "refAbonne";
	elseif(!is_null($this->refAbonne))
	    $this->tableau['REFABONNE'] = $this->refAbonne;

	if($this->type != 2 and $this->type != 5 and $this->type != 11 and $this->type != 13 and $this->type != 14 and $this->type != 17 and $this->type != 52 and $this->type != 55 and $this->type != 58 and $this->type != 61 and is_null($this->porteur))
	    return "porteur";
	elseif(!is_null($this->porteur))
	    $this->tableau['PORTEUR'] = $this->porteur;

	if(is_null($this->dateval) and $this->type != 2 and $this->type != 11 and $this->type != 13 and $this->type != 14 and $this->type != 17 and $this->type != 52 and $this->type != 58 and $this->type != 61)
	    return "dateval";
	elseif(!is_null($this->dateval))
	    $this->tableau['DATEVAL'] = $this->dateval;

	if(!is_null($this->cvv))
	    $this->tableau['CVV'] = $this->cvv;

	if(!is_null($this->activite))
	    $this->tableau['ACTIVITE'] = $this->activite;

	if(!is_null($this->archivage))
	    $this->tableau['ARCHIVAGE'] = $this->archivage;

	if(!is_null($this->differe))
	    $this->tableau['DIFFERE'] = $this->differe;

	if(is_null($this->numAppel) and ($this->type == 2 or $this->type == 5 or $this->type == 13 or $this->type == 52 or $this->type == 55))
	    return "numAppel";
	elseif(!is_null($this->numAppel))
	    $this->tableau['NUMAPPEL'] = $this->numAppel;

	if(is_null($this->numTrans) and ($this->type ==2 or $this->type == 5 or $this->type == 13 or $this->type == 17 or $this->type == 52 or $this->type == 55) )
	    return "numTrans";
	elseif(!is_null($this->numTrans))
	    $this->tableau['NUMTRANS'] = $this->numTrans;

	if(!is_null($this->autorisation))
	    $this->tableau['AUTORISATION'] = $this->autorisation;
	else
	    $this->tableau['AUTORISATION'] = "";

	$this->tableau['PAYS'] = $this->pays;

	if(!is_null($this->priv_codeTraitement))
	    $this->tableau['PRIV_CODETRAITEMENT'] = $this->priv_codeTraitement;

	if(!is_null($this->datenaiss))
	    $this->tableau['DATENAISS'] = $this->datenaiss;

	$this->tableau['TYPECARTE'] = $this->typeCarte;

	$this->tableau['SHA-1'] = $this->sha1;

	return true;

    }

    /**
     * Méthode appelée pour envoyer la requête à PayBox
     */
    public function envoyer() {
	$rs = $this->initioPostValues();
	if(!is_bool($rs)) {
	    $this->retour['CODEREPONSE'] = '99999';
	    $this->retour['COMMENTAIRE'] = $rs;
	}
	$this->curl->setUrl($this->url);
	$this->curl->setPost($this->tableau);
	$rs = $this->curl->send();
	$rs = explode("&", $rs);
	foreach($rs as $v) {
	    $tmp = explode("=", $v);
	    $this->retour[$tmp[0]] = $tmp[1];
	}
	$this->historise();
	print_r($this->retour);
	exit;
    }

    private function loadDatasFromContact() {
	$rs = $this->sql->getCoordonnesBancaires($this->contact);
	if(array_key_exists('PORTEUR', $rs)) {
	    $this->porteur = $rs['PORTEUR'];
	    $this->dateval = $rs['DATEVAL'];
	    $this->cvv = $rs['CVV'];
	    $this->refAbonne = $rs['REFABONNE'];
	}
    }

    private function historise() {
	if($this->type == "00056")
	    $this->addBanqueDatas();
	elseif($this->type == "00057")
	    $this->modifBanqueDatas();
	//$in['date_pb'] = date('Y-M-d h:m:s');
	$in['type_pb'] = $this->type;
	$in['numQuestion_pb'] = $this->numQuestion;
	$in['devise_pb'] = $this->devise;
	$in['reference_pb'] = $this->reference;
	$in['abonne_pb'] = $this->retour['REFABONNE'];
	$in['activite_pb'] = $this->activite;
	$in['archivage_pb'] = $this->archivage;
	$in['differe_pb'] = $this->differe;
	$in['numAppel_pb'] = $this->retour['NUMAPPEL'];
	$in['numTrans_pb'] = $this->retour['NUMTRANS'];
	$in['carte_pb'] = $this->retour['TYPECARTE'];
	$in['codeReponse_pb'] = $this->retour['CODEREPONSE'];
	$in['porteur_pb'] = $this->retour['PORTEUR'];
	$in['autorisation_pb'] = $this->retour['AUTORISATION'];
	$in['status_pb'] = $this->retour['STATUS'];
	$in['remise_pb'] = $this->retour['REMISE'];
	$in['commentaire_pb'] = $this->retour['COMMENTAIRE'];
	$in['montant_pb'] = $this->montant;
	$this->sql->insert($in);
    }

    private function addBanqueDatas() {
	if(is_numeric($this->contact)) {
	    $banque['REFABONNE'] = $this->retour['REFABONNE'];
	    $banque['PORTEUR'] = $this->retour['PORTEUR'];
	    $banque['DATEVAL'] = $this->dateval;
	    $banque['CVV'] = $this->cvv;
	    $this->sql->insertCoordonnesBancaires($this->contact, $banque);
	}
    }

    private function modifBanqueDatas() {
	if(is_numeric($this->contact)) {
	    $banque['REFABONNE'] = $this->refAbonne;
	    $banque['PORTEUR'] = $this->retour['PORTEUR'];
	    $banque['DATEVAL'] = $this->dateval;
	    $banque['CVV'] = $this->cvv;
	    $this->sql->updateCoordonnesBancaires($this->contact, $banque);
	}
    }

}
?>
