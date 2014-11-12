<?php

$col = array('reset' => "\033[0;0m", 'red' => "\033[0;31m", 'blue' => "\033[1;36m", 'green' => "\033[0;32m");
$config = array();

function loadConf() {
    global $config;

    $sxcmdpath = dirname(array_shift(get_included_files()));
    $conffile = $sxcmdpath . "/sxcmd.json";
    $projectpath = dirname($sxcmdpath);

    if (file_exists($conffile) && ($str = file_get_contents($conffile)) !== false) {
        $config = json_decode($str, true);
        $config['path'] = $sxcmdpath;
        $config['conf'] = $conffile;
        $config['project']['path'] = $projectpath;
        $GLOBALS['EP'] = $config['output_prefix'];
        return true;
    }
    $config['path'] = $sxcmdpath;
    $config['conf'] = $conffile;
    $config['project']['path'] = $projectpath;
    return false;
}

function ask($q) {
    return trim(shell_exec("read -p '$q ' q\necho \$q"));
}