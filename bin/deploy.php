<?php
echo "********************************\n";
echo "*                              *\n";
echo "* OpenNemas deployment script  *\n";
echo "*                              *\n";
echo "********************************\n\n";

$basePath = realpath(__DIR__.'/../');
$phpBinPath = exec('which php');

chdir($basePath);

echo " - Updating onm instance\n";
$output = exec('git pull');
echo $output."\n\n";

echo " - Updating vendor libraries\n";
$output = exec($phpBinPath.' bin/composer.phar install');
echo $output."\n\n";

echo " - Updating public themes\n";
foreach (glob($basePath.'/public/themes/*') as $theme) {
    // echo $theme."\n";
    chdir($theme);
    echo "     * Updating ".basename($theme)." path\n";
    $output = exec('git pull');
    chdir($basePath);
    echo "\n";
}