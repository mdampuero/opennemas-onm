<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// TODO: convertir este controlador para que sea una clase
require('core/uploader.class.php');

$actions_uploader = array('upload-container', 'upload-form', 'upload-progress',
                          'upload-run', 'upload-finish', 'posted-youtube');
// $action_uploader = (!isset($_REQUEST['action']))? 'upload-container': $_REQUEST['action'];
if(!isset($_REQUEST['action']) || !in_array($_REQUEST['action'], $actions_uploader)) {
    $action_uploader = 'upload-container';
} else {
    $action_uploader = $_REQUEST['action'];
}

// Asignar la acción para que aparezca en la plantilla
$tpl->assign('ACTION_UPLOADER', $action_uploader);

switch($action_uploader) {
    case 'upload-container':
        // Template con el iframe
    break;

    case 'upload-form':
        // Contenido del iframe
        $tpl->assign('uniqid', uniqid());
        $tpl->assign('maxfilesize', '10500500');
        
        $tpl->display('uploader.tpl');
        exit(0);
    break;

    case 'upload-progress':
        // Respuesta del progreso            
        $progress_stats = Uploader::progressStatus( $_REQUEST['APC_UPLOAD_PROGRESS'] );
        // Header X-JSON for ajax response
        header('X-JSON: '.json_encode( $progress_stats ) );
        
        if($progress_stats['apc'] && ($progress_stats['done']!=1)) {
            $percent = @floor($progress_stats['current']*100/$progress_stats['total']);
            echo($percent . ' %');
        } elseif(!$progress_stats['apc']) {
            echo( 'Subiendo el fichero, espere por favor...' );
        }
        exit(0);
    break;

    case 'upload-run':                       
        $uploader = new Uploader('archivo');
        
        // Establecer un entorno de ejecución que acepte los file uploads
        $uploader->sanitizeEnv();
        
        // Poner el path donde se guarda
        $uploader->setPathUpload( $path );
        
        // Si es un formato de video postear en YouTube
        $uploader->registerEvent(onAfterUpload, 'post_video');        
        
        // Descomprimir si es un zip, tar, tgz, ...
        $uploader->registerEvent(onAfterUpload, 'descompress_file', array(true));
        // $uploader->registerEvent(onAfterUpload, 'descompress_file', array(false)); // No borra el fichero comprimido
        
        // Escalar la imagen a unos valores (por defecto: 240x130)
        //$uploader->registerEvent(onAfterUpload, 'scale');
        // $uploader->registerEvent(onAfterUpload, 'scale', array(400, 400));
        
        // Al finalizar el upload hace una redirección al formulario inicial
        $uploader->registerEvent(onAfterUpload, 'reload_form');
                        
        // Comienzo del proceso
        $uploader->upload();        
    break;

    case 'upload-finish':
        $tpl->display('uploader.tpl');
        exit(0);
    break;

    case 'posted-youtube':
        $tpl->display('uploader.tpl');
        exit(0);
    break; 
}