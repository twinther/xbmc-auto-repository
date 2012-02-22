<?php
require_once('util.php');

check_preconditions();

$addon_xml_files = list_addon_xml_files();
if(should_rebuild_addons_xml($addon_xml_files)) {
	build_addons_xml($addon_xml_files);
}

$db = new AddonStatsDB();
$db->register_visit();
$db->close();

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
	if(!file_exists(ADDONS_XML)) {
		return true;
	}
	$last_modified = filemtime(ADDONS_XML);

	// Check for any new or updated addons
	foreach($addon_xml_files as $file) {
		if($last_modified < filemtime($file)) {
			return true;
		}
	}

	// Check for deleted addons
	$dom = new DOMDocument();
	$dom->load(ADDONS_XML);
	$xpath = new DOMXpath($dom);

	$nodes = $xpath->query('/addons/addon/@id');
	$len = $nodes->length;
	for($i=0; $i<$len; $i++) {
		$node = $nodes->item($i);
		
		if(!file_exists($node->nodeValue)) {
			return true;
		}
	}

	return false;
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

