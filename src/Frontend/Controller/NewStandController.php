<?php
/**
 * Defines the frontend controller for the kiosko content type
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for kiosko content type
 *
 * @package Frontend_Controllers
 */
class NewStandController extends Controller
{
    /**
     * Common code for all the actions
     */
    public function init()
    {
        $this->cm            = new \ContentManager();
        $this->category_name = $this->get('request_stack')
            ->getCurrentRequest()
            ->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $this->view->assign([ 'actual_category' => $this->category_name, ]);
    }

    /**
     * Renders the newstand frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function frontpageAction(Request $request)
    {
        $month = $request->query->getDigits('month', date('m'));
        $year  = $request->query->getDigits('year', date('Y'));
        $day   = $request->query->getDigits('day', '01');

        if (empty($this->category_name)) {
            $this->category_name = 'home';
        }

        // Get settings for frontpage rendering
        $configurations = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('kiosko_settings');
        $order          = $configurations['orderFrontpage'];

        // Setup templating cache layer
        $this->view->setConfig('kiosko');
        $cacheID = $this->view->getCacheId(
            'frontpage',
            'kiosko',
            $this->category_name,
            $order . $year . $month . $day
        );

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('newsstand/newsstand.tpl', $cacheID)
        ) {
            $kioskos = [];

            if ($order == 'grouped') {
                $categories = $this->get('api.service.category')->getList();

                $where = "";
                $limit = "LIMIT 48";
                $month = $request->query->getDigits('month');
                $year  = $request->query->getDigits('year');

                if (!empty($month)) {
                    $where .= " AND MONTH(`kioskos`.date)='{$month}' ";
                    $limit  = "";
                }

                if (!empty($year)) {
                    $where .= " AND YEAR(`kioskos`.date)='{$year}' ";
                    $limit  = "";
                }

                foreach ($categories['items'] as $category) {
                    $portadas = $this->cm->find_by_category(
                        'Kiosko',
                        $category->pk_content_category,
                        ' `contents`.`content_status`=1 ' . $where,
                        "ORDER BY `kioskos`.date DESC {$limit}"
                    );

                    if (!empty($portadas)) {
                        $kioskos[] = [
                            'category' => $category->title,
                            'portadas' => $portadas
                        ];
                    }
                }
            } elseif ($order == 'sections') {
                $date     = "$year-$month-$day";
                $portadas = $this->cm->findAll(
                    'Kiosko',
                    ' `contents`.`content_status`=1 AND  `kioskos`.date ="' . $date . '"',
                    'ORDER BY `kioskos`.date DESC '
                );

                if (!empty($portadas)) {
                    $kioskos[] = [ 'portadas' => $portadas ];
                }
            } else {
                $categories = $this->get('api.service.category')->getList();

                foreach ($categories['items'] as $category) {
                    $portadas = $this->cm->find_by_category(
                        'Kiosko',
                        $category->pk_content_category,
                        ' `contents`.`content_status`=1   ' .
                        'AND MONTH(`kioskos`.date)=' . $month . ' AND' .
                        ' YEAR(`kioskos`.date)=' . $year,
                        'ORDER BY `kioskos`.date DESC '
                    );

                    if (!empty($portadas)) {
                        $kioskos[] = [
                            'category' => $category->title,
                            'portadas' => $portadas
                        ];
                    }
                }
            }

            $this->view->assign('kiosko', $kioskos);
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('newsstand/newsstand.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
            'KIOSKO_IMG_URL' => INSTANCE_MEDIA . KIOSKO_DIR,
            'selected_date'  => '1-' . $month . '-' . $year,
            'MONTH'          => $month,
            'YEAR'           => $year,
            'year'           => $year,
            'month'          => $month,
            'order'          => $order,
            'x-tags'         => 'newsstand-frontpage'
        ]);
    }

    /**
     * Renders a particular cover given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function showAction(Request $request)
    {
        $dirtyID = $request->get('id', null);

        $content = $this->get('content_url_matcher')
            ->matchContentUrl('kiosko', $dirtyID, null, $this->category_name);

        if (empty($content)) {
            throw new ResourceNotFoundException();
        }

        // Setup templating cache layer
        $this->view->setConfig('kiosko');
        $cacheID = $this->view->getCacheId('content', $content->id);

        if (($this->view->getCaching() === 0)
            || (!$this->view->isCached('newsstand/newsstand.tpl', $cacheID))
        ) {
            $month = date('m', $date);
            $year  = date('Y', $date);

            $this->view->assign([
                'date'   => '1-' . $month . '-' . $year,
                'YEAR'   => $year,
            ]);
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('newsstand/newsstand.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'epaper'         => $content,
            'content'        => $content,
            'cache_id'       => $cacheID,
            'KIOSKO_IMG_URL' => INSTANCE_MEDIA . KIOSKO_DIR,
            'o_content'      => $content,
            'x-tags'         => 'newsstand,' . $content->pk_content,
            'tags'           => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($content->tag_ids)['items']
        ]);
    }

    /**
     * Fetches the advertisement
     *
     * @return array of advertisements
     */
    private function getAds()
    {
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        // Get news_stand positions
        $positionManager = $this->get('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup(
            'frontpage',
            [103, 105]
        );
        $advertisements  = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
