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

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
/**
 * Setting up available categories for menu.
*/
$ccm = new ContentCategoryManager();

$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
if(empty($category_name)) {
    $category_name = filter_input(INPUT_POST,'category_name',FILTER_SANITIZE_STRING);
}
$actual_category_id = $category = 0; //NEED CODE WIDGETS

$menuFrontpage = Menu::renderMenu('video');
$tpl->assign('menuFrontpage',$menuFrontpage->items);

if(!empty($category_name)) {
    $category = $ccm->get_id($category_name);
    $actual_category_id = $category;
    $category_real_name = $ccm->get_title($category_name);
    $tpl->assign(array( 'category_name' => $category_name ,
                        'category' => $category ,
                        'actual_category_id' => $actual_category_id ,
                        'category_real_name' => $category_real_name ,
                ) );
}

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

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
        $cacheID = $tpl->generateCacheId('video-frontpage', $category_name, '');
        
        if (($tpl->caching == 0)
            || !$tpl->isCached('video/video_frontpage.tpl', $cacheID)
        ) {

            $videosSettings = s::get('video_settings');
            
            $totalVideosFrontpage = isset($videosSettings['total_front'])?:2;
            $days = isset( $videosSettings['time_last'])?:124;

            if (isset($category_name) && !empty($category_name) ) {
              
                $front_videos = $cm->find_all(
                    'Video',
                    'available=1 AND `contents_categories`.`pk_fk_content_category` ='
                    . $actual_category_id . '', 'ORDER BY created DESC LIMIT '.$totalVideosFrontpage
                );

                $videos = $cm->find_all(
                    'Video',
                    'available=1 AND `contents_categories`.`pk_fk_content_category` ='
                    . $actual_category_id . '', 'ORDER BY views DESC LIMIT 3'
                );

                $others_videos = $cm->find_all(
                    'Video',
                    'available=1 AND created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                    'ORDER BY views DESC LIMIT 3'
                );
                
                if (count($front_videos) > 0) {
                    foreach ($front_videos as &$video) {
                        $video->category_name = $video->loadCategoryName($video->id);
                        $video->category_title = $video->loadCategoryTitle($video->id);
                    }
                }
                $tpl->assign( 'front_videos', $front_videos);
               
            } else {
                $videos = $cm->find_all( 'Video',
                    ' available=1 ',
                    'ORDER BY created DESC LIMIT 3'
                );

                $others_videos = $cm->find_all('Video',
                    ' available=1 AND created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                    'ORDER BY views DESC LIMIT 8'
                );
            }

            if (count($videos) > 0) {
                foreach ($videos as &$video) {
                    $video->category_name = $video->loadCategoryName($video->id);
                    $video->category_title = $video->loadCategoryTitle($video->id);
                }
            } else {
                   // Application::forward301('/video/');
            }
            
            if (count($others_videos) > 0) {
                foreach ($others_videos as &$video) {
                    $video->category_name = $video->loadCategoryName($video->id);
                    $video->category_title = $video->loadCategoryTitle($video->id);
                }
            }

            $tpl->assign( array( 'videos' => $videos,
                                 'others_videos' => $others_videos,
                                 'page' => '1' ) );
        }

        require_once("video_advertisement.php");

        // Get last comments to show in video frontpage
        $latestComments = $cm->cache->getLastComentsContent('Video', true, $actual_category_id, 4);
        $tpl->assign('lasts_comments', $latestComments);

        if (isset($category_name) && !empty($category_name) ) {
            $tpl->display('video/video_frontpage.tpl', $cacheID);
        } else {
            $tpl->display('video/video_main_frontpage.tpl', $cacheID);
        }
        break;

    case 'inner':

        $videoID = filter_input( INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT );

        # If is not cached process this action
        $cacheID = $tpl->generateCacheId('video-inner', $category_name, $videoID);

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

        /******* SUGGESTED CONTENTS *******/
        $objSearch = cSearch::Instance();
        $arrayResults=$objSearch->SearchSuggestedContents(
            $video->metadata, 'Video',
            "pk_fk_content_category= ".$video->category.
            " AND contents.available=1 AND pk_content = pk_fk_content",
            4
        );
        $tpl->assign('suggested', $arrayResults);

        Content::setNumViews($videoID);
        $tpl->display('video/video_inner.tpl', $cacheID);

    break;

    case 'videos_incategory':

        $video = NULL;

        $items_page = 6;

        $page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING,  array('options' => array('default' => '1')));

        $_limit = 'LIMIT ' . ($page - 1) * $items_page . ', ' . ($items_page);
       
        $videos = $cm->find_all(
            'Video',
            'available=1 AND `contents_categories`.`pk_fk_content_category` =' . $category . '',
            'ORDER BY created DESC ' . $_limit
        );

        if (count($videos) > 0) {
            foreach ($videos as &$video) {
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
        
        $page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING,  array('options' => array('default' => '1')));

        $items_page = 3;

        $_limit = 'LIMIT ' . ($page - 1) * $items_page . ', ' . ($items_page);

        $others_videos = $cm->find_all(
            'Video',
            'available=1 AND `contents_categories`.`pk_fk_content_category` <> ' . $category . '',
            'ORDER BY created DESC ' . $_limit
        );

        if (count($others_videos) > 0) {
            foreach ($others_videos as &$video) {
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
