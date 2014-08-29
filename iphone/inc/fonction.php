<?php

function Erreur($msgerreur)/*Afficher page html jusqua body*/
{
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<link rel="stylesheet" media="screen" type="text/css" title="Design" href="file/style-explorateur.css" />
<script language="javascript" type="text/javascript" src="file/javascript.js"></script> 
</head>
<body>
<div class="bulle">'.htmlentities($msgerreur).'</div>
</body></html>';
}

function GetFileName($path)/*Permet d'extraire le nom d'un fichier via le chemin*/
{
  $name=explode("/",$path);
  return $name[sizeof($name)-1];
}

function download($file)/*Fonction download permer de telecharger un fichier*/
{
  $name=GetFileName($file);/*Extraie le nom via la fonction GetFileName*/
/*
Modifie l'header forcer le telechargement au client , au  fichier desirer
*/
$filetype = trim(shell_exec("file -bi ".$file));
  header('Content-disposition: attachment; filename='.$name);/*Indique le nom*/
  header('Content-Type: '.$filetype);/*Indique le type*/
  header('Content-Length: '.filesize($file));/*Indique la taille pour permet au client de savoir le % de telechargement Ceci n'est pas obligatoire .*/
  header('Pragma: no-cache');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0, public');
  header('Expires: 0');
  readfile($file); /*Lit le fichier */
  exit; /*On Quit pour ne rien envoyez d'autre*/
}


?>
