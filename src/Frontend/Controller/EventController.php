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
        'listTag' => 'frontend_event_listtag',
        'show' => 'frontend_event_show'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.event';

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list' => 'event/list.tpl',
        'listtag' => 'event/list.tpl',
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

        if (!$this->get('core.helper.content')->isReadyForPublish($item)) {
            throw new ResourceNotFoundException();
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $date = gmdate('Y-m-d H:i:s');

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        // Ejecutar la consulta SQL
        $response = $this->get('api.service.content')->getListBySql(sprintf(
            'select * from contents '
            . 'inner join contentmeta as cm1 on contents.pk_content = cm1.fk_content '
            . 'and cm1.meta_name = "event_start_date" '
            . 'left join contentmeta as cm2 on contents.pk_content = cm2.fk_content '
            . 'and cm2.meta_name = "event_end_date" '
            . 'where content_type_name="event" and content_status=1 and in_litter=0 '
            . 'and (cm1.meta_value >= "%s" or (cm1.meta_value < "%s" and cm2.meta_value >= "%s"))'
            . 'and (starttime is null or starttime < "%s") '
            . 'and (endtime is null or endtime > "%s") '
            . 'order by cm1.meta_value asc',
            gmdate('Y-m-d'),
            gmdate('Y-m-d'),
            gmdate('Y-m-d'),
            $date,
            $date
        ));

        $items = $response['items'];
        $total = count($items);
        $limit = ($params['epp'] * ($params['page'] - 1) + $params['epp']) > $total
            ? ($total - ($params['epp'] * ($params['page'] - 1)))
            : $params['epp'];

        $items = array_slice(
            $items,
            $params['epp'] * ($params['page'] - 1),
            $limit
        );

        // No first page and no contents
        if ($params['page'] > 1 && empty($items)) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);
            $params['x-cache-for'] = $expire;
        }

        $params['x-tags'] .= ',event-frontpage';

        $params['contents']   = $items;
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $params['epp'],
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => 'frontend_events'
        ]);

        $params['tags'] = $this->getTags($items);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateListTag(array &$params = []) : void
    {
        $currentDate = gmdate('Y-m-d');
        $fullDate    = gmdate('Y-m-d H:i:s');

        $oql = sprintf(
            'SELECT * FROM contents c
            INNER JOIN contentmeta as cm1 ON c.pk_content = cm1.fk_content
            AND cm1.meta_name = "event_start_date"
            LEFT JOIN contentmeta as cm2 ON c.pk_content = cm2.fk_content
            AND cm2.meta_name = "event_end_date"
            JOIN contents_tags ct ON c.pk_content = ct.content_id
            JOIN tags t ON ct.tag_id = t.id
            WHERE content_type_name="event"
            AND content_status=1
            AND in_litter=0
            AND (cm1.meta_value >= "%s" OR (cm1.meta_value < "%s" AND cm2.meta_value >= "%s"))
            AND (starttime IS NULL OR starttime < "%s")
            AND (endtime IS NULL OR endtime > "%s")
            AND t.slug = "%s"
            ORDER BY cm1.meta_value ASC',
            $currentDate,
            $currentDate,
            $currentDate,
            $fullDate,
            $fullDate,
            $params['tag']
        );

        $response = $this->get('api.service.content')->getListBySql($oql);
        $items    = $response['items'];

        if (empty($items)) {
            throw new ResourceNotFoundException();
        }

        if ($expire = $this->get('core.helper.content')->getCacheExpireDate()) {
            $this->setViewExpireDate($expire);
            $params['x-cache-for'] = $expire;
        }

        $params['contents'] = $items;
    }
}
