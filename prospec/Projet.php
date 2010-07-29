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
$out = new PageDisplay($PC->channel);
$out->headerHTML->initCalendar();
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/
$bddtmp = new Bdd($GLOBALS['PropsecConf']['DBPool']);
if($PC->rcvG['action'] == 'suppconfirm') {
    $bddtmp->makeRequeteDelete('projet',array("id_proj" => $PC->rcvG['id_proj']));
    $bddtmp->process();
    echo "<html><body><script language=\"javascript\">window.location.reload();zuno.popup.close();</script></body></html>";
    exit;
}
elseif (isset($PC->rcvG['id_proj'])) {
    $receive_proj = TRUE;
    $bddtmp->makeRequeteFree("SELECT * FROM projet WHERE id_proj = '".$PC->rcvG['id_app']."'");
    $res = $bddtmp->process();
    $proj = $res[0];
    $PC->rcvG['id_cont'] = $proj['contact_proj'];
    $bddtmp->makeRequeteFree("SELECT nom_cont,prenom_cont,civ_cont,entreprise_cont FROM contact WHERE id_cont = '".$PC->rcvG['id_cont']."'");
    $res = $bddtmp->process();
    $cont = $res[0];
    $PC->rcvG['id_ent'] = $cont['entreprise_cont'];
    $bddtmp->makeRequeteFree("SELECT nom_ent FROM entreprise WHERE id_ent = '".$PC->rcvG['id_ent']."'");
    $res = $bddtmp->process();
    $ent = $res[0];

    if ($proj['appel_proj'] != '') {
	$PC->rcvG['id_app'] = $proj['appel_proj'];
	$bddtmp->makeRequeteFree("SELECT contact_app,appel_app FROM appel WHERE id_app = '".$PC->rcvG['id_app']."'");
	$res = $bddtmp->process();
	$app = $res[0];
    }
}
elseif (isset($PC->rcvG['id_app'])) {
    $receive_app = TRUE;
    $bddtmp->makeRequeteFree("SELECT contact_app,appel_app FROM appel WHERE id_app = '".$PC->rcvG['id_app']."'");
    $res = $bddtmp->process();
    $app = $res[0];
    $PC->rcvG['id_cont'] = $app['contact_app'];
    $bddtmp->makeRequeteFree("SELECT nom_cont,prenom_cont,civ_cont,entreprise_cont FROM contact WHERE id_cont = '".$PC->rcvG['id_cont']."'");
    $res = $bddtmp->process();
    $cont = $res[0];
    $PC->rcvG['id_ent'] = $cont['entreprise_cont'];
    $bddtmp->makeRequeteFree("SELECT nom_ent FROM entreprise WHERE id_ent = '".$PC->rcvG['id_ent']."'");
    $res = $bddtmp->process();
    $ent = $res[0];
}
if (isset($PC->rcvG['id_cont'])) {
    $receive_cont = TRUE;
    $bddtmp->makeRequeteFree("SELECT nom_cont,prenom_cont,civ_cont,entreprise_cont FROM contact WHERE id_cont = '".$PC->rcvG['id_cont']."'");
    $res = $bddtmp->process();
    $cont = $res[0];
    $PC->rcvG['id_ent'] = $cont['entreprise_cont'];
    $bddtmp->makeRequeteFree("SELECT nom_ent FROM entreprise WHERE id_ent = '".$PC->rcvG['id_ent']."'");
    $res = $bddtmp->process();
    $ent = $res[0];
}
if (isset($PC->rcvG['id_ent'])) {
    $receive_ent = TRUE;
    $bddtmp->makeRequeteFree("SELECT nom_ent FROM entreprise WHERE id_ent = '".$PC->rcvG['id_ent']."'");
    $res = $bddtmp->process();
    $ent = $res[0];
}



if(($receive_proj == TRUE)and
	($PC->rcvG['action'] != 'supp'))
    $sortie = projetView::ProjetFicheBlock($PC->rcvG['id_proj'],$PC->rcvG['id_cont'],'modif');
elseif (($receive_cont == TRUE)or
	($receive_app == TRUE))
    $sortie = projetView::ProjetFicheBlock($PC->rcvG['id_proj'],$PC->rcvG['id_cont'],'new');
elseif ((isset($PC->rcvG['action']))and
	($PC->rcvG['action'] == 'supp'))
    $sortie = projetView::ProjetFicheBlock($PC->rcvG['id_proj'],$PC->rcvG['id_cont'],'supp');
else  $sortie = "<html><body><script language=\"javascript\">zuno.popup.close();</script></body></html>";



if((isset($PC->rcvG['format']))and
	($PC->rcvG['format'] == 'popup')) {
    echo $sortie;
    exit;
}
$out->AddBodyContent($sortie);
$out->Process();

?>
