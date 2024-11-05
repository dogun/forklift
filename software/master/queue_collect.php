<?php
include('inc.php');

while (true) {
	//_log(LOG_LEVEL::INFO->value, 'QUEUE RUN', 0, M_TYPE::QUEUE->value);
	__log('QUEUE RUN');
	$tasks = _query_ready_tasks();
	__log('TASK:'.count($tasks));
	foreach ($tasks as $task) {
		$task_id = $task['id'];
		$action = $task['action'];
		$printer_id = $task['printer_id'];
		$forklift_id = $task['forklift_id'];
		$board_id = $task['board_id'];
		$fl = _query_forklift($forklift_id);
		if (!$fl) {
			__log('FORKLIFT NULL:'.$forklift_id);
			continue;
		}
		$fl_status = $fl['status'];
		__log('PROCESS TASK:'.$task_id);
		if ($fl_status == M_STATUS::READY->value) { //当前空闲，关联叉车和打印机
			__log('FORKLIFT READY, RUN...');
			$r = _update_forklift_now_printer_and_status($forklift_id, $printer_id, M_STATUS::RUNNING->value);
			if (!$r) {
				__log('update now printer and status error:'.$task_id);
			}
		} elseif ($fl_status == M_STATUS::MAINTAIN->value) { //维护中
			//下次再执行
			__log('FORKLIFT MAINTAIN, WAITTING...');
			continue;
		} elseif ($fl_status == M_STATUS::ERROR->value) { //出错了
			//TODO 报警
			__log('FORKLIFT ERROR');
			continue;
		}
		//重新查询叉车信息
		$fl = _query_forklift($forklift_id);
		if ($fl['now_printer'] != $printer_id) { //当前叉车有其他任务
			__log('forklift busy: '.$fl['now_printer'].', '.$printer_id);
			continue;
		}
		$macro = $task['content'];
		if ($action == QUEUE_ACTION::PRINTER_CALL_FORKLIFT) {
			$host = $fl['host'];
			$port = $fl['port'];
			$res = _remote_run_macro($printer_id, M_TYPE::QUEUE->value, $forklift_id, $action, $host, $port, $macro);
			if (!$res) {
				__log('run macro error:'.var_dump($res));
			}
		}elseif ($action == QUEUE_ACTION::FORKLIFT_CALL_PRINTER) {
			$p_r = _query_printer($printer_id);
			if (!$p_r) {
				__log('printer not found:'.$printer_id);
			}else {
				$host = $p_r['host'];
				$prot = $p_r['port'];
				$res = _remote_run_macro($forklift_id, M_TYPE::QUEUE->value, $printer_id, $action, $host, $port, $macro);
				if (!$res) {
					__log('run macro error:'.var_dump($res));
				}
			}
		}elseif ($action == QUEUE_ACTION::FORKLIFT_CALL_BOARD) {
			$b_r = _query_board($board_id);
			if (!$p_r) {
				__log('board not found:'.$board_id);
			}else {
				$host = $b_r['host'];
				$prot = $b_r['port'];
				$res = _remote_run_macro($forklift_id, M_TYPE::QUEUE->value, $board_id, $action, $host, $port, $macro);
				if (!$res) {
					__log('run macro error:'.var_dump($res));
				}
			}
		}elseif ($action == QUEUE_ACTION::BOARD_CALL_FORKLIFT) {
			$host = $fl['host'];
			$port = $fl['port'];
			$res = _remote_run_macro($board_id, M_TYPE::QUEUE->value, $forklift_id, $action, $host, $port, $macro);
			if (!$res) {
				__log('run macro error:'.var_dump($res));
			}
		}
		$r = _update_task_status($task['id'], QUEUE_STATUS::FINISHED->value);
		if (!$r) { 
			__log('update task status error:'.var_dump($r));
		}
		_log(LOG_LEVEL::INFO->value, "QUEUE RUN:$task_id $res", 0, M_TYPE::QUEUE->value);
	}
	sleep(1);
}
