<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Redirect Mobile browsers to mobile site unless a cookie exists.
*/
// $app->mobileRouter();

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');

/**
 * Fetch HTTP variables
*/

$category_name = $request->query->filter('category_name', 'opinion', FILTER_SANITIZE_STRING);

$action        = $request->query->filter('action', 'list' , FILTER_SANITIZE_STRING);
$page          = $request->query->filter('page', 1, FILTER_VALIDATE_INT);

$authorID      = (int)$request->query->filter('author_id', '' , FILTER_SANITIZE_STRING);

/**
 * Redirect to home if category_name is not opinion
*/
if ($category_name !="opinion") { Application::forward('/home/'); }

/**
 * Set up Model
*/
/**
 * Fetch information for some uncached parts of the view
*/
require_once ("opinion_index_advertisement.php");

/**
 * Generate the ID for use it to fetch caches
*/
$cacheID = 'opinion|'.(($authorID != '') ? $authorID.'|' : '').$page;
$tpl->assign('actual_category', 'opinion'); // Used in renderMenu

switch ($action) {
    case 'list_opinions': // Index frontpage

        // Don't execute the app logic if there are caches available
        if (!$tpl->isCached('opinion/opinion_index.tpl', $cacheID)) {

            $cm = new ContentManager();

            // Fetch last opinions from editorial
            $editorial = $cm->find(
                'Opinion',
                'opinions.type_opinion=1 '.
                'AND contents.available=1 '.
                'AND contents.in_home=1 '.
                'AND contents.content_status=1 ',
                'ORDER BY position ASC, created DESC '.
                'LIMIT 2'
            );

            // Fetch last opinions from director
            $director = $cm->find(
                'Opinion',
                'opinions.type_opinion=2 '.
                'AND contents.available=1 '.
                'AND contents.in_home=1 '.
                'AND contents.content_status=1 ',
                'ORDER BY created DESC LIMIT 2'
            );

            if (isset($director) && !empty($director)) {
                // Fetch the photo images of the director
                $aut = new Author($director[0]->fk_author);
                $foto = $aut->get_photo($director[0]->fk_author_img);
                if (isset($foto->path_img)){
                    $dir['photo'] = $foto->path_img;
                }
                $dir['name'] = $aut->name;
                $tpl->assign('dir', $dir);
                $tpl->assign('director',  $director[0]);
            }

            $_limit ='LIMIT '.(($page-1)*ITEMS_PAGE).', '.(($page)*ITEMS_PAGE);
            $url    ='opinion';

            $total_opinions = $cm->count(
                'Opinion','in_home=1 and available=1 and type_opinion=0',
                'ORDER BY type_opinion DESC, position ASC, created DESC '
            );

            // Fetch last opinions of contributors and paginate them by ITEM_PAGE
            $opinions = $cm->find(
                'Opinion',
                'in_home=1 and available=1 and type_opinion=0',
                'ORDER BY type_opinion DESC, position ASC, created DESC '.$_limit
            );

            foreach ($opinions as &$opinion) {
                $opinion->author           = new Author($opinion->fk_author);
                $opinion->author->photo    = $opinion->author->get_photo($opinion->fk_author_img);
                $opinion->name             = $opinion->author->name;
                $opinion->author_name_slug = StringUtils::get_title($opinion->name);
            }

            $pagination = $cm->create_paginate($total_opinions, 1, ITEMS_PAGE, 'URL', $url, '');


            $tpl->assign('editorial', $editorial);
            $tpl->assign('opinions',  $opinions);
            $tpl->assign('pagination',  $pagination);
            $tpl->assign('page', $page);

        }

        $tpl->display('opinion/opinion_frontpage.tpl', $cacheID);

    break;

    case 'list_op_author':  // Author frontpage

        // Don't execute the app logic if there are caches available
        if (!$tpl->isCached('opinion/frontpage_author.tpl', $cacheID)) {

            $_limit=' LIMIT '.(($page-1)*ITEMS_PAGE).', '.(ITEMS_PAGE);

            $cm = new ContentManager();
            // Fetch editorial opinions
            if ($authorID==1) { //Editorial

                $opinions = $cm->find_listAuthorsEditorial('contents.available=1  AND contents.content_status=1', 'ORDER BY created DESC '.$_limit);
                $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=1 and contents.available=1  and contents.content_status=1');
                $name_author= 'editorial';
                if (!empty($opinions)) {
                    foreach ($opinions as &$opinion) {
                        $opinion['pk_author'] = 1;
                        $opinion['author_name_slug']  = $name_author;
                    }
                }
            // Fetch director opinions
            } elseif ($authorID == 2) { //Director

                $opinions = $cm->find_listAuthors('opinions.type_opinion=2 and contents.available=1 and contents.content_status=1', 'ORDER BY created DESC '.$_limit);
                $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=2 and contents.available=1  and contents.content_status=1');
                $name_author = 'director';
                if (!empty($opinions)) {
                    foreach ($opinions as &$opinion) {
                        $opinion['pk_author'] = 2;
                        $opinion['author_name_slug']  = $name_author;
                    }
                }
            // Fetch common author opinions
            } else { //Author

                // First, I need to know the amount of opinions for if it is necessary to paginate.
                $total_opinions = $cm->count('Opinion',
                                                'opinions.type_opinion=0 and opinions.fk_author='.($authorID)
                                                .' AND contents.available=1 AND contents.content_status=1');
                $opinions = $cm->find_listAuthors('opinions.type_opinion=0 and opinions.fk_author='.($authorID)
                                                    .' and contents.available=1  and contents.content_status=1',
                                                    'ORDER BY created DESC '.$_limit);
                $aut = new Author($authorID);
                if (!empty($opinions)) {
                    foreach ($opinions as &$opinion) {
                        $opinion['author_name_slug']  = StringUtils::get_title($opinion['name']);
                    }
                }

                $tpl->assign('author_name', $aut->name);
            }

            // If there aren't opinions just redirect to homepage opinion
            if(empty($total_opinions)){ Application::forward301('/seccion/opinion/'); }

            $url = Uri::generate(
                'opinion_author_frontpage',
                array(
                    'slug' => $opinions[0]['author_name_slug'],
                    'id' => $opinions[0]['pk_author']
                    )
            );

            $pagination = $cm->create_paginate($total_opinions, ITEMS_PAGE, 2, 'URL', $url);

            $tpl->assign('pagination_list', $pagination);
            $tpl->assign('opinions', $opinions);
            $tpl->assign('author_id', $authorID);
            $tpl->assign('page', $page);

        } // End if isCached

        $tpl->display('opinion/opinion_author_index.tpl', $cacheID);
    break;

}
