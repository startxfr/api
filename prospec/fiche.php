<?php
/*#########################################################################
#
#   name :       fiche.php
#   desc :       Prospection list for sxprospec
#   categorie :  prospec page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZView/ProspecView','ZView/ContactView'));
loadPlugin(array('ZModels/ContactModel'));
loadPlugin(array('ZControl/ContactControl'));
loadPlugin(array('ZControl/GeneralControl'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if($PC->rcvG['id_ent'] != '') {
    $sortie = viewFiche($PC->rcvG['id_ent'], 'contactEntreprise', '', 'non', 'web', true);
}
elseif($PC->rcvP['action'] == 'modifLightEnt') {
    aiJeLeDroit('contact', 15, 'web');
    $PC->rcvP['tel_ent'] = prepareTelTraitement($PC->rcvP['tel_ent']);
    $PC->rcvP['telsi_ent'] = prepareTelTraitement($PC->rcvP['telsi_ent']);
    $PC->rcvP['fax_ent'] = prepareTelTraitement($PC->rcvP['fax_ent']);
    $PC->rcvP['remise_ent'] = prepareNombreTraitement($PC->rcvP['remise_ent']);
    $PC->rcvP['remise_ent'] = prepareNombreTraitement($PC->rcvP['remise_ent']);
    $PC->rcvP['tauxTVA_ent'] = prepareNombreTraitement($PC->rcvP['tauxTVA_ent']);
    if(!array_key_exists('siege_ent', $PC->rcvP))
	$PC->rcvP['siege_ent'] = '0';
    echo updateBDD($PC->rcvP['id_ent'], 'contactEntreprise', $PC->rcvP, 'LightInfo', 'web', true);
    exit;
}
elseif($PC->rcvP['action'] == 'modifEntPopup') {
    aiJeLeDroit('contact', 15, 'web');
    $PC->rcvP['tel_ent'] = prepareTelTraitement($PC->rcvP['tel_ent']);
    $PC->rcvP['telsi_ent'] = prepareTelTraitement($PC->rcvP['telsi_ent']);
    $PC->rcvP['fax_ent'] = prepareTelTraitement($PC->rcvP['fax_ent']);
    $PC->rcvP['remise_ent'] = prepareNombreTraitement($PC->rcvP['remise_ent']);
    $PC->rcvP['remise_ent'] = prepareNombreTraitement($PC->rcvP['remise_ent']);
    $PC->rcvP['tauxTVA_ent'] = prepareNombreTraitement($PC->rcvP['tauxTVA_ent']);
    if(!array_key_exists('siege_ent', $PC->rcvP))
	$PC->rcvP['siege_ent'] = '0';
    echo updateBDD($PC->rcvP['id_ent'], 'contactEntreprise', $PC->rcvP, 'popup', 'web', true);
    exit;
}
elseif($PC->rcvP['action'] == 'modifBigEnt') {
    aiJeLeDroit('contact', 15, 'web');
    $PC->rcvP['tel_ent'] = prepareTelTraitement($PC->rcvP['tel_ent']);
    $PC->rcvP['telsi_ent'] = prepareTelTraitement($PC->rcvP['telsi_ent']);
    $PC->rcvP['fax_ent'] = prepareTelTraitement($PC->rcvP['fax_ent']);
    $PC->rcvP['remise_ent'] = prepareNombreTraitement($PC->rcvP['remise_ent']);
    $PC->rcvP['tauxTVA_ent'] = prepareNombreTraitement($PC->rcvP['tauxTVA_ent']);
    if(!array_key_exists('siege_ent', $PC->rcvP))
	$PC->rcvP['siege_ent'] = '0';
    echo updateBDD($PC->rcvP['id_ent'], 'contactEntreprise', $PC->rcvP, 'BigInfo', 'web', true);

    exit;
}
elseif($PC->rcvP['action'] == 'creerEnt') {
    aiJeLeDroit('contact', 20, 'web');
    $PC->rcvP['tel_ent'] = prepareTelTraitement($PC->rcvP['tel_ent']);
    $PC->rcvP['telsi_ent'] = prepareTelTraitement($PC->rcvP['telsi_ent']);
    $PC->rcvP['fax_ent'] = prepareTelTraitement($PC->rcvP['fax_ent']);
    $PC->rcvP['remise_ent'] = prepareNombreTraitement($PC->rcvP['remise_ent']);
    $PC->rcvP['tauxTVA_ent'] = prepareNombreTraitement($PC->rcvP['tauxTVA_ent']);
    unset($PC->rcvP['id_ent']);
    insertBDD('contactEntreprise', $PC->rcvP, 'all', 'web', true);
    exit;
}
elseif($PC->rcvG['action'] == 'addContEnt') {
    aiJeLeDroit('contact', 15, 'web');
    loadPlugin(array('ZView/ContactView'));
    $view = new contactView();
    $dev = new contactEntrepriseModel();
    $data['listePays'] = $dev->getPays();
    $data['entreprise_cont'] = $PC->rcvG['entreprise'];
    $data['idRetour'] = $PC->rcvG['idRetour'];
    $data['from'] = 'contactEntreprise';
    echo $view->popupCont($data);
    exit;
}
elseif($PC->rcvG['action'] == 'modifContEnt') {
    aiJeLeDroit('contact', 15, 'web');
    loadPlugin(array('ZView/ContactView'));
    $view = new contactView();
    $dev = new contactParticulierModel();
    $data = $dev->getDataFromID($PC->rcvG['contact']);
    $data = $data[1][0];
    $data['listePays'] = $dev->getPays();
    $data['entreprise_cont'] = $PC->rcvG['entreprise'];
    $data['idRetour'] = $PC->rcvG['contact'];
    $data['from'] = 'contactEntreprise';
    echo $view->popupCont($data, '', '', 'modifContPopup');
    exit;
}
elseif($PC->rcvG['action'] == 'suppContEnt') {
    aiJeLeDroit('contact', 30, 'web');
    $dev = new contactParticulierModel();
    $data = $dev->getDataFromID($PC->rcvG['contact']);
    $data = $data[1][0];
    $view = new contactEntrepriseView();
    echo $view->confirmSupp($data);
    exit;
}
elseif($PC->rcvP['action'] == 'suppCont') {
    aiJeLeDroit('contact', 30, 'web');
    $dev = new contactParticulierModel();
    $result = $dev->delete($PC->rcvP['id_cont']);
    echo viewFiche($PC->rcvP['entreprise_cont'], 'contactEntreprise', 'listeCont', 'non', 'web', true, 'Contact supprimé');
    exit;
}
elseif($PC->rcvG['action'] == 'moveContEnt') {
    aiJeLeDroit('contact', 15, 'web');
    $dev = new contactParticulierModel();
    $data = $dev->getDataFromID($PC->rcvG['contact']);
    $data = $data[1][0];
    $view = new contactEntrepriseView();
    echo $view->moveContact($data);
    exit;
}
elseif($PC->rcvP['action'] == 'moveCont') {
    aiJeLeDroit('contact', 15, 'web');
    $dev = new contactParticulierModel();
    $result = $dev->update($PC->rcvP,$PC->rcvP['id_cont']);
    echo viewFiche($PC->rcvP['entrepriseC'], 'contactEntreprise', 'listeCont', 'non', 'web', true, 'Contact déplacé');
    exit;
}
elseif($PC->rcvG['action'] == 'appelCont') {
    $dev = new contactParticulierModel();
    $data = $dev->getDataFromID($PC->rcvG['contact']);
    $data = $data[1][0];
    $dev = new projetModel();
    $data['types'] = $dev->getTypesProj();
    $view = new contactEntrepriseView();
    echo $view->appelContact($data);
    exit;
}
elseif($PC->rcvP['action'] == 'addAppelCont') {
    if($PC->rcvP['contact_app'] != '') {
	$PC->rcvP['utilisateur_app'] = $_SESSION['user']['id'];
	if($PC->rcvP['rappel'] != "1") {
	    $PC->rcvP['rappel_app'] = null;
	    $PC->rcvP['heure_app'] = null;
	}
	$model = new appelModel();
	$result = $model->insert($PC->rcvP);
	echo viewFiche($PC->rcvP['id_ent'], 'contactEntreprise', 'all', 'non', 'web', true, 'Appel Enregistré');
	exit;
    }
    else {
	echo '<erreur>erreurPopupAppel</erreur><span class="important" style="text-align:center;">Il faut un contact !</span>';
	exit;
    }
}
elseif($PC->rcvG['action'] == 'addAppelEnt') {
    $view = new contactEntrepriseView();
    $dev = new projetModel();
    $sql = new contactEntrepriseModel();
    $res = $sql->getDataFromID($PC->rcvG['entreprise']);

    if(count($res[1][0]['contact']) < 5 and count($res[1][0]['contact'] > 0)) {
	$data['civ_def'] = $res[1][0]['contact'][0]['civ_cont'];
	$data['prenom_def'] = $res[1][0]['contact'][0]['prenom_cont'];
	$data['nom_def'] = $res[1][0]['contact'][0]['nom_cont'];
	$data['id_def'] = $res[1][0]['contact'][0]['id_cont'];
    }

    $data['nom_ent'] = $res[1][0]['nom_ent'];
    $data['types'] = $dev->getTypesProj();
    $data['id_ent'] = $PC->rcvG['entreprise'];
    echo $view->appelContact($data);
    exit;
}
elseif($PC->rcvG['action'] == 'modifAppelEnt') {
    $model = new appelModel();
    $appel = $model->getDataFromID($PC->rcvG['appel']);
    $appel = $appel[1][0];
    $appel['id_ent'] = $PC->rcvG['entreprise'];
    $model = new projetModel();
    $appel['types'] = $model->getTypesProj();
    $view = new contactEntrepriseView();
    echo $view->appelContact($appel);
    exit;


}
elseif($PC->rcvG['action'] == 'modifProjetEnt') {
    $dev = new projetModel();
    $data = $dev->getDataFromId($PC->rcvG['projet']);
    $data = $data[1][0];
    $data['types'] = $dev->getTypesProj();
    $data['id_ent'] = $PC->rcvG['entreprise'];
    $data['action'] = 'modifProj';
    $view = new contactEntrepriseView();
    echo $view->popupProjet($data);
    exit;
}
elseif($PC->rcvP['action'] == 'modifProj') {
    if($PC->rcvP['contact_proj'] != '') {
	$model = new projetModel();
	$result = $model->update($PC->rcvP, $PC->rcvP['id_proj']);
	$model = new contactEntrepriseModel();
	$datas['projet'] = $model->getProjets($PC->rcvP['id_ent']);
	$datas['affaire'] = $model->getAffaires($PC->rcvP['id_ent']);
	$view = new contactEntrepriseView();
	echo $view->view($datas, 'interneProjet', 'Projet Modifié');
	exit;
    }
    else {
	echo '<erreur>erreurPopupProj</erreur><span class="important" style="text-align:center;">Il faut un contact !</span>';
	exit;
    }
}
elseif($PC->rcvP['action'] == 'modifAppelCont') {
    if($PC->rcvP['contact_app'] != '') {
	$model = new appelModel();
	$result = $model->update($PC->rcvP, $PC->rcvP['id_app']);
	echo viewFiche($PC->rcvP['id_ent'], 'contactEntreprise', 'all', 'non', 'web', true, 'Appel Modifié');
	exit;
    }
    else {
	echo '<erreur>erreurPopupAppel</erreur><span class="important" style="text-align:center;">Il faut un contact !</span>';
	exit;
    }

}
elseif($PC->rcvG['action'] == 'suppAppelEnt') {
    $dev = new appelModel();
    $data = $dev->getDataFromID($PC->rcvG['appel']);
    $data = $data[1][0];
    $view = new contactEntrepriseView();
    echo $view->confirmSuppAppel($data);
    exit;
}
elseif($PC->rcvG['action'] == 'suppProjetEnt') {
    $dev = new projetModel();
    $data = $dev->getDataFromID($PC->rcvG['projet']);
    $data = $data[1][0];
    $view = new contactEntrepriseView();
    echo $view->confirmSuppProjet($data);
    exit;
}
elseif($PC->rcvP['action'] == 'suppProj') {
    $model = new projetModel();
    $result = $model->delete($PC->rcvP['id_proj']);
    $model = new contactEntrepriseModel();
    $datas['projet'] = $model->getProjets($PC->rcvP['entreprise_cont']);
    $datas['affaire'] = $model->getAffaires($PC->rcvP['entreprise_cont']);
    $view = new contactEntrepriseView();
    echo $view->view($datas, 'interneProjet', 'Projet Supprimé');
    exit;
}
elseif($PC->rcvP['action'] == 'suppApp') {
    $model = new appelModel();
    $result = $model->delete($PC->rcvP['id_app']);
    $model = new contactEntrepriseModel();
    $data = $model->getAppels($PC->rcvP['entreprise_cont']);
    $datas['appel'] = $data;
    $view = new contactEntrepriseView();
    echo $view->view($datas, 'interneAppel', 'Appel Supprimé');
    exit;
}
elseif($PC->rcvP['action'] == 'addProjAppel') {
    if($PC->rcvP['contact_proj'] != '') {
	$model = new projetModel();
	$result = $model->insert($PC->rcvP);
	$model = new contactEntrepriseModel();
	$datas['projet'] = $model->getProjets($PC->rcvP['id_ent']);
	$datas['affaire'] = $model->getAffaires($PC->rcvP['id_ent']);
	$view = new contactEntrepriseView();
	echo $view->view($datas, 'interneProjet', 'Projet enregistré');
	exit;
    }
    else {
	echo '<erreur>erreurPopupProj</erreur><span class="important" style="text-align:center;">Il faut un contact !</span>';
	exit;
    }
}
elseif($PC->rcvG['action'] == 'addProjetEnt') {
    $dev = new projetModel();
    $data['types'] = $dev->getTypesProj();
    $data['id_ent'] = $PC->rcvG['entreprise'];
    $data['action'] = 'addProj';
    $view = new contactEntrepriseView();
    echo $view->popupProjet($data);
    exit;
}
elseif($PC->rcvP['action'] == 'addProjet') {
    if($PC->rcvP['contact_proj'] != '') {
	$model = new projetModel();
	$result = $model->insert($PC->rcvP);
	$model = new contactEntrepriseModel();
	$datas['projet'] = $model->getProjets($PC->rcvP['id_ent']);
	$datas['affaire'] = $model->getAffaires($PC->rcvP['id_ent']);
	$view = new contactEntrepriseView();
	echo $view->view($datas, 'interneProjet', 'Projet enregistré');
	exit;
    }
    else {
	echo '<erreur>erreurPopupProj</erreur><span class="important" style="text-align:center;">Il faut un contact !</span>';
	exit;
    }
}
else {
    aiJeLeDroit('contact', 20, 'web');
    $info = new contactEntrepriseModel();
    $datas['pays'] =  $info->getPays();
    $datas['user'] = $info->getUser();
    $datas['type'] = $info->getTypesEnt();
    $datas['groupe'] = $info->getGroupesEnt();
    $datas['activite'] = $info->getActivitesEnt();
    $view = new contactEntrepriseView();
    $sortie = $view->creerEntreprise($datas);
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
