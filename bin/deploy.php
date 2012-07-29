<?php
echo "********************************\n";
echo "*                              *\n";
echo "* OpenNemas deployment script  *\n";
echo "*                              *\n";
echo "********************************\n\n";

$basePath = realpath(__DIR__.'../');
$phpBinPath = exec('which php');

echo " - Updating the repository\n";
$output = exec('git pull');
echo $output."\n\n";

echo " - Updating the vendor libraries\n";
$output = exec($phpBinPath.' bin/composer.phar install');
echo $output."\n\n";