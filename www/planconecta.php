<?php
//error_reporting(E_ALL); <-- correxir canto antes
require('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('./core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('./core/content_manager.class.php');
require_once('./core/content.class.php');
require_once('./core/article.class.php');
require_once('./core/advertisement.class.php');
require_once('./core/related_content.class.php');
require_once('./core/attachment.class.php');
require_once('./core/attach_content.class.php');

require_once('./core/opinion.class.php');
require_once('./core/comment.class.php');

require('./core/media.manager.class.php');
require_once('./core/img_galery.class.php');
require('./core/photo.class.php');
require('./core/author.class.php');
require_once('./core/content_category.class.php');
require_once('./core/content_category_manager.class.php');

require_once('./core/method_cache_manager.class.php');

require_once('./core/pc_content_manager.class.php');
require_once('./core/pc_content.class.php');
require_once('./core/pc_content_category.class.php');
require_once('./core/pc_photo.class.php');
require_once('./core/pc_video.class.php');
require_once('./core/pc_letter.class.php');
require_once('./core/pc_opinion.class.php');
require_once('./core/pc_user.class.php');
require_once('./core/pc_poll.class.php');
//require_once('core/pc_comments.class.php');

require_once('./libs/phpmailer/class.phpmailer.php');

$tpl = new Template(TEMPLATE_USER);

$cm = new ContentManager();

/******************** CATEGORIA ********************************************************/
$cc = new ContentCategoryManager();

//$category id para los advertisements
$category = $_GET['category'] = 9;
$tpl->assign('category', $category);

/******************** CATEGORIA ********************************************************/
$ccm = ContentCategoryManager::get_instance();

if ($_GET['category_name'] != "conecta") {
    Application::forward301('/home/');
}

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


// Se borrara cuando se contemple action en la url (comprobar
//print_r($_REQUEST);
if( !isset($_REQUEST['action']) ) {
    $_REQUEST['action']='list';
}

if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        
        case 'list': { //Buscar publicidad entre los content
     // <editor-fold defaultstate="collapsed" desc="conecta_todos_contents">

                /******************
                // DON'T REMOVE (old version plan conecta frontpage)
                $cp = new PC_ContentManager();

                $photoDenuncia = $cp->find_by_category_name('PC_Photo', 'foto-denuncia', 'available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0, 1');
                $photoDia = $cp->find_by_category_name('PC_Photo', 'foto-dia', 'available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0, 1');
                $tpl->assign('photoDenuncia', $photoDenuncia);
                $tpl->assign('photoDia', $photoDia);

                $videoDenuncia = $cp->find_by_category_name('PC_Video', 'video-denuncia', 'available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0, 1');
                $videoDia = $cp->find_by_category_name('PC_Video', 'video-dia', 'available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0, 1');
                $tpl->assign('videoDenuncia', $videoDenuncia);
                $tpl->assign('videoDia', $videoDia);

                $opinions = $cp->find('PC_Opinion', 'available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0, 1');
                $tpl->assign('opinions', $opinions);

                $letters = $cp->find('PC_Letter', 'available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0, 1');
                $tpl->assign('letters', $letters);

                $users = PC_User::get_instance();
                $conecta_users = $users->get_all_authors();
                $tpl->assign('conecta_users', $conecta_users);
            
            ****** */
                require_once('pc_portada_list.php');
         // </editor-fold >
             
        } break;

        case 'signin': {
            if($_POST || $_POST['op']=='signin'){
                $user = new PC_User();
                
                if( ($registered = $user->create( $_POST )) ) {
                    //Application::forward('/conecta/');
                    unset($_SESSION['pc_user']); // En caso de que exista sesión anterior
                } else { //mensaje: Problemas volver a form
                                //Application::forward('/conecta/rexistro/');
                    $message = 'Aparecieron errores en los datos proporcionados. Revíselos y vuelva enviarlos, gracias.';
                    $tpl->assign('message', $message);
                    $tpl->assign('errors', $user->_errors);
                }

                $tpl->assign('registered', $registered);
            }
        } break;
// <editor-fold defaultstate="collapsed" desc="conecta_users login, accept, perfil, cambio o perdida pwd, logout">
        case 'accept': {
            //cuando pulsa desde  el mail.
            //acept=true;
            $user = new PC_User();
            $id = $user->accept( $_GET );
            if($id){
                $user->set_status($id, 1);
                
                // Cambio o valor de action para que amose a tpl de login
                $_REQUEST['action'] = 'login';
                $user->read($id);
                $tpl->assign('login_email', $user->email);
                $tpl->assign('message', 'Gracias por registrarte.<br />Introduce tu contraseña para acceder a Conect@');
                // Non redirixo nin creo sesión, amoso a formulario de login
                /* $_SESSION['pc_user']  = $id;
                $_SESSION['nameuser'] = $_GET['user'];
                Application::forward('/conecta/envio/'); */
            } else {
                Application::forward('/conecta/');
            }
        } break;        

        case 'login': {
            if($_POST['op']=='login') {
                $user = new PC_User();
                $id = $user->login( $_POST );
                
                if($id) {
                    $user->read($id);
                    $_SESSION['pc_user']   = $id;
                    $_SESSION['nameuser']  = $user->nick;
                    $_SESSION['firstname'] = $user->firstname;
                    $_SESSION['lastname']  = $user->lastname;
                    $_SESSION['email']     = $user->email;
                    
                    if(isset($_POST['redirect'])) {
                        $redirect = trim(strip_tags($_POST['redirect']));
                        Application::forward('/conecta/' . $redirect . '/');
                    }
                    
                    //Application::forward('/conecta/');
                    Application::forward('/conecta/perfil/');
                } else{
                    $tpl->assign('message', 'Login incorrecto o pendiente de confirmación.');
                }
            }
        } break;
        
        case 'ajax-login': {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                if(isset($_POST['email']) && isset($_POST['password'])) {
                    $response = array();
                    $_POST['email'] = filter_input(INPUT_POST, 'email',  FILTER_SANITIZE_EMAIL);
                    
                    $user = new PC_User();
                    $id = $user->login( $_POST );
                    
                    // Destroy session
                    $_SESSION = array();
                    
                    if($id) {
                        $user->read($id);
                        $response = array('pc_user'   => $id,
                                          'nameuser'  => $user->nick,
                                          'firstname' => $user->firstname,
                                          'lastname'  => $user->lastname,
                                          'email'     => $user->email);
                        
                        $_SESSION['pc_user']   = $id;
                        $_SESSION['nameuser']  = $user->nick;
                        $_SESSION['nick']      = $user->nick;
                        
                        $_SESSION['name']      = $user->name;
                        $_SESSION['firstname'] = $user->firstname;
                        $_SESSION['lastname']  = $user->lastname;
                        $_SESSION['email']     = $user->email;                                                
                    } else {
                        $response['message'] = 'Email o contraseña incorrectos.';                        
                    }
                    
                    header('X-JSON: ' . json_encode($response));                    
                    $tpl->display('boxAuth/conecta.tpl');
                } /*else {
                    $tpl->assign('message', 'Email o contraseña incorrectos.');
                    $tpl->display('conecta_login_ajax.tpl');
                }*/
            } else {
                $tpl->display('boxAuth/loginConecta.tpl');
            }
            
            exit(0);
        } break;
        
        case 'ajax-logout': {
            /* Close conecta authentication */
            $_SESSION = array();
            
            header('Content-type: text/html');
            $tpl->display('boxAuth/default.tpl');
            flush();
            
            exit(0);
        } break;
        
        case 'perfil': {
            // If not exists session then redirect to login page
            if( !isset($_SESSION['pc_user']) || !is_numeric($_SESSION['pc_user']) ) {
                Application::forward('/conecta/login/');
            }
            
            // Id do usuario
            $id = $_SESSION['pc_user'];
            $user = new PC_User($id);
            
            if( isset($_POST['nombreDA']) ) {                
                $_POST['id'] = $id;
                // FIXME: filtrar posibles ataques - SQL injection, ...
                if( $user->update( $_POST ) ) {
                    $id = $user->id;
                    
                    $tpl->assign('message', 'Sus datos fueron actualizados correctamente.');
                } else {
                    $tpl->assign('message', 'Sus datos NO fueron actualizados.<br /> Por favor, revíselos e inténtelo nuevamente.');
                }
            }
            
            // Otherwise load user values
            $user->read( $id );
            $tpl->assign('user', $user);
        } break;
        
        /* Formulario de cambio de contraseña y acciones que modifican en base de datos */
        case 'cambio': {
            // If not exists session then redirect to login page
            if( !isset($_SESSION['pc_user']) || !is_numeric($_SESSION['pc_user']) ) {
                Application::forward('/conecta/login/');
            }
            
            if( isset($_POST['passDA']) ) {
                // Id do usuario
                $id = $_SESSION['pc_user'];
                $user = new PC_User($id);                 
                
                // Verificar con contraseña actual
                if( $user->password != md5($_POST['passOld']) ) {
                    $tpl->assign('message', 'La contraseña actual no coincide con la disponible en el sistema.<br />La contraseña NO se actualizó.');
                } else {
                    $_POST['id'] = $id;
                    // FIXME: filtrar posibles ataques - SQL injection, ...
                    $user->change_password( $_POST );
                    $id = $user->id;
                    
                    $tpl->assign('message', 'Su contraseña fue actualizada correctamente.');
                }                                
            }            
        } break;
    
        /* Formulario de olvido de contraseña y acciones que modifican en base de datos */
        case 'olvido': {          
            if( isset($_POST['emailDA']) ) {                
                $user = new PC_User();                                
                $user = $user->getUserByEmail( $_POST['emailDA'] );                
                
                // Verificar con contraseña actual
                if( is_null($user) || $user->nick != $_POST['nickDA'] ) {
                    $tpl->assign('message', 'No existe la cuenta en el sistema.<br />Revise su cuenta de correo y nick.');
                    
                } else {
                    $data['id']     = $user->id;
                    $data['passDA'] = $user->createRandomPassword( rand(7, 12) );
                    // FIXME: filtrar posibles ataques - SQL injection, ...
                    $user->change_password( $data );
                    
                    $user->send_new_pass( $user->email, $data['passDA'] );
                    
                    $_REQUEST['action'] = $_POST['action'] = 'login';
                    $tpl->assign('message', 'En breve recibirá su nueva contraseña en su dirección de correo.');
                    $tpl->assign('login_email', $user->email);
                }                                
            }            
        } break;    
        
        /* existsNick action, check if nickDA exists in database via ajax request */
        case 'existsNick': {            
            $exists = (PC_User::exists_nick( $_REQUEST['nickDA'] ))? '1': '0';            
            echo( '{exists: '.$exists.'}' );            
            
            exit(0);
        } break;
    
        
        /* existsEmail action, check if emailDA exists in database via ajax request */
        case 'existsEmail': {
            $exists = (PC_User::exists_email( $_REQUEST['emailDA'] ))? '1': '0';            
            echo( '{exists: '.$exists.'}' );                        
            exit(0);
        } break;    
        
        case 'logout': {
            $_SESSION = array();
            session_destroy(); // Destruir sesión
            
            // Redirixir a /conecta/ (old version)
            // Application::forward('/conecta/');
            Application::forward('/');
        } break;        
         // </editor-fold >

        case 'faq': { 
            //display tpl faq
        } break;
        
        case 'boletin': {
            if(!isset($_SESSION['pc_user']) || !is_numeric($_SESSION['pc_user'])) {
                Application::forward('/conecta/login/boletin/');
            }
            
            $id = $_SESSION['pc_user'];
            $user = new PC_User($id);
            
            if(($_SERVER['REQUEST_METHOD'] == 'POST')) {
                $subscription = (isset($_POST['subscription']))? $_POST['subscription']: 0;                                
                
                $user->updateSubscription($subscription);
                $tpl->assign('message', 'Estado de suscripción actualizado correctamente.');
            }
            
            $tpl->assign('subscription', $user->subscription);
            
            // Display conecta_CZonaBoletin.tpl
        } break;
       
        case 'polls': { //display tpl envio
        // <editor-fold defaultstate="collapsed" desc="conecta_poll">
                $cp = new PC_ContentManager();

                $tpl->assign('ya_vote','¿No has votado?');
                if(!isset($_REQUEST['id'])){                   
                     $polls = $cp->find('PC_Poll', 'available=1', 'ORDER BY favorite DESC, content_status DESC, created DESC LIMIT 0,1');
                     $_REQUEST['id']=$polls[0]->id;
                    }
                if(!$_POST['respEncuesta']){$_REQUEST['op'] = 'visualizar';}
                $cookie="polls".$_REQUEST['id'];
                if (isset($_COOKIE[$cookie])){
                    $_REQUEST['op']='votar';
                    $tpl->assign('ya_vote','ya has votado');
                }

                if( !isset($_REQUEST['op']) ) {
                    $_REQUEST['op'] = 'visualizar';
                }

                if( isset($_REQUEST['op']) ) {
                    switch($_REQUEST['op']) {
                        case 'votar':
                            if($_POST['respEncuesta'] && !isset($_COOKIE[$cookie])){
                                $ip = $_SERVER['REMOTE_ADDR'];
                                $poll=new PC_Poll($_REQUEST['id']);
                                $poll->vote($_POST['respEncuesta'],$ip);
                                $tpl->assign('ya_vote','gracias por tu voto'.$_COOKIE[$cookie]);
                            }
                            $tpl->assign('op', 'votar'); //conecta_CZonaEncuesta.tpl
                        break;
                    
                        default:
                            $tpl->assign('op', 'visulizar'); //conecta_CZonaVisionadoMedia.tpl
                        break;
                    }
                }            
    
                $poll = new PC_Poll( $_REQUEST['id']);
                $tpl->assign('poll', $poll); //Encuesta seleccionada

                $items=$poll->get_items($_REQUEST['id']); //respuestas
                $tpl->assign('items', $items);
                $arrayPolls = $cp->find('PC_Poll', 'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status DESC, changed DESC LIMIT 0, 30');

                $tpl->assign('accion', $_REQUEST['action']);
                $params="'pc_".$_REQUEST['action']."','','".$_REQUEST['id']."'";
                $arrayPolls=$cm->paginate_num_js($arrayPolls,5, 1, 'get_paginate_pc',$params);
                $tpl->assign('arrayPolls', $arrayPolls);
                $tpl->assign('pages', $cm->pager);
                //pc_comments polls
                $comment = new PC_Comment();
                $comments = $comment->get_public_comments($_REQUEST['id']);
                $comments = $cm->paginate_num_js($comments, 9, 1, 'get_paginate_comments','NULL');
                $tpl->assign('paginacion', $cm->pager);
                $tpl->assign('comments', $comments);
                // </editor-fold >
        } break;

// <editor-fold defaultstate="collapsed" desc="conecta_send_contents">
        //Hacer comprobaciones de tamaños y campos vacios en js
        case 'send': {
            if(isset($_SESSION['pc_user'])) {
                $pccm = new PC_ContentCategoryManager();
                $allcategorys=$pccm->find_all_types('available=1');
               //  var_dump($allcategorys);
              // exit();

                $tpl->assign('allcategorys',$allcategorys);
                if($_POST['tituloArticulo']) {
                    $_POST['title']    = $_POST['tituloArticulo'];
                    $_POST['fk_user']  = $_SESSION['pc_user'];
                    $_POST['fk_pc_content_category'] = $_POST['temasPrincipales'];
                    $_POST['country']  = $_POST['selectPais'];
                    $_POST['locality'] = $_POST['localidad'];
                    $_POST['date']     = $_POST['selectAnyo']."-".$_POST['selectMes']."-".$_POST['selectDia']." 00:00:00";
                    $_POST['ip']       = $_SERVER['REMOTE_ADDR'];
                    $_POST['metadata'] = $_POST['palabrasClave'];
                    $_POST['content_status'] = 0;

                    $message = 'Envio correcto. Queda pendiente de aprobaci&oacute;n';

                    if($_POST['tipoArchivo']=='1') {
                        // $_FILES["file"]["size"];
                        if(!in_array($_FILES['file']['type'], array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'))) {
                            $message = 'El tipo de fichero enviado no se corresponde con un tipo de imagen válido (gif, jpg/jpeg, png)';
                        } else {
                            $photo = new PC_Photo();
                            $_POST['description'] = $_POST['textoArticulo'];
                            $photo->create( $_POST );
                        }
                    } elseif($_POST['tipoArchivo']=='2') {
                        $_POST['code'] = $_POST['codigoVideo'];
                        $_POST['description'] = $_POST['textoArticulo'];
                        $video = new PC_Video();
                        $video->create( $_POST );

                    } elseif($_POST['tipoArchivo']=='3') {
                        // Opinión
                        $_POST['body'] = $_POST['textoArticulo'];
                        $_POST['fk_pc_content_category'] = '6';
                        $opinion = new PC_Opinion();
                        $opinion->create( $_POST );

                    } elseif($_POST['tipoArchivo']=='4') {
                        // Cartas al director
                        $_POST['body'] = $_POST['textoArticulo'];
                        $_POST['fk_pc_content_category'] = '5';
                        $letter = new PC_Letter();
                        $letter->create( $_POST );

                    }

                    $tpl->assign('message', $message);
                }
            } else {
                Application::forward('/conecta/login/');
            }
        } break;
  // </editor-fold >
  //
  // <editor-fold defaultstate="collapsed" desc="conecta_listados">
        //Listados segun categorias.
        //Mejorar find con paginate y mirar de poner caches

         case 'fotografias': {
           
            if (empty($_REQUEST['name'])){
                 Application::forward('/conecta/');
            }

            if (empty($_REQUEST['id'])){
                $cp = new PC_ContentManager();
                $contentID = $cp->find_by_category_name('PC_Photo', $_REQUEST['name'], 'available=1 and favorite=1', 'ORDER BY changed DESC LIMIT 0, 1');               
                $_REQUEST['id'] = (empty($contentID[0]->pk_pc_photo))? '0': $contentID[0]->pk_pc_photo;
                $arrayContents = $cp->find_by_category_name('PC_Photo',$_REQUEST['name'],'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status ASC, created DESC LIMIT 0,30');
                // Si no hay favorito
                if(empty($_REQUEST['id'])){ $contentID[0]=array_shift($arrayContents); $_REQUEST['id']=$contentID[0]->pk_pc_photo; }
            }else{
                
                $cp = new PC_ContentManager();
                $contentID = $cp->find_by_category_name('PC_Photo', $_REQUEST['name'], 'pk_pc_content='.$_REQUEST['id'].' AND available=1', 'ORDER BY changed DESC LIMIT 0,1');
                $arrayContents = $cp->find_by_category_name('PC_Photo', $_REQUEST['name'], 'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status ASC, created DESC LIMIT 0,30');
            }
            $params="'".$_REQUEST['action']."','".$_REQUEST['name']."','".$_REQUEST['id']."'";
            $arrayContents=$cm->paginate_num_js($arrayContents,5, 1, 'get_paginate_pc',$params);
            $tpl->assign('pages', $cm->pager);

            $tpl->assign('arrayPhotos', $arrayContents);
            $tpl->assign('photoID', $contentID);
            $tpl->assign('accion', $_REQUEST['name']);

            $users = PC_User::get_instance();
            $conecta_users = $users->get_all_authors();
            $tpl->assign('conecta_users', $conecta_users);
            if ($_REQUEST['is_ajax']=='ok'){
                 $html_out=$tpl->fetch('conecta_Fotos_listado.tpl');
                 echo($html_out);
                exit(0);
            }
        } break;
      
        case "videos": {
            if (empty($_REQUEST['name'])){
                 Application::forward('/conecta/');
            }
            if (empty($_REQUEST['id'])) {
                $cp = new PC_ContentManager();
                $videoID = $cp->find_by_category_name('PC_Video',  $_REQUEST['name'], 'available=1 and favorite=1', 'ORDER BY changed DESC LIMIT 0, 1');
                $id = (empty($videoID[0]->pk_pc_video))? '0': $videoID[0]->pk_pc_video; 
                $arrayVideos = $cp->find_by_category_name('PC_Video', $_REQUEST['name'],'available=1 and pk_pc_content <> '.$id.'', 'ORDER BY content_status ASC, created DESC  LIMIT 0,30');
                 // Si no hay favorito
                if(empty($_REQUEST['id'])){ $videoID[0]=array_shift($arrayVideos); $_REQUEST['id']=$arrayVideos[0]->pk_pc_photo; }
            }else{
                $cp = new PC_ContentManager();
                $videoID = $cp->find_by_category_name('PC_Video',  $_REQUEST['name'], 'pk_pc_content='.$_REQUEST['id'].' AND available=1', 'ORDER BY changed DESC LIMIT 0,1');
                $arrayVideos = $cp->find_by_category_name('PC_Video',  $_REQUEST['name'], 'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status ASC, created DESC  LIMIT 0,30');
            }
            $params="'".$_REQUEST['action']."','".$_REQUEST['name']."','".$_REQUEST['id']."'";
            $arrayVideos=$cm->paginate_num_js($arrayVideos,5, 1, 'get_paginate_pc',$params);
            $tpl->assign('pages', $cm->pager);
            $tpl->assign('arrayVideos', $arrayVideos);
            $tpl->assign('videoID', $videoID);
            $tpl->assign('accion', $_REQUEST['name']);

            $users = PC_User::get_instance();
            $conecta_users = $users->get_all_authors();
            $tpl->assign('conecta_users', $conecta_users);
            if ($_REQUEST['is_ajax']=='ok'){
                 $html_out=$tpl->fetch('conecta_Videos_listado.tpl');
                 echo($html_out);
                exit(0);
            }
        } break;
        
        case "cartas": {
            if (empty($_REQUEST['name'])){
                 Application::forward('/conecta/');
            }
            if (empty($_REQUEST['id'])) {
                $cp = new PC_ContentManager();
                $letterID = $cp->find_by_category_name('PC_Letter',  $_REQUEST['name'], 'available=1 and favorite=1', 'ORDER BY changed DESC LIMIT 0, 1');
                $id = (empty($letterID[0]->pk_pc_video))? '0': $letterID[0]->pk_pc_video;
                $arrayletters = $cp->find_by_category_name('PC_Letter', $_REQUEST['name'],'available=1 and pk_pc_content <> '.$id.'', 'ORDER BY content_status ASC, created DESC  LIMIT 0,30');
                 // Si no hay favorito
                if(empty($_REQUEST['id'])){ $letterID[0]=array_shift($arrayletters); $_REQUEST['id']=$arrayletters[0]->pk_pc_photo; }
            }else{
                $cp = new PC_ContentManager();
                $letterID = $cp->find_by_category_name('PC_Letter',  $_REQUEST['name'], 'pk_pc_content='.$_REQUEST['id'].' AND available=1', 'ORDER BY changed DESC LIMIT 0,1');
                $arrayletters = $cp->find_by_category_name('PC_Letter',  $_REQUEST['name'], 'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status ASC, created DESC  LIMIT 0,30');
            }
            $params="'".$_REQUEST['action']."','".$_REQUEST['name']."','".$_REQUEST['id']."'";
            $arrayletters=$cm->paginate_num_js($arrayletters,5, 1, 'get_paginate_pc',$params);
            $tpl->assign('pages', $cm->pager);
            $tpl->assign('arrayletters', $arrayletters);
            $tpl->assign('letterID', $letterID);
            $tpl->assign('accion', $_REQUEST['name']);

            $users = PC_User::get_instance();
            $conecta_users = $users->get_all_authors();
            $tpl->assign('conecta_users', $conecta_users);
            if ($_REQUEST['is_ajax']=='ok'){
                 $tpl->assign('arraytextos', $arrayletters);
                 $html_out=$tpl->fetch('conecta_Textos_listado.tpl');
                 echo($html_out);
                exit(0);
            }
        } break;
        
        case "opiniones": {
           
            if (empty($_REQUEST['name'])){
                 Application::forward('/conecta/');
            }
            if (empty($_REQUEST['id'])) {
                $cp = new PC_ContentManager();
                $opinionID = $cp->find_by_category_name('PC_Opinion',  $_REQUEST['name'], 'available=1 and favorite=1', 'ORDER BY changed DESC LIMIT 0, 1');
                $id = (empty($opinionID[0]->pk_pc_video))? '0': $opinionID[0]->pk_pc_video;
                $arrayopinions = $cp->find_by_category_name('PC_Opinion', $_REQUEST['name'],'available=1 and pk_pc_content <> '.$id.'', 'ORDER BY content_status ASC, created DESC  LIMIT 0,30');
                 // Si no hay favorito
                if(empty($_REQUEST['id'])){ $opinionID[0]=array_shift($arrayopinions); $_REQUEST['id']=$arrayopinions[0]->pk_pc_photo; }
            }else{
                $cp = new PC_ContentManager();
                $opinionID = $cp->find_by_category_name('PC_Opinion',  $_REQUEST['name'], 'pk_pc_content='.$_REQUEST['id'].' AND available=1', 'ORDER BY changed DESC LIMIT 0,1');
                $arrayopinions = $cp->find_by_category_name('PC_Opinion',  $_REQUEST['name'], 'available=1 and pk_pc_content <> '.$_REQUEST['id'].'', 'ORDER BY content_status ASC, created DESC  LIMIT 0,30');
            }
            $params="'".$_REQUEST['action']."','".$_REQUEST['name']."','".$_REQUEST['id']."'";
            $arrayopinions=$cm->paginate_num_js($arrayopinions,5, 1, 'get_paginate_pc',$params);
            $tpl->assign('pages', $cm->pager);
            $tpl->assign('arrayopinions', $arrayopinions);
            $tpl->assign('opinionID', $opinionID);
            $tpl->assign('accion', $_REQUEST['name']);

            $users = PC_User::get_instance();
            $conecta_users = $users->get_all_authors();
            $tpl->assign('conecta_users', $conecta_users);
            if ($_REQUEST['is_ajax']=='ok'){
                 $tpl->assign('arraytextos', $arrayopinions);
                 $html_out=$tpl->fetch('conecta_Textos_listado.tpl');
                 echo($html_out);
                exit(0);
            }
        } break;
     // </editor-fold >

        case 'get_tags': {           
            $tags = $_GET['title']." ".$_GET['tags'];
            $tags = String_Utils::get_tags($tags);
            echo $tags;
            exit(0);

        } break;

        default: {
            Application::forward('/conecta/');
        } break;
    }
}

/* *********************************  CONECTA COLUMN3  ***************************************** */
require_once("index_conecta.php");
$tpl->assign('MEDIA_CONECTA_WEB', MEDIA_CONECTA_WEB);
$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB); // alternative {$smarty.const.MEDIA_IMG_PATH_WEB}
/* ********************************************************************************************* */

/* ********************************   PUBLICIDAD   ********************************************* */
require_once ("planconecta_advertisement.php");
/* ********************************************************************************************* */

// Display
$tpl->display('conecta.tpl');