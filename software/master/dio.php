<?php
$fd = dio_open('COM5', O_RDONLY);
$op = array();
$op['baud'] = 115200;
//dio_tcsetattr($fd, $op);
$f = fopen('log.txt', 'w');
while (($str = dio_read($fd, 10)) != NULL) {
	fwrite($f, $str);
	echo $str;
}
fclose($f);
dio_close($fd);
