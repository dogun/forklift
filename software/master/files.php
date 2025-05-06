<?php
include('inc.php');
include('auth.php');

$file = @$_FILES['file'];
if ($file && $file['size'] > 0) {
	$file_name = $file['name'];
	$size = $file['size'];
	$path = $file['tmp_name'];
	$id = _insert_task_file($file_name, $size, $user_id, 'ABS', 'BLACK');
	$fname = $FILE_PATH.$id;
	move_uploaded_file($path, $fname);
	echo $fname;
}

$fs = _query_all_print_files();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>任务管理</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
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
    <h2>文件上传</h2>
    <form action="task.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" value="上传">
    </form>

    <h2>文件列表</h2>
    <table id="fileList">
        <thead>
            <tr>
                <th>文件名</th>
                <th>大小（字节）</th>
                <th>材料</th>
				<th>颜色</th>
                <th>分配打印机</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
<?php
foreach ($fs as $f) {
?>
            <tr>
                <td><?php echo htmlspecialchars($f['name']); ?>(<?php echo $f['id'];?>)</td>
                <td><?php echo $f['size'];?></td>
                <td><?php echo $f['material'];?></td>
				<td><?php echo $f['color'];?></td>
                <td>分配打印机</td>
                <td>操作</td>
            </tr>
<?php
}
?>
        </tbody>
    </table>
</body>
</html>