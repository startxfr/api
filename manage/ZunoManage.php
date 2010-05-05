<?php
/*#########################################################################
#
#   name :       ZunoManage.php
#   desc :       Gestion de lapplication Zuno
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
loadPlugin(array('ZunoCore'));
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


//CONTENT PAGE DISPLAY
$listesupported = explode(",",'ref_activite,ref_condireglement,ref_fonction,ref_modereglement,ref_prodfamille,ref_statusaffaire,ref_statuscommande,ref_statusdevis,ref_statusfacture,ref_typeentreprise,ref_typeproj');
foreach ($listesupported as $val)
{
	$RowContent['table']	= $val;
	$RefList .= templating('prospec/ZunoConfiguration.RefRow',$RowContent);
}

$outrow['message']= $message;
$outrow['RefList'] = $RefList;
$sortie = templating('prospec/ZunoConfiguration',$outrow);
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
