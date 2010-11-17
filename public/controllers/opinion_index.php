<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Redirect Mobile browsers to mobile site unless a cookie exists.
*/
$app->mobileRouter();

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');

/**
 * Fetch HTTP variables
*/
$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
$subcategory_name = filter_input(INPUT_GET,'subcategory_name',FILTER_SANITIZE_STRING);
$authorID = filter_input(INPUT_GET,'author_id',FILTER_SANITIZE_STRING);

/**
 * Redirect to home if category_name is not opinion
*/
if ($category_name !="opinion") { Application::forward('/home/'); }

/**
 * Set up Model
*/
$cm = new ContentManager();
$ccm = new ContentCategoryManager();

/**
 * Fetch information for some uncached parts of the view
*/
require_once ("opinion_index_advertisement.php");

/**
 * Generate the ID for use it to fetch caches
*/
$page = (!isset($_GET['pageop'])) ? $page = 0 : $page = $_GET['pageop']-1;
$cacheID = 'opinion|'.(($authorID != '') ? $authorID.'|' : '').$page;


if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
      	case 'list_opinions': //Listado de opiniones portada seccion opinion.

            /**
             * Don't execute the app logic if there are caches available
            */
            if (!$tpl->isCached('opinion/opinion_index.tpl', $cacheID)) {

                /**
                 * Fetch last opinions from editorial
                */
                $editorial = $cm->find('Opinion',
                                       'opinions.type_opinion=1 '.
                                       'AND contents.available=1 '.
                                       'AND contents.in_home=1 '.
                                       'AND contents.content_status=1 ',
                                       'ORDER BY position ASC, created DESC '.
                                       'LIMIT '.($page*2).',1');
                /**
                 * Fetch last opinions from director
                */
                $director = $cm->find('Opinion',
                                      'opinions.type_opinion=2 '.
                                      'AND contents.available=1 '.
                                      'AND contents.in_home=1 '.
                                      'AND contents.content_status=1 ',
                                      'ORDER BY created DESC LIMIT '.$page.',2');

                /**
                 * Fetch the photo images of the director
                */
                $aut = new Author($director[0]->fk_author);
                $foto = $aut->get_photo($director[0]->fk_author_img);
                if (isset($foto->path_img)){
                    $dir['photo'] = $foto->path_img;
                }
                $dir['name'] = $aut->name;
                $tpl->assign('dir', $dir);


                $_limit='LIMIT '.($page*ITEMS_PAGE).', '.(ITEMS_PAGE);
                $params='/seccion/opinion';
                /**
                 * Fetch last opinions of contributors and paginate them by ITEM_PAGE
                */
                $opinions = $cm->find_listAuthors('opinions.type_opinion=0 '.
                                              'AND contents.available=1 '.
                                              'AND contents.content_status=1',
                                              'ORDER BY in_home DESC, position ASC, created DESC '.$_limit);

                $total_opinions = $cm->cache->count('Opinion',
                                                'opinions.type_opinion=0 '.
                                                'AND contents.available=1  '.
                                                'AND contents.content_status=1');

                $paginacion =$cm->create_paginate($total_opinions, ITEMS_PAGE, 4, 'URL', $params);

                require_once ('widget_headlines_past.php');
                require_once ("index_sections.php");
                /**
                 * Fetch information for Static Pages
                */
                require_once("widget_static_pages.php");

                $tpl->assign('editorial', $editorial);
                $tpl->assign('director',  $director[0]);
                $tpl->assign('opinions',  $opinions);
                $tpl->assign('paginate',  $paginacion);

            }

            $tpl->display('opinion/opinion_index.tpl');

        break;

        case 'list_op_author':  //Listado de Opiniones de un autor

            /**
             * Don't execute the app logic if there are caches available
            */

            if (!$tpl->isCached('opinion/opinion_author_index.tpl', $cacheID)) {

                $_limit=' LIMIT '.($page*ITEMS_PAGE).', '.(ITEMS_PAGE);

                /**
                 * Fetch editorial opinions
                */
                if ($authorID==1) { //Editorial

                    $opinions = $cm->find_listAuthorsEditorial('contents.available=1  AND contents.content_status=1', 'ORDER BY created DESC '.$_limit);
                    $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=1 and contents.available=1  and contents.content_status=1');
                    $name_author= 'Editorial';

                /**
                 * Fetch director opinions
                */
                } elseif ($authorID == 2) { //Director

                    $opinions = $cm->find_listAuthors('opinions.type_opinion=2 and contents.available=1 and contents.content_status=1', 'ORDER BY created DESC '.$_limit);
                    $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=2 and contents.available=1  and contents.content_status=1');
                    $name_author= 'Director';

                /**
                 * Fetch common author opinions
                */
                } else { //Author

                    //necesito saber el count para paginar si es necesario.
                    $total_opinions = $cm->count('Opinion','opinions.type_opinion=0 and opinions.fk_author='.($authorID).' and contents.available=1  and contents.content_status=1');
                    $opinions = $cm->find_listAuthors('opinions.type_opinion=0 and opinions.fk_author='.($authorID).' and contents.available=1  and contents.content_status=1','ORDER BY created DESC '.$_limit);
                    $aut = new Author($authorID);
                    $name_author= $aut->name;

                }

                /**
                 * If there aren't opinions just redirect to homepage opinion
                */
                if(empty($total_opinions)){ Application::forward301('/seccion/opinion/'); }

                $params='/opinions_autor/'.$authorID.'/'.$name_author;
                $pagination = $cm->create_paginate($total_opinions, ITEMS_PAGE, 2, 'URL', $params);

                require_once ('widget_headlines_past.php');
                require_once ("index_sections.php");
                /**
                 * Fetch information for Static Pages
                */
                require_once("widget_static_pages.php");

                $tpl->assign('author_name', $name_author);
                $tpl->assign('pagination_list', $pagination);
                $tpl->assign('opinions', $opinions);
                $tpl->assign('author_id', $authorID);

            } // End if isCached

            $tpl->display('opinion/opinion_author_index.tpl', $cacheID);
        break;
    }
}
