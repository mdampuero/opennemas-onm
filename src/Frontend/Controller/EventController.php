<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays static pages.
 */
class EventController extends Controller
{
    /**
     * Displays the list of the latest events.
     *
     * @return Response The response object.
     */
    public function frontpageAction(Request $request)
    {
        $page  = $request->get('page', 1);
        $date  = date('Y-m-d H:i:s');
        $epp   = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page');
        $offset = ($page <= 2) ? 0 : ($page-1) * $epp;

        $eventIds = $this->get('orm.manager')->getConnection('instance')
            ->executeQuery(
                "SELECT SQL_CALC_FOUND_ROWS DISTINCT pk_content, contentmeta.meta_value as event_startdate "
                . "FROM contents join contentmeta "
                . "ON contentmeta.meta_name = 'event_startdate' "
                . "AND contents.pk_content = contentmeta.fk_content "
                . "WHERE fk_content_type = 19 AND content_status = 1 and in_litter = 0 "
                . "AND (starttime = '0000-00-00 00:00:00' OR starttime IS NULL OR starttime <= ? ) "
                . "AND (endtime IS NULL OR endtime = '0000-00-00 00:00:00' OR endtime > ?) "
                . " ORDER BY event_startdate DESC LIMIT ? OFFSET ?",
                [ $date, $date, $epp, $offset ]
            )
            ->fetchAll();

        $sql = 'SELECT FOUND_ROWS()';

        $total = $this->get('dbal_connection')->fetchAssoc($sql);
        $total = array_pop($total);



        $eventIds = array_map(function ($event) {
            return $event['pk_content'];
        }, $eventIds);

        $contents = $this->get('api.service.content')
            ->getListByIds($eventIds);

        list($positions, $advertisements) = $this->getAds();

        $pagination = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => 5,
            'page'        => $page,
            'total'       => $total,
            'route'       => 'frontend_events_frontpage'
        ]);


        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('event', 'frontpage', $page);

        return $this->render('event/frontpage.tpl', [
            'ads_positions'      => $positions,
            'advertisements'     => $advertisements,
            'contents'           => $contents['items'],
            'page'               => $page,
            'cache_id'           => $cacheID,
            'pagination'         => $pagination,
            'x-tags'             => 'event-frontpage',
        ]);
    }


    /**
     * Displays an event.
     *
     * @param string $slug The event slug.
     *
     * @return Response The response object.
     */
    public function showAction($slug)
    {
        $oql = 'content_type_name = "event"'
            . ' and slug = "%s" and content_status = "1" and in_litter = "0"';

        try {
            $content = $this->get('api.service.content')
                ->getItemBy(sprintf($oql, $slug));
        } catch (\Exception $e) {
            // If the content does not exist or is not published raise an error
            throw new ResourceNotFoundException();
        }

        // TODO: Remove when pk_content column renamed to id
        $content->id = $content->pk_content;

        $contentAux       = new \Content();
        $contentAux->id   = $content->id;
        $auxTagIds        = $contentAux->getContentTags($content->id);
        $content->tag_ids = array_key_exists($content->id, $auxTagIds) ?
            $auxTagIds[$content->id] :
            [];

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('content', $content->id);

        list($positions, $advertisements) = $this->getAds();
        return $this->render('event/show.tpl', [
            'ads_positions'      => $positions,
            'advertisements'     => $advertisements,
            'category_real_name' => $content->title,
            'content'            => $content,
            'content_id'         => $content->id,
            'page'               => $content,
            'cache_id'           => $cacheID,
            'o_content'          => $content,
            'x-tags'             => 'static-page,' . $content->id,
            'tags'               => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($content->tag_ids)['items']
        ]);
    }

    /**
     * Returns all the advertisements for an static page.
     *
     * @return array A list of Advertisements.
     */
    public function getAds()
    {
        // Get static_pages positions
        $positionManager = getService('core.helper.advertisement');
        $positions       = $positionManager
            ->getPositionsForGroup('article_inner', [ 1, 2, 5, 6, 7 ]);

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, 0);

        return [ $positions, $advertisements ];
    }
}
