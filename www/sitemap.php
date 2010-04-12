<?php
require('./config.inc.php');
require_once('./core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('./core/content.class.php');
require_once('./core/content_category.class.php');
require_once('./core/content_manager.class.php');
require_once('./core/content_category_manager.class.php');

$tpl = new Template(TEMPLATE_USER);

header('Content-type: application/xml');

if(isset($_REQUEST['action']) ) {

    $cm  = new ContentManager();
    $ccm = ContentCategoryManager::get_instance();
    $allcategorys = $ccm->order_by_posmenu($ccm->categories);

    $categoriesnewsID = array();
    $categorieswebID = array();

    switch($_REQUEST['action']) {

        case 'web': {

            //FIXME: add this value in a config file for easy editing
            $numArticlesCategory = 250;
            foreach ($allcategorys as $catID_key => $catID_value) {
                if ($catID_value->inmenu == 1 && $catID_value->internal_category == 1) {
                    $categorieswebID[$catID_key] = $cm->get_permalinks_by_categoryID($catID_value->pk_content_category, 'available=1 AND fk_content_type=1','ORDER BY created DESC LIMIT 0 ,'.$numArticlesCategory);
                    $categorieswebID[$catID_key] = $cm->getInTime($categorieswebID[$catID_key]);
                }
            }
            
            $opinions = $cm->getOpinionAuthorsPermalinks('contents.available=1 and contents.content_status=1', 'ORDER BY in_home DESC, position ASC, changed DESC LIMIT 100');

            $tpl->assign('categorieswebID',$categorieswebID);
            $tpl->assign('opinions',$opinions);

        }break;

        case 'news': {

            //FIXME: add this value in a config file for easy editing
            $interval='DATE_SUB(CURDATE(), INTERVAL 3 DAY)';
            
            foreach ($allcategorys as $cID_key => $cID_value) {
                if ($cID_value->inmenu == 1 && $cID_value->internal_category == 1) {
                    $categoriesnewsID[$cID_key] = $cm->get_permalinks_by_categoryID($cID_value->pk_content_category, 'available=1 AND fk_content_type=1 AND changed >='.$interval.'','ORDER BY changed DESC');
                    $categoriesnewsID[$cID_key] = $cm->getInTime($categoriesnewsID[$cID_key]);
                }
            }

            $opinions = $cm->getOpinionAuthorsPermalinks('contents.available=1 AND contents.content_status=1 AND changed >='.$interval.'', 'ORDER BY position ASC, changed DESC LIMIT 100');

            $tpl->assign('categoriesnewsID',$categoriesnewsID);
            $tpl->assign('opinions',$opinions);

        }
    }
    
    $tpl->assign('SITE', SITE);
    $tpl->assign('allcategorys', $allcategorys);

    $app->conn->Close();
}

echo $tpl->fetch('sitemap.tpl');