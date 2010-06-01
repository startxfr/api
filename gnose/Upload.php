<?php
include ('../inc/conf.inc');		// Declare global variables from config files
include ('../inc/core.inc');		// Load core library

$PC = new PageContext('gnose');
$PC->GetSessionContext();


function upload_file($field = '', $dirPath = '', $maxSize = 100000, $allowed = array()) {
    $tmp_name = $type = $name = $error = $size = '';
    foreach ($_FILES[$field] as $key => $val)
        $$key = $val;

    if ((!is_uploaded_file($tmp_name)) || ($error != 0) || ($size == 0) || ($size > $maxSize))
        return false;    // file failed basic validation checks

    if ((is_array($allowed)) && (!empty($allowed)))
        if (!in_array($type, $allowed))
            return false;    // file is not an allowed type

    if(is_dir($dirPath))
        $path = $dirPath . DIRECTORY_SEPARATOR . basename($name);
    else
        $path = $dirPath;
    if (move_uploaded_file($tmp_name, $path))
        return $path;

    return false;
}
if (array_key_exists('submit', $_POST))  // form has been submitted
{
    if (upload_file('fichier', $GLOBALS['SVN_Pool1']['WorkCopy'].$_POST['path'], 700000, array())) {
        if(is_dir($GLOBALS['SVN_Pool1']['WorkCopy'].$_POST['path']))
            shell_exec('svn add --config-dir '.$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir'].' --username '.$_SESSION['user']['id'].' '.rtrim($GLOBALS['SVN_Pool1']['WorkCopy'].$_POST['path']."/".basename($_FILES['fichier']['name'])));
        shell_exec("export EDITOR=\"vi\"; export LC_CTYPE=\"fr_FR.UTF-8\"; export LANG=\"fr_FR.UTF-8\"; svn ci -m \"Le fichier ".$_FILES['fichier']['name']." a été uploadé. \" --config-dir ".$GLOBALS['REP']['appli'].$GLOBALS['SVN_Pool1']['ConfigDir']." --username ".$_SESSION['user']['id']." ".$GLOBALS['SVN_Pool1']['WorkCopy']);
        shell_exec('svn up --non-interactive '.$GLOBALS['SVN_Pool1']['WorkCopy']);
        $_SESSION['temp']['upload'] = $_FILES['fichier'];
        if($_POST['retour'] == '')
             header('Location:BrowseWork.php');
        else header('Location:'.$_POST['retour']);
    }
    else header('Location:'.$_POST['retour']);
}
?>
