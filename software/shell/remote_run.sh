#!/bin/bash

name=$1
action=$2
macro=$3


url="http://192.168.7.1/forklift/remote_run.php?printer=${name}&action=${action}&macro=${macro}"
echo $url
curl $url


