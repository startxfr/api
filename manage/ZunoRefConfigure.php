<?php
/*#########################################################################
#
#   name :       ZunoRefConfigure.php
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
loadPlugin(array('ZunoCore'));
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
if ($PC->rcvP['table'] != '') {

    if (substr($PC->rcvP['action'],0,6) == 'Change') {

	$table = $PC->rcvP['table'];
	$id    = substr($PC->rcvP['action'],6);
	// requete pour la liste
	$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
	$structure = $bddtmp->AnalyseTableStructure($table,"");

	$newval = $PC->rcvP[$structure['titre'].$id];


	$var_recv[$structure['titre']] = $newval;
	$bddtmp->makeRequeteUpdate($table,$structure['key'],$id,$var_recv);
	$bddtmp->process();
    }
}
elseif ($PC->rcvG['table'] != '') {

    $table = $PC->rcvG['table'];
}


// requete pour la liste
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
$bddtmp->makeRequeteFree("SELECT * FROM ".$table);
$res = $bddtmp->process();

$structure = $bddtmp->AnalyseTableStructure($table,"");

$tag['FormName'] = 'ChangeRef';
$tag['action'] = inputTag('hidden','action','','','',$tag['FormName'],' id="MakeRefAction"');
$tag['action'] .= inputTag('hidden','table','','','',$table,'');

if (count($res) > 0) {
    foreach ($res as $key => $dev1) {
	$tag['link'] = linkTag('javascript:document.'.$tag['FormName'].'.submit()',imageTag('../img/valid.png','valider','middle'),'','','onclick="ChangeRefAction(\'Change'.$dev1[$structure['key']].'\')"');
	$tag['nom'] = inputTag('text',$structure['titre'].$dev1[$structure['key']],'','','',$dev1[$structure['titre']],'');
	$tag['id'] = $dev1[$structure['key']];

	$tmptab .= templating('manage/ZunoRefConfigure.row',$tag);
    }
    $tag['RefList'] = $tmptab;
    $tag['table'] = $table;
    $tag['message'] = $message;
    $content = templating('manage/ZunoRefConfigure',$tag);
}
else {
    $content = '<span class="important">Aucune table ne correspond a votre demande</span>';
}


/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($content);
echo $out->DisplayHeader();
echo $out->DisplayBodyContent();
echo $out->CreateDebug();

?>
