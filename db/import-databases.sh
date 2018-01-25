#!/bin/bash
dir=`dirname $0`

mysql -h mysql -uroot -proot -e "create database \`onm-instances\`;"
mysql -h mysql -uroot -proot -e "create database \`c-default\`;"
mysql -h mysql -uroot -proot onm-instances < $dir/onm-instances.sql
mysql -h mysql -uroot -proot c-default < $dir/instance-default.sql
mysql -h mysql -uroot -proot c-default < $dir/instance-default.sql
mysql -h mysql -uroot -proot c-default -e "REPLACE INTO settings (name, value) VALUES ('recaptcha', 'a:2:{s:10:\"public_key\";s:40:\"6LdWlgkUAAAAADzgu34FyZ-wBSB0xlCUc7UVFWGw\";s:11:\"private_key\";s:40:\"6LdWlgkUAAAAAOUnzzBwHNpPgTBIaLwfDjr6XaeQ\";}')"
