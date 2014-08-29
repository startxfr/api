<?php
/*#########################################################################
#
#   name :       JournalEcriture.php
#   desc :       Display page content
#   categorie :  journalBanque
#   ID :  	 $Id: JournalEcriture.php 2814 2009-06-29 14:54:25Z nm $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/JournalBanqueView', 'ZunoRenduHTML'));
loadPlugin(array('ZControl/GeneralControl'));
loadPlugin(array('ZControl/DevisControl'));

// Whe get the page context
$PC = new PageContext('facturier');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
//var_dump($PC);exit;
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$sql = new JournalBanqueModel();

if($PC->rcvP['action'] == 'modifJournalEcriture') {
    aiJeLeDroit('journalBanque', 15, 'web');
    $fileHas2beUpdated = false;
    $message= "";
    $PC->rcvP['id_jb'] = $PC->rcvP['idJournalBanque'];

    if($PC->rcvP['entreprise_jb'] == '')
	$message.= "Vous devez associer un client à votre écriture<br/>";
    if($PC->rcvP['montant_jb'] == '')
	$message.= "Vous devez saisir un montant<br/>";
    else $PC->rcvP['montant_jb'] = ($PC->rcvP['sens'] == 'D') ? ($PC->rcvP['montant_jb']*-1) : $PC->rcvP['montant_jb'];
    if(is_array($PC->rcvF['file_jb']) and $PC->rcvF['file_jb']['error'] == 0 and is_file($PC->rcvF['file_jb']['tmp_name'])) {
	$fileHas2beUpdated = true;
	$file2update = $PC->rcvF['file_jb']['tmp_name'];
    }
    if(trim($PC->rcvP['libelle_jb']) == '') {
	$nom = ($PC->rcvP['montant_jb'] < 0) ? 'Débit' : 'Crédit';
	$nom.= ' de '.abs($PC->rcvP['montant_jb']).' euros';
	if($PC->rcvP['date_effet_jb'] != '')
	    $nom.= ' enregistré le '.$PC->rcvP['date_effet_jb'];
	$PC->rcvP['libelle_jb'] = $nom;
    }
    if($PC->rcvP['date_record_jb'] != '')
	$PC->rcvP['date_record_jb'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['date_record_jb']));
    if($PC->rcvP['date_effet_jb'] != '')
	$PC->rcvP['date_effet_jb'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['date_effet_jb']));

    if($message == '') {
	$rs = $sql->updateJournalEcriture($PC->rcvP, $PC->rcvP['idJournalBanque'],$fileHas2beUpdated,$file2update);
	$sortie = '<span class="importantgreen">Votre écriture vient d\'être modifiée</span>';
    }
    else $sortie.= '<span class="important">'.$message.'</span>';
    $sortie.= viewFiche($PC->rcvP['id_jb'], 'journalBanque', '', 'non', 'web');
}
elseif($PC->rcvP['action'] == 'addJournalEcriture') {
    aiJeLeDroit('journalBanque', 20, 'web');
    $message= "";

    if($PC->rcvP['entreprise_jb'] == '')
	$message.= "Vous devez associer un client à votre écriture<br/>";
    if($PC->rcvP['montant_jb'] == '')
	$message.= "Vous devez saisir un montant<br/>";
    else $PC->rcvP['montant_jb'] = ($PC->rcvP['sens'] == 'D') ? ($PC->rcvP['montant_jb']*-1) : $PC->rcvP['montant_jb'];

    if($message == '') {
	if(trim($PC->rcvP['libelle_jb']) == '') {
	    $nom = ($PC->rcvP['montant_jb'] < 0) ? 'Débit' : 'Crédit';
	    $nom.= ' de '.abs($PC->rcvP['montant_jb']).' euros';
	    if($PC->rcvP['date_effet_jb'] != '')
		$nom.= ' enregistré le '.$PC->rcvP['date_effet_jb'];
	    $PC->rcvP['libelle_jb'] = $nom;
	}
	if($PC->rcvP['date_record_jb'] != '')
	    $PC->rcvP['date_record_jb'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['date_record_jb']));
	if($PC->rcvP['date_effet_jb'] != '')
	    $PC->rcvP['date_effet_jb'] = DateTimestamp2Univ(DateHuman2Timestamp($PC->rcvP['date_effet_jb']));
	$rs = $sql->insertJournalEcriture($PC->rcvP);
	if($rs[0]) {
	    echo '<span class="importantgreen">Votre écriture vient d\'être enregistrée</span>';
	    echo '<redirection style="display:none">../facturier/JournalBanque.php</redirection>';
	    exit;
	}
    }
    echo '<span class="important">'.$message.'</span>';
    $view = new JournalBanqueView();
    echo $view->creer($PC->rcvP);
    exit;
}
elseif($PC->rcvG['action'] == 'get' and $PC->rcvG['id_jb'] != '') {
    aiJeLeDroit('journalBanque', 10, 'web');
    $data = $sql->getJournalEcritureByID($PC->rcvG['id_jb']);
    PushFileToBrowser($GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['zunoJournalBanque']['pieceDir'].$data['file_jb']);
    exit;
}
elseif($PC->rcvG['id_jb'] != '') {
    $sortie = viewFiche($PC->rcvG['id_jb'], 'journalBanque', '', 'non', 'web');
}
else {
    aiJeLeDroit('journalBanque', 20, 'web');
    $view = new JournalBanqueView();
    $sortie = $view->creer(array());
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
