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
	global $EP;
	$branchname= "dev";
    switch ($choice) {
		case "1": echo "$EP    Go to branch $branchname\n";
			gitCheckout($branchname);
            break;
		case "2": echo "$EP    Branch $branchname status\n";
			gitStatus($branchname);
            break;
		case "3": echo "$EP    Commit branch $branchname\n";
			gitCommit($branchname, "$branchname commit");
            break;
		case "4": echo "$EP    Push branch $branchname\n";
			gitPush($branchname);
            break;
		case "5": echo "$EP    Commit & Push branch $branchname\n";
			gitCommit($branchname, "$branchname commit");
			gitPush($branchname);
            break;
        case "6": echo "$EP    Merge $branchname > master\n";
			gitMerge($branchname, "master");
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
	$branchname= "testing";
    switch ($choice) {
        case "1": echo "$EP    Go to branch $branchname\n";
			gitCheckout($branchname);
            break;
        case "2": echo "$EP    Merge master > $branchname\n";
			gitMerge("master", $branchname);
            break;
        case "3": echo "$EP    Merge $branchname > prod\n";
			gitMerge($branchname, "production");
            break;
        case "4": echo "$EP    Push branch $branchname\n";
			gitPush($branchname);
            break;
        case "5": echo "$EP    Push branch $branchname > Paas\n";
			gitPush($branchname);
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
	$branchname= "production";
    switch ($choice) {
        case "1": echo "$EP    Go to branch $branchname\n";
			gitCheckout("production");
            break;
        case "2": echo "$EP    Merge testing > $branchname\n";
			gitMerge("testing", "production");
            break;
        case "3": echo "$EP    Push branch $branchname\n";
			gitPush('production');
            break;
        case "4": echo "$EP    Push branch $branchname > Paas\n";
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
