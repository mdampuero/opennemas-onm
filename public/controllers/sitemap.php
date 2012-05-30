<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Set up view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('sitemap');

// Bootup ContentManager and ContentManagerCategory
$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

$action = $request->query->filter('action', 'web', FILTER_SANITIZE_STRING);
$cacheID = $tpl->generateCacheId('sitemap', '', $action);

if (($tpl->caching == 0)
    || !$tpl->isCached('sitemap/sitemap.tpl', $cacheID)
) {

    // Get all available categories
    list($availableCategories, $subcats, $other) = $ccm->getArraysMenu(0, 1);

    switch ($action) {

        case 'web':

            //FIXME: add this value in a config file for easy editing
            $maxArticlesByCategory = 250;
            $numContents = 50;

            $articlesByCategory = array();

            // Foreach available category retrieve last $maxArticlesByCategory articles in there
            foreach ($availableCategories as $category) {
                if ($category->inmenu == 1
                    && $category->internal_category == 1
                ) {
                    $articlesByCategory[$category->name] = $cm->getArrayOfArticlesInCategory(
                        $category->pk_content_category,
                        'available=1 AND fk_content_type=1',
                        ' ORDER BY created DESC',
                        $maxArticlesByCategory
                    );
                    $articlesByCategory[$category->name] = $cm->getInTime(
                        $articlesByCategory[$category->name]
                    );

                }
            }

            $opinions = $cm->getOpinionAuthorsPermalinks(
                'contents.available=1 and contents.content_status=1',
                'ORDER BY in_home DESC, position ASC, changed DESC LIMIT 100'
            );
            foreach ($opinions as &$opinion) {
                $opinion['author_name_slug'] = StringUtils::get_title($opinion['name']);
            }

            $tpl->assign('articlesByCategory', $articlesByCategory);
            $tpl->assign('opinions', $opinions);

            break;

        case 'news':

            $articlesByCategory = array();

            $maxArticlesByCategory = floor(900 / count($availableCategories));

            // Foreach available category and retrieve articles from 700 days ago
            foreach ($availableCategories as $category) {
                if ($category->inmenu == 1
                    && $category->internal_category == 1
                ) {
                    $articlesByCategory[$category->name] = $cm->getArrayOfArticlesInCategory(
                        $category->pk_content_category,
                        'available=1 AND fk_content_type=1 ',
                        'ORDER BY changed DESC',
                        $maxArticlesByCategory
                    );
                    $articlesByCategory[$category->name] = $cm->getInTime(
                        $articlesByCategory[$category->name]
                    );

                }
            }

            // Get latest opinions
            $opinions = $cm->getOpinionAuthorsPermalinks(
                'contents.available=1 AND contents.content_status=1 ',
                'ORDER BY position ASC, changed DESC LIMIT 100'
            );

            $improvedOpinions = array();
            foreach ($opinions as $opinion) {

                $opinion['author_name_slug'] = StringUtils::get_title($opinion['name']);
                $improvedOpinions []= $opinion;
            }

            $tpl->assign('articlesByCategory', $articlesByCategory);
            $tpl->assign('opinions', $improvedOpinions);
            break;
    }

    $tpl->assign('availableCategories', $availableCategories);
}
$tpl->assign('action', $action);
$sitemapContents = $tpl->fetch('sitemap/sitemap.tpl', $cacheID);

$format = $request->query->filter('format', null, FILTER_SANITIZE_STRING);

if ($format == 'gz') {
    // disable ZLIB ouput compression
    ini_set('zlib.output_compression', 'Off');
    // compress data
    $gzipoutput = gzencode($sitemapContents, 6);
    header('Content-Type: application/x-download');
    header('Content-Encoding: gzip'); #
    header('Content-Length: '.strlen($gzipoutput));
    echo $gzipoutput;
} else {
    // Return the output as xml
    header('Content-type: application/xml charset=utf-8');
    echo $sitemapContents;
}
