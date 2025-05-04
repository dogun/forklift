<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php?url=".urlencode($__FILE__));
    exit();
}
$user_id = $_SEESION['user_id'];
$user = _query_user($user_id);
if (!$user || $user['status'] != USER_STATUS::NORMAL->value) {
    header("Location: login.php?url=".urlencode($__FILE__));
    exit();
}
?>
