<?php
include('inc.php');

$time_cnt = 0;
while (true) {
	try {
		$time_cnt ++;
		if (($time_cnt % 10) == 0)
			__log('QUEUE PRINT RUN');

		$tasks = _query_all_ready_print_files_queues();
		if (count($tasks) > 0) __log('TASK:'.count($tasks));
		else if (($time_cnt % 10) == 0) __log('TASK:'.count($tasks));
		foreach ($tasks as $task) {
			$task_id = $task['id'];
			$file_id = $task['file_id'];
			$printer_id = $task['printer_id'];
			$p = _query_printer($printer_id);

			if (!$p) {
				__log('printer not found:'.$printer_id);
				_update_print_files_queue_status($task_id, PRINT_FILES_STATUS::ERROR->value);
			}
			
			$pstatus = _query_printer_info($p['host'], $p['port']);
			$status = _query_printer_objects($p['host'], $p['port'], array('print_stats'=>null));
			
			$s1 = $pstatus['result']['state'];
			$s2 = $status['result']['status']['print_stats']['state'];
			
			if ($s1 == 'ready' && ($s2 == 'standby' || $s2 == 'complete')) {
				$file_ex = false;
				$fs = _query_printer_files($p['host'], $p['port']);
				$files = $fs['result']['files'];
				foreach ($files as $file) {
					if ($file['filename'] == $file_id.'.gcode') {
						$file_ex = true;
					}
				}
				if ($file_ex == false) {
					__log('start upload file:'.$file_id);
					_upload_printer_file($p['host'], $p['port'], $file_id);
					__log('uploaded file:'.$file_id);
				}
				
				$r = _start_print($p['host'], $p['port'], $file_id);
				if ($r['result'] == 'ok') {
					$r = _update_print_files_queue_status($task['id'], PRINT_FILES_STATUS::PRINTING->value);
					if (!$r) { 
						__log('update task status error:'.var_dump($r));
					}
					_log(LOG_LEVEL::INFO->value, "PRINT QUEUE RUN:$task_id $r", 0, M_TYPE::QUEUE->value);
					__log('print:'.$file_id.', printer:'.$p['name']);
				} else {
					__log('print error:'.$r.', '.$file_id.', printer:'.$p['name']);
				}
			} else {
				__log('printer busy:'.$p['name'].' '.$s1.' '.$s2);
			}
		}
	}catch (Exception $e) {
		__log('error:'.$e);
	}
	sleep(1);
}
