<?php

$addon_xml_files = list_addon_xml_files();

if(should_rebuild_addons_xml($addon_xml_files)) {
	$xml = build_addons_xml($addon_xml_files);
	file_put_contents('addons.xml', $xml);
	$xml_md5 = md5($xml);
	file_put_contents('addons.xml.md5', $xml_md5);
}

header('Content-Type: text/xml; charset=UTF-8');
echo file_get_contents('addons.xml');


/**
 * @param $addon_xml_files
 */
function should_rebuild_addons_xml($addon_xml_files) {
	return true;	
}

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

function build_addons_xml($addon_xml_files) {
	$date = date(DATE_RFC822);
	$addons_xml = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!-- Generated on $date -->
<addons>

XML;

	foreach($addon_xml_files as $file) {
		$xml = file_get_contents($file);
		if(substr($xml, 0, 5) == '<?xml') {
			// strip first line
			$xml = substr($xml, strpos($xml, "\n") + 1);
		}

		$addons_xml .= $xml;
	}

	$addons_xml .= <<<XML
</addons>
XML;
	
	return $addons_xml;
}
