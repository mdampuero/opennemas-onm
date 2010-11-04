<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Set up view
*/
$tpl = new Template(TEMPLATE_USER);

/**
 * Set up Category management
*/
$ccm = new ContentCategoryManager();

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
/****  CATEGORY DEFAULT mientras no hay home de gallery  * ***/
//Getting articles
$cm = new ContentManager();
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
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
if (!isset($_GET['subcategory_name'])) {
    $actual_category = $_GET['category_name'];
} else {
    $actual_category = $_GET['subcategory_name'];
}
$tpl->assign('actual_category', $actual_category);
$actual_category_id = $ccm->get_id($actual_category);
$tpl->assign('actual_category_id', $actual_category_id);

/**************************************   VIDEOS  ***********************************************/
if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
        case 'list':
            //SECURITY REASONS
            $video = NULL;
            $cm = new ContentManager();
            $videos = $cm->find_all('Video', 'available=1 AND `contents_categories`.`pk_fk_content_category` =' . $actual_category_id . '', 'ORDER BY created DESC LIMIT 0, 6');
            if (count($videos) > 0) {
                foreach ($videos as $video) {
                    //miramos el fuente youtube o vimeo
                    if ($video->author_name == 'vimeo') {
                        $url = "  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                        $curl = curl_init('http://vimeo.com/api/v2/video/' . $video->videoid . '.php');
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                        $return = curl_exec($curl);
                        $return = unserialize($return);
                        curl_close($curl);
                        if (!empty($return)) {
                            $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                            $video->thumbnail_small = $return[0]['thumbnail_small'];
                        }
                    }
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
            $others_videos = $cm->find_all('Video', 'available=1 AND `contents_categories`.`pk_fk_content_category` <> ' . $actual_category_id . '', 'ORDER BY created DESC LIMIT 0, 3');
            foreach ($others_videos as $video) {
                //miramos el fuente youtube o vimeo
                if ($video->author_name == 'vimeo') {
                    $url = "  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                    $curl = curl_init('http://vimeo.com/api/v2/video/' . $video->videoid . '.php');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                    $return = curl_exec($curl);
                    $return = unserialize($return);
                    curl_close($curl);
                    $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                    $video->thumbnail_small = $return[0]['thumbnail_small'];
                }
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
            $tpl->assign('videos', $videos);
            $tpl->assign('others_videos', $others_videos);
            $tpl->assign('page', '1');
            //last_comments
            //$lasts_comments = $cm->find_all('Comment','available=1 AND  content_status=1','ORDER BY created DESC LIMIT 0, 6');
            $lasts_comments = $cm->cache->getLastComentsContent('Video', true, $actual_category_id, 4);
            $tpl->assign('lasts_comments', $lasts_comments);
            /********************************* ADVERTISEMENTS  *********************************************/
            require_once ("video_advertisement.php");
            /********************************* ADVERTISEMENTS  *********************************************/
        break;
        case 'inner':
            //FIXED: check if there is the album 'id_album' otherwise exit()
            //SECURITY REASONS
            $video = NULL;
            $cm = new ContentManager();
            if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
                $videos = $cm->find('Video', 'available=1 and pk_content !=' . $_REQUEST['id'], 'ORDER BY created DESC LIMIT 0 , 2');
                $thisvideo = new Video($_REQUEST['id']);
            } else {
                $videos = $cm->find('Video', 'available=1', 'ORDER BY created DESC LIMIT 0 , 2');
                $thisvideo = array_shift($videos); //Extrae el primero
                
            }
            $category = $thisvideo->category;
            $thisvideo->setNumViews();
            //Content::setNumViews($thisvideo->id);
            $thisvideo->category_name = $thisvideo->loadCategoryName($thisvideo->id);
            $thisvideo->category_title = $thisvideo->loadCategoryTitle($thisvideo->id);
            $tpl->assign('category', $category);
            $tpl->assign('category_name', $thisvideo->category_name);
            foreach ($videos as $video) {
                if ($video->author_name == 'vimeo') {
                    $url = "  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                    $curl = curl_init('http://vimeo.com/api/v2/video/' . $video->videoid . '.php');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                    $return = curl_exec($curl);
                    $return = unserialize($return);
                    curl_close($curl);
                    $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                    $video->thumbnail_small = $return[0]['thumbnail_small'];
                }
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
            $others_videos = $cm->find('Video', 'available=1', 'ORDER BY created DESC LIMIT 0, 3');
            foreach ($others_videos as $video) {
                if ($video->author_name == 'vimeo') {
                    $url = "  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                    $curl = curl_init('http://vimeo.com/api/v2/video/' . $video->videoid . '.php');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                    $return = curl_exec($curl);
                    $return = unserialize($return);
                    curl_close($curl);
                    $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                    $video->thumbnail_small = $return[0]['thumbnail_small'];
                }
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
            $tpl->assign('video', $thisvideo);
            $tpl->assign('videos', $videos);
            $tpl->assign('others_videos', $others_videos);
            $tpl->assign('action', 'inner');
            /********************************* ADVERTISEMENTS  *********************************************/
            require_once ("video_inner_advertisement.php");
            /********************************* ADVERTISEMENTS  *********************************************/
        break;
        case 'videos_incategory':
            $video = NULL;
            if ($_GET['page'] > 0) {
                $page = $_GET['page'];
            } else {
                $page = 1;
            }
            $category = $_GET['category'];
            $items_page = 6;
            $_limit = 'LIMIT ' . ($page - 1) * $items_page . ', ' . ($items_page);
            $cm = new ContentManager();
            $videos = $cm->find_all('Video', 'available=1 AND `contents_categories`.`pk_fk_content_category` =' . $category . '', 'ORDER BY created DESC ' . $_limit);
            if (count($videos) > 0) {
                foreach ($videos as $video) {
                    //miramos el fuente youtube o vimeo
                    if ($video->author_name == 'vimeo') {
                        $url = "  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                        $curl = curl_init('http://vimeo.com/api/v2/video/' . $video->videoid . '.php');
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                        $return = curl_exec($curl);
                        $return = unserialize($return);
                        curl_close($curl);
                        $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                        $video->thumbnail_small = $return[0]['thumbnail_small'];
                    }
                    $video->category_name = $video->loadCategoryName($video->id);
                    $video->category_title = $video->loadCategoryTitle($video->id);
                }
            } else {
                $page = 1;
                Application::forward('/videos.php?action=videos_incategory&category=' . $category . '&page=1');
            }
            $tpl->assign('videos', $videos);
            $tpl->assign('page', $page);
            $tpl->assign('category', $category);
            $tpl->assign('total_incategory', '9');
            $html = $tpl->fetch('video/widget_video_incategory.tpl');
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
            $others_videos = $cm->find_all('Video', 'available=1 AND `contents_categories`.`pk_fk_content_category` <> ' . $category . '', 'ORDER BY created DESC ' . $_limit);
            if (count($others_videos) > 0) {
                foreach ($others_videos as $video) {
                    //miramos el fuente youtube o vimeo
                    if ($video->author_name == 'vimeo') {
                        $url = "  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                        $curl = curl_init('http://vimeo.com/api/v2/video/' . $video->videoid . '.php');
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                        $return = curl_exec($curl);
                        $return = unserialize($return);
                        curl_close($curl);
                        $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                        $video->thumbnail_small = $return[0]['thumbnail_small'];
                    }
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
            $html = $tpl->fetch('video/widget_video_more.tpl');
            echo $html;
            exit(0);
        break;
        default:
            Application::forward301('/');
        break;
    }
} else {
    Application::forward301('/');
}
require_once ("widget_static_pages.php");
require_once ('widget_videos_lastest.php');
/******************************************************************************************************/
// Render
$tpl->display('video/video.tpl');
