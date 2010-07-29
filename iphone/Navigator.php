<?php
// On inclus les librairies et les fichiers de configurations
include ('../inc/conf.inc');
include ('../inc/core.inc');
loadPlugin(array('ZView/FactureView','OOConverter','Send/Send'));
include_once ('lib/Debug.inc.php');
include_once ('lib/HtmlElement.inc.php');
include_once ('V/GeneralView.inc.php');
ob_start();
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>';
$PC = new PageContext('iPhone');
$PC->GetVarContext();
$PC->GetChannelContext();
if($PC->GetSessionContext('',false) === false) {
    echo HtmlElementIphone::redirectOnSessionEnd();
    ob_end_flush();
    exit;
}
aiJeLeDroit('navigator', '05');
function nicesize($size) {
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
function DirSize($path , $recursive=TRUE) {
    $result = 0;
    if(!is_dir($path) || !is_readable($path))
	return 0;
    $fd = dir($path);
    while($file = $fd->read()) {
	if(($file != ".") && ($file != "..")) {
	    if(@is_dir($path.'/'.$file.'/'))
		$result += $recursive?DirSize($path.'/'.$file.'/'):0;
	    else
		$result += filesize($path.'/'.$file);
	}
    }
    $fd->close();
    return $result;
}
function getImg($file) {
    $types = array ("torrent"=>"bt", "iso"=>"cdimage", "mdf"=>"cdimage", "mds"=>"cdimage", "deb"=>"deb", "doc"=>"document", "odt"=>"document", "swx"=>"document", "exe"=>"exec_win", "temp"=>"file_temporary", "ttf"=>"font", "html"=>"html", "htm"=>"html", "xhtml"=>"html", "asp"=>"html", "aspx"=>"html", "php"=>"html", "php3"=>"html", "php4"=>"html", "php5"=>"html", "xml"=>"html", "png"=>"image", "gif"=>"image", "jpg"=>"image", "jpeg"=>"images", "bmp"=>"image", "wbmp"=>"image", "log"=>"log", "mid"=>"midi", "mod"=>"midi", "sid"=>"midi", "xm"=>"midi", "pdf"=>"pdf", "ps"=>"postscript", "mov"=>"quicktime", "readme"=>"readme", "nfo"=>"readme", "rpm"=>"rpm", "sh"=>"shellscript", "mp3"=>"sound", "ogg"=>"sound", "wav"=>"sound", "au"=>"sound", "c"=>"source_c", "cpp"=>"source_cpp", "f"=>"source_f", "h"=>"source_h", "j"=>"source_j", "jar"=>"source_java", "java"=>"source_java", "l"=>"source_l", "moc"=>"source_moc", "o"=>"source_moc", "o"=>"souce_o", "p"=>"source_p", "pl"=>"source_pl", "py"=>"source_py", "s"=>"source_s", "y"=>"source_y", "tar"=>"tar", "gz"=>"tar", "rar"=>"tar", "bz"=>"tar", "bz2"=>"tar", "zip"=>"tar", "ace"=>"tar", "tex"=>"tex", "txt"=>"txt", "svg"=>"vector", "svgx"=>"vector", "avi"=>"video", "wmv"=>"video");
    $ext = strtolower(substr($file, strrpos($file, '.') + 1));
    if ($types[$ext] != "")
	return "../img/files/".$types[$ext].".png";
    else
	return "../img/files/unknown.png";
}

function Naviguer($dirname = '', $nombre = 0) {
    $taillemax = $GLOBALS['SVN_Pool1']['NbCharAffIphone'];
    if($dirname == "") $dirname = $GLOBALS['SVN_Pool1']['WorkCopy'].$GLOBALS['SVN_Pool1']['WorkDir'];
    if(is_dir($dirname)) {
	$folder_nb = 0;
	$files_nb = 0;
	$dir = @opendir($dirname);
	$itemlist = scandir($dirname);
	closedir($dir);
	if ($itemlist === NULL) {
	    return '<div class="msg">Aucun fichier ou dossier dans ce dossier</div>';
	}
	sort($itemlist,SORT_STRING);
	$out = '<div class="iPanel"><fieldset><ul class="iArrow" id="dossiersNavigateur'.$nombre.'">';
	foreach ($itemlist as $item) {
	    if($item[0] == '.') {
		continue;
	    }
	    $aumoinsunaffiche = 1;
	    $relname = $dirname."/".$item;
	    if (is_dir($relname)) {
		if(strlen($item) > 25 && strlen($item) < 35) {
		    $taille = 'style="font-size:13px;"';
		}
		elseif(strlen($item) > 34) {
		    $taille = 'style="font-size:10px;"';
		}
		else {
		    $taille = '';
		}
		//			$tailleDossier = nicesize(DirSize($relname));
		$out .= '<li>';
		$out .= '<a href="Navigator.php?file='.$relname.'&nombre='.$nombre.'" rev="async" '.$taille.' >';
		$out .= '<img src="../img/files/folder.gif" alt="folder"/>  '.$item.'</a>';
		$out .= '<span title="'.str_replace('"', '\"', $relname).'"></span></li>';
		$folder_nb++;
	    }
	    else {
		$files[$files_nb] = $item;
		$files_nb++;
	    }
	}
	if($aumoinsunaffiche != 1) {
	    return '<div class="msg">Aucun fichier ou dossier dans ce dossier</div>';
	}
	$out .= '</ul></fieldset><fieldset><ul class="iArrow" id="fichiersNavigateur'.$nombre.'">';
	if ($files_nb>0) {
	    $i = $folder_nb;
	    foreach ($files as $item) {
		$relname = $dirname."/".$item;
		if(strlen($item) > 25 && strlen($item) < 35) {
		    $taille = 'style="font-size:13px;"';
		}
		elseif(strlen($item) > 34) {
		    $taille = 'style="font-size:10px;"';
		}
		else {
		    $taille = '';
		}
		$out .= '<li><a href="./inc/explorer.php?download='.$relname.'" target="_blank" '.$taille.' ><img src="'.getImg($item).'" alt="img" />  '.$item.'</a></li>';
		$i++;
	    }
	}
	$out .= '</div>';
    }
    else {
	return '<div class="err">Aucun dossier '.$dirname.' disponible dans cette instance de Zuno</div>';
    }
    return $out;
}

if($PC->rcvG['action'] == 'racine') {
    ?>
<root><go to="waNavigator"/>
    <title set="waNavigator"><?php echo 'Racine'; ?></title>
    <part><destination mode="replace" zone="waNavigator" create="true"/>
	<data><![CDATA[ <?php echo Naviguer(); ?> ]]></data>
    </part>
    <script><![CDATA[ adaptTextNavigator(0); ]]></script>
</root>
    <?php
}
elseif($PC->rcvG['file'] != NULL) {
    $pos = strripos($PC->rcvG['file'], '/');
    $titre = substr($PC->rcvG['file'], $pos+1, strlen($PC->rcvG['file']));
    $nombre = $PC->rcvG['nombre'];
    $zone = "waNavigator".$nombre;
    $nombre++;
    ?>
<root><go to="<?php echo $zone; ?>"/>
    <title set="<?php echo $zone; ?>"><?php echo $titre; ?></title>
    <part><destination mode="replace" zone="<?php echo $zone; ?>" create="true"/>
	<data><![CDATA[ <?php echo Naviguer($PC->rcvG['file'], $nombre); ?> ]]></data>
    </part>
</root>
    <?php
}

elseif($PC->rcvG['download'] != NULL) {
    $path = $PC->rcvG['download'];
    $name=explode("/",$path);
    $name = $name[sizeof($name)-1];
    readfile($path);
}

?>
