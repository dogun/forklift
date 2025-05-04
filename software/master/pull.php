<?php
$dir = __DIR__;
$cmd = "cd $dir; git pull";
echo $cmd;
exec($cmd, $res);
print_r($res);
?>
