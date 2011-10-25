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
\Onm\Module\ModuleManager::checkActivatedOrForward('BOOK_MANAGER');

 // Check if the user can admin books
Acl::checkOrForward('BOOK_ADMIN');


$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', _('Book Management'));

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
    $category ='favorite';
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

$configurations = s::get('book_settings');
$numFavorites = (isset($configurations['total_widget']) && !empty($configurations['total_widget']))? $configurations['total_widget']: 1;
$sizeFile = (isset($configurations['size_file']) && !empty($configurations['size_file']))? $configurations['size_file']: 5000000;

switch($action) {

    case 'list':
        Acl::checkOrForward('SPECIAL_ADMIN');

        $cm = new ContentManager();

        if (empty($page)) {
            $limit= "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit= "LIMIT ".($page-1) * ITEMS_PAGE .', '.ITEMS_PAGE;
        }

        if ($category == 'favorite') {
            $specials = $cm->find_all('Special', 'favorite =1 AND available =1', 'ORDER BY position, created DESC '.$limit);

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
        $cm = new ContentManager();
        $special = new Special($_REQUEST['id']);
        $nots=$special->get_contents($_REQUEST['id']);
        $tpl->assign('special', $special);
        if($nots){
            foreach($nots as $noticia){
                if(($noticia['position']%2)==0){
                    $noticias_right[]=new Article($noticia['fk_content']);
                }else{
                    $noticias_left[]=new Article($noticia['fk_content']);
                }
            }
        }

        $tpl->assign('noticias_right',$noticias_right);
                    $tpl->assign('noticias_left',$noticias_left);
        $articles= $cm->find_by_category('Article', $category, ' 1=1 ', 'ORDER BY created DESC LIMIT 0,1000');
        $params=$category;
        $articles = $cm->paginate_num_js($articles,16,1,'changeSpecials',$params);
                    //var_dump($cm->pager);
        $tpl->assign('paginacion_articles', $cm->pager);
        $tpl->assign('articles', $articles);

        $tpl->display('special/new.tpl');
	
    break;
    case 'new':
        $cm = new ContentManager();
        $articles= $cm->find_by_category('Article', $category, '1=1 ', 'ORDER BY created DESC LIMIT 0,1000');
        $params = $category;
        $articles = $cm->paginate_num_js($articles,16,1,'changeSpecials',$params);
                    //var_dump($cm->pager);
        $tpl->assign('paginacion_articles', $cm->pager);
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
        $special = new Special($_REQUEST['id']);
        $special->update($_POST);
        if(isset($_GET['desde']) && $_GET['desde']=='hemeroteca'){
            Application::forward('hemeroteca.php?action=list&type=special&category='.$category);
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'duplicate':
        // Crear un nuevo special  a partir de uno existente
        $special = new Special( $_POST['id'] );
        if($special->duplicate( $_POST )) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_POST['category']);
        } else {
            $tpl->assign('errors', $article->errors);

        }
    break;

    case 'delete':
        $special = new Special($_REQUEST['id']);
        $special->delete($_REQUEST['id']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'change_favorite':
        $special = new Special($_REQUEST['id']);
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $special->set_favorite($_REQUEST['id'],$status,$category);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'inhome_status':

        $special = new Special($_REQUEST['id']);
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $special->set_inhome($status,$_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'available_status':
        $special = new Special($_REQUEST['id']);

        // FIXME: evitar otros valores erróneos
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $special->set_available($_REQUEST['id'],$status);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'change_status':
        $special = new Special($_REQUEST['id']);

        //Publicar o no, comprobar num clic
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $special->set_status($status);

        if(isset($_GET['desde']) && $_GET['desde']=='search'){
            Application::forward('search.php?action=search&stringSearch='.$_GET['stringSearch'].'&page='.$_GET['page']);
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'mstatus':
        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
            $fields = $_REQUEST['selected_fld'];
            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $special = new Special($i);
                    $special->set_status($i);   //Se reutiliza el id para pasar el status
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'mfrontpage':
        $status = ($_REQUEST['id']==1)? 1: 0; // Evitar otros valores
        if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
        {
            $fields = $_REQUEST['selected_fld'];

            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $special = new Special($i);
                    $special->set_frontpage($status);   //Se reutiliza el id para pasar el estatus
                    $special->set_available($i,$status);//Disponible
                }
            }
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;

    case 'mdelete':
          if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
             $fields = $_REQUEST['selected_fld'];
             if(is_array($fields)) {
                foreach($fields as $content ) {
                    $special=new Special($content);
                    $special->delete($content);
                }
             }
          }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
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
        $articles= $cm->find_by_category('Article',$category, '1=1 ', 'ORDER BY created DESC LIMIT 0,1000');
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



    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    break;
	
}
