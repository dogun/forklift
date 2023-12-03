<?php

include('inc.php');

$action = @$_GET['action'];
$macro = @$_GET['macro'];
$host = @$_SERVER['REMOTE_ADDR'];
$printer = @$_GET['printer'];

echo "$printer $action $macro $host";

$pr = $mysqli->query("select * from printers where name='".$mysqli->real_escape_string($printer)."'");
$r = $pr->fetch_assoc();
print_r($r);

