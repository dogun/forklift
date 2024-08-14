<?php
include('forklift/inc.php');
$uid = $_GET['p'];
$tvoc = intval($_GET['t']);
$co2 = intval($_GET['c']);
$f = intval($_GET['j']);

if ($uid != 'work1' && $uid != 'work2') die('uid error');
if ($tvoc <= 0 || $co2 <= 0 || $f <= 0) die('val error');

$q = "insert into tvoc_log (uid, tvoc, co2, f) values ('$uid', $tvoc, $co2, $f)";
$r = $mysqli->query($q);
print_r($r);
