<?php


function gitCheckout($branch, $verb = true) {
    global $config;
    $rootdir = $config['project']['path'];

    $red = (!$verb) ? "&> /dev/null" : "";
    $cmd_output = `(cd $rootdir ; git checkout $branch $red)`;
    echo $cmd_output;
    $cmd_output = `(cd $rootdir ; git pull origin $branch $red)`;
    echo $cmd_output;
}

function gitStatus($branchsrc) {
    global $config;
    $rootdir = $config['project']['path'];

    gitCheckout($branchsrc, false);
    $cmd_output = `(cd $rootdir ; git status)`;
    echo $cmd_output;
}

function gitCommit($branchsrc, $commitMsg) {
    global $EP, $config;
    $rootdir = $config['project']['path'];
    $newMessage = ask("$EP    Votre message de commit : ");
    $message = ($newMessage != "") ? $newMessage : $commitMsg;
    gitCheckout($branchsrc, false);
    $cmd_output = `(cd $rootdir ; git add -A)`;
    echo $cmd_output;
    $cmd_output = `(cd $rootdir ; git commit -am "$message")`;
    echo $cmd_output;
}

function gitPush($branch) {
    global $config;
    $rootdir = $config['project']['path'];

    gitCheckout($branch, false);
    $cmd_output = `(cd $rootdir ; git push origin $branch)`;
    echo $cmd_output;
}

function gitMerge($branchToMerge, $branchsrc) {
    global $config;
    $rootdir = $config['project']['path'];

    gitCheckout($branchsrc, false);
    $cmd_output = `(cd $rootdir ; git merge $branchToMerge ; git push origin $branchsrc)`;
    echo $cmd_output;
}

?>
