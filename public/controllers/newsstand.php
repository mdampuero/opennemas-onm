<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Fetch HTTP variables
*/
$tpl = new Template(TEMPLATE_USER);

$contentType = Content::getIDContentType('kiosko');

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

$category_name = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', '', FILTER_SANITIZE_STRING);

$action = $request->query->filter('action', 'list' , FILTER_SANITIZE_STRING);
$cache_page = $request->query->filter('page', 0, FILTER_VALIDATE_INT);
$month = $request->query->filter('month', date('n'), FILTER_VALIDATE_INT);
$year = $request->query->filter('year', date('Y'), FILTER_VALIDATE_INT);

$tpl->assign(array( 'actual_category' =>$category_name, ));
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

/**
 * Setup view
*/

$tpl->setConfig('kiosko');

if(!defined('KIOSKO_DIR'))
        define('KIOSKO_DIR', "kiosko".SS);

switch ($action) {
    case 'list':
        /**
         * Avoid to run the entire app logic if is available a cache for this page
        */
        $cache_id = $tpl->generateCacheId('newsstand', $category_name,  $cache_page);
        $kiosko =array();
        if( ($tpl->caching == 0)
           || !$tpl->isCached('newsstand/newsstand.tpl', $cache_id) )
        {
            $ccm = ContentCategoryManager::get_instance();
            $category = $ccm->get_id($category_name);
            list($allcategorys, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);
            foreach ($allcategorys as $theCategory) {
                $cm = new ContentManager();

                $portadas = $cm->find_by_category('Kiosko', $theCategory->pk_content_category,
                                              ' `contents`.`available`=1   '.
                                              'AND MONTH(`kioskos`.date)='.$month.' AND'.
                                              ' YEAR(`kioskos`.date)='.$year.'',
                                              'ORDER BY `kioskos`.date DESC ');
                if (!empty($portadas)) {
                    $kiosko[] = array ('category' => $theCategory->title,
                                   'portadas' => $portadas);
                }
            }

            $tpl->assign( array('KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                 'date' => '1-'.$month.'-'.$year,
                 'MONTH' =>$month,
                 'YEAR' => $year
            ) );

            $tpl->assign('kiosko', $kiosko);

        }

    break;

    case 'read':

        $dirtyID = $request->query->filter('id', '' , FILTER_SANITIZE_STRING);

        $epaperId = Content::resolveID($dirtyID);

        /**
         * Redirect to album frontpage if id_album wasn't provided
         */
        if (is_null($epaperId)) { Application::forward301('/portadas_papel/'); }


        $cache_id = $tpl->generateCacheId('newsstand', $epaperId,  $cache_page);

        $epaper = new Kiosko($epaperId);

        if (!empty($epaper)) {
            $tpl->assign('epaper', $epaper);

            $format_date = strtotime($epaper->date);
            $month = date('m', $format_date);
            $year = date('Y',$format_date);
            $cm = new ContentManager();

            $portadas = $cm->find_by_category('Kiosko', $epaper->category,
                                          ' `contents`.`available`=1   '.
                                          'AND MONTH(`kioskos`.date)='.$month.' AND'.
                                          ' YEAR(`kioskos`.date)='.$year.' ',
                                          'ORDER BY `kioskos`.date DESC ');
            $kiosko =array();
            if (!empty($portadas)) {
                $kiosko[] = array ('category' => '',
                               'portadas' => $portadas);
            }
            $tpl->assign( array('KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                 'date' => '1-'.$month.'-'.$year,
                 'MONTH' =>$month,
                 'YEAR' => $year
            ) );

            $tpl->assign('kiosko', $kiosko);

        } else {
            Application::forward301('/portadas_papel/');
        }


    break;
}

//for widget_newsstand_dates
//TODO: intelligent wigget
$ki = new Kiosko();
$months_kiosko = $ki->get_months_by_years();
$tpl->assign('months_kiosko', $months_kiosko);

// advertisement NOCACHE
require_once('index_advertisement.php');


// Show in Frontpage
$tpl->display('newsstand/newsstand.tpl', $cache_id);
