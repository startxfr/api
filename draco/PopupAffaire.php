<?php
/*#########################################################################
#
#   name :       page.php
#   desc :       Display page content
#   categorie :  page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('ZunoCore','ZView/AffaireView'));

// Whe get the page context
$PC = new PageContext('draco');
$PC->GetFullContext();
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);

// Création d'une affaire a partir d'un projet
if($PC->rcvG['id_proj'] != '') {
    aiJeLeDroit('affaire', 20, 'web');
	$receive_proj = TRUE;
	$bddtmp->makeRequeteFree("SELECT * FROM projet WHERE id_proj = '".$PC->rcvG['id_proj']."'");
	$res = $bddtmp->process();
	$proj = $res[0];
	if($proj['affaire_proj'] != '') {
		$PC->rcvG['id_aff'] = $proj['affaire_proj'];
		$PC->rcvG['action'] = 'modif';
	}
	else {
		$id_aff = affaireModel::affaireGenerateID();
		// On copie les donnï¿½es du projet vers l'affaire
		$var['id_aff'] = $id_aff;
		$var['commercial_aff'] = $proj['utilisateur_proj'];
		$var['typeproj_aff'] = $proj['typeproj_proj'];
		$var['decid_aff'] = $proj['decid_proj'];
		$var['budget_aff'] = $proj['budget_proj'];
		$var['echeance_aff'] = $proj['echeance_proj'];
		$var['desc_aff'] = addslashes($proj['desc_proj']);
		$var['detect_aff'] = $proj['detect_proj'];
		$var['titre_aff'] = addslashes($proj['titre_proj']);
		$var['projet_aff'] = $proj['id_proj'];
		$var['contact_aff'] = $proj['contact_proj'];
		$var['status_aff'] = "1";
		$var['comm_aff'] = addslashes($proj['comm_proj']);
		if($proj['SSII_proj'] != '' or $proj['SSII_proj'] != 0) {
			$bddtmp->makeRequeteFree("SELECT * FROM ref_SSII WHERE id_SSII = '".$proj['SSII_proj']."'");
			$res = $bddtmp->process();
			$SSII = $res[0]['nom_SSII'];
			$var['comm_aff'] .= "\n\t SSII en compétition: ".$SSII;
		}
		if($proj['SSLL_proj'] != '' or $proj['SSLL_proj'] != 0) {
			$bddtmp->makeRequeteFree("SELECT * FROM ref_SSLL WHERE id_SSLL = '".$proj['SSLL_proj']."'");
			$res = $bddtmp->process();
			$SSLL = $res[0]['nom_SSLL'];
			$var['comm_aff'] .= "\n\t SSLL en compétition: ".$SSLL;
		}

		// On créer l'affaire
		affaireModel::createNewAffaireInDB($var);
		//et on update le projet d'origine
		$bddtmp->makeRequeteFree("UPDATE projet SET affaire_proj = '".$id_aff."' WHERE id_proj = '".$PC->rcvG['id_proj']."';");
		$bddtmp->process();
	}
	$receive_aff = TRUE;
	$PC->rcvG['id_aff'] = $id_aff;
	$PC->rcvG['action'] = 'modif';
}
elseif($PC->rcvG['action'] == 'suppconfirm') {
    aiJeLeDroit('affaire', 30, 'web');
	affaireModel::deleteAffaireInDB($PC->rcvG['id_aff']);
	echo "<html><body><script language=\"javascript\">window.location.reload();zuno.popup.close();</script></body></html>";
}
elseif(isset($PC->rcvG['id_aff'])) {
	$receive_aff = TRUE;
	$bddtmp->makeRequeteFree("SELECT * FROM affaire WHERE id_aff = '".$PC->rcvG['id_app']."'");
	$res = $bddtmp->process();
	$proj = $res[0];
	$PC->rcvG['id_cont'] = $proj['contact_aff'];
}
if(isset($PC->rcvG['id_cont'])) {
	$receive_cont = TRUE;
	$bddtmp->makeRequeteFree("SELECT nom_cont,prenom_cont,civ_cont,entreprise_cont FROM contact WHERE id_cont = '".$PC->rcvG['id_cont']."'");
	$res = $bddtmp->process();
	$cont = $res[0];
	$PC->rcvG['id_ent'] = $cont['entreprise_cont'];
}

aiJeLeDroit('affaire', 10, 'web');
if(($receive_aff == TRUE)and
   ($PC->rcvG['action'] != 'supp'))
	echo affaireView::affaireFicheBlockSimple($PC->rcvG['id_aff'],$PC->rcvG['id_cont'],'modif');
elseif ($PC->rcvG['action'] == 'supp')
	echo affaireView::affaireFicheBlockSimple($PC->rcvG['id_aff'],$PC->rcvG['id_cont'],'supp');
elseif (($receive_cont == TRUE)or
	($receive_aff == TRUE))
	echo affaireView::affaireFicheBlockSimple($PC->rcvG['id_aff'],$PC->rcvG['id_cont'],'new');
else  echo "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";
?>