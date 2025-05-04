<?php
$res = '';
exec('tail collect.log -n 100', $res);
echo $res;
?>