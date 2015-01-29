<?php

function gitCheckout($branch, $verb = true, $update = true) {
    global $config;
    $rootdir = $config['project']['path'];
    $red = (!$verb) ? "&> /dev/null" : "";
    $cmd_output = `(cd $rootdir ; git checkout $branch $red)`;
    if ($verb) {
        echo $cmd_output;
    }
    if ($update) {
        $cmd_output = `(cd $rootdir ; git pull origin $branch $red)`;
        if ($verb) {
            echo $cmd_output;
        }
    }
}

function gitStatus($branchsrc, $update = true) {
    global $config;
    $rootdir = $config['project']['path'];

    gitCheckout($branchsrc, false, $update);
    $cmd_output = `(cd $rootdir ; git status)`;
    echo $cmd_output;
}

function gitCommit($branchsrc, $commitMsg, $update = true, $verb = true) {
    global $EP, $config;
    $rootdir = $config['project']['path'];
    $newMessage = ask("$EP    Votre message de commit : ");
    $message = ($newMessage != "") ? $newMessage : $commitMsg;
    $cmd_output = `(cd $rootdir ; git add -A)`;
    if ($verb) {
        echo $cmd_output;
    }
    gitStatus($branchsrc, $update);
    $cmd_output1 = `(cd $rootdir ; git commit -am "$message")`;
    if ($verb) {
        echo $cmd_output1;
    }
}

function gitPush($branch, $update = true, $verb = true) {
    global $config;
    $rootdir = $config['project']['path'];

    gitCheckout($branch, false, $update);
    $cmd_output = `(cd $rootdir ; git push origin $branch)`;
    if ($verb) {
        echo $cmd_output;
    }
}

function gitMerge($branchToMerge, $branchsrc, $update = true, $verb = true) {
    global $config;
    $rootdir = $config['project']['path'];

    gitCheckout($branchsrc, false, $update);
    $cmd_output = `(cd $rootdir ; git merge $branchToMerge ; git push origin $branchsrc)`;
    if ($verb) {
        echo $cmd_output;
    }
}

?>
