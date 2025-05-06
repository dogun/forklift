<?php
include('pw.php');
$mysqli = new mysqli('localhost', 'root', $pw, '3d_world');

#$FILE_PATH = __DIR__.'/files/';
$FILE_PATH = '/root/files/';

enum USER_STATUS:string {
	case NORMAL = 'NORMAL';
	case DELETED = 'DELETED';
	case DISABLED = 'DISABLED';
}

enum USER_TYPE:string {
	case ADMIN = 'ADMIN';
	case USER = 'USER';
}

enum MATERIAL:string {
	case ABS = 'ABS';
	case PETH = 'PETG';
	case PLA = 'PLA';
	case TPU = 'TPU';
}

enum COLOR:string {
	case BLACK = 'BLACK';
	case WHITE = 'WHITE';
	case RED = 'RED';
	case ORANGE = 'ORANGE';
	case BLUE = 'BLUE';
	case NOT_SET = 'NOT_SET';
}

enum PRINT_FILES_STATUS:string {
	case INIT = 'INIT';
	case WAIT_PRINT = 'WAIT_PRINT';
	case PRINTING = 'PRINTING';
	case FINISHED = 'FINISHED';
}

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
	case CANCLED = 'CANCLED';
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

function _query_all_tasks() {
	global $mysqli;
	$ret = array();
	$r = $mysqli->query("select * from action_queue order by id desc limit 100");
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

function _query_all_printers() {
	global $mysqli;
	$pr = $mysqli->query("select * from printers order by name asc");
	$ret = array();
	while (($row = $pr->fetch_assoc()) != NULL) {
		$ret[] = $row;
	}
	return $ret;
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

function _query_all_forklifts() {
	global $mysqli;
	$pr = $mysqli->query("select * from forklifts order by name asc");
	$ret = array();
	while (($row = $pr->fetch_assoc()) != NULL) {
		$ret[] = $row;
	}
	return $ret;
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

function _query_all_boards() {
	global $mysqli;
	$pr = $mysqli->query("select * from boards order by name asc");
	$ret = array();
	while (($row = $pr->fetch_assoc()) != NULL) {
		$ret[] = $row;
	}
	return $ret;
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
	_log(LOG_LEVEL::INFO->value, "RC $call_id $action $target_id $url $res", $call_id, $call_id_type);
	return $res;
}

function _query_printer_info($host, $port) {
	$url = "http://$host:$port/printer/info";
	$res = file_get_contents($url);
	$data = json_decode($res, true);
	return $data;
}

function _query_printer_objects($host, $port, $objects) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://$host:$port/printer/objects/query");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = array(
		'objects' => $objects
	);

	$jsonData = json_encode($data);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($jsonData)
	));
	$response = curl_exec($ch);
	
	if (curl_errno($ch)) {
		echo 'cURL 错误: ' . curl_error($ch);
	} else {
		// 获取响应状态码
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// 根据状态码处理响应
		if ($statusCode == 200) {
			// 解析 JSON 响应
			$responseData = json_decode($response, true);

			// 检查是否成功解析 JSON
			if (json_last_error() === JSON_ERROR_NONE) {
				// 处理解析后的数据
				return $responseData;
			} else {
				echo 'JSON 解析错误: ' . json_last_error_msg();
			}
		} else {
			echo "请求失败，状态码: $statusCode";
		}
	}
	return array();
}

function calculateAge($birthDate) {
    // 解析给定的日期
    $birthDateTime = new DateTime($birthDate);
    // 获取当前日期
    $currentDateTime = new DateTime();

    // 计算年份差值
    $years = $currentDateTime->format('Y') - $birthDateTime->format('Y');
    // 计算月份差值
    $months = $currentDateTime->format('m') - $birthDateTime->format('m');

    // 如果当前月份小于出生月份，年份减 1，月份加上 12
    if ($months < 0) {
        $years--;
        $months += 12;
    }

    return array('years' => $years, 'months' => $months);
}

function _query_user($user_id) {
	global $mysqli;
	$user_id = intval($user_id);
	$u = $mysqli->query("select * from users where id=$user_id");
	$r = $u->fetch_assoc();
	return $r;
}

function _query_user_by_name($uname) {
	global $mysqli;
	$fl = $mysqli->query("select * from users where name='".$mysqli->real_escape_string($uname)."'");
	$r = $fl->fetch_assoc();
	return $r;
}

function _insert_print_file($name, $size, $user_id, $material, $color) {
	global $mysqli;
	$size = intval($size);
	$name = $mysqli->real_escape_string($name);
	$material = MATERIAL::from($material)->value;
	$color = COLOR::from($color)->value;
	$user_id = intval($user_id);
	$status = TASK_FILES_STATUS::INIT->value;
	$pr = $mysqli->query("insert into task_files (name, user_id, size, status, material, color) values ('$name', $user_id, $size, '$status', '$material', '$color')");
	return $mysqli->insert_id;
}

function _query_all_print_files() {
	global $mysqli;
	$pr = $mysqli->query("select * from print_files order by id asc");
	$ret = array();
	while (($row = $pr->fetch_assoc()) != NULL) {
		$ret[] = $row;
	}
	return $ret;
}

#test
#_log('DEBUG', 'test " log', 1, 'PRINTER');
#echo _queue(1, 'PRINTER', 'CALL_FORKLIFT', 'content " content');
#$p = _query_printer_by_name('NO.2');
#var_dump($p);
#$fl = _query_forklift(0);
#var_dump($fl);
#var_dump(_query_printer(8));
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
#var_dump(_query_forklift_by_name('b1'));
