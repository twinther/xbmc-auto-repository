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

class AddonStatsDB extends SQLite3 {
	function __construct() {
		$this->open('addonstats.db');
		$this->exec('CREATE TABLE IF NOT EXISTS stats(
			id INTEGER PRIMARY KEY,
			user_agent TEXT,
			ip_address TEXT,
			last_accessed INTEGER,
			access_count INTEGER
			);'
		);
	}

	function register_visit() {
		$stmt = $this->prepare('SELECT id, last_accessed FROM stats WHERE user_agent=:user_agent AND ip_address=:ip_address');
		$stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT']);
		$stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR']);
		$result = $stmt->execute();

		if(($row = $result->fetchArray()) === false) {
			$stmt = $this->prepare('INSERT INTO stats(user_agent, ip_address, last_accessed, access_count) VALUES(:user_agent, :ip_address, datetime(\'now\'), 1);');
			$stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT']);
			$stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR']);
			$stmt->execute();
		} else {
			$stmt = $this->prepare('UPDATE stats SET last_accessed=datetime(\'now\'), access_count=access_count+1 WHERE id=:id;');
			$stmt->bindValue(':id', $row['id']);
			$stmt->execute();
		}
	}

	function get_all_visits() {
		$stmt = $this->prepare('SELECT * FROM stats');
		$result = $stmt->execute();

		$visits = array();
		while(($row = $result->fetchArray()) !== false) {
			$visits[] = $row;
		}
		$result->finalize();

		return $visits;
	}

	function get_visits_per_user_agent() {
		$stmt = $this->prepare('SELECT user_agent, COUNT(*) AS visits FROM stats GROUP BY user_agent ORDER BY user_agent DESC');
		$result = $stmt->execute();

		$visits = array();
		while(($row = $result->fetchArray()) !== false) {
			$visits[] = $row;
		}
		$result->finalize();

		return $visits;
	}
}
