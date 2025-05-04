<?php
session_start();
print_r($_SESSION);
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php?url=".urlencode($_SERVER['REQUEST_URI']));
    exit();
}
$user_id = $_SEESION['user_id'];
$user = _query_user($user_id);
if (!$user || $user['status'] != USER_STATUS::NORMAL->value) {
    header("Location: login.php?user=error&url=".urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
