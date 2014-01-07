<?php
/**
 * Handles the actions for newsstand
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for newsstand
 *
 * @package Frontend_Controllers
 **/
class NewStandController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('kiosko');

        $this->cm = new \ContentManager();

        // Esta variable no se utiliza?¿ Ni tp viene por .htaccess
        // $subcategory_name = $this->request->query->filter('subcategory_name', '', FILTER_SANITIZE_STRING);
        // solo se usa al cachear en show (tiene sentido?¿) Tp viene por .htaccess
        // $page  = $this->request->query->getDigits('page', 1);
        $this->category_name = $this->request->query->filter('category_name', '', FILTER_SANITIZE_STRING);

        $this->view->assign(array( 'actual_category' => $this->category_name, ));

        if (!defined('KIOSKO_DIR')) {
            define('KIOSKO_DIR', "kiosko".SS);
        }
    }

    /**
     * Renders the newstand frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        // Avoid to run the entire app logic if is available a cache for this page
        $configurations = s::get('kiosko_settings');
        $month = $request->query->getDigits('month', date('n'));
        $year  = $request->query->getDigits('year', date('Y'));

        $order = $configurations['orderFrontpage'];
        if ($order =='grouped') {
            $cache_date = $year.$month;
            $cacheID = $this->view->generateCacheId('newsstand', $this->category_name, $cache_date);
            $kiosko =array();
            if (($this->view->caching == 0)
                || !$this->view->isCached('newsstand/newsstand.tpl', $cacheID)
            ) {
                $ccm = \ContentCategoryManager::get_instance();
                $contentType = \ContentManager::getContentTypeIdFromName('kiosko');
                $category = $ccm->get_id($this->category_name);

                list($allcategorys, $subcat, $categoryData)
                    = $ccm->getArraysMenu($category, $contentType);
                $where = "";
                $limit = "LIMIT 48";
                $month = $request->query->getDigits('month');
                if (!empty($month)) {
                    $where .= " AND MONTH(`kioskos`.date)='{$month}' ";
                    $limit ="";
                }
                $year = $request->query->getDigits('year');
                if (!empty($year)) {
                    $where .= " AND YEAR(`kioskos`.date)='{$year}' ";
                    $limit ="";
                }

                foreach ($allcategorys as $theCategory) {
                    $portadas = $this->cm->find_by_category(
                        'Kiosko',
                        $theCategory->pk_content_category,
                        ' `contents`.`available`=1   '.
                        $where,
                        "ORDER BY `kioskos`.date DESC  {$limit}"
                    );
                    if (!empty($portadas)) {
                        $kiosko[] = array (
                            'category' => $theCategory->title,
                            'portadas' => $portadas
                        );
                    }
                }
            }
        } elseif ($order =='sections') {
            $day        = $request->query->getDigits('day', 1);
            $cache_date = $year.$month.$day;
            $cacheID    = $this->view->generateCacheId('newsstand', $this->category_name, $cache_date);
            $kiosko     = array();
            if (($this->view->caching == 0)
                || !$this->view->isCached('newsstand/newsstand.tpl', $cacheID)
            ) {

                $date = "$year-$month-$day";
                $portadas = $this->cm->findAll(
                    'Kiosko',
                    ' `contents`.`available`=1 AND  `kioskos`.date ="'.$date.'"',
                    'ORDER BY `kioskos`.date DESC '
                );

                if (!empty($portadas)) {
                    $kiosko[] = array (
                        'portadas' => $portadas
                    );
                }
            }
        } else {
            $cacheDate = $year.$month;
            $cacheID   = $this->view->generateCacheId('newsstand', $this->category_name, $cacheDate);
            $kiosko    = array();
            if (($this->view->caching == 0)
                || !$this->view->isCached('newsstand/newsstand.tpl', $cacheID)
            ) {
                $ccm = \ContentCategoryManager::get_instance();
                $contentType = \ContentManager::getContentTypeIdFromName('kiosko');
                $category = $ccm->get_id($this->category_name);
                list($allcategorys, $subcat, $categoryData) = $ccm->getArraysMenu($category, $contentType);

                foreach ($allcategorys as $theCategory) {
                    $portadas = $this->cm->find_by_category(
                        'Kiosko',
                        $theCategory->pk_content_category,
                        ' `contents`.`available`=1   '.
                        'AND MONTH(`kioskos`.date)='.$month.' AND'.
                        ' YEAR(`kioskos`.date)='.$year.'',
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

        $this->widgetNewsstandDates();
        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        // Show in Frontpage
        return $this->render(
            'newsstand/newsstand.tpl',
            array(
                'cache_id' => $cacheID,
                'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                'selected_date'  => '1-'.$month.'-'.$year,
                'MONTH'          => $month,
                'YEAR'           => $year,
                'year'           => $year,
                'month'          => $month,
                'order'          => $order,
                'kiosko'         => $kiosko
            )
        );

    }

    /**
     * Renders a particular cover given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID = $request->query->getDigits('id', null);

        $epaperId = \Content::resolveID($dirtyID);

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($epaperId)) {
            return new RedirectResponse($this->generateUrl('frontend_newstandPaypal_frontpage'));
        }

        $cacheID = $this->view->generateCacheId('newsstand', null, $epaperId);
        if (($this->view->caching == 0)
            || (!$this->view->isCached('newsstand/newsstand.tpl', $cacheID))
        ) {

            $epaper = new \Kiosko($epaperId);

            $format_date = strtotime($epaper->date);
            $month       = date('m', $format_date);
            $year        = date('Y', $format_date);

            $portadas = $this->cm->find_by_category(
                'Kiosko',
                $epaper->category,
                ' `contents`.`available`=1   ',
                'ORDER BY `kioskos`.date DESC  LIMIT 4'
            );
            $kiosko =array();
            if (!empty($portadas)) {
                $kiosko[] = array (
                    'category' => '',
                    'portadas' => $portadas
                );
            }
            $this->view->assign(
                array(
                    'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                    'date'           => '1-'.$month.'-'.$year,
                    'MONTH'          => $month,
                    'YEAR'           => $year,
                    'epaper'         => $epaper,
                    'content'        => $epaper,
                    'kiosko'         => $kiosko
                )
            );
        }

        $this->widgetNewsstandDates();
        $this->getAds();

        return $this->render(
            'newsstand/newsstand.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * calculates the months of the covers existing
     *
     * @return
     **/
    public function widgetNewsstandDates()
    {
        //for widget_newsstand_dates
        //TODO: intelligent wigget
        $ki = new \Kiosko();
        $months_kiosko = $ki->get_months_by_years();
        $this->view->assign('months_kiosko', $months_kiosko);
    }

    /**
     * Fetches the advertisement
     *
     * @return array of advertisements
     **/
    private function getAds()
    {
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        // Get news_stand positions
        $positionManager = getContainerParameter('instance')->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('frontpage', array(103, 105));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }
}
