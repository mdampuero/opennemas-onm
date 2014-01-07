<?php
/**
 * Handles the actions for newstand paypal
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
 * Handles the actions for newstand paypal
 *
 * @package Frontend_Controllers
 **/
class NewStandPaypalController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->sessionBootstrap($this->request);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('kiosko');

        $this->cm = new \ContentManager();

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
        $month = $request->query->getDigits('month', date('n'));
        $year  = $request->query->getDigits('year', date('Y'));

        $configurations = s::get('kiosko_settings');
        $order = $configurations['orderFrontpage'];

        // Order by grouped dates
        if ($order =='grouped') {
            // Avoid run entire logic if cached
            $cacheID = $this->view->generateCacheId('newsstand', $this->category_name, $year);
            $kiosko =array();
            if (($this->view->caching == 0)
               || !$this->view->isCached('newsstand/newsstand.tpl', $cacheID)
            ) {
                $ccm = \ContentCategoryManager::get_instance();
                $contentType = \ContentManager::getContentTypeIdFromName('kiosko');
                $category = $ccm->get_id($this->category_name);
                list($allcategorys, $subcat, $categoryData) =
                    $ccm->getArraysMenu($category, $contentType);

                foreach ($allcategorys as $theCategory) {
                    $portadas = $this->cm->find_by_category(
                        'Kiosko',
                        $theCategory->pk_content_category,
                        ' `contents`.`available`=1   '.
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

        } elseif ($order =='sections') {
            // Order by categories
            $day        = $request->query->getDigits('day', 1);
            $cache_date = $year.$month.$day;
            $cacheID = $this->view->generateCacheId('newsstand', $this->category_name, $cache_date);
            $kiosko =array();
            if (($this->view->caching == 0)
               || !$this->view->isCached('newsstand/newsstand.tpl', $cacheID)
            ) {
                // $ccm = \ContentCategoryManager::get_instance();
                // $category = $ccm->get_id($this->category_name);
                // list($allcategorys, $subcat, $categoryData) =
                //      $ccm->getArraysMenu($category, $contentType);

                $date = "$year-$month-$day";
                $portadas = $this->cm->findAll(
                    'Kiosko',
                    ' `contents`.`available`=1'.
                    ' AND  `kioskos`.date ="'.$date.'" AND `kioskos`.`type`=0',
                    'ORDER BY `kioskos`.date DESC '
                );

                if (!empty($portadas)) {
                    $kiosko[] = array (
                        'portadas' => $portadas
                    );
                }

            }

        } else {
            // Order by simple date
            $cacheDate = $year.$month;
            $cacheID   = $this->view->generateCacheId('newsstand', $this->category_name, $cacheDate);
            $kiosko     = array();
            if (($this->view->caching == 0)
               || !$this->view->isCached('newsstand/newsstand.tpl', $cacheID)
            ) {
                $ccm = \ContentCategoryManager::get_instance();
                $contentType = \ContentManager::getContentTypeIdFromName('kiosko');
                $category = $ccm->get_id($this->category_name);
                list($allcategorys, $subcat, $categoryData) =
                    $ccm->getArraysMenu($category, $contentType);

                foreach ($allcategorys as $theCategory) {
                    $portadas = $this->cm->find_by_category(
                        'Kiosko',
                        $theCategory->pk_content_category,
                        ' `contents`.`available`=1   '.
                        'AND MONTH(`kioskos`.date)='.$month.' AND'.
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

        $this->view->assign(
            array(
                'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                'selected_date'  => '1-'.$month.'-'.$year,
                'MONTH'          => $month,
                'YEAR'           => $year,
                'kiosko'         => $kiosko,
                'content'        => $epaper,
            )
        );

        $this->widgetNewsstandDates();

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        // Show in Frontpage
        return $this->render(
            'newsstand/newsstand.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );

    }

     /**
     * Render a particular cover
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
            return new RedirectResponse($this->generateUrl('frontend_kiosko_frontpage'));
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
                    'epaper'         => $epaper,
                    'content'         => $epaper,
                    'date'           => '1-'.$month.'-'.$year,
                    'MONTH'          => $month,
                    'YEAR'           => $year,
                    'kiosko'         => $kiosko,
                    'KIOSKO_IMG_URL' => INSTANCE_MEDIA.KIOSKO_DIR,
                )
            );
        }

        $this->widgetNewsstandDates();
        $this->advertisements();

        // Show in Frontpage
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
     * @return void
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
     * @return void
     **/
    private function getAds()
    {
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        // Get news_stand positions
        $positionManager = getContainerParameter('instance')->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('frontpage', array(103, 105));

        return \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }

    /**
     * undocumented function
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    private function sessionBootstrap(Request $request)
    {
        $sessionLifeTime = (int) s::get('max_session_lifetime', 60);
        if ((int) $sessionLifeTime > 0) {
            ini_set('session.cookie_lifetime', $sessionLifeTime*60);
        } else {
            s::set('max_session_lifetime', 60*30);
        }

        session_name('_onm_sess');
        $session = $this->container->get('session');
        $session->start();
        $request->setSession($session);

        if (!isset($_SESSION['userid'])
            && !preg_match('@^/login@', $request->getPathInfo())
        ) {
            return new RedirectResponse('/');
        }
    }
}
