<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

//error_reporting(E_ALL);

// php.ini settings for files upload 
set_time_limit(0);
ini_set('upload_max_filesize',  20 * 1024 * 1024 );
ini_set('post_max_size',        20 * 1024 * 1024 );
ini_set('file_uploads',         'On'  );


require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

require_once('attachments_events.php');

require_once('core/content_manager.class.php');
require_once('core/content_category_manager.class.php');
require_once('core/content.class.php');
require_once('core/attachment.class.php');
require_once('core/string_utils.class.php');
require_once('core/attach_content.class.php');
require_once('libs/Pager/Pager.php');

// Check ACL
require_once('./core/privileges_check.class.php');
if( !Acl::_('FILE_ADMIN')){
    Acl::deny();
}

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Adjuntar archivos a noticias');

if (!isset($_REQUEST['category'])) {
    $_REQUEST['category'] = 10;
}

if( !isset($_POST['op']) ) {
    $op = 'view';
}    

 
if( isset($_POST['op']) && ($_POST['op'] == "Adjuntar") ) {
    
    $category = $_REQUEST['category'];    
    $cc = new ContentCategoryManager();
    $cat  = $cc->get_name( $_REQUEST['category'] );
    $pref = substr($cat, 0, 2); 
    $pref = strtoupper($pref);
    
    $dateStamp = date('Ymd');
    
    $ruta = "../media/files/".$cat."/";

    $nombre_archivo = $HTTP_POST_FILES['path']['name'];
    $tipo_archivo   = $HTTP_POST_FILES['path']['type'];
    $tamano_archivo = $HTTP_POST_FILES['path']['size'];
    
    //$nombre_archivo = $pref .$dateStamp .$nombre_archivo;
    //$nombre_archivo = String_Utils::normalize_name( $nombre_archivo ); NON!!!
    $nombre_archivo = strtolower($nombre_archivo);
    $nombre_archivo = preg_replace('/[^a-z0-9_\-\.]/i', '-', $nombre_archivo);
    $data['title'] = $_POST['title'];
    $data['path'] = $nombre_archivo;
    $data['category'] = $category;
    $data['available'] = 1;
    $data['description'] = $_POST['title'];
    
    $stringutils = new String_Utils();
    $data['metadata'] = $stringutils->get_tags($_POST['title']);
    $data['fk_publisher'] = $_SESSION['userid'];
    
    // Create folder if it doesn't exist

    $dir_date =date("/Y/m/d/");
    $ruta = MEDIA_PATH.MEDIA_FILE_DIR.$dir_date ;

    if(!is_dir($ruta)) {
        FilesManager::createDirectory($ruta);
    }
    $datos = pathinfo($nameFile);     //sacamos infor del archivo
    // Move uploaded file
    $uploadStatus = @move_uploaded_file($_FILES['path']['tmp_name'], $ruta.$nombre_archivo);
    
    if($uploadStatus !== false) {
        $attachment = new Attachment();
        
        if( $attachment->create($data) ) {
            //recuperar id.
            $elid = $GLOBALS['application']->conn->Insert_ID();
            if($_REQUEST['desde'] == 'fich') {
                $jscode = '<script type="text/javascript"> parent.location.href= \'ficheros.php?action=list&category='.$category.'\'; </script>';                
            } else {
                $jscode = "<script>
                             var nuevo =  \" <div id='capa".$elid."' style='display: inline;' ><table border='0' cellpadding='4' cellspacing='0' class='fuente_cuerpo' width='100%'> <tr bgcolor='#ffffff'> <td width='50%'>   <input type='text' id='titles[".$elid."]' name='titles[".$elid."]' class='required' size='70' value='".$_POST['title']."' onChange=cambiarlistas(".$elid.",'titles[".$elid."]'); /> </td><td>".$nombre_archivo."</td>  <td align='center' width='80'> <input name='attach_selectos[]' class='pru' value='".$_POST['title']."' id='por".$elid."' type='checkbox' checked='checked' onClick=javascript:probarAttach('por".$elid."','thelist2');></td><td align='center' width='80'><input type='checkbox' id='int".$elid."' value='".$_POST['title']."' name='att_interior[]' checked='checked' onClick=javascript:probarAttach('int".$elid."','thelist2int');></td><td align='center' width='80'> <a  href='#'  onclick=javascript:ocultar('".$elid."');  title='Desvincular'> <img src='themes/default/images/trash.png' border='0' /> </a> </td></tr></table> </div> \";                           
                             parent.document.getElementById( 'adjunto' ).innerHTML = parent.document.getElementById( 'adjunto' ).innerHTML  + nuevo ;
                           parent.document.getElementById('adjunt').style.display = \"none\";
                           meterLista('por".$elid."');
                           meterListaint('int".$elid."');
                            </script>";
            }
            
            $tpl->assign('jscode', $jscode);
        }
        
    } else {
        $tpl->assign('mensaje', '<h3>Ocurrió algún error al subir el fichero y no pudo guardarse. <br />Póngase en contacto con los administradores</h3>');
    }
}


$tpl->display('adjuntos.tpl');