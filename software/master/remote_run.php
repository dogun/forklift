<?php

include('inc.php');

$action = @$_GET['action'];
$macro = @$_GET['macro'];
$r_host = @$_SERVER['REMOTE_ADDR'];
$printer = @$_GET['printer'];

_log('INFO', "$printer $action $macro @ $r_host", 0, 'HTTP');

//2024.11.05 所有都异步处理
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
	$board_id = $f_r['board_id'];
	_queue($printer_id, $forklift_id, $board_id, QUEUE_ACTION::PRINTER_CALL_FORKLIFT->value, $macro);
	echo 'QUEUED';
} elseif ($action == 'call_printer') {
	$f_r = _query_forklift_by_name($printer);
	if (!$f_r) {
		die('FORKLIFT NOT FOUND:'.$printer);
	}
	$f_id = $f_r['id'];
	$p_id = $f_r['now_printer'];
	$b_id = $f_r['board_id'];
	
	$p_r = _query_printer($p_id);
	if (!$p_r) {
		die('PRINTER NOT FOUND:'.$p_id);
	}
	$printer_id = $p_r['id'];
	$forklift_id = $p_r['forklift_id'];
	_queue($printer_id, $forklift_id, $b_id, QUEUE_ACTION::FORKLIFT_CALL_PRINTER->value, $macro);
	echo 'QUEUED';
} elseif ($action == 'forklift_ready') {
	$f_r = _query_forklift_by_name($printer);
	if (!$f_r) {
		die('FORKLIFT NOT FOUND:'.$printer);
	}
	$f_id = $f_r['id'];
	$p_id = $f_r['now_printer'];
	
	$p_r = _query_printer($p_id);
	if (!$p_r) {
		echo 'PRINTER NOT FOUND:'.$p_id;
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
	$printer_id = $f_r['now_printer'];
	$forklift_id = $f_r['id'];
	$board_id = $b_id;
	_queue($printer_id, $forklift_id, $board_id, QUEUE_ACTION::FORKLIFT_CALL_BOARD->value, $macro);
	echo 'QUEUED';
} elseif ($action == 'board_call_forklift') {
	$b_r = _query_board_by_name($printer);
	if (!$b_r) {
		die('BOARD NOT FOUND:'.$printer);
	}
	$b_id = $b_r['id'];
	$f_id = $b_r['forklift_id'];
	
	$f_r = _query_forklift($f_id);
	if (!$f_r) {
		die('FORKLIFT NOT FOUND:'.$f_id);
	}
	$printer_id = $f_r['now_printer'];
	$forklift_id = $f_r['id'];
	$board_id = $b_id;
	_queue($printer_id, $forklift_id, $board_id, QUEUE_ACTION::BOARD_CALL_FORKLIFT->value, $macro);
	echo 'QUEUED';
}
