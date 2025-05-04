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
        <input type="file" name="files[]" multiple>
        <input type="submit" value="上传">
    </form>

    <h2>文件列表</h2>
    <table id="fileList">
        <thead>
            <tr>
                <th>文件名</th>
                <th>大小（字节）</th>
                <th>打印份数</th>
                <th>分配打印机</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <!-- 文件列表将通过服务器端渲染 -->
        </tbody>
    </table>
</body>

</html>