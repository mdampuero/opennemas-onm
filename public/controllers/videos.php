<?php
/**
 * Start up and setup the app
 */
require_once ('../bootstrap.php');
use \Onm\Settings as s;

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('video');
$cm = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

if (isset($_GET['category_name'])) {
    $category_name = $_GET['category_name'];
} else {
    $the_categorys = $ccm->find(' fk_content_category=0 AND inmenu=1 AND (internal_category =1 OR internal_category = 5)', 'ORDER BY internal_category DESC, inmenu DESC, posmenu ASC LIMIT 0,6');
    foreach ($the_categorys as $categ) {

        if (!$ccm->isEmpty($categ->name)) {
            $this_category_data = $categ;
            break;
        }
    }
    $category_name = $this_category_data->name;
    $category_title = $this_category_data->title;
    $category = $this_category_data->pk_content_category;
    $_GET['category_name'] = $category_name;
}
$actual_category = $category_name;

if (isset($_GET['subcategory_name'])) {
    $subcategory_name = $_GET['subcategory_name'];
    $actual_category = $_GET['subcategory_name'];
}

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

if (!isset($_GET['subcategory_name'])) {
    $actual_category = $_GET['category_name'];
} else {
    $actual_category = $_GET['subcategory_name'];
}
$tpl->assign('actual_category', $actual_category);
$actual_category_id = $ccm->get_id($actual_category);
$tpl->assign('actual_category_id', $actual_category_id);


$tpl->assign('category_name', $category_name);
if (!isset($menuFrontpage) || empty($menuFrontpage->items)) {
    $menuFrontpage= Menu::renderMenu('video');
}
$tpl->assign('menuFrontpage',$menuFrontpage->items);

/**
 * Getting request params
 */
$action = filter_input(
    INPUT_GET, 'action', FILTER_SANITIZE_STRING,
    array('options' => array('default' => 'list'))
);

switch ($action) {

    case 'list':

        # If is not cached process this action
        $cacheID = $tpl->generateCacheId($actual_category, null, '');
        if (($tpl->caching == 0)
            || !$tpl->isCached('video/video_frontpage.tpl', $cacheID)
        ) {

            $videosSettings = s::get('video_settings');
            $totalVideosFrontpage = $videosSettings['total_front'] ?: 2;

            $videos = $cm->find_all(
                'Video',
                'available=1 AND `contents_categories`.`pk_fk_content_category` ='
                . $actual_category_id . '', 'ORDER BY created DESC LIMIT '.$totalVideosFrontpage
            );

            if (count($videos) > 0) {
                foreach ($videos as &$video) {
                    $video->category_name = $video->loadCategoryName($video->id);
                    $video->category_title = $video->loadCategoryTitle($video->id);
                }
            } else {

                if (isset($subcategory_name) && !empty($subcategory_name)) {
                    Application::forward301('/video/' . $category_name . '/');
                } else {
                    Application::forward301('/video/');
                }
            }

            $others_videos = $cm->find_all(
                'Video',
                'available=1 AND `contents_categories`.`pk_fk_content_category` <> ' . $actual_category_id . '',
                'ORDER BY created DESC LIMIT 0, 3'
            );
            foreach ($others_videos as &$video) {
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }

            $tpl->assign('videos', $videos);
            $tpl->assign('others_videos', $others_videos);
            $tpl->assign('page', '1');
        }

        // Get last comments to show in video frontpage
        $latestComments = $cm->cache->getLastComentsContent('Video', true, $actual_category_id, 4);
        $tpl->assign('lasts_comments', $latestComments);
        require_once ("video_advertisement.php");

        $tpl->display('video/video_frontpage.tpl', $cacheID);

        break;

    case 'inner':

        $videoID = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT );

        # If is not cached process this action
        $cacheID = $tpl->generateCacheId($actual_category, '', $videoID);
        if (($tpl->caching == 0)
            || !$tpl->isCached('video/video_inner.tpl', $_REQUEST['id'])
        ) {

            $video = new Video($videoID);
            $tpl->assign('contentId', $videoID); // Used on module_comments.tpl
            $category = $video->category;

            Content::setNumViews($video->id);
            $video->category_name = $video->loadCategoryName($video->id);
            $video->category_title = $video->loadCategoryTitle($video->id);
            $tpl->assign(array(
                'category' => $category,
                'category_name' => $video->category_name,
                'contentId' => $videoID,
                'video' => $video,
                'action' => 'inner',
            ));
            require_once ("video_inner_advertisement.php");

        } //end iscached

        //Fetch comments for this opinion
        $tpl->assign('contentId', $videoID);

        Content::setNumViews($videoID);
        $tpl->display('video/video_inner.tpl', $cacheID);

    break;

    case 'videos_incategory':

        $video = NULL;

        $items_page = 6;
        if ($_GET['page'] > 0) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $category = $_GET['category'];

        $_limit = 'LIMIT ' . ($page - 1) * $items_page . ', ' . ($items_page);
        $cm = new ContentManager();
        $videos = $cm->find_all(
            'Video',
            'available=1 AND `contents_categories`.`pk_fk_content_category` =' . $category . '',
            'ORDER BY created DESC ' . $_limit
        );

        if (count($videos) > 0) {
            foreach ($videos as $video) {
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
        } else {
            $page = 1;
            Application::forward('/controllers/videos.php?action=videos_incategory&category=' . $category . '&page=1');
        }
        $tpl->assign('videos', $videos);
        $tpl->assign('page', $page);
        $tpl->assign('category', $category);
        $tpl->assign('total_incategory', '9');
        $html = $tpl->fetch('video/partials/_widget_video_incategory.tpl');
        echo $html;
        exit(0);

        break;

    case 'videos_more':

        $video = NULL;
        $category = $_GET['category'];

        if ($_GET['page'] > 0) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $items_page = 3;
        $_limit = 'LIMIT ' . ($page - 1) * $items_page . ', ' . ($items_page);
        $cm = new ContentManager();
        $others_videos = $cm->find_all(
            'Video',
            'available=1 AND `contents_categories`.`pk_fk_content_category` <> ' . $category . '',
            'ORDER BY created DESC ' . $_limit
        );

        if (count($others_videos) > 0) {
            foreach ($others_videos as $video) {
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
        } else {
            $page = 1;
        }
        $tpl->assign('others_videos', $others_videos);
        $tpl->assign('page', $page);
        $tpl->assign('category', $category);
        $tpl->assign('total_more', '4');
        $html = $tpl->fetch('video/partials/_widget_video_more_interesting.tpl');
        echo $html;
        exit(0);
    break;

    default:
        Application::forward301('/');
    break;
}
