<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Set up view
*/
$tpl = new Template(TEMPLATE_USER);

header('Content-type: application/xml');

if(isset($_REQUEST['action']) ) {

    $cm  = new ContentManager();
    $ccm = ContentCategoryManager::get_instance();
    $allcategorys = $ccm->order_by_posmenu($ccm->categories);

    $categoriesnewsID = array();
    $categorieswebID = array();
    $opinions = array();

    $action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);
    switch($action) {

        case 'web':

            $categoriesVideos = array();
            $categoriesGallerys = array();

            //FIXME: add this value in a config file for easy editing
            $numArticlesCategory = 250;
            $numContents = 50;
            foreach ($allcategorys as $catID_key => $catID_value) {
                if ($catID_value->inmenu == 1 && $catID_value->internal_category == 1) {
                    $categorieswebID[$catID_key] = $cm->get_permalinks_by_categoryID($catID_value->pk_content_category, 'available=1 AND fk_content_type=1',' ORDER BY created DESC LIMIT 0 ,'.$numArticlesCategory);
                    $categorieswebID[$catID_key] = $cm->getInTime($categorieswebID[$catID_key]);

                    $categoriesVideos[$catID_key] = $cm->get_permalinks_by_categoryID($catID_value->pk_content_category, 'available=1 AND fk_content_type=9',' ORDER BY created DESC LIMIT 0 ,'.$numContents);
                    $categoriesGallerys[$catID_key] = $cm->get_permalinks_by_categoryID($catID_value->pk_content_category, 'available=1 AND fk_content_type=7',' ORDER BY created DESC LIMIT 0 ,'.$numContents);
                }
            }

            $opinions = $cm->getOpinionAuthorsPermalinks('contents.available=1 and contents.content_status=1', 'ORDER BY in_home DESC, position ASC, changed DESC LIMIT 100');

            $tpl->assign('categorieswebID',$categorieswebID);
            $tpl->assign('opinions',$opinions);
            $tpl->assign('categoriesVideos', $categoriesVideos);
            $tpl->assign('categoriesGallerys', $categoriesGallerys);


        break;

        case 'news': {

            //FIXME: add this value in a config file for easy editing
            $interval='DATE_SUB(CURDATE(), INTERVAL 700 DAY)';

            foreach ($allcategorys as $cID_key => $cID_value) {
                if ($cID_value->inmenu == 1 && $cID_value->internal_category == 1) {
                    $categoriesnewsID[$cID_key] = $cm->get_permalinks_by_categoryID($cID_value->pk_content_category, 'available=1 AND fk_content_type=1 AND changed >='.$interval.'','ORDER BY changed DESC');
                    $categoriesnewsID[$cID_key] = $cm->getInTime($categoriesnewsID[$cID_key]);
                }
            }

            $opinions = $cm->getOpinionAuthorsPermalinks('contents.available=1 AND contents.content_status=1 AND changed >='.$interval.'', 'ORDER BY position ASC, changed DESC LIMIT 100');

            $tpl->assign('categoriesnewsID', $categoriesnewsID);
            $tpl->assign('opinions', $opinions);

        }
    }

    $tpl->assign('SITE', SITE);
    $tpl->assign('allcategorys', $allcategorys);
    $app->conn->Close();
}

echo $tpl->fetch('sitemap/sitemap.tpl');
