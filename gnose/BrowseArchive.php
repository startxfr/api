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
if(($_GET['action'] == 'naviguer') or (!array_key_exists('action', $_GET)))
{
	if($_GET['rep'] == '')
	{
		$rep = new documentViewRepertoire('', 'ARCHIVES');
		$sortie = generateZBox('titre','', $rep->afficher(), '', 'id_de_test');
	}
	elseif($_GET['sortie'] == 'popup')
	{
		$rep = new documentViewRepertoire($_GET['rep'], 'ARCHIVES');
		$sortie = $rep->afficher('contenu', false, 'dossierlight');
	}
	else
	{
		$rep = new documentViewRepertoire($_GET['rep'], 'ARCHIVES');
		$sortie = $rep->afficher('contenu', false);
	}	
}
elseif($_GET['action'] == 'download')
{
	$fichier = new Fichier($_GET['fich']);
	$fichier->download();
}
elseif($_GET['action'] == 'suppD')
{
	$rep = new documentViewRepertoire($_GET['path'], 'ARCHIVES');
	$rep->effacerdocumentViewRepertoire();
	$rep = new documentViewRepertoire('', 'ARCHIVES');
	$sortie = generateZBox('titre','', $rep->afficher(), '', 'id_de_test');
}
elseif($_GET['action'] == 'suppF')
{
	$fichier = new documentViewFichier($_GET['path']);
	$fichier->effacerFichier();
	$rep = new documentViewRepertoire('', 'ARCHIVES');
	$sortie = generateZBox('titre','', $rep->afficher(), '', 'id_de_test');
}
elseif($_GET['action'] == 'modifD')
{
	$sortie = '<div class="titre">Renomer</div>';
	$sortie .= '<form method="POST" action="masuperpage.php?action=doModifD&path='.$_GET['path'].'">';
	$sortie .= 'Nouveau nom : <input type="text" name="nom" />';
	$sortie .= '<input type="submit" value="Valider" />';
	$sortie .= '</form>';
}
elseif($_GET['action'] == 'doModifD')
{
	$rep = new documentViewRepertoire($_GET['path'], 'ARCHIVES');
	$rep->renomer($_POST['nom']);
	$sortie = "<script language=\"javascript\">window.location.reload();zuno.popup.close();</script>";
}
elseif($_GET['action'] == 'copierD')
{
	$temp = explode('/', $_GET['path']);
	$sortie = '<div class="titre">Copier</div>';
	$sortie .= '<form method="POST" action="masuperpage.php?action=doCopierD&path='.$_GET['path'].'">';
	$sortie .= '<div>Nom du nouveau dossier : <input type="text" name="nom" /><div>';
	$sortie .= 'Dans quel dossier : ';
	$sortie .= '<input type="submit" value="Valider" />';
	$sortie .= '</form>';	
}
/*------------------------------------------------------------------------+
| DISPLAY PROCESSING
+------------------------------------------------------------------------*/
if ($PC->rcvG['format'] == 'popup')
{
	echo $sortie;
}
elseif($PC->rcvG['style'] == 'ajax')
{
	echo $sortie;
}
else
{
	$out->AddBodyContent($sortie.$sortie1);
	$out->Process();
}
?>
