#!/bin/bash

host=$1
port=$2
macro=$3

url="http://${host}:${port}/printer/gcode/script?script=${macro}"
curl $url
