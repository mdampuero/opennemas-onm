<?php

$restartFilePath = realpath(
    __DIR__.DIRECTORY_SEPARATOR.'..'
    .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'restart.txt'
);
if ($restartFilePath !== false) {
    if (file_exists('/usr/sbin/service')) {
        exec('/usr/sbin/service apache2 reload', $output, $exitCode);
        if ($exitCode == 0) {
            echo "[ONM] Apache web server restart successfully \n";
        } else {
            echo "[ONM] Apache web server restart with errors (Exit code: {$exitCode}): \n"
                .implode("\n",$output)."\n";
        }
        
        unlink($restartFilePath);
    } else {
        exec('sudo /etc/init.d/apache2 reload', $output, $exitCode);
        unlink($restartFilePath);
    }
    exit($exitCode);
} else {
    // echo "Doesn't exists";
    exit(0);
}
