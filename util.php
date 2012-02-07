<?php
define('ADDONS_XML', 'addons.xml');
define('ADDONS_XML_MD5', 'addons.xml.md5');

/**
 */
function list_addon_xml_files() {
	$addon_xml_files = array();

	$dir = opendir(dirname(__FILE__));
	while(($path = readdir($dir)) !== false) {
		if($path[0] != '.' && is_dir($path)) {
			$addon_xml = $path.'/addon.xml';
			if(file_exists($addon_xml)) {
				$addon_xml_files[] = $addon_xml;
			}
		}
	}

	return $addon_xml_files;
}


