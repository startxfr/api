<?php
/*#########################################################################
#
#   name :       payement.php
#   desc :       Web Service
#   categorie :  management page
#   ID :  	 $Id:$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZA.Wsdl','ZModels/TransactionModel', 'ZModels/PaylineModel', 'ZModels/FactureModel', 'Payline/Payline', 'ZDoc/FactureDoc', 'Send/Send'));
include_once 'class/Payement.php';
setlocale(LC_ALL,'fr_FR.UTF8');

$server = new zunoWsdlServer('payement','Gestion des payements des clients ZUNO');
//Initialisation du serveur
$server->server->wsdl->addComplexType('MyRetour',
        'complexType',
        'struct',
        'all',
        '',
        array(
        'code_retour' => array(
                'name' => 'code_retour',
                'type' => 'xsd:string'
        ),
        'message' => array(
                'name' => 'message',
                'type' => 'xsd:string'
        ),
        'interne' => array(
                'name' => 'interne',
                'type' => 'xsd:boolean'
        ),
        'facture' => array(
            'name' => 'facture',
            'type' => 'xsd:string'
        )
        )
);
//Crétion d'une structure pour organiser les retours des Web services
$server->registerAuthenticatedAction('doFirstPayement',
        array('civ' => 'xsd:string',
        'prenom' => 'xsd:string',
        'nom' => 'xsd:string',
        'carte' => 'xsd:string',
        'date' => 'xsd:string',
        'cvv' => 'xsd:string',
        'montant' => 'xsd:string',
        'save' => 'xsd:boolean',
        'contact' => 'xsd:string'),
        array('return' => 'tns:MyRetour'),
        'Premier paiement d\'un client'
);
//Web service pour effectuer une création d'un wallet avec un paiement dans la foulée
$server->registerAuthenticatedAction('doPayementFacture',
        array('facture' => 'xsd:string'),
        array('return' => 'tns:MyRetour'),
        'Paiement d\'une facture donnée. Le client prélevé sera celui lié à la facture'
);
//Web service pour faire payer une facture existante
$server->registerAuthenticatedAction('doFirstPayementWithFacture',
        array('civ' => 'xsd:string',
        'prenom' => 'xsd:string',
        'nom' => 'xsd:string',
        'carte' => 'xsd:string',
        'date' => 'xsd:string',
        'cvv' => 'xsd:string',
        'montant' => 'xsd:string',
        'save' => 'xsd:boolean',
        'contact' => 'xsd:string',
        'articles' => 'xsd:string'),
        array('return' => 'tns:MyRetour'),
        'Premier paiement d\'un client et génération automatique de la facture'
);
//Web service pour créer un wallet, effectuer le paiement, générer la facture correspondante et l'envoyer par mail à l'acheteur
$server->registerAuthenticatedAction('doRegistredContactPayement',
        array('id' => 'xsd:string',
        'montant' => 'xsd:string',
        'articles' => 'xsd:string'),
        array('return' => 'tns:MyRetour'),
        'Paiement d\'un client sauvegardé'
);
//Web service pour faire payer un mec ayant déjà effectué un paiement
$server->registerAuthenticatedAction('getFacture',
        array('id' => 'xsd:string',
        'date' => 'xsd:string',
        'contact' => 'xsd:string'),
        array('return'  => 'xsd:base64Binary'),
        'Le pdf de la facture de l\'id donné ou bien du client de la date demandée'
);
//Web service qui retourne une facture soit par son id soit par le client et la date sous forme de fichier encodé en base64
$server->registerAuthenticatedAction('listeFactures',
        array('id' => 'xsd:string'),
        array('return' => 'xsd:string'),
        'La liste des factures de ce client'
);
//Web service qui retourne la liste des factures liées à un client
$server->registerAuthenticatedAction('synchroFacture',
        array('id' => 'xsd:string'),
        array('return' => 'xsd:string'),
        'Toutes informations dans la BDD de la facture demandée');
//Web service qui retourne toutes les informations sur la facture demandée
$server->service();
//Lancement des Webservices

function doFirstPayement($token, $civ, $prenom, $nom, $carte, $date, $cvv, $montant, $save, $contact, $facturation = false, $articles = array()) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($civ != 'M.' and $civ != 'Melle' and $civ != 'Mme' and $civ != 'M') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doFirstPayement','Le paramètre Civilité est invalide',array($civ));
        }
        elseif($civ == 'M') {
            $civ = 'M.';
        }
        if($prenom == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doFirstPayement','Le paramètre prenom est invalide',array($prenom));
        }
        if($carte == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doFirstPayement','Le paramètre carte est invalide',array($carte));
        }
        if($date == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doFirstPayement','Le paramètre date est invalide',array($date));
        }
        if($cvv == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doFirstPayement','Le paramètre cvv est invalide',array($cvv));
        }
        if(is_nan($montant)) {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doFirstPayement','Le paramètre montant est invalide',array($montant));
        }
        if($nom == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doFirstPayement','Le paramètre nom est invalide',array($nom));
        }
        if($save != false) {
            $save = true;
        }
        if($save and $contact == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doFirstPayement','Le paramètre contact est invalide',array($contact));
        }
        $payement = new Payement();
        $payement->setNom($nom);
        $payement->setPrenom($prenom);
        $payement->setContact($contact);
        $payement->setCarte($carte);
        $payement->setDate($date);
        $payement->setCvv($cvv);
        if($facturation) {
            $payement->setArticles($articles);
            $out =  $payement->payerPuisFacturer($save);
        }
        else {
            $payement->setMontant($montant);
            $out = $payement->doPayement($save);
        }
        return array('code_retour' => $out[0], 'message' => $out[1], 'interne' => $out[2], 'facture' => $out[3]);
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.payement.doFirstPayement',$zunoLastError,array($token));
}

function doFirstPayementWithFacture($token, $civ, $prenom, $nom, $carte, $date, $cvv, $montant, $save, $contact, $articles) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        return doFirstPayement($token, $civ, $prenom, $nom, $carte, $date, $cvv, $montant, $save, $contact, true, $articles);
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.payement.doFirstPayementWithFacture',$zunoLastError,array($token));
}

function doPayementFacture($token, $facture) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($facture == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doPayementFacture','Le paramètre facture est invalide',array($id));
        }
        $payement = new Payement();
        $payement->setFacture($facture);
        $out = $payement->payerFacture();
        return array('code_retour' => $out[0], 'message' => $out[1], 'interne' => $out[2], 'facture' => $out[3]);
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.payement.doFirstPayementWithFacture',$zunoLastError,array($token));
}

function doRegistredContactPayement($token, $id, $montant, $articles) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($id == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doRegistredContactPayement','Le paramètre id est invalide',array($id));
        }
        if(is_nan($montant) or $articles == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.doRegistredContactPayement','Le paramètre montant est invalide',array($montant));
        }
        $payement = new Payement();
        $payement->setContact($id);
        if($articles == '') {
            $payement->setMontant($montant);
            $out = $payement->doPayement();
        }
        else {
            $payement->setArticles($articles);
            $out = $payement->facturer(true);
        }
        return array('code_retour' => $out[0], 'message' => $out[1], 'interne' => $out[2], 'facture' => $out[3]);
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.payement.doRegistredContactPayement',$zunoLastError,array($token));
}

function getFacture($token, $id, $date, $contact) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {

        if($id == '' or ( $date == '' and $contact == '')) {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.getFacture','Le paramètre id est invalide',array($id));
        }
        $payement = new Payement();
        if($id != '')
            $rs = $payement->getFactureByID($id);
        else {
            $payement->setContact($contact);
            $rs = $payement->getFacture($date);
        }
        if($rs[0])
            return $rs[1];
        else
            return zunoWsdlServer::raiseFault('internalError','webservice sxa.payement.getFacture',$rs[1],array());

    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.payement.getFacture',$zunoLastError,array($token));
}

function listeFactures($token, $id) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {

        if($id == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.listeFactures','Le paramètre id est invalide',array($id));
        }
        $payement = new Payement();
        $payement->setContact($id);
        $rs = $payement->getListeFacture();
        if($rs[0])
            return $rs[1];
        else
            return zunoWsdlServer::raiseFault('internalError','webservice sxa.payement.listeFactures',$rs[1],array());

    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.payement.listeFactures',$zunoLastError,array($token));
}

function synchroFacture($token, $id){
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($id == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.payement.listeFactures','Le paramètre id est invalide',array($id));
        }
        $payement = new Payement();
        return $payement->getInfosFacture($id);
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.payement.listeFactures',$zunoLastError,array($token));
}

?>
