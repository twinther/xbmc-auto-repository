<?php

function build_stats_html($width = 200) {
	$db = new AddonStatsDB();
	$visits = $db->get_visits_per_user_agent();
	$db->close();


	$stats = array();
	$total_visits = 0;
	$max_visits = 0;
	$other_visits = 0;
	foreach($visits as $visit) {
		$total_visits += $visit['visits'];
		if(substr($visit['user_agent'], 0, 4) != 'XBMC') {
			$other_visits += $visit['visits'];
			continue;
		}
		$user_agent_prefix = substr($visit['user_agent'], 0, strpos($visit['user_agent'], ' '));

		if(!isset($stats[$user_agent_prefix])) {
			$stats[$user_agent_prefix] = $visit['visits'];
		} else {
			$stats[$user_agent_prefix] += $visit['visits'];
		}

		if($visit['visits'] > $max_visits) {
			$max_visits = $visit['visits'];
		}
	}
	$stats['Non-XBMC'] = $other_visits;


	$html = <<<HTML
	<div id="stats">
		<span style="font-weight: bold; font-size: larger">Visits</span>
HTML;

	foreach($stats as $user_agent => $count) {
		$cell_width = (int) ($width * $count / $total_visits);
		$html .= <<<HTML
		<div style="background-color: #B4D9D9; height: 20px; width: {$width}px; position: relative; margin-bottom: 2px;">
			<div style="background-color: #708C87; height: 20px; width: {$cell_width}px; position: absolute;">&nbsp;</div>
			<span style="float: right; padding-right: 2px;">$count</span>
			<div style="position: absolute; height: 20px; padding-left: 2px;">$user_agent</div>
		</div>

HTML;
	}
	$html .= <<<HTML
	</div>

HTML;

	return $html;
}
