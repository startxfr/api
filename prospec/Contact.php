<?php
/*#########################################################################
#
#   name :       Contact.php
#   desc :       Display page content
#   categorie :  prospec
#   ID :  	 $Id: Contact.php 2814 2009-06-29 14:54:25Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/ProspecView','ZView/ContactView'));
loadPlugin(array('ZControl/ContactControl'));
loadPlugin(array('ZControl/GeneralControl'));

// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if($PC->rcvP['action'] == 'addContPopup' or $PC->rcvP['action'] == 'modifContPopup') {
    if($PC->rcvP['action'] == 'addContPopup')
	aiJeLeDroit('contact', 20, 'web');
    else
	aiJeLeDroit('contact', 15, 'web');
    $view = new contactView();
    $PC->rcvP['tel_cont'] = prepareTelTraitement($PC->rcvP['tel_cont']);
    $PC->rcvP['mob_cont'] = prepareTelTraitement($PC->rcvP['mob_cont']);
    $PC->rcvP['fax_cont'] = prepareTelTraitement($PC->rcvP['fax_cont']);
    $PC->rcvP['nom_cont'] = ucfirst($PC->rcvP['nom_cont']);

    if($PC->rcvP['nom_cont'] == '') {
	echo '<erreur>popupAddCont</erreur>'.$view->popupCont($PC->rcvP, 'erreurCont', 'Le nom du contact est vide !', $PC->rcvP['action']);
	exit;
    }
    elseif($PC->rcvP['mail_cont'] != '' and !generalControl::mailControl($PC->rcvP['mail_cont'])) {
	echo '<erreur>popupAddCont</erreur>'.$view->popupCont($PC->rcvP, 'erreurCont', 'Le mail entré est invalide.', $PC->rcvP['action']);
	exit;
    }
    elseif($PC->rcvP['tel_cont'] != '' and !generalControl::telephoneControl($PC->rcvP['tel_cont'])) {
	echo '<erreur>popupAddCont</erreur>'.$view->popupCont($PC->rcvP, 'erreurCont', 'Le numéro de téléphone est invalide', $PC->rcvP['action']);
	exit;
    }
    elseif($PC->rcvP['mob_cont'] != '' and !generalControl::telephoneControl($PC->rcvP['mob_cont'])) {
	echo '<erreur>popupAddCont</erreur>'.$view->popupCont($PC->rcvP, 'erreurCont', 'Le numéro de portable est invalide', $PC->rcvP['action']);
	exit;
    }
    elseif($PC->rcvP['cp_cont'] != '' and !generalControl::codePostalControl($PC->rcvP['cp_cont'])) {
	echo '<erreur>popupAddCont</erreur>'.$view->popupCont($PC->rcvP, 'erreurCont', 'Le code postal est invalide', $PC->rcvP['action']);
	exit;
    }
    $sql = new contactParticulierModel();
    if($PC->rcvP['action'] == 'modifContPopup')
	$result = $sql->update($PC->rcvP, $PC->rcvP['retour']);
    else
	$result = $sql->insert($PC->rcvP);
    if(!$result) {
	echo $view->popupCont($PC->rcvP, 'erreurCont', 'L\' insertion a échoué');
	exit;
    }
    else {
	if($PC->rcvP['from'] == 'contactEntreprise') {
	    echo viewFiche($PC->rcvP['entreprise_cont'], 'contactEntreprise', 'listeCont', 'non', 'web', true, 'Contact Enregistré');
	}
	else {
	    if($PC->rcvP['idChamp'] != "")
		$js = '$(\''.$PC->rcvP['idChamp'].'\').value = \''.$PC->rcvP['civ_cont'].' '.$PC->rcvP['prenom_cont'].' '.$PC->rcvP['nom_cont'].'\';
                $(\''.$PC->rcvP['idChamp'].'hidden\').value = \''.$sql->getLastId().'\';';
	    $js .='zuno.popup.close();';
	    echo '<img src="'.getStaticUrl('img').'ajax-loader.gif" onload="'.$js.'" alt="loader" />';
	}

	exit;
    }
}
elseif($PC->rcvP['action'] == 'addEntPopup') {
    aiJeLeDroit('contact', 20, 'web');
    $PC->rcvP['tel_ent'] = prepareTelTraitement($PC->rcvP['tel_ent']);
    $PC->rcvP['telsi_ent'] = prepareTelTraitement($PC->rcvP['telsi_ent']);
    $PC->rcvP['fax_ent'] = prepareTelTraitement($PC->rcvP['fax_ent']);
    $view = new contactView();
    $sql = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $sql->makeRequeteFree("SELECT * FROM ref_typeentreprise ");
    $result = $sql->process2();
    if(is_array($result[1])) {
	foreach($result[1] as $v) {
	    $PC->rcvP['types'][$v['id_tyent']] = $v['nom_tyent'];
	}
    }
    if($PC->rcvP['nom_ent'] == '') {
	echo $view->popupEnt($PC->rcvP, 'erreurCont', 'Le nom de l\'entreprise est vide !');
	exit;
    }
    elseif($PC->rcvP['tel_ent'] != '' and !generalControl::telephoneControl($PC->rcvP['tel_ent'])) {
	echo $view->popupEnt($PC->rcvP, 'erreurCont', 'Le numéro de téléphone est invalide');
	exit;
    }
    elseif($PC->rcvP['fax_ent'] != '' and !generalControl::telephoneControl($PC->rcvP['fax_ent'])) {
	echo $view->popupEnt($PC->rcvP, 'erreurCont', 'Le numéro de portable est invalide');
	exit;
    }
    elseif($PC->rcvP['cp_ent'] != '' and !generalControl::codePostalControl($PC->rcvP['cp_ent'])) {
	echo $view->popupEnt($PC->rcvP, 'erreurCont', 'Le code postal est invalide');
	exit;
    }
    $sql = new contactEntrepriseModel();
    $result = $sql->insert($PC->rcvP);
    if(!$result) {
	echo $view->popupEnt($PC->rcvP, 'erreurEnt', 'L\' insertion a échoué');
	exit;
    }
    else {
	$id = $sql->getLastId();
	//echo '<script type="text/javascript">$(\''.$PC->rcvP['retour'].'\').value = \''.$PC->rcvP['prenom_cont'].' '.$PC->rcvP['nom_cont'].'\'; $(\''.$PC->rcvP['retour'].'hidden\').value=\''.$id.'\'; zuno.popup.close();</script>';
	//echo $view->popupCont($PC->rcvP, 'erreurCont', '<img src="'.getStaticUrl('img').'ajax-loader.gif" onload="return function() { $(\''.$PC->rcvP['retour'].'\').value = \''.$PC->rcvP['prenom_cont'].' '.$PC->rcvP['nom_cont'].'\'; $(\''.$PC->rcvP['retour'].'hidden\').value=\''.$id.'\'; return zuno.popup.close(); } " alt="loader" />');
	echo $view->popupEnt($PC->rcvP, 'erreurEnt', '<img src="'.getStaticUrl('img').'ajax-loader.gif" onload="zuno.popup.close(); " alt="loader" />');
	exit;
    }
}
elseif($PC->rcvG['id_cont'] != '') {
    $sortie = viewFiche($PC->rcvG['id_cont'], 'contactParticulier', '', 'non', 'web', true);
}
elseif($PC->rcvP['action'] == 'modifCont') {
    aiJeLeDroit('contact', 15, 'web');
    $PC->rcvP['tel_cont'] = prepareTelTraitement($PC->rcvP['tel_cont']);
    $PC->rcvP['mob_cont'] = prepareTelTraitement($PC->rcvP['mob_cont']);
    $PC->rcvP['fax_cont'] = prepareTelTraitement($PC->rcvP['fax_cont']);

    echo updateBDD($PC->rcvP['id_cont'], 'contactParticulier', $PC->rcvP, 'infosContact', 'web', true);
    exit;
}
elseif($PC->rcvP['action'] == 'creerCont') {
    aiJeLeDroit('contact', 20, 'web');
    $PC->rcvP['tel_cont'] = prepareTelTraitement($PC->rcvP['tel_cont']);
    $PC->rcvP['mob_cont'] = prepareTelTraitement($PC->rcvP['mob_cont']);
    $PC->rcvP['fax_cont'] = prepareTelTraitement($PC->rcvP['fax_cont']);

    unset($PC->rcvP['id_cont']);
    echo insertBDD('contactParticulier', $PC->rcvP, 'all', 'web', true);
    exit;
}
else {
    aiJeLeDroit('contact', 20, 'web');
    $info = new contactParticulierModel();
    $datas['fonctions'] = $info->getFonction();
    $datas['pays'] =  $info->getPays();
    $datas['user'] = $info->getUser();
    $view = new contactParticulierView();
    $sortie = $view->creerContact($datas);
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
