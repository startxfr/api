<?php


function docGenerate()
{
	global $EP, $config;
	$dirI = $config['project']['path']."/api-lib/lib/resources/";
	$dirO = "resources/";

	echo "$EP	 Generating doc\n";

	$obj = sanitizeFiles(getAllFiles(split_dir($dirI)));
	createDoc($obj, $dirO);
	createSidebar($dirO);
	move_dir($dirO, $config['docgenerator']['docpath']);
        $cmd = "cd ".$config['project']['path']."/.startx/lib/lib-doc/ ; ./generate-doxygen";
	$result = `($cmd)`;

	echo "$EP	 Doc generated\n";
}

function docPublish()
{
	global $EP, $config;

	echo "$EP	 Publishing doc\n";
        $cmd = "cd ".$config['docgenerator']['docpath'].";
	git pull;
	git add -A;
	git commit -m \"".$config['docgenerator']['default_commit_msg']."\" >> /dev/null;
	git push origin master >> /dev/null;
	cd -";
	$result = `$cmd`;

	echo "$EP	 Publishing done\n";
}

?>
