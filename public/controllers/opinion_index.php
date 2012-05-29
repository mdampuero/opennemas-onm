<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
// Start up and setup the app
require_once '../bootstrap.php';

// Redirect Mobile browsers to mobile site unless a cookie exists.
// $app->mobileRouter();

//Setup view
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');

// HTTP variables
$action   = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);
$authorID = (int) $request->query->filter('author_id', null, FILTER_VALIDATE_INT);
$page     = $request->query->filter('page', 1, FILTER_VALIDATE_INT);

$tpl->assign('actual_category', 'opinion'); // Used in renderMenu

// Fetch information for some uncached parts of the view
require_once "opinion_index_advertisement.php";

// Generate the ID for use it to fetch caches
$cacheID = 'opinion|'.(($authorID != '') ? $authorID.'|' : '').$page;

switch ($action) {

    // Index frontpage
    case 'list_opinions':
        // Don't execute the app logic if there are caches available
        if (!$tpl->isCached('opinion/opinion_index.tpl', $cacheID)) {

            $cm = new ContentManager();

            // Fetch last opinions from editorial
            $editorial = $cm->find('Opinion',
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
                if (isset($foto->path_img)) {
                    $dir['photo'] = $foto->path_img;
                }
                $dir['name'] = $aut->name;
                $tpl->assign('dir', $dir);
                $tpl->assign('director', $director[0]);
            }

            $_limit ='LIMIT '.(($page-1)*ITEMS_PAGE).', '.(($page)*ITEMS_PAGE);
            $url    ='opinion';

            $total_opinions = $cm->count(
                'Opinion',
                'in_home=1 and available=1 and type_opinion=0',
                'ORDER BY type_opinion DESC, position ASC, created DESC '
            );

            // Fetch last opinions of contributors and
            // paginate them by ITEM_PAGE
            $opinions = $cm->find(
                'Opinion',
                'in_home=1 and available=1 and type_opinion=0',
                'ORDER BY type_opinion DESC, position ASC, created DESC '.$_limit
            );

            foreach ($opinions as &$opinion) {
                $opinion->author           = new Author($opinion->fk_author);
                $opinion->author->photo    =
                    $opinion->author->get_photo($opinion->fk_author_img);
                $opinion->name             = $opinion->author->name;
                $opinion->author_name_slug = StringUtils::get_title($opinion->name);
            }

            $pagination = $cm->create_paginate(
                $total_opinions,
                ITEMS_PAGE, 2, 'URL', $url, ''
            );

            $tpl->assign('editorial', $editorial);
            $tpl->assign('opinions', $opinions);
            $tpl->assign('pagination', $pagination);
            $tpl->assign('page', $page);
        }

        $tpl->display('opinion/opinion_frontpage.tpl', $cacheID);
        break;

    case 'list_op_author':  // Author frontpage

        // Don't execute the app logic if there are caches available
        if (!$tpl->isCached('opinion/frontpage_author.tpl', $cacheID)) {

            $cm = new ContentManager();

            // Get author info
            $author = new Author($authorID);

            // Setting filters for the further SQLs
            if ($authorID == 1) {
                // Editorial
                $filter = 'opinions.type_opinion=1';
                $authorName = 'editorial';
            } elseif ($authorID == 2) {
                // Director
                $filter =  'opinions.type_opinion=2';
                $authorName = 'director';
            } else {
                // Regular authors
                $filter = 'opinions.type_opinion=0 AND opinions.fk_author='.$authorID;
                $authorName = StringUtils::get_title($author->name);
            }

            $_limit=' LIMIT '.(($page-1)*ITEMS_PAGE).', '.(ITEMS_PAGE);

            // Get the number of total opinions for this
            // author for pagination purpouses
            $countOpinions = $cm->cache->count(
                'Opinion',
                $filter
                .' AND contents.available=1  and contents.content_status=1 '
            );

            // Get the list articles for this author
            $opinions = $cm->getOpinionArticlesWithAuthorInfo(
                $filter
                .' AND contents.available=1 and contents.content_status=1',
                'ORDER BY created DESC '.$_limit
            );

            if (!empty($opinions)) {
                foreach ($opinions as &$opinion) {
                    $opinion['pk_author'] = $authorID;
                    $opinion['author_name_slug']  = $authorName;
                }
            }

            // If there aren't opinions just redirect to homepage opinion
            if (empty($countOpinions)) {
                Application::forward301('/seccion/opinion/');
            }

            $url = Uri::generate(
                'opinion_author_frontpage',
                array(
                    'slug' => $opinions[0]['author_name_slug'],
                    'id' => $opinions[0]['pk_author']
                )
            );

            $pagination = $cm->create_paginate(
                $countOpinions, ITEMS_PAGE, 2, 'URL', $url, ''
            );

            // Clean weird variables from this assign (must check
            // all the templates)
            // pagination_list cahnge to pagination
            // drop author_id, $author_name as they are inside author var
            $tpl->assign(array(
                'pagination_list' => $pagination,
                'opinions'        => $opinions,
                'author_id'       => $authorID,
                'author'          => $author,
                'author_name'     => $author->name,
                'page'            => $page,
            ));

        } // End if isCached

        $tpl->display('opinion/opinion_author_index.tpl', $cacheID);
        break;
}
