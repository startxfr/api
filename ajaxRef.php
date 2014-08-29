<?php
/*#########################################################################
#
#   name :       Login.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id: Login.php 1915 2008-12-13 01:46:04Z cl $
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('inc/conf.inc');		// Declare global variables from config files
include ('inc/core.inc');		// Load core library
loadPlugin(array('ZView/ProduitView','ZModels/ProduitModel','ZunoSxa'));

$PC = new PageContext();
$PC->GetVarContext();


if(array_key_exists('action', $PC->rcvG)) {
    $sql = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $out = '<option value=""></option>';
    //Affichage des villes par code postal
    if($PC->rcvG['action'] == 'villeForCp') {
	$sql = new Bdd($GLOBALS['referenceBase']['dbPool']);
	if(array_key_exists('cp', $PC->rcvG)) {
	    $sql->makeRequeteFree("SELECT Ville from ".$GLOBALS['referenceBase']['tableCP']." where CP = '".$PC->rcvG['cp']."' ");
	    $resultat = $sql->process2();
	    if($resultat[0] and is_array($resultat[1]))
		foreach($resultat[1] as $k => $v) {
		    if($k == 0)
			$out .= '<option selected="selected" value="'.$v['Ville'].'">'.$v['Ville'].'</option>';
		    else
			$out .= '<option value="'.$v['Ville'].'">'.$v['Ville'].'</option>';
		}

	}
	else $out = '<option value=""><i>ERREUR: pas de code postal</i></option>';
    }
    elseif($PC->rcvG['action'] == 'listeContact') {
	$ent = ($PC->rcvG['id_ent'] != '') ? $PC->rcvG['id_ent'] : $PC->rcvP['id_ent'];
	if($ent != '')
	    $req = "SELECT id_cont, civ_cont, nom_cont, prenom_cont, nom_fct from contact left join ref_fonction on ref_fonction.id_fct = contact.fonction_cont where entreprise_cont = '".$ent."' ";
	else $req = "SELECT * from contact, ref_pays  where entreprise_cont is null AND pays_cont = id_pays ";
	if($PC->rcvP['value'] != "**")
	    $req .= " AND ( nom_cont LIKE '%".$PC->rcvP['value']."%' or prenom_cont LIKE '%".$PC->rcvP['value']."%') ";
	$sql->makeRequeteFree($req);
	$resultat = $sql->process2();
	if(is_array($resultat[1])) {
	    foreach ($resultat[1] as $v)
		if($ent != '')
		    $out .= '<li title="'.$v['id_cont'].'" >'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].' ('.$v['nom_fct'].')</li>';
		else
		    $out .= '<li title="'.$v['id_cont'].'" value="'.$v['add1_cont'].'-_-'.$v['add2_cont'].'-_-'.$v['cp_cont'].'-_-'.$v['ville_cont'].'-_-'.$v['id_pays'].'-_-'.$v['nom_pays'].'" >'.$v['civ_cont'].' '.$v['prenom_cont'].' '.$v['nom_cont'].' ('.$v['nom_fct'].')</li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeProduit') {
	loadPlugin(array('ZModels/ProduitModel'));
	$req = new ProduitModel();
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$result = $req->getDataForSearchProduit($search,25);
	if(is_array($result[1])) {
	    foreach($result[1] as $v)
		$out .= '<li title="'.$v['id_prod'].'-_-'.$v['treePathKey'].' '.$v['nom_prodfam'].'-_-'.$v['prix_prod'].'-_-'.$v['description_prod'].'-_-'.$v['PF'].'">['.$v['id_prod'].'] '.$v['nom_prod'].' <span class="informal">('.formatCurencyDisplay((double)$v['prix_prod']).')</span></li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeAffaire') {
	loadPlugin(array('ZModels/AffaireModel'));
	$req = new affaireModel();
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	if($PC->rcvG['id_ent'] != '')
	    $ent = "AND id_ent = '".$PC->rcvG['id_ent']."'";
	else {
	    $ent = "";
	}
	$result = $req->getDataForSearch($search, '10', '0', $ent);
	if(is_array($result[1])) {
	    foreach($result[1] as $v)
		$out .= '<li title="'.$v['id_aff'].'">['.$v['id_aff'].'] '.$v['titre_aff'].'</li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeFacturePlusEnt') {
	loadPlugin(array('ZModels/FactureModel'));
	$req = new factureModel();
	$search = ($PC->rcvP['value'] == '**') ? '' : $PC->rcvP['value'];
	$result = $req->getDataForSearch($search, '10', '0');
	if(is_array($result[1])) {
	    foreach($result[1] as $v) {
		if(strlen($v['id_fact']) == 1)
		    $id = "00".$v['id_fact'];
		elseif(strlen($v['id_fact']) == 2)
		    $id = "0".$v['id_fact'];
		else $id = $v['id_fact'];
		$out .= '<li title="'.$v['id_fact'].'">'.$v['type_fact'].' '.$req->getFormatedIdFromData($v).': '.$v['titre_fact'].'</li>';
	    }
	}
    }
    elseif($PC->rcvG['action'] == 'listeProjet') {
	loadPlugin(array('ZModels/ContactModel'));
	$req = new projetModel();
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	if($PC->rcvG['id_ent'] != '')
	    $ent = "AND entreprise_cont = '".$PC->rcvG['id_ent']."'";
	else {
	    $ent = "";
	}
	$result = $req->getDataForSearch($search, '10', '0', $ent);
	if(is_array($result[1])) {
	    foreach($result[1] as $v)
		$out .= '<li title="'.$v['id_proj'].'">['.$v['id_proj'].'] '.$v['titre_proj'].'</li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeUser') {
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$sql->makeRequeteFree("SELECT * from user where login LIKE '%".$search."%' or nom LIKE '%".$search."%' or prenom LIKE '%".$search."%' Limit 0,10 ");
	$resultat = $sql->process2();
	if(is_array($resultat[1])) {
	    foreach($resultat[1] as $v)
		$out .= '<li title="'.$v['login'].'">'.$v['civ'].' '.$v['prenom'].' '.$v['nom'].'</li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeCp') {
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$sql->makeRequeteFree("SELECT * from ref_departement where nom_dep LIKE '".$search."%' or id_dep LIKE'".$search."%' Limit 0,10 ");
	$resultat = $sql->process2();
	if(is_array($resultat[1])) {
	    foreach($resultat[1] as $v)
		$out .= '<li title="'.$v['id_dep'].'">'.$v['nom_dep'].' ('.$v['id_dep'].')</li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeEntreprise') {
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$sql->makeRequeteFree("SELECT * from entreprise where nom_ent LIKE '".$search."%' Limit 0,10 ");
	$resultat = $sql->process2();
	if(is_array($resultat[1])) {
	    foreach($resultat[1] as $v) {
		if ($v['type_ent'] != '')
		    $v['nom_ent']	= imageTag(getStaticUrl('img').$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$v['type_ent'].'.png',$v['nom_tyent']).' '.$v['nom_ent'];
		$out .= '<li title="'.$v['id_ent'].'">'.$v['nom_ent'].' ('.$v['ville_ent'].')</li>';
	    }
	}
    }
    elseif($PC->rcvG['action'] == 'listePays') {
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$sql->makeRequeteFree("SELECT * from ref_pays WHERE nom_pays LIKE '".$search."%' ");
	$resultat = $sql->process2();
	if(is_array($resultat[1])) {
	    foreach($resultat[1] as $v)
		$out .= '<li title="'.$v['id_pays'].'">'.$v['nom_pays'].'</li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeEntAffDevis') {
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$qTag = "LIKE '".$search."%'";
	$sql->makeRequeteFree("SELECT * FROM affaire LEFT JOIN entreprise ON entreprise.id_ent = affaire.entreprise_aff LEFT JOIN ref_pays on ref_pays.id_pays = entreprise.pays_ent, contact where (id_aff $qTag OR titre_aff $qTag OR nom_ent $qTag OR cp_ent $qTag) AND actif_aff = '1' AND contact_aff = id_cont GROUP BY id_aff ORDER BY id_aff ASC LIMIT 0,10 ");
	$result = $sql->process2();
	if(is_array($result[1]))
	    foreach($result[1] as $v)
		$out .= '<li title="'.$v['id_aff'].'-_-'.$v['id_ent'].'-_-'.$v['add1_ent'].'-_-'.$v['add2_ent'].'-_-'.$v['cp_ent'].'-_-'.$v['ville_ent'].'-_-'.$v['pays_ent'].'-_-'.$v['nom_pays'].'-_-'.$v['id_cont'].'-_-'.$v['civ_cont'].'-_-'.$v['prenom_cont'].'-_-'.$v['nom_cont'].'">'.imageTag(getStaticUrl('img').'actualite/affaire.png','affaire').' '.$v['id_aff'].' - '.$v['titre_aff'].' ('.$v['nom_ent'].')</li>';
	$sql->makeRequeteFree("SELECT * from entreprise LEFT JOIN ref_pays ON ref_pays.id_pays = entreprise.pays_ent, contact where (nom_ent $qTag or cp_ent $qTag or ville_ent $qTag) AND id_ent = entreprise_cont GROUP BY id_ent ORDER BY nom_ent ASC LIMIT 0,10 ");
	$result = $sql->process2();
	if(is_array($result[1])) {
	    foreach($result[1] as $v) {
		if ($v['type_ent'] != '')
		    $v['nom_ent']	= imageTag(getStaticUrl('img').$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$v['type_ent'].'.png',$v['nom_tyent']).' '.$v['nom_ent'];
		$out .= '<li title="null-_-'.$v['id_ent'].'-_-'.$v['add1_ent'].'-_-'.$v['add2_ent'].'-_-'.$v['cp_ent'].'-_-'.$v['ville_ent'].'-_-'.$v['pays_ent'].'-_-'.$v['nom_pays'].'-_-'.$v['id_cont'].'-_-'.$v['civ_cont'].'-_-'.$v['prenom_cont'].'-_-'.$v['nom_cont'].'">'.$v['nom_ent'].' ('.$v['ville_ent'].')</li>';
	    }
	}
    }

    elseif($PC->rcvG['action'] == 'listeEntCmdFact') {
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$qTag = "LIKE '".$search."%'";
	$sql->makeRequeteFree("SELECT * from entreprise LEFT JOIN ref_pays ON ref_pays.id_pays = entreprise.pays_ent, contact where (nom_ent $qTag or cp_ent $qTag or ville_ent $qTag) AND entreprise_cont = id_ent GROUP BY id_ent ORDER BY nom_ent ASC LIMIT 0,10 ");
	$result = $sql->process2();
	if(is_array($result[1])) {
	    foreach($result[1] as $v) {
		if ($v['type_ent'] != '')
		    $v['nom_ent']	= imageTag(getStaticUrl('img').$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$v['type_ent'].'.png',$v['nom_tyent']).' '.$v['nom_ent'];
		$out .= '<li title="null-_-'.$v['id_ent'].'-_-'.$v['add1_ent'].'-_-'.$v['add2_ent'].'-_-'.$v['cp_ent'].'-_-'.$v['ville_ent'].'-_-'.$v['pays_ent'].'-_-'.$v['nom_pays'].'-_-'.$v['id_cont'].'-_-'.$v['civ_cont'].'-_-'.$v['prenom_cont'].'-_-'.$v['nom_cont'].'">'.$v['nom_ent'].' ('.$v['ville_ent'].')</li>';
	    }
	}
	$sql->makeRequeteFree("SELECT * FROM commande c LEFT JOIN entreprise ON entreprise.id_ent = c.entreprise_cmd LEFT JOIN ref_pays on ref_pays.id_pays = entreprise.pays_ent, contact where (id_cmd $qTag OR titre_cmd $qTag OR nom_ent $qTag OR cp_ent $qTag) AND status_cmd IN (4,5,6,7,8) AND contact_cmd = id_cont GROUP BY id_cmd ORDER BY id_cmd ASC LIMIT 0,10 ");
	$result = $sql->process2();
	if(is_array($result[1])) {
	    foreach($result[1] as $v)
		$out .= '<li title="'.$v['id_cmd'].'-_-'.$v['id_ent'].'-_-'.$v['add1_ent'].'-_-'.$v['add2_ent'].'-_-'.$v['cp_ent'].'-_-'.$v['ville_ent'].'-_-'.$v['pays_ent'].'-_-'.$v['nom_pays'].'-_-'.$v['id_cont'].'-_-'.$v['civ_cont'].'-_-'.$v['prenom_cont'].'-_-'.$v['nom_cont'].'">'.imageTag(getStaticUrl('img').'actualite/commande.png','commande').' '.$v['id_cmd'].' - '.$v['titre_cmd'].' ('.$v['nom_ent'].')</li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeFamille') {
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$sql->makeRequeteFree("SELECT * FROM ref_prodfamille where nom_prodfam LIKE '".$search."%' or treePathKey LIKE '".$search."%' order by nom_prodfam");
	$result = $sql->process2();
	if($result[0]) {
	    foreach($result[1] as $v)
		$out .='<li title="'.$v['id_prodfam'].'">'.$v['treePathKey'].' '.$v['nom_prodfam'].'</li>';
	}
    }
    elseif($PC->rcvG['action'] == 'listeContactFournisseur') {
	$sql->makeRequeteFree("SELECT id_cont, civ_cont, prenom_cont, nom_cont, cp_ent, nom_ent from contact left join entreprise on entreprise.id_ent = contact.entreprise_cont where entreprise_cont='".$PC->rcvG['value']."'");
	$result = $sql->process2();
	if($result[0]) {
	    $out = '{ "datas" : { "nomEntreprise" : "'.$result[1][0]['nom_ent'].'", "cpEntreprise" : "'.$result[1][0]['cp_ent'].'",
                                "contacts" : [';

	    foreach($result[1] as $v)
		$out .= '{"id_cont" : "'.$v['id_cont'].'", "civ_cont" : "'.$v['civ_cont'].'", "prenom_cont" : "'.$v['prenom_cont'].'", "nom_cont" : "'.$v['nom_cont'].'"}, ';

	    $out = substr($out, 0,strlen($out)-2);
	    $out .='] } }';
	    echo $out;
	    exit;
	}
    }
    elseif($PC->rcvG['action'] == 'synchToSB') {
        loadPlugin('ZA.Wsdl');
        $client = new zunoWsdlClient('tools', $GLOBALS['zunoManagerWebService']['superBaseWebServicePath']);
        $client->setParam('manager_name', $GLOBALS['zunoManagerWebService']['manager_name']);
        $client->setParam('manager_key', $GLOBALS['zunoManagerWebService']['manager_key']);
        $client->setParam('all',false);
        if($client->call('updateProduit')) {
            $rs = unserialize($client->reponse);
            if($rs['good']) {
                $good = 'true';
            }
            else {
                $good = 'false';
                $mess = $rs[1];
            }
        }
        else {
            $good = 'false';
            $mess = $client->message;
        }
        echo json_encode(array('good' => $good, "message" => $mess));
        exit;
    }
    elseif($PC->rcvG['action'] == 'listeMail') {

	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$sql->makeRequeteFree("Select mail_cont from contact where (mail_cont LIKE '".$search."%' or nom_cont LIKE '".$search."%') and mail_cont is not null group by mail_cont order by mail_cont limit 0,10");
	$result = $sql->process2();
	if($result[0]) {
	    foreach($result[1] as $v) {
		if($v['mail_cont'] != "")
		    $out .='<li title="'.$v['mail_cont'].'">'.$v['mail_cont'].'</li>';
	    }
	}
    }
    elseif($PC->rcvG['action'] == 'quickSearchPropsec') {
	if($PC->rcvP['value'] == '**')
	    $search = '';
	else
	    $search = $PC->rcvP['value'];
	$sql->makeRequeteFree("Select id_cont, civ_cont, prenom_cont, nom_cont FROM contact WHERE nom_cont LIKE '".$search."%' OR prenom_cont LIKE '".$search."%' ORDER BY nom_cont ASC, prenom_cont ASC limit 0,10");
	$rc = $sql->process2();
	$sql->makeRequeteFree("SELECT id_ent, nom_ent, cp_ent, ville_ent FROM entreprise WHERE nom_ent LIKE '".$search."%' ORDER BY nom_ent limit 0,10");
	$re = $sql->process2();
	if($rc[0] and $re[0]) {
	    $tab = array();
	    foreach($rc[1] as $v) {
		$tab[$v['nom_cont']." ".$v['prenom_cont']][0] = $v['id_cont'];
		$tab[$v['nom_cont']." ".$v['prenom_cont']][1] = $v['civ_cont']." ".$v['prenom_cont']." ".$v['nom_cont'];
		$tab[$v['nom_cont']." ".$v['prenom_cont']][2] = 'cont';
	    }
	    foreach($re[1] as $v) {
		$tab[$v['nom_ent']][0] = $v['id_ent'];
		$tab[$v['nom_ent']][1] = $v['nom_ent']." (".$v['cp_ent']." - ".$v['ville_ent'].")";
		$tab[$v['nom_ent']][2] = 'ent';
	    }
	    ksort($tab);
	    $tab = array_slice($tab,0,10);
	    foreach($tab as $v) {
		if($v[2] == 'cont')
		    $out .= '<li onclick="window.location.replace(\'Contact.php?id_cont='.$v[0].'\');">'.$v[1].'</li>';
		else
		    $out .= '<li onclick="window.location.replace(\'fiche.php?id_ent='.$v[0].'\');">'.$v[1].'</li>';
	    }

	}
    }

    elseif($PC->rcvG['action'] == 'suppCloud') {
	if($PC->rcvP['module'] == '') {
	    $retour = array('error'=>'true');
	}
	elseif($PC->rcvP['module'] == 'prospec') {
	    loadPlugin('ZModels/CloudModel');
	    $cloud = new CloudModel();
	    $ret = $cloud->dropToCloud(array('contact', 'entreprise'), $PC->rcvP['user']);
	    if($ret[0])
		$retour['error'] = 'false';
	    else
		$retour['error'] = 'true';
	}
	else {
	    loadPlugin('ZModels/CloudModel');
	    $cloud = new CloudModel();
	    $ret = $cloud->dropToCloud($PC->rcvP['module'], $PC->rcvP['user']);
	    if($ret[0])
		$retour['error'] = 'false';
	    else
		$retour['error'] = 'true';
	}
	echo json_encode($retour);
	exit;
    }
    else $out = '<li title=""><i>ERREUR: action non reconnue</i></li>';
}
else $out = '<li title=""><i>ERREUR: action non précisé</i></li>';




if($PC->rcvG['action'] != 'villeForCp')
    $out = '<ul>'.$out.'</ul>';

echo $out;
?>