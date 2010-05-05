<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of Payement
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class Payement {
    private $facture; //ID de la facture
    private $contact; //ID du contact
    private $dataContact; //Données relatives à ce contact
    private $nom; //Nom du propriétaire de la carte
    private $prenom; //Prenom du propriétaire de la carte
    private $carte; //Numéro de la carte
    private $date; //Date de fin de validité de la carte
    private $typeCarte; //Type de carte
    private $cvv; //Cryptogramme de la carte
    private $montant; //Montant du payement
    private $articles; //Articles à facturer
    private $facturePDF; //Fichier pdf en base 64 de la facture
    private $reference; //Référence payline de la facture
    private $pm; //PaylineModel
    private $dataFact; //Datas de la facture

    /**
     * Constructeur de la classe qui initialise ce qui peut l'être
     */
    public function  __construct() {
        $this->facturePDF = null;
        $this->sommeFact = 0;
        $this->reference = "Payement".time();
        $this->pm = new PaylineModel();
    }

    /**
     * Setter du prénom du payeur
     * @param string $prenom
     */
    public function setPrenom($prenom) {
        if($prenom != '')
            $this->prenom = ucfirst($prenom);
    }

    /**
     * Setter de l'id de la facture
     * @param int $facture
     */
    public function setFacture($facture) {
        $this->facture = $facture;
    }


    /**
     * Setter de l'id du type de carte
     * @param int $typeCarte
     */
    public function setTypeCarte($typeCarte) {
        $this->typeCarte = $typeCarte;
    }

    /**
     * Setter de l'id du contact
     * @param int $contact
     */
    public function setContact($contact) {
        $rs = $this->pm->getInfosContact($contact);
        if(is_array($rs) and array_key_exists('id_cont', $rs)) {
            $this->contact = $contact;
            $this->dataContact = $rs;
        }
    }

    /**
     * Setter du nom du payeur
     * @param string $nom
     */
    public function setNom($nom) {
        if($nom != '')
            $this->nom = ucfirst($nom);
    }

    /**
     * Setter du numéro de carte bancaire
     * @param int $carte
     */
    public function setCarte($carte) {
        $this->carte = $carte;
    }

    /**
     * Setter de la date d'expiration de la carte
     * @param int $date
     */
    public function setDate($date) {
        $this->date = $date;
    }

    /**
     * Setter du cryptograme de la carte
     * @param int $cvv
     */
    public function setCvv($cvv) {
        $this->cvv = $cvv;
    }

    /**
     * Setter du montant en € du payement
     * @param float $montant
     */
    public function setMontant($montant) {
        $this->montant = $montant;
    }

    /**
     * Setter des articles à inscrire dans la facture
     * @param string $articles Le tableau sérialisé des articles
     */
    public function setArticles($articles) {
        $this->articles = $articles;
    }

    /**
     * Méthode qui encode une facture en base64 dans l'objectif d'être envoyé via web service
     */
    private function encodeFacture() {
        if(is_file($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].'Facture.'.$this->facture.'.pdf')) {
            $fhandle = fopen ($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].'Facture.'.$this->facture.'.pdf', 'rb');
            $fcontent = fread ($fhandle, filesize($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoFacture']['dir.facture'].'Facture.'.$this->facture.'.pdf'));
            fclose($fhandle);
            $this->facturePDF = base64_encode($fcontent);
        }
    }

    /**
     * Méthode qui récupère la facture d'un client pour une date correspondate
     * @param int $date la date de la facture
     * @return array La facture en base 64
     */
    public function getFacture($date) {
        if($date == 'last' or $date == 0)
            $date = 1;
        if($this->contact == '' or $date == '')
            return array(false, 'Paramètres manquants');
        $sql = new factureModel();
        $liste = $sql->getIdFromContactDate($this->contact, $date);
        if(!$liste[0])
            return array(false, 'Erreur SQL');
        $this->facture = $liste[1][0]['id_fact'];

        $this->encodeFacture();
        if(is_null($this->facturePDF))
            return array(false, 'Aucune facture générée');
        else return array(true, $this->facturePDF);
    }

    /**
     * Méthode qui récupère une facture
     * @param int $id L'id de la facture
     * @return array La facture en base 64
     */
    public function getFactureByID($id) {
        $this->facture = $id;
        $this->encodeFacture();
        if(is_null($this->facturePDF))
            return array(false, 'Aucune facture générée', true);
        else return array(true, $this->facturePDF);
    }

    /**
     * Méthode qui récupère la liste des factures d'un client
     * @return array La liste sérialisée
     */
    public function getListeFacture() {
        if($this->contact == '')
            return array(false, 'Paramètre id manquant');
        $sql = new factureModel();
        $liste = $sql->getListeFactureFromContact($this->contact);
        if(!$liste[0])
            return array(false, 'Erreur SQL', true);
        $liste = serialize($liste[1]);
        return array(true, $liste);
    }

    /**
     * Méthode qui sauvegarde la transaction dans notre BDD
     */
    private function saveTransaction() {
        $data['payline_trans'] = $this->pm->getLastId();
        $data['contact_trans'] = $this->contact;
        $data['nom_trans'] = ($this->dataContact != null) ? $this->dataContact['nom_cont'] : $this->nom;
        $data['prenom_trans'] = ($this->dataContact != null) ? $this->dataContact['prenom_cont'] : $this->prenom;
        $data['facture_trans'] = ($this->facture != '') ? $this->facture : 0;
        $data['devise_trans'] = 978;
        $data['montant_trans'] = $this->montant;
        $trans = new TransactionModel();
        $trans->insert($data);
    }

    /**
     * Méthode qui effectue un payement
     * @param bool $save Précie si on sauvegarde les données ou pas
     * @return array Le retour du payement
     */
    public function doPayement() {
        Logg::loggerInfo('Payement.doPayement() ~ Execution du paiement pour le contact  '.$this->contact,serialize($this),__FILE__.'@'.__LINE__);
        if($this->nom == '' and $this->contact != '') {
            if($this->dataContact['wallet_cont'] != '') {
                $pl = new Payline();
                $pl->setContact($this->contact, true);
                $pl->setMontant($this->montant);
                $pl->setReference($this->reference);
                $out = $pl->doWalletPayement();
            }
            else {
                Logg::loggerError('Payement.doPayement() ~ Erreur: Aucune donnée bancaire pour ce contact ',serialize($this),__FILE__.'@'.__LINE__);
                return array(false, 'Aucune donnée bancaire pour ce contact');
            }
        }
        elseif($this->nom != '' and $this->prenom != '' and $this->carte != '' and $this->cvv != '' and $this->date != '') {
            $pl = new Payline();
            $data = $this->pm->getInfosContact($this->contact);
            if($data['id_cont'] == '') {
                Logg::loggerError('Payement.doPayement() ~ Erreur: Contact inconnu ',serialize(array($data,$this->contact)),__FILE__.'@'.__LINE__);
                return array(false, 'Contact inconnu');
            }
            else {
                $pl->setContact($this->contact, true);
                $pl->setMontant($this->montant);
                $pl->setReference($this->reference);
                $out = $pl->doWalletPayement();
            }
        }
        else {
            Logg::loggerError('Payement.doPayement() ~ Erreur: Données manquantes ',serialize(array($this,$this->contact)),__FILE__.'@'.__LINE__);
            return array(false, 'Données manquantes');
        }

        if($out[0]) {
            $this->saveTransaction();
            $this->sendConfirmMail(true);
        }
        else $this->sendConfirmMail(false);
        return $out;
    }

    /**
     * Méthode qui envoi un mail de confirmation du paiement
     * @param bool $done Précise si le paiement a été effectué ou pas
     */
    private function sendConfirmMail($done = false) {
        Logg::loggerInfo('Payement.sendConfirmMail() ~ Envoi du mail de confirmation de règlement pour la facture '.$this->facture,serialize($this),__FILE__.'@'.__LINE__);
        loadPlugin('Send/Send');
        if($this->facture != "")
            $array['id'] = $this->facture;
        else
            $array['id'] = $this->reference;
        $array['partie'] = "send";
        $array['typeE'] = "mail";
        $array['typeEmail'] = "html";
        if($done) {
            $array['message'] = "La tache CRON de paiement a effectué une demande paiement d'un montant de ".prepareNombreAffichage($this->montant)." €.<br />".
                    "Votre client ".$prenom." ".$nom." a bien été prélevé.<br />".
                    "Ce payement est lié à la facture ".$this->facture." et a été effectué ce jour, le ".strftime('%A %d %B %Y')."<br />";
            $array['sujet'] = "Débit client sur Carte Bancaire";
        }
        else {
            $array['message'] = "Vous avez effectué une demande paiement d'un montant de ".prepareNombreAffichage($this->montant)." €.<br />".
                    "Votre client ".$prenom." ".$nom." n'a cependant pas été prélevé.<br />".
                    "La facture ".$this->facture." est à ce jour, ".strftime('%A %d %B %Y')." impayée<br />";
            $array['sujet'] = "Échec débit client sur Carte Bancaire";
        }

        $array['expediteur'] = 'ZUNO';
        $array['bug'] = true;
        $array['mail'] = 'nm@startx.fr';
        //$array['mail'] = $GLOBALS['zunoClientCoordonnee']['mail'];

        $sender = new Sender($array);
        $sender->send();
    }

    /**
     * Méthode qui récupère et sérialise toutes les infos d'une facture
     * @return string Les données de la facture sérialisée
     */
    private function getFactureDatas() {
        Logg::loggerInfo('Payement.getFactureDatas() ~ Recuperation des informations sur la facture '.$this->facture,serialize($this),__FILE__.'@'.__LINE__);
        $sql = new factureModel();
        $fact = $sql->getDataFromID($this->facture);
        if($fact[0]) {
            $fact = $fact[1][0];
            $sql = new factureModel();
            $prod = $sql->getProduitsFromID($this->facture);
            if($prod[0]) {
                $fact['produits'] = $prod[1];
                $this->dataFact = serialize($fact);
            }
            else $this->dataFact = $prod;
        }
        else $this->dataFact = $fact;
        return $this->dataFact;
    }

    /**
     * Méthode qui met à jour les informations de payement d'un client
     * @return array Indique si tout s'est bien déroulé
     */
    public function updateDatas() {
        Logg::loggerInfo('Payement.updateDatas() ~ Mise a jour des informations de paiement pour le contact '.$this->contact,serialize($this),__FILE__.'@'.__LINE__);
        $pl = new Payline();
        $pl->setNom($this->nom);
        $pl->setPrenom($this->prenom);
        $pl->setCarte($this->carte);
        $pl->setDateCarte($this->date);
        $pl->setTypeCarte($this->typeCarte);
        $pl->setCvvCarte($this->cvv);
        $pl->setContact($this->contact, false);
        $rs = $pl->updateWallet();
        if($rs[0]) {
            $rs = $pl->saveCarteDatas();
            if($rs[0])
                $rs[2] = false;
            else $rs[2] = true;
        }
        else $rs[2] = false;
        $rs[0] = (bool) $rs[0];
        return $rs;
    }

    /**
     * Méthode qui créée une facture
     * @return array L'ID de la facture créée
     */
    private function creerFacture() {
        Logg::loggerInfo('Payement.creerFacture() ~ Creation de la facture pour le contact '.$this->contact,serialize($this),__FILE__.'@'.__LINE__);
        $liste = unserialize($this->articles);
        if(!is_array($liste)) {
            return array(false, 'La facture ne peut être vide');
        }
        $sql = new factureModel();
        $data['id_fact'] = ($sql->getLastId() +1);
        $data['titre_fact'] = "Abonnement ZUNO";
        $data['commercial_fact'] = "nm";
        $data['tauxTVA_fact'] = '19.6';
        $data['modereglement_fact'] = '5';
        $data['condireglement_fact'] = '3';
        $data['contact_fact'] = $this->contact;
        $data['contact_achat_fact'] = $this->contact;
        $data['status_fact'] = '1';
        $data['entreprise_fact'] = $this->dataContact['entreprise_cont'];
        $data['nomentreprise_fact'] = $this->dataContact['nom_ent'];
        $data['add1_fact'] = ($this->dataContact['add1_ent'] != '') ? $this->dataContact['add1_ent'] : $this->dataContact['add1_cont'];
        $data['add2_fact'] = ($this->dataContact['add2_ent'] != '') ? $this->dataContact['add2_ent'] : $this->dataContact['add2_cont'];
        $data['cp_fact'] = ($this->dataContact['cp_ent'] != '') ? $this->dataContact['cp_ent'] : $this->dataContact['cp_cont'];
        $data['ville_fact'] = ($this->dataContact['ville_ent'] != '') ? $this->dataContact['ville_ent'] : $this->dataContact['ville_cont'];

        $rs = $sql->insert($data, 'WS', $liste);
        if(!$rs[0])
            return array(false, 'Erreur SQL : '.$rs[1], true);
        $this->facture = $data['id_fact'];
        return array(true, $data['id_fact']);
    }

    /**
     * Méthode qui génère la facture en pdf et l'envoie au client
     * @param string $status Indique "non paye" ou "paye"
     * @return array Indique le résultat des actions.
     */
    private function genererFacture($status = 'non paye') {
        Logg::loggerInfo('Payement.genererFacture() ~ Generation de la facture '.$this->facture,serialize($this),__FILE__.'@'.__LINE__);
        if($status == 'paye') {
            $sql = new factureModel();
            $sql->update(array('commentreglement_fact' => 'Facture prélevée le '.date('d/m/Y')), $this->facture);
            $mess = "<br />Le prélèvement du montant TTC de votre facture (".prepareNombreAffichage($this->montant)." €)  a été réalisé.";
        }
        else $mess = "<br />Le prélèvement du montant TTC de votre facture (".prepareNombreAffichage($this->montant)." €) n'a pas pu être réalisé.<br/>Nous avons rencontré l'erreur suivante : ".$status;
        $gnose = new factureGnose();
        $data['fichier'] = $gnose->FactureGenerateDocument($this->facture);
        if(!is_string($data['fichier']))
            return array(false, 'Impossible de générer le document');
        $gnose->FactureSaveDocInGnose($data['fichier'],$this->facture,"Facture générée par le web service payement");
        $data['id'] = 0;
        $data['partie'] = 'send';
        $data['typeE'] = 'mail';
        $data['expediteur'] = 'Zuno';
        $data['from'] = "zuno@startx.fr";
        $data['typeEmail'] = 'html';
        $data['bug'] = true;
        $data['sujet'] = 'Facturation service ZUNO';
        $data['message'] = "Madame, Monsieur, <br />Veuillez trouver ci-joint la facture correspondant à votre abonnement à ZUNO.$mess<br />Coordialement, <br />L'équipe commerciale ZUNO.";
        $data['mail'] = $this->dataContact['mail_cont'];
        $data['path'] = $GLOBALS['ZunoFacture']['dir.facture'];
        $sender = new Sender($data);
        $rs = $sender->send();
        if($rs[0]) {
            $sql = new factureModel();
            $sql->update(array('dateenvoi_fact' => date('Y-m-d H:i:s'), 'status_fact' => '4'), $this->facture);
        }
        else {
            Logg::loggerNotice('Payement.genererFacture() ~ Erreur dans l\'envoi du mail de confirmation  ',serialize($rs),__FILE__.'@'.__LINE__);
            $rs[0] = $rs[2] = true;
        }
        return $rs;
    }

    /**
     * Méthode qui facture les clients débite d'après une facture
     * @param bool $payer Permet d'enchainer directement sur le payement de la facture
     * @param bool $save Dans le cas d'un payment dans la foulée précise si on enregistre les coordonnées bancaires
     * @return array Indique le bon déroulement
     */
    public function facturer($payer = false, $save = false) {
        Logg::loggerInfo('Payement.facturer() ~ Facturation du contact '.$this->contact,serialize($this),__FILE__.'@'.__LINE__);
        $rs = $this->creerFacture();
        if(!$rs[0])
            return array_merge($rs, array('2'=>true), array('3' => $this->getFactureDatas()));

        if($payer) {
            $this->reference = "Fact".$this->facture;
            $sql = new factureModel();
            $this->montant = $sql->getMontantTTC($this->facture);
            $rs = $this->doPayement($save);
            if($rs[0]) {
                $rs1 = $this->genererFacture('paye');
                if(!$rs1[0])
                    return array_merge($rs1, array('2'=>true), array('3' => $this->getFactureDatas()));
                $sql->update(array('status_fact' => '6'), $this->facture);
            }
            elseif($rs[2]) {
                $rs1 = $this->genererFacture('Une erreur interne est survenue');
                if(!$rs1[0])
                    return array_merge($rs1, array('2'=>true), array('3' => $this->getFactureDatas()));
            }
            else {
                $rs1 = $this->genererFacture($rs[1]);
                if(!$rs1[0])
                    return array_merge($rs1, array('2'=>true), array('3' => $this->getFactureDatas()));
            }
        }
        if(!is_bool($rs[2]))
            $rs[2] = false;
        $rs[3] = $this->getFactureDatas();
        return $rs;
    }

    /**
     * Méthode qui permet de payer une facture précise
     * @param bool $save Précise si on sauvegarde en interne les nouvelles données
     * @return array Indique si tout s'est bien déroulé
     */
    public function payerFacture($save = false) {
        Logg::loggerInfo('Payement.payerFacture() ~ Paiement de la facture '.$this->facture,serialize($this),__FILE__.'@'.__LINE__);
        if($this->facture != '') {
            $this->reference = "Fact".$this->facture;
            $sql = new factureModel();
            $status = $sql->getStatusFacture($this->facture);
            if($status[1][0] > 5)
                return array(false, 'Facture déjà payée', true, $this->getFactureDatas());
            $this->setContact($sql->getContactFromFacture($this->facture));
            $this->montant = $sql->getMontantTTC($this->facture);
            $rs = $this->doPayement($save);
            if($rs[0]) {
                $sql->update(array('status_fact' => '6'), $this->facture);
            }
        }
        else {
            $rs = array(false, 'Aucune facture précisée', true);
        }
        $this->getFactureDatas();
        $rs[2] = false;
        $rs[3] = $this->dataFact;
        return $rs;
    }

    /**
     * Méthode qui débite un client avant de générer la facture
     * @return array Indique si tout s'est bien déroulé
     */
    public function payerPuisFacturer() {
        Logg::loggerInfo('Payement.payerPuisFacturer() ~ Paiement puis facturation pour le contact '.$this->contact,serialize($this),__FILE__.'@'.__LINE__);
        $liste = unserialize($this->articles);
        $montant = 0;
        foreach($liste as $v)
            $montant += $v['prix']*(1-$v['remise']/100)*$v['quantite'];
        $tva = 1.196;//Todo récup tva
        $this->montant = prepareNombreTraitement($montant*$tva);
        $sql = new factureModel();
        $this->reference = "Fact".($sql->getLastId() +1);
        $rs1 = $this->doPayement();
        if($rs1[0]) {
            $rs = $this->creerFacture();
            if(!$rs[0]) {
                Logg::loggerError('Payement.payerPuisFacturer() ~ Erreur dans la création de la facture  ',serialize($rs),__FILE__.'@'.__LINE__);
                return $rs;
            }
            $rs = $this->genererFacture('paye');
            if(!$rs[0]) {
                Logg::loggerError('Payement.payerPuisFacturer() ~ Erreur dans la generation de la facture  ',serialize($rs),__FILE__.'@'.__LINE__);
                return array_merge($rs, array('2'=>true));
            }
            else $sql->update(array('status_fact' => '6'), $this->facture);
        }
        else Logg::loggerError('Payement.payerPuisFacturer() ~ Erreur dans le paiement  ',serialize($rs1),__FILE__.'@'.__LINE__);
        $this->getFactureDatas();
        if(!is_bool($rs[2]))
            $rs[2] = false;
        $rs1[3] = $this->dataFact;
        return $rs1;
    }

    /**
     * Méthode qui retourne toutes les infos sur la facture et ses produits
     * @param int $id L'id de la facture
     * @return array La facture
     */
    public function getInfosFacture($id) {
        $this->facture = $id;
        $this->getFactureDatas();
        if(is_array($this->dataFact))
            return serialize(array('0' => '0', '1' => 'Impossible de récupérer les informations'));
        return $this->dataFact;
    }
}
?>
