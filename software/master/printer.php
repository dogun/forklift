<?php
include('inc.php');
$ps = _query_all_printers();
$fs = _query_all_forklifts();
$bs = _query_all_boards();
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
            background-color: #00AA00;
            color: #FF22FF;
        }
		
        .status-RUNNING {
            background-color: #00AA00;
            color: #FF22FF;
        }

        .status-complete {
            background-color: #00AAAA;
            color: #AA0000;
        }

        .status-paused {
            background-color: #0000AA;
            color: #AAAA22;
        }

        .status-standby {
            background-color: #AAAA00;
            color: #2222AA;
        }
		
        .status-ready {
            background-color: #AAAA00;
            color: #2222AA;
        }

        .status-error {
            background-color: #AA0000;
            color: #22AAAA;
        }
		
        .status-shutdown {
            background-color: #AA0000;
            color: #22AAAA;
        }
		
        .status-cancelled {
            background-color: #AAAAAA;
            color: #222222;
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
        <tr>
            <th>名称</th>
            <th>地址</th>
			<th>喉管类型</th>
			<th>材料类型</th>
			<th>板车</th>
            <th>年龄</th>
			<th>机态</th>
			<th>工态</th>
            <th>当前打印文件</th>
            <th>操作</th>
        </tr>
<?php
$fl = array();
foreach ($ps as $p) {
	$f_id = $p['forklift_id'];
	if (!@$fl[$f_id]) {
		$fl[$f_id] = _query_forklift($f_id);
	}
	$pstatus = _query_printer_info($p['host'], $p['port']);
	$status = _query_printer_objects($p['host'], $p['port'], array('print_stats'=>null));
?>
        <tr>
            <td class="printer-name"><?php echo $p['name']; ?> (<?php echo $p['id'];?>)</td>
            <td><a href="http://<?php echo $p['host'];?>" target="_blank"><?php echo $p['host'].':'.$p['port']; ?></a></td>
			<td><?php echo $p['throat_type']; ?></td>
			<td><?php echo $p['material_type']; ?></td>
			<td><?php echo $fl[$f_id]['name']; ?></td>
            <td><?php $a = calculateAge($p['created']); echo $a['years'].'岁'.$a['months'].'个月'; ?></td>
			<td class="printer-status status-<?php echo $pstatus['result']['state']; ?>">
				<?php echo $pstatus['result']['state']; ?>
			</td>
			<td class="printer-status status-<?php echo $status['result']['status']['print_stats']['state'];?>">
				<?php echo $status['result']['status']['print_stats']['state']; ?>
			</td>
            <td><?php echo $status['result']['status']['print_stats']['filename']; ?> 
				( <?php echo intval($status['result']['status']['print_stats']['print_duration']); ?>秒 / <?php echo intval($status['result']['status']['print_stats']['total_duration']); ?>秒 )
			</td>
            <td>
                <button disabled>暂停</button>
                <button disabled>取消</button>
            </td>
        </tr>
<?php 
}
?>
    </table>

    <h1>板车监控面板</h1>
    <!-- 板车列表表格 -->
    <table id="printerTable">
        <tr>
            <th>名称</th>
            <th>地址</th>
			<th>板仓</th>
            <th>年龄</th>
			<th>机态</th>
			<th>工态</th>
            <th>当前打印机</th>
            <th>操作</th>
        </tr>
<?php
$pl = array();
foreach ($fs as $p) {
	$p_id = $p['now_printer'];
	if (!@$pl[$p_id]) {
		$pl[$p_id] = _query_printer($p_id);
	}
	$pstatus = _query_printer_info($p['host'], $p['port']);
	$board = _query_board($p['board_id']);
?>
        <tr>
            <td class="printer-name"><?php echo $p['name']; ?> (<?php echo $p['id'];?>)</td>
            <td><a href="http://<?php echo $p['host'];?>" target="_blank"><?php echo $p['host'].':'.$p['port']; ?></a></td>
			<td><?php echo $board['name']; ?></td>
            <td><?php $a = calculateAge($p['created']); echo $a['years'].'岁'.$a['months'].'个月'; ?></td>
			<td class="printer-status status-<?php echo $pstatus['result']['state']; ?>">
				<?php echo $pstatus['result']['state']; ?>
			</td>
			<td class="printer-status status-<?php echo $p['status']; ?>">
				<?php echo $p['status']; ?>
			</td>
            <td><?php echo $pl[$p_id]['name']; ?></td>
            <td>
                <button disabled>暂停</button>
                <button disabled>取消</button>
            </td>
        </tr>
<?php 
}
?>
    </table>
	
	    <h1>板仓监控面板</h1>
	    <!-- 板仓列表表格 -->
	    <table id="printerTable">
	        <tr>
	            <th>名称</th>
	            <th>地址</th>
				<th>板车</th>
	            <th>年龄</th>
				<th>机态</th>
	            <th>操作</th>
	        </tr>
	<?php
	$pl = array();
	foreach ($bs as $p) {
		$pstatus = _query_printer_info($p['host'], $p['port']);
		$fl = _query_forklift($p['forklift_id']);
	?>
	        <tr>
	            <td class="printer-name"><?php echo $p['name']; ?> (<?php echo $p['id'];?>)</td>
	            <td><a href="http://<?php echo $p['host'];?>" target="_blank"><?php echo $p['host'].':'.$p['port']; ?></a></td>
				<td><?php echo $fl['name']; ?></td>
	            <td><?php $a = calculateAge($p['created']); echo $a['years'].'岁'.$a['months'].'个月'; ?></td>
				<td class="printer-status status-<?php echo $pstatus['result']['state']; ?>">
					<?php echo $pstatus['result']['state']; ?>
				</td>
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
