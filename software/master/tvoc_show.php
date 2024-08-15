<?php
include('forklift/inc.php');
$stime = intval($_GET['stime']);
$etime = intval($_GET['etime']);
$uid = $_GET['uid'];

$q = "select uid, tvoc, co2, f, time from tvoc_log where uid=''";
if ($stime > 0) $q .= " and time >= CURRENT_TIMESTAMP() - $stime";
if ($etime > 0) $q .= " and time <= CURRENT_TIMESTAMP() - $etime";

echo $q;