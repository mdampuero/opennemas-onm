<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require_once '../bootstrap.php';

use \Onm\Settings as s;

// Setup view
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('video');

//Setting up available categories for menu.
$category_name = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);

$action = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);
$page = $request->query->filter('page', 0, FILTER_VALIDATE_INT);

if (!empty($category_name) && $category_name != 'home' ) {
    $ccm = ContentCategoryManager::get_instance();
    $category = $ccm->get_id($category_name);
    $actual_category_id = $category;
    $category_real_name = $ccm->get_title($category_name);
    $tpl->assign(
        array(
            'category_name'      => $category_name ,
            'category'           => $category ,
            'actual_category_id' => $actual_category_id ,
            'category_real_name' => $category_real_name ,
            'actual_category'    => $category_name,
        )
    );
} else {
    $category_real_name = 'Portada';
    $tpl->assign(
        array(
            'category_real_name' => $category_real_name ,
        )
    );
    $actual_category_id = $category = 0; //NEED CODE WIDGETS
}

$cm = new ContentManager();

switch ($action) {
    case 'list':
        # If is not cached process this action
        $cacheID = $tpl->generateCacheId($category_name, '', $page);


        if (($tpl->caching == 0)
            || !$tpl->isCached('video/video_frontpage.tpl', $cacheID)
        ) {

            $videosSettings = s::get('video_settings');

            $totalVideosFrontpage = isset($videosSettings['total_front'])?:2;
            $days = isset( $videosSettings['time_last'])?:365;

            if (isset($category_name)
                && !empty($category_name)
                && $category_name != 'home'
            ) {
                $front_videos = $cm->find_all(
                    'Video',
                    'available=1 AND `contents_categories`.`pk_fk_content_category` ='
                    . $actual_category_id . '',
                    'ORDER BY created DESC LIMIT '.$totalVideosFrontpage
                );

                $videos = $cm->find_all(
                    'Video',
                    'available=1 AND `contents_categories`.`pk_fk_content_category` ='
                    . $actual_category_id . '',
                    'ORDER BY views DESC LIMIT 3'
                );

                $others_videos = $cm->find_all(
                    'Video',
                    'available=1 AND created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                    'ORDER BY views DESC LIMIT 3, 9'
                );

                if (count($front_videos) > 0) {
                    foreach ($front_videos as &$video) {
                        $video->category_name = $video->loadCategoryName($video->id);
                        $video->category_title = $video->loadCategoryTitle($video->id);
                    }
                }
                $tpl->assign('front_videos', $front_videos);

            } else {
                $videos = $cm->find_all(
                    'Video',
                    ' available=1 ',
                    'ORDER BY created DESC LIMIT 3'
                );

                $others_videos = $cm->find_all(
                    'Video',
                    ' available=1 AND created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                    'ORDER BY starttime DESC LIMIT 3,12'
                );
            }

            if (count($videos) > 0) {
                foreach ($videos as &$video) {
                    $video->category_name  = $video->loadCategoryName($video->id);
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

            $tpl->assign(
                array(
                    'videos' => $videos,
                    'others_videos' => $others_videos,
                    'page' => '1'
                )
            );
        }

        require_once("video_advertisement.php");

        // Get last comments to show in video frontpage
        $latestComments = $cm->cache->getLastComentsContent('Video', true, $actual_category_id, 4);
        $tpl->assign('lasts_comments', $latestComments);

        if (isset($category_name)
            && !empty($category_name)
            && $category_name != 'home'
        ) {
            $tpl->display('video/video_frontpage.tpl', $cacheID);
        } else {
            $tpl->display('video/video_main_frontpage.tpl', $cacheID);
        }

        break;
    case 'inner':

        $dirtyID = $request->query->filter('id', '', FILTER_SANITIZE_STRING);
        $videoID = Content::resolveID($dirtyID);

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($videoID)) {
            Application::forward301('/video/');
        }

        //Get other_videos for widget video most
        $days = isset( $videosSettings['time_last'])?:124;
        $others_videos = $cm->find_all(
            'Video',
            ' available=1 AND pk_content <> '.$videoID,
            ' ORDER BY created DESC LIMIT 4'
        );

        if (count($others_videos) > 0) {
            foreach ($others_videos as &$otherVideo) {
                $otherVideo->category_name  = $otherVideo->loadCategoryName($otherVideo->id);
                $otherVideo->category_title = $otherVideo->loadCategoryTitle($otherVideo->id);
            }
        }

        $tpl->assign('others_videos', $others_videos);
        # If is not cached process this action
        $cacheID = $tpl->generateCacheId($category_name, '', $videoID);

        if (($tpl->caching == 0)
            || !$tpl->isCached('video/video_inner.tpl', $videoID)
        ) {

            $video = new Video($videoID);
            $tpl->assign('contentId', $videoID); // Used on module_comments.tpl
            $category = $video->category;

            Content::setNumViews($video->id);
            $video->category_name = $video->loadCategoryName($video->id);
            $video->category_title = $video->loadCategoryTitle($video->id);
            $tpl->assign(
                array(
                    'category' => $category,
                    'category_name' => $video->category_name,
                    'contentId' => $videoID,
                    'video' => $video,
                    'action' => 'inner',
                )
            );

            require_once "video_inner_advertisement.php";

        } //end iscached

        //Fetch comments for this opinion
        $tpl->assign('contentId', $videoID);

        /******* SUGGESTED CONTENTS *******/
        $objSearch = cSearch::getInstance();
        $arrayResults=$objSearch->searchSuggestedContents(
            $video->metadata,
            'Video',
            "pk_fk_content_category= ".$video->category.
            " AND contents.available=1 AND pk_content = pk_fk_content",
            4
        );
        $tpl->assign('suggested', $arrayResults);

        Content::setNumViews($videoID);
        $tpl->display('video/video_inner.tpl', $cacheID);

        break;
    case 'videos_incategory':

        $video = null;

        $itemsPage = 3;

        $category_name = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        if ($category == 0) {
            $category = $request->query->filter('category', '', FILTER_SANITIZE_STRING);
        }

        $page = $request->query->filter('page', 1, FILTER_VALIDATE_INT);

        $_limit = 'LIMIT ' . ($page - 1) * $itemsPage . ', ' . ($itemsPage);

        $videos = $cm->find_all(
            'Video',
            'available=1 AND `contents_categories`.`pk_fk_content_category` =' . $category . '',
            'ORDER BY created DESC ' . $_limit
        );

        if (count($videos) > 0) {
            foreach ($videos as &$video) {
                $video->category_name  = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
        } else {
            $page = 1;
            Application::forward('/controllers/videos.php?action=videos_incategory&category=' . $category . '&page=1');
        }
        $tpl->assign('videos', $videos);
        $tpl->assign('page', $page);
        $tpl->assign('actual_category_id', $category);
        $tpl->assign('total_incategory', '9');
        $html = $tpl->fetch('video/partials/_widget_video_incategory.tpl');
        echo $html;

        break;
    case 'videos_more':

        $video = null;

        $category_name = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $page = $request->query->filter('page', 1, FILTER_VALIDATE_INT);

        if ($category == 0) {
            $itemsPage = 6;
        } else {
            $itemsPage = 3;
        }

        $_limit = 'LIMIT ' . ($page - 1) * $itemsPage . ', ' . ($itemsPage);

        $others_videos = $cm->find_all(
            'Video',
            'available=1 AND `contents_categories`.`pk_fk_content_category` <> ' . $category . '',
            'ORDER BY created DESC ' . $_limit
        );

        if (count($others_videos) > 0) {
            foreach ($others_videos as &$video) {
                $video->category_name  = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
        } else {
            $page = 1;
            Application::forward('/controllers/videos.php?action=videos_more&category=' . $category . '&page=1');
        }
        $tpl->assign('others_videos', $others_videos);
        $tpl->assign('page', $page);
        $tpl->assign('actual_category_id', $category);
        $tpl->assign('total_more', '4');
        $html = $tpl->fetch('video/partials/_widget_video_more_interesting.tpl');
        echo $html;

        break;
    default:

        Application::forward301('/');
        break;
}

