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
	global $EP, $DEVBRANCH;

    switch ($choice) {
		case "1": echo "$EP    Go to dev Branch\n";
			gitCheckout($DEVBRANCH);
            break;
		case "2": echo "$EP    Status\n";
			gitStatus($DEVBRANCH);
            break;
		case "3": echo "$EP    Commit\n";
			gitCommit($DEVBRANCH, "dev commit");
            break;
		case "4": echo "$EP    Commit & Push\n";
			gitCommit($DEVBRANCH, "dev commit");
			gitPush($DEVBRANCH);
            break;
        case "5": echo "$EP    Merge to master\n";
			gitMerge($DEVBRANCH, "master");
            break;
        case "6": echo "$EP    Merge from master\n";
			gitMerge("master", $DEVBRANCH);
            break;
        case "7": echo "$EP    Commit & Push & Merge to master\n";
			gitCommit($DEVBRANCH, "dev commit");
			gitPush($DEVBRANCH);
			gitMerge($DEVBRANCH, "master");
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
	global $EP;

    switch ($choice) {
        case "1": echo "$EP    Go to test Branch\n";
			gitCheckout("testing");
            break;
        case "2": echo "$EP    Merge from master\n";
			gitMerge("master", "testing");
            break;
        case "3": echo "$EP    Merge to prod\n";
			gitMerge("testing", "production");
            break;
        case "4": echo "$EP    Push\n";
			gitPush('testing');
            break;
        case "5": echo "$EP    Push Paas\n";
			gitPush('testing');
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
	global $EP;

    switch ($choice) {
        case "1": echo "$EP    Go to prod Branch\n";
			gitCheckout("production");
            break;
        case "2": echo "$EP    Merge from test\n";
			gitMerge("testing", "production");
            break;
        case "3": echo "$EP    Push\n";
			gitPush('production');
            break;
        case "4": echo "$EP    Push Paas\n";
			gitPush('production');
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
