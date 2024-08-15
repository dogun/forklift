<?php
include('inc.php');
$stime = intval(@$_GET['stime']);
$etime = intval(@$_GET['etime']);
$uid = $mysqli->real_escape_string(@$_GET['uid']);

$q = "select uid, tvoc, co2, f, time from tvoc_log where uid='$uid'";
if ($stime > 0) $q .= " and time >= CURRENT_TIMESTAMP() - $stime";
if ($etime > 0) $q .= " and time <= CURRENT_TIMESTAMP() - $etime";
$q .= " order by id desc";

$r = $mysqli->query($q) or die('');
$arr = array();
while (($list = $r->fetch_assoc())) {
	if ($uid == 'work1') $tvoc = $list['tvoc'] - 230;
	if ($uid == 'work2') $tvoc = $list['co2'];
	$arr[] = array($list['time'], $tvoc);
}

foreach ($arr as $v) {
	echo $v[0].' '.$v[1].'<br />';
	print_r($v[0]);
}

