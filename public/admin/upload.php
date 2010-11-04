<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

//$path = preg_replace('|^'.URL_UPLOAD.'|', '', $_GET['path']);
$path = $_SERVER['DOCUMENT_ROOT'].preg_replace('|^http://'.$_SERVER['SERVER_NAME'].'|', '', $_GET['path']);
$path = preg_replace('|^'.PATH_UPLOAD.'|', '', $path);

$_FILES['Filedata']['tmp_name'] = str_replace(' ', "\ ", $_FILES['Filedata']['tmp_name']);

/* TODO: corrixir erros de seguridade
 * hay moitas formas de explotar este ficheiro
*/
//move the uploaded file
move_uploaded_file($_FILES['Filedata']['tmp_name'], realpath(PATH_UPLOAD.'/'.$path.'/').'/'.$_FILES['Filedata']['name']);
