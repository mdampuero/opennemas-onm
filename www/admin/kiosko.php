<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

// Register events
require_once('albums_events.php');

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Portadas del periódico');

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');
require_once('core/user.class.php');

require_once('core/kiosko.class.php');

require_once('core/privileges_check.class.php');

if( !Privileges_check::CheckPrivileges('MUL_ADMIN'))
{
    Privileges_check::AccessDeniedAction();
} 

$ccm = new ContentCategoryManager();
if (!isset($_REQUEST['category'])) {
    $_REQUEST['category'] = $ccm->get_id('kiosko-xornal');
}

$tpl->assign('category', $_REQUEST['category']);

$allcategorys = $ccm->find('internal_category=4  AND fk_content_category=0', 'ORDER BY posmenu');
$tpl->assign('allcategorys', $allcategorys);

$tpl->assign('formAttrs', 'enctype="multipart/form-data"');

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':  //Buscar publicidad entre los content
			$cm = new ContentManager();
			// ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);                        
			list($portadas, $pager)= $cm->find_pages('Kiosko', 'fk_content_type=14 ', 'ORDER BY  date DESC ',$_REQUEST['page'],10, $_REQUEST['category']);

                        $aut=new User();
                        foreach ($portadas as $portada){
                            $portada->publisher=$aut->get_user_name($portada->fk_publisher);
                            $portada->editor=$aut->get_user_name($portada->fk_user_last_editor);
                        }

                        $tpl->assign('portadas', $portadas);
			$tpl->assign('paginacion', $pager);

                        $tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);

		break;

		case 'new':
		break;

		case 'read': //habrá que tener en cuenta el tipo
			$cm = new ContentManager();
			$kiosko = new Kiosko( $_REQUEST['id'] );
			$tpl->assign('kiosko', $kiosko);
                        $tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);
		break;

		case 'update':
                        $_REQUEST['fk_user_last_editor']=$_SESSION['userid'];

			$portada = new kiosko();
			$portada->update( $_REQUEST );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;

		case 'create':

                        $_POST['fk_publisher']=$_SESSION['userid'];
                        
                        //Se crea el nombre del PDF
                        $date = new DateTime($_POST['date']);
                        $_POST['name'] = $date->format('dmy').'.pdf';

                        //Se crea el path
                        $cc = new ContentCategoryManager();
                        $cat  = $cc->get_name( $_REQUEST['category'] );
                        $_POST['path'] = '/'.$cat.'/'.$date->format('Ym').'/';

                        $ruta = MEDIA_PATH.'/files/kiosko'.$_POST['path'];
                        // Create folder if it doesn't exist
                        if( !file_exists($ruta) ) {
                            mkdir($ruta, 0777, true);
                        }

                        // Move uploaded file
                        $uploadStatus = @move_uploaded_file($_FILES['file']['tmp_name'], $ruta.$_POST['name']);

                        $kiosko = new Kiosko();
                        if($uploadStatus!== false && $kiosko->create( $_POST )) {
                            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
                        } else {
                            if ($uploadStatus===false) {
                                $msg = new Message('Ocurrió algún error al subir la portada y no pudo guardarse. <br />Póngase en contacto con los administradores.', 'error');
                                $msg->push();
                                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
                            }
                            //Error debido a portada ya subida enla fecha indicada
                            $_REQUEST['action'] = 'new';
                            $tpl->assign('kiosko', $kiosko);
                        }
		break;

		case 'delete':
			$portada = new Kiosko($_REQUEST['id']);
                        $portada->remove();
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;
		
		case 'change_status':
			$portada = new Kiosko($_REQUEST['id']);

			//Publicar o no, comprobar num clic
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			//$album->set_status($status,$_SESSION['userid']);

                        $portada->set_available($status, $_SESSION['userid']);

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
		break;

                case 'change_favorite':
			$portada = new Kiosko($_REQUEST['id']);

                        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                        if($portada->available==1){
                                $portada->set_favorite($status,$_SESSION['userid'],$_REQUEST['category']);
                        }else{
                                $msg="No se puede esta despublicado";
                        }
                        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$_REQUEST['category']);
		break;
		
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page'].'&category='.$_REQUEST['category']);
}

$tpl->display('kiosko.tpl');
?>
