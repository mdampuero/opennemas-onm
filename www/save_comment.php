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
 
require './config.inc.php';
require_once './session_bootstrap.php';

require_once './core/application.class.php';

Application::import_libs('*');
$app = Application::load();

require_once './core/content_manager.class.php';
require_once './core/content.class.php';
require_once './core/comment.class.php';


/**
 * Helper function to save comment into Comment
 *
 * @param array $data
 * @return string Message
 */
function saveComment($data)
{

    $comment = new Comment();

    
    // Check it's clone article {{{
    if(Article::isClone($_POST['id'])) {
        $_POST['id'] = Article::getOriginalPk($_POST['id']);
    }
    // }}}
    
    // Prevent XSS attack
    $data = array_map('strip_tags', $data);
    
    if($comment->hasBadWorsComment($data)) {
        return "Su comentario fue rechazado automáticamente.\n Evite el uso de palabras malsonantes.";
    }
    
    $ip = Application::getRealIP();    
    if($comment->create($_POST['id'],  $data, $ip)) {
        return "Su comentario ha sido guardado y está pendiente de publicación.";
    }
    
    return "Su comentario no ha sido guardado.\nAsegúrese de cumplimentar correctamente todos los campos.";    
}

if(isset($_POST['textareacomentario']) && !empty($_POST['textareacomentario'])) {
    
    if( isset($_POST['security_code']) && empty($_POST['security_code']) ) {
        /*  Anonymous comment ************************* */
        $data = array();
        $data['body']     = $_POST['textareacomentario'];
        $data['author']   = $_POST['nombre'];
        $data['title']    = $_POST['title'];
        $data['category'] = $_POST['category'];
        $data['email']    = $_POST['email'];        
        
        echo saveComment($data);
        
    } else {
        
        /* Check if user is facebook logged **************** */        
        require_once dirname(__FILE__) . '/fb/facebook.php';
        // require_once dirname(__FILE__) . '/fb/config.php'; // deprecated, see section [Facebook API KEY] in config.inc.php
        $fb = new Facebook(FB_APP_APIKEY, FB_APP_SECRET);
        $fb_user = $fb->get_loggedin_user();
        
        if($fb_user) {
            $user_details = $fb->api_client->users_getInfo($fb_user, array('name', 'proxied_email'));  
            
            $data = array();
            $data['body']     = $_POST['textareacomentario'];
            $data['author']   = $user_details[0]['name'];
            $data['title']    = $_POST['title'];
            $data['category'] = $_POST['category'];
            $data['email']    = $user_details[0]['proxied_email'];        
            
            echo saveComment($data);
            
        } else {
            echo("Su comentario no ha sido guardado.\nAsegúrese de cumplimentar correctamente todos los campos.");
        }                
	}
    
} else {
    echo("Su comentario no ha sido guardado.\nAsegúrese de cumplimentar correctamente todos los campos.");
}

	
	
	