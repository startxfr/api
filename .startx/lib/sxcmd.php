<?php

$VERSION = "dev/cl";
$SXCMD_PATH = "~/.sxcmd";
$EP = "[sxcmd-api]";
$cwd = trim(shell_exec("pwd"));
$COL = array('white' => "\033[0;37m", 'red' => "\033[0;31m", 'blue' => "\033[1;36m", 'green' => "\033[0;32m");

require_once('sxcmd_executeMenu.php');

function ask($q) {
    return trim(shell_exec("read -p '$q ' q\necho \$q"));
}

function displayIntro() {
    global $EP, $VERSION, $cwd, $COL;
    echo $COL['red']."$EP\n";
    echo "$EP   +------------------------------------------+\n";
    echo "$EP   | SXCMD API : Command line for API project |\n";
    echo "$EP   +------------------------------------------+\n";
    echo "$EP    Version : $VERSION\n";
    echo "$EP    Chemin  : $cwd\n".$COL['white'];
}

function displayMenuPrincipal() {
    global $EP, $COL;
    echo "$EP\n";
    echo "$EP -- ".$COL["blue"]."Menu principal\n".$COL["white"];
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP ".$COL["green"]."1. Bases de données\n".$COL["white"];
    echo "$EP ".$COL["green"]."2. Documentation\n".$COL["white"];
    echo "$EP ".$COL["green"]."3. Developement\n".$COL["white"];
    echo "$EP ".$COL["green"]."4. Test\n".$COL["white"];
    echo "$EP ".$COL["green"]."5. Production\n".$COL["white"];
    echo "$EP ".$COL["green"]."0. Exit\n".$COL["white"];
    echo "$EP\n";
    switch (ask("$EP    Votre choix : ")) {
        case "1": displayMenuDatabase();
            break;
        case "2": displayMenuDocumentation();
            break;
        case "3": displayMenuDev();
            break;
        case "4": displayMenuTest();
            break;
        case "5": displayMenuProd();
            break;
        case "0": exit;
            break;
        default: displayMenuPrincipal();
            break;
    }
}

function displayMenuDatabase() {
    global $EP, $COL;
    echo "$EP\n";
    echo "$EP -- ".$COL["blue"]."Menu bases de données\n".$COL["white"];
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP ".$COL["green"]."1. Sauvegarder la base mongodb locale\n".$COL["white"];
    echo "$EP ".$COL["green"]."2. Importer la base sauvegardée\n".$COL["white"];
    echo "$EP ".$COL["green"]."9. Retour menu principal\n".$COL["white"];
    echo "$EP ".$COL["green"]."0. Exit\n".$COL["white"];
    echo "$EP\n";
	executeMenuDatabase(ask("$EP    Votre choix : "));
}

function displayMenuDocumentation() {
    global $EP, $COL;
    echo "$EP\n";
    echo "$EP -- ".$COL["blue"]."Menu documentation\n".$COL["white"];
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP ".$COL["green"]."1. Générer et publier\n".$COL["white"];
    echo "$EP ".$COL["green"]."2. Générer la documentation\n".$COL["white"];
    echo "$EP ".$COL["green"]."3. Publier la documentation\n".$COL["white"];
    echo "$EP ".$COL["green"]."9. Retour menu principal\n".$COL["white"];
    echo "$EP ".$COL["green"]."0. Exit\n".$COL["white"];
    echo "$EP\n";
	executeMenuDocumentation(ask("$EP    Votre choix : "));
}

function displayMenuDev() {
    global $EP, $COL;
    echo "$EP\n";
    echo "$EP -- ".$COL["blue"]."Menu developpement\n".$COL["white"];
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP ".$COL["green"]."1. Go to dev branch\n".$COL["white"];
    echo "$EP ".$COL["green"]."2. Status\n".$COL["white"];
    echo "$EP ".$COL["green"]."3. Commit\n".$COL["white"];
    echo "$EP ".$COL["green"]."4. Push\n".$COL["white"];
    echo "$EP ".$COL["green"]."5. Commit & push\n".$COL["white"];
    echo "$EP ".$COL["green"]."6. Merge to master\n".$COL["white"];
    echo "$EP ".$COL["green"]."9. Retour menu principal\n".$COL["white"];
    echo "$EP ".$COL["green"]."0. Exit\n".$COL["white"];
    echo "$EP\n";
	executeMenuDev(ask("$EP    Votre choix : "));
}

function displayMenuTest() {
    global $EP, $COL;
    echo "$EP\n";
    echo "$EP -- ".$COL["blue"]."Menu test\n".$COL["white"];
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP ".$COL["green"]."1. Go to test branch\n".$COL["white"];
    echo "$EP ".$COL["green"]."2. Merge from master\n".$COL["white"];
    echo "$EP ".$COL["green"]."3. Merge to prod\n".$COL["white"];
    echo "$EP ".$COL["green"]."4. Push\n".$COL["white"];
    echo "$EP ".$COL["green"]."5. Push Paas\n".$COL["white"];
    echo "$EP ".$COL["green"]."9. Retour menu principal\n".$COL["white"];
    echo "$EP ".$COL["green"]."0. Exit\n".$COL["white"];
    echo "$EP\n";
	executeMenuTest(ask("$EP    Votre choix : "));
}

function displayMenuProd() {
    global $EP, $COL;
    echo "$EP\n";
    echo "$EP -- ".$COL["blue"]."Menu production\n".$COL["white"];
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP ".$COL["green"]."1. Go to prod branch\n".$COL["white"];
    echo "$EP ".$COL["green"]."2. Merge from test\n".$COL["white"];
    echo "$EP ".$COL["green"]."3. Push\n".$COL["white"];
    echo "$EP ".$COL["green"]."4. Push Paas\n".$COL["white"];
    echo "$EP ".$COL["green"]."9. Retour menu principal\n".$COL["white"];
    echo "$EP ".$COL["green"]."0. Exit\n".$COL["white"];
    echo "$EP\n";
	executeMenuProd(ask("$EP    Votre choix : "));
}

function displayUsage() {
	global $EP;

	echo "$EP		Usage:\n";
	echo "$EP	./sxcmd [args]\n";
	echo "$EP	Using arguments will overide the display of the sxcmd manager and execute the scripts which code correspond to the arguments.\n";
	echo "$EP	Arguments have the following compostion: \n";
	echo "$EP		a.b		where a is the number of the menu and b the method one,\n";
	echo "$EP		a.b[:b]	additional methods from the same menu can be add.\n";
	return 0;
}

function displayHelp() {
	global $EP;

	echo "$EP	Help\n";
	echo "$EP	Some of the sxcmd script need external programmes in order to work properly.
		Make sure to install doxygen (on deb system: apt-get install doxygen, on yum system: yum install doxygen)\n";
	echo "$EP	Clone the Wiki repository next to the 'api'\n";
}

function choose_menu($opts)
{
	foreach ($opts as $elem) {
		$cmd = explode('.', $elem);
		switch ($cmd[0])
		{
			case "1": $menu = "executeMenuDatabase";
				break;
			case "2": $menu = "executeMenuDocumentation";
				break;
			case "3": $menu = "executeMenuDev";
				break;
			case "4": $menu = "executeMenuTest";
				break;
			case "5": $menu = "executeMenuProd";
				break;
			default: exit;
				break;
		}
		$methods = explode(':', $cmd[1]);
		foreach ($methods as $method) {
			$menu($method, false);
		}
	}
}

function check_options()
{
	global $argv, $argc;

	if ($argc === 1)
		return 1;
	$opts = preg_grep('/^\d\.\d(:\d)*$/', $argv);
	if (count($opts) !== $argc - 1)
		return displayUsage();
	choose_menu($opts);
	return 0;
}

if (check_options())
{
	displayIntro();
	displayMenuPrincipal();
}	
