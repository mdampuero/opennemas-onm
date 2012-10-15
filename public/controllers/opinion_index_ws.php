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
use Onm\Settings as s;

// Redirect Mobile browsers to mobile site unless a cookie exists.
// $app->mobileRouter();

//Setup view
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');
$cm = new ContentManager();

// HTTP variables
$action     = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);
$authorID   = (int) $request->query->filter('author_id', null, FILTER_VALIDATE_INT);
$authorSlug = $request->query->filter('author_slug', null, FILTER_SANITIZE_STRING);
$page       = $request->query->filter('page', 1, FILTER_VALIDATE_INT);

$tpl->assign('actual_category', 'opinion'); // Used in renderMenu

/**
 * Getting Synchronize setting params
 **/
$wsUrl = '';
$syncParams = s::get('sync_params');

foreach ($syncParams as $siteUrl => $categoriesToSync) {
    foreach ($categoriesToSync as $value) {
        if (preg_match('/opinion/i', $value)) {
            $wsUrl = $siteUrl;
        }
    }
}
// Get external media url for author images
$externalMediaUrl = $cm->getUrlContent($wsUrl.'/ws/instances/mediaurl/', true);

// Fetch information for some uncached parts of the view
require_once 'opinion_index_advertisement.php';

// Generate the ID for use it to fetch caches
$cacheID = 'syncopinion|'.(($authorID != '') ? $authorID.'|' : '').$page;

switch ($action) {
    // Index frontpage
    case 'list_opinions':
        // Don't execute the app logic if there are caches available
        if (!$tpl->isCached('opinion/opinion_index.tpl', $cacheID)) {


            $editorial = $cm->getUrlContent($wsUrl.'/ws/opinions/editorialinhome/', true);

            $director = $cm->getUrlContent($wsUrl.'/ws/opinions/directorinhome/', true);

            // Some director logic
            if (isset($director) && !empty($director)) {
                // Fetch the photo images of the director
                $aut = $cm->getUrlContent($wsUrl.'/ws/authors/id/'.$director[0]->fk_author, true);
                $foto = $cm->getUrlContent(
                    $wsUrl.'/ws/authors/photo/'.$director[0]->fk_author,
                    true
                );

                if (isset($foto->path_img)) {
                    $dir['photo'] = $foto->path_img;
                }

                $dir['name'] = $aut->name;
                $tpl->assign('dir', $dir);
                $tpl->assign('director', $director[0]);
            }

            if ($page == 1) {
                $opinions = $cm->getUrlContent($wsUrl.'/ws/opinions/authorsinhome/', true);
                $totalHome = count($opinions);
            } else {
                // Fetch last opinions of contributors and paginate them by ITEM_PAGE
                $opinions = $cm->getUrlContent($wsUrl.'/ws/opinions/authorsnotinhomepaged/'.$page, true);
            }

            // Sum of total opinions in home + not in home for the pager
            $totalOpinions =  ITEMS_PAGE + (int)$cm->getUrlContent(
                $wsUrl.'/ws/opinions/countauthorsnotinhome/',
                true
            );

            $authors = array();
            foreach ($opinions as &$opinion) {
                if (!array_key_exists($opinion->fk_author, $authors)) {
                    $author = $cm->getUrlContent($wsUrl.'/ws/authors/id/'.$opinion->fk_author, true);
                    $authors[$opinion->fk_author] = $author;
                }
                $opinion->author           = $authors[$opinion->fk_author];
                $opinion->name             = $opinion->author->name;
                $opinion->author_name_slug = StringUtils::get_title($opinion->name);
                $opinion->author->uri = 'ext'.Uri::generate(
                    'opinion_author_frontpage',
                    array(
                        'slug' => $opinion->author->name,
                        'id' => $opinion->author->pk_author
                    )
                );
            }

            $url    ='extopinion';
            $pagination = $cm->create_paginate(
                $totalOpinions,
                ITEMS_PAGE,
                2,
                'URL',
                $url,
                ''
            );

            $tpl->assign(
                array(
                    'editorial'  => $editorial,
                    'opinions'   => $opinions,
                    'authors'    => $authors,
                    'pagination' => $pagination,
                    'page'       => $page,
                    'ext'        => $externalMediaUrl,
                )
            );
        }

        $tpl->display('opinion/opinion_frontpage.tpl', $cacheID);

        break;
    case 'list_op_author':  // Author frontpage
        // Don't execute the app logic if there are caches available
        if (!$tpl->isCached('opinion/frontpage_author.tpl', $cacheID)) {

            // Get author info
            $author = $cm->getUrlContent($wsUrl.'/ws/authors/id/'.$authorID, true);

            // Setting filters for the further SQLs
            if ($authorID == 1 && strtolower($authorSlug) == 'editorial') {
                // Editorial
                $authorName = 'editorial';
                $countOpinions = $cm->getUrlContent(
                    $wsUrl.'/ws/opinions/counteditorialopinions/',
                    true
                );
                $opinions = $cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionseditorial/'.$page,
                    true
                );
            } elseif ($authorID == 2 && strtolower($authorSlug) == 'director') {
                // Director
                $authorName = 'director';
                $countOpinions = $cm->getUrlContent(
                    $wsUrl.'/ws/opinions/countdirectoropinions/',
                    true
                );
                $opinions = $cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionsdirector/'.$page,
                    true
                );
            } else {
                // Regular authors
                $authorName = StringUtils::get_title($author->name);
                $countOpinions = $cm->getUrlContent(
                    $wsUrl.'/ws/opinions/countauthoropinions/'.$authorID,
                    true
                );
                $opinions = $cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionsauthor/'.$page.'/'.$authorID,
                    true
                );
            }

            if (!empty($opinions)) {
                foreach ($opinions as &$opinion) {
                    $opinion->author_name_slug  = $authorName;
                    // Overload opinion uri on opinion Object
                    $opinion->uri = 'ext'.Uri::generate(
                        'opinion',
                        array(
                            'id'       => $opinion->id,
                            'date'     => date('YmdHis', strtotime($opinion->created)),
                            'category' => StringUtils::get_title($opinion->name),
                            'slug'     => $opinion->slug,
                        )
                    );
                    // Overload author uri on opinion Object
                    $opinion->author_uri = 'ext'.Uri::generate(
                        'opinion_author_frontpage',
                        array(
                            'slug' => StringUtils::get_title($opinion->name),
                            'id' => $opinion->pk_author
                        )
                    );
                    $opinion = (array)$opinion; // template dependency
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
                    'ext'             => $externalMediaUrl,
                )
            );

        } // End if isCached

        $tpl->display('opinion/opinion_author_index.tpl', $cacheID);
        break;
}

