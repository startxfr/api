<?php

require_once('sxcmd_documentation.php');
require_once('sxcmd_database.php');
require_once('sxcmd_gitcommand.php');

function executeMenuDatabase($choice, $display = true)
{
    switch ($choice) {
        case "1": exportDB();
            break;
        case "2": importDB();
            break;
        case "9": if ($display){displayMenuPrincipal();}
            break;
        case "0": exit;
            break;
    }
	if ($display)
		displayMenuDatabase();
}

function executeMenuDocumentation($choice, $display = true)
{
    switch ($choice) {
        case "1": docGenerate();docPublish();
            break;
        case "2": docGenerate();
            break;
        case "3": docPublish();
            break;
        case "9": if ($display){displayMenuPrincipal();}
            break;
        case "0": exit;
            break;
    }
	if ($display)
		displayMenuDocumentation();
}

function executeMenuDev($choice, $display = true)
{
    switch ($choice) {
		case "1": echo "Go to dev Branch\n";
			gitCheckout("dev2");
            break;
		case "2": echo "Status\n";
			gitStatus("dev2");
            break;
		case "3": echo "Commit\n";
			gitCommit("dev2", "sxcmd_auto-commit");
            break;
		case "4": echo "Push\n";
			gitPush('dev2');
            break;
		case "5": echo "Commit & Push\n";
			gitCommit("dev2", "dev commit");
			gitPush('dev2');
            break;
        case "6": echo "Merge to master\n";
			gitMerge("dev2", "master");
            break;
        case "9": if ($display){displayMenuPrincipal();}
            break;
        case "0": exit;
            break;
    }
	if ($display)
		displayMenuDev();
}

function executeMenuTest($choice, $display = true)
{
    switch ($choice) {
        case "1": echo "Go to test Branch\n";
			gitCheckout("testing");
            break;
        case "2": echo "Merge from master\n";
			gitMerge("master", "testing");
            break;
        case "3": echo "Merge to prod\n";
			gitMerge("testing", "production");
            break;
        case "4": echo "Push\n";
			gitPush('testing');
            break;
        case "5": echo "Push Paas\n... nothing happened...\n";
            break;
        case "9": if ($display){displayMenuPrincipal();}
            break;
        case "0": exit;
            break;
    }
	if ($display)
		displayMenuTest();
}

function executeMenuProd($choice, $display = true)
{
    switch ($choice) {
        case "1": echo "Go to prod Branch\n";
			gitCheckout("production");
            break;
        case "2": echo "Merge from test\n";
			gitMerge("testing", "production");
            break;
        case "3": echo "Push\n";
			gitPush('production');
            break;
        case "4": echo "Push Paas\n... nothing happened...\n";
            break;
        case "9": if ($display){displayMenuPrincipal();}
            break;
        case "0": exit;
            break;
    }
	if ($display)
		displayMenuProd();
}

?>
