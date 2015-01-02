<?php

function executeMenuDatabase($choice, $display = true) {
    require_once('sxcmd_database.php');
    switch ($choice) {
        case "1": exportDB();
            break;
        case "2": importDB();
            break;
        case "9": if ($display) {
                displayMenuPrincipal();
            }
            break;
        case "0": exit;
            break;
    }
    if ($display) {
        displayMenuDatabase();
    }
}

function executeMenuDocumentation($choice, $display = true) {
    global $config;
    require_once('sxcmd_documentation.php');
    require_once('sxcmd_documentation_wiki.php');
    require_once($config['project']['path'] . "/api-lib/kernel/loader.php");
    switch ($choice) {
        case "1": docGenerate();
            docPublish();
            break;
        case "2": docGenerate();
            break;
        case "3": docPublish();
            break;
        case "9": if ($display) {
                displayMenuPrincipal();
            }
            break;
        case "0": exit;
            break;
    }
    if ($display) {
        displayMenuDocumentation();
    }
}

function executeMenuDev($choice, $display = true) {
    global $EP, $config;
    require_once('sxcmd_gitcommand.php');
    $dev_branch = $config['git']['branch_dev'];
    $testing_branch = $config['git']['branch_testing'];

    switch ($choice) {
        case "1": echo "$EP    Go to branch $dev_branch\n";
            gitCheckout($dev_branch);
            break;
        case "2": echo "$EP    Branch $dev_branch status\n";
            gitStatus($dev_branch);
            break;
        case "3": echo "$EP    Commit into branch $dev_branch\n";
            gitCommit($dev_branch, "$dev_branch commit");
            break;
        case "4": echo "$EP    Commit & Push branch $dev_branch\n";
            gitCommit($dev_branch, "$dev_branch commit");
            gitPush($dev_branch);
            break;
        case "5": echo "$EP    Merge branch $dev_branch > master\n";
            generatePages();
            gitMerge($dev_branch, "master");
            break;
        case "6": echo "$EP    Merge branch master > $dev_branch\n";
            gitMerge("master", $dev_branch);
            break;
        case "7": echo "$EP    Commit + push branch $dev_branch & Merge to master\n";
            gitCommit($dev_branch, "$dev_branch commit");
            gitPush($dev_branch);
            gitMerge($dev_branch, "master");
            break;
        case "9": if ($display) {
                displayMenuPrincipal();
            }
            break;
        case "0": exit;
            break;
    }
    if ($display) {
        displayMenuDev();
    }
}

function executeMenuTest($choice, $display = true) {
    global $EP, $config;
    require_once('sxcmd_gitcommand.php');
    $testing_branch = $config['git']['branch_testing'];
    $production_branch = $config['git']['branch_production'];

    switch ($choice) {
        case "1": echo "$EP    Go to branch $testing_branch\n";
            gitCheckout($testing_branch);
            break;
        case "2": echo "$EP    Merge branch master > $testing_branch\n";
            gitMerge("master", $testing_branch);
            break;
        case "3": echo "$EP    Merge branch $testing_branch > $production_branch\n";
            generatePages();
            gitMerge($config['git']['branch_testing'], $config['git']['branch_production']);
            break;
        case "4": echo "$EP    Push branch $testing_branch\n";
            gitPush($config['git']['branch_testing']);
            break;
        case "5": echo "$EP    Push branch $testing_branch to Paas server\n";
            gitPush($config['git']['branch_testing']);
            break;
        case "9": if ($display) {
                displayMenuPrincipal();
            }
            break;
        case "0": exit;
            break;
    }
    if ($display) {
        displayMenuTest();
    }
}

function executeMenuProd($choice, $display = true) {
    global $EP, $config;
    require_once('sxcmd_gitcommand.php');
    $testing_branch = $config['git']['branch_testing'];
    $production_branch = $config['git']['branch_production'];

    switch ($choice) {
        case "1": echo "$EP    Go to branch $production_branch\n";
            gitCheckout($production_branch);
            break;
        case "2": echo "$EP    Merge branch $testing_branch > $production_branch\n";
            gitMerge($testing_branch, $production_branch);
            break;
        case "3": echo "$EP    Push branch $production_branch\n";
            gitPush($production_branch);
            break;
        case "4": echo "$EP    Push branch $production_branch to Paas server\n";
            gitPush($production_branch);
            break;
        case "9": if ($display) {
                displayMenuPrincipal();
            }
            break;
        case "0": exit;
            break;
    }
    if ($display) {
        displayMenuProd();
    }
}

?>
