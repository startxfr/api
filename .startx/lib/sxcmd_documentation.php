<?php

$PROJECT_DIR = "/var/www/html/startx/api";
$DOCGEN = "$PROJECT_DIR/.startx/doc-generate";
$DOC_COMMIT_MSG="génération de la documentation";

function docGenerate()
{
	global $EP, $DOCGEN;

	echo "$EP   generating doc\n";

	`. $DOCGEN`;
	echo "$EP   doc generated\n";
}

function docPublish()
{
	global $EP, $PROJECT_DIR, $DOC_COMMIT_MSG;

	echo "$EP   publishing doc\n";

	`cd $PROJECT_DIR/../api.wiki;
	git pull;
	git add *;
	git commit -a -m ".$DOC_COMMIT_MSG." >> /dev/null;
	git push origin master >> /dev/null;
	cd -`;

	echo "$EP   publishing done\n";
}

?>
