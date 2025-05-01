<?php
include('inc.php');
$ps = _query_all_printer();
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D 打印机列表</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        .printer-name {
            font-weight: 600;
        }

        .printer-status {
            padding: 2px 5px;
            border-radius: 3px;
        }

        .status-printing {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .status-paused {
            background-color: #fcf8e3;
            color: #8a6d3b;
        }

        .status-idle {
            background-color: #d9edf7;
            color: #31708f;
        }

        .status-error {
            background-color: #f2dede;
            color: #a94442;
        }

        .pagination {
            text-align: center;
        }

        .pagination button {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            margin: 0 5px;
            cursor: pointer;
            background-color: #f5f5f5;
        }

        .pagination button.active {
            background-color: #2c3e50;
            color: white;
        }
    </style>
</head>

<body>
    <h1>打印机监控面板</h1>
    <!-- 打印机列表表格 -->
    <table id="printerTable">
        <!-- 第一页打印机数据 -->
        <tr>
            <th>名称</th>
            <th>状态</th>
            <th>地址</th>
			<th>取板车</th>
            <th>创建时间</th>
            <th>修改时间</th>
            <th>操作</th>
        </tr>
<?php
$fl = array();
foreach ($ps as $p) {
	$f_id = $p['forklift_id'];
	if (!@$fl[$f_id]) {
		$fl[$f_id] = _query_forklift($f_id);
	}
?>
        <tr>
            <td class="printer-name"><?php echo $p['name']; ?></td>
            <td class="printer-status status-printing"><?php echo $p['status']; ?></td>
            <td><?php echo $p['host'].':'.$p['port']; ?></td>
			<td><?php echo $fl[$f_id]['name']; ?></td>
            <td><?php echo $p['created']; ?></td>
            <td><?php echo $p['modified']; ?></</td>
            <td>
                <button disabled>暂停</button>
                <button disabled>取消</button>
            </td>
        </tr>
<?php 
}
?>
    </table>
</body>

</html>
