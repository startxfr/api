<?php

function gitCheckout($branch, $verb = true)
{
	$cmd_output = `git checkout $branch`;
	if ($verb)
		echo $cmd_output;
	$cmd_output = `git pull origin $branch`;
	if ($verb)
		echo $cmd_output;
}

function gitStatus($branchsrc)
{
	gitCheckout($branchsrc, false);
	$cmd_output = `git status`;
	echo $cmd_output;
}

function gitCommit($branchsrc, $commitMsg)
{
	gitCheckout($branchsrc, false);
	$cmd_output = `git add -A`;
	echo $cmd_output;
	$cmd_output = `git commit -am "$commitMsg"`;
	echo $cmd_output;
}

function gitPush($branch)
{
	gitCheckout($branch, false);
	$cmd_output = `git push origin $branch`;
	echo $cmd_output;
}

function gitMerge($branchToMerge, $branchsrc)
{
	gitCheckout($branchsrc, false);
	$cmd_output = `git merge $branchToMerge`;
	echo $cmd_output;
}

?>
