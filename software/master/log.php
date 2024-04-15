<?php
include('inc.php');
$logs = _query_log();
?>
<html>
<head>
<title>LOG</title>
</head>
<body>
<?php foreach ($logs as $log) { ?>
<?php echo $log['level'];?>&nbsp;
<i><?php echo $log['time'];?></i>&nbsp;
<?php echo $log['log'];?>
<br />
<?php } ?>
</body>
</html>