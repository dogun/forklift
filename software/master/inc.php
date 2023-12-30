<?php
$mysqli = new mysqli('localhost', 'root', 'dogunhaha', '3d_world');

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
	case CALL_FORKLIFT = 'CALL_FORKLIFT';
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

#test
#_log('DEBUG', 'test " log', 1, 'PRINTER');
#echo _queue(1, 'PRINTER', 'CALL_FORKLIFT', 'content " content');
#$p = _query_printer_by_name('NO.2');
#var_dump($p);
#$fl = _query_forklift(0);
#var_dump($fl);