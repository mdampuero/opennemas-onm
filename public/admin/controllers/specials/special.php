<?php

/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message as m;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

//Check if module is activated in this onm instance
\Onm\Module\ModuleManager::checkActivatedOrForward('SPECIAL_MANAGER');

 // Check if the user can admin specials
Acl::checkOrForward('SPECIAL_ADMIN');


$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);

/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('special');

$category = filter_input(INPUT_GET,'category',FILTER_VALIDATE_INT);

if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT);
}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);

if(empty($category)) {
    $category ='widget';
}

$tpl->assign('category', $category);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);

$tpl->assign('datos_cat', $categoryData);

/******************* GESTION CATEGORIAS  *****************************/

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

$configurations = s::get('special_settings');

$numFavorites = (isset($configurations['total_widget']) && !empty($configurations['total_widget']))? $configurations['total_widget']: 1;
$sizeFile = (isset($configurations['size_file']) && !empty($configurations['size_file']))? $configurations['size_file']: 5000000;
$cm = new ContentManager();

switch($action) {

    case 'list':
        Acl::checkOrForward('SPECIAL_ADMIN');


        if (empty($page)) {
            $limit= "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit= "LIMIT ".($page-1) * ITEMS_PAGE .', '.ITEMS_PAGE;
        }

        if ($category == 'widget') {
            $specials = $cm->find_all('Special', 'in_home =1 AND available =1', 'ORDER BY position, created DESC '.$limit);

            if(!empty($specials)) {
                foreach ($specials as &$special) {
                    $special->category_name = $ccm->get_name($special->category);
                    $special->category_title = $ccm->get_title($special->category_name);
                }
            }
            if (count($specials) != $numFavorites ) {
                m::add( sprintf(_("You must put %d specials in the HOME widget"), $numFavorites));
            }

        } else {

            $specials = $cm->find_by_category('Special', $category, '1=1',
                           'ORDER BY created DESC '.$limit);

        }



        $params = array(
            'page'=>$page, 'items'=>ITEMS_PAGE,
            'total' => count($specials),
            'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$category
        );

        $pagination = \Onm\Pager\SimplePager::getPagerUrl($params);

        $tpl->assign(array(
            'pagination' => $pagination,
            'specials' => $specials
        ));

        $tpl->display('special/list.tpl');

    break;

    case 'read':
        Acl::checkOrForward('SPECIAL_UPDATE');
        $special = new Special($_REQUEST['id']);
        $nots=$special->get_contents($_REQUEST['id']);
        $tpl->assign('special', $special);
        $noticias_left = array();
        $noticias_right = array();
        if(!empty($nots)) {
            foreach($nots as $noticia){
                if(($noticia['position']%2)==0){
                    $noticias_right[]=new Content($noticia['fk_content']);
                }else{
                    $noticias_left[]=new Content($noticia['fk_content']);
                }
            }
            $tpl->assign('noticias_right',$noticias_right);
            $tpl->assign('noticias_left',$noticias_left);
        }


        $articles= $cm->find_by_category('Article', $category, ' 1=1 ', 'ORDER BY created DESC LIMIT 0,100');
        $tpl->assign('articles', $articles);

        $tpl->display('special/new.tpl');

    break;
    case 'new':
        Acl::checkOrForward('SPECIAL_CREATE');

        $articles= $cm->find_by_category('Article', $category, '1=1 ', 'ORDER BY created DESC LIMIT 0,100');

        $tpl->assign('articles', $articles);

        $tpl->display('special/new.tpl');

    break;

    case 'create':
        $special = new Special();
        if($special->create( $_POST )) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
        } else {
            $tpl->assign('errors', $special->errors);
        }
    break;

    case 'update':
        Acl::checkOrForward('SPECIAL_UPDATE');
        $special = new Special($_REQUEST['id']);
        $special->update($_POST);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;



    case 'delete':

        Acl::checkOrForward('SPECIAL_DELETE');

        $special = new Special($_REQUEST['id']);
        $special->delete($_REQUEST['id']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'change_favorite':

        Acl::checkOrForward('SPECIAL_FAVORITE');

        $special = new Special($_REQUEST['id']);
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $special->set_favorite($status);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'change_inHome':

        Acl::checkOrForward('SPECIAL_HOME');
        $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
        $status = filter_input(INPUT_GET,'status',FILTER_VALIDATE_INT,
                                array('options' => array('default'=> 0)));
        $special = new Special($id);
        if ($special->available == 1) {
            $special->set_inhome($status,$_SESSION['userid']);
        } else {
            m::add(_("This special is not published so you can't define it as widget home content.") );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

    break;


    case 'change_status':

        Acl::checkOrForward('SPECIAL_AVAILABLE');

        $special = new Special($_REQUEST['id']);

        //Publicar o no, comprobar num clic
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $special->set_status($status);

        if(isset($_GET['desde']) && $_GET['desde']=='search'){
            Application::forward('search.php?action=search&stringSearch='.$_GET['stringSearch'].'&page='.$_GET['page']);
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;


    case 'batchFrontpage':

        Acl::checkOrForward('ALBUM_AVAILABLE');

        if(isset($_GET['selected_fld']) && count($_GET['selected_fld']) > 0) {
            $fields = $_GET['selected_fld'];

            $status = filter_input ( INPUT_GET, 'status' , FILTER_SANITIZE_NUMBER_INT );
            if (is_array($fields)) {
                foreach ($fields as $i) {
                    $album = new Album($i);
                    $album->set_available($status, $_SESSION['userid']);
                    if ($status == 0) {
                        $album->set_favorite($status);
                    }
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);

    break;


    case 'batchDelete':
        Acl::checkOrForward('SPECIAL_DELETE');

        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld']) > 0) {
            $fields = $_REQUEST['selected_fld'];

            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $special = new Special($i);
                    $special->delete( $i, $_SESSION['userid'] );
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letter_status=' .
                    $letterStatus . '&page=' . $page);
    break;


     case 'save_positions':
        $positions = $_GET['positions'];
        if (isset($positions)  && is_array($positions)
                && count($positions) > 0) {
           $_positions = array();
           $pos = 1;

           foreach($positions as $id) {
                    $_positions[] = array($pos, '1', $id);
                    $pos += 1;
            }

            $album = new Album();
            $msg = $album->set_position($_positions, $_SESSION['userid']);

            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            require_once(SITE_CORE_PATH.'template_cache_manager.class.php');
            $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('home|0');
         }
         if(!empty($msg) && $msg == true) {
             echo _("Positions saved successfully.");
         } else{
             echo _("Unable to save the new positions. Please contact with your system administrator.");
         }
        exit(0);
    break;


    case 'm_no_in_special':

          if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
             $fields = $_REQUEST['selected_fld'];
             if(is_array($fields)) {
                foreach($fields as $content) {
                    $special=new Special($id_special);
                    $special->delete_contents($content);
                }
             }
          }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'no_in_special':
        $special=new Special($id_special);
        $special->delete_contents($content);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'change_article_page':
        $articles= $cm->find_by_category('Article',$category, '1=1 ', 'ORDER BY created DESC LIMIT 0,100');
        $params=$category;
        $articles = $cm->paginate_num_js($articles,16,1,'changeSpecials',$params);
        $tpl->assign('paginacion_articles', $cm->pager);
        $tpl->assign('articles', $articles);
        $tpl->assign('category', $category);
        $listado=$tpl->fetch('listados_contents.tpl');
        echo $listado;
        exit();
    break;

    case 'save_orden_list':
          $orden=$_GET['orden'];

          if(isset($orden)){
               $tok = strtok($orden,",");
               $pos=1;
                while (($tok !== false) AND ($tok !=" ")) {
                    $special = new Special($tok);
                    if(($_GET['category'] == 'home') || ($_GET['category'] == '0')){
                        $special->set_home_position($pos);

                    }else{
                        $special->set_position($pos);
                    }
                    $tok = strtok(",");
                    $pos+=1;
                 }
           }
          exit(0);
    break;

    case 'save_positions':
        $positions = $_GET['positions'];
        if (isset($positions)  && is_array($positions)
                && count($positions) > 0) {
           $_positions = array();
           $pos = 1;

           foreach($positions as $id) {
                    $_positions[] = array($pos, '1', $id);
                    $pos += 1;
            }

            $special = new Special();
            $msg = $special->set_position($_positions, $_SESSION['userid']);

            // FIXME: buscar otra forma de hacerlo
            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            require_once(SITE_CORE_PATH.'template_cache_manager.class.php');
            $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete('home|0');
         }
         if(!empty($msg) && $msg == true) {
             echo _("Positions saved successfully.");
         } else{
             echo _("Have a problem, positions can't be saved.");
         }
        exit(0);
    break;

    case 'config':

        $configurationsKeys = array('special_settings',);
        $configurations = s::get($configurationsKeys);
        $tpl->assign(array(
            'configs'   => $configurations,
        ));

        $tpl->display('special/config.tpl');

    break;

    case 'save_config':

        Acl::checkOrForward('SPECIAL_SETTINGS');

        unset($_POST['action']);
        unset($_POST['submit']);

        foreach ($_POST as $key => $value ) { s::set($key, $value); }

        m::add(_('Settings saved successfully.'), m::SUCCESS);

        $httpParams = array(array('action'=>'list'),);
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($httpParams));

    break;

    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    break;

}
