<?php
include('inc.php');
include('auth.php');

$id = @$_REQUEST['id'];
$ps = _query_all_printers();
$file = _query_file($id);
$queues = _query_print_files_queue_by_file_id($id);
$qps = array();
foreach ($queues as $q) {
	if ($q['status'] == PRINT_FILES_STATUS::INIT->value)
		$qps[$q['printer_id']] = 1;
}
$ps_i = array();
foreach ($ps as $p) {
	$ps_i[$p['id']] = $p;
}

$pids = @$_POST['printer'];
if (is_array($pids)) {
	foreach ($pids as $pid) {
		$pid = intval($pid);
		if (@$qps[$pid]) {
			//
		}else {
			_insert_print_files_queue($id, $pid, PRINT_FILES_STATUS::INIT->value);
		}
	}
}
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>分配打印机</title>
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

    <h1>分配历史</h1>
    <table>
        <thead>
            <tr>
                <th>打印机</th>
                <th>分配时间</th>
                <th>状态</th>
            </tr>
        </thead>
        <tbody>
<?php
foreach ($queues as $p) {
?>
            <tr>
                <td><?php echo $ps_i[$p['printer_id']]['name']; ?></td>
                <td><?php echo $p['created']; ?></td>
                <td><?php echo $p['status']; ?></td>
            </tr>
<?php
}
?>
        </tbody>
    </table>

    <h1>打印机列表</h1>
	<form action="print_file.php" method="post">
	<input type="hidden" name="id" value="<?php echo $id;?>" />
    <table>
        <thead>
            <tr>
                <th>名称</th>
                <th>材料</th>
                <th>颜色</th>
                <th>状态</th>
                <th>选择</th>
            </tr>
        </thead>
        <tbody>
            <tr style="color: #DD2222">
                <td><?php echo $file['name']; ?></td>
                <td><?php echo $file['material']; ?></td>
                <td><?php echo $file['color']; ?></td>
                <td><?php echo $file['status']; ?></td>
                <td>&nbsp;</td>
            </tr>
<?php
$fl = array();
foreach ($ps as $p) {
	//$pstatus = _query_printer_info($p['host'], $p['port']);
	$status = _query_printer_objects($p['host'], $p['port'], array('print_stats'=>null));
?>
            <tr>
                <td><?php echo $p['name']; ?></td>
                <td><?php echo $p['material_type']; ?></td>
                <td><?php echo $p['color']; ?></td>
                <td><?php echo $p['status'].' / '.$status['result']['status']['print_stats']['state']; ?></td>
                <td><input type="checkbox" name="printer[]" <?php if (@$qps[$p['id']]) echo 'checked'; ?> value="<?php echo $p['id']; ?>"></td>
            </tr>
<?php
}
?>
        </tbody>
    </table>
    <button type="submit">确定</button>
    <a href="files.php">取消</a>
	</form>
</body>

</html>
