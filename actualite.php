<?php
/*#########################################################################
#
#   name :       actualite.php
#   desc :       Gestion des actualités de l'appli
#   categorie :  sans
#   ID :  	 
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
$PC = new PageContext('normal');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
/*---------------------------------------------------------------------------+
 |
 +---------------------------------------------------------------------------*/
$sortie = '';
if($PC->rcvG['action'] == 'ajax') {
    $bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
    $req = "SELECT id, type, titre FROM actualite WHERE isVisibleFilActu = '1' ORDER BY id DESC limit 0,10";
    $bddtmp->makeRequeteFree($req);
    $result = $bddtmp->process2();
    if($PC->rcvG['channel'] == 'normal')
	$suffixe = '';
    else	$suffixe = '../';
    if($result[0]) {
	foreach($result[1] as $k => $v)
	    $sortie .= '<p><a href="#" onclick="return zuno.filActu.popupActu('.$v['id'].')"><img src=\''.$suffixe.'img/actualite/'.strtolower($v['type']).'.png\' alt=\''.$v['type'].'\'/> '.str_replace("\n", ' ',$v['titre']).'</a></p>';
    }
    else $sortie = '<p id="filActuBlock0">Problème de connexion</p>"';
    if(array_key_exists('user', $_SESSION))
	echo $sortie;
    exit;
}
elseif($PC->rcvG['id'] != '') {
    loadPlugin(array('ZModels/ActualiteModel'));
    $sql = new actualiteModel();
    $result = $sql->getDataFromID($PC->rcvG['id']);
    if($result[0]) {
	if($PC->rcvG['type'] == 'popup' and $PC->rcvG['channel'] != 'normal')
	    $prefixe = '../';
	else $prefixe = './';

	$contenu = '<div class="block width50"><div class="form">';
	$contenu .= '<div class="row"><div class="label">Type : </div>';
	$contenu .= '<div class="field"><img src=\''.$prefixe.'img/actualite/'.strtolower($result[1][0]['type']).'.png\' alt=\''.$result[1][0]['type'].'\'/> &nbsp;'.$result[1][0]['type'].'</div></div>';
	$contenu .= '<div class="row"><div class="label">Date : </div>';
	$contenu .= '<div class="field">'.DateUniv2Human($result[1][0]['date'], 'simpleDH').'</div></div>';
	$contenu .= '<div class="row"><div class="label">Utilisateur : </div>';
	$contenu .= '<div class="field">'.$result[1][0]['civ'].' '.$result[1][0]['prenom'].' '.$result[1][0]['nom'].'</div></div>';
	$contenu .= '<div class="row"><div class="label">Détail : </div>';
	$contenu .= '<div class="field">'.$result[1][0]['desc'].'</div></div>';
	$contenu.= '</div></div>';
	$contenu .= '<div class="block width50"><div class="form">';
	if($result[1][0]['id_ent'] != '') {
	    if ($result[1][0]['type_ent'] != '')
		$result[1][0]['nom_ent']	= imageTag($prefixe.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/'.$result[1][0]['type_ent'].'.png',$result[1][0]['nom_tyent']).' '.$result[1][0]['nom_ent'];
	    else $result[1][0]['nom_ent']	= imageTag($prefixe.'img/'.$GLOBALS['PropsecConf']['dir.img'].'TypeEntreprise/particulier.png','particulier').' <i>Particulier</i>';
	    $contenu .= '<div class="row"><div class="label">Entreprise : </div>';
	    $contenu .= '<div class="field"><a href="'.$prefixe.'prospec/fiche.php?id_ent='.$result[1][0]['id_ent'].'">'.$result[1][0]['nom_ent'].'</a></div></div>';
	}
	if($result[1][0]['id_cont'] != '') {
	    $contenu .= '<div class="row"><div class="label">Contact : </div>';
	    $contenu .= '<div class="field"><a href="'.$prefixe.'prospec/Contact.php?id_cont='.$result[1][0]['id_cont'].'"><img src="'.$prefixe.'img/actualite/contact.png" title="Contact", alt="contact" /> '.$result[1][0]['civ_cont'].' '.$result[1][0]['prenom_cont'].' '.$result[1][0]['nom_cont'].'</a></div></div>';
	}
	if($result[1][0]['id_aff'] != '') {
	    $contenu .= '<div class="row"><div class="label">Affaire : </div>';
	    $contenu .= '<div class="field"><a href="'.$prefixe.'draco/Affaire.php?id_aff='.$result[1][0]['id_aff'].'"><img src="'.$prefixe.'img/actualite/affaire.png" title="Affaire", alt="affaire" /> '.$result[1][0]['id_aff'].' - '.$result[1][0]['titre_aff'].' </a></div></div>';
	}
	if($result[1][0]['id_dev'] != '') {
	    $contenu .= '<div class="row"><div class="label">Devis : </div>';
	    $contenu .= '<div class="field"><a href="'.$prefixe.'draco/Devis.php?id_dev='.$result[1][0]['id_dev'].'"><img src="'.$prefixe.'img/actualite/devis.png" title="Devis", alt="devis" /> '.$result[1][0]['id_dev'].' - '.$result[1][0]['titre_dev'].'</a></div></div>';
	}
	if($result[1][0]['id_cmd'] != '') {
	    $contenu .= '<div class="row"><div class="label">Commande : </div>';
	    $contenu .= '<div class="field"><a href="'.$prefixe.'pegase/Commande.php?id_cmd='.$result[1][0]['id_cmd'].'"><img src="'.$prefixe.'img/actualite/commande.png" title="Commande", alt="commande"> '.$result[1][0]['id_cmd'].' - '.$result[1][0]['titre_cmd'].'</a></div></div>';
	}
	if($result[1][0]['id_fact'] != '') {
	    $contenu .= '<div class="row"><div class="label">Facture : </div>';
	    $contenu .= '<div class="field"><a href="'.$prefixe.'facturier/Facture.php?id_fact='.$result[1][0]['id_fact'].'"><img src="'.$prefixe.'img/actualite/facture.png" title="Facture", alt="facture"> '.$result[1][0]['id_fact'].' - '.$result[1][0]['titre_fact'].'</a></div></div>';
	}
	$contenu .= '</div></div>';
	$titre = $result[1][0]['titre'];
	$sortie = generateZBox($titre,$titre,$contenu,'','actualite'.$PC->rcvG['id']);
	if($PC->rcvG['type'] == 'popup') {
	    echo $sortie;
	    exit;
	}
    }
}
$out->AddBodyContent($sortie);
$out->Process();
?>
