<?php
/*#########################################################################
#
#   name :       PageModif.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');	// Declare global variables from config files
include ('../inc/core.inc');	// Load core library
loadPlugin(array('PageAdmin'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/



if (is_array($PC->rcvP['pageDo']) and  $PC->rcvP['bouton'] != '')
{
	foreach ($PC->rcvP['pageDo'] as $key => $pageId)
	{
		$sql .= " id_pg = '".$pageId."' OR";
	}
	$sqladd = "(".substr($sql,0,-2).")";

	$bddtmp = new Bdd("");
	$bddtmp->makeRequeteFree("SELECT * FROM ref_page WHERE ".$sqladd." ORDER BY nom_pg ASC");
	$res = $bddtmp->process();

	// remplissage du tableau
	if (count($res) > 0)
	{
		$j=1;
		foreach ($res as $key => $page)
		{
			$page['altern'] = ($j++ % 2);
			$_SESSION["PageLotModif"][] = $page['id_pg'];
			$tmptab .= templating('page/PageModifLot.Row',$page);
		}
		$tab['liste'] = $tmptab ;
		if ($PC->rcvP['bouton'] == 'Publier')
		{
			$tab['action'] = $PC->rcvP['bouton'];
			$tab['texte']  = "publication";
		}
		elseif ($PC->rcvP['bouton'] == 'Supprimer')
		{
			$tab['action'] = $PC->rcvP['bouton'];
			$tab['texte']  = "suppression";
		}
		elseif ($PC->rcvP['bouton'] == 'De-Publier')
		{
			$tab['action'] = $PC->rcvP['bouton'];
			$tab['texte']  = "dé-publication";
		}
		else
		{
			$tab['action'] = 'Archiver';
			$tab['texte']  = "archivage";
		}
		$titre = $tab['texte'].' des pages suivantes';
		$tab['channel'] = $PC->rcvP['channel'];
		$_SESSION["PageLotModifChannel"] = $PC->rcvP['channel'];
		$corps = templating('page/PageModifLot',$tab);
	}
	else
	{
		$corps = '<span class="important">Merci de choisir une ou plusieurs pages a traiter</span>';
		$option = 'none';
		$titre = 'Pas de page(s) séléctionnée(s)';
	}
	$content = generateZBox($titre, $titre, $corps,'','PageModifLot', $option);
}
elseif(is_array($_SESSION["PageLotModif"]) and $PC->rcvP['action'] != '')
{
	foreach ($_SESSION["PageLotModif"] as $key => $pageId)
	{
		if ($PC->rcvP['action'] == 'Publier')
		{
			$data['id_pg'] = $pageId;
			$data['actif_pg'] = "1";
			PageAdminToolkit::DBRecord($data);
		}
		elseif ($PC->rcvP['action'] == 'Archiver')
		{
			$data['id_pg'] = $pageId;
			$data['actif_pg'] = "2";
			PageAdminToolkit::DBRecord($data);
		}
		elseif ($PC->rcvP['action'] == 'Supprimer')
		{
			PageAdminToolkit::DBDelete($pageId,$_SESSION["PageLotModifChannel"]);
		}
		else
		{
			$data['id_pg'] = $pageId;
			$data['actif_pg'] = "0";
			PageAdminToolkit::DBRecord($data);
		}
	}
	unset($_SESSION["PageLotModif"]);
	unset($_SESSION["PageLotModifChannel"]);
	header("Location: PageManage.php ");
}
else
{
	header("Location: PageManage.php ");
}

/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
$out->Process();

?>
