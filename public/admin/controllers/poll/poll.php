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
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

\Onm\Module\ModuleManager::checkActivatedOrForward('POLL_MANAGER');

Acl::checkOrForward('POLL_ADMIN');

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Polls Management');

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT, array('options' => array('default' => 1)));


/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('poll');


$category = filter_input(INPUT_GET,'category');
if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT, array('options' => array('default' => 0 )));
}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);

if(empty($category)) {$category ='favorite';}

$tpl->assign('category', $category);
$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $categoryData);
$allcategorys = $parentCategories;
/******************* GESTION CATEGORIAS  *****************************/

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

switch ($action) {

    case 'list':  //Buscar publicidad entre los content

        $cm = new ContentManager();


        if(empty($numItems)) {$numItems=16;}
        $configurations = s::get('poll_settings');
        $numFavorites = $configurations['total_widget'];

        if (empty($page)) {
            $limit = "LIMIT ".(ITEMS_PAGE+1);
        } else {
            $limit = "LIMIT ".($page-1) * ITEMS_PAGE .', '.$numItems;
        }

        if ($category == 'favorite') { //Widget video
            $polls = $cm->find_all('Poll', 'favorite = 1 AND available =1', 'ORDER BY  created DESC '. $limit);

            if (count($polls) != $numFavorites ) {
                m::add( sprintf(_("You must put %d polls in the HOME widget"), $numFavorites));
            }

            if(!empty($polls)){
                foreach ($polls as &$poll) {
                    $poll->category_name = $ccm->get_name($poll->category);
                    $poll->category_title = $ccm->get_title($poll->category_name);
                }
            }

        } elseif ($category == 'all') {
            $polls = $cm->find_all('Poll', 'available =1', 'ORDER BY created DESC '. $limit);

            if(!empty($polls)){
                foreach ($polls as &$poll) {
                    $poll->category_name = $ccm->get_name($poll->category);
                    $poll->category_title = $ccm->get_title($poll->category_name);
                }
            }
        } else {
            // ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
            $polls = $cm->find_by_category('Poll', $category, ' 1=1', 'ORDER BY  created DESC '.$limit);
        }

        $params = array(
            'page'=>$page, 'items'=>ITEMS_PAGE,
            'total' => count($polls),
            'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&category='.$category,
        );

        $pagination = \Onm\Pager\SimplePager::getPagerUrl($params);
        $tpl->assign( array(
            'paginacion' => $pagination,
            'polls'=> $polls)
        );

        $tpl->display('polls/list.tpl');

    break;

    case 'new':

        Acl::checkOrForward('POLL_CREATE');

        $tpl->display('polls/new.tpl');

    break;

    case 'read': //habrá que tener en cuenta el tipo
        Acl::checkOrForward('POLL_UPDATE');

        $poll = new Poll( $_REQUEST['id'] );
        $tpl->assign('poll', $poll);

        $items=$poll->get_items($_REQUEST['id']);
        $tpl->assign('items', $items);

        $tpl->display('polls/new.tpl');

    break;

    case 'create':
        Acl::checkOrForward('POLL_CREATE');

        $poll = new Poll();
        $_POST['publisher'] = $_SESSION['userid'];
        if ($poll->create( $_POST )) {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
        } else {
            $tpl->assign('errors', $poll->errors);
        }
        $tpl->display('polls/new.tpl');

    break;

    case 'update':
        Acl::checkOrForward('POLL_UPDATE');

        $poll = new Poll();
        $poll->update( $_REQUEST );

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

    break;

    case 'validate':
        $poll = null;

        if (empty($_POST["id"])) {
            Acl::checkOrForward('POLL_CREATE');
            $poll = new Poll();
            if(!$poll->create( $_POST ))
                $tpl->assign('errors', $poll->errors);
        } else {
            Acl::checkOrForward('POLL_UPDATE');
            $poll = new Poll();
            $poll->update( $_REQUEST );
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$poll->id);
    break;

    case 'delete':
        Acl::checkOrForward('POLL_DELETE');
        $poll = new Poll();
        $poll->delete( $_POST['id'] );

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    break;

    case 'change_status':
        Acl::checkOrForward('POLL_AVAILABLE');
        $poll = new Poll($_REQUEST['id']);
        //Publicar o no,
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $poll->set_available($status, $_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);

    break;

    case 'change_favorite':
        Acl::checkOrForward('POLL_FAVORITE');
        $poll = new Poll($_REQUEST['id']);
        //Publicar o no,
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $poll->set_favorite($status);
        if (isset($_GET['from']) && $_GET['from'] =='index') {
            Application::forward('index.php?action=list&msg='.$msg);
        }else{
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
        }
    break;

    case 'change_available':
        Acl::checkOrForward('POLL_AVAILABLE');

        $poll = new Poll($_REQUEST['id']);
        //Publicar o no,
        $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
        $poll->set_available($_REQUEST['id'],$status);

        if ($_GET['from']=='index') {
            Application::forward('index.php?action=list');
        } else {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        }
    break;

    case 'mfrontpage':
        Acl::checkOrForward('POLL_AVAILABLE');

        if (isset($_REQUEST['selected_fld'])
                && count($_REQUEST['selected_fld'])>0) {
            $fields = $_REQUEST['selected_fld'];

            if (is_array($fields)) {
                foreach($fields as $i ) {
                    $poll = new Poll($i);
                    $poll->set_status($_REQUEST['id']);   //Se reutiliza el id para pasar el estatus
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category);
    break;
    case 'mdelete':
        Acl::checkOrForward('POLL_DELETE');

        if(isset($_REQUEST['selected_fld'])
                && count($_REQUEST['selected_fld'])>0) {

            $fields = $_REQUEST['selected_fld'];

            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $poll = new Poll($i);
                    $poll->delete( $i);
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    break;

    case 'config':

        Acl::checkOrForward('POLL_SETTINGS');

        $configurationsKeys = array('poll_settings',);
        $configurations = s::get($configurationsKeys);
        $tpl->assign(array(
            'configs'   => $configurations,
        ));

        $tpl->display('polls/config.tpl');

    break;

    case 'save_config':

        Acl::checkOrForward('POLL_SETTINGS');

        unset($_POST['action']);
        unset($_POST['submit']);

        foreach ($_POST as $key => $value ) { s::set($key, $value); }

        m::add(_('Settings saved successfully.'), m::SUCCESS);

        $httpParams = array(array('action'=>'list'),);
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));


    default:
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$page);
    break;
}
