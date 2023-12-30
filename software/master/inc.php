<?php
$mysqli = new mysqli('localhost', 'root', 'dogunhaha', '3d_world');

enum M_TYPE:string {
	case PRINTER = 'PRINTER';
	case FORKLIFT = 'FORKLIFT';
	case ADMIN = 'ADMIN';
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

function _log($level, $log, $m_id, $m_type) {
	global $mysqli;
	$m_id = intval($m_id);
	$m_type = M_TYPE::from($m_type);
	$level = LOG_LEVEL::from($level);
	$pr = $mysqli->query("insert into log (m_id, m_type, time, level, log) values ($m_id, '".$m_type->value."', CURRENT_TIMESTAMP(), '".$level->value."', '".$mysqli->real_escape_string($log)."')");
	var_export($pr);
}


#test
_log('DEBUG', 'test " log', 1, 'PRINTER');