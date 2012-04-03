<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require_once('../bootstrap.php');

$file      = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_STRING);
$transform = filter_input(INPUT_GET, 'transform', FILTER_SANITIZE_STRING);

if (preg_match('@(jpg|jpeg|gif|png)@i',$file)) {
    $filePath = MEDIA_PATH.DIRECTORY_SEPARATOR.$file;
    $image = new \Onm\File\Image\Imagick;
    $image->load($filePath);
    if (!empty($transform)) {
        $image->transform($transform);
    }

    $image->output();
}