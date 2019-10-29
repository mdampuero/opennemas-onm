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
 * Displays events.
 */
class EventController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list' => 'articles',
        'show' => 'articles'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list' => 'article_inner',
        'show' => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'list' => [ 1, 2, 5, 6, 7 ],
        'show' => [ 1, 2, 5, 6, 7 ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $queries = [
        'list' => [ 'page' ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'list' => 'frontend_events',
        'show' => 'frontend_event_show'
    ];

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list' => 'event/list.tpl',
        'show' => 'event/item.tpl'
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.events';

    /**
     * Returns the list of items basing on a list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return array The list of items.
     */
    protected function getItem(Request $request)
    {
        try {
            $item = $this->get('api.service.content')
                ->getItemBySlugAndContentType(
                    $request->get('slug'),
                    \ContentManager::getContentTypeIdFromName('event')
                );
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        if (!$item->isReadyForPublish()) {
            throw new ResourceNotFoundException();
        }

        return $item;
    }

    /**
     * Returns the list of items basing on a list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return array The list of items.
     */
    protected function getItems($params)
    {
        $date   = date('Y-m-d H:i:s');
        $offset = ($params['page'] <= 1) ? 0 : ($params['page'] - 1) * $params['epp'];

        $eventIds = $this->get('orm.manager')->getConnection('instance')
            ->fetchAll(
                "SELECT SQL_CALC_FOUND_ROWS DISTINCT pk_content, contentmeta.meta_value as event_start_date "
                . "FROM contents join contentmeta "
                . "ON contentmeta.meta_name = 'event_start_date' "
                . "AND contents.pk_content = contentmeta.fk_content "
                . "WHERE fk_content_type = 5 AND content_status = 1 and in_litter = 0 "
                . "AND (starttime IS NULL OR starttime <= ? ) "
                . "AND (endtime IS NULL OR endtime > ?) "
                . " ORDER BY event_start_date DESC LIMIT ? OFFSET ?",
                [ $date, $date, $params['epp'], $offset ]
            );

        $sql = 'SELECT FOUND_ROWS()';

        $total = $this->get('orm.manager')->getConnection('instance')
            ->fetchAssoc($sql);

        $total = array_pop($total);

        $contents = $this->get('api.service.content')
            ->getListByIds(array_map(function ($event) {
                return $event['pk_content'];
            }, $eventIds));

        return [
            $contents['items'],
            $total
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($request, $item = null)
    {
        $params = parent::getParameters($request, $item);

        if (!array_key_exists('page', $params)) {
            $params['page'] = 1;
        }

        $params['epp'] = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        // Prevent invalid page when page is not numeric
        $params['page'] = (int) $params['page'];

        list($positions, $advertisements) = $this->getAdvertisements($item);

        return array_merge($this->params, $params, [
            'cache_id'       => $this->getCacheId($params),
            'ads_positions'  => $positions,
            'advertisements' => $advertisements
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        // Invalid page provided as parameter
        if ($params['page'] <= 0) {
            throw new ResourceNotFoundException();
        }

        list($contents, $total) = $this->getItems($params);

        // No first page and no contents
        if ($params['page'] > 1 && empty($contents)) {
            throw new ResourceNotFoundException();
        }

        $expires = $this->getCacheExpire($contents);

        if (!empty($expires)) {
            $lifetime = strtotime($expires) - time();

            if ($lifetime < $this->view->getCacheLifetime()) {
                $this->view->setCacheLifetime($lifetime);
            }

            $params['x-cache-for'] = $expires;
        }

        $params['contents']   = $contents;
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'boundary'    => false,
            'epp'         => $params['epp'],
            'maxLinks'    => 5,
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => 'frontend_events',
        ]);

        $params['related_contents'] = $this->getRelations($contents);
        $params['tags']             = $this->getTags($contents);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []) : void
    {
        $params['related_contents'] = $this->getRelations($params['content']);
        $params['tags']             = $this->getTags($params['content']);
    }

    /**
     * Returns the list of covers
     *
     * @param array $coverIds the list of contents to fetch related from
     *
     * @return array
     */
    public function getRelations($contents)
    {
        if (!is_array($contents)) {
            $contents = [ $contents ];
        }

        $ids = [];
        foreach ($contents as $content) {
            if (!$content->hasRelated('cover')) {
                continue;
            }

            $ids[] = $content->getMedia('cover');
        }

        if (empty($ids)) {
            return [];
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
}
