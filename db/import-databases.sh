#!/bin/bash
dir=`dirname $0`

mysql -h mysql -uroot -proot -e "create database \`onm-instances\`;"
mysql -h mysql -uroot -proot -e "create database \`c-default\`;"
mysql -h mysql -uroot -proot onm-instances < $dir/onm-instances.sql
mysql -h mysql -uroot -proot c-default < $dir/instance-default.sql
