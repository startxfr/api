<?php
/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZA.Wsdl','ZModels/TransactionModel', 'ZModels/PaylineModel', 'Payline/Payline', 'ZModels/ContactModel', 'ZModels/AffaireModel'));
setlocale(LC_ALL,'fr_FR.UTF8');
$GLOBALS['currentChannel'] = 'webservice';
if($_SERVER['QUERY_STRING'] == 'wsdl')
    Logg::loggerInfo('webservice sxa.client ~ Appel au fichier WSDL CLIENT',"IP : ".$_SERVER['REMOTE_ADDR'],__FILE__.'@'.__LINE__);
else Logg::loggerInfo('webservice sxa.client ~ Appel au webservice CLIENT',"IP : ".$_SERVER['REMOTE_ADDR'],__FILE__.'@'.__LINE__);


$server = new zunoWsdlServer('client','Gestion des données des clients ZUNO sur l\'application SXA');
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
        'contact' => 'xsd:string',
        'typeCarte' => 'xsd:string'),
        array('return' => 'tns:MyRetour'),
        'Mise à jour des informations de paiement'
);
//Web service pour ajouter les coordonnées bancaires d'un client
$server->registerAuthenticatedAction('addContactWallet',
        array('civ' => 'xsd:string',
        'prenom' => 'xsd:string',
        'nom' => 'xsd:string',
        'carte' => 'xsd:string',
        'date' => 'xsd:string',
        'cvv' => 'xsd:string',
        'contact' => 'xsd:string',
        'typeCarte' => 'xsd:string'),
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
$server->registerAuthenticatedAction('getListeEntreprise',
        array('recherche' => 'xsd:string'),
        array('return' => 'xsd:string'),
        'Récupération d\'une liste d\'entreprises correspondant à la recherche'
);
//Web service pour récupérer une liste d'entreprises
$server->registerAuthenticatedAction('getClient',
        array('id_ent' => 'xsd:string'),
        array('return' => 'xsd:string'),
        'Récupération des données d\'une entreprise'
);
//Web service pour récupérer les données d'une entreprise
$server->service();
//Lancement des Webservices

function addClient($token, $valCont, $valEnt) {
    Logg::loggerInfo('webservice sxa.client.addClient() ~ Début du traitement',"Contact : ".$valCont."\nEntreprise : ".$valEnt,__FILE__.'@'.__LINE__);
    $addAffaire = true;
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if(!is_array(unserialize($valCont)) or array_key_exists('id_cont', unserialize($valCont)))
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addClient','Le paramètre valCont est invalide',array($valCont),__FILE__,__LINE__);
        if(!is_array(unserialize($valEnt)) or array_key_exists('id_ent', unserialize($valEnt)))
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addClient','Le paramètre valEnt est invalide',array($valEnt),__FILE__,__LINE__);
        $model2 = new contactEntrepriseModel();
        $out = $model2->insert(unserialize($valEnt));
        $ent = $model2->getLastId();
        if($out[0]) {
            $model = new contactParticulierModel();
            $temp = unserialize($valCont);
            $temp['entreprise_cont'] = $ent;
            $out = $model->insert($temp);
            if(!$out[0])
                $model2->delete($ent);
            else $cont = $model->getLastId();
        }
        if($out[0]) {
            if($addAffaire) {
                $aff['id_aff'] = affaireModel::affaireGenerateID();
                $aff['entreprise_aff'] = $ent;
                $aff['contact_aff'] = $cont;
                $aff['actif_aff'] = '1';
                $aff['archived_aff'] = '0';
                $aff['titre_aff'] = 'Client ZUNO';
                $aff['modif_aff'] = date('Y-m-d');
                $aff['detect_aff'] = date('Y-m-d');
                $aff['status_aff'] = '5';
                $aff['typeproj_aff'] = '6';
                $aff['commercial_aff'] = 'mg';
                $aff['technique_aff'] = 'cl';
                affaireModel::createNewAffaireInDB($aff,true,'creation','');
            }
            $out['entreprise'] = $ent;
            $out['contact'] = $cont;
        }
        Logg::loggerInfo('webservice sxa.client.addClient() ~ fin du traitement',serialize($out),__FILE__.'@'.__LINE__);
        return serialize($out);
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContact',$zunoLastError,array($token),__FILE__,__LINE__);
}

function updateClient($token, $contact, $valCont, $entreprise, $valEnt) {
    Logg::loggerInfo('webservice sxa.client.updateClient() ~ Début du traitement',"Contact : ".$contact."\nNouvelles valeurs : ".$valCont."\nEntreprise : ".$entreprise."\nNouvelles valeurs : ".$valEnt,__FILE__.'@'.__LINE__);
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($contact == '')
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateClient','Le paramètre contact est invalide',array($contact),__FILE__,__LINE__);
        if(!is_array(unserialize($valCont)) or array_key_exists('id_cont', unserialize($valCont))  or array_key_exists('entreprise_cont', unserialize($valCont)))
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateClient','Le paramètre valCont est invalide',array($valCont),__FILE__,__LINE__);
        if($entreprise == '')
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateClient','Le paramètre entreprise est invalide',array($entreprise),__FILE__,__LINE__);
        if(!is_array(unserialize($valEnt)) or array_key_exists('id_ent', unserialize($valEnt)))
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateClient','Le paramètre valEnt est invalide',array($valEnt),__FILE__,__LINE__);
        $model = new contactParticulierModel();
        $out = $model->update(unserialize($valCont), $contact);
        if($out[0]) {
            $model = new contactEntrepriseModel();
            return serialize($model->update(unserialize($valEnt), $entreprise));
        }
        else {
            Logg::loggerInfo('webservice sxa.client.updateClient() ~ fin du traitement',serialize($out),__FILE__.'@'.__LINE__);
            return serialize($out);
        }
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContact',$zunoLastError,array($token),__FILE__,__LINE__);
}

function updateContact($token, $id, $values) {
    Logg::loggerInfo('webservice sxa.client.updateContact() ~ Début du traitement',"Contact : ".$id."\nNouvelles valeurs : ".$values,__FILE__.'@'.__LINE__);
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($id == '')
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContact','Le paramètre id est invalide',array($id),__FILE__,__LINE__);
        if(!is_array(unserialize($values)) or array_key_exists('id_cont', unserialize($values))  or array_key_exists('entreprise_cont', unserialize($values)))
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContact','Le paramètre values est invalide',array($values),__FILE__,__LINE__);
        $model = new contactParticulierModel();
        $sortie = serialize($model->update(unserialize($values), $id));
        Logg::loggerInfo('webservice sxa.client.updateContact() ~ fin du traitement',$sortie,__FILE__.'@'.__LINE__);
        return $sortie;
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContact',$zunoLastError,array($token),__FILE__,__LINE__);
}

function updateEntreprise($token, $id, $values) {
    Logg::loggerInfo('webservice sxa.client.updateEntreprise() ~ Début du traitement',"Entreprise : ".$id."\nNouvelles valeurs : ".$values,__FILE__.'@'.__LINE__);
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($id == '')
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateEntreprise','Le paramètre id est invalide',array($id),__FILE__,__LINE__);
        if(!is_array(unserialize($values)) or array_key_exists('id_ent', unserialize($values)))
            return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateEntreprise','Le paramètre values est invalide',array($values),__FILE__,__LINE__);
        $model = new contactEntrepriseModel();
        $sortie = serialize($model->update(unserialize($values), $id));
        Logg::loggerInfo('webservice sxa.client.updateEntreprise() ~ fin du traitement',$sortie,__FILE__.'@'.__LINE__);
        return $sortie;
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContact',$zunoLastError,array($token),__FILE__,__LINE__);
}

function updateContactWallet($token, $civ, $prenom, $nom, $carte, $date, $cvv, $contact,$typeCarte) {
    Logg::loggerInfo('webservice sxa.client.updateContactWallet() ~ Début du traitement',serialize(array($prenom, $nom, $carte, $date, $cvv, $contact,$typeCarte)),__FILE__.'@'.__LINE__);
    require_once 'class/Payement.php';
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($prenom == '')   return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre prenom est invalide',array($prenom),__FILE__,__LINE__);
        if($carte == '')    return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre carte est invalide',array($carte),__FILE__,__LINE__);
        if($date == '')	    return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre date est invalide',array($date),__FILE__,__LINE__);
        if($cvv == '')	    return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre cvv est invalide',array($cvv),__FILE__,__LINE__);
        if($nom == '')	    return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre nom est invalide',array($nom),__FILE__,__LINE__);
        if($contact == '')  return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre contact est invalide',array($contact),__FILE__,__LINE__);
        if($typeCarte == '') return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.updateContactWallet','Le paramètre typeCarte est invalide',array($typeCarte),__FILE__,__LINE__);
        $payement = new Payement();
        $payement->setContact($contact);
        $payement->setNom($nom);
        $payement->setPrenom($prenom);
        $payement->setCarte($carte);
        $payement->setDate($date);
        $payement->setCvv($cvv);
        $payement->setTypeCarte($typeCarte);
        $out = $payement->updateDatas();
        $sortie = array('code_retour' => $out[0], 'message' => $out[1], 'interne' => $out[2]);
        Logg::loggerInfo('webservice sxa.client.updateContactWallet() ~ fin du traitement',$sortie,__FILE__.'@'.__LINE__);
        return $sortie;
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.updateContactWallet',$zunoLastError,array($token),__FILE__,__LINE__);
}


function addContactWallet($token, $civ, $prenom, $nom, $carte, $date, $cvv, $contact,$typeCarte) {
    Logg::loggerInfo('webservice sxa.client.addContactWallet() ~ Début du traitement',serialize(array($prenom, $nom, $carte, $date, $cvv, $contact,$typeCarte)),__FILE__.'@'.__LINE__);
    require_once 'class/Payement.php';
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        if($prenom == '')   return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addContactWallet','Le paramètre prenom est invalide',array($prenom),__FILE__,__LINE__);
        if($carte == '')    return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addContactWallet','Le paramètre carte est invalide',array($carte),__FILE__,__LINE__);
        if($date == '')	    return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addContactWallet','Le paramètre date est invalide',array($date),__FILE__,__LINE__);
        if($cvv == '')	    return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addContactWallet','Le paramètre cvv est invalide',array($cvv),__FILE__,__LINE__);
        if($nom == '')	    return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addContactWallet','Le paramètre nom est invalide',array($nom),__FILE__,__LINE__);
        if($contact == '')  return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addContactWallet','Le paramètre contact est invalide',array($contact),__FILE__,__LINE__);
        if($typeCarte == '')return zunoWsdlServer::raiseFault('missingStatement','webservice sxa.client.addContactWallet','Le paramètre typeCarte est invalide',array($typeCarte),__FILE__,__LINE__);
        $pl = new Payline();
        $pl->setNom($nom);
        $pl->setPrenom($prenom);
        $pl->setCarte($carte);
        $pl->setDateCarte($date);
        $pl->setCvvCarte($cvv);
        $pl->setTypeCarte($typeCarte);
        $pl->setContact($contact, false);
        $pl->generateWalletId();
        $pl->saveCarteDatas();
        $pl->setContact($contact, true);
        $model = new PaylineModel();
        $data = $model->getInfosContact($contact);
        Logg::loggerInfo('webservice sxa.client.addContactWallet() ~ Test de la présence du Wallet '.$data['wallet_cont'],$data,__FILE__.'@'.__LINE__);
        if($data['wallet_cont'] != '') {
            $out = $pl->updateWallet();
        }
        else {
            $out = $pl->createWallet();
        }
        $sortie = array('code_retour' => $out[0], 'message' => $out[1], 'interne' => $out[2]);
        Logg::loggerInfo('webservice sxa.client.addContactWallet() ~ fin du traitement',$sortie,__FILE__.'@'.__LINE__);
        return $sortie;
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.addContactWallet',$zunoLastError,array($token),__FILE__,__LINE__);
}

function getListeEntreprise($token, $recherche) {
    Logg::loggerInfo('webservice sxa.client.getListeEntreprise() ~ Début du traitement',"Recherche : ".$recherche,__FILE__.'@'.__LINE__);
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        loadPlugin('ZModels/ContactModel');
        $model = new contactEntrepriseModel();
        $sortie = $model->getDataForSearch($recherche);
        return serialize($sortie);
    }
    else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.getListeEntreprise',$zunoLastError,array($token),__FILE__,__LINE__);
}

function getClient($token, $id_ent) {

    Logg::loggerInfo('webservice sxa.client.getClient() ~ Début du traitement',"Recherche : ".$recherche,__FILE__.'@'.__LINE__);
    global $zunoLastError;
    if(zunoWsdlServer::checkServerCredentials($token)) {
        loadPlugin('ZModels/ContactModel');
        $model = new contactEntrepriseModel();
        $sortie = $model->getDataFromID($id_ent);
        return serialize($sortie);

    }else return zunoWsdlServer::raiseFault('accesNonAutorisé','webservice sxa.client.getClient',$zunoLastError,array($token),__FILE__,__LINE__);
}

?>