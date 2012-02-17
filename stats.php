<?php
require_once('util.php');

$db = new AddonStatsDB();
$visits = $db->get_all_visits();
$db->close();

foreach($visits as $visit) {
	echo $visit['last_accessed'].' - '.$visit['ip_address'].' ('.$visit['access_count'].') : '. $visit['user_agent'].'<br />';
}
