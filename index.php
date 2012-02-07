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

div.addon {
	background-color: #eee;
	border-radius: 15px;
	margin: 10px;
	padding: 20px;
	height: 64px;
	width: 45%;
	float: left;
}

div.addon img {
	width: 64px;
	float: left;
	padding-right: 10px;
}

div.addon .name {
	font-weight: bold;
	display: block;
	width: 100%;
}

div.addon .version {
	font-size: smaller;
	display: block;
}

div.addon .last_updated {
	font-size: smaller;
}

div.addon .id {
	display: block;
	text-align: right;
	font-size: smaller;
}

		</style>
	</head>

	<body>
		<div id="header">
			<span class="title">XBMC auto-repository</span>
			<span class="github"><a href="https://github.com/twinther/xbmc-auto-repository/">github.com/twinther/xbmc-auto-repository/</a></span>
		</div>
<?php
require_once('util.php');

$addons = list_addon_xml_files();
$dom = new DOMDocument();




foreach($addons as $addon) {
	$dom->load($addon);
	$xpath = new DOMXpath($dom);

	$node = $xpath->query('/addon')->item(0);
	$id = $node->getAttribute('id');
	$name = $node->getAttribute('name');
	$version = $node->getAttribute('version');
	$last_updated = date('j. M Y, G:i', filemtime($addon));

	echo <<<HTML
		<div class="addon">
			<img src="{$id}/icon.png" />
			<span class="name">{$name}</span>
			<span class="version">v. {$version}</span>
			<span class="last_updated">Updated on {$last_updated}</span>
			<span class="id"><a href="{$id}">{$id}</a> | <a href="zip.php?addon={$id}">zip</a></span>
		</div>

HTML;
}


?>
	</body>
</html>

