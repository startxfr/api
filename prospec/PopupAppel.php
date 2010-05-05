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
loadPlugin(array('ZunoCore','ZView/ProspecView','ZView/ContactView'));

// Whe get the page context
$PC = new PageContext('prospec');
$PC->GetFullContext();
// Whe initialize page display
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);

if ($PC->rcvG['action'] == 'supp') {
	$bddtmp->makeRequeteFree("SELECT contact_app FROM appel WHERE id_app = '".$PC->rcvG['id_appel']."'");
	$res = $bddtmp->process();
	$id_cont = $res[0]['contact_app'];
	$bddtmp->makeRequeteFree("DELETE FROM appel WHERE id_app = '".$PC->rcvG['id_appel']."';");
	$bddtmp->process();
	echo "<HTML><HEAD>
	     </HEAD><body onLoad=\"window.location='../prospec/Contact.php?id_cont=".$id_cont."'\"></body>";
	exit;
}
elseif(($PC->rcvG['id_cont'] != '')or($PC->rcvG['id_app'] != '')) {
	if (isset($PC->rcvG['id_cont'])) {
		$receive_cont = TRUE;
		$id_cont = $PC->rcvG['id_cont'];
		$bddtmp->makeRequeteFree("SELECT nom_cont,prenom_cont,civ_cont,entreprise_cont FROM contact WHERE id_cont = '".$id_cont."'");
		$cont = $bddtmp->process();
		$cont = $cont[0];
		$id_ent = $cont['entreprise_cont'];
	}
	if (isset($PC->rcvG['id_app'])) {
		$receive_app = TRUE;
		$id_app = $PC->rcvG['id_app'];
		$bddtmp->makeRequeteFree("SELECT contact_app,appel_app FROM appel WHERE id_app = '".$id_app."'");
		$app = $bddtmp->process();
		$app = $app[0];
		$id_cont = $app['contact_app'];
		$bddtmp->makeRequeteFree("SELECT nom_cont,prenom_cont,civ_cont,entreprise_cont FROM contact WHERE id_cont = '".$id_cont."'");
		$cont = $bddtmp->process();
		$cont = $cont[0];
		$id_ent = $cont['entreprise_cont'];
	}

	if ($PC->rcvG['action'] == 'supp')
		echo FicheBlockAppel($id_cont,$id_app,"delete");
	else {
		if ($receive_app)
			echo FicheBlockAppel($id_cont,$id_app,"modifpop");
		else  echo FicheBlockAppel($id_cont,'',"newpop");
	}
}
else  echo "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";
?>
