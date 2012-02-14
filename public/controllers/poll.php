<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s;
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);


/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

/**
 * Setting up available categories for menu.
*/

$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
if(empty($category_name)) {
    $category_name = filter_input(INPUT_POST,'category_name',FILTER_SANITIZE_STRING);
}

$menuFrontpage= Menu::renderMenu('encuesta');
$tpl->assign('menuFrontpage',$menuFrontpage->items);

if(!empty($category_name)) {
    $category = $ccm->get_id($category_name);
    $actual_category_id = $category; // FOR WIDGETS
    $category_real_name = $ccm->get_title($category_name); //used in title
    $tpl->assign(array( 'category_name' => $category_name ,
                        'category' => $category ,
                        'actual_category_id' => $actual_category_id ,
                        'category_real_name' => $category_real_name ,
                ) );
} else {
     $category_real_name = 'Portada';
     $tpl->assign(array(
                        'category_real_name' => $category_real_name ,
                ) );
}
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


   $pollSettings = s::get('poll_settings');

   $tpl->assign(array (
                        'settings' =>$pollSettings
                ) );

/**************************************  SECURITY  *******************************************/

$action = filter_input(INPUT_POST,'action', FILTER_SANITIZE_STRING);
if(empty($action)) {
    $action = filter_input(INPUT_GET,'action', FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}
$page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING,
								 array('options'=> array('default' => 0)));
switch($action) {
    case 'frontpage':

        $tpl->setConfig('poll-frontpage');

        $cacheID = $tpl->generateCacheId('poll'.$category_name, '', $page);

        /**
         * Don't execute action logic if was cached before
         */
        if ( ($tpl->caching == 0)
           || (!$tpl->isCached('poll/poll-frontpage.tpl',$cacheID))) {
            if (isset($category) && !empty($category)) {
                $polls = $cm->find_by_category('Poll', $category, 'available=1 ',
                                            'ORDER BY created DESC LIMIT 2');
                $otherPolls = $cm->find('Poll', 'available=1 ',
                                            'ORDER BY created DESC LIMIT 5');
            } else {
                 $polls = $cm->find('Poll', 'available=1 ',
                                            'ORDER BY created DESC LIMIT 2');
                 $otherPolls = $cm->find('Poll', 'available=1 ',
                                            'ORDER BY created DESC LIMIT 2,7');
            }

            if(!empty($polls)) {
                foreach($polls as &$poll) {
                    $poll->items = $poll->get_items($poll->id);
                    $poll->dirtyId = date('YmdHis', strtotime($poll->created)).sprintf('%06d',$poll->id);

                }
            }

            $tpl->assign( array('polls'=> $polls, 'otherPolls'=> $otherPolls ) );

        }

        require_once('poll_advertisement.php');

        $tpl->display('poll/poll_frontpage.tpl', $cacheID);

    break;

    case 'show':

        $tpl->setConfig('poll-inner');

        $dirtyID = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
        if(empty($dirtyID)) {
            $dirtyID = filter_input(INPUT_POST,'id',FILTER_SANITIZE_STRING);
        }

        $pollId = Content::resolveID($dirtyID);

        /**
         * Redirect to album frontpage if id_album wasn't provided
         */
        if (is_null($pollId)) { Application::forward301('/encuesta/'); }

        $poll = new Poll($pollId);

        if (!empty($poll)) {

            if (($poll->available==1) && ($poll->in_litter==0)) {
                // Increment numviews if it's accesible
                $poll->setNumViews($pollId);
                $items = $poll->get_items($pollId);
                $poll->dirtyId = $dirtyID;

                $cacheID = $tpl->generateCacheId($category_name,'', $pollId );

                if ( ($tpl->caching == 0) || !$tpl->isCached('poll/poll.tpl', $cacheID) ) {

                    $comment = new Comment();
                    $comments = $comment->get_public_comments($pollId);

                    $otherPolls = $cm->find('Poll', 'available=1 ',
                                            'ORDER BY created DESC LIMIT 5');

                    $tpl->assign( array( 'poll'=>$poll,
                        'items' => $items,
                        'num_comments'=> count($comments),
                        'otherPolls'=>$otherPolls,
                        ) );
                      $cookie="polls".$pollId;
                      $msg='';
                      if (isset($_COOKIE[$cookie])) {
                          if($_COOKIE[$cookie]=='tks') {
                               $msg = 'Ya ha votado esta encuesta';
                          } else {
                               $msg = 'Gracias, por su voto';
                               Application::setCookieSecure($cookie, 'tks');
                          }
                        }
                        $tpl->assign('msg',$msg);

                } // end if $tpl->is_cached

                require_once('poll_inner_advertisement.php');

                $tpl->assign('contentId', $pollId); // Used on module_comments.tpl

                $tpl->display('poll/poll.tpl', $cacheID);

            }

            /************* COLUMN-LAST *******************************/

            require_once("widget_static_pages.php");
         } else {
            Application::forward301('/404.html');
        }

    break;

    case 'addVote':

        $dirtyID = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
        if(empty($dirtyID)) {
            $dirtyID = filter_input(INPUT_POST,'id',FILTER_SANITIZE_STRING);
        }

        $pollId = Content::resolveID($dirtyID);
        /**
         * Redirect to album frontpage if id_album wasn't provided
         */
        if (is_null($pollId)) { Application::forward301('/encuesta/'); }

        $poll = new Poll($pollId);

        if(!empty($poll->id)) {
            $cookie="polls".$pollId;
            if (isset($_COOKIE[$cookie])) {
                 Application::setCookieSecure($cookie, 'tks');
            }
            if (isset($_POST['respEncuesta'])
                    && !empty($_POST['respEncuesta'])
                     && !isset($_COOKIE[$cookie]) ) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $poll->vote($_POST['respEncuesta'],$ip);
            }

            $cacheID= $tpl->generateCacheId($category_name, '',$pollId );
            $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);
            $tplManager->delete($cacheID, 'poll.tpl');
            $cacheID = $tpl->generateCacheId('poll'.$category_name, '', $page);
            $tplManager->delete($cacheID, 'poll_frontpage.tpl');

            Application::forward(SITE_URL.$poll->uri);
        }

    break;

    default:
      //  Application::forward301('index.php');
    break;
}
