<?php
header('Content-Type: text/html; charset=utf-8');/*Encodage*/

/*
Programme par : kiki67100
le mardi 27 novembre 2007

Navigateur de fichier permet de crée , supprimmer , lister les fichier présent sur un serveur
*/

if(!@include('fonction.php')){
echo '<div style="position:absolute; top:45%; left:40%; color:red;">Impossible d\'inclure fonction.php ...</div>';
exit;
}

$DEFAULT=$_SERVER['DOCUMENT_ROOT']; /*Default redirection quand le script commence*/
$IMGFOLDER='img/file.png'; /*L'icon pour le dossier*/
$IMGFILE='img/fichier.gif'; /*Icon pour le fichier*/
$IMGCREATEFILE='img/filenew.png'; /*Fichier pour crée un fichier*/
$IMGUPLOAD='img/upload.gif'; /*Fichier pour upload des fichier*/
$IMGCREATEFOLDER='img/folder-new.png';
$IMGSEARCH='img/search.png';
$IMGRENAME='img/edit.png';

if(!isset($_GET['rename'])&&!isset($_GET['pathren'])&&!isset($_GET['en'])&&!isset($_GET['upload'])&&!isset($_POST['pathupload'])&&!isset($_GET['touch'])&&!isset($_GET['download'])&&/*Verifie si rien n'est appellé*/
!isset($_GET['delete'])&&!isset($_GET['path'])&&!isset($_GET['dir'])&&!isset($_FILES['fichier'])&&!isset($_GET['mkdir'])&&!isset
($_GET['pathmkdir']))
{
	header('location:?dir='.$DEFAULT);
}

if(isset($_GET['upload'])&&isset($_POST)&&!file_exists($_POST['pathupload'].$_FILES['fichier']['name']))
{
$tmp_file = $_FILES['fichier']['tmp_name'];
$name_file = $_FILES['fichier']['name'];

    if( !is_uploaded_file($tmp_file) )
    {
        Erreur('Erreur lors du telechargement !');
		
		exit;
    }
	
	 if( !move_uploaded_file($tmp_file, $_POST['pathupload'].'/'. $name_file) )
    {

		Erreur('Erreur lors du deplacement du fichier !</div></body></html>');
		exit;
    }

	header('location:'.$_SERVER['HTTP_REFERER']);
}



if(isset($_GET['touch'])&&!empty($_GET['touch'])&&isset($_GET['path'])&&!empty($_GET['path']))/*Permer de crée un fichier*/
{
 
 if(file_exists($_GET['path'].'/'.$_GET['touch']))
 {
	Erreur('Un fichier porte deja le nom : '.$_GET['touch'].' !');
	exit;
 }
 
 if(!@touch($_GET['path'].'/'.$_GET['touch']))
 {
     Erreur('Erreur l\'ors de la creation du fichier '.$_GET['touch'].'');
     exit;
 }
  
 header('location:'.'?dir='.$_GET['path']);/*Redirection a l'url precedent*/

}

if(isset($_GET['mkdir'])&&!empty($_GET['mkdir'])&&isset($_GET['pathmkdir'])&&!empty($_GET['pathmkdir']))
{
 if(file_exists($_GET['pathmkdir'].'/'.$_GET['mkdir'])&&is_dir($_GET['pathmkdir'].'/'.$_GET['mkdir']))
 {
  Erreur('Erreur un dossier porte deja se nom :'.$_GET['mkdir'].' ...');
  exit;
	}
 
 if(!@mkdir($_GET['pathmkdir'].'/'.$_GET['mkdir'],0755)){
     Erreur('Erreur l\'ors de la création du fichier '.$_GET['mkdir'].'!');
     exit;
 }
  header('location:?dir='.$_GET['pathmkdir']);
}


if(isset($_GET['rename'])&&!empty($_GET['rename'])&&isset($_GET['pathren'])&&!empty($_GET['pathren'])&&isset($_GET['en'])&&!empty($_GET['en']))
{

if(!file_exists($_GET['pathren'].'/'.$_GET['rename']))
{
	Erreur('Fichier '.$_GET['rename'].' Introuvable ...');
	exit;
 }

if(file_exists($_GET['pathren'].'/'.$_GET['en']))
{
	Erreur('Un fichier porte deja le nom : '.$_GET['en'].' ...');
	exit;
	}
 
 if(!@rename($_GET['pathren'].'/'.$_GET['rename'],$_GET['pathren'].'/'.$_GET['en'])){
     Erreur('Erreur pour renommer '.$_GET['rename'].' en '.$_GET['en']);
     exit;
}

  header('location:?dir='.$_GET['pathren']);
}



if(isset($_GET['download'])&&!empty($_GET['download'])&&file_exists($_GET['download'])&&is_file($_GET['download']))/*Telecharge un fichier*/
{
  download($_GET['download']);/*....*/
}

if(isset($_GET['delete'])&&!empty($_GET['delete'])&&file_exists($_GET['delete'])&&is_file($_GET['delete']))/*Supprimé un fichier ...*/
{
  
  if(!@unlink($_GET['delete']))
  {
		Erreur('Erreur l\ors de la suppresion de '.$_GET['delete'].'');
		exit;
  }
  header('location:'.$_SERVER['HTTP_REFERER']);
}

if(isset($_GET['dir'])&&!empty($_GET['dir'])&&file_exists($_GET['dir'])&&is_dir($_GET['dir']))/*Verifie la variable et bien un repertoire*/
{
$rep=$_GET['dir'];
$rep=str_replace("//","/",$rep);
$handle = @opendir($rep);/* Ouvre le repertoire */

if(!$handle)
  {
	Erreur('Erreur l\'ors de l\'ouverture de '.$rep.' !');
	exit;
}

/**************************************------------------------HTML-------------------------*************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<link rel="stylesheet" media="screen" type="text/css" title="Design" href="file/style-explorateur.css" />
<script language="javascript" type="text/javascript" src="file/javascript.js"></script> 
</head>
<body>
<div class="opensrc"><img src="http://upload.wikimedia.org/wikipedia/commons/thumb/4/42/Opensource.svg/288px-Opensource.svg.png" /></div>
<div style="float:right">
<a href="#" onclick="display_('touch');"><img title="Cree un fichier"  title="Cree un fichier" src="<?php echo $IMGCREATEFILE; ?>" /></a><br />
<a href="#" onclick="display_('upload');"><img title="Telecharger un fichier"  title="Telecharger un fichier" src="<?php echo $IMGUPLOAD; ?>" /></a><br />
<a href="#" onclick="display_('mkdir');"><img title="Cree un dossier"  title="Cree un dossier" src="<?php echo $IMGCREATEFOLDER; ?>" /></a><br />
<a href="#" onclick="display_('search');"><img title="Chercher"  title="Chercher" src="<?php echo $IMGSEARCH; ?>" /></a></span><br />
<a href="#" onclick="display_('rename');"><img title="Renommer"  title="Renommer" src="<?php echo $IMGRENAME; ?>" /></a></span><br />

</div>
</body>
</html>
<?php
while ($f = readdir($handle)) { //Boucle qui enumere tout les fichier d'un repertoire
     $lien=str_replace(" ",'%20',$f); /*Pour les espace fichier*/
	 $replien=str_replace(" ",'%20',$rep);/*idem pour les dossier*/
     
	 /*Pour la couleur du background ......................................*/
	 if($i==0){  echo '<div class="color1">'; 
	 $i=1;
    }else{ echo '<div class="color2">';
    $i=0;
    }
	 /*Fin de la couleur ..................................*/
	 
  if(@is_dir($rep.'/'.$f)){ /*verifie si c'est un repertoire*/
	  
     echo '<a href="?dir='.$replien.'/'.$lien.'"><img alt="Dossier" src="'.$IMGFOLDER.'" />'.$f.'</a><br />'; 
   
   }elseif(@is_file($rep.'/'.$f)){/*Verifie si c'est bien un fichier*/
   
	  echo '<img src="'.$IMGFILE.'" alt="Fichier"/>'.$f.'<a href="?delete='.$replien.'/'.$lien.'" onclick="return confirm(\'Supprimer '.$f.' ?\');"><img alt="Supprimmer" title="/!\Supprimer/!\ " src="img/delete.gif" /></a><a href="?download='.$replien.'/'.$lien.'" ><img alt="Telecharger" title="Telecharger " src="img/download.png" /></a><br />';
}
echo '</div>'."\n"; /*ferme la div pour la couleur.*/
/*Crée le formulaire pour crée un fichier par default display:none affiche en cliquant en  haut*/

}
}

/*Formulaire Pour crée un fichier */
echo '<div class="bulle" id="touch" style="display:none;"><form method="get" action="?" >
<img src="'.$IMGFILE.'"></img><input type="text" name="touch"  title="Fichier a cree" size="30" />
<input type="hidden" name="path" value="'.$replien.'" />
</form></div>';

/*Formulaire pour upload un fichier*/
echo '<div class="bulle" id="upload" style="display:none;">
<form method="post" enctype="multipart/form-data" action="?upload">
<input type="file" name="fichier" size="25">
<input type="submit" name="upload" value="Go">
<input type="hidden" name="pathupload" value="'.$replien.'" />
</form></div>';

/*Formulaire pour crée un dossier :)*/

echo '<div class="bulle" id="mkdir" style="display:none;"><form method="get" action="?" >
<img src="'.$IMGFOLDER.'" ></img><input type="text" name="mkdir"  title="Cree dossier" size="30" />
<input type="hidden" name="pathmkdir" value="'.$replien.'" />
</form></div>';

/*renommer*/

echo '<div class="bulle" id="rename" style="display:none;"><form method="get" action="?" >
<img src="'.$IMGRENAME.'"></img><input type="text" name="rename"  title="Renommer ?" size="10" /> en <input type="text" name="en"  title="en" size="10" /><input type="submit" value="go" />
<input type="hidden" name="pathren" value="'.$replien.'" />
</form></div>';

echo '<div class="bulle" id="search" style="display:none;">
<img src='.$IMGSEARCH.'></img><input type="text" size="20" id="larecherche"/><br><div id="recherche"></div></div>
';

?>
</body>
</html>
