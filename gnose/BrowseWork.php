<?php
/*#########################################################################
#
#   name :       MyDesk.php
#   desc :       enter Gnose personal main page
#   categorie :  page
#   ID :  	 $Id: BrowseWork.php 2492 2009-04-28 11:55:41Z cl $
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
	$rep = new documentViewRepertoire();
	$sortie = generateZBox('Navigateur','', $rep->afficher(), '', 'id_du_navigateur');
    }
    elseif($_GET['sortie'] == 'popup') {
	$rep = new documentViewRepertoire($_GET['rep']);
	$sortie = $rep->afficher('contenu', false, 'dossierlight');
    }
    else {
	$rep = new documentViewRepertoire($_GET['rep']);
	$sortie = $rep->afficher('contenu', false);
    }
}
elseif($_GET['action'] == 'download') {
    $fichier = new documentViewFichier($_GET['fich']);
    $fichier->download();
}

elseif($_GET['action'] == 'suppD') {
    $rep = new documentViewRepertoire($_GET['path']);
    $rep->effacerRepertoire();
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'suppF') {
    $fichier = new documentViewFichier($_GET['path']);
    $fichier->effacerFichier();
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'modifD') {
    $sortie = '<form method="POST" action="BrowseWork.php?action=doModifD&path='.$_GET['path'].'">';
    $sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Nouveau nom : </div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;"></div>';
    $sortie .= '</div>';
    $sortie .= '<div style="display:table-row;"><div class="cellule" style="display:table-cell;"><input type="text" name="nom" style="float:left;" /></div>';
    $sortie .= '<div class="cellule" style="display:table-cell;"><input type="submit" value="Valider" /></div>';
    $sortie .= '</div></div></div>';
    $sortie .= '</form>';
    $titre = 'Modifier le répertoire '.basename($_GET['path']);
}
elseif($_GET['action'] == 'doModifD') {
    $rep = new documentViewRepertoire($_GET['path']);
    $rep->renomer($_POST['nom']);
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'copierD') {
    $rep = new documentViewRepertoire();
    $sortie .= '<form method="POST" action="BrowseWork.php?action=doCopierD&path='.$_GET['path'].'">';
    $sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Nouveau nom</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Destination</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;"></div>';
    $sortie .= '</div>';
    $sortie .= '<div style="display:table-row;"><div style="display:table-cell;"><input type="text" name="nom" value="'.$_GET['nom'].'" style="float:left;" /></div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$rep->afficher('', false, 'dossierlight').'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;"><input type="submit" value="Valider" /><input id="nom_rep_form_cp" type="text" name="rep" readonly="readonly" /></div></div>';
    $sortie .= '</div></div>';
    $sortie .= '<input type="hidden" name="path" id="path_hidden_form_cp"/>';
    $sortie .= '</form>';
    $titre = 'Copier le répertoire '.$_GET['nom'];
}
elseif($_GET['action'] == 'copierF') {
    $rep = new documentViewRepertoire();
    $sortie .= '<form method="POST" action="BrowseWork.php?action=doCopierF&path='.$_GET['path'].'">';
    $sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Nouveau nom</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Destination</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;"></div>';
    $sortie .= '</div>';
    $sortie .= '<div style="display:table-row;"><div style="display:table-cell;"><input type="text" name="nom" value="'.$_GET['nom'].'" style="float:left;"/></div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$rep->afficher('', false, 'dossierlight').'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;"><input type="submit" value="Valider" /><input id="nom_rep_form_cp" type="text" name="rep" readonly="readonly" /></div></div>';
    $sortie .= '</div></div>';
    $sortie .= '<input type="hidden" name="path" id="path_hidden_form_cp"/>';
    $sortie .= '</form>';
    $tite = 'Copier le fichier '.$_GET['nom'];
}
elseif($_GET['action'] == 'doCopierD') {
    $rep = new documentViewRepertoire($_GET['path']);
    $rep->copier($GLOBALS['SVN_Pool1']['WorkCopy'].$_POST['path'].'/'.$_POST['nom']);
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'doCopierF') {
    $rep = new documentViewFichier($_GET['path']);
    $rep->copier($GLOBALS['SVN_Pool1']['WorkCopy'].$_POST['path'].'/'.$_POST['nom']);
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'deplacerD') {
    $rep = new documentViewRepertoire();
    $sortie .= '<form method="POST" action="BrowseWork.php?action=doDeplacerD&path='.$_GET['path'].'">';
    $sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Repertoire</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Destination</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;"></div>';
    $sortie .= '</div>';
    $sortie .= '<div style="display:table-row;"><div style="display:table-cell;"><input type="text" name="nom" value="'.$_GET['nom'].'" readonly="readonly" style="float:left;"/></div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$rep->afficher('', false, 'dossierlight').'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;"><input type="submit" value="Valider" /><input id="nom_rep_form_cp" type="text" name="rep" readonly="readonly" /></div></div>';
    $sortie .= '</div></div>';
    $sortie .= '<input type="hidden" name="path" id="path_hidden_form_cp"/>';
    $sortie .= '</form>';
    $titre = 'Déplacer le dossier '.$_GET['nom'];
}
elseif($_GET['action'] == 'deplacerF') {
    $rep = new documentViewRepertoire();
    $sortie .= '<form method="POST" action="BrowseWork.php?action=doDeplacerF&path='.$_GET['path'].'">';
    $sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Fichier</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Destination</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;"></div>';
    $sortie .= '</div>';
    $sortie .= '<div style="display:table-row;"><div style="display:table-cell;"><input type="text" name="nom" value="'.$_GET['nom'].'" readonly="readonly" style="float:left;"/></div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$rep->afficher('', false, 'dossierlight').'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;"><input type="submit" value="Valider" /><input id="nom_rep_form_cp" type="text" name="rep" readonly="readonly" /></div></div>';
    $sortie .= '</div></div>';
    $sortie .= '<input type="hidden" name="path" id="path_hidden_form_cp"/>';
    $sortie .= '</form>';
    $titre = 'Déplacer le fichier '.$_GET['nom'];
}
elseif($_GET['action'] == 'doDeplacerD') {
    $rep = new documentViewRepertoire($_GET['path']);
    $rep->deplacer($GLOBALS['SVN_Pool1']['WorkCopy'].$_POST['path'].'/'.$_POST['nom']);
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'doDeplacerF') {
    $rep = new documentViewFichier($_GET['path']);
    $rep->deplacer($GLOBALS['SVN_Pool1']['WorkCopy'].$_POST['path'].'/'.$_POST['nom']);
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'archiverD') {
    $rep = new documentViewRepertoire('', $GLOBALS['SVN_Pool1']['ArchivesDir']);
    $sortie .= '<form method="POST" action="BrowseWork.php?action=doArchiverD&path='.$_GET['path'].'">';
    $sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Nouveau nom</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Destination</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;"></div>';
    $sortie .= '</div>';
    $sortie .= '<div style="display:table-row;"><div class="celluleH" style="display:table-cell;"></div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;"><b><a onclick="placerLiens(\'a'.$GLOBALS['SVN_Pool1']['ArchivesDir'].'\', \''.$GLOBALS['SVN_Pool1']['ArchivesDir'].'\');" ><img src="../img/files/dir.png" style="margin-left:-18px;"/>'.$GLOBALS['SVN_Pool1']['ArchivesDir'].'</a></b></div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;"></div></div>';
    $sortie .= '<div style="display:table-row;"><div style="display:table-cell;"><input type="text" name="nom" value="'.$_GET['nom'].'" readonly="readonly" style="float:left;"/></div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$rep->afficher('', false, 'dossierlight').'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;"><input type="submit" value="Valider" /><input id="nom_rep_form_cp" type="text" name="rep" readonly="readonly" /></div></div>';
    $sortie .= '</div></div>';
    $sortie .= '<input type="hidden" name="path" id="path_hidden_form_cp"/>';
    $sortie .= '</form>';
    $titre = 'Archiver le dossier '.$_GET['nom'];
}
elseif($_GET['action'] == 'doArchiverD') {
    $rep = new documentViewRepertoire($_GET['path']);
    $rep->deplacer($GLOBALS['SVN_Pool1']['WorkCopy'].$_POST['path'].'/'.$_POST['nom']);
    header('Location:BrowseWork.php');
}
elseif($_GET['action'] == 'upload') {
    $rep = new documentViewRepertoire();
    $sortie .= '<form method="POST" action="Upload.php" enctype="multipart/form-data" >';
    $sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Fichier</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Destination</div>';
    $sortie .= '</div>';
    $sortie .= '<div style="display:table-row;"><div style="display:table-cell;"><input type="hidden" name="MAX_FILE_SIZE" value="104857600" /><input type="file" name="fichier" style="float:left;"/></div>';
    $sortie .= '<div class="cellule" style="display:table-cell;">'.$rep->afficher('', false, 'dossierlight').'</div>';
    $sortie .= '<div class="cellule" style="display:table-cell;"><input type="submit" name="submit" value="Valider" /><input id="nom_rep_form_cp" type="text" name="rep" readonly="readonly" /></div></div>';
    $sortie .= '</div></div>';
    $sortie .= '<input type="hidden" name="path" id="path_hidden_form_cp"/>';
    $sortie .= '</form>';
    $titre = 'Télécharger un nouveau répertoire';
}
elseif($_GET['action'] == 'modifF') {
    $rep = new documentViewRepertoire();
    $sortie .= '<form method="POST" action="Upload.php" enctype="multipart/form-data" >';
    $sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
    $sortie .= '<div class="titre" style="display:table-row;">';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Fichier</div>';
    $sortie .= '<div class="celluleH" style="display:table-cell;">Destination</div>';
    $sortie .= '</div>';
    $sortie .= '<div style="display:table-row;"><div class="cellule" style="display:table-cell;"><input type="hidden" name="MAX_FILE_SIZE" value="104857600" />
				<input type="file" name="fichier" style="float:left;"/></div>';
    $sortie .= '<div class="cellule" style="display:table-cell;"><input type="submit" name="submit" value="Valider" /><input type="text" name="rep" value="'.basename($_GET['path']).'" readonly="readonly" /></div>';
    $sortie .= '</div>';
    $sortie .= '</div></div>';
    $sortie .= '<input type="hidden" name="path" id="path_hidden_form_cp" value="'.$_GET['path'].'" />';
    $sortie .= '</form>';
    $titre = 'Modifier le fichier '.basename($_GET['path']);
}
elseif($_GET['action'] == 'detailsR') {
    $rep = new documentViewRepertoire($_GET['path']);
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
