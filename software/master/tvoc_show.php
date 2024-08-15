<?php
include('inc.php');
$stime = intval(@$_GET['stime']);
$etime = intval(@$_GET['etime']);

$dv = 60;

$s = time() - $stime;
$e = time() - $etime;

$q = "select uid, tvoc, co2, f, UNIX_TIMESTAMP(time) as t from tvoc_log where ";
$q .= " time >= FROM_UNIXTIME($s)";
$q .= " and time <= FROM_UNIXTIME($e)";
$q .= " order by id asc";

$r = $mysqli->query($q) or die('');
$a1 = array();
$a2 = array();
while (($list = $r->fetch_assoc())) {
	$time = intval($list['t'] / $dv);
	$uid = $list['uid'];
	if ($uid == 'work1') {
		$tvoc = $list['tvoc'] - 230;
		$a1[$time] = $tvoc;
	}
	if ($uid == 'work2') {
		$tvoc = $list['co2'] * 3;
		$a2[$time] = $tvoc;
	}
}

?>
<html>
<body>
<table border="0">
<?php
$s = intval($s / $dv);
$e = intval($e / $dv);
$l1 = 0;
$l2 = 0;
for ($i = $s; $i < $e; ++$i) {
	$tvoc1 = intval(@$a1[$i]);
	$tvoc2 = intval(@$a2[$i]);
	if ($tvoc1 == 0) $tvoc1 = $l1;
	if ($tvoc2 == 0) $tvoc2 = $l2;
	$l1 = $tvoc1;
	$l2 = $tvoc2;
	echo '<tr>';
	echo '<td>'.date('Y-m-d H:i:s', $i*$dv).'</td>';
	echo '<td>';
	echo '<table border="0" style="background-color:#F00"><tr><td style="width:'.$tvoc1.'px">'.$tvoc1.'</td></tr></table>';
	echo '<table border="0" style="background-color:#000"><tr><td style="width:'.$tvoc2.'px;color:#FFF">'.$tvoc2.'</td></tr></table>';
	echo '</td>';
	echo '</tr>';
}
?>
</table>
</body></html>
