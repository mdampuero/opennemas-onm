<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

use Onm\Settings as s;

//Start up and setup the app
require_once '../bootstrap.php';
require_once 'session_bootstrap.php';

// If user has no session, redirect to index
if (!isset($_SESSION['userid'])) {
    Application::forward('/');
}

// Fetch HTTP variables
$category_name    = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', '', FILTER_SANITIZE_STRING);
$action     = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);
$cache_page = $request->query->filter('page', 0, FILTER_VALIDATE_INT);
$month      = $request->query->filter('month', date('n'), FILTER_VALIDATE_INT);
$year       = $request->query->filter('year', date('Y'), FILTER_VALIDATE_INT);

// Setup View
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('kiosko');

// Get content type Id for kiosko
$contentType = Content::getIDContentType('kiosko');

// Assign category name to template
$tpl->assign('actual_category', $category_name);

if (!defined('KIOSKO_DIR')) {
    define('KIOSKO_DIR', "kiosko".SS);
}

switch ($action) {
    case 'list':
        // Get config vars
        $configurations = s::get('kiosko_settings');
        $order = $configurations['orderFrontpage'];

        // Order by grouped dates
        if ($order =='grouped') {
            // Avoid run entire logic if cached
            $cache_id = $tpl->generateCacheId('newsstand', $category_name, $year);
            $kiosko =array();
            if (($tpl->caching == 0)
               || !$tpl->isCached('newsstand/newsstand.tpl', $cache_id)
            ) {
                $cm = new ContentManager();
                $ccm = ContentCategoryManager::get_instance();
                $category = $ccm->get_id($category_name);
                list($allcategorys, $subcat, $categoryData) =
                    $ccm->getArraysMenu($category, $contentType);
                foreach ($allcategorys as $theCategory) {
                    $portadas = $cm->find_by_category(
                        'Kiosko',
                        $theCategory->pk_content_category,
                        ' `contents`.`available`=1 '.
                        'AND YEAR(`kioskos`.date)='.$year.' AND `kioskos`.`type`=0',
                        'ORDER BY `kioskos`.date DESC '
                    );
                    if (!empty($portadas)) {
                        $kiosko[] = array (
                            'category' => $theCategory->title,
                            'portadas' => $portadas
                        );
                    }
                }
            }
            // Order by categories
        } elseif ($order =='sections') {
            $day        = $request->query->filter('day', '1', FILTER_VALIDATE_INT);
            $cache_date = $year.$month.$day;
            $cache_id = $tpl->generateCacheId('newsstand', $category_name, $cache_date);
            $kiosko =array();
            if (($tpl->caching == 0)
               || !$tpl->isCached('newsstand/newsstand.tpl', $cache_id)
            ) {
                $cm = new ContentManager();

                // $ccm = ContentCategoryManager::get_instance();
                // $category = $ccm->get_id($category_name);
                // list($allcategorys, $subcat, $categoryData) =
                //      $ccm->getArraysMenu($category, $contentType);


                $date = "$year-$month-$day";
                $portadas = $cm->findAll(
                    'Kiosko',
                    ' `contents`.`available`=1 '.
                    'AND  `kioskos`.date ="'.$date.'" AND `kioskos`.`type`=0',
                    'ORDER BY `kioskos`.date DESC '
                );

                if (!empty($portadas)) {
                    $kiosko[] = array (
                        'portadas' => $portadas
                    );
                }

            }
            // Order by simple date
        } else {

            $cache_date = $year.$month;
            $cache_id   = $tpl->generateCacheId('newsstand', $category_name, $cache_date);
            $kiosko     = array();
            if (($tpl->caching == 0)
               || !$tpl->isCached('newsstand/newsstand.tpl', $cache_id)
            ) {
                $cm = new ContentManager();
                $ccm = ContentCategoryManager::get_instance();
                $category = $ccm->get_id($category_name);
                list($allcategorys, $subcat, $categoryData) =
                    $ccm->getArraysMenu($category, $contentType);
                foreach ($allcategorys as $theCategory) {
                    $portadas = $cm->find_by_category(
                        'Kiosko',
                        $theCategory->pk_content_category,
                        ' `contents`.`available`=1 AND MONTH(`kioskos`.date)='.$month.' AND'.
                        ' YEAR(`kioskos`.date)='.$year.' AND `kioskos`.`type`=0',
                        'ORDER BY `kioskos`.date DESC '
                    );
                    if (!empty($portadas)) {
                        $kiosko[] = array (
                            'category' => $theCategory->title,
                           'portadas' => $portadas
                        );
                    }
                }
            }
        }

        $tpl->assign(
            array(
                'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                'date'           => '1-'.$month.'-'.$year,
                'MONTH'          => $month,
                'YEAR'           => $year,
                'kiosko'         => $kiosko
            )
        );

        break;
    case 'read':

        $dirtyID = $request->query->filter('id', '', FILTER_SANITIZE_STRING);

        $epaperId = Content::resolveID($dirtyID);

        /**
         * Redirect to album frontpage if id_album wasn't provided
         */
        if (is_null($epaperId)) {
            Application::forward301('/kiosko/');
        }

        $cache_id = $tpl->generateCacheId('newsstand', $epaperId, $cache_page);

        $epaper = new Kiosko($epaperId);

        if (!empty($epaper)) {
            $tpl->assign('epaper', $epaper);

            $format_date = strtotime($epaper->date);
            $month       = date('m', $format_date);
            $year        = date('Y', $format_date);
            $cm          = new ContentManager();

            $portadas = $cm->find_by_category(
                'Kiosko',
                $epaper->category,
                ' `contents`.`available`=1 ',
                'ORDER BY `kioskos`.date DESC  LIMIT 4'
            );
            $kiosko =array();
            if (!empty($portadas)) {
                $kiosko[] = array (
                    'category' => '',
                    'portadas' => $portadas
                );
            }
            $tpl->assign(
                array(
                    'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                    'date' => '1-'.$month.'-'.$year,
                    'MONTH' =>$month,
                    'YEAR' => $year
                )
            );

            $tpl->assign('kiosko', $kiosko);

        } else {
            Application::forward301('/kiosko/');
        }
        break;
}

//for widget_newsstand_dates
//TODO: intelligent wigget
$ki = new Kiosko();
$months_kiosko = $ki->get_months_by_years();
$tpl->assign('months_kiosko', $months_kiosko);

// advertisement NOCACHE
require_once 'index_advertisement.php';

// Show in Frontpage
$tpl->display('newsstand/newsstand.tpl', $cache_id);

