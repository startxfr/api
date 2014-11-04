<?php

function gitCheckout($branch, $verb = true)
{
	global $PROJECT_DIR;

	$red = "";
	if (!$verb)
		$red = "&> /dev/null";
	$cmd_output = `(cd $PROJECT_DIR ; git checkout $branch $red)`;
	echo $cmd_output;
	$cmd_output = `(cd $PROJECT_DIR ; git pull origin $branch $red)`;
	echo $cmd_output;
}

function gitStatus($branchsrc)
{
	global $PROJECT_DIR;

	gitCheckout($branchsrc, false);
	$cmd_output = `(cd $PROJECT_DIR ; git status)`;
	echo $cmd_output;
}

function gitCommit($branchsrc, $commitMsg)
{
	global $PROJECT_DIR;

	gitCheckout($branchsrc, false);
	$cmd_output = `(cd $PROJECT_DIR ; git add -A)`;
	echo $cmd_output;
	$cmd_output = `(cd $PROJECT_DIR ; git commit -am "$commitMsg")`;
	echo $cmd_output;
}

function gitPush($branch)
{
	global $PROJECT_DIR;

	gitCheckout($branch, false);
	$cmd_output = `(cd $PROJECT_DIR ; git push origin $branch)`;
	echo $cmd_output;
}

function gitMerge($branchToMerge, $branchsrc)
{
	global $PROJECT_DIR;

	gitCheckout($branchsrc, false);
	$cmd_output = `(cd $PROJECT_DIR ; git merge $branchToMerge)`;
	echo $cmd_output;
}

?>
