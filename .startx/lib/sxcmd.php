<?php

$VERSION = "dev/cl";
$SXCMD_PATH = "~/.sxcmd";
$EP = "[sxcmd-api]";
$cwd = trim(shell_exec("pwd"));

function ask($q) {
    return trim(shell_exec("read -p '$q ' q\necho \$q"));
}

function displayIntro() {
    global $EP, $VERSION, $cwd;
    echo "$EP\n";
    echo "$EP   +------------------------------------------+\n";
    echo "$EP   | SXCMD API : Command line for API project |\n";
    echo "$EP   +------------------------------------------+\n";
    echo "$EP    Version : $VERSION\n";
    echo "$EP    Chemin  : $cwd\n";
}

function displayMenuPrincipal() {
    global $EP;
    echo "$EP\n";
    echo "$EP -- Menu principal\n";
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP 1. Bases de données\n";
    echo "$EP 2. Documentation\n";
    echo "$EP 3. Developement\n";
    echo "$EP 4. Test\n";
    echo "$EP 5. Production\n";
    echo "$EP 9. Exit\n";
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
        case "9": exit;
            break;
        default: displayMenuPrincipal();
            break;
    }
}

function displayMenuDatabase() {
    global $EP;
    echo "$EP\n";
    echo "$EP -- Menu bases de données\n";
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP 1. Sauvegarder la base mongodb locale\n";
    echo "$EP 2. Importer la base sauvegardée\n";
    echo "$EP 9. Retour menu principal\n";
    echo "$EP\n";
    switch (ask("$EP    Votre choix : ")) {
        case "1": displayMenuDatabase();
            break;
        case "2": displayMenuDocumentation();
            break;
        case "9": displayMenuPrincipal();
            break;
        default: displayMenuDatabase();
            break;
    }
}

function displayMenuDocumentation() {
    global $EP;
    echo "$EP\n";
    echo "$EP -- Menu documentation\n";
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP 1. Générer et publier\n";
    echo "$EP 2. Générer la documentation\n";
    echo "$EP 3. Publier la documentation\n";
    echo "$EP 9. Retour menu principal\n";
    echo "$EP\n";
    switch (ask("$EP    Votre choix : ")) {
        case "1": displayMenuDatabase();
            break;
        case "2": displayMenuDocumentation();
            break;
        case "9": displayMenuPrincipal();
            break;
        default: displayMenuDatabase();
            break;
    }
}

function displayMenuDev() {
    global $EP;
    echo "$EP\n";
    echo "$EP -- Menu developpement\n";
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP 1. Go to dev branch\n";
    echo "$EP 2. Status\n";
    echo "$EP 3. Commit\n";
    echo "$EP 4. Commit & push\n";
    echo "$EP 5. Push\n";
    echo "$EP 6. Merge to master\n";
    echo "$EP 9. Retour menu principal\n";
    echo "$EP\n";
    switch (ask("$EP    Votre choix : ")) {
        case "1": displayMenuDatabase();
            break;
        case "2": displayMenuDocumentation();
            break;
        case "9": displayMenuPrincipal();
            break;
        default: displayMenuDatabase();
            break;
    }
}

function displayMenuTest() {
    global $EP;
    echo "$EP\n";
    echo "$EP -- Menu test\n";
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP 1. Go to test branch\n";
    echo "$EP 2. Merge from master\n";
    echo "$EP 3. Merge to prod\n";
    echo "$EP 4. Push\n";
    echo "$EP 5. Push Paas\n";
    echo "$EP 9. Retour menu principal\n";
    echo "$EP\n";
    switch (ask("$EP    Votre choix : ")) {
        case "1": displayMenuDatabase();
            break;
        case "2": displayMenuDocumentation();
            break;
        case "9": displayMenuPrincipal();
            break;
        default: displayMenuDatabase();
            break;
    }
}

function displayMenuProd() {
    global $EP;
    echo "$EP\n";
    echo "$EP -- Menu production\n";
    echo "$EP    Choisissez une option parmis les actions suivantes :\n";
    echo "$EP\n";
    echo "$EP 1. Go to prod branch\n";
    echo "$EP 2. Merge from test\n";
    echo "$EP 4. Push\n";
    echo "$EP 5. Push Paas\n";
    echo "$EP 9. Retour menu principal\n";
    echo "$EP\n";
    switch (ask("$EP    Votre choix : ")) {
        case "1": displayMenuDatabase();
            break;
        case "2": displayMenuDocumentation();
            break;
        case "9": displayMenuPrincipal();
            break;
        default: displayMenuDatabase();
            break;
    }
}

displayIntro();
displayMenuPrincipal();
