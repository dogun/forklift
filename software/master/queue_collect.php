<?php
include('inc.php');

while (true) {
	//_log(LOG_LEVEL::INFO->value, 'QUEUE RUN', 0, M_TYPE::QUEUE->value);
	__log('QUEUE RUN');
	$tasks = _query_ready_task_collect();
	__log('TASK:'.count($tasks));
	foreach ($tasks as $fl_id => $task) {
		$task_id = $task['id'];
		$action = $task['action'];
		$fl = _query_forklift($task['forklift_id']);
		if (!$fl) {
			__log('FORKLIFT NULL:'.$task['forklift_id']);
			continue;
		}
		$fl_status = $fl['status'];
		__log('PROCESS TASK:'.$task_id);
		if ($fl_status == M_STATUS::READY->value) { //当前空闲，调用处理
			//1. 设置叉车的收料打印机ID
			//2. 调用叉车，告诉它要开始收料
			//3. 设置任务状态为完成
			__log('FORKLIFT READY, RUN...');
			$r = _update_forklift_now_printer($task['forklift_id'], $task['printer_id']);
			if (!$r) { //TODO 失败
			}
			$host = $fl['host'];
			$port = $fl['port'];
			$macro = 'B1'; //TODO 这里要写死吗？
			$res = _remote_run_macro(0, M_TYPE::QUEUE->value, $task['printer_id'], $action, $host, $port, $macro);
			if (!$res) { //TODO 失败
			}
			$r = _update_task_status($task['id'], QUEUE_STATUS::FINISHED->value);
			if (!$r) { //TODO 失败
			}
			_log(LOG_LEVEL::INFO->value, "COLLECT RUN FINISHED:$task_id $res", 0, M_TYPE::QUEUE->value);
		} elseif ($fl_status == M_STATUS::RUNNING->value || $fl_status == M_STATUS::MAINTAIN->value) { //正在运行或维护中
			//下次再执行
			__log('FORKLIFT NOT READY, WAITTING...');
		} elseif ($fl_status == M_STATUS::ERROR->value) { //出错了
			//TODO 报警
			__log('FORKLIFT ERROR');
		}
	}
	sleep(1);
}
