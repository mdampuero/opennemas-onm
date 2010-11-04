<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

// redirect to /mobile/ if it's mobile device request
$app->mobileRouter();

$tpl = new Template(TEMPLATE_USER);

$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

/**************************************  SECURITY  *******************************************/
//Is category initialized redirect the user to /
/* $category_name = $_GET['category_name'];
$subcategory_name = $_GET['subcategory_name']; */


if( isset($_REQUEST['op']) ) {
    switch($_REQUEST['op']) {
        case 'votar':
     
        
            $_REQUEST['poll_id'] = $_POST['id'];
     
            $poll = new Poll($_REQUEST['poll_id']);
            
            if(!empty($poll)){
                $cookie="polls".$_REQUEST['poll_id'];
                if (isset($_COOKIE[$cookie])){
                    $_REQUEST['op']='votar';
                    $tpl->assign('msg','ya ha votado');
                }
                if($_POST['respEncuesta'] /* && !isset($_COOKIE[$cookie]) */){
                    $ip = $_SERVER['REMOTE_ADDR'];
                   // $poll=new Poll($_REQUEST['id']);
                    $poll->vote($_POST['respEncuesta'],$ip);
                    $tpl->assign('msg','Gracias por su voto'.$_COOKIE[$cookie]);
                }
            }else{
                
                $_REQUEST['poll_id'] = '';
            }
            $tpl->assign('op', 'votar'); //conecta_CZonaEncuesta.tpl
        break;

        default:
            $tpl->assign('op', 'visulizar'); //conecta_CZonaVisionadoMedia.tpl
        break;
    }
}

if($_REQUEST['action']=='vote' ||  $_REQUEST['action']=='rating' ) {
    $category_name = 'home';
    $subcategory_name = null;
// If $action == 'rss' desnormalize process
}else{
    if($_REQUEST['action']=='rss' ) {
        $category_name = $_REQUEST['category_name'];
        $subcategory_name = $_REQUEST['subcategory_name'];
    } else {
        if(!empty($_REQUEST['poll_id'])){
            $poll = new Poll($_REQUEST['poll_id']);
         
            $poll->category_name = $poll->loadCategoryName($_REQUEST['poll_id']);

            $category_name = $poll->category_name;
            $subcategory_name = null;
            $category = $ccm->get_id($category_name);
            $polls = $cm->find_by_category('Poll',$category, 'available=1 AND pk_content != '.$_REQUEST['poll_id'].' ', 'ORDER BY created DESC LIMIT 0,7');
             
        }else{
            $category_name = $_GET['category_name'];
        }
    }

    if(empty($category_name)){
        $category_name =$_REQUEST['category_name'];
    }
    if(empty($subcategory_name) && isset($_REQUEST['subcategory_name'])){
        $subcategory_name = $_REQUEST['subcategory_name'];
    } else {
        $subcategory_name = '';
    }
    // Normalizar os nomes
    list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);
    $_GET['category_name'] = $category_name;
    $_GET['subcategory_name'] = $subcategory_name;

    $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
    $section = (is_null($section))? 'home': $section;


    if (isset($category_name) && !empty($category_name)) {

        if (!$ccm->exists($category_name)) {
            Application::forward301('/');
        } else {
            $category = $ccm->get_id($category_name);
        }

        if ( isset($subcategory_name)
           && !empty($subcategory_name))
        {
            if (!$ccm->exists($subcategory_name)) {
                Application::forward301('/');
            } else {
                $subcategory = $ccm->get_id($subcategory_name);
            }
        }
    } elseif(isset($_REQUEST["action"]) && ($_REQUEST["action"]=="rss")) {
        $_GET['category_name'] = $category_name = 'home';
    } elseif( isset($_REQUEST["action"])
             && ($_REQUEST["action"]!="rating" && $_REQUEST["action"]!="vote"
             && $_REQUEST["action"]!="rss" && $_REQUEST["action"]!="get_plus"))
    {
        Application::forward301('/');
    }
     if(!isset($_REQUEST['poll_id']) || empty($_REQUEST['poll_id'])){

         $polls = $cm->find_by_category('Poll',$category, 'available=1  ',
                                        'ORDER BY created DESC LIMIT 0,7');
         if(empty($polls)){Application::forward301('/');}
         $poll=array_shift($polls);

         $poll->category_name = $poll->loadCategoryName($_REQUEST['poll_id']);

         $category_name = $poll->category_name;
         $subcategory_name = null;
         $_REQUEST['poll_id'] =$poll->pk_poll;
         $_REQUEST["action"] = 'read';
        
     }
     
}
$tpl->assign('polls',$polls);
 
 
/**************************************  SECURITY  *******************************************/


if(isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {        
        case 'read':             
      
              // Load config
            $tpl->setConfig('polls');

             /******************************  BREADCRUB *********************************/

            $str = new String_Utils();
            $title = $str->get_title($poll->title);

            // print URL
            $print_url = '/imprimir/' . $title. '/' . $category_name . '/';

            $breadcrub   = array();
            $breadcrub[] = array('text' => $ccm->get_title($category_name),
                                 'link' => '/seccion/' . $category_name . '/' );
            if(!empty($subcategory_name)) {
                $breadcrub[] = array(
                    'text' => $ccm->get_title($subcategory_name),
                    'link' => '/seccion/' . $category_name . '/' . $subcategory_name . '/'
                );

                $print_url .= $subcategory_name . '/';
            }

            $print_url .= $poll->pk_content . '.html';
            $tpl->assign('print_url', $print_url);

            // Check if $section is "in menu" then show breadcrub
            $cat = $ccm->getByName($section);
            if(!is_null($cat) && $cat->inmenu) {
                $tpl->assign('breadcrub', $breadcrub);
            }

            /******************************  CATEGORIES & SUBCATEGORIES  *********************************/
            require_once ("index_sections.php");
            /******************************  CATEGORIES & SUBCATEGORIES  *********************************/

            $tpl->assign('category_name', $_GET['category_name']);

            $cm = new ContentManager();

            if(($poll->available==1) && ($poll->in_litter==0)) {

                $items=$poll->get_items($_REQUEST['poll_id']);
                $tpl->assign('items', $items);
                 $data_rows = array();
                 $max_value = 0;
                 
                 if(!empty($items)){
                    foreach($items as $item){
                        $data_rows[] = "['".$item['item']."',".$item['votes']."]";
                        if($max_value < $item['votes']) {$max_value = $item['votes'];}
                    }

                    $data_rows = '['.implode(', ',$data_rows).']';
                 }

                 $tpl->assign('max_value', $max_value);
                 $tpl->assign('data_rows', $data_rows);


                // Increment numviews if it's accesible
                $poll->setNumViews($_GET['poll_id']);
                if(isset($subcategory_name) && !empty($category_name)){
                    $actual_category = $subcategory_name;
                }else{
                    $actual_category =$category_name;
                }
                $actual_category_id = $ccm->get_id($actual_category);
                $actual_category_title = $ccm->get_title($actual_category);
                $tpl->assign('actual_category_title',$actual_category_title);


                $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $_GET['poll_id']);

                // MUTEXT CODE, DON'T use for performance
                // Application::getMutex($cache_id);

                if(  ($tpl->caching == 0) || !$tpl->is_cached('poll/poll.tpl', $cache_id) ) {

                   



                    $video = $cm->find_by_category_name('Video',  $actual_category, 'contents.content_status=1', 'ORDER BY created DESC LIMIT 0 , 1');
                    if(isset($video[0])){
                        $tpl->assign('videoInt', $video[0]);
                    }
                    /**************** PHOTOs ****************/

                    /******* RELATED  CONTENT *******/

                    $comment = new Comment();
                    $comments = $comment->get_public_comments($_REQUEST['poll_id']);

                    $tpl->assign('num_comments', count($comments));
                 
                } // end if $tpl->is_cached
                $tpl->assign('poll', $poll);
                // END MUTEXT
                // Application::releaseMutex();

                /************* COLUMN-LAST *******************************/

                $other_news = $cm->find_by_category_name('Article', $actual_category, 'contents.frontpage=1 AND contents.content_status=1 AND contents.available=1  AND contents.fk_content_type=1  AND contents.pk_content != '.$_REQUEST['article_id'].'', 'ORDER BY views DESC, placeholder ASC, position ASC, created DESC LIMIT 1,3');

                $tpl->assign('other_news', $other_news);

                require_once('widget_headlines_past.php');
               // require_once('widget_media.php');


                 /************* END COLUMN-LAST *******************************/

                // Advertisements for single article NO CACHE
                require_once('article_advertisement.php');

                 require_once("widget_static_pages.php");
             } else {
                Application::forward301('/404.html');
            }

 
        break;
        
        default: {
          //  Application::forward301('index.php');
        } break;
    }
    
} else {
    Application::forward301('index.php');
}

$tpl->display('poll/poll.tpl', $cache_id);

