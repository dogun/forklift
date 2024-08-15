<?php
include('forklift/inc.php');
$stime = intval($_GET['stime']);
$etime = intval($_GET['etime']);
$uid = $mysqli->real_escape_string($_GET['uid']);

$q = "select uid, tvoc, co2, f, time from tvoc_log where uid='$uid'";
if ($stime > 0) $q .= " and time >= CURRENT_TIMESTAMP() - $stime";
if ($etime > 0) $q .= " and time <= CURRENT_TIMESTAMP() - $etime";
$q .= " order by id desc";

$r = mysqli->query($q) or die('');
$list = $r->fetch_assoc();
for ($list as $val) {
	echo $val['uid'].' '.$val['tvoc'].'<br />';
}