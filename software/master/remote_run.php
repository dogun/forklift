<?php

include('inc.php');

$action = @$_GET['action'];
$macro = @$_GET['macro'];
$host = @$_SERVER['REMOTE_ADDR'];
$printer = @$_GET['printer'];

echo "$printer $action $macro $host \n";

if ($action == 'call_forklift') {
	$pr = $mysqli->query("select * from printers where name='".$mysqli->real_escape_string($printer)."'");
	$r = $pr->fetch_assoc();
	if (!$r) {
		die('ERROR: PRINTER NOT FOUND');
	}

	$forklift_id = $r['forklift_id'];
	$fl = $mysqli->query("select * from forklifts where id=$forklift_id");
	$r = $fl->fetch_assoc();
	if (!$r) {
		die('ERROR: FORKLIFT NOT FOUND');
	}

	$host = $r['host'];
	$port = $r['port'];
	$url = "http://$host:$port/printer/gcode/script?script=SHOULIAO";
	echo file_get_contents($url);

}
