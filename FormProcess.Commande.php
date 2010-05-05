<?php
/*#########################################################################
#
#   name :       FormProcess.Commande.php
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
loadPlugin(array('ZunoCore','ZView/CommandeView','ZView/FactureView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext();
$PC->GetVarContext();
$PC->GetChannelContext();
$PC->GetSessionContext();
//print_r($PC);exit;
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
if ($PC->rcvG['id_cmd'] != "") {
    $id_cmd= $PC->rcvG['id_cmd'];
}
elseif ($PC->rcvP['id_cmd'] != "") {
    $id_cmd= $PC->rcvP['id_cmd'];
}

if ($PC->rcvG['action'] == "suppconfirm") {
    $action = $PC->rcvG['action'];
}
elseif (($PC->rcvP['action'] == "ModifCommande")or
	($PC->rcvP['action'] == "ValidBDCC")or
	($PC->rcvP['action'] == "GenererBDCC")or
	($PC->rcvP['action'] == "GenererBDL")or
	($PC->rcvP['action'] == "GenererRI")or
	($PC->rcvP['action'] == "VoirBDCC")or
	($PC->rcvP['action'] == "VoirBDL")or
	($PC->rcvP['action'] == "VoirRI")or
	($PC->rcvP['action'] == "Generer")or
	($PC->rcvP['action'] == "SendBDCF")or
	($PC->rcvP['action'] == "BDCF-AR")or
	($PC->rcvP['action'] == "BDCFValid")or
	($PC->rcvP['action'] == "CommandeExped")or
	($PC->rcvP['action'] == "CommandeValid")or
	($PC->rcvP['action'] == "ValidCMD")or
	($PC->rcvP['action'] == "CreateFact")) {
    $action = $PC->rcvP['action'];
}
elseif (substr($PC->rcvP['action'],0,8) == "VoirBDCF") {
    $action = "VoirBDCF";
    $id_fourn = substr($PC->rcvP['action'],9);
}
elseif (substr($PC->rcvP['action'],0,11) == "GenererBDCF") {
    $action = "GenererBDCF";
    $id_fourn = substr($PC->rcvP['action'],12);
}
elseif($PC->rcvP['action'] == "") {
    $action = $PC->rcvG['action'];
    $id_fourn = $PC->rcvG['fourn'];
}
$toInsertField = array ('affaire_cmd'=>'',
	'titre_cmd'=>'',
	'status_cmd'=>'',
	'commercial_cmd'=>'',
	'BDCclient_cmd'=>'',
	'modereglement_cmd'=>'',
	'condireglement_cmd'=>'',
	'entreprise_cmd'=>'',
	'contact_cmd'=>'',
	'contact_achat_cmd'=>'',
	'datemodif_cmd'=>'',
	'daterecord_cmd'=>'',
	'nomdelivery_cmd'=>'',
	'adressedelivery_cmd'=>'',
	'adresse1delivery_cmd'=>'',
	'villedelivery_cmd'=>'',
	'cpdelivery_cmd'=>'',
	'paysdelivery_cmd'=>'',
	'maildelivery_cmd'=>'',
	'complementdelivery_cmd'=>'');
if(is_array($PC->rcvP)) {
    foreach($PC->rcvP as $key => $val) {
	if(array_key_exists($key,$toInsertField)) {
	    $var_recv[$key] = $val;
	}
    }
}


$bddtmp = new CommandeModel();
$location = $_SERVER["HTTP_REFERER"];

// Traitement classique des commandes
if ($action == 'ValidBDCC') {
    $bddtmp->makeRequeteFree("SELECT * FROM commande WHERE  id_cmd = '".$id_cmd."'");
    $lignes = $bddtmp->process();
    $cmd = $lignes[0];
    $var_recv['status_cmd'] = "2";
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,$var_recv);
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','Validation du Bon de commande client '.$id_cmd.'C','Le bon de commande client '.$id_cmd.'C vient d\'étre validée.');
}
elseif ($action == 'GenererBDCC') {
    $Doc = commandeGnose::CommandeGenerateBDCC($id_cmd);
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
}
elseif ($action == 'GenererBDCF') {
    $Doc = commandeGnose::CommandeGenerateBDCF($id_cmd,$id_fourn);
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
}
elseif ($action == 'GenererBDL') {
    $Doc = commandeGnose::CommandeGenerateBDL($id_cmd);
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
}
elseif ($action == 'GenererRI') {
    $Doc = commandeGnose::CommandeGenerateRI($id_cmd);
    PushFileToBrowser($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp'].$Doc,$Doc);
}
elseif ($action == 'Generer') {
    $bddtmp->makeRequeteFree("SELECT id_fourn
				  FROM `commande_produit`, fournisseur
				  WHERE id_fourn = fournisseur AND `id_commande` = '".$id_cmd."'");
    $fournisseurs = $bddtmp->process();
    if (count($fournisseurs) > 0)
	foreach ($fournisseurs as $key => $fournisseur)
	    if($fournisseur['id_fourn'] != '')
		$ListeFournisseur[$fournisseur['id_fourn']] = $fournisseur['id_fourn'];

    if(count($ListeFournisseur) > 0)
	foreach ($ListeFournisseur as $idfourn => $val)
	    $Doc[] = commandeGnose::CommandeGenerateBDCF($id_cmd,$idfourn);

    if(count($PC->rcvP['GetDoc']) > 0)
	foreach ($PC->rcvP['GetDoc'] as $id => $actionDoc) {
	    if($actionDoc == "BDC") {
		$Doc[] = commandeGnose::CommandeGenerateBDCC($id_cmd);
		$txtDo .= ' bon de commande client, ';
	    }
	    if($actionDoc == "BDL") {
		$Doc[] = commandeGnose::CommandeGenerateBDL($id_cmd);
		$txtDo .= ' bon de livraison, ';
	    }
	    if($actionDoc == "RI") {
		$Doc[] = commandeGnose::CommandeGenerateRI($id_cmd);
		$txtDo .= ' rapport d\'intervention, ';
	    }
	}

    commandeGnose::CommandeSaveDocInGnose($Doc,$id_cmd,"Enregistrement des documents relatifs à une commande.");
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"3"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','Génération des documents de la commande '.$id_cmd,'Les documents'.substr($txtDo,0,-2).' de la commande '.$id_cmd.' ont été généré. Ils sont disponibles dans l\'entrepôt des documents.');
}
elseif ($action == 'VoirBDCC' or
	$action == 'VoirBDCF' or
	$action == 'VoirBDL' or
	$action == 'VoirRI') {
    $bddtmp->makeRequeteSelect('affaire','id_aff',substr($id_cmd,0,-5));
    $aff = $bddtmp->process();
    $aff = $aff[0];
    $PathTo  = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$aff['dir_aff'];
    if ($action == 'VoirBDCC')		$Doc = $id_cmd.'C.pdf';
    elseif ($action == 'VoirBDCF')	$Doc = $id_cmd.'F-'.$id_fourn.'.pdf';
    elseif ($action == 'VoirBDL')		$Doc = substr($id_cmd,0,-1).'L.pdf';
    elseif ($action == 'VoirRI')		$Doc = "RapportIntervention.".$id_cmd.'.pdf';
    PushFileToBrowser($PathTo.$Doc,$Doc);
}
elseif ($action == 'SendBDCF') {
    foreach($PC->rcvP as $key => $val)
	if((substr($key,0,4) == 'type')or
		(substr($key,0,4) == 'mail')or
		(substr($key,0,4) == 'titr')or
		(substr($key,0,4) == 'mess')or
		(substr($key,0,4) == 'faxx'))
	    $Type[substr($key,4)][substr($key,0,4)] = $val;

    $bddtmp->makeRequeteSelect('affaire','id_aff',substr($id_cmd,0,-5));
    $aff = $bddtmp->process();
    $aff = $aff[0];
    $PathTo  = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'].$aff['dir_aff'];

    if(count($Type) > 1) $actuDesc = 'Envoi du bon de commande ';
    else			   $actuDesc = 'Envoi des bons de commandes ';

    foreach($Type as $fournisseur => $val) {
	if($val['type'] == "mail") {
	    MailAttach($val['mail'],
		    $val['mess'],
		    $PathTo.$id_cmd.'F-'.$fournisseur.'.pdf',
		    '',
		    '',
		    $val['titr'],
		    $_SESSION['user']['mail']);
	    $actuDesc .= $id_cmd.'F-'.$fournisseur.' par messagerie ï¿½lï¿½ctronique vers l\'adresse '.$val['mail'].', ';
	}
	elseif($val['type'] == "fax") {
	    // mettre ici le plugin pour envoi par fax
	    $actuDesc .= $id_cmd.'F-'.$fournisseur.' par fax vers le numï¿½ro '.$val['fax'].', ';
	}
	elseif($val['type'] == "soap") {
	    // mettre ici le plugin pour envoi par EDI/soap
	    $actuDesc .= $id_cmd.'F-'.$fournisseur.' par EDI (soap), ';
	}
    }
    $actuDesc = substr($actuDesc,0,-2);

    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"4"));
    $bddtmp->process();
    $bddtmp->makeRequeteUpdate('affaire',"id_aff",substr($id_cmd,0,-5),array("status_aff"=>"9"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','Envoi du Bon de commande Fournisseur '.$id_cmd,$actuDesc);
}
elseif ($action == 'BDCF-AR') {
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"5"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','Commande '.$id_cmd.' en cours de traitement','Les bon de commandes fournisseurs ont bien été réceptionnés par les fournisseurs');
}
elseif ($action == 'BDCFValid') {
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"6"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','Commande '.$id_cmd.' en cours de traitement','Les bons de commandes fournisseurs viennent d\'être validés.');
}
elseif ($action == 'CommandeExped') {
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"7"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','Commande '.$id_cmd.' expédiée par le fournisseur','La commande viens d\'être éxpédiée par le fournisseur.');
}
elseif ($action == 'CommandeValid') {
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"7"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','Commande '.$id_cmd.' traitée et expédiée.','La commande viens d\'être validé. Elle est maintenant considérée comme récéptionnée par le fournisseur, et livrée au client.');
}
elseif ($action == 'ValidCMD') {
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"8"));
    $bddtmp->process();
    // On laisse une trace dans l'histo de l'affaire
    $bddtmp->makeRequeteUpdate('affaire',"id_aff",substr($id_cmd,0,-5),array("status_aff"=>"11"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','La commande viens d\'être expédiée par le fournisseur.','Commande '.$id_cmd.' expédiée par le fournisseur');
}
elseif ($action == 'CreateFact') {
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,$var_recv);
    $bddtmp->process();
    // On recupére toutes les données de la commande
    $bddtmp->makeRequeteFree("SELECT * FROM entreprise,commande WHERE entreprise_cmd = id_ent AND id_cmd = '".$id_cmd."'");
    $res = $bddtmp->process();
    $toTransfert = array ('id_cmd'=>'commande_fact',
	    'titre_cmd'=>'titre_fact',
	    'BDCclient_cmd'=>'BDCclient_fact',
	    'entreprise_cmd'=>'entreprise_fact',
	    'contact_cmd'=>'contact_fact',
	    'sommeHT_cmd'=>'sommeHT_fact',
	    'contact_achat_cmd'=>'contact_achat_fact',
	    'modereglement_cmd'=>'modereglement_fact',
	    'tauxTVA_ent'=>'tauxTVA_fact',
	    'nom_ent'=>'nomentreprise_fact',
	    'add1_ent'=>'add1_fact',
	    'add2_ent'=>'add2_fact',
	    'cp_ent'=>'cp_fact',
	    'ville_ent'=>'ville_fact',
	    'pays_ent'=>'pays_fact',
	    'SIRET_ent'=>'numeroTVA_fact');
    //On mets a jour les info sur le mode et condi de rglement

    if (count($res) > 0) {
	$var_recv = $res[0];
	foreach($toTransfert as $key => $val)
	    if(array_key_exists($key,$res[0]))
		$varCmd[$val] = addslashes($res[0][$key]);
	$varCmd['id_fact'] = factureModel::FactureGenerateID($id_cmd);
	$varCmd['status_fact'] = "1";
	$varCmd['commercial_fact'] = $_SESSION['user']['id'];
	$varCmd['daterecord_fact'] = DateTimestamp2Univ('');
	// on insert la facture
	$bddtmp->makeRequeteInsert('facture',$varCmd);
	$bddtmp->process();
	// On copie les ligne du devis
	$bddtmp->makeRequeteFree("SELECT * FROM devis_produit WHERE id_devis = '".strtr($id_cmd,0,9)."'");
	$res = $bddtmp->process();
	if (count($res) > 0) {
	    foreach($res as $key1 => $produit) {
		unset($produit['id_devis']);
		$produit1['id_facture'] = $varCmd['id_fact'];
		$produit1['desc'] = addslashes($produit['desc']);
		$produit1['quantite'] = addslashes($produit['quantite']);
		$produit1['remise'] = addslashes($produit['remise']);
		$produit1['prix'] = addslashes($produit['prix']);
		$produit1['id_produit'] = $produit['id_produit'];
		// on insert les produits
		$bddtmp->makeRequeteInsert('facture_produit',$produit1);
		$bddtmp->process();
	    }
	}

	// On change la commande
	$bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"9"));
	$bddtmp->process();
	// on créé l'historique de status de la facture
	$bddfact = new FactureModel();
	$bddfact->addActualite($varCmd['id_fact'], 'free','Création de la facture '.$varCmd['id_fact'],'La facture '.$varCmd['id_fact'].' vient d\'étre créée à partir de la commande '.$varCmd['commande_fact']);
	$location = "facturier/Facture.php?id_fact=".$varCmd['id_fact'];
    }
}
elseif ($action == 'ModifCommande') {
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,$var_recv);
    $bddtmp->process();
}



if ($action == 'suppconfirm') {
    $bddtmp->makeRequeteUpdate('commande',"id_cmd",$id_cmd,array("status_cmd"=>"2"));
    $bddtmp->process();
    $bddtmp->addActualite($id_cmd, 'free','Commande '.$id_cmd.' supprimée','La commande viens d\'étre supprimée.');
    $out = new PageDisplayHeader();
    echo $out->Process();
    echo "<script language=\"javascript\">window.location.reload();zuno.popup.close();</script>";
    exit;
}


header("Location: ".$location);
exit();

?>
