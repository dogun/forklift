#!/bin/bash

time=`date '+%Y-%m-%d.%H:%M:%S'`

action=$1
pname=$2
gcode=$3

cat "/home/dogun/shell/notify/${action}.eml" | sed "s/{time}/$time/" | sed "s/{print_name}/ $pname /" | sed "s/{gcode_name}/ $gcode /" | ssmtp 2246761@qq.com
cat "/home/dogun/shell/notify/${action}.eml" | sed "s/{time}/$time/" | sed "s/{print_name}/ $pname /" | sed "s/{gcode_name}/ $gcode /" | ssmtp 1462000287@qq.com
