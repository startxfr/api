#!/usr/bin/php
<?php

require_once('../api-lib/kernel/loader.php');


$dirI = "../api-lib/lib/resources/";
$dirO = "resources/";
$file = "mysqlModelResource";

function split_dir($dir) {
	$list = glob("$dir*", GLOB_MARK);
	return $list;
}

function getAllFiles($list) {
	$new_list = array();
	foreach ($list as $file) {
		if (is_dir($file)) {
			$list2 = getAllFiles(split_dir($file));
			$new_list = array_merge($new_list, $list2);
		}
		else
			$new_list[] = $file;
	}
	return $new_list;
}

function sanitizeFiles($list) {
	$new_list = array();
	foreach ($list as $file) {
		if (preg_match('/Resource.php$/', $file)) {
			$tmp = explode("/", $file);
			$tmp = explode(".", $tmp[count($tmp) - 1]);
			$new_list[] = $tmp[0];
		}
	}
	return $new_list;
}

function getFamily($class) {
	if (property_exists($class, "ConfDesc") === false)
		return false;
	$obj = json_decode($class::$ConfDesc, true);
	while (($parent = get_parent_class($class)) !== "Configurable" ) {
		if (property_exists($parent, "ConfDesc")) {
			$tmp = json_decode($parent::$ConfDesc, true);
			$obj["propreties"] = array_merge($obj["propreties"], $tmp["propreties"]);
		}
		$obj['family'][] = $parent;
		$class = $parent;
	}
	return($obj);
}

function createMdPage($file, $dirO) {
	if (($obj = getFamily($file)) === false)
		return 0;
	$fd = fopen($dirO . $file . ".md", "w");
	$title = "##" . $obj["class_name"] . "\n";
	$desc = $obj["desc"] . "\n";

	$table = "|name|type|mandatory|desc|\n|----|----|----|----|\n";
	foreach ($obj["propreties"] as $line) {
		$table .= "|" . $line["name"] . "|" . $line["type"]. "|" 
					. $line["mandatory"]. "|" . $line["desc"]. "|\n";
	}

	$family_ref = $obj["class_name"];
	foreach ($obj["family"] as $parent) {
		$family_ref .= " <- " . $parent;
	}
	$family_ref .= "\n";

	fwrite($fd, $title . $desc . $table . $family_ref);
	fclose($fd);
	return 1;
}

#mkdir($dirO);
#createMdPage($file, $dirO);

$obj = sanitizeFiles(getAllFiles(split_dir($dirI)));
foreach ($obj as $file) {
	print "-------\n" . $file . " : ";
	$ret = createMdPage($file, $dirO);
	print $ret . "\n";
}
?>
