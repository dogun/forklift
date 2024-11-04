<?php

include('inc.php');

$action = @$_GET['action'];
$macro = @$_GET['macro'];
$r_host = @$_SERVER['REMOTE_ADDR'];
$printer = @$_GET['printer'];

_log('INFO', "$printer $action $macro @ $r_host", 0, 'HTTP');

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

	if (!$macro || $macro == 'NONE') { //没有指定宏，则异步收料
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
} elseif ($action == 'forklift_ready') {
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
	$r = _update_forklift_now_printer_and_status($f_id, 0, M_STATUS::READY->value);
	if (!$r) {
		__log('update now printer error:'.var_dump($r));
	}
	echo $res;
} elseif ($action == 'call_board') {
	$f_r = _query_forklift_by_name($printer);
	if (!$f_r) {
		die('FORKLIFT NOT FOUND:'.$printer);
	}
	$f_id = $f_r['id'];
	$b_id = $f_r['board_id'];
	
	$p_r = _query_board($b_id);
	if (!$p_r) {
		die('BOARD NOT FOUND:'.$p_id);
	}
	$host = $p_r['host'];
	$port = $p_r['port'];
	$res = _remote_run_macro($f_id, M_TYPE::FORKLIFT->value, $p_id, $action, $host, $port, $macro);
	echo $res;
} elseif ($action == 'board_call_forklift') {
	$f_r = _query_board_by_name($printer);
	if (!$f_r) {
		die('BOARD NOT FOUND:'.$printer);
	}
	$f_id = $f_r['id'];
	$b_id = $f_r['forklift_id'];
	
	$p_r = _query_forklift($b_id);
	if (!$p_r) {
		die('FORKLIFT NOT FOUND:'.$p_id);
	}
	$host = $p_r['host'];
	$port = $p_r['port'];
	$res = _remote_run_macro($f_id, M_TYPE::FORKLIFT->value, $p_id, $action, $host, $port, $macro);
	echo $res;
}
