<?php
include('inc.php');
$username = @$_POST['username'];
$password = @$_POST['password'];
$url = @$_POST['url'];
$message = '';
if ($username && $password) {
	$user = _query_user_by_name($username);
	$ps = md5($seed.$password.$seed);
	echo $seed.$password.$seed;
	echo ' ';
	echo $ps;
	echo ' ';
	echo $user['password'];
	if ($user && $user['password'] == $ps) {
		//login success
		if ($user['status'] == USER_STATUS::NORMAL->value) {
			session_start();
			$_SESSION['user_id'] = $user['id'];
			$_SESSION['user_name'] = $user['name'];
			if (!$url) $url = 'printer.php';
			header('location: '.$url);
		} else {
			$message = 'USER ERROR';
		}
	} else {
		$message = 'PASSWORD ERROR';
	}
} else {
	$url = @$_GET['url'];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录页面</title>
</head>

<body>
    <h2>用户登录</h2>
	<p><?php echo $message;?></p>
    <form action="login.php" method="post">
        <label for="username">用户名：</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">密码：</label>
        <input type="password" id="password" name="password" required><br><br>
		<input type="hidden" name="url" value="<?php echo htmlspecialchars($url); ?>" />
        <input type="submit" value="登录">
    </form>
</body>

</html>
