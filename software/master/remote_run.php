<?php

include('inc.php');

$action = @$_GET['action'];
$macro = @$_GET['macro'];
$host = @$_SERVER['REMOTE_ADDR'];
$printer = @$_GET['printer'];

_log('INFO', "$printer $action $macro @ $host", 0, 'HTTP');

if ($action == 'call_forklift') {
	$r = _query_printer_by_name($printer);
	if (!$r) {
		die('PRINTER NOT FOUND:'.$printer);
	}
	$printer_id = $r['id'];
	$forklift_id = $r['forklift_id'];
	$f_r = _query_forklift($forklift_id);
	if (!$f_r) {
		die('FORKLIFT NOT FOUND:'.$forklift_id);
	}

	$host = $f_r['host'];
	$port = $f_r['port'];

	if (!$macro) { //没有指定宏，则异步收料
		$str = _queue_collect_serialize($printer_id, $forklift_id);
		_queue($printer_id, M_TYPE::PRINTER->value, QUEUE_ACTION::CALL_FORKLIFT_COLLECT->value, $str);
		echo 'QUEUED';
	} else {
		$res = _remote_run_macro($printer_id, M_TYPE::PRINTER->value, $forklift_id, $action, $host, $port, $macro);
		echo $res;
	}
} elseif ($action == 'call_printer') {
	$f_r = _query_forklift_by_name($printer);
	if (!$f_r) {
		die('FORKLIFT NOT FOUND:'.$printer);
	}
	$f_id = $f_r['id'];
	$p_id = $f_r['now_printer'];
	
	$p_r = _query_printer($p_id);
	if (!$p_r) {
		die('PRINTER NOT FOUND:'.$p_id);
	}
	$host = $p_r['host'];
	$port = $p_r['port'];
	$res = _remote_run_macro($f_id, M_TYPE::FORKLIFT->value, $p_id, $action, $host, $port, $macro);
	echo $res;
}
