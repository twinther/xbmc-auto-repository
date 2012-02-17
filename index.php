<!DOCTYPE html>
<html>
	<head>
		<title>xbmc-auto-repository</title>
		<style type="text/css">

* {
	font-family: Arial;
}

a:link, a:visited {
	color: blue;
}

a:hover {
	color: red;
}

#header {
	background-color: #9ab;
	border-radius: 15px;
	margin: 10px;
	padding-top: 30px;
	padding-left: 30px;
	height: 80px;
}

#header .title {
	font-size: x-large;
	font-weight: bold;
	clear: both;
	float: left;
}

#header .github {
	font-size: smaller;
	clear: both;
	float: left;
}

#stats {
	background-color: #acd;
	border-radius: 15px;
	margin: 20px;
	padding: 20px;
	position: absolute;
	top: 110px;
	right: 0px;
}

#addons {
	padding: 10px;
	position: absolute;
	top: 110px;
	right: 255px;
	left: 0px;
}

table.addon {
	background-color: #eee;
	border-radius: 15px;
	margin: 10px;
	padding: 20px;
	height: 64px;
}

table.addon img {
	width: 64px;
	padding-right: 10px;
}

table.addon .info {
	width: 200px;
	vertical-align: top;
}

table.addon .img {
	width: 84px;
	vertical-align: top;
}

table.addon .name {
	font-weight: bold;
	display: block;
	width: 100%;
}

table.addon .version {
	font-size: smaller;
	display: block;
}

table.addon .last_updated {
	font-size: smaller;
}

table.addon .links {
	display: block;
	text-align: right;
	font-size: smaller;
}

table.addon .description {
	font-size: smaller;
}

		</style>
	</head>

	<body>
		<div id="header">
			<span class="title">xbmc-auto-repository</span>
			<span class="github"><a href="https://github.com/twinther/xbmc-auto-repository/">github.com/twinther/xbmc-auto-repository/</a></span>
		</div>
<?php
require_once('util.php');
require_once('stats.php');

echo build_stats_html();

echo <<<HTML
		<div id="addons">

HTML;

$addons = list_addon_xml_files();
usort($addons, 'compare_mtime');
$dom = new DOMDocument();
foreach($addons as $addon) {
	$dom->load($addon);
	$xpath = new DOMXpath($dom);

	$node = $xpath->query('/addon')->item(0);
	$id = $node->getAttribute('id');
	$name = $node->getAttribute('name');
	$version = $node->getAttribute('version');

	$node = $xpath->query('/addon/extension[@point="xbmc.addon.metadata"]/description')->item(0);
	$description = utf8_decode(str_replace('[CR]', '<br />', $node->textContent));

	$last_updated = date('j. M Y, G:i', filemtime($addon));

	echo <<<HTML
		<table class="addon">
			<tr>
				<td rowspan="2" class="img">
					<img src="{$id}/icon.png" />
				</td>
				<td rowspan="2" class="info">
					<span class="name">{$name}</span>
					<span class="version">v. {$version}</span>
					<span class="last_updated">Updated on {$last_updated}</span>
				</td>
				<td class="description">{$description}</td>
			</tr>
			<tr>
				<td class="links"><a href="{$id}">{$id}</a> | <a href="zip.php?addon={$id}">zip</a> | <a href="{$id}/changelog.txt">changelog</a></td>
			</tr>
		</table>

HTML;
}

function compare_mtime($mine, $yours) {
	return filemtime($mine) < filemtime($yours);
}


?>
		</div>
	</body>
</html>

