<?php
/*#########################################################################
#
#   name :       MyDesk.php
#   desc :       enter Gnose personal main page
#   categorie :  page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

// AATTEENNTTIIOONN changer ca pour les appels réalisés a la ligne de commande
//chdir('/var/www/html/zuno/manage/');
/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load required plugins library
loadPlugin(array("Send/Send", "ZModels/TokenModel", "ZModels/RenouvellementModel", "ZModels/TransactionModel"));

/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/
$PC = new PageContextVar();
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

$tok = new TokenModel();

$outRapport = "Tache cron démarre\n";
$jour = date("d");
$mois = date("m");
$annee = date("Y");
$today = DateUniv2Human(DateTimestamp2Univ(mktime(0,0,0,$mois,$jour,$annee)),'univSimple');
$tomorrow = DateUniv2Human(DateTimestamp2Univ(mktime(0,0,0,$mois,$jour+1,$annee)),'univSimple');

if(date('w') != 5)
    $aAAT = DateUniv2Human(DateTimestamp2Univ(mktime(0,0,0,$mois,$jour+2,$annee)),'univSimple');
else $aAAT = DateUniv2Human(DateTimestamp2Univ(mktime(0,0,0,$mois,$jour+4,$annee)),'univSimple');

$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree("SELECT contact_app
				  FROM appel,contact
				  WHERE contact_app = id_cont
				  AND relactive_cont = 1
				  AND rappel_app = $today
				  GROUP BY contact_app
				  ORDER BY `rappel_app` ASC");
$res = $bddtmp->process();

if (count($res) > 0) {
    $outVar['rappel_app'] = $tomorrow;
    $outVar['relactive_app'] = "1";
    // pour chaque contact ayant une date de relance a aujourd'hui
    $i = 0;
    foreach ($res as $key => $appel) {
	// On récupére le dernier appel
	$listeContactRelance = substr($listeContactRelanceTmp,0,-2);
	$bddtmp->makeRequeteFree("SELECT id_app, rappel_app
						  FROM appel
						  WHERE contact_app = ".$appel['contact_app']."
						  ORDER BY `appel_app` DESC LIMIT 1");
	$res1 = $bddtmp->process();
	$lastAppel = $res1[0];

	$UnivRappel =   $lastAppel['rappel_app']{0}.$lastAppel['rappel_app']{1}.$lastAppel['rappel_app']{2}.$lastAppel['rappel_app']{3}.
		$lastAppel['rappel_app']{5}.$lastAppel['rappel_app']{6}.
		$lastAppel['rappel_app']{8}.$lastAppel['rappel_app']{9};
	if($UnivRappel == $today) {
	    $bddtmp->makeRequeteUpdate("appel",'id_app',$lastAppel['id_app'],$outVar);
	    $bddtmp->process();
	    $i++;
	}
    }
    $outRapport .= $i." relance d'appel reporte au lendemain \n";
}
$outRapport .= "Traitement relance terminé\n";

if(date('w') != 0 and date('w') != 6) {
    $bddtmp->makeRequeteFree("select id_factfourn, titre_factfourn, desc_factfourn, datePaye_factfourn, montantHT_factfourn, montantTVA_factfourn, fichier_factfourn, nom_ent, cp_ent, ville_ent
                                from facture_fournisseur ff
                                left join entreprise e on e.id_ent = ff.entreprise_factfourn
                                where status_factfourn < 4
                                and datePaye_factfourn <= '".$aAAT."' ");
    $res = $bddtmp->process2();

    if($res[0]) {
	$bddtmp->makeRequeteFree("Select mail, prenom, user.login from user
                            left join user_droits on user_droits.login = user.login
                            where user_droits.droit = '1880' ");
	$mails = $bddtmp->process2();

	if($mails[0] and count($mails[1]) != 0) {
	    $data['id'] = 0;
	    $data['partie'] = 'factureFournisseur';
	    $data['typeE'] = 'mail';
	    $data['sujet'] = 'Zuno : Rappel sur les factures à payer';
	    $data['expediteur'] = 'Zuno';
	    $data['from'] = 'manager@zuno.fr <instance '.$GLOBALS['zunoWebService']['instance_code'].'>';
	    $data['typeEmail'] = 'html';
	    $data['mail'] = $mails[1][0]['mail'];
	    $data['bug'] = true;
	    $mail = new Sender($data);
	    $compteur = 0;
	    $pj = 0;
	    foreach($res[1] as $v) {
		$ttc = prepareNombreAffichage($v['montantHT_factfourn']+$v['montantTVA_factfourn']);
		foreach($mails[1] as $z) {
		    $mail->setMail($z['mail']);
		    $resultat = $tok->insert($z['login'], "/facturier/FactureFournisseur.php?id_factfourn=".$v['id_factfourn']);
		    if($v['fichier_factfourn'] != '') {
			$mail->setFichier($v['fichier_factfourn']);
			$pj ++;
		    }
		    else $mail->setFichier("");
		    if(strtotime($v['datePaye_factfourn']) < time() ) {
			$mail->setSujet("Zuno : Retard de payement sur une facture fournisseur");
			$mess = "Bonjour ".$z['prenom'].",<br />";
			$mess .= "<br />La facture <i>".$v['titre_factfourn']."</i>";
			if($v['nom_ent'] != '')
			    $mess .= " relative à l'entreprise : ".$v['nom_ent']." (".$v['cp_ent']." - ".$v['ville_ent'].")";
			$mess .= "<br />d'un montant de : ".$ttc." € ".
				"<br />devait être payée le : <b>".date('d/m/Y', strtotime($v['datePaye_factfourn']))."</b>".
				"<br />Description de la facture : `".$v['desc_factfourn']."`";
		    }
		    else {
			$mail->setSujet("Zuno : Règlement fournisseur à effectuer");
			$mess = "Bonjour ".$z['prenom'].",<br />";
			$mess .= "<br />La facture <i>".$v['titre_factfourn']."</i>";
			if($v['nom_ent'] != '')
			    $mess .= " relative à l'entreprise : ".$v['nom_ent']." (".$v['cp_ent']." - ".$v['ville_ent'].")";
			$mess .= "<br />d'un montant de : ".$ttc." € ".
				"<br />doit être payée le : <b>".date('d/m/Y', strtotime($v['datePaye_factfourn']))."</b>".
				"<br />Description de la facture : `".$v['desc_factfourn']."`";
		    }
		    $mess .= '<br /><br />Pour consulter cette facture :
                            <a href="https://'.$GLOBALS['URL']['appli'].'Login.php?token='.$resultat[1].'" title="Zuno Facture Fournisseur" >https://'.$GLOBALS['URL']['appli'].'Login.php?token='.$resultat[1].'</a>';
		    $mail->setMessage($mess);
		    $resMail = $mail->send();
		    if($resMail[0])
			$compteur ++;
		}
	    }
	    $outRapport .= $compteur." mails facture fournisseur envoyés dont ".$pj." avec pièces jointes\n";
	}
    }
    $outRapport .= "Traitement mails terminé\n";
}

$ren = new RenouvellementModel();
$res = $ren->getToDoToday();
if($res[0]) {
    $outRapport .= "Traitement renouvellements\n";
    $compteur = $aff = $dev = $cmd = $fact = $factfourn = $proj = 0;
    $erreur = null;

    $data['id'] = 0;
    $data['partie'] = 'renouvellement';
    $data['typeE'] = 'mail';
    $data['sujet'] = 'Zuno : Rapport sur les renouvellements automatiques';
    $data['expediteur'] = 'Zuno';
    $data['from'] = 'manager@zuno.fr <instance '.$GLOBALS['zunoWebService']['instance_code'].'>';
    $data['typeEmail'] = 'html';
    $data['mail'] = $GLOBALS['zunoClientCoordonnee']['mail'];
    $data['bug'] = true;
    $mail = new Sender($data);

    $debutMail = "Bonjour<br/>";
    $finMail = "<br />Vous avez reçu ce mail suite à votre demande via votre interface Zuno.";
    $finMail .= "<br />Pour ne plus recevoir de mail à propos des renouvellements, veuillez changer vos préférences.";
    $oldDest = $res[1][0]['mail_ren'];

    foreach($res[1] as $v) {
	if($v['mail_ren'] != $oldDest)
	    $corpsMail = "";
	switch($v['type_ren']) {
	    case "affaire" :
		$res[$compteur] = $ren->clonerAffaire($v['id_aff'], $v['statusChamp_ren']);
		if($res[$compteur][0]) {
		    $aff++;
		    $corpsMail .= "<br />L'affaire <b>".$v['id_aff']."</b> a été renouvellée ce jour.";
		}
		else $erreur[$v['id_ren']]=$v['id_aff'];
		break;
	    case "devis" :
		$res[$compteur] = $ren->clonerDevis($v['id_dev'], $v['statusChamp_ren']);
		if($res[$compteur][0]) {
		    $dev++;
		    $corpsMail .= "<br />Le devis <b>".$v['id_dev']."</b> a été renouvellé ce jour.";
		}
		else $erreur[$v['id_ren']]=$v['id_dev'];
		break;
	    case "commande" :
	    //A faire plus tard
		break;
	    case "facture" :
		$res[$compteur] = $ren->clonerFacture($v['id_fact'], $v['statusChamp_ren']);
		if($res[$compteur][0]) {
		    $fact++;
		    $corpsMail .= "<br />La facture <b>".$v['id_fact']."</b> a été renouvellée ce jour.";
		}
		else $erreur[$v['id_ren']]=$v['id_fact'];
		break;
	    case "factureFournisseur" :
		$res[$compteur] = $ren->clonerFactureFournisseur($v['id_factfourn'], $v['statusChamp_ren']);
		if($res[$compteur][0]) {
		    $factfourn++;
		    $corpsMail .= "<br />La facture fournisseur <b>".$v['id_factfourn']."</b> a été renouvellée ce jour.";
		}
		else $erreur[$v['id_ren']]=$v['id_factfourn'];
		break;
	    case "projet" :
		$res[$compteur] = $ren->clonerProjet($v['id_proj']);
		if($res[$compteur][0]) {
		    $proj++;
		    $corpsMail .= "<br />Le projet <b>".$v['id_proj']."</b> a été renouvellé ce jour.";
		}
		else $erreur[$v['id_ren']]=$v['id_proj'];
		break;
	}
	$compteur++;

	if($v['mail_ren'] != $oldDest) {
	    if($oldDest != '' and $corpsMail != "") {
		$mail->setMessage($debutMail.$corpsMail.$finMail);
		$mail->setMail($oldDest);
		$envoye = $mail->send();
		if($envoye[0])
		    $outRapport .= "1 Mail de renouvellement envoyé à ".$oldDest;
		else $outRapport .= "Problème à l'envoi d'un mail pour ".$oldDest;
	    }
	    $oldDest = $v['mail_ren'];

	}
    }

    if($oldDest != '' and $corpsMail != "") {
	$mail->setMessage($debutMail.$corpsMail.$finMail);
	$mail->setMail($oldDest);
	$envoye = $mail->send();
	if($envoye[0])
	    $outRapport .= "1 Mail de renouvellement envoyé à ".$oldDest."\n";
	else $outRapport .= "Problème à l'envoi d'un mail pour ".$oldDest."\n";
    }

    $outRapport .= $compteur." renouvellements à faire : \n";
    $outRapport .= $aff." affaires renouvellées\n";
    $outRapport .= $dev." devis renouvellés\n";
    $outRapport .= $cmd." commandes renouvellées\n";
    $outRapport .= $fact." factures renouvellées\n";
    $outRapport .= $factfourn." factures fournisseur renouvellées\n";
    $outRapport .= $proj." projets renouvellés\n";
    if(is_array($erreur)) {
	foreach($erreur as $kk => $vv) {
	    $outRapport .= "Une erreur est survenue sur le renouvellement n° ".$kk." concernant la fiche à l'id : ".$vv."\n";
	}
    }
    $outRapport .= "Traitement des renouvellements terminé. \n";
}

$model = new TransactionModel();
$datas = $model->getTodayDatas();
if($datas[0]) {
    $outRapport .="Début du traitement du mail de récap des transactions bancaires. \n";
    if(count($datas[1]) < 1) {
	$outRapport .= "Aucune transaction bancaire ce jour. \n";
    }else {
	$mail = "Bonsoir.<br />Ceci est un récapitulatif de toutes les transactions bancaires du jour<br />";
	$mail .= "<table><tr><th>Facture</th><th>Type</th><th>Montant</th></tr>";
	$total = 0;
	foreach($datas[1] as $v) {
	    if($v['type_hp'] == 11 or $v['type_hp'] == 12) {
		$type = "Debit";
		$total -= $v['montant_trans'];
	    }
	    else {
		$type = "Crédit";
		$total += $v['montant_trans'];
	    }
	    $mail .= "<tr><td>".$v['facture_trans']."</td><td>".$type."</td><td>".$v['montant_trans']."</td></tr>";
	}
	$mail .= "</table>";
	$mail .= "<br />Le tout pour un montant de : ".$total;
	$data['id'] = 0;
	$data['partie'] = 'renouvellement';
	$data['typeE'] = 'mail';
	$data['sujet'] = 'Zuno : Rapport sur les transactions du jour';
	$data['expediteur'] = 'Zuno';
	$data['from'] = 'manager@zuno.fr <instance '.$GLOBALS['zunoWebService']['instance_code'].'>';
	$data['typeEmail'] = 'html';
	$data['mail'] = $GLOBALS['zunoClientCoordonnee']['mail'];
	$data['bug'] = true;
	$email = new Sender($data);
	$email->setMessage($mail);
	$email->send();
    }
    $outRapport .= "Fin du traitement du récap des transactions bancaires. \n";
}

$outRapport .= "Tache cron terminée\n";
echo $outRapport;
?>
