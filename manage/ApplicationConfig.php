<?php
/*#########################################################################
#
#   name :       actualite.manage.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('admin');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);


/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
//ACTIONS
if ($PC->rcvP['action'] == 'Nettoyer le cache')
{
	rm($GLOBALS['REP']['appli'].$GLOBALS['REP']['tmp']."*");
}
elseif ($PC->rcvP['action'] == 'SearchReplace')
{

	$dbConnexion = new Bdd();
	$liste = $dbConnexion->AnalyseTableStructure('ref_page');
	$exclude = array( 'id_pg','owner_pg','droit_pg','modif_date_pg','modif_user_pg',
			'create_date_pg','create_user_pg','img_pg','img_menu_pg','page_pg',
			'channel_pg','parent_pg','order_pg','menuon_pg','sousmenu_pg',
			'frameset_pg','style_pg','actif_pg');
	foreach($liste as $key => $val)
	{
		if (!in_array($key,$exclude))
		{
			$SQLAdd .= " `".$key."` LIKE '%".$PC->rcvP['search']."%' OR";
		}
	}
	$SQLAdd   = " AND (".substr($SQLAdd, 0, -3).")";
	$varsql['channel_pg'] = $PC->rcvP['channel'];
	$dbConnexion->makeRequeteAuto('ref_page',$varsql,$SQLAdd.' ORDER BY order_pg, nom_pg ASC');
	$HResult = $dbConnexion->process();
	if(count($HResult) > 0)
	{
		foreach($HResult as $kl => $ligne)
		{
			foreach($ligne as $key => $val)
			{
				if ((in_array($key,$exclude)) or (strpos($val,$PC->rcvP['search']) === false))
				{
				}
				else
				{
					$activateUpdate = 'ok';
					$listeUpdate[$key] = addslashes(trim(str_replace($PC->rcvP['search'],$PC->rcvP['replace'],$val)));
				}
			}
			if ($activateUpdate == 'ok')
			{
				$dbConnexion->makeRequeteUpdate('ref_page','id_pg',$ligne['id_pg'],$listeUpdate);
				$dbConnexion->process();
				unset($activateUpdate);
				unset($listeUpdate);
			}
		}
		$message .=	"<span class=\"importantgreen\">".count($HResult).$GLOBALS['Tx4Lg']['AppsConfSearchReplaceOK1'].
				$PC->rcvP['search'].$GLOBALS['Tx4Lg']['AppsConfSearchReplaceOK2'].$PC->rcvP['replace']."</span>";
	}
	else
	{
		$message .=	"<span class=\"importantgreen\">".$GLOBALS['Tx4Lg']['AppsConfSearchReplaceOK3']."</span>";
	}
}
elseif ($PC->rcvP['action'] == 'CreateChannel')
{
	//Création du channel en base de donnée
	$dbConnexion = new Bdd();
	$liste = $dbConnexion->AnalyseTableStructure('ref_page');
	$typeListe = explode("','",substr($liste['channel_pg']['Type'],6,-2));
	if(!in_array($PC->rcvP['channel'],$typeListe))
	{
		$typeListe[] = $PC->rcvP['channel'];
		foreach($typeListe as $chanlist)
		{
			$stringEnum .= "'".$chanlist."',";
		}
		$stringEnum = substr($stringEnum,0,-1);
		$SQL = "ALTER TABLE `ref_page` CHANGE `channel_pg` `channel_pg` ENUM(".$stringEnum.") NOT NULL DEFAULT 'normal';";
		$dbConnexion->makeRequeteFree($SQL);
		$dbConnexion->process();
	}
	//création du répertoire
	if(!is_dir($GLOBALS['REP']['appli'].$PC->rcvP['channel']))
	{
		mkdir($GLOBALS['REP']['appli'].$PC->rcvP['channel'],0777);
	}
	//Modification du fichier de conf channel.xml
	$ConffileChannel = $GLOBALS['REP']['appli']."conf/permanent/channel.xml";
	if(file_exists($ConffileChannel))
	{
		$configTree = simplexml_load_file($ConffileChannel);
		$dom1 = new DomDocument();
		$dom1->loadXML($configTree->asXML());
		$xpath = new domXPath($dom1);
		$xpathQuery = $xpath->query("//group[@name = 'CHANNEL_list']/*");
		$size 	    = $xpathQuery->length;
		$xpathQuery = $xpath->query("//group[@name = 'CHANNEL_list']");
		$node = $xpathQuery->item(0);
		$new = $dom1->createElement('value');
		$new->setAttribute("id",$size);
		$value = $dom1->createTextNode($PC->rcvP['channel']);
		$new->appendChild($value);
		$new = $node->appendChild($new);
		$xpathQuery = $xpath->query("/configuration");
		$Roots = $xpathQuery->item(0);
		$xpathQuery1 = $xpath->query("//group[@name = 'CHANNEL_normal']");
		$Clonable = $xpathQuery1->item(0);
		$Clonenode1 = $Clonable->cloneNode(true);
		$Clonenode1->setAttribute("name",'CHANNEL_'.$PC->rcvP['channel']);
		$Roots->appendChild($Clonenode1);
		$dom1->save($ConffileChannel);
	}
}

//CONTENT PAGE DISPLAY
$listesupported = explode(",",$GLOBALS['LANGUE']['supported']);
foreach ($listesupported as $val)
{
	$RowContent['langID']	= $val;
	$RowContent['langTitre']= $GLOBALS['LANGUE_'.$_SESSION["language"]][$val];
	if ($val == $GLOBALS['LANGUE']['default'])
	{
		$RowContent['langTitre'] .= imageTag("../img/exclam.png",'important');
	}
	$LangList .= templating('manage/AppliConfiguration.LangRow',$RowContent);
}




$outrow['message']	= $message;
$outrow['LangList'] = $LangList;
$sortie = templating('manage/AppliConfiguration',$outrow);
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
