<?php
loadPlugin('ZView/GeneralView');

/**
 * Classe qui gère l'affichage et le traitement
 * des fichiers via le navigateur intégré à ZUNO
 *
 * @author Nicolas Mannocci
 * @version 1.0
 */
class documentViewFichier extends generalView {
    private $m_nom; //Le nom du fichier
    private $m_chemin; //Le chemin complet au dossier parent du fichier
    private $m_taille; //La taille en affichage humain du fichier
    private $m_proprio; //Le propriétaire Subversion du fichier
    private $m_dateM; //La date de modification Subversion
    private $m_image; //Le path de l'image à afficher
    private $m_souspath; //Le path du fichier, relatif au répertoire de gnose


    /**
     * Constructeur de la classe
     * Il récupère toutes les informations du fichier passé en paramètre
     * @param string $path Le chemin du fichier, relatif au répertoire gnose
     */
    function __construct($path) {
	$this->m_souspath = $path;
	$path = $GLOBALS['SVN_Pool1']['WorkCopy'].$path;
	$temp = explode('/', $path);
	$total = count($temp);
	for($k=1; $k<$total-1; $k++) {
	    $chemin .= '/'.$temp[$k];
	}
	$this->m_nom = $temp[$total-1];
	$this->m_chemin = $chemin;
	$this->actualiserImage();
	$this->m_taille = $this->nicesize(filesize($path));
	$temp = libSvn::SVNInfo('', $path);
	$this->m_dateM = substr($temp['Last Changed Date'], 8, 2)."/".substr($temp['Last Changed Date'], 5, 2)."/".substr($temp['Last Changed Date'], 0, 4)." ".substr($temp['Last Changed Date'], 11, 5);
	$this->m_proprio = $temp['Last Changed Author'];
    }

    /**
     * Méthode qui récupère la bonne image e fonction du type du fichier
     */
    private function actualiserImage() {
	$types = fileGetListOfSupportedImages();
	$ext = strtolower(substr($this->m_nom, strrpos($this->m_nom, '.') + 1));
	if ($types[$ext] != "")
	    $this->m_image =  "../img/files/".$types[$ext].".png";
	else
	    $this->m_image =  "../img/files/unknown.png";
    }

    /**
     * Méthode qui affiche une taille avec des unités "humaines"
     * @param int $size La taille en octets du fichier
     * @return string La taille formatée
     */
    private function nicesize($size) {
	if ($size >= 1024*1024*1024)
	    return round($size/(1024*1024*1024), 2)." Go";
	else if ($size >= 1024*1024)
	    return round($size/(1024*1024), 2)." Mo";
	else if ($size >= 1024)
	    return round($size/1024, 2)." Ko";
	else {
	    if ($size>1)
		return $size." octets";
	    else
		return $size." octet";
	}
    }

    /**
     * Méthode qui effectue la suppression d'un fichier du répertoire gnose
     * Elle met à jour l'entrepot svn juste après
     */
    public function effacerFichier() {
	$out = shell_exec('svn rm --non-interactive --force --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.$this->m_chemin."/".$this->m_nom);
	$out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Suppression du fichier ".$this->m_nom."\" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$this->m_chemin."/".$this->m_nom);
	$out .= shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy']);
    }

    /**
     * Getter du path complet du fichier
     * @return string Le path complet du fichier
     */
    public function path() {
	return $this->m_chemin."/".$this->m_nom;
    }

    /**
     * Getter du path de l'image associée
     * @return string Le path de l'image
     */
    public function image() {
	return $this->m_image;
    }

    /**
     * Getter de la taille du fichier
     * @return string La taille du fichier
     */
    public function taille() {
	return $this->m_taille;
    }

    /**
     * Getter du nom du fichier
     * @return string le nom du fichier
     */
    public function nom() {
	return $this->m_nom;
    }

    /**
     * Getter du proprio du fichier
     * @return string Le proprio SVN du fichier
     */
    public function proprio() {
	return $this->m_proprio;
    }

    /**
     * Getter de la date de dernière modification du fichier
     * @return string La date de la dernière modification SVN du fichier
     */
    public function dateM() {
	return $this->m_dateM;
    }

    /**
     * Getter du path du dossier parent au fichier
     * @return string Le path du dossier parent au fichier
     */
    public function chemin() {
	return $this->m_chemin;
    }

    /**
     * Gette du path du fichier, relatif au dossier GNOSE
     * @return string Le path du fichier relatif au dossier GNOSE
     */
    public function sousPath() {
	return $this->m_souspath;
    }

    /**
     * Méthode qui retourne le fichier au navigateur client
     * Permet de downloader le fichier
     */
    public function download() {
	$file = $this->m_chemin."/".$this->m_nom;
	$filetype = trim(shell_exec("file -bi ".$file));
	header('Content-disposition: attachment; filename='.$this->m_nom);/*Indique le nom*/
	header('Content-Type: '.$filetype);/*Indique le type*/
	header('Content-Length: '.filesize($file));/*Indique la taille pour permet au client de savoir le % de telechargement Ceci n'est pas obligatoire .*/
	header('Pragma: no-cache');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0, public');
	header('Expires: 0');
	readfile($file);
	exit;
    }

    /**
     * Méthode qui permet de renomer un fichier
     * @param string $nom Le nouveau nom du fichier (sans le path)
     * @return string Retourne les éventuels retours de SVN
     */
    public function renomer($nom) {
	$out = shell_exec('svn mv --non-interactive --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.$this->m_chemin."/".$this->m_nom.' '.$this->chemin."/".$nom);
	$out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Le fichier ".$this->nom()."se nomme à présent ".$nom."\" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$this->m_path);
	$out .= shell_exec('svn up --non-interactive '.$this->m_chemin);
	$this->m_nom = $nom;
	return $out;
    }

    /**
     * Méthode pour copier un fichier dans un autre répertoire
     * @param string $newpath Le nouveau path relatif au dossier GNOSE
     * @return string Le retour de SVN
     */
    public function copier($newpath) {
	$temp = explode('/', $newpath);
	$total = count($temp);
	for($k=1; $k<$total-1; $k++) {
	    $chemin .= '/'.$temp[$k];
	}
	if(!file_exists($newpath)) {
	    $out = shell_exec('svn cp --non-interactive --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.rtrim($this->m_chemin."/".$this->m_nom, "/").' '.$newpath);
	    $out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Le fichier ".$this->m_nom." a été copié dans ".$newpath." \" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$chemin);
	    $out .= shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy']);
	    $this->m_chemin = $chemin;
	    $this->m_nom = $temp[$total-1];
	}
	else {
	    $out = false;
	}
	return $out;
    }

    /**
     * Méthode pour déplacer un fichier dans un autre répertoire
     * @param string $newpath Le nouveau path relatif au dossier GNOSE
     * @return string Le retour de SVN
     */
    public function deplacer($newpath) {
	$temp = explode('/', $newpath);
	$total = count($temp);
	for($k=1; $k<$total-1; $k++) {
	    $chemin .= '/'.$temp[$k];
	}
	if(!file_exists($newpath)) {
	    $out = shell_exec('svn mv --non-interactive --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.rtrim($this->m_chemin."/".$this->m_nom, "/").' '.$newpath);
	    $out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Le fichier ".$this->m_nom." a été déplacé dans ".$newpath." \" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$GLOBALS['SVN_Pool1']['WorkCopy']);
	    $out .= shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy']);
	    $this->m_chemin = $chemin;
	}
	else {
	    $out = false;
	}
	return $out;
    }

    /**
     * Méthode pour récupérer tous les détails SVN du fichier
     * @return array Les détails SVN du fichier
     */
    public function detailFichier() {
	$info = libSvn::SVNInfo('', $this->m_souspath);
	$info['Last Changed Date'] = libSvn::SVNDateSvn2human($info['Last Changed Date']);
	$log = libSvn::SVNLog('', $this->m_souspath);
	return array($info, $log);
    }
}

/**
 * Classe qui gère l'affichage et le traitement
 * des dossiers dans le navigateur intégré de ZUNO
 *
 * @author Nicolas Mannocci
 * @version 1.1
 */
class documentViewRepertoire extends generalView {
    private $m_path; //Le path complet du répertoire
    private $m_repertoires; //Les sous répertoires directs de ce répertoire
    private $m_fichiers; //Les fichiers directs de ce répertoire
    private $m_isvide; //Booléen pour préciser si le dossier est vide
    private $m_isvisite; //Booléen pour préciser si le dossier a déjà été "regardé"
    private $m_proprio; //Le propriétaire SVN du dossier
    private $m_dateM; //La date de dernière modif SVN du dossier
    private $m_triche; //Booléen pour savoir si l'utilisateur a tenté d'user du navigateur hors du cadre normal d'utilisation
    private $m_rang; //Int qui indique le rang du dossier vis à vis du dossier principal de GNOSE
    private $m_lieu; //Indique si on est dans WORK ou ARCHIVES
    private $m_page; //Indique la page à afficher (WORK ou ARCHIVES)
    private $m_souspath; //Le path relatif au dossier GNOSE du dossier

    /**
     * Constructeur de la classe qui récupère toutes les informations sur le dossier
     * @param string $path Le path du dossier relatif au dossier GNOSE
     * @param string $lieu Précise si on est dans WORK (par défaut) ou dans ARCHIVES
     */
    function __construct($path='', $lieu = 'WORK/') {

	if(array_key_exists('out',$GLOBALS))
	    $GLOBALS['out']->setTitle('Répertoire "'.$path.'"','',true);
	if($lieu == $GLOBALS['SVN_Pool1']['ArchivesDir']) {
	    $pathDefaut = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['ArchivesDir'];
	    $this->m_lieu=$GLOBALS['SVN_Pool1']['ArchivesDir'];
	    $this->m_page = '../gnose/BrowseArchive.php';
	}
	else {
	    $pathDefaut = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'];
	    $this->m_lieu=$GLOBALS['SVN_Pool1']['WorkDir'];
	    $this->m_page = '../gnose/BrowseWork.php';
	}
	$path2 = $path;
	$path = $GLOBALS['SVN_Pool1']['WorkCopy'].$path;
	if($path == $GLOBALS['SVN_Pool1']['WorkCopy']) {
	    $this->m_path = $pathDefaut;
	    $this->m_rang = 1;
	    $this->m_triche = false;
	    $this->m_souspath = $this->m_lieu;
	}
	elseif(!is_dir($path)) {
	    $this->m_path = $pathDefaut;
	    $this->m_triche = true;
	    $this->m_souspath = $this->m_lieu;
	}
	else {
	    $temp = explode('/', $path);
	    $pd= (substr($pathDefaut,-1,1) == '/') ? substr($pathDefaut,0,-1) : $pathDefaut;
	    $temp2 = explode('/', $pd);
	    if(count($temp) < count($temp2)) {
		$this->m_path = $pathDefaut;
		$this->m_triche = true;
		$this->m_souspath = $this->m_lieu;
	    }
	    else {
		$erreur = 0;
		for($k=1; $k < count($temp2); $k++) {
		    if($temp[$k] == $temp2[$k]) {
			continue;
		    }
		    else {
			$erreur = 1;
			break;
		    }
		}
		if($erreur) {
		    $this->m_path = $pathDefaut;
		    $this->m_triche = true;
		    $this->m_souspath = $this->m_lieu;
		}
		else {
		    $this->m_path = $path;
		    $this->m_triche = false;
		    $this->m_rang = count($temp)-count($temp2);
		    $this->m_souspath = $path2;
		}
	    }
	}
	$temp = libSvn::SVNInfo('', $this->m_path);
	$this->m_dateM = substr($temp['Last Changed Date'], 8, 2)."/".substr($temp['Last Changed Date'], 5, 2)."/".substr($temp['Last Changed Date'], 0, 4)." ".substr($temp['Last Changed Date'], 11, 5);
	$this->m_proprio = $temp['Last Changed Author'];
	$this->m_isvisite = false;
    }

    /**
     * Méthode qui calcule la taille du répertoire
     * @param bool $recursive Précise si on veut la taille du dossier en incluant les sous dossiers ou juste le dossier et ses fichiers
     * @return string La taille du dossier
     */
    public function tailleRepertoire($recursive = true ) {
	$result = 0;
	if(!is_dir($this->m_path) || !is_readable($this->m_path))
	    return 0;
	$fd = dir($this->m_path);
	while($file = $fd->read()) {
	    if(($file != ".") && ($file != "..")) {
		if(@is_dir($this->m_path.'/'.$file.'/'))
		    $result += $recursive?$this->tailleRepertoire($this->m_path.'/'.$file.'/'):0;
		else
		    $result += filesize($this->m_path.'/'.$file);
	    }
	}
	$fd->close();
	return $this->nicesize($result);
    }

    /**
     * Méthode qui formate la taille du dossier dans une écriture "humaine"
     * @param int $size La taille du dossier en octet
     * @return string La taille du dossier
     */
    private function nicesize($size) {
	if ($size >= 1024*1024*1024)
	    return round($size/(1024*1024*1024), 2)." Go";
	else if ($size >= 1024*1024)
	    return round($size/(1024*1024), 2)." Mo";
	else if ($size >= 1024)
	    return round($size/1024, 2)." Ko";
	else {
	    if ($size>1)
		return $size." octets";
	    else
		return $size." octet";
	}
    }

    /**
     * Méthode qui cherche le contenu d'un dossier
     * @param bool $cache Indique si on affiche les fichiers et dossiers cachés ou pas
     * @param bool $recursif Indique si on regarde également le contenu des sous répertoires
     */
    private function recupererContenu($cache = false, $recursif = false) {
	$folder_nb = 0;
	$files_nb = 0;
	$dir = @opendir($this->m_path);
	$itemlist = scandir($this->m_path);
	closedir($dir);
	if ($itemlist === NULL) {
	    $this->m_isvide = true;
	}
	sort($itemlist,SORT_STRING);
	foreach ($itemlist as $item) {
	    if($cache) {
		if($item[0] == '.' and $item[1] == '.') {
		    continue;
		}
		elseif($item[0] == '.' and strlen($item) == 1) {
		    continue;
		}
	    }
	    else {
		if($item[0] == '.') {
		    continue;
		}
	    }
	    $aumoinsun = 1;
	    $this->m_isvide = false;
	    $relname = $this->m_souspath."/".$item;
	    if (is_dir($GLOBALS['SVN_Pool1']['WorkCopy'].$relname)) {
		$this->m_repertoires[$item] = new documentViewRepertoire($relname, $this->m_lieu);
		if($recursif)
		    $this->m_repertoires[$item]->recupererContenu();
	    }
	    else {
		$this->m_fichiers[$item] = new documentViewFichier($relname);
	    }
	}
	if($aumoinsun != 1) {
	    $this->m_isvide = true;
	}
	$this->m_isvisite = true;
    }

    /**
     * Méthode qui supprime le répertoire
     */
    public function effacerRepertoire() {
	if($this->m_path == $GLOBALS['SVN_Pool1']['WorkCopy'].$this->m_lieu) {
	    return false;
	}
	$out = shell_exec('svn rm --non-interactive --force --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.$this->m_path);
	$out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Suppression du répertoire ".$this->nom()."\" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$this->m_path);
	$out .= shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy'].$this->m_lieu);
    }

    /**
     * Méthode qui "prépare" l'affichage du répertoire dans le navigateur
     * @param bool $recursif Précise si on récupère tous les sous dossiers récursivement
     * @return array Les fichiers et répertoires du répertoire
     */
    public function preparerAffichage($recursif = false) {
	if($this->m_triche) {
	    return '<div class="err">Ce dossier n\'existe pas ou plus.</div>';
	}
	if(!$this->m_isvisite) {
	    $this->recupererContenu(false, $recursif);
	}
	return array($this->m_fichiers, $this->m_repertoires);
    }

    /**
     * Méthode qui regarde si pour un dossier donné, on peut afficher les options
     * Suppression, modification.
     * @return bool Indique s'il faut afficher les options
     */
    private function afficherOptions() {

	switch(substr($this->m_souspath, 0,14)) {
	    case $GLOBALS['SVN_Pool1']['WorkDir'].$GLOBALS['ZunoAffaire']['dir.affaire'] :
		if(!is_numeric(substr($this->m_souspath, 14,6)) and substr($this->m_souspath, 14,6) != "")
		    return true;
	    case $GLOBALS['SVN_Pool1']['WorkDir']."Cannevas/" :
		return false;
		break;
	    case $GLOBALS['SVN_Pool1']['WorkDir']."Factures/" :
		if(substr($this->m_souspath, 14,6) != "" and !(substr($this->m_souspath,14,7) == "Clients" or substr($this->m_souspath,14,12) == "Fournisseurs"))
		    return true;
		return false;
		break;

	}
	return true;
    }

    /**
     * Méthode qui génère le code HTML d'affichage des sous dossiers du dossier
     * @param array $dossier Les sous-dossiers du dossier
     * @param int $alternance Initialise l'alternance
     * @param string $mode Permet de préciser si on veut tout ou partie
     * @return string Le code HTML
     */
    private function afficherDossier($dossier, $alternance = 0, $mode = 'complet') {
	if(is_array($dossier)) {
	    foreach($dossier as $k => $v) {
		$marge = 18*($v->m_rang-1);

		if($mode == 'complet') {
		    $sortie .= '<div ouvert="false" class="altern'.$alternance.'" id="'.$v->sousPath().'" style="display:table-row;">';
		    $ligne = '<div class="cellule bg" style="display:table-cell;"><a title="Informations sur ce dossier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'?path='.$v->sousPath().'&action=detailsR&format=popup&nom='.$v->nom().'\',\'500\',\'300\',\'\',\'\',\'\',\'Informations\');"><img title="Info sur le dossier '.$k.'" alt="Info sur le dossier '.$k.'" src="../img/files/dir.png" style="margin-left:'.$marge.'px"/></a>';
		    $ligne .= ' <b><a title="'.$k.'" onclick="afficherContenu(\''.$this->m_page.'\', \'action=naviguer&style=ajax&rep='.$v->sousPath().'\', \''.$v->sousPath().'\');">'.$k.'</a></b></div>';
		    $ligne .= '<div class="cellule center bg" style="display:table-cell;width:30px;white-space:nowrap;padding-left:7px;padding-right:7px;"></div>';
		    $ligne .= '<div class="cellule center bg" style="display:table-cell;width:30px;white-space:nowrap;padding-left:7px;padding-right:7px;">'.$v->dateM().'</div><div class="cellule right bg" style="display:table-cell;white-space:nowrap;width:30px;padding-left:7px;padding-right:7px;"><a title="Détail sur l\'utilisateur '.$v->proprio().'" href="#" onclick="zuno.popup.open(\'../User.php\',\'type=popup&id='.$v->proprio().'\',\'300\',\'300\',\'\',\'\',\'\',\'User\');"><b>'.$v->proprio().'</b></a></div>';
		    if($this->m_lieu == $GLOBALS['SVN_Pool1']['WorkDir']) {
			if($v->afficherOptions())
			    $plus = '<a title="Modifier ce dossier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'path='.$v->sousPath().'&action=modifD&format=popup\',\'400\',\'150\',\'\',\'\',\'\',\'Modifier\');">
								<img alt="Modifier ce dossier" src="../img/gnose/modify.png"/>
								</a>
								<a title="Supprimer le dossier" href="'.$this->m_page.'?action=suppD&path='.$v->sousPath().'" onclick="return(confirmation(\'Êtes vous sur de vouloir supprimer ce dossier ?\'));">
								<img alt="Supprimer le dossier" src="../img/gnose/delete.png"/>
								</a>
								<a title="Copier le dossier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'?path='.$v->sousPath().'&action=copierD&format=popup&nom='.$v->nom().'\',\'700\',\'350\',\'\',\'\',\'\',\'Copier\');">
								<img alt="Copier le dossier" src="../img/gnose/copy.png"/>
								</a>
								<a title="Déplacer le dossier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'?path='.$v->sousPath().'&action=deplacerD&format=popup&nom='.$v->nom().'\',\'700\',\'350\',\'\',\'\',\'\',\'Déplacer\');">
								<img alt="Déplacer le dossier" src="../img/gnose/move.png"/>
								</a>
								<a title="Archiver le dossier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'?path='.$v->sousPath().'&action=archiverD&format=popup&nom='.$v->nom().'\',\'700\',\'350\',\'\',\'\',\'\',\'Archiver\');">
								<img alt="Archiver le dossier" src="../img/gnose/archive.png"/>
								</a>';

			$ligne .= '<div class="cellule right bg" style="display:table-cell;white-space:nowrap;width:30px;padding-left:7px;padding-right:7px;">
								'.$plus.'</div>';
		    }
		    else
			$ligne .= '<div class="cellule right bg" style="display:table-cell;white-space:nowrap;width:30px;padding-left:7px;padding-right:7px;">
								<a title="Supprimer le dossier" href="'.$this->m_page.'?action=suppD&path='.$v->sousPath().'" onclick="return(confirmation(\'Êtes vous sur de vouloir supprimer ce dossier ?\'));">
								<img alt="Supprimer le dossier" src="../img/gnose/delete.png"/>
								</a>
								</div>';
		    $sortie .= $ligne.'</div>';
		}
		elseif($mode == 'light') {
		    $sortie .= '<div ouvert="false" class="altern'.$alternance.'" id="a'.$v->sousPath().'" style="display:table-row;">';
		    $ligne = '<div class="cellule bg" style="display:table-cell;"><a title="'.$k.'" onclick="afficherContenu2(\''.$this->m_page.'\', \'action=naviguer&style=ajax&sortie=popup&rep='.$v->sousPath().'\', \'a'.$v->sousPath().'\', \''.$k.'\');"><img title="Info sur le dossier '.$k.'" alt="Info sur le dossier '.$k.'" src="../img/files/dir.png" style="margin-left:'.$marge.'px"/>';
		    $ligne .= '<b>'.$k.'</a></b></div>';
		    $sortie .= $ligne.'</div>';
		}

		$alternance = ($alternance+1)%2;
		if(is_array($v->m_repertoires)) {
		    $sortie .= $this->afficherDossier($v->m_repertoires, $alternance, $k);
		}
		if(is_array($v->m_fichiers)) {
		    $sortie .= $this->afficherFichier($v->m_fichiers, $alternance, $marge+18, $k);
		}
	    }
	}
	return $sortie;
    }

    /**
     * Méthode pour afficher les fichiers d'un dossier
     * @param array $fichiers Les fichiers du dossier
     * @param int $alternance Intialise l'alternance
     * @param int $marge Indique la marge à laisser à gauche pour faire une hiérarchisation
     * @return string Le code HTML
     */
    private function afficherFichier($fichiers, $alternance, $marge) {
	if(is_array($fichiers)) {
	    foreach($fichiers as $k => $v) {
		$sortie .= '<div class="altern'.$alternance.'" style="display:table-row;" id="'.$v->sousPath().'">';
		$ligne = '<div class="cellule bg" style="display:table-cell;"><a title="Informations sur ce fichier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'?path='.$v->sousPath().'&action=detailsF&format=popup&nom='.$v->nom().'\',\'500\',\'300\',\'\',\'\',\'\',\'Informations\');">
							<img title="Info sur le fichier '.$k.'" alt="Info sur le fichier '.$k.'" src="'.$v->image().'" style="margin-left:'.$marge.'px"/>
							</a>';
		$ligne .= '<b><a title="'.$k.'" href="'.$this->m_page.'?action=download&fich='.$v->sousPath().'">'.$k.'</a>
							</b></div>';
		$ligne .= '<div class="cellule center bg" style="display:table-cell;white-space:nowrap;">'.$v->taille().'</div>';
		$ligne .= '<div class="cellule right bg" style="display:table-cell;padding-left:7px;padding-right:7px;white-space:nowrap;">
							'.$v->dateM().'</div>
							<div class="cellule right bg" style="display:table-cell;padding-left:7px;padding-right:7px;white-space:nowrap;"><a title="Détail sur l\'utilisateur '.$v->proprio().'" href="#" onclick="zuno.popup.open(\'../User.php\',\'type=popup&id='.$v->proprio().'\',\'300\',\'300\',\'\',\'\',\'\',\'User\');">
							<b>root</b>
							</a></div>';
		if($this->m_lieu == $GLOBALS['SVN_Pool1']['WorkDir'])
		    $ligne .= '<div class="cellule right bg" style="display:table-cell;padding-left:7px;padding-right:7px;white-space:nowrap;">
							<a title="Modifier ce fichier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'?action=modifF&path='.$v->sousPath().'&format=popup\',\'400\',\'150\',\'\',\'\',\'\',\'Uploader\');">
							<img alt="Modifier ce fichier" src="../img/gnose/modify.png"/>
							</a>
							<a title="Supprimer le fichier" href="'.$this->m_page.'?action=suppF&path='.$v->sousPath().'" onclick="return(confirmation(\'Êtes vous sur de vouloir supprimer ce fichier ?\'));">
							<img alt="Supprimer le fichier" src="../img/gnose/delete.png"/>
							</a>
							<a title="Copier le fichier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'?path='.$v->sousPath().'&action=copierF&format=popup&nom='.$k.'\',\'700\',\'350\',\'\',\'\',\'\',\'Copier\');">
							<img alt="Copier le fichier" src="../img/gnose/copy.png"/>
							</a>
							<a title="Déplacer le fichier" onclick="zuno.popup.open(\''.$this->m_page.'\',\'?path='.$v->sousPath().'&action=deplacerF&format=popup&nom='.$k.'\',\'700\',\'350\',\'\',\'\',\'\',\'Déplacer\');">
							<img alt="Déplacer le fichier" src="../img/gnose/move.png"/>
							</a>
							<a title="Envoyer le fichier" onclick="zuno.popup.open(\'../gnose/Send.php\',\'?file='.$v->sousPath().'\',\'500\',\'400\',\'\',\'\',\'resize\',\'Envoyer\');">
							<img alt="Envoyer le fichier" src="../img/gnose/send.png"/>
							</a>
							</div>';
		else
		    $ligne .= '<div class="cellule right bg" style="display:table-cell;padding-left:7px;padding-right:7px;white-space:nowrap;">
							<a title="Supprimer le fichier" href="'.$this->m_page.'?action=suppF&path='.$v->sousPath().'" onclick="return(confirmation(\'Êtes vous sur de vouloir supprimer ce fichier ?\'));">
							<img alt="Supprimer le fichier" src="../img/gnose/delete.png"/>
							</a>
							<a title="Envoyer le fichier" onclick="zuno.popup.open(\'../gnose/Send.php\',\'?file='.$v->sousPath().'\',\'500\',\'400\',\'\',\'\',\'resize\',\'Envoyer\');">
							<img alt="Envoyer le fichier" src="../img/gnose/send.png"/>
							</a></div>';
		$sortie .= $ligne.'</div>';
		$alternance = ($alternance+1)%2;
	    }
	}
	return $sortie;
    }


    /**
     * Méthode qui permet d'afficher le contenu d'un dossier
     * @param string $tout Précise si on met le Javascript ou non
     * @param bool $recursif Précise si on veut le contenu des sous dossiers récursivement
     * @param string $mode Précise le mode d'affichage
     * @return string Le code HTML généré
     */
    public function afficher($tout = '', $recursif = false, $mode = 'complet') {
	$temp = $this->preparerAffichage($recursif);
	if(!is_array($temp)) {
	    return $temp;
	}
	if($tout == '') {
	    $js = '<script type="text/javascript">
			function confirmation(message) {if(confirm(message)){return true;} else {return false;}}
                        function verifAutocompleteur(champ) {if(champ.value == "" || champ.value == null) {$(champ.id+\'hidden\').value = "";} else if(!$(champ.id+\'_choix\').hasChildNodes()) {$(champ.id+\'hidden\').value = champ.value;} }
		</script>';
	    if($mode == 'complet') {
		$sortie = '<div id="messageGnose"></div>';
		$sortie .= '<div class="blockTable"><div class="tableau" style="display:table;">';
		$sortie .= '<div class="titre" style="display:table-row;">';
		$sortie .= '<div class="celluleH" style="display:table-cell;">Nom</div>
							<div class="celluleH" style="display:table-cell;">Poids</div>
							<div class="celluleH center" style="display:table-cell;" title="Date de modification">Date</div>
							<div class="celluleH center" style="display:table-cell;" title="Auteur de la modification">Auteur</div>
							<div class="celluleH right" style="display:table-cell;">Actions</div></div>';
	    }
	    elseif($mode == 'dossierlight' or $mode == 'fichierlight' or $mode == 'light') {
		$sortie = '<div class="blockTable"><div class="tableau" style="display:table;">';
	    }
	}
	if($mode == 'complet') {
	    $sortie .= $this->afficherDossier($this->m_repertoires, 0);
	    $sortie .= $this->afficherFichier($this->m_fichiers, 0, ($this->m_rang)*18);
	}
	elseif($mode == 'dossierlight') {
	    $sortie .= $this->afficherDossier($this->m_repertoires, 0, 'light');
	}
	if($tout == '') {
	    $sortie .= '</div></div>'.$js;
	}
	return $sortie;
    }

    /**
     * Getter du path complet du dossier
     * @return string Le path du dossier
     */
    public function path() {
	return $this->m_path;
    }

    /**
     * Méthode pour créer un sous dossier
     * @param string $nom Le nom du sous dossier
     * @return string Indique si tout s'est bien passé
     */
    public function creerSousDossier($nom) {
	if(is_dir($this->m_path."/".$nom)) {
	    return false;
	}
	else {
	    $temp = explode('/', $this->m_path);
	    $total = count($temp);
	    for($k=1; $k<$total-1; $k++) {
		$chemin .= '/'.$temp[$k];
	    }
	    $out = shell_exec('svn mkdir --non-interactive --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.rtrim($this->m_path, "/")."/".$nom);
	    $out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Le répertoire ".$nom." vient d'être créé \" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$chemin);
	    $out .= shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy'].$this->m_lieu);
	    return $out;
	}
    }

    /**
     * Méthode pour renomer un dossier
     * @param string $nom Le nouveau nom du dossier
     * @return string Le retour de SVN
     */
    public function renomer($nom) {
	$temp = explode('/', $this->m_path);
	$total = count($temp);
	for($k=1; $k<$total-1; $k++) {
	    $chemin .= '/'.$temp[$k];
	}
	$newpath = $chemin."/".$nom;
	if(!file_exists($newpath)) {
	    $out = shell_exec('svn mv --non-interactive --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.rtrim($this->m_path, "/").' '.$newpath);
	    $out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Le répertoire ".$this->nom()." se nomme à présent ".$nom."\" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$chemin);
	    $out .= shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy'].$this->m_lieu);
	    $this->m_path = $newpath;
	}
	else {
	    $out = false;
	}
	return $out;
    }

    /**
     * Méthode pour copier un dossier
     * @param string $newpath Le path du nouveau dossier
     * @return string Le retour de SVN
     */
    public function copier($newpath) {
	$temp = explode('/', $newpath);
	$total = count($temp);
	for($k=1; $k<$total-1; $k++) {
	    $chemin .= '/'.$temp[$k];
	}
	if(!file_exists($newpath)) {
	    $out = shell_exec('svn cp --non-interactive --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.rtrim($this->m_path, "/").' '.$newpath);
	    $out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Le répertoire ".$this->nom()." a été copié dans ".$newpath." \" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$chemin);
	    $out .= shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy'].$this->m_lieu);
	    $this->m_path = $newpath;
	}
	else {
	    $out = false;
	}
	return $out;
    }

    /**
     * Méthode pour déplacer un dossier
     * @param string $newpath Le path du nouveau dossier
     * @return string Le retour de SVN
     */
    public function deplacer($newpath) {
	if(!file_exists($newpath)) {
	    $out = shell_exec('svn mv --non-interactive --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' '.rtrim($this->m_path, "/").' '.$newpath);
	    $out .= shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Le répertoire ".$this->nom()." a été déplacé dans ".$newpath." \" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$GLOBALS['SVN_Pool1']['WorkCopy']);
	    $out .= shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy']);
	    $this->m_path = $newpath;
	}
	else {
	    $out = false;
	}
	return $out;
    }

    /**
     * Getter du path relatif du dossier
     * @return string Le path du dossier
     */
    public function sousPath() {
	return $this->m_souspath;
    }

    /**
     * Getter du path complet du dossier parent
     * @return string Le path du dossier parent
     */
    public function chemin() {
	$temp = explode('/', $this->m_path);
	$total = count($temp);
	for($k=1; $k<$total-1; $k++) {
	    $chemin .= '/'.$temp[$k];
	}
	return $chemin;
    }

    /**
     * Getter du nom du dossier
     * @return string Le nom du dossier
     */
    public function nom() {
	$temp = explode('/', $this->m_path);
	$total = count($temp);
	return $temp[$total-1];
    }

    /**
     * Getter du propriétaire SVN du dossier
     * @return string Le propriétaire du dossier
     */
    public function proprio() {
	return $this->m_proprio;
    }

    /**
     * Getter de la date de dernière modif SVN du dossier
     * @return string La date de dernière modif du dossier
     */
    public function dateM() {
	return $this->m_dateM;
    }

    /**
     * Getter du nombre de sous dossiers du dossier
     * @return int Le nombre de sous dossiers
     */
    public function totalReps() {
	return count($this->m_repertoires);
    }

    /**
     * Getter du nombre de fichiers du dossier
     * @return int Le nombre de fichiers
     */
    public function totalFichiers() {
	return count($this->m_fichiers);
    }

    /**
     * Méthode qui récupère toutes les informations SVN d'un répertoire
     * @return array Les informations
     */
    public function detailRepertoire() {
	$info = libSvn::SVNInfo('', $this->m_souspath);
	$info['Last Changed Date'] = libSvn::SVNDateSvn2human($info['Last Changed Date']);
	$log = libSvn::SVNLog('', $this->m_souspath);
	return array($info, $log);
    }
}
?>
