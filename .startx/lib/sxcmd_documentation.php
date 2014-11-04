<?php

$PROJECT_DIR = "/var/www/html/startx/api";
$WIKI_DIR = "/var/www/html/startx/api.wiki";
$DOC_COMMIT_MSG="génération de la documentation";

require_once('sxcmd_documentation_wiki.php');

function docGenerate()
{
	global $EP, $PROJECT_DIR, $WIKI_DIR;
	$dirI = "$PROJECT_DIR/api-lib/lib/resources/";
	$dirO = "resources/";

	echo "$EP	 Generating doc\n";

	$obj = sanitizeFiles(getAllFiles(split_dir($dirI)));
	createDoc($obj, $dirO);
	createSidebar($dirO);
	move_dir($dirO, $WIKI_DIR);

	`(cd $PROJECT_DIR/.startx/lib/lib-doc/ ; ./generate-doxygen)`;

	echo "$EP	 Doc generated\n";
}

function docPublish()
{
	global $EP, $WIKI_DIR, $DOC_COMMIT_MSG;

	echo "$EP	 Publishing doc\n";

	`cd $WIKI_DIR;
	git pull;
	git add -A;
	git commit -m $DOC_COMMIT_MSG >> /dev/null;
	git push origin master >> /dev/null;
	cd -`;

	echo "$EP	 Publishing done\n";
}

?>
