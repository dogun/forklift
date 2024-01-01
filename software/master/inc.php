<?php
include('pw.php');
$mysqli = new mysqli('localhost', 'root', $pw, '3d_world');

enum M_TYPE:string {
	case PRINTER = 'PRINTER';
	case FORKLIFT = 'FORKLIFT';
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
	case CALL_FORKLIFT_COLLECT = 'CALL_FORKLIFT_COLLECT';
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

function _queue($m_id, $m_type, $action, $content) {
	global $mysqli;
	$m_id = intval($m_id);
	$m_type = M_TYPE::from($m_type);
	$action = QUEUE_ACTION::from($action);
	$pr = $mysqli->query("insert into action_queue (m_id, m_type, action, content, created, status) values ($m_id, '".$m_type->value."', '".$action->value."', '".$mysqli->real_escape_string($content)."', CURRENT_TIMESTAMP(), '".QUEUE_STATUS::READY->value."')");
	return $pr;
}

function _query_ready_task() {
	global $mysqli;
	$r = $mysqli->query("select * from action_queue where status='".QUEUE_STATUS::READY->value."' order by id asc limit 1");
	return $r->fetch_assoc();
}

function _query_ready_task_collect() {
	global $mysqli;
	$r = $mysqli->query("select * from action_queue where action='".QUEUE_ACTION::CALL_FORKLIFT_COLLECT->value."' and status='".QUEUE_STATUS::READY->value."' order by id asc");
	$arr = array();
	while (($q = $r->fetch_assoc())) {
		$content = _queue_collect_unserialize($q['content']);
		$q['printer_id'] = $content['printer_id'];
		$q['forklift_id'] = $content['forklift_id'];
		$forklift_id = $content['forklift_id'];
		if (!@$arr[$forklift_id]) { //不存在这个叉车，就加入处理，只加入最早的一个
			$arr[$forklift_id] = $q;
		}
	}
	return $arr;
}

function _update_task_status($id, $status) {
	global $mysqli;
	$id = intval($id);
	$status = QUEUE_STATUS::from($status);
	$r = $mysqli->query("update action_queue set status='".$status->value."' where id=$id");
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
	$q = $mysqli->query("update forklifts set now_printer=$p_id where id=$fl_id");
	return $q;
}

function _queue_collect_serialize($print_id, $forklift_id) {
	$arr = array();
	$arr['printer_id'] = $print_id;
	$arr['forklift_id'] = $forklift_id;
	return serialize($arr);
}

function _queue_collect_unserialize($str) {
	return unserialize($str);
}

function _remote_run_macro($call_id, $call_type, $target_id, $action, $host, $port, $macro) {
	$url = "http://$host:$port/printer/gcode/script?script=$macro";
	return _remote_run($call_id, $call_type, $target_id, $action, $url);
}

function _remote_run($call_id, $call_type, $target_id, $action, $url) {
	$res = file_get_contents($url);
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