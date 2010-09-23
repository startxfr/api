<?php
/*#########################################################################
#
#   name :       MyDesk.php
#   desc :       enter Gnose personal main page
#   categorie :  page
#   ID :  	 $Id$
#
#   copyright:   See licence.txt for this script licence
#########################################################################*/

/*------------------------------------------------------------------------+
| FRAMEWORK LOADING	& LOG ACTIVITY
+------------------------------------------------------------------------*/
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library
loadPlugin(array('ZView/DocumentView'));
/*------------------------------------------------------------------------+
| INITIALIZE PAGE CONTEXT
+------------------------------------------------------------------------*/

// Whe get the page context
$PC = new PageContext('gnose');
$PC->GetFullContext();
// Whe initialize page display
$out = new PageDisplay($PC->channel);
$out->ConfigureWithPageData($PC->Data,$PC->cacheXML);
/*------------------------------------------------------------------------+
| MODULE PROCESSING
+------------------------------------------------------------------------*/


//////////////////////////////////
// Gestion de l'affichage de la liste des répertoires
//////////////////////////////////
if(($_GET['action'] == 'naviguer') or (!array_key_exists('action', $_GET))) {
    if($_GET['rep'] == '') {
	$rep = new documentViewRepertoire('', $GLOBALS['SVN_Pool1']['ArchivesDir']);
	$sortie = generateZBox('Navigateur','', $rep->afficher(), '', 'id_du_navigateur');
    }
    elseif($_GET['sortie'] == 'popup') {
	$rep = new documentViewRepertoire($_GET['rep'], $GLOBALS['SVN_Pool1']['ArchivesDir']);
	$sortie = $rep->afficher('contenu', false, 'dossierlight');
    }
    else {
	$rep = new documentViewRepertoire($_GET['rep'], $GLOBALS['SVN_Pool1']['ArchivesDir']);
	$sortie = $rep->afficher('contenu', false);
    }
}
elseif($_GET['action'] == 'download') {
    $fichier = new documentViewFichier($_GET['fich'], $GLOBALS['SVN_Pool1']['ArchivesDir']);
    $fichier->download();
}

elseif($_GET['action'] == 'suppD') {
    $rep = new documentViewRepertoire($_GET['path'], $GLOBALS['SVN_Pool1']['ArchivesDir']);
    $rep->effacerRepertoire();
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'suppF') {
    $fichier = new documentViewFichier($_GET['path'], $GLOBALS['SVN_Pool1']['ArchivesDir']);
    $fichier->effacerFichier();
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'detailsR') {
    $rep = new documentViewRepertoire($_GET['path'], $GLOBALS['SVN_Pool1']['ArchivesDir']);
    $detail = $rep->detailRepertoire();
    $sortie .= '<div class="blockTable"><div style="display:table;" class="tableau">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Fichier</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Dernière révision par</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Date dernière révision</div></div>';
    $sortie .= '<div style="display:table-row;">';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$_GET['nom'].'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$detail[0]['Last Changed Author'].'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$detail[0]['Last Changed Date'].'</div></div>';
    $sortie .= '</div>';
    $titre = 'Détails sur le répertoire '.$_GET['nom'];
}elseif($_GET['action'] == 'detailsF') {
    $rep = new documentViewFichier($_GET['path']);
    $detail = $rep->detailFichier();
    $sortie .= '<div class="blockTable"><div style="display:table;" class="tableau">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Fichier</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Dernière révision par</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Date dernière révision</div></div>';
    $sortie .= '<div style="display:table-row;">';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$_GET['nom'].'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$detail[0]['Last Changed Author'].'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$detail[0]['Last Changed Date'].'</div></div>';
    $sortie .= '</div>';
    $titre = 'Détails sur le fichier '.$_GET['nom'];
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/
if ($PC->rcvG['format'] == 'popup')
    echo generateZBox($titre, $titre, $sortie,'<div class="footer"></div>','popupWORK','');
elseif($PC->rcvG['style'] == 'ajax')
    echo $sortie;
else {
    $out->AddBodyContent($sortie.$sortie1);
    $out->Process();
}
?>