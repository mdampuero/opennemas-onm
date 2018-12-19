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
        $page = $request->get('page', 1);

        $this->view->setConfig('frontpages');
        $cacheID = $this->view->getCacheId('frontpage', 'events', $page);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached("event/frontpage.tpl", $cacheID)
        ) {
            $date   = date('Y-m-d H:i:s');
            $epp    = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('items_per_page');
            $offset = ($page <= 2) ? 0 : ($page - 1) * $epp;

            $eventIds = $this->get('orm.manager')->getConnection('instance')
                ->executeQuery(
                    "SELECT SQL_CALC_FOUND_ROWS DISTINCT pk_content, contentmeta.meta_value as event_start_date "
                    . "FROM contents join contentmeta "
                    . "ON contentmeta.meta_name = 'event_start_date' "
                    . "AND contents.pk_content = contentmeta.fk_content "
                    . "WHERE fk_content_type = 5 AND content_status = 1 and in_litter = 0 "
                    . "AND (starttime = '0000-00-00 00:00:00' OR starttime IS NULL OR starttime <= ? ) "
                    . "AND (endtime IS NULL OR endtime = '0000-00-00 00:00:00' OR endtime > ?) "
                    . " ORDER BY event_start_date DESC LIMIT ? OFFSET ?",
                    [ $date, $date, $epp, $offset ]
                )
                ->fetchAll();

            $sql = 'SELECT FOUND_ROWS()';

            $total = $this->get('dbal_connection')->fetchAssoc($sql);
            $total = array_pop($total);

            $contents = $this->get('api.service.content')
                ->getListByIds(array_map(function ($event) {
                    return $event['pk_content'];
                }, $eventIds));

            $this->view->assign([
                'contents'        => $contents['items'],
                'pagination'      => $this->get('paginator')->get([
                    'directional' => true,
                    'epp'         => 5,
                    'page'        => $page,
                    'total'       => $total,
                    'route'       => 'frontend_events_frontpage'
                ]),
                'related_contents' => $this->getRelations($contents['items']),
                'tags'             => $this->getTags($contents['items']),
            ]);
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('event/frontpage.tpl', [
            'ads_positions'      => $positions,
            'advertisements'     => $advertisements,
            'page'               => $page,
            'cache_id'           => $cacheID,
            'x-tags'             => 'event-frontpage,' . $page,
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
            'cache_id'           => $cacheID,
            'o_content'          => $content,
            'x-tags'             => 'event,' . $content->id,
            'tags'               => $this->getTags($content),
            'related_contents'   => $this->getRelations($content),
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

    /**
     * Returns the list of covers
     *
     * @param array $coverIds the list of contents to fetch related from
     *
     * @return array
     */
    public function getRelations(&$contents)
    {
        if (!is_array($contents)) {
            $contents = [ $contents ];
        }

        $ids = [];
        foreach ($contents as $content) {
            if (!$content->hasRelated('cover')) {
                continue;
            }

            $ids[] = $content->getRelated('cover');
        }

        $relations = $this->get('entity_repository')->findBy([
            'content_type_name' => [[ 'value' => 'photo', ]],
            'pk_content'        => [[ 'value' => array_unique($ids), 'operator' => 'IN', 'value']],
        ]);

        $relations = $this->get('data.manager.filter')
            ->set($relations)
            ->filter('mapify', [ 'key' => 'id' ])
            ->get();

        return $relations;
    }

    /**
     * Returns the list of tags from a list of contents
     *
     * @param array $contents The list of contents to fetch tags from
     *
     * @return array
     */
    public function getTags($contents)
    {
        if (!is_array($contents)) {
            $contents = [ $contents ];
        }

        $tagIds = [];
        foreach ($contents as $content) {
            $tagIds = array_merge($tagIds, $content->tags);
        }

        $tags = $this->get('api.service.tag')
            ->getListByIdsKeyMapped($tagIds)['items'];

        return $tags;
    }
}
