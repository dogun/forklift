<?php
include('inc.php');
include('auth.php');

$action = @$_GET['action'];
if ($action == 'cancle') {
	$id = $_GET['id'];
	_update_task_status($id, QUEUE_STATUS::CANCLE->value);
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
    </style>
</head>

<body>
    <h1>执行队列</h1>
    <table id="infoTable">
        <thead>
            <tr>
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
                <td><?php echo $p['name']; ?></td>
                <td><?php echo $f['name']; ?></td>
                <td><?php echo $b['name']; ?></td>
                <td><?php echo $t['action']; ?></td>
                <td><?php echo $t['content']; ?></td>
                <td><?php echo $t['created']; ?></td>
                <td><?php echo $t['modified']; ?></td>
                <td><?php echo $t['status']; ?></td>
                <td><a href="queue_tasks.php?action=cancle&id=<?php echo $t['id'];?>">取消</a></td>
<?php } ?>
            </tr>
        </tbody>
    </table>
</body>

</html>