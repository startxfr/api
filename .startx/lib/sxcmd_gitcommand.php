<?php

function gitCheckout($branch)
{
	$cmd_output = `git checkout $branch`;
	echo $cmd_output;
}

function gitStatus()
{
	$cmd_output = `git status`;
	echo $cmd_output;
}

function gitCommit($commitMsg)
{
	$cmd_output = `git add *`;
	echo $cmd_output;
	$cmd_output = `git commit -am "$commitMsg"`;
	echo $cmd_output;
}

function gitPush($branch)
{
	$cmd_output = `git push origin $branch`;
	echo $cmd_output;
}

function gitMerge($branch)
{
	$cmd_output = `git merge $branch`;
	echo $cmd_output;
}

?>
