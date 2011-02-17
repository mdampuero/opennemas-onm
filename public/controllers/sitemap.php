<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Set up view
*/
$tpl = new Template(TEMPLATE_USER);

// Bootup ContentManager and ContentManagerCategory
$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

// Get all available categories
$availableCategories = $ccm->order_by_posmenu($ccm->categories);

$action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);
switch($action) {

    case 'web':

        //FIXME: add this value in a config file for easy editing
        $maxArticlesByCategory = 250;
        $numContents = 50;

        $articlesByCategory = array();

        // Foreach available category retrieve last $maxArticlesByCategory articles in there
        foreach ($availableCategories as $category) {
            if ($category->inmenu == 1
                && $category->internal_category == 1)
            {

                $articlesByCategory[$category->name] = $cm->getArrayOfArticlesInCategory($category->pk_content_category, 'available=1 AND fk_content_type=1',' ORDER BY created DESC LIMIT 0 ,'.$maxArticlesByCategory);
                $articlesByCategory[$category->name] = $cm->getInTime($articlesByCategory[$category->name]);

            }
        }

        $opinions = $cm->getOpinionAuthorsPermalinks('contents.available=1 and contents.content_status=1', 'ORDER BY in_home DESC, position ASC, changed DESC LIMIT 100');

        $tpl->assign('articlesByCategory',$articlesByCategory);
        $tpl->assign('opinions',$opinions);


    break;

    case 'news': {

        //FIXME: add this value in a config file for easy editing
        $interval='DATE_SUB(CURDATE(), INTERVAL 700 DAY)';

        $articlesByCategory = array();

        // Foreach available category and retrieve articles from 700 days ago
        foreach ($availableCategories as $category) {
            if ($category->inmenu == 1
                && $category->internal_category == 1)
            {

                $articlesByCategory[$category->name] = $cm->getArrayOfArticlesInCategory($category->pk_content_category, 'available=1 AND fk_content_type=1 AND changed >='.$interval.'','ORDER BY changed DESC');
                $articlesByCategory[$category->name] = $cm->getInTime($articlesByCategory[$category->name]);

            }
        }

        // Get latest opinions
        $opinions = $cm->getOpinionAuthorsPermalinks('contents.available=1 AND contents.content_status=1 AND changed >='.$interval.'', 'ORDER BY position ASC, changed DESC LIMIT 100');

        $tpl->assign('articlesByCategory',$articlesByCategory);
        $tpl->assign('opinions', $opinions);

    }
}

$tpl->assign('availableCategories', $availableCategories);

// Return the output as xml
header('Content-type: application/xml charset=utf-8');
echo $tpl->fetch('sitemap/sitemap.tpl');
