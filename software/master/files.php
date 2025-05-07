<?php
include('inc.php');
include('auth.php');

$file = @$_FILES['file'];
if ($file && $file['size'] > 0) {
	$file_name = $file['name'];
	$size = $file['size'];
	$path = $file['tmp_name'];
	$material = MATERIAL::from($_POST['material'])->value;
	$color = COLOR::from($_POST['color'])->value;
	$id = _insert_print_file($file_name, $size, $user_id, $material, $color);
	$fname = $FILE_PATH.$id;
	move_uploaded_file($path, $fname);
}

$fs = _query_all_print_files();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文件管理</title>
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
    <form action="files.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <!-- 材料下拉选框 -->
        <label for="material">选择材料:</label>
        <select name="material" id="material">
		<?php foreach (MATERIAL::cases() as $m) { ?>
            <option value="<?php echo $m->value; ?>"><?php echo $m->value; ?></option>
        <?php } ?>
        </select>

        <!-- 颜色下拉选框 -->
        <label for="color">选择颜色:</label>
        <select name="color" id="color">
		<?php foreach (COLOR::cases() as $c) { ?>
            <option value="<?php echo $c->value; ?>"><?php echo $c->value; ?></option>
        <?php } ?>
        </select>
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