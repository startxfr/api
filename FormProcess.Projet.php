<?php
/*#########################################################################
#
#   name :       FormProcess.Projet.php
#   desc :       Prospection list for sxprospec
#   categorie :  prospec page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('inc/conf.inc');		// Declare global variables from config files
include ('inc/core.inc');		// Load core library
loadPlugin(array('ZunoCore','ZView/ProspecView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetVarContext();
$PC->GetChannelContext();
$PC->GetSessionContext();
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
if ($PC->rcvP['action'] == "modif") {
    $id_proj= $PC->rcvP['id_proj'];
    $id_cont= $PC->rcvP['contact_proj'];
    $action = $PC->rcvP['action'];
}
elseif ($PC->rcvP['action'] == "new") {
    $action = $PC->rcvP['action'];
    $id_cont= $PC->rcvP['contact_proj'];
}
elseif ($PC->rcvP['action'] == "newapp") {
    $action = $PC->rcvP['action'];
    $id_cont= $PC->rcvP['contact_proj'];
    $id_app = $PC->rcvP['appel_proj'];
}

if (isset($PC->rcvP['contact_proj'])) {
    $var_recv['contact_proj'] = $PC->rcvP['contact_proj'];
}
if ($PC->rcvP['actif_proj'] == '1') {
    $var_recv['actif_proj'] = '1';
}
else {
    $var_recv['actif_proj'] = '0';
}
if (isset($PC->rcvP['appel_proj'])) {
    $var_recv['appel_proj'] = $PC->rcvP['appel_proj'];
}
if (isset($PC->rcvP['titre_proj'])) {
    $var_recv['titre_proj'] = addslashes($PC->rcvP['titre_proj']);
}
if (isset($PC->rcvP['heure_proj'])) {
    $var_recv['heure_proj'] = $PC->rcvP['heure_proj'];
}
if ($PC->rcvP['echeance_proj'] == '') {
    $Decheance_proj = 'NULL';
}
else {
    $Decheance_proj = "'".DateHuman2Univ($PC->rcvP['echeance_proj'])."'";
}
if ($PC->rcvP['rdv_proj'] == '') {
    $Drdv_proj = 'NULL';
}
else {
    $Drdv_proj = "'".DateHuman2Univ($PC->rcvP['rdv_proj'])."'";
}
if (isset($PC->rcvP['budget_proj'])) {
    $var_recv['budget_proj'] = $PC->rcvP['budget_proj'];
}
if (isset($PC->rcvP['decid_proj'])) {
    $var_recv['decid_proj'] = $PC->rcvP['decid_proj'];
}
if (isset($PC->rcvP['rdvavec_proj'])) {
    $var_recv['rdvavec_proj'] = $PC->rcvP['rdvavec_proj'];
}
if (isset($PC->rcvP['SSII_proj'])) {
    $var_recv['SSII_proj'] = $PC->rcvP['SSII_proj'];
}
if (isset($PC->rcvP['SSLL_proj'])) {
    $var_recv['SSLL_proj'] = $PC->rcvP['SSLL_proj'];
}
if (isset($PC->rcvP['comm_proj'])) {
    $var_recv['comm_proj'] = addslashes($PC->rcvP['comm_proj']);
}
if (isset($PC->rcvP['desc_proj'])) {
    $var_recv['desc_proj'] = addslashes($PC->rcvP['desc_proj']);
}
if (isset($PC->rcvP['typeproj_proj'])) {
    $var_recv['typeproj_proj'] = $PC->rcvP['typeproj_proj'];
}

//############################################################################
//		RECEPTION DES VARABLES
//############################################################################
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$datedetect = date("Ymd");
if ($action == 'move') {
    $req = "SELECT * FROM contact WHERE id_cont = '".$id_cont."'";
    $bddtmp->makeRequeteFree($req);
    $res = $bddtmp->process();
    $cont = $res[0];
    $ent_cont_avant = $cont['entreprise_cont'];
    $req = "UPDATE contact SET entreprise_cont = ".$new_ent_cont." WHERE id_cont = '".$id_cont."';";
    $bddtmp->makeRequeteFree($req);
    $bddtmp->process();
    $ent_cont = $ent_cont_avant;
}
if ($action == 'modif') {
    if (($Drdv_proj == 'NULL')and
	    ($var_recv['rdvavec_proj'] != '')) {
	if ($var_recv['heure_proj'] != 0) {
	    $h = $var_recv['heure_proj'];
	}
	else {
	    $h = 9;
	}
	$req = "SELECT * FROM projet,contact,entreprise WHERE rdvavec_proj = id_cont AND entreprise_cont = id_ent AND id_proj = '".$id_proj."'";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	$infoRDV = $res[0];

	$info[1] = $infoRDV['nom_ent']." - ".$infoRDV['civ_cont']." ".$infoRDV['prenom_cont'].
		' '.$infoRDV['nom_cont'].' sujet: '.$infoRDV['titre_proj'];//titre
	$info[2] = '<b>description : </b>'.$var_recv['desc_proj'].'<br><b>Budget : </b>'.$var_recv['budget_proj'].'&euro;<br><b>Commentaire : </b>'.$var_recv['comm_proj'].'<br><br><b>+ dinfo : </b><a href="prospec/Contact.php?id_cont='.$infoRDV['id_cont'].'" target="_blank">fiche.php?id_cont'.$infoRDV['id_cont'].'</a>';//description
	$info[3] = $infoRDV['cp_ent']." - ".$infoRDV['ville_ent'];//lieu RDV
    }

    $req = "UPDATE projet SET ";
    foreach ($var_recv as $key => $value) {
	if ($value == '') {
	    $value = 'NULL';
	}
	else {
	    $value = "'".$value."'";
	}
	$req .= $key." = ".$value.", ";
    }
    $req .= "rdv_proj = ".$Drdv_proj.", ";
    $req .= "echeance_proj = ".$Decheance_proj.", ";
    $req .= "id_proj = '".$id_proj."' WHERE id_proj = '".$id_proj."'";
    $bddtmp->makeRequeteFree($req);
    $bddtmp->process();
}

if (($action == 'new')or($action == 'newadd')) {
    if ($Drdv_proj == 'NULL') {
	$ins_egw = 'NULL';
    }
    else {
	if ($var_recv['heure_proj'] != 0) {
	    $h = $var_recv['heure_proj'];
	}
	else {
	    $h = 9;
	}
	$info[0] = mktime ($h,0,0,substr($Drdv_proj,6,8),substr($Drdv_proj,4,6),substr($Drdv_proj,0,4));		//date RDV
	$req = "SELECT * FROM contact,entreprise WHERE ".$var_recv['rdvavec_proj']." = id_cont AND entreprise_cont = id_ent";
	$bddtmp->makeRequeteFree($req);
	$res = $bddtmp->process();
	$infoRDV = $res[0];

	$info[1] = $infoRDV['nom_ent'].' - '.$infoRDV['civ_cont'].' '.$infoRDV['prenom_cont'].' '.$infoRDV['nom_cont'].' sujet: '.$var_recv['titre_proj'];//titre
	$info[2] = '<b>description : </b>'.$var_recv['desc_proj'].'<br><b>Budget : </b>'.$var_recv['budget_proj'].'&euro;<br><b>Commentaire : </b>'.$var_recv['comm_proj'].'<br><br><b>+ info : </b><a href="prospec/Contact.php?id_cont='.$infoRDV['id_cont'].'" target="_blank">fiche.php?id_cont'.$infoRDV['id_cont']."</a>";//description
	$info[3] = $infoRDV['cp_ent']." - ".$infoRDV['ville_ent'];//lieu RDV
    }

    foreach ($var_recv as $key => $value) {
	$req_tete  .= $key.", ";
	$req_corps .= "'".$value."', ";
    }

    echo $req = "INSERT INTO projet (".$req_tete." id_proj, detect_proj, rdv_proj, echeance_proj) VALUES (".$req_corps." '', '".$datedetect."',".$Drdv_proj.",".$Decheance_proj.");";
    $bddtmp->makeRequeteFree($req);
    $bddtmp->process();
    echo $bddtmp->makeRequeteFree("SELECT id_proj FROM projet ORDER BY id_proj DESC LIMIT 1");
    $res = $bddtmp->process();
    $id_proj = $res[0]['id_app'];
    exit;
}

if ($popup == 'on') {
    echo "<HTML><HEAD>
		</HEAD><body onLoad=\"zuno.popup.close();\"></body>";
}
else {
    header("Location: ".$_SERVER["HTTP_REFERER"]);
    exit();
}
?>
