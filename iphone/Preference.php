<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');
$GLOBALS['LOG']['DisplayDebug'] =
$GLOBALS['LOG']['DisplayError'] = false;

// On inclus les librairies speciales iPhone
include_once ('lib/Debug.inc.php');
include_once ('lib/ZunoLayerGeneral.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('lib/HtmlForm.inc.php');
include_once ('lib/ZunoLayerPreference.inc.php');
include_once ('V/GeneralView.inc.php');

// On lance la bufferisation de sortie et les entetes qui vont bien
ob_start();
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>';

// On recupère les informations sur le contexte de la page
// (channel, variables d'entrée, session)
$PC = new PageContext('iPhone');
$PC->GetVarContext();
$PC->GetChannelContext();
// Contrôle de la session et de sa validité
if($PC->GetSessionContext('',false) === false) {
    echo HtmlElementIphone::redirectOnSessionEnd();
    ob_end_flush();
    exit;
}


$footer = HtmlElementIphone::footer();
// MENU PREFERENCES
if($PC->rcvG['action'] == 'disconnect') {
    $uid = $_SESSION['user']['id'];
    $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $sqlConn->makeRequeteFree("DELETE FROM user_iphoneConfig WHERE user = '".$uid."'");
    $sqlConn->process2();
    if(array_key_exists('user',$_SESSION) and
	    array_key_exists('config',$_SESSION['user']))
	foreach($_SESSION['user']['config'] as $k => $v) {
	    $sqlConn->makeRequeteInsert('user_iphoneConfig',array('user'=>$uid,'key'=>$k,'val'=>$v));
	    $sqlConn->process2();
	}
    $Session = new Session();
    $Session->CatchSession();
    $Session->Deconnect();
    ?>
<root><go to="waHome"/></root>
    <?php
}
elseif($PC->rcvG['action'] == 'profil') {
    placementAffichage ('Profil', "waMenuPrefsProfil", 'ZunoLayerPreference::partProfil', array(), '', 'replace');
}
elseif($PC->rcvG['action'] == 'session') {
    placementAffichage ('Session', "waMenuPrefsSession", 'ZunoLayerPreference::partSession', array(), '', 'replace');
}
elseif($PC->rcvG['action'] == 'serveur') {
    placementAffichage ('Serveur', "waMenuPrefsServeur", 'ZunoLayerPreference::partServeur', array(), '', 'replace');
}
elseif($PC->rcvG['action'] == 'debug') {
    placementAffichage ('Debug', "waMenuPrefsDebug", 'ZunoLayerPreference::partDebug', array(), '', 'replace');
}
elseif($PC->rcvG['action'] == 'recordProfil') {
    aiJeLeDroit('preference', 90);
    if($PC->rcvP['LenghtSearchGeneral'] != '') $_SESSION['user']['config']['LenghtSearchGeneral'] = $PC->rcvP['LenghtSearchGeneral'];
    if($PC->rcvP['LenghtSearchActualite'] != '') $_SESSION['user']['config']['LenghtSearchActualite'] = $PC->rcvP['LenghtSearchActualite'];
    if($PC->rcvP['LenghtSearchContactEnt'] != '') $_SESSION['user']['config']['LenghtSearchContactEnt'] = $PC->rcvP['LenghtSearchContactEnt'];
    if($PC->rcvP['LenghtSearchContactPart'] != '') $_SESSION['user']['config']['LenghtSearchContactPart'] = $PC->rcvP['LenghtSearchContactPart'];
    if($PC->rcvP['LenghtSearchAffaire'] != '') $_SESSION['user']['config']['LenghtSearchAffaire'] = $PC->rcvP['LenghtSearchAffaire'];
    if($PC->rcvP['LenghtSearchDevis'] != '') $_SESSION['user']['config']['LenghtSearchDevis'] = $PC->rcvP['LenghtSearchDevis'];
    if($PC->rcvP['LenghtSearchCommande'] != '') $_SESSION['user']['config']['LenghtSearchCommande'] = $PC->rcvP['LenghtSearchCommande'];
    if($PC->rcvP['LenghtSearchFacture'] != '') $_SESSION['user']['config']['LenghtSearchFacture'] = $PC->rcvP['LenghtSearchFacture'];
    if($PC->rcvP['LenghtSearchProduit'] != '') $_SESSION['user']['config']['LenghtSearchProduit'] = $PC->rcvP['LenghtSearchProduit'];
    $_SESSION['user']['config']['autocorrect'] = ($PC->rcvP['autocorrect'] == 'ok') ? 'ok' : 'non';
    $_SESSION['user']['config']['autocapitalize'] = ($PC->rcvP['majauto'] == 'ok') ? 'ok' : 'non';
    $uid = $_SESSION['user']['id'];
    $sqlConn = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $sqlConn->makeRequeteFree("DELETE FROM user_iphoneConfig WHERE user = '".$uid."'");
    $sqlConn->process2();
    foreach($_SESSION['user']['config'] as $k => $v) {
	$sqlConn->makeRequeteInsert('user_iphoneConfig',array('user'=>$uid,'key'=>$k,'val'=>$v));
	$sqlConn->process2();
    }
    if($PC->rcvP['login'] != '') {
	$_SESSION['user']['id'] = $PC->rcvP['login'];
	$data['login'] = $PC->rcvP['login'];
    }
    if($PC->rcvP['nom'] != '') {
	$_SESSION['user']['nom'] = $PC->rcvP['nom'];
	$data['nom'] = $PC->rcvP['nom'];
    }
    if($PC->rcvP['prenom'] != '') {
	$data['prenom'] = $PC->rcvP['prenom'];
	$_SESSION['user']['prenom'] = $PC->rcvP['prenom'];
    }
    if($PC->rcvP['actif'] == 'ok') {
	$data['actif'] = '1';
    }
    else {
	$data['actif'] = '0';
    }
    $sqlConn->makeRequeteUpdate('user', 'login', $uid, $data);
    $sqlConn->process2();
    ?>
<root>
    <destination mode="replace" zone="formProfilResult"/>
    <data><![CDATA[ <div class="msg">Enregistrement de la configuration de votre profil <strong>réalisé</strong></div> ]]></data>
</root>
    <?php
}
elseif($PC->rcvG['action'] == 'CP') {
    placementAffichage ('Code Postal', "waToolboxCP", 'generalView::CPToolbox', array(), '', 'replace');
}
elseif($PC->rcvG['action'] == 'validFormCP') {
    $sqlConn = new Bdd($GLOBALS['referenceBase']['dbPool']);
    if(is_numeric($PC->rcvP['valeur']))
	$sqlConn->makeRequeteFree("select distinct CP, Ville, Pays from code_postal where CP ='".$PC->rcvP['valeur']."' order by Ville, CP  ;");
    else	$sqlConn->makeRequeteFree("select distinct CP, Ville, Pays from code_postal where Ville like '%".$PC->rcvP['valeur']."%' order by Ville, CP ;");
    $temp = $sqlConn->process2();
    $data=$temp[1];
    placementAffichage ('', "resultToolboxCP", 'generalView::ResultCPToolbox', array($data), '', 'replace');
}
elseif($PC->rcvG['action'] == 'TVA') {
    placementAffichage ('Calcul TVA', "waToolboxTVA", 'generalView::TVAToolbox', array(), '', 'replace');
}
elseif($PC->rcvG['action'] == 'validFormTVA') {
    $ht = ($PC->rcvP['ht'] != '') ? str_replace(',', '.', $PC->rcvP['ht']) : 'sans';
    $ttc = ($PC->rcvP['ttc'] != '') ? str_replace(',', '.', $PC->rcvP['ttc']) : 'sans';
    if(!is_numeric($ttc)) {
	$result = $ht*(1+$PC->rcvP['tva']);
	$parametres = array('Prix TTC : ', formatCurencyDisplay($result,2,''), ' &euro;');
    }
    elseif(!is_numeric($ht)) {
	$result = $ttc/(1+$PC->rcvP['tva']);
	$parametres = array('Prix HT : ', formatCurencyDisplay($result,2,''), ' &euro;');
    }
    else {
	$result = ($ttc/$ht-1)*100;
	$parametres = array('TVA : ', formatCurencyDisplay($result,1,'%'), ' %');
    }
    placementAffichage ('', "resultToolboxTVA", 'generalView::ResultTVAToolbox', $parametres, '', 'replace');
}
else {	?>
<root>
    <go to="waMainMenuPref"/>
</root>
    <?php
}

ob_end_flush();
?>