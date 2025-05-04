<?php
$res = '';
exec('tail collect.log -n 100', $res);
print_r($res);
?>