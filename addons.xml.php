<?php

define('ADDONS_XML', 'addons.xml');
define('ADDONS_XML_MD5', 'addons.xml.md5');
check_preconditions();

$addon_xml_files = list_addon_xml_files();
if(should_rebuild_addons_xml($addon_xml_files)) {
	build_addons_xml($addon_xml_files);
}

header('Content-Type: text/xml; charset=UTF-8');
echo file_get_contents(ADDONS_XML);
exit;




/**
 */
function check_preconditions() {
	if(!is_writable(dirname(__FILE__))) {
		exit('ERROR: Current folder is not writable!');
	}
}

/**
 */
function should_rebuild_addons_xml($addon_xml_files) {
	$last_modified = -1;
	if(file_exists(ADDONS_XML)) {
		$last_modified = filemtime(ADDONS_XML);
	}

	foreach($addon_xml_files as $file) {
		if($last_modified < filemtime($file)) {
			return true;
		}
	}

	return false;
}

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

/**
 */
function build_addons_xml($addon_xml_files) {
	$date = date(DATE_RFC822);
	$addons_xml = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!-- Generated on $date -->
<addons>

XML;

	foreach($addon_xml_files as $file) {
		$xml = file_get_contents($file);
		if(strpos($xml, '<?xml') !== false) {
			// strip first line
			$xml = substr($xml, strpos($xml, "\n") + 1);
		}

		$addons_xml .= $xml;
	}

	$addons_xml .= <<<XML
</addons>
XML;
	
	file_put_contents(ADDONS_XML, $addons_xml);
	file_put_contents(ADDONS_XML_MD5, md5($addons_xml));
}

