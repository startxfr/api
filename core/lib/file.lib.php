<?php
/* #########################################################################
  #
  #   name :       file.inc
  #   desc :       library for file management
  #   categorie :  core module
  #   ID :  	 $Id$
  #
  #   copyright:   See licence.txt for this script licence
  ######################################################################### */

/* ------------------------------------------------------------------------+
  | Return content of the given file
  +------------------------------------------------------------------------ */
function FileCleanFileName($filename, $type = "") {
    $filename = trim($filename);
    if ($filename != '') {
        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r'
        );
        $tableToLower = array(
            'Š' => 's', 'š' => 's', 'Đ' => 'dj', 'đ' => 'dj', 'Ž' => 'z', 'ž' => 'z', 'Č' => 'c', 'č' => 'c', 'Ć' => 'c', 'ć' => 'c',
            'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ã' => 'a', 'Ä' => 'a', 'Å' => 'a', 'Æ' => 'a', 'Ç' => 'c', 'È' => 'e', 'É' => 'e',
            'Ê' => 'e', 'Ë' => 'e', 'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'Ñ' => 'n', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o',
            'Õ' => 'o', 'Ö' => 'o', 'Ø' => 'o', 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'Ý' => 'y', 'Þ' => 'b', 'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'r', 'ŕ' => 'r'
        );
        $tableSign = array(
            '{' => '_', '}' => '_', '<' => '_', '>' => '_', '(' => '_', ')' => '_', ',' => '_', '*' => '_', '!' => '_', '%' => '_',
            'µ' => '_', '¥' => '_', '#' => '_', '~' => '_', '|' => '_', '@' => '_', '`' => '_', '£' => '_', '°' => '_', '€' => '_',
            ';' => '_', '\\' => '_', ':' => '_'
        );
        $tableApos = array(
            '`' => '_', '""' => '_', '\'' => '_', '\\' => '_'
        );
        if ($type == 'SVN_PROP' or $type == 'FILE_PATH')
            $table = array_merge($table, $tableSign, array(' ' => '_'));
        elseif ($type == 'FILE_PATH_LOWER')
            $table = array_merge($tableToLower, $tableSign, $tableApos, array(' ' => '_'));
        elseif ($type == 'TOLOWER')
            $table = $tableToLower;
        elseif ($type == 'APOSTROPHE')
            $table = array_merge($tableApos, array(' ' => '_'));
        else
            $table = array_merge($table, $tableSign, $tableApos, array(' ' => '_'));

        $out = strtr($filename, $table);
        $out = str_replace('__', '_', $out);
        $out = str_replace('__', '_', $out);
        $out = str_replace('__', '_', $out);
    }
    return $out;
}

/* ------------------------------------------------------------------------+
  | Return content of the given file
  +------------------------------------------------------------------------ */
function FileReadFile($file) {
    if (FileIsFileExist($file)) {
        $fp = fopen($file, "r");
        if ($fp !== false) {
            $sortie = @fread($fp, filesize($file));
            fclose($fp);
        }
    } else {
        $sortie = FileIsFileExist($file);
    }
    return $sortie;
}

/* ------------------------------------------------------------------------+
  | FileAddLine2File
  +------------------------------------------------------------------------ */
function FileAddLine2File($filename, $text) {
    if (!FileIsFileExist($filename)) {
        touch($filename);
    }

    $text = $text . "\r\n";
    $file = fopen($filename, 'r+b'); // binary update mode
    if ($file !== false) {
        while (!feof($file)) {
            $ff = fgets($file, 1024);
        }
        fputs($file, $text, strlen($text));
        fclose($file);
    }
}

/* ------------------------------------------------------------------------+
  | FileAddLine2File
  +------------------------------------------------------------------------ */
function File_Add2File($filename, $text, $replace = FALSE) {
    if (!FileIsFileExist($filename)) {
        touch($filename);
    }
    if ($replace) {
        unlink($filename);
        touch($filename);
    }

    $text = $text . "\r\n";
    $file = fopen($filename, 'r+b'); // binary update mode
    if ($file !== false) {
        while (!feof($file)) {
            $ff = fgets($file, 1024);
        }
        fputs($file, $text, strlen($text));
        fclose($file);
    }
}

/* ------------------------------------------------------------------------+
  | FileIsFileExist
  +------------------------------------------------------------------------ */
function FileIsFileExist($filename) {
    if (!($fp = @fopen($filename, "r")))
        return FALSE;
    else
        return TRUE;
}

/* ------------------------------------------------------------------------+
  | FileMoveUploaded
  +------------------------------------------------------------------------ */
function FileMoveUploaded($tmpfile, $newname, $repertoire = '') {
    if ($repertoire == '') {
        $repertoire = $GLOBALS['REP']['appli'] . $GLOBALS['REP']['tmp'];
    }
    if (rename($tmpfile, $repertoire . $newname)) {
        return true;
    } else {
        return false;
    }
}

/* ------------------------------------------------------------------------+
  | FileGetExtention
  +------------------------------------------------------------------------ */
function FileGetExtention($filename) {
    $chaine = explode(".", $filename);
    $id = count($chaine) - 1;
    return strtolower($chaine[$id]);
}

/* ------------------------------------------------------------------------+
  | FileConvertSize2Human
  +------------------------------------------------------------------------ */
function FileConvertSize2Human($bytes) {
    if ($bytes >= 1099511627776) {
        $return = round($bytes / 1024 / 1024 / 1024 / 1024, 2);
        $suffix = "To";
    } elseif ($bytes >= 1073741824) {
        $return = round($bytes / 1024 / 1024 / 1024, 2);
        $suffix = "Go";
    } elseif ($bytes >= 1048576) {
        $return = round($bytes / 1024 / 1024, 2);
        $suffix = "Mo";
    } elseif ($bytes >= 1024) {
        $return = round($bytes / 1024, 2);
        $suffix = "Ko";
    } else {
        $return = $bytes;
        $octal = TRUE;
        $suffix = " octets";
    }
    return $return . " " . $suffix;
}

/* ------------------------------------------------------------------------+
  | FileOutputType
  +------------------------------------------------------------------------ */
function FileOutputType($filename, $output = 'name') {
    $types = fileGetListOfSupportedImages();
    $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
    $ext = FileGetExtention($filename);
    if (($output == 'image') or ( $output == 'image_right')) {
        if ($output == 'image_right')
            $align = 'right';
        if ($types[$ext] != "")
            $sortie = imageTag(getStaticUrl('img') . 'files/' . $types[$ext] . '.png', $ext, $align);
        else
            $sortie = imageTag(getStaticUrl('img') . 'files/unknown.png', $ext, $align);
    }
    else {
        if ($ext != '')
            $sortie = strtolower($ext);
        else
            $sortie = 'Inconnu';
    }
    return $sortie;
}

function fileGetListOfSupportedImages() {
    return array(
        'torrent' => 'bt',
        'iso' => 'cdimage',
        'mdf' => 'cdimage',
        'mds' => 'cdimage',
        'deb' => 'deb',
        'doc' => 'document',
        'docx' => 'document',
        'odt' => 'document',
        'swx' => 'document',
        'xls' => 'calc',
        'xlsx' => 'calc',
        'ods' => 'calc',
        'sws' => 'calc',
        'csv' => 'calc',
        'ppt' => 'presentation',
        'pptx' => 'presentation',
        'odp' => 'presentation',
        'swt' => 'presentation',
        'exe' => 'exec_win',
        'bat' => 'exec_win',
        'ttf' => 'font',
        'html' => 'html',
        'htm' => 'html',
        'xhtml' => 'html',
        'asp' => 'html',
        'aspx' => 'html',
        'php' => 'html',
        'php3' => 'html',
        'php4' => 'html',
        'php5' => 'html',
        'xml' => 'html',
        'png' => 'image',
        'gif' => 'image',
        'jpg' => 'image',
        'jpeg' => 'images',
        'bmp' => 'image',
        'wbmp' => 'image',
        'log' => 'log',
        'mid' => 'midi',
        'mod' => 'midi',
        'sid' => 'midi',
        'xm' => 'midi',
        'pdf' => 'pdf',
        'ps' => 'postscript',
        'mov' => 'quicktime',
        'readme' => 'readme',
        'nfo' => 'readme',
        'rpm' => 'rpm',
        'sh' => 'shellscript',
        'mp3' => 'sound',
        'ogg' => 'sound',
        'wav' => 'sound',
        'au' => 'sound',
        'c' => 'source',
        'cpp' => 'source',
        'f' => 'source',
        'h' => 'source',
        'j' => 'source',
        'jar' => 'source',
        'java' => 'source',
        'l' => 'source',
        'moc' => 'source',
        'o' => 'source',
        'o' => 'souce',
        'p' => 'source',
        'pl' => 'source',
        'py' => 'source',
        's' => 'source',
        'y' => 'source',
        'tar' => 'tar',
        'gz' => 'tar',
        'rar' => 'tar',
        'bz' => 'tar',
        'bz2' => 'tar',
        'zip' => 'tar',
        'ace' => 'tar',
        'tex' => 'tex',
        'txt' => 'txt',
        'svg' => 'vector',
        'svgx' => 'vector',
        'avi' => 'video',
        'mkv' => 'video',
        'flv' => 'video',
        'wmv' => 'video');
}

/* ------------------------------------------------------------------------+
  | FileDirectoryDetail
  +------------------------------------------------------------------------ */
function FileDirectoryDetail($rep, $ext = '', $profondeur = '') {
    //on test le repertoire
    if (!$rep or ! is_dir($rep))
        return false;
    //on initialise la profondeur
    if (($profondeur == 'all') or ( $profondeur == ''))
        $profondeur = '10';
    if ($profondeur != '0')
        $nextprof = $profondeur - 1;
    else
        $nextprof = 0;

    //on initialise les extentions
    if (is_array($ext))
        $testext = TRUE;
    else
        $testext = FALSE;

    //on commence l'analyse
    $handle = opendir($rep);
    $i = 0;
    // on parcours le repertoire
    for (; ($contenu = readdir($handle));) {
        if ($contenu != '.' && $contenu != '..' && $contenu != '.svn') {
            $chemin = $rep . $contenu;
            // si c'est un repertoire
            if (is_dir($chemin)) {
                // si ce n'est pas la profonder max on va voir
                if ($nextprof != '0')
                    $output[$contenu] = FileDirectoryDetail($chemin . '/', $ext, $nextprof);
                // si c'est la profondeur max on r�cupere les infos
                else {
                    $output[$contenu]['nom'] = $contenu;
                    $output[$contenu]['type'] = 'repertoire';
                    $size = FileGetDirectorySize($chemin);
                    $output[$contenu]['size'] = FileConvertSize2Human($size);
                    $output[$contenu]['Osize'] = $size;
                    $output[$contenu]['date'] = DateTimestamp2Univ(filemtime($chemin));
                }
            }
            // si c'est un fichier
            elseif (is_file($chemin)) {
                $detfile = pathinfo($chemin);
                if ($testext) {
                    foreach ($ext as $idsf => $extention) {
                        if ($extention == $detfile['extension']) {
                            $output[$i]['nom'] = $contenu;
                            $output[$i]['type'] = $detfile['extension'];
                            $output[$i]['size'] = FileConvertSize2Human(filesize($chemin));
                            $output[$i]['Osize'] = FileConvertSize2Human(filesize($chemin));
                            //	$output[$i]['Osize']= FileGetDirectorySize($chemin);
                            $output[$i]['date'] = DateTimestamp2Univ(filemtime($chemin));
                            $i++;
                        }
                    }
                } else {
                    $output[$i]['nom'] = $contenu;
                    $output[$i]['type'] = $detfile['extension'];
                    $output[$i]['size'] = FileConvertSize2Human(filesize($chemin));
                    $output[$i]['Osize'] = filesize($chemin);
                    $output[$i]['date'] = DateTimestamp2Univ(filemtime($chemin));
                    $i++;
                }
            }
        }
    }
    closedir($handle);
    return $output;
}

function FileGetDirectorySize($target, $output = false) {
    if (is_dir($target)) {
        $sourcedir = opendir($target);
        while (false !== ($filename = readdir($sourcedir))) {
            if ($output)
                echo 'Processing: ' . $target . '/' . $filename . '<br>';
            if ($filename != '.' && $filename != '..' && $filename != '.svn') {
                if (is_dir($target . '/' . $filename))
                // recurse subdirectory; call of function recursive
                    $totalsize += FileGetDirectorySize($target . '/' . $filename, $output);
                elseif (is_file($target . '/' . $filename))
                    $totalsize += filesize($target . '/' . $filename);
            }
        }
        closedir($sourcedir);
        return $totalsize;
    }
    // si c'est un fichier
    elseif (is_file($target)) {
        return filesize($target);
    }
}

/**
 * rm() -- Vigorously erase files and directories.
 *
 * @param $fileglob mixed If string, must be a file name (foo.txt), glob pattern (*.txt), or directory name.
 *                        If array, must be an array of file names, glob patterns, or directories.
 */
if (!function_exists('rm')) {
    function rm($fileglob) {
        if (is_string($fileglob)) {
            if (is_file($fileglob))
                return unlink($fileglob);
            elseif ((is_dir($fileglob))) {
                $ok = rm("$fileglob/*");
                if (!$ok)
                    return false;
                return rmdir($fileglob);
            }
            else {
                $matching = glob($fileglob);
                if ($matching === false) {
                    trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
                    return false;
                }
                $rcs = array_map('rm', $matching);
                if (in_array(false, $rcs))
                    return false;
            }
        }
        elseif (is_array($fileglob)) {
            $rcs = array_map('rm', $fileglob);
            if (in_array(false, $rcs))
                return false;
        }
        else {
            trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
            return false;
        }
        return true;
    }

}

/**
 * Create a directory structure recursively
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.0
 * @param       string   $pathname    The directory structure to create
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function mkdirr($pathname, $mode = null) {
    // Check if directory already exists
    if (is_dir($pathname) || empty($pathname))
        return true;
    // Ensure a file does not already exist with the same name
    if (is_file($pathname)) {
        trigger_error('mkdirr() File exists', E_USER_WARNING);
        return false;
    }
    // Crawl up the directory tree
    $next_pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
    if (mkdirr($next_pathname, $mode))
        if (!file_exists($pathname))
            return mkdir($pathname, $mode);
    return false;
}

/**
 * Output content to navigator
 * @param       string   $file    file to return to browser
 * @param       string   $fileName name of this file
 * @param       string   $type	  type of content
 */
function PushFileToBrowser($file, $fileName = '', $type = '') {
    // Check if directory already exists
    if (is_file($file)) {
        $ext = FileGetExtention($file);
        if ($type == '') {
            $mimes = mimeTypesList();
            $type = ($mimes[$ext] != '') ? $mimes[$ext] : 'application/download';
            unset($mimes);
        }

        if ($fileName == '') {
            $fileNameTmp = explode('/', $file);
            $last = count($fileNameTmp) - 1;
            if ($fileNameTmp[$last] != '')
                $fileName = $fileNameTmp[$last];
            else
                $fileName = 'fichier.' . $ext;
        }

        $download_size = filesize($file);
        header('Content-Type: ' . $type);
        header("Content-Disposition: inline; filename=$fileName");
        header('Expires: 0');
        header('Cache-Control: private');
        header('Accept-Ranges: bytes');
        header("Content-Length: $download_size");
        header('Pragma: public');
        @readfile($file);
        exit;
    }
}

/**
 * Get an array with all associated mime-type
 */
function mimeTypesList() {
    if (is_array($GLOBALS['MimeType']))
        return $GLOBALS['MimeType'];
    else {
        if (!is_file($GLOBALS['REP']['MimeTypeFile']) || !is_readable($GLOBALS['REP']['MimeTypeFile']))
            return false;
        $types = array();
        $fp = fopen($GLOBALS['REP']['MimeTypeFile'], "r");
        while (false != ($line = fgets($fp, 4096))) {
            if (!preg_match("/^\s*(?!#)\s*(\S+)\s+(?=\S)(.+)/", $line, $match))
                continue;
            $tmp = preg_split("/\s/", trim($match[2]));
            foreach ($tmp as $type)
                $types[strtolower($type)] = $match[1];
        }
        fclose($fp);
        $GLOBALS['MimeType'] = $types;
        return $GLOBALS['MimeType'];
    }
}

?>
