#!/usr/bin/php
<?php
$basePath = dirname(__FILE__);
require_once $basePath . '/../www/configs/config.inc.php';

$opts = $argv;
array_shift($opts);

$cmd  = $basePath . '/bin/liquibase --driver=com.mysql.jdbc.Driver --changeLogFile=changelog/db.changelog-master.xml --url="jdbc:mysql://' . BD_HOST . '/' .
       BD_INST . '" --username=' . BD_USER . ' --password=' . BD_PASS . ' ' . implode(' ', $opts);

system($cmd);