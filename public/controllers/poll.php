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

//TODO: define dir to save xml and charts.swf dir
//TODO: widget others polls

$poll_path = MEDIA_PATH . DIRECTORY_SEPARATOR . MEDIA_DIR . DIRECTORY_SEPARATOR . POLL_DIR . DIRECTORY_SEPARATOR ;

$tpl->assign(array ('chartPolls'  => MEDIA_URL.SS.INTERNAL_DIR ,
                    'xmlDirPolls' => MEDIA_URL.SS.MEDIA_DIR.SS.POLL_DIR.SS,
                    'poll_path'   => $poll_path,
            ) );


   $pollSettings = s::get('poll_settings');
  
   $tpl->assign(array (
                        'settings' =>$pollSettings
                ) );
        
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

        $cacheID = $tpl->generateCacheId('poll-frontpage',$category_name, $page);

        /**
         * Don't execute action logic if was cached before
         */
        if ( ($tpl->caching == 0)
           && (!$tpl->isCached('poll/poll-frontpage.tpl',$cacheID))) {
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
            $tpl->assign( array('polls'=> $polls, 'otherPolls'=> $otherPolls ) );
               
        }
        
        require_once('poll_advertisement.php');
        
        $tpl->display('poll/poll_frontpage.tpl', $cacheID);

    break;

    case 'show':

        $tpl->setConfig('poll-inner');
 
        $poll_id = filter_input(INPUT_GET,'id');
        if (empty($poll_id)) {
            $poll_id = filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT, array('options' => array('default' => 0 )));
        }

        $poll = new Poll($poll_id);
 
        if (!empty($poll)) {

            if (($poll->available==1) && ($poll->in_litter==0)) {
                // Increment numviews if it's accesible
                $poll->setNumViews($poll_id);

                $cacheID = $tpl->generateCacheId($category_name, $poll_id );

                if( 1 || ($tpl->caching == 0) || !$tpl->is_cached('poll/poll.tpl', $cacheID) ) {

                    $items = $poll->get_items($poll_id);
                   
 
                    $comment = new Comment();
                    $comments = $comment->get_public_comments($poll_id);

                    $otherPolls = $cm->find('Poll', 'available=1 ',
                                            'ORDER BY created DESC LIMIT 5');
 
                    $tpl->assign( array( 'poll'=>$poll,
                        'items' => $items,
                        'num_comments'=> count($comments),
                        'otherPolls'=>$otherPolls,
                        ) );
 
                    //TODO save name in db
                    if ($poll->visualization == '0') { // pie
                         $tpl->assign('type_poll','pie');
                    } else {
                         $tpl->assign('type_poll','bars');
                    }
 
                    $xml = $tpl->fetch('poll/graphic_poll.tpl');

                    $file =  $poll_path. $poll_id.'.xml';
                    FilesManager::mkFile($file);

                    FilesManager::writeInFile($file, $xml);
                

                } // end if $tpl->is_cached

                  require_once('poll_inner_advertisement.php');

                $tpl->assign('contentId', $poll_id); // Used on module_comments.tpl

                $tpl->display('poll/poll.tpl', $cacheID);

            }

            /************* COLUMN-LAST *******************************/
          
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
            if (isset($_POST['respEncuesta'])
                    && !empty($_POST['respEncuesta'])
                     && !isset($_COOKIE[$cookie]) ) {
                $ip = $_SERVER['REMOTE_ADDR'];
               // $poll=new Poll($_REQUEST['id']);
                $poll->vote($_POST['respEncuesta'],$ip);
                $tpl->assign('msg','Gracias por su voto ');
            }
            $items = $poll->get_items($poll_id);
            

            $otherPolls = $cm->find('Poll', 'available=1 ',
                                    'ORDER BY created DESC LIMIT 5');

            $tpl->assign( array( 'poll'=>$poll,
                'items' => $items,                 
                'otherPolls'=>$otherPolls,
                ) );

            //TODO save name in db
            if ($poll->visualization == '0') { // pie
                 $tpl->assign('type_poll','pie');
            } else {
                 $tpl->assign('type_poll','bars');
            }

            $xml = $tpl->fetch('poll/graphic_poll.tpl');

            $file =  $poll_path.$poll_id.'.xml';
            FilesManager::mkFile($file);
            FilesManager::writeInFile($file, $xml);

            $comment = new Comment();
            $comments = $comment->get_public_comments($poll_id);
            $tpl->assign('num_comments', count($comments));

            $tpl->assign('contentId', $poll_id); // Used on module_comments.tpl

            $poll->setNumViews($poll_id);

            $cacheID= $tpl->generateCacheId($category_name, $poll_id );

            require_once('poll_inner_advertisement.php');
              
            $tpl->display('poll/poll.tpl', $cacheID);
        }
       
    break;

    default:
      //  Application::forward301('index.php');
    break;
}
