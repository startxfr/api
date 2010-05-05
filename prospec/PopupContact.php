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
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
if(($PC->rcvG['id_ent'] != '')or($PC->rcvG['id_cont'] != ''))
{
	if (isset($PC->rcvG['id_cont'])) {
		$receive_cont = TRUE;
		$id_cont = $PC->rcvG['id_cont'];
		$bddtmp->makeRequeteFree("SELECT nom_cont,prenom_cont,civ_cont,entreprise_cont FROM contact WHERE id_cont = '".$id_cont."'");
		$cont = $bddtmp->process();
		$cont = $cont[0];
		$id_ent = $cont['entreprise_cont'];
		$bddtmp->makeRequeteFree("SELECT nom_ent FROM entreprise WHERE id_ent = '".$id_ent."'");
		$ent = $bddtmp->process();
		$ent = $ent[0];
	}
	else {
		$receive_ent = TRUE;
		$id_ent = $PC->rcvG['id_ent'];
		$bddtmp->makeRequeteFree("SELECT nom_ent FROM entreprise WHERE id_ent = '".$id_ent."'");
		$ent = $bddtmp->process();
		$ent = $ent[0];
	}


	if ($PC->rcvG['action'] == 'supp')
		echo FicheBlockContact($id_cont,$id_ent,'delete');
	elseif ($PC->rcvG['action'] == 'move')
		echo FicheBlockContact($id_cont,$id_ent,'move');
	else {
		if ($receive_cont)
			echo FicheBlockContact($id_cont,$id_ent,'modifpop');
		else  echo FicheBlockContact($id_cont,$id_ent,'newpop');
	}
}
else
{
	echo "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";
}
?>
