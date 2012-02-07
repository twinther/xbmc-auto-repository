<?php
require_once('util.php');

$addon = isset($_REQUEST['addon']) ? $_REQUEST['addon'] : false;
if(!$addon) {
	exit('No addon specified!');
}

if(is_allowed($addon)) {
	$tempname = tempnam(sys_get_temp_dir(), 'xbmcautorepo-');
	$zip = new ZipArchive();
	if($zip->open($tempname, ZipArchive::CREATE)) {
		add_files($zip, $addon);
		$zip->close();

		header('Content-type: application/zip');
		header('Content-disposition: attachment; filename='.$addon.'.zip');
		readfile($tempname);
		unlink($tempname);
	} else {
		echo 'There was a problem generating the ZIP-file';
	}
} else {
	echo 'You are not allowed to ZIP this folder!';
}

function is_allowed($addon) {
	$addon_xml_files = list_addon_xml_files();
	foreach($addon_xml_files as $addon_xml) {
		if(dirname($addon_xml) == $addon) {
			return true;
		}
	}

	return false;
}

function add_files($zip, $path) {
	$dir = opendir($path);
	while(($entry = readdir($dir)) != false) {
		$full_path = $path.'/'.$entry;

		if($entry != '.' && $entry != '..' && $entry != '.git') {
			if(is_dir($full_path)) {
				add_files($zip, $full_path);
			} else {
				$zip->addFile($full_path);
			}
		}
	}
}
