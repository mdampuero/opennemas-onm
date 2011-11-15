<?php
use Onm\Settings as s,
    Onm\LayoutManager;
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Dieguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

//require_once(SITE_LIBS_PATH.'Pager/Pager.php');
require_once('../../controllers/utils_content.php');

// Fetch request variables
$action   = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING,  array('options' => array( 'default' => 'list')));
$page     = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array('options' => array( 'default' => 1)));
$category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING,   array('options' => array( 'default' => 'home')));
(!isset($_SESSION['_from'])) ? $_SESSION['_from'] = $category : null ;
(!isset($_SESSION['desde'])) ? $_SESSION['desde'] = 'list' : null ;


$tpl->assign('category', $category);

/**
 * Getting categories
*/
$ccm = ContentCategoryManager::get_instance();
$tplFrontend = new Template(TEMPLATE_USER);
$section = $ccm->get_name($category);
$section = (empty($section))? 'home': $section;
$categoryID = ($category == 'home') ? 0 : $category;
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($categoryID);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);
$allcategorys = $parentCategories;


switch ($action) {

    case 'list':

        // Check if the user can edit frontpages
        if(!Acl::check('ARTICLE_FRONTPAGE')) {
            Acl::deny();
        } elseif (!Acl::_C($categoryID)) {
            $categoryID = $_SESSION['accesscategories'][0];
            $section = $ccm->get_name($categoryID);
            $_REQUEST['category'] = $categoryID;
            list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

            $tpl->assign('subcat', $subcat);
            $tpl->assign('allcategorys', $parentCategories);
            $tpl->assign('datos_cat', $datos_cat);
            $tpl->assign('category', $_REQUEST['category']);
        }

        $lm  = new LayoutManager(
            SITE_PATH."/themes/".TEMPLATE_USER."/layouts/default.xml"
        );
        $layout = $lm->render();

        $cm      = new ContentManager();
        $rating  = new Rating();
        $comment = new Comment();
        $aut     = new User();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsForHomepageOfCategory($categoryID);

        // Sort all the elements by its position
        $contentElementsInFrontpage  = $cm->sortArrayofObjectsByProperty($contentElementsInFrontpage, 'position');

        // Populaze 
        foreach ($contentElementsInFrontpage as $content){
            $content->category_name  = $content->loadCategoryName($content->id);
            $content->publisher      = $aut->get_user_name($content->fk_publisher);
            $content->last_editor    = $aut->get_user_name($content->fk_user_last_editor);
            $content->ratings        = $rating->get_value($content->id);
            $content->comments       = $comment->count_public_comments($content->id);
        }

        $contentsExcludedForProposed = array();
        foreach($contentElementsInFrontpage as &$content) {
            $contentsExcludedForProposed[] = $content->id;
        }

        if(count($contentsExcludedForProposed) >0) {
            $opinionsExcluded = implode(', ', $contentsExcludedForProposed);
            $sqlExcludedOpinions = ' AND `pk_opinion` NOT IN ('.$opinionsExcluded.')';
        } else {
            $sqlExcludedOpinions = ' AND 1 = 1';
        }

        $opinions = $cm->find(
            'Opinion',
            'contents.available = 1 ' . $sqlExcludedOpinions,
            ' ORDER BY created DESC LIMIT 0,16'
        );

        $rating = new Rating();
        foreach($opinions as $opinion) {
            $opinion->comments = $comment->count_public_comments($opinion->id);
            $opinion->author   = new Author($opinion->fk_author);
            $opinion->ratings  = $rating->get_value($opinion->id);
        }

        if (count($contentsExcludedForProposed) >0) {
            $widgets_excluded = implode(', ', $contentsExcludedForProposed);
            $sql_excluded_widgets = ' AND `pk_widget` NOT IN ('.$widgets_excluded.')';
        } else {
            $sql_excluded_widgets  = ' AND 1 = 1';
        }

        $widgets = $cm->find(
            'Widget',
            'fk_content_type=12 AND `available`=1 ' . $sql_excluded_widgets,
            'ORDER BY created DESC '
        );

        $tpl->assign(array(
            'widgets'            =>  $widgets,
            'opinions'           => $opinions,
            'category'           => $category,
            'frontpage_articles' => $contentElementsInFrontpage,
            'layout'             => $layout,
        ));
        $_SESSION['desde'] = 'list';
        $_SESSION['_from'] = $category;

        $tpl->display('frontpagemanager/list.tpl');

    break;

    default: {
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    } break;
}