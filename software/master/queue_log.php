<?php
include('inc.php');
include('auth.php');
?>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>队列日志</title>
</head>
<body>
<?php
exec('tail collect.log -n 100', $res);
$res = array_reverse($res);
foreach ($res as $l) {
	echo $l.'<br />';
}
?>
</body>
</html>
