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

$menuFrontpage= Menu::renderMenu('frontpage');
$tpl->assign('menuFrontpage',$menuFrontpage->items);

/******************************  *********************************/

$action = filter_input(INPUT_POST,'action', FILTER_SANITIZE_STRING);
if(empty($action)) {
    $action = filter_input(INPUT_GET,'action', FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

switch($action) {
    case 'frontpage':

        $tpl->setConfig('letter-frontpage');

        $page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING,
								 array('options'=> array('default' => 0)));

        $cacheID = $tpl->generateCacheId('letter-frontpage', '', $page);

        /**
         * Don't execute action logic if was cached before
         */
        if (1==1 ||  ($tpl->caching == 0)
           || (!$tpl->isCached('letter/letter-frontpage.tpl',$cacheID))) {

                $otherLetters = $cm->find_all('Letter', 'available=1 ',
                                            'ORDER BY created DESC LIMIT 5');

            $tpl->assign( array('otherLetters'=> $otherLetters ) );

        }

        require_once('letter_advertisement.php');

        $tpl->display('letter/letter_frontpage.tpl', $cacheID);

    break;

    case 'show':

        $tpl->setConfig('letter-inner');

        $dirtyID = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
        if(empty($dirtyID)) {
            $dirtyID = filter_input(INPUT_POST,'id',FILTER_SANITIZE_STRING);
        }

        $slug = filter_input(INPUT_GET,'slug',FILTER_SANITIZE_STRING);

        $letterId = Content::resolveID($dirtyID);

        /**
         * Redirect to album frontpage if id_album wasn't provided
         */
        if (is_null($letterId)) { Application::forward301('/cartas-al-director/'); }

        $letter = new Letter($letterId);

        if (!empty($letter)) {

            if (($letter->available==1) && ($letter->in_litter==0)) {
                // Increment numviews if it's accesible
                $letter->setNumViews($letterId);

                //if($slug != $letter->slug) { }

                $cacheID = $tpl->generateCacheId('letter-inner','', $letterId );

                if (1==1 || ($tpl->caching == 0) || !$tpl->isCached('letter/letter.tpl', $cacheID) ) {

                    $comment = new Comment();
                    $comments = $comment->get_public_comments($letterId);

                    $otherLetters = $cm->find('Letter', 'available=1 ',
                                            'ORDER BY created DESC LIMIT 5');

                    $tpl->assign( array( 'letter'=>$letter,
                        'num_comments'=> count($comments),
                        'otherLetters'=>$otherLetters,
                        ) );


                } // end if $tpl->is_cached

                require_once('letter_inner_advertisement.php');

                $tpl->assign('contentId', $letterId); // Used on module_comments.tpl

                $tpl->display('letter/letter.tpl', $cacheID);

            }

            /************* COLUMN-LAST *******************************/

            require_once("widget_static_pages.php");
         } else {
            Application::forward301('/404.html');
        }

    break;

    case 'save_letter':

           if(isset($_POST['lettertext']) && !empty($_POST['lettertext'])) {
                if( isset($_POST['security_code']) && empty($_POST['security_code']) ) {

                    /*  Anonymous comment ************************* */
                    $data = array();
                    $data['body']     = $_POST['lettertext'];
                    $data['author']   = $_POST['name'];
                    $data['title']    = $_POST['subject'];
                    $data['email']    = $_POST['mail'];
                    $data['available'] = 0; //pendding

                    $letter = new Letter();
                    $msg =  $letter->saveLetter($data);
                }
           }else{
               $msg = 'Su Carta al Director <strong>no</strong> ha sido guardada.';
           }
           echo $msg;
           exit();

    default:
      //  Application::forward301('index.php');
    break;
}
