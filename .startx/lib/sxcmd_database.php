<?php

function exportDB() {
    global $EP, $config;
    $confDb = $config['datatbases']['nosql'];
    $newDb = ask("$EP    Database to export [" . $confDb['database'] . "] : ");
    $db = ($newDb != "") ? $newDb : $confDb['database'];
    $newExclude = ask("$EP    Collections to exclude [" . $confDb['exclude_table'] . "] : ");
    $exclude = ($newExclude != "") ? $newExclude : $confDb['exclude_table'];
    $newUser = ask("$EP    User to use [" . $confDb['user'] . "] : ");
    $user = ($newUser != "") ? $newUser : $confDb['user'];
    $newPwd = ask("$EP    Password to use [" . $confDb['pwd'] . "] : ");
    $pwd = ($newPwd != "") ? $newPwd : $confDb['pwd'];
    $newDir = ask("$EP    Destination directory [" . $config['project']['path'] . '/' . $confDb['dump_dir'] . "] : ");
    $dir = ($newDir != "") ? $newDir : $config['project']['path'] . '/' . $confDb['dump_dir'];
    echo "$EP   dumping database " . $db . "\n";
    $mongo = new MongoClient();
    $collections = $mongo->selectDB($db)->getCollectionNames();
    if ($exclude != "") {
        $ex_col = explode(",", $exclude);
        $collections = array_diff($collections, $ex_col);
    }
    foreach ($collections as $col) {
        shell_exec("mongoexport -d " . $db . " -c $col -u " . $user . " -p " . $pwd . " -o " . $dir . "/" . "dump_$col.json --jsonArray");
        echo "$EP   dump_$col.json exported\n";
    }
}

function importDB() {
    global $EP, $config;
    $confDb = $config['datatbases']['nosql'];

    $newDir = ask("$EP    Source directory [" . $config['project']['path'] . '/' . $confDb['dump_dir'] . "] : ");
    $dir = ($newDir != "") ? $newDir : $config['project']['path'] . '/' . $confDb['dump_dir'];
    $newExclude = ask("$EP    Collections to exclude [" . $confDb['exclude_table'] . "] : ");
    $exclude = ($newExclude != "") ? $newExclude : $confDb['exclude_table'];
    $newDb = ask("$EP    Database to import [" . $confDb['database'] . "] : ");
    $db = ($newDb != "") ? $newDb : $confDb['database'];
    $newUser = ask("$EP    User to use [" . $confDb['user'] . "] : ");
    $user = ($newUser != "") ? $newUser : $confDb['user'];
    $newPwd = ask("$EP    Password to use [" . $confDb['pwd'] . "] : ");
    $pwd = ($newPwd != "") ? $newPwd : $confDb['pwd'];
    echo "$EP   Start importing database " . $db . "\n";
    $dirs = scandir($dir);
    $ex_col = array();
    if ($exclude != "") {
        $ex_col = explode(",", $exclude);
        foreach ($ex_col as $k => $e) {
            $ex_col[$k] = "dump_$e.json";
        }
    }
    array_push($ex_col, ".");
    array_push($ex_col, "..");
    array_push($ex_col, "index.php");
    if ($dirs != false and count($dirs) > 3) {
        $mongo = new MongoClient();
        $mongo->selectDB($db);
        $dirs = array_diff($dirs, $ex_col);
        foreach ($dirs as $d) {
            $col = str_replace("dump_", "", str_replace(".json", "", $d));
            shell_exec("mongoimport -d " . $db . " -c $col -u " . $user . " -p " . $pwd . " " . $dir . "/" . "$d --jsonArray");
            echo "$EP   $d imported\n";
        }
    }
}

?>
