<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/



/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZA.Wsdl','ZModels/TransactionModel', 'ZModels/PaylineModel', 'Payline/Payline', 'ZModels/ContactModel'));
setlocale(LC_ALL,'fr_FR.UTF8');


$server = new zunoWsdlServer('client','Gestion des données des clients ZUNO');
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
        )
        )
);
//Crétion d'une structure pour organiser les retours des Web services
$server->registerAuthenticatedAction('updateContactWallet',
        array('civ' => 'xsd:string',
        'prenom' => 'xsd:string',
        'nom' => 'xsd:string',
        'carte' => 'xsd:string',
        'date' => 'xsd:string',
        'cvv' => 'xsd:string',
        'contact' => 'xsd:string'),
        array('return' => 'tns:MyRetour'),
        'Mise à jour des informations de paiement'
);
//Web service pour mettre à jour les coordonnées bancaires d'un client
$server->registerAuthenticatedAction('updateContact',
        array('id' => 'xsd:string',
        'values' => 'xsd:string'),
        array('return' => 'xsd:string'),
        'Mise à jour des informations non bancaires du client'
);
//Web service pour mettre à jour les informations d'un contact
$server->registerAuthenticatedAction('updateEntreprise',
        array('id' => 'xsd:string',
        'values' => 'xsd:string'),
        array('return' => 'xsd:string'),
        'Mise à jour des informations non bancaires de l\'entreprise du client'
);
//Web service pour mettre à jour les informations d'une entreprise
$server->registerAuthenticatedAction('updateClient',
        array('contact' => 'xsd:string',
        'valCont' => 'xsd:string',
        'entreprise' => 'xsd:string',
        'valEnt' => 'xsd:string'),
        array('return' => 'xsd:string'),
        'Mise à jour des informations non bancaires de l\'entreprise et du client'
);
//Web service pour mettre à jour les informations d'un client, sauf les informations bancaires
$server->registerAuthenticatedAction('addClient',
        array('valCont' => 'xsd:string',
        'valEnt' => 'xsd:string'),
        array('return' => 'xsd:string'),
        'Ajout/création des informations non bancaires de l\'entreprise et du client'
);
//Web service pour créer un client, sans préciser ses informations bancaires
$server->service();
//Lancement des Webservices

function addClient($token, $valCont, $valEnt) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if(!is_array(unserialize($valCont)) or array_key_exists('id_cont', unserialize($valCont))) {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addClient','Le paramètre valCont est invalide',array($valCont));
        }
        if(!is_array(unserialize($valEnt)) or array_key_exists('id_ent', unserialize($valEnt))) {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addClient','Le paramètre valEnt est invalide',array($valEnt));
        }
        $model2 = new contactEntrepriseModel();
        $out = $model2->insert(unserialize($valEnt));
        $ent = $model2->getLastId();
        if($out[0]) {
            $model = new contactParticulierModel();
            $temp = unserialize($valCont);
            $temp['entreprise_cont'] = $ent;
            $out = $model->insert($temp);
            if(!$out[0]) {
                $model2->delete($ent);
            }
            else{
                $cont = $model->getLastId();
            }
        }
        if($out[0]){
            $out['entreprise'] = $ent;
            $out['contact'] = $cont;
        }
        return serialize($out);

    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContact',$zunoLastError,array($token));
}

function updateClient($token, $contact, $valCont, $entreprise, $valEnt) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($contact == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateClient','Le paramètre contact est invalide',array($contact));
        }
        if(!is_array(unserialize($valCont)) or array_key_exists('id_cont', unserialize($valCont))  or array_key_exists('entreprise_cont', unserialize($valCont))) {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateClient','Le paramètre valCont est invalide',array($valCont));
        }
        if($entreprise == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateClient','Le paramètre entreprise est invalide',array($entreprise));
        }
        if(!is_array(unserialize($valEnt)) or array_key_exists('id_ent', unserialize($valEnt))) {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateClient','Le paramètre valEnt est invalide',array($valEnt));
        }
        $model = new contactParticulierModel();
        $out = $model->update(unserialize($valCont), $contact);
        if($out[0]) {
            $model = new contactEntrepriseModel();
            return serialize($model->update(unserialize($valEnt), $entreprise));
        }
        else
            return serialize($out);

    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContact',$zunoLastError,array($token));
}

function updateContact($token, $id, $values) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($id == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContact','Le paramètre id est invalide',array($id));
        }
        if(!is_array(unserialize($values)) or array_key_exists('id_cont', unserialize($values))  or array_key_exists('entreprise_cont', unserialize($values))) {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContact','Le paramètre values est invalide',array($values));
        }
        $model = new contactParticulierModel();
        return serialize($model->update(unserialize($values), $id));
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContact',$zunoLastError,array($token));
}

function updateEntreprise($token, $id, $values) {
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($id == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateEntreprise','Le paramètre id est invalide',array($id));
        }
        if(!is_array(unserialize($values)) or array_key_exists('id_ent', unserialize($values))) {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateEntreprise','Le paramètre values est invalide',array($values));
        }
        $model = new contactEntrepriseModel();
        return serialize($model->update(unserialize($values), $id));
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContact',$zunoLastError,array($token));
}

function updateContactWallet($token, $civ, $prenom, $nom, $carte, $date, $cvv, $contact) {
    require_once 'class/Payement.php';
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($prenom == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre prenom est invalide',array($prenom));
        }
        if($carte == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre carte est invalide',array($carte));
        }
        if($date == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre date est invalide',array($date));
        }
        if($cvv == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre cvv est invalide',array($cvv));
        }
        if($nom == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre nom est invalide',array($nom));
        }
        if($contact == '') {
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre contact est invalide',array($contact));
        }
        $payement = new Payement();
        $payement->setNom($nom);
        $payement->setPrenom($prenom);
        $payement->setContact($contact);
        $payement->setCarte($carte);
        $payement->setDate($date);
        $payement->setCvv($cvv);
        $out = $payement->updateDatas();
        return array('code_retour' => $out[0], 'message' => $out[1], 'interne' => $out[2]);
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContactWallet',$zunoLastError,array($token));
}


?>
