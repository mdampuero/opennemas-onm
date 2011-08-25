<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);

$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

/**
 * Setting up available categories for menu.
*/

$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
if(empty($category)) {
    $category_name = filter_input(INPUT_POST,'category_name',FILTER_SANITIZE_STRING);
}
if(empty($category_name)) {
    $contentType = Content::getIDContentType('poll');
    //Get first category
    list($parentCategories, $subcat, $categoryData) = $ccm->getArraysMenu(0, $contentType);
    $category_name = $categoryData[0]->name;
    $category = $categoryData[0]->pk_content_category;
}
$actual_category = $category_name;


$tpl->assign(array( 'category'=>$category ,
                    'category_name'=>$category_name , ) );

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


//TODO: define dir to save xml
$tpl->assign('xmlDirPolls', MEDIA_URL.SS.MEDIA_DIR.'/polls/');

/**************************************  SECURITY  *******************************************/

$action = filter_input(INPUT_POST,'action', FILTER_SANITIZE_STRING);
if(empty($action)) {
    $action = filter_input(INPUT_GET,'action', FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

switch($action) {
    case 'frontpage':

        $tpl->setConfig('poll-frontpage');

        $page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING,
								 array('options'=> array('default' => 0)));

        $cacheID = $tpl->generateCacheId('poll-frontpage', $page);

        /**
         * Don't execute action logic if was cached before
         */
        if ( ($tpl->caching == 0)
           && (!$tpl->isCached('poll/poll-frontpage.tpl',$cacheID))){

                $polls = $cm->find_by_category('Poll',$category, 'available=1  ',
                                            'ORDER BY created DESC LIMIT 0,7');
           
             $tpl->assign('polls', $polls);

 
        }
 
        $tpl->display('poll/poll_frontpage.tpl', $cacheID);

    break;

    case 'show':

        $tpl->setConfig('poll-inner');
 
        $poll_id = filter_input(INPUT_GET,'id');
        if (empty($poll_id)) {
            $category = filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT, array('options' => array('default' => 0 )));
        }

        $poll = new Poll($poll_id);
 
        if(!empty($poll)){

            if(($poll->available==1) && ($poll->in_litter==0)) {
                // Increment numviews if it's accesible
                $poll->setNumViews($poll_id);

                $cacheID= $tpl->generateCacheId($category_name, $poll_id );

                if( ($tpl->caching == 0) || !$tpl->is_cached('poll/poll.tpl', $cacheID) ) {

                    $items=$poll->get_items($poll_id);

                    $tpl->assign('items', $items);

                    $comment = new Comment();
                    $comments = $comment->get_public_comments($poll_id);

                    $tpl->assign('num_comments', count($comments));

                    $tpl->assign('poll', $poll);
 
                    //TODO save name in db
                    if ($poll->visualization == '0') { // pie
                         $tpl->assign('type_poll','pie');
                    } else {
                         $tpl->assign('type_poll','bars');
                    }
 
                    $xml = $tpl->fetch('poll/graphic_poll.tpl');

                    $file =  MEDIA_PATH.'/polls/'.$poll_id.'.xml';
                    FilesManager::mkFile($file);

                    FilesManager::writeInFile($file, $xml);
                

                } // end if $tpl->is_cached


                $tpl->assign('contentId', $poll_id); // Used on module_comments.tpl

                $tpl->display('poll/poll.tpl', $cacheID);

            }

            /************* COLUMN-LAST *******************************/
            require_once('index_advertisement.php');

            require_once("widget_static_pages.php");
         } else {
            Application::forward301('/404.html');
        }


    break;

    case 'addVote':
 
        $poll_id = filter_input(INPUT_GET,'id');
        if(empty($poll_id)) {
            $poll_id = filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT, array('options' => array('default' => 0 )));
        }

        $poll = new Poll($poll_id);

        if(!empty($poll->id)){

            $cookie="polls".$poll_id;
            if (isset($_COOKIE[$cookie])) {
                $tpl->assign('msg','Ya ha votado esta encuesta');
            }
            if(isset($_POST['respEncuesta']) && !empty($_POST['respEncuesta'])  && !isset($_COOKIE[$cookie]) ){
                $ip = $_SERVER['REMOTE_ADDR'];
               // $poll=new Poll($_REQUEST['id']);
                $poll->vote($_POST['respEncuesta'],$ip);
                $tpl->assign('msg','Gracias por su voto ');
            }
            $items = $poll->get_items($poll_id);
            $tpl->assign('items', $items);

            $tpl->assign('poll', $poll);

            //TODO save name in db
            if ($poll->visualization == '0') { // pie
                 $tpl->assign('type_poll','pie');
            } else {
                 $tpl->assign('type_poll','bars');
            }

            $xml = $tpl->fetch('poll/graphic_poll.tpl');

            $file =  MEDIA_PATH.'/polls/'.$poll_id.'.xml';
            FilesManager::mkFile($file);
            FilesManager::writeInFile($file, $xml);

            $comment = new Comment();
            $comments = $comment->get_public_comments($poll_id);
            $tpl->assign('num_comments', count($comments));

            $tpl->assign('contentId', $poll_id); // Used on module_comments.tpl

            $poll->setNumViews($poll_id);

            $cacheID= $tpl->generateCacheId($category_name, $poll_id );


            $tpl->display('poll/poll.tpl', $cacheID);
        }
       
    break;

    default:
      //  Application::forward301('index.php');
    break;
}
