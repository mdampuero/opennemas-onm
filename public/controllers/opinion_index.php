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
$action     = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);
$authorID   = (int) $request->query->filter('author_id', null, FILTER_VALIDATE_INT);
$authorSlug = $request->query->filter('author_slug', null, FILTER_SANITIZE_STRING);
$page       = $request->query->filter('page', 1, FILTER_VALIDATE_INT);

$tpl->assign('actual_category', 'opinion'); // Used in renderMenu

// Fetch information for some uncached parts of the view
require_once 'opinion_index_advertisement.php';

// Generate the ID for use it to fetch caches
$cacheID = 'opinion|'.(($authorID != '') ? $authorID.'|' : '').$page;

switch ($action) {
    // Index frontpage
    case 'list_opinions':
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
                if (isset($foto->path_img)) {
                    $dir['photo'] = $foto->path_img;
                }
                $dir['name'] = $aut->name;
                $tpl->assign('dir', $dir);
                $tpl->assign('director', $director[0]);
            }

            if ($page == 1) {
                $opinions = $cm->find(
                    'Opinion',
                    'in_home=1 and available=1 and type_opinion=0',
                    'ORDER BY position ASC, starttime DESC '
                );
                $totalHome = count($opinions);

            } else {
                $_limit ='LIMIT '.(($page-2)*ITEMS_PAGE).', '.(($page-1)*ITEMS_PAGE);
                // Fetch last opinions of contributors and
                // paginate them by ITEM_PAGE
                $opinions = $cm->find(
                    'Opinion',
                    'in_home=0 and available=1 and type_opinion=0',
                    'ORDER BY starttime DESC '.$_limit
                );
            }
            // Added ITEMS_PAGE for count first page
            $total_opinions =  ITEMS_PAGE + $cm->count(
                'Opinion',
                'in_home=0 and available=1 and type_opinion=0',
                'ORDER BY type_opinion DESC, created DESC '
            );

            $authors = array();
            foreach ($opinions as &$opinion) {
                if (!array_key_exists($opinion->fk_author, $authors)) {
                    $author = new Author($opinion->fk_author);
                    $author->get_author_photos();
                    $authors[$opinion->fk_author] = $author;
                }
                $opinion->author           = $authors[$opinion->fk_author];
                $opinion->name             = $opinion->author->name;
                $opinion->author_name_slug = StringUtils::get_title($opinion->name);
                $opinion->author->uri = Uri::generate(
                    'opinion_author_frontpage',
                    array(
                        'slug' => $opinion->author->name,
                        'id' => $opinion->author->pk_author
                    )
                );
            }

            $url    ='opinion';
            $pagination = $cm->create_paginate(
                $total_opinions,
                ITEMS_PAGE,
                2,
                'URL',
                $url,
                ''
            );

            $tpl->assign('editorial', $editorial);
            $tpl->assign('opinions', $opinions);
            $tpl->assign('authors', $authors);
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
            $author->get_author_photos();
            $photos = $author->get_author_photos();

            // Setting filters for the further SQLs
            if ($authorID == 1 && strtolower($authorSlug) == 'editorial') {
                // Editorial
                $filter = 'opinions.type_opinion=1';
                $authorName = 'editorial';
            } elseif ($authorID == 2 && strtolower($authorSlug) == 'director') {
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
                    $opinion['uri'] = Uri::generate(
                        'opinion',
                        array(
                            'id'       => $opinion['id'],
                            'date'     => date('YmdHis', strtotime($opinion['created'])),
                            'category' => $opinion['author_name_slug'],
                            'slug'     => $opinion['slug'],
                        )
                    );

                    $opinion['author_uri'] = Uri::generate(
                        'opinion_author_frontpage',
                        array(
                            'slug' => $opinion['author_name_slug'],
                            'id' => $opinion['pk_author']
                        )
                    );
                }
            }

            $url = Uri::generate(
                'opinion_author_frontpage',
                array(
                    'slug' => $authorName,
                    'id' => $author->pk_author
                )
            );

            $pagination = $cm->create_paginate(
                $countOpinions,
                ITEMS_PAGE,
                2,
                'URL',
                $url,
                ''
            );

            // Clean weird variables from this assign (must check
            // all the templates)
            // pagination_list cahnge to pagination
            // drop author_id, $author_name as they are inside author var
            $tpl->assign(
                array(
                    'pagination_list' => $pagination,
                    'opinions'        => $opinions,
                    'author_id'       => $authorID,
                    'author_slug'     => strtolower($authorSlug),
                    'author'          => $author,
                    'author_name'     => $author->name,
                    'page'            => $page,
                )
            );

        } // End if isCached

        $tpl->display('opinion/opinion_author_index.tpl', $cacheID);
        break;
}

