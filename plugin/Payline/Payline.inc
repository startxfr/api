<?php
include_once 'paylineSDK.php';//SDK fournit par PayLine qui gère toute la partie webservice via SOAP

/**
 * Description of Payline
 * Classe qui fait le lien entre le SDK de Payline et Zuno.
 * Elle génère les demandes au bon format pour partir via SOAP
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class Payline {

    private $payline; //Variable qui va stoker le paylineSDK
    private $array; //Tableau que l'on va utiliser pour passer les paramètres au payline
    private $sql; //Variable pour stoker le PaylineModel
    private $retour; //Variable pour stoker les retours des appels au paylineSDK

    private $contractNumber; //Numéro de contrat du commerçant chez Payline
    private $contact; //Id du contact
    private $nom; //Nom du contact
    private $prenom; //Prenom du contact
    private $wallet; //Id du contact chez Payline
    private $carte; //Numéro de carte bancaire du contact
    private $dateCarte; //Date fin de validité de la carte
    private $cvvCarte; //Cryptogramme de la carte
    private $anniv; //Date anniversaire du contact (ddmmyy)
    private $passwordCarte; //Mot de passe de la carte
    private $commentaires; //Commentaires du commerçant
    private $devise; //Devise utilisée (978 Euro)
    private $montant; //Montant en centimes
    private $typeCarte; //Le type de carte utilisée
    private $differe; //Date d'un éventuel différé
    private $mailRetour; //Adresse email de retour
    private $transaction; //Identifiant unique de transaction Payline utile pour demande de remboursement

    private $mode; //Précise comptant CPT ou différé DIF
    private $reference; //Précise la référence de la commande
    private $codeAction; //Code de l'action chez payline
    private $availableCodeAction; //Tableau des codes d'actions existants
    private $clientCodeRetour; //Code de retour retourné à l'utilisateur final


    /**
     * Constructeur de la classe.
     * Il initialise la base et le SDK
     * ainsi que certaines variables "peu" variable.
     */
    public function  __construct() {
        $this->payline = new paylineSDK();
        $this->sql = new PaylineModel();
        $this->contractNumber = $GLOBALS['zunoPayline']['CONTRACT_NUMBER'];
        $this->mailRetour = $GLOBALS['zunoClientCoordonnee']['mail'];

        $this->availableCodeAction = array('100', '101', '110', '111', '120', '121', '130', '131', '201', '204', '421', '422', '202');
        $this->codeAction = 101; //Autorisation + validation
        $this->mode = "CPT";//Comptant
        $this->devise = 978; //Euro par défaut
        $this->typeCarte = "CB"; //CB, VISA et MASTERCARD venant de France
    }

    /**
     * Méthode pour mettre un numéro de VAD autre que celui du fichier de conf.
     * @param string $value Le numéro de VAD
     */
    public function setContractNumber($value) {
        if(strlen($value) <= 50)
            $this->contractNumber = $value;
    }

    /**
     * Setter pour la variable nom de famille du porteur de carte
     * @param string $nom Le nom du porteur de la carte
     */
    public function setNom($nom) {
        $this->nom = $nom;
    }

    /**
     * Setter pour la variable prenom du porteur de carte
     * @param string $prenom Le prénom du porteur de la carte
     */
    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    /**
     * Méthode pour renseigner le contact de la Base client à associer
     * @param int $contact L'id du contact
     * @param bool $auto Permet de faire charger automatiquement depuis la base toutes les infos de ce contacts en relation avec la demande de payement
     */
    public function setContact($contact, $auto = false) {
        $this->contact = $contact;
        if($auto) {
            $rs = $this->sql->getInfosContact($this->contact);
            Logg::loggerInfo('Payline.setContact() ~ Récupération automatique des information du contact '.$this->contact,$rs,__FILE__.'@'.__LINE__);
            $this->nom = $rs['nom_cont'];
            $this->prenom = $rs['prenom_cont'];
            $this->wallet = $this->sql->unpack_str($this->sql->zDecrypt($rs['wallet_cont']));
            $this->cvvCarte = $this->sql->unpack_str($this->sql->zDecrypt($rs['cvvCarte_cont']));
            $this->setDateCarte($rs['dateCarte_cont']);
        }
    }

    /**
     * Méthode pour renseigner le contact de la Base client à associer
     * @param int $contact L'id du contact
     * @param bool $auto Permet de faire charger automatiquement depuis la base toutes les infos de ce contacts en relation avec la demande de payement
     */
    public function generateWalletId() {
        $this->setWallet(strtoupper(substr($this->nom,0,4).substr($this->nom,0,2).substr($this->carte,-6,4)));
    }

    /**
     * Setter pour la variable d'id du wallet Payline
     * @param string $wallet L'ID Wallet du client chez Payline
     */
    public function setWallet($wallet) {
        $this->wallet = $wallet;
    }

    /**
     * Setter pour le numéro de la carte
     * @param string $carte Le numéro de la carte à débiter
     */
    public function setCarte($carte) {
        $this->carte = $carte;
    }

    /**
     * Setter pour la date d'expiration de la carte
     * @param int $dateCarte La date d'expiration au format MMAA
     */
    public function setDateCarte($dateCarte) {
        $this->dateCarte = $dateCarte;
    }

    /**
     * Setter pour le cryptograme de la carte
     * @param int $cvvCarte Le cryptograme de la carte
     */
    public function setCvvCarte($cvvCarte) {
        $this->cvvCarte = $cvvCarte;
    }

    /**
     * Setter pour la date d'anniversaire du porteur de la carte
     * @param string $anniv La date d'anniversaire
     */
    public function setAnniv($anniv) {
        $this->anniv = $anniv;
    }

    /**
     * Setter pour le mot de passe de la carte
     * @param string $passwordCarte Le mot de passe de la carte
     */
    public function setPasswordCarte($passwordCarte) {
        $this->passwordCarte = $passwordCarte;
    }

    /**
     * Setter pour insérer des commentaires
     * @param string $commentaires Les commentaires à insérer
     */
    public function setCommentaires($commentaires) {
        $this->commentaires = $commentaires;
    }

    /**
     * Setter pour changer la devise (défaut Euro)
     * @param int $devise Le code ISO de la devise (978 pour Euro)
     */
    public function setDevise($devise) {
        $this->devise = $devise;
    }

    /**
     * Méthode pour préciser le montant de la transaction
     * @param int $montant Montant (à préciser en centimes)
     */
    public function setMontant($montant) {
        $montant = str_replace(",", ".", $montant);
        $montant = round($montant, 2);
        $montant = $montant * 100;
        if($montant <= 9999999999)
            $this->montant = $montant;
    }

    /**
     * Méthode pour entrer le type de carte utilisé
     * @param string $typeCarte Le type de carte utilisé (CB pour Carte Bleu, Visa et Mastercard en france)
     */
    public function setTypeCarte($typeCarte) {
        if(in_array(strtolower($typeCarte),array('cb','visa','mastercard')))
            $this->typeCarte = strtoupper($typeCarte);
    }

    /**
     * Setter de la date de différé d'un payement
     * @param string $differe La date de différé
     */
    public function setDiffere($differe) {
        $this->differe = $differe;
    }

    /**
     * Setter du code de l'action à effectuer (101 pour un payement immédiat)
     * @param int $code Le code de l'action à effectuer
     */
    public function setCodeAction($code) {
        if(in_array($code, $this->availableCodeAction))
            $this->codeAction = $code;
    }

    /**
     * Setter du mode de payement, comptant ou différé
     * @param string $mode Le mode CPT ou DIF
     */
    public function setMode($mode) {
        if($mode == "CPT" or $mode == "DIF")
            $this->mode = $mode;
    }

    /**
     * Setter de la référence de la commande (se doit d'être unique)
     * @param string $ref La référence
     */
    public function setReference($ref) {
        if(strlen($ref) <= 50 and !is_null($ref))
            $this->reference = $ref;
    }

    /**
     * Setter de l'ID de transaction Payline
     * @param string $transaction L'ID transaction Payline
     */
    public function setTransaction($transaction) {
        $this->transaction = $transaction;
    }

    /**
     * Méthode qui génère un code de retour exploitable par l'utilisateur final
     */
    private function genererCodeRetour() {
        switch($this->retour['result']['code']) {
            case '00000' :
            case '02400' :
            case '02500' :
                $this->clientCodeRetour = array(1,"Opération effectuée et validée",false);
                $interne = false;
                break;
            case '01100' :
            case '01200' :
                $this->clientCodeRetour = "Crédits insuffisants";
                $interne = false;
                break;
            case '01101' :
            case '01201' :
                $this->clientCodeRetour = "Carte expirée";
                $interne = false;
                break;
            case '01103':
            case '01108' :
                $this->clientCodeRetour = "Veuillez contacter votre banque pour autorisation";
                $interne = false;
                break;
            case '01110' :
                $this->clientCodeRetour = "Montant invalide";
                $interne = true;
                break;
            case '01111' :
            case '02305':
                $this->clientCodeRetour = "Numéro de carte invalide";
                $interne = false;
                break;
            case '01117' :
            case '01130':
                $this->clientCodeRetour = "Code PIN invalide";
                $interne = false;
                break;
            case '01119' :
            case '01120' :
                $this->clientCodeRetour = "Transaction non autorisée";
                $interne = false;
                break;
            case '01206' :
                $this->clientCodeRetour = "Nombre maximum de paiements atteint";
                $interne = false;
                break;
            case '01208' :
            case '01209':
                $this->clientCodeRetour = "Carte perdue ou volée";
                $interne = false;
                break;
            case '01915' :
            case '02302':
                $this->clientCodeRetour = "Transaction refusée ou invalide";
                $interne = false;
                break;
            case '02501' :
                $this->clientCodeRetour = array(1, "Enregistrement validé malgré la fin de validité proche",false);
                $interne = false;
                break;
            case '02305' :
                $this->clientCodeRetour = "Numéro invalide";
                $interne = true;
                break;
            default :
                $this->clientCodeRetour = "Une erreur lors du paiement est survenue.";
                $interne = true;
                break;
        }
        if(is_string($this->clientCodeRetour))
            $this->clientCodeRetour = array(0, $this->clientCodeRetour, $interne);
        if($this->clientCodeRetour[0])
            Logg::loggerInfo('Payline.genererCodeRetour() ~ Réponse de payline OK : '.$this->clientCodeRetour[1],$this->retour,__FILE__.'@'.__LINE__);
        else Logg::loggerError('Payline.genererCodeRetour() ~ Réponse de payline ERREUR : '.$this->clientCodeRetour[1],$this->retour,__FILE__.'@'.__LINE__);
    }

    /**
     * Méthode qui initialise la variable array.
     * Nécessaire pour s'assurer que deux appels consécutifs ne se mélangent pas
     */
    private function viderArray() {
        $this->array = array();
    }

    /**
     * Méthode qui génère le tableau à envoyer pour une demande de création d'un wallet.
     */
    private function generateArrayCreateWallet() {
        $this->array['contractNumber'] = $this->contractNumber;

        $this->array['wallet']['walletId'] = $this->wallet;
        $this->array['wallet']['lastName'] = $this->nom;
        $this->array['wallet']['firstName'] = $this->prenom;
        $this->array['wallet']['email'] = $this->mailRetour;
        $this->array['card']['number'] = $this->carte;
        $this->array['card']['type'] = $this->typeCarte;
        if(strlen($this->dateCarte) == 4 and strpos($this->dateCarte, "-") === false)
            $this->array['card']['expirationDate'] = $this->dateCarte;
        else {
            list($year, $month, $day) = explode("-", $this->dateCarte);
            $this->array['card']['expirationDate'] = $month.substr($year,2,2);
        }
        $this->array['card']['cvx'] = $this->cvvCarte;
        $this->array['card']['ownerBirthdayDate'] = $this->anniv;
        $this->array['card']['password'] = $this->passwordCarte;
    }

    /**
     * Méthode qui génère le tableau à envoyer pour une demande de payement
     */
    private function generateArrayImmediatePayementWallet() {
        $this->array['walletId'] = $this->wallet;

        $this->array['payment']['amount'] = $this->montant;
        $this->array['payment']['currency'] = $this->devise;
        $this->array['payment']['mode'] = $this->mode;
        $this->array['payment']['action'] = $this->codeAction;
        $this->array['payment']['contractNumber'] = $this->contractNumber;
        $this->array['payment']['differedActionDate'] = $this->differe;

        $this->array['order']['ref'] = $this->reference;
        $this->array['order']['amount'] = $this->montant;
        $this->array['order']['currency'] = $this->devise;
        $this->array['order']['origin'] = 1;
        $this->array['order']['country'] = "FR";
    }

    /**
     * Méthode qui génère le tableau à envoyer pour une demande de réactivation d'un wallet
     */
    private function generateArrayEnableWallet() {
        $this->array['contractNumber'] = $this->contractNumber;
        $this->array['walletId'] = $this->wallet;
    }

    /**
     * Méthode qui génère le tableau à envoyer pour une demande de payement sans wallet
     */
    private function generateArrayAuthorisation() {
        $this->array['card']['number'] = $this->carte;
        $this->array['card']['type'] = $this->typeCarte;
        if(strlen($this->dateCarte) == 4 and strpos($this->dateCarte, "-") === false)
            $this->array['card']['expirationDate'] = $this->dateCarte;
        else {
            list($year, $month, $day) = explode("-", $this->dateCarte);
            $this->array['card']['expirationDate'] = $month.substr($year,2,2);
        }
        $this->array['card']['cvx'] = $this->cvvCarte;
        $this->array['card']['ownerBirthdayDate'] = $this->anniv;
        $this->array['card']['password'] = $this->passwordCarte;

        $this->array['order']['ref'] = $this->reference;
        $this->array['order']['origin'] = 1;
        $this->array['order']['country'] = "FR";
        $this->array['order']['amount'] = $this->montant;
        $this->array['order']['currency'] = $this->devise;

        $this->array['payment']['amount'] = $this->montant;
        $this->array['payment']['currency'] = $this->devise;
        $this->array['payment']['action'] = "101";
        $this->array['payment']['mode'] =  "CPT";
        $this->array['payment']['contractNumber'] = $this->contractNumber;
    }

    /**
     * Méthode qui génère le tableau à renvoyer pour une demande de remboursement
     */
    private function generateRefundDemand() {
        $rs = $this->sql->getInfosByTransaction($this->transaction);
        if($rs[0]) {
            $this->array['transactionID'] = $this->transaction;
            $this->array['payment']['amount'] = $rs[1][0]['montant_hp'];
            $this->array['payment']['currency'] = $rs[1][0]['devise_hp'];
            $this->array['payment']['action'] = 421;
            $this->array['payment']['mode'] = "CPT";
            $this->array['payment']['contractNumber'] = $this->contractNumber;
        }
    }

    /**
     * Méthode qui sauvegarde le "dialogue" avec Payline
     * @param int $action L'action qui a été effectuée
     */
    private function saveToBDD($action) {

        $data['type_hp'] = $action;
        $data['montant_hp'] = $this->montant;
        $data['devise_hp'] = $this->devise;
        $data['typeCb_hp'] = $this->typeCarte;
        if(!is_null($this->differe))
            $data['differe_hp'] = $this->differe;
        $data['contact_hp'] = $this->contact;
        $data['transaction_hp'] = $this->retour['transaction']['id'];
        $data['codeRetour_hp'] = $this->retour['result']['code'];
        $data['shortRetour_hp'] = $this->retour['result']['shortMessage'];
        $data['longRetour_hp'] = $this->retour['result']['longMessage'];
        $data['fraude'] = $this->retour['transaction']['isPossibleFraud'];
        $data['doublon_hp'] = $this->retour['transaction']['isDuplicated'];
        $data['autorisation_hp'] = $this->retour['authorization']['number'];
        if(!is_null($this->retour['authorization']['date'])) {
            $d = $this->retour['authorization']['date'];
            $d = substr(date('Y'), 0,2).substr($d, 6,2)."-".substr($d, 3,2)."-".substr($d,0,2)." ".substr($d,9,5).":00";
            $data['dateAutorisation_hp'] = $d;
        }

        $this->sql->insert($data);
    }

    /**
     * Méthode appelée pour créer un wallet
     * @return array Indique si la requête s'est bien déroulée avec un message propre
     */
    public function createWallet() {
        $this->viderArray();
        $this->generateArrayCreateWallet();
        Logg::loggerInfo('Payline.createWallet() ~ Creation du wallet pour le client '.$this->contact,$this->array,__FILE__.'@'.__LINE__);
        ob_start();
        $this->retour = $this->payline->create_Wallet($this->array);
        $out1 = ob_get_contents();
        ob_end_clean();
        if(substr($out1,0,13) == '<strong>ERROR') {
            Logg::loggerError('Payline.createWallet() ~ Erreur SOAP ',$out1,__FILE__.'@'.__LINE__);
            // FAUSSE ENTREE FAITE POUR RETOURNER TOUJOURS OK SUR LA TRANSACTION
            // TODO: remove ca pour test en prod
            Logg::loggerNotice('Payline.createWallet() ~ FAUSSE INFORMATION POUR LE TEST : La methode Payline::createWallet() retourne toujours OK sur SXA',$this->array,__FILE__.'@'.__LINE__);
            return array(true,"FAUSSE INFORMATION POUR LE TEST : La methode Payline::createWallet() retourne toujours OK sur SXA.",false);
            //return array(false,"Erreur SOAP : ".$out1,false);
        }
        $this->saveToBDD("14");
        $this->genererCodeRetour();
        return $this->clientCodeRetour;
    }

    /**
     * Méthode appelée pour mettre à jour un wallet
     * @return array Indique si la requête s'est bien déroulée avec un message propre
     */
    public function updateWallet() {
        $this->viderArray();
        $this->generateArrayCreateWallet();
        Logg::loggerInfo('Payline.updateWallet() ~ Mise a jour du wallet pour le client '.$this->contact,$this->array,__FILE__.'@'.__LINE__);
        ob_start();
        $this->retour = $this->payline->update_Wallet($this->array);
        $out1 = ob_get_contents();
        ob_end_clean();
        if(substr($out1,0,13) == '<strong>ERROR') {
            Logg::loggerError('Payline.updateWallet() ~ Erreur SOAP ',$out1,__FILE__.'@'.__LINE__);
            return array(false,"Erreur SOAP : ".$out1,false);
        }
        $this->saveToBDD("15");
        $this->genererCodeRetour();
        return $this->clientCodeRetour;
    }

    /**
     * Méthode appelée pour enregistrer les coordonnées bancaires d'un client
     * dans notre base client
     * @return array Indique si la requête s'est bien déroulée
     */
    public function saveCarteDatas() {
        $tableau['carte'] = $this->carte;
        $tableau['id'] = $this->contact;
        $tableau['wallet_cont'] = $this->wallet;
        $tableau['dateCarte_cont'] = $this->dateCarte;
        $tableau['typeCarte_cont'] = $this->typeCarte;
        $tableau['cvvCarte_cont'] = $this->cvvCarte;
        $tableau['nom_cont'] = $this->nom;
        $tableau['prenom_cont'] = $this->prenom;
        return $this->sql->saveInfosContact($tableau);
    }

    /**
     * Méthode appelée pour réactiver un wallet précédement désactivé
     * @return array Indique si la requête s'est bien déroulée avec un message propre
     */
    public function enableWallet() {
        $this->viderArray();
        $this->generateArrayEnableWallet();
        Logg::loggerInfo('Payline.enableWallet() ~ Activation du wallet pour le client '.$this->contact,$this->array,__FILE__.'@'.__LINE__);
        ob_start();
        $this->retour = $this->payline->enable_Wallet($this->array);
        $out1 = ob_get_contents();
        ob_end_clean();
        if(substr($out1,0,13) == '<strong>ERROR') {
            Logg::loggerError('Payline.enableWallet() ~ Erreur SOAP ',$out1,__FILE__.'@'.__LINE__);
            return array(false,"Erreur SOAP : ".$out1,false);
        }
        $this->saveToBDD("17");
        $this->genererCodeRetour();
        return $this->clientCodeRetour;
    }

    /**
     * Méthode appelée pour désactiver un wallet précédement créé
     * @return array Indique si la requête s'est bien déroulée avec un message propre
     */
    public function disableWallet() {
        $this->viderArray();
        $this->generateArrayEnableWallet();
        $this->payline->setWalletIdList($this->wallet);
        Logg::loggerInfo('Payline.disableWallet() ~ Desactivation du wallet pour le client '.$this->contact,$this->array,__FILE__.'@'.__LINE__);
        ob_start();
        $this->retour = $this->payline->disable_Wallet($this->array);
        $out1 = ob_get_contents();
        ob_end_clean();
        if(substr($out1,0,13) == '<strong>ERROR') {
            Logg::loggerError('Payline.disableWallet() ~ Erreur SOAP ',$out1,__FILE__.'@'.__LINE__);
            return array(false,"Erreur SOAP : ".$out1,false);
        }
        $this->payline->setWalletIdList(0);
        $this->saveToBDD("16");
        $this->genererCodeRetour();
        return $this->clientCodeRetour;
    }

    /**
     * Méthode appelée pour effectuer un payement immédiat depuis un wallet
     * @return array Indique si la requête s'est bien déroulée avec un message propre
     */
    public function doWalletPayement() {
        $this->viderArray();
        $this->generateArrayImmediatePayementWallet();
        Logg::loggerInfo('Payline.doWalletPayement() ~ Lancement d\'un paiement avec wallet',$this->array,__FILE__.'@'.__LINE__);
        ob_start();
        $this->retour = $this->payline->do_immediate_wallet_payment($this->array);
        $out1 = ob_get_contents();
        ob_end_clean();
        if(substr($out1,0,13) == '<strong>ERROR') {
            Logg::loggerError('Payline.doWalletPayement() ~ Erreur SOAP ',$out1,__FILE__.'@'.__LINE__);
            return array(false,"Erreur SOAP : ".$out1,false);
        }
        switch($this->codeAction) {
            case "100" : $k = 1;
                break;
            case "101" : $k = 2;
                break;
            case "110" : $k = 3;
                break;
            case "111" : $k = 4;
                break;
            case "120" : $k = 5;
                break;
            case "121" : $k = 6;
                break;
            case "130" : $k = 7;
                break;
            case "131" : $k = 8;
                break;
            case "201" : $k = 9;
                break;
            case "204" : $k = 10;
                break;
            case "421" : $k = 11;
                break;
            case "422" : $k = 12;
                break;
            case "202" : $k = 13;
                break;
        }
        $this->saveToBDD($k);
        $this->genererCodeRetour();
        return $this->clientCodeRetour;
    }

    /**
     * Méthode appelée pour demander un remboursement d'une transaction précédement validée
     * @return array Indique si la requête s'est bien déroulée avec un message propre
     */
    public function doRefund() {
        $this->viderArray();
        $this->generateRefundDemand();
        Logg::loggerInfo('Payline.doRefund() ~ Anulation d\'un paiement ',$this->array,__FILE__.'@'.__LINE__);
        ob_start();
        $this->retour = $this->payline->do_refund($this->array);
        $out1 = ob_get_contents();
        ob_end_clean();
        if(substr($out1,0,13) == '<strong>ERROR') {
            Logg::loggerError('Payline.doRefund() ~ Erreur SOAP ',$out1,__FILE__.'@'.__LINE__);
            return array(false,"Erreur SOAP : ".$out1,false);
        }
        $this->saveToBDD("11");
        $this->genererCodeRetour();
        return $this->clientCodeRetour;
    }

    /**
     * Méthode appelée pour demander un payement sans passer par un wallet
     * @return array Indique si la requête s'est bien déroulée avec un message propre
     */
    public function doPayement() {
        $this->viderArray();
        $this->generateArrayAuthorisation();
        Logg::loggerInfo('Payline.doPayement() ~ Lancement d\'un paiement direct',$this->array,__FILE__.'@'.__LINE__);
        ob_start();
        $this->retour = $this->payline->do_authorization($this->array);
        $out1 = ob_get_contents();
        ob_end_clean();
        if(substr($out1,0,13) == '<strong>ERROR') {
            Logg::loggerError('Payline.doPayement() ~ Erreur SOAP ',$out1,__FILE__.'@'.__LINE__);
            return array(false,"Erreur SOAP : ".$out1,false);
        }
        $this->saveToBDD("2");
        $this->genererCodeRetour();
        return $this->clientCodeRetour;
    }
}
?>
