<?php
include('pw.php');
$mysqli = new mysqli('localhost', 'root', $pw, '3d_world');

enum M_TYPE:string {
	case PRINTER = 'PRINTER';
	case FORKLIFT = 'FORKLIFT';
	case BOARD = 'BOARD';
	case QUEUE = 'QUEUE';
	case ADMIN = 'ADMIN';
	case HTTP = 'HTTP';
}

enum M_STATUS:string {
	case READY = 'READY';
	case RUNNING = 'RUNNING';
	case ERROR = 'ERROR';
	case MAINTAIN = 'MAINTAIN';
}

enum LOG_LEVEL:string {
	case DEBUG = 'DEBUG';
	case INFO = 'INFO';
	case WARN = 'WARN';
	case ERROR = 'ERROR';
}

enum QUEUE_STATUS:string {
	case READY = 'READY';
	case RUNNING = 'RUNNING';
	case FINISHED = 'FINISHED';
	case ERROR = 'ERROR';
}

enum QUEUE_ACTION:string {
	case PRINTER_CALL_FORKLIFT = 'PRINTER_CALL_FORKLIFT';
	case FORKLIFT_CALL_BOARD = 'FORKLIFT_CALL_BOARD';
	case FORKLIFT_CALL_PRINTER = 'FORKLIFT_CALL_PRINTER';
	case BOARD_CALL_FORKLIFT = 'BOARD_CALL_FORKLIFT';
}

function __log($log) {
	$now = date('Y-m-d H:i:s');
	echo "$now $log\n";
}

function _log($level, $log, $m_id, $m_type) {
	global $mysqli;
	$m_id = intval($m_id);
	$m_type = M_TYPE::from($m_type);
	$level = LOG_LEVEL::from($level);
	$pr = $mysqli->query("insert into log (m_id, m_type, time, level, log) values ($m_id, '".$m_type->value."', CURRENT_TIMESTAMP(), '".$level->value."', '".$mysqli->real_escape_string($log)."')");
	return $pr;
}

function _query_log() {
	global $mysqli;
	$r = $mysqli->query("select * from log order by id desc");
	$ret = array();
	while (($q = $r->fetch_assoc())) {
		$ret[] = $q;
	}
	return $ret;
}

function _queue($printer_id, $forklift_id, $board_id, $action, $content) {
	global $mysqli;
	$printer_id = intval($printer_id);
	$forklift_id = intval($forklift_id);
	$board_id = intval($board_id);
	$action = QUEUE_ACTION::from($action);
	$pr = $mysqli->query("insert into action_queue (printer_id, forklift_id, board_id, action, content, created, status) values ($printer_id, $forklift_id, $board_id, '".$action->value."', '".$mysqli->real_escape_string($content)."', CURRENT_TIMESTAMP(), '".QUEUE_STATUS::READY->value."')");
	return $pr;
}

function _query_ready_tasks() {
	global $mysqli;
	$ret = array();
	$r = $mysqli->query("select * from action_queue where status='".QUEUE_STATUS::READY->value."' order by id asc limit 1");
	while (($row = $r->fetch_assoc()) != NULL) {
		$ret[] = $row;
	}
	return $ret;
}

function _update_task_status($id, $status) {
	global $mysqli;
	$id = intval($id);
	$status = QUEUE_STATUS::from($status);
	$r = $mysqli->query("update action_queue set status='".$status->value."', modified=CURRENT_TIMESTAMP() where id=$id");
	return $r;
}

function _query_printer($printer_id) {
	global $mysqli;
	$printer_id = intval($printer_id);
	$pr = $mysqli->query("select * from printers where id=$printer_id");
	$r = $pr->fetch_assoc();
	return $r;
}

function _query_printer_by_name($printer_name) {
	global $mysqli;
	$pr = $mysqli->query("select * from printers where name='".$mysqli->real_escape_string($printer_name)."'");
	$r = $pr->fetch_assoc();
	return $r;
}

function _query_forklift($fl_id) {
	global $mysqli;
	$fl_id = intval($fl_id);
	$fl = $mysqli->query("select * from forklifts where id=$fl_id");
	$r = $fl->fetch_assoc();
	return $r;
}

function _query_forklift_by_name($fl_name) {
	global $mysqli;
	$fl = $mysqli->query("select * from forklifts where name='".$mysqli->real_escape_string($fl_name)."'");
	$r = $fl->fetch_assoc();
	return $r;
}

function _update_forklift_now_printer($fl_id, $p_id) {
	global $mysqli;
	$fl_id = intval($fl_id);
	$p_id = intval($p_id);
	$q = $mysqli->query("update forklifts set now_printer=$p_id , modified=CURRENT_TIMESTAMP() where id=$fl_id");
	return $q;
}

function _update_forklift_now_printer_and_status($fl_id, $p_id, $status) {
	global $mysqli;
	$fl_id = intval($fl_id);
	$p_id = intval($p_id);
	$status = M_STATUS::from($status);
	$q = $mysqli->query("update forklifts set now_printer=$p_id , modified=CURRENT_TIMESTAMP(), status='".$status->value."' where id=$fl_id");
	return $q;
}

function _query_board_by_name($b_name) {
	global $mysqli;
	$fl = $mysqli->query("select * from boards where name='".$mysqli->real_escape_string($b_name)."'");
	$r = $fl->fetch_assoc();
	return $r;
}

function _query_board($b_id) {
	global $mysqli;
	$b_id = intval($b_id);
	$fl = $mysqli->query("select * from boards where id=$b_id");
	$r = $fl->fetch_assoc();
	return $r;
}

function _remote_run_macro($call_id, $call_type, $target_id, $action, $host, $port, $macro) {
	$url = "http://$host:$port/printer/gcode/script?script=$macro";
	return _remote_run($call_id, $call_type, $target_id, $action, $url);
}

function _remote_run($call_id, $call_type, $target_id, $action, $url) {
	$res = file_get_contents($url);
	__log($url);
	_log(LOG_LEVEL::INFO->value, "RC $call_id $action $target_id $url $res", $call_id, $call_type);
	return $res;
}

#test
#_log('DEBUG', 'test " log', 1, 'PRINTER');
#echo _queue(1, 'PRINTER', 'CALL_FORKLIFT', 'content " content');
#$p = _query_printer_by_name('NO.2');
#var_dump($p);
#$fl = _query_forklift(0);
#var_dump($fl);
#var_dump(_query_printer(1));
#var_dump(_query_forklift_by_name('fl.1'));
#var_dump(_queue_collect_unserialize(_queue_collect_serialize(1, 2)));
#var_dump(_remote_run(1, 'PRINTER', 2, 'CALL_FORKLIFT', 'http://localhost/'));
#var_dump(_query_ready_task());
#var_dump(_update_forklift_now_printer(1, 1));
#var_dump(_remote_run_macro(1, 'PRINTER', 2, 'CALL_FORKLIFT', 'localhost', 80, 'test'));
#var_dump(_update_task_status(5, 'ERROR'));
#echo _queue(1, 'PRINTER', 'CALL_FORKLIFT_COLLECT', _queue_collect_serialize(3, 3));
#var_dump(_query_ready_task_collect());
#var_dump(_query_log());
#var_dump(_query_board_by_name('b1'));
