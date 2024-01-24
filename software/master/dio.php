<?php
$fd = dio_open('COM8', O_RDONLY);
$f = fopen('log.txt', 'w');
while (($str = dio_read($fd, 10)) != NULL) {
	fwrite($f, $str);
	echo $str;
}
fclose($f);
dio_close($fd);