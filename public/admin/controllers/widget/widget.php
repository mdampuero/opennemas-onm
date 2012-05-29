<?php
use Onm\Settings as s,
    Onm\Message as m;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(dirname(__FILE__).'/../../session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gestor de Widgets');

// Widget instance
$widget = new Widget();
$c = new Content();
$cm = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;

switch($action) {
    case 'edit': {
        $id = $_REQUEST['id'];
        $widget->read($id);
        if(isset($_REQUEST['category'])) {
            $_SESSION['categoria'] = $_REQUEST['category'];
        }
        $allInteligentWidgets = Widget::getAllInteligentWidgets();
        $tpl->assign('all_widgets', $allInteligentWidgets);
        $tpl->assign('id', $id);
        $tpl->assign('widget', $widget);
        $tpl->display('widget/edit.tpl');
        break;
    } // Executa tamén new

    case 'new': {
        $allInteligentWidgets = Widget::getAllInteligentWidgets();
        $tpl->assign('all_widgets', $allInteligentWidgets);
        $tpl->display('widget/edit.tpl');
        break;
    }

    case 'delete': {
        $id = $_REQUEST['id'];
        $widget->delete($id);

        Application::forward('?action=list');
        break;
    }

    case 'save': {
        $data = $_POST;

        if(intval($data['id']) > 0) {
            $widget->update($data);
        } else {
            $widget->create($data);
        }

        if (isset($_SESSION['desde'])) {
            if ($_SESSION['desde'] == 'list') {
                Application::forward('/admin/controllers/frontpagemanager/frontpagemanager.php?action='.$_SESSION['desde'].'&category='.$_SESSION['categoria']);
            }elseif ($_SESSION['desde'] == 'widget') {
                Application::forward('?action=list');
            }elseif ($_SESSION['desde'] == 'search_advanced') {
                Application::forward('/admin/controllers/search_advanced/search_advanced.php');
            }
        }

        Application::forward('?action=list');

        break;
    }

    case 'changeavailable': {
        $widget->read($_REQUEST['id']);

        $available = ($widget->available+1) % 2;
        $widget->set_available($available, $_SESSION['userid']);

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            list($img, $text)  = ($available)? array('g', _('Published')): array('r', _('Pending'));

            echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            exit(0);
        }

        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
        break;
    }

    case 'unpublish': {
        $widget = new Widget();
        $widget->read($_REQUEST['id']);
        $widget->dropFromHomePageOfCategory($_REQUEST['category'],$_REQUEST['id']);
        /* Limpiar la cache de portada de todas las categorias */
        $c->refreshFrontpage();
        //$refresh = Content::refreshFrontpageForAllCategories();

        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
        break;
    }

    case 'archive': {
        $widget = new Widget();
        $widget->read($_REQUEST['id']);
        $widget->dropFromHomePageOfCategory($_REQUEST['category'],$_REQUEST['id']);
        /* Limpiar la cache de portada de todas las categorias */
        $c->refreshFrontpage();
        //$refresh = Content::refreshFrontpageForAllCategories();

        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
        break;
    }

    case 'content-provider':

        $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING,   array('options' => array( 'default' => 'home')));
        $page     = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING,   array('options' => array( 'default' => 1)));

        if ($category == 'home') { $category = 0; }

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedOpinions = '';
        if (count($contentElementsInFrontpage) > 0) {
            $contentsExcluded = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedOpinions = ' AND `pk_widget` NOT IN ('.$contentsExcluded.')';
        }

        list($widgets, $pager) = $cm->find_pages(
            'Widget',
            'contents.available=1 '. $sqlExcludedOpinions,
            'ORDER BY created DESC ', $page, 5
        );

        $tpl->assign(array(
            'widgets' => $widgets,
            'pager'   => $pager,
        ));

        $tpl->display('widget/content-provider.tpl');

        break;

    case 'content-list-provider':
        $items_page = s::get('items_per_page') ?: 20;
        $page = filter_input( INPUT_GET, 'page' , FILTER_SANITIZE_STRING, array('options' => array('default' => '1')) );
        $cm = new ContentManager();

        list($widgets, $pager)= $cm->find_pages('Widget', "fk_content_type=12 AND `available`=1",
                                                'ORDER BY starttime DESC ',
                                                 $page, $items_page);

        $tpl->assign(array('contents'=>$widgets,
                            'pagination'=>$pager->links
                    ));

        $html_out = $tpl->fetch("common/content_provider/_container-content-list.tpl");
        Application::ajaxOut($html_out);

    break;

    case 'list':
    default: {
        //$widgets = $cm->find_by_category('Widget', 3, 'fk_content_type=12 ', 'ORDER BY created DESC');
        $widgets = $cm->find('Widget', 'fk_content_type=12', 'ORDER BY title ASC ');

        /*$items_page = 25;
        $page = (!isset($_REQUEST['page']))? 1: intval($_REQUEST['page']);

        // Pager
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($terms),
        );
        $pager = Pager::factory($pager_options);

        $terms = array_slice($terms, ($page-1)*$items_page, $items_page);*/

        $_SESSION['desde'] = 'widget';

        $tpl->assign('widgets', $widgets);
        //$tpl->assign('pager', $pager);
        $tpl->display('widget/list.tpl');
        break;
    }
}
