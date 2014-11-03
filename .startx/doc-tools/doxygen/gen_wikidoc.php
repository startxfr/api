#!/usr/bin/php
<?php
require_once('../../../api-lib/kernel/loader.php');

function create_dir($dir) {
    if (is_dir($dir) === false)
        mkdir($dir, 0777, true);
}

function move_dir($src, $dest) {
    if (is_dir($dest . $src))
        shell_exec("rm -rf " . $dest . $src);
    rename($src, $dest . $src);
}

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
        } else
            $new_list[] = $file;
    }
    return $new_list;
}

function sanitizeFiles($list) {
    $new_list = array();
    foreach ($list as $file) {
        if (preg_match('/resources\/(.*Resource)\.php$/', $file, $matches)) {
            $new_list[] = $matches[1];
        }
    }
    return $new_list;
}

function getFamily($class) {
    if (property_exists($class, "ConfDesc") === false)
        return false;
    $obj = json_decode($class::$ConfDesc, true);
	if (array_key_exists("properties", $obj) && !is_array($obj['properties']))
		$obj['properties'] = array();
	while (($parent = get_parent_class($class)) !== "Configurable") {
		if (property_exists($parent, "ConfDesc")) {
			$tmp = json_decode($parent::$ConfDesc, true);
			if (array_key_exists("properties", $tmp)) {
				$obj["properties"] = array_merge($obj["properties"], $tmp["properties"]);
			}
		}
		$obj["family"][] = $parent;
		$class = $parent;
	}
	return($obj);
}

function createMdPage($file, $dirO) {
	$ep = "Resources_";
	$filepath = explode("/", $file);
	$file = array_pop($filepath);
	$filedir = $dirO;
	if (($obj = getFamily($file)) === false)
		return 0;


	if (empty($filepath) === false) {
		$filedir .= implode("/", $filepath) . "/";
		create_dir($filedir);
	}
	$fd = fopen($filedir .$ep. $file . ".md", "w");

	$title = "##" . $obj["class_name"] . "\r\n\n";
	$desc = $obj["desc"] . "\r\n\n";
	$table = "|name|type|mandatory|desc|\r\n|----|----|----|----|\r\n";
	foreach ($obj["properties"] as $line) {
		$table .= "|" . $line["name"] . "|" . $line["type"] . "|"
			. $line["mandatory"] . "|" . $line["desc"] . "|\r\n";
	}
	#$family_ref = $obj["class_name"];
	#if (isset($obj['family'])) {
	#	foreach ($obj["family"] as $parent) {
	#		$family_ref .= " <- [" . $parent . "](" . $parent . ")";
	#	}
	#}
	$family_ref = "";
	if (isset($obj['family'])) {
		$obj['family'] = array_reverse($obj['family']);
		$right_spaces = "  ";
		foreach ($obj["family"] as $parent) {
			$family_ref .= "* [" . $parent . "](" .$ep. $parent . ")" . "\r\n" . $right_spaces ;
			$right_spaces .= "  ";
		}
	}
	$family_ref .= "* " . $obj["class_name"];
	$family_ref .= "\r\n";

	fwrite($fd, $title . $desc . $table . $family_ref);
	fclose($fd);
	return 1;
}

function createDoc($obj, $dirO, $verbose = 0) {
	foreach ($obj as $file) {
		$ret = createMdPage($file, $dirO);
		if ($verbose)
			print "-------\n" . $file . " : " . $ret . "\n";
	}
}

function sanitize($str, $extra = false) {
	$tab = array_filter(explode("/", $str));
	$str = array_pop($tab);
	$tab = array_filter(explode(".", $str));
	$str = array_shift($tab);
	if ($extra) {
		$tab = array_filter(explode("_", $str));
		$str = array_pop($tab);
	}
	return $str;
}

function createSidebar($dir) {
	$ep = "Resources_";
	$fdS = fopen($dir . "_Sidebar.md", "w");
	$fdI = fopen($dir . $ep."Index_" . sanitize($dir) . ".md", "w");
	$files = glob("$dir*", GLOB_MARK);
	$head = "[back home](Home)\r\n\r\n[back to resources](".$ep."Index_resources)\r\n\r\n";
	$title = "**" . sanitize($dir) . "**" . "\r\n";
	$file_link = "";
	$dir_link = "";
	foreach ($files as $file) {
		if (preg_match("/_Sidebar|Index_/", $file) === 1)
			continue;
		else if (is_dir($file)) {
			$dir_link .= "* [" . sanitize($file, true) . "](" . $ep."Index_" . sanitize($file) . ")" . "\r\n";
			createSidebar($file);
		} else {
			$file_link .= "* [" . sanitize($file, true) . "](" . sanitize($file) . ")\r\n";
		}
	}
	fwrite($fdS, $head . $title . $dir_link . $file_link);
	fwrite($fdI, $head . $title . $dir_link . $file_link);
	fclose($fdS);
	fclose($fdI);
}

$dirI = "../../../api-lib/lib/resources/";
$dirO = "resources/";
$wiki = "../../../../api.wiki/";

$obj = sanitizeFiles(getAllFiles(split_dir($dirI)));
createDoc($obj, $dirO);
createSidebar($dirO);
move_dir($dirO, $wiki);
?>
