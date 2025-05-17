<?php
include('inc.php');
include('auth.php');

$id = @$_REQUEST['id'];

$action = @$_GET['action'];
if ($action == 'delete') {
	$q_id = $_GET['q_id'];
	_update_print_files_queue_status($q_id, PRINT_FILES_STATUS::DELETED->value);
}

$fs = _query_all_print_files();
$ps = _query_all_printers();
$queues = _query_all_print_files_queues();
$qps = array();
foreach ($queues as $q) {
	if ($q['status'] == PRINT_FILES_STATUS::INIT->value)
		$qps[$q['printer_id']] = 1;
}
$ps_i = array();
foreach ($ps as $p) {
	$ps_i[$p['id']] = $p;
}
$fs_i = array();
foreach($fs as $f) {
	$fs_i[$f['id']] = $f;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>打印队列</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 10px 20px;
            margin-right: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <h1>打印队列</h1>
    <table>
        <thead>
            <tr>
				<th>文件</th>
                <th>打印机</th>
                <th>分配时间</th>
                <th>状态</th>
				<th>操作</th>
            </tr>
        </thead>
        <tbody>
<?php
foreach ($queues as $p) {
?>
            <tr>
				<td><?php echo $fs_i[$p['file_id']]['name']; ?></td>
                <td><?php echo $ps_i[$p['printer_id']]['name']; ?></td>
                <td><?php echo $p['created']; ?></td>
                <td><?php echo $p['status']; ?></td>
				<td><?php if ($p['status'] != PRINT_FILES_STATUS::DELETED->value) {?><a href="print_file.php?id=<?php echo $id; ?>&q_id=<?php echo $p['id']; ?>&action=delete">删除</a><?php } ?></td>
            </tr>
<?php
}
?>
        </tbody>
    </table>
</body>

</html>
