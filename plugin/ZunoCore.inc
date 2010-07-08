<?php
/*#########################################################################
#
#   name :       Prospec
#   desc :       Prospec Channel module
#   categorie :  plugin module
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/**
 * Create select liste with contact list for a given company
 * @param $name String: name of the select tag
 * @param $id_ent String: company ID to get contact list from
 * @param $id_select String: Contact ID to select
 * @param $class String: CSS style to apply
 * @param $autre String: Other information to push into this tag
 * @return HTML Select list
 */
function ZunoSelectContact($name,$id_ent,$id_select = "",$class = "",$autre = "") {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $bddtmp->makeRequeteFree("SELECT * FROM contact LEFT JOIN ref_fonction ON ref_fonction.id_fct = contact.fonction_cont
				  WHERE entreprise_cont = '".$id_ent."' ORDER BY nom_cont ASC");
    $res = $bddtmp->process();
    // remplissage du block de liste des appels
    if (count($res) > 0) {
	$j=1;
	foreach($res as $key => $con) {
	    $champ[$con['id_cont']] = $con['civ_cont']." ".$con['prenom_cont']." ".$con['nom_cont']." (".$con['nom_fct'].")";
	}
	if($name == 'DISPLAY') {
	    return $champ[$id_select];
	}
	else {
	    return selectTag($name,$champ,$id_select,$class,$autre,TRUE,$GLOBALS['PropsecConf']['DBPool']);
	}
    }
    else {
	return "<i>pas de contact enregistrés</i>";
    }
}

/**
 * Create select liste with active affaire list
 * @param $name String: name of the select tag
 * @param $id_select String: Contact ID to select
 * @param $class String: CSS style to apply
 * @param $autre String: Other information to push into this tag
 * @return HTML Select list
 */
function ZunoSelectAffaire($name = "affaire",$id_select = "",$class = "",$autre = "") {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $req = "SELECT * FROM entreprise,affaire
		LEFT JOIN contact ON contact.id_cont = affaire.contact_aff
		LEFT JOIN ref_statusaffaire ON affaire.status_aff = ref_statusaffaire.id_staff
		WHERE entreprise_cont = id_ent
		AND actif_aff = '1'
		ORDER BY id_aff ASC, detect_aff ASC,nom_ent ASC";
    $bddtmp->makeRequeteFree($req);
    $res = $bddtmp->process();
    // remplissage du block de liste des appels
    if (count($res) > 0) {
	foreach($res as $key => $con) {
	    if(strlen($con['nom_ent']) > 28) {
		$con['nom_ent'] = substr($con['nom_ent'],0,28)."...";
	    }
	    $champ[$con['id_aff']] = $con['id_aff']." - ".$con['nom_ent']." (".$con['nom_staff'].")";
	}
	if($name == 'DISPLAY')
	    return $champ[$id_select];
	else return selectTag($name,$champ,$id_select,$class,$autre,TRUE,$GLOBALS['PropsecConf']['DBPool']);
    }
    else return "<i>pas d'affaire enregistrées</i>";
}

/**
 * Create select liste with active commande list
 * @param $name String: name of the select tag
 * @param $id_select String: Contact ID to select
 * @param $class String: CSS style to apply
 * @param $autre String: Other information to push into this tag
 * @return HTML Select list
 */
function ZunoSelectCommande($name = "commande",$id_select = "",$class = "",$autre = "") {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $req = "SELECT id_cmd, titre_cmd, nom_ent, sommeHT_cmd FROM entreprise,devis,commande
		WHERE entreprise_dev = id_ent
		AND devis_cmd = id_dev
		ORDER BY id_cmd ASC, nom_ent ASC";
    $bddtmp->makeRequeteFree($req);
    $res = $bddtmp->process();
    // remplissage du block de liste des appels
    if (count($res) > 0) {
	$j=1;
	foreach($res as $key => $con) {
	    $champ[$con['id_cmd']] = $con['id_cmd']." - ".$con['titre_cmd'].", ".$con['nom_ent']."(".$con['sommeHT_cmd'].")";
	}
	if($name == 'DISPLAY')
	    return $champ[$id_select];
	else return selectTag($name,$champ,$id_select,$class,$autre,TRUE,$GLOBALS['PropsecConf']['DBPool']);
    }
    else return "<i>pas de commande enregistrées</i>";
}


/**
 * Create portlet for Company detail
 * @param $id_ent String: company ID
 * @return HTML portlet ready to insert
 */
function  BlockSendmail($id_cont,$data,$opt = "new") {
    if($id_cont != '') {
	$id_cont = $id_cont;
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$bddtmp->makeRequeteFree("SELECT * FROM entreprise,contact WHERE entreprise_cont = id_ent AND id_cont = '".$id_cont."'");
	$cont = $bddtmp->process();
	$cont1 = $cont[0];
	if(is_array($data))
	    $cont = array_merge($cont1,$data);
	else  $cont = $cont1;
    }


    $tag['formName'] = "SendMail";
    if($opt == 'modif') {
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','id_cont','','','',$id_cont,'');
    }
    else {
	$opt = "new";
	$cont['sender'] = $_SESSION['user']['mail'];
	$tag['action'] = inputTag('hidden','action','','','',$opt,'');
	$tag['action'] .= inputTag('hidden','id_cont','','','',$id_cont,'');
    }


    $tag['type'] = selectTag('type', array('mail' => 'Par e-mail', 'fax' => 'Par fax', 'courrier' => 'Par Courrier'), 'mail');
    $tag['message'] = $cont['message'];
    $tag['liste_sender'] = $_SESSION['user']['fullnom']." (".$_SESSION['user']['mail'].")".inputTag('hidden', 'sender','','', '',$cont['sender']);
    if ($cont['mail_cont'] != '')
	$tag['destinataire'] =  $cont['civ_cont']." ".$cont['prenom_cont']." ".$cont['nom_cont']." (".$cont['mail_cont'].")".
		inputTag('hidden','to','','','',$cont['mail_cont']);
    else  $tag['destinataire'] =  inputTag('text','to','','','',$cont['to']);
    $tag['titre'] 	= inputTag('text','titre','','',60,$cont['titre'],' style="float:left"');
    $tag['mess']	= textareaTag('mess',$cont['mess'],'',60,10,' id="MessageToSend" style="float:left"');

    foreach ($GLOBALS['ZunoSendMail'] as $key => $val) {
	$k = explode('.',$key,2);
	if($k[0] == 'pj')
	    $toto[$k[1]] = utf8_decode($val);
    }
    $tag['fileChoose'] = selectTag("file[]",$toto,'','','style="height:100px" multiple="multiple"',FALSE);
    $tag['fileChoose1'] = '';
    if($data['fileAttach'] != '')
	$tag['fileChoose1'] = '<div class="row"><div class="label">Fichier additionnel : </div><div class="field">'.$data['fileAttach'].
		inputTag('hidden','fileAdd','','','',$data['fileAttach']).'</div></div>';

    $tag['copie'] = inputTag('checkbox', 'cc', '', '', '', $_SESSION['user']['mail']);
    $input = array();
    $output= array();
    $cleanFrom = array("\n","\t","\r");
    $cleanTo  = array("\\n","\\t","\\r");
    foreach ($cont as $in => $out) {
	$cleanFrom[] = "{".$in."}";
	$cleanTo[] = $out;
    }
    loadPlugin(array('docGenerator'));
    foreach ($a = docGeneratorGetZunoConfInfo() as $in => $out) {
	$cleanFrom[] = "{".$in."}";
	$cleanTo[] = $out;
    }

    $i = 0;


    $corps = templating('prospec/SendMail',$tag);

    $titre 	= imageTag('../img/prospec/contact.png','resultat')." Envoi d'un mail à ".$cont['civ_cont']." ".$cont['prenom_cont']." ".$cont['nom_cont'];
    $pied 	= '<a name="annuler" onclick="zuno.popup.close();">'.imageTag('../img/prospec/cancel.png','','middle').' Annuler</a>
		   <a name="valider" onclick="zuno.business.formTools.sendFormAjah(\'SendMail\', \'prospec/PopupSendMail.php\', \'ficheInterne\');zuno.popup.close();">'.imageTag('../img/prospec/record.png','','middle').' Envoyer</a>';

    return generateZBox($titre,$titre,$corps,$pied,'sendMail','');
}



?>