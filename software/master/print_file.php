<?php
include('inc.php');
include('auth.php');

$ps = _query_all_printers();
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>选择打印机</title>
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
    <h1>打印机列表</h1>
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
<?php
$fl = array();
foreach ($ps as $p) {
	//$pstatus = _query_printer_info($p['host'], $p['port']);
	$status = _query_printer_objects($p['host'], $p['port'], array('print_stats'=>null));
?>
            <tr>
                <td><?php echo $p['name']; ?></td>
                <td><?php echo $p['material']; ?></td>
                <td><?php echo $p['color']; ?></td>
                <td><?php echo $p['status'].' / '.$status['result']['status']['print_stats']['state']; ?></td>
                <td><input type="checkbox" name="printer" value="<?php echo $p['id']; ?>"></td>
            </tr>
<?php
}
?>
        </tbody>
    </table>
    <button type="submit">确定</button>
    <button type="cancle">取消</button>
</body>

</html>