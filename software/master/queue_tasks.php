<?php
include('inc.php');
include('auth.php');

$action = @$_GET['action'];
if ($action == 'cancle') {
	$id = $_GET['id'];
	_update_task_status($id, QUEUE_STATUS::CANCLED->value);
}

$at = _query_all_tasks();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>执行队列</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
		
        .printer-status {
            padding: 2px 5px;
            border-radius: 3px;
        }
		
        .status-RUNNING {
            background-color: #00AA00;
            color: #FF22FF;
        }

        .status-FINISHED {
            background-color: #00AAAA;
            color: #AA0000;
        }

        .status-READY {
            background-color: #AAAA00;
            color: #2222AA;
        }

        .status-ERROR {
            background-color: #AA0000;
            color: #22AAAA;
        }
		
        .status-CANCLED {
            background-color: #AAAAAA;
            color: #222222;
        }
    </style>
</head>

<body>
	<p>
		<a href="queue_log.php" target="_blank">队列日志</a>
		<a href="log.php" target="_blank">调用日志</a>
	</p>
    <h1>执行队列</h1>
    <table id="infoTable">
        <thead>
            <tr>
				<th>序号</th>
                <th>打印机</th>
                <th>板车</th>
                <th>板仓</th>
                <th>动作</th>
                <th>动作详情</th>
                <th>创建时间</th>
                <th>修改时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
<?php
foreach ($at as $t) {
	$p = _query_printer($t['printer_id']);
	$f = _query_forklift($t['forklift_id']);
	$b = _query_board($t['board_id']);
?>
            <tr>
				<td><?php echo $t['id']; ?></td>
                <td><?php echo $p['name']; ?></td>
                <td><?php echo $f['name']; ?></td>
                <td><?php echo $b['name']; ?></td>
                <td><?php echo $t['action']; ?></td>
                <td><?php echo $t['content']; ?></td>
                <td><?php echo $t['created']; ?></td>
                <td><?php echo $t['modified']; ?></td>
                <td class="printer-status status-<?php echo $t['status']; ?>"><?php echo $t['status']; ?></td>
                <td>
					<?php if ($t['status'] == QUEUE_STATUS::READY->value) { ?>
					<a href="queue_tasks.php?action=cancle&id=<?php echo $t['id'];?>">取消</a>
					<?php } else { ?>
					&nbsp;
					<?php } ?>
				</td>
<?php } ?>
            </tr>
        </tbody>
    </table>
</body>

</html>