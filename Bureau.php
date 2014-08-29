<?php
/*#########################################################################
#
#   name :       index.php
#   desc :       Authentification interface
#   categorie :  management page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('inc/conf.inc');		// Declare global variables from config files
include ('inc/core.inc');		// Load core library
loadPlugin(array('ZunoCore','ZView/ContactView','ZView/ProspecView','ZView/AffaireView','ZView/DevisView','ZView/CommandeView','ZView/ProduitView','ZView/FactureView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext();
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);

/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/

if(!is_array($_SESSION) or !array_key_exists('user',$_SESSION))
    header("Location: Login.php?from=Bureau.php");

if ($PC->rcvG['action'] == "reset") {
    unset($_SESSION['historiqueVisite']);
    $message = '<span class="importantgreen">Votre bureau vient d\'être réinitialisé</span><br/>';
}


$sortieB[] = contactView::MyBureau();
$sortieB[] = projetView::MyBureau();
$sortieB[] = BureauMyDevis();
$sortieB[] = BureauMyAffaire();
$sortieB[] = BureauMyCommande();
$sortieB[] = factureView::MyBureau();
$sortieB[] = BureauMyProduit();
$initio = 0;
$sortieL = $sortieR = "";
foreach($sortieB as $v) {
    if($v == "")
	continue;
    elseif($initio % 2 == 0)
	$sortieL .= $v;
    else $sortieR .= $v;
    $initio++;
}
if($sortieL == $sortieR)
    $sortieZ = "$message<span class=\"importantblue\">Votre bureau est un espace réservé pour vous afficher un résumé des dernières fiches que vous avez consultées.<br />Pour le moment, vous n'avez consulté aucune fiche.</span>";
else $sortieZ = '<div style="float: left; width: 49%; ">'.$sortieL.'</div>
	        <div style="float: right; width: 49%; ">'.$sortieR.'</div>
	        <div style="float: right; width: 100%; "><a href="Bureau.php?action=reset" title="reinitialiser le bureau" class="bouton">Réinitialiser le bureau</a></div>';
//var_dump($sortieL,$sortieR);exit;
$sortie = "";

if($PC->rcvG['module'] != '') {
    $sortieZ = '<span class="important">Vous n\'avez pas acheté le module pour effectuer cette opération.<br/>
				Pour y accéder vous avez besoin du module <b>'.$PC->rcvG['module'].'</b></span>'.$sortieZ;
}
elseif($PC->rcvG['droits'] == 'absent') {
    $sortieZ = '<span class="important" style="text-align: center;">Vous n\'avez pas les droits suffisants pour effectuer cette opération</span>'.$sortieZ;
}

$sortie .= $sortieZ;
$sortie.= '<br class="clear"/>';
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/

$out->AddBodyContent($sortie);
$out->Process();
?>
