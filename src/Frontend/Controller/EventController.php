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

        if (!$this->get('core.helper.content')->isReadyForPublish($item)) {
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
        $offset = $params['epp'] * ($params['page'] - 1);

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
    protected function hydrateList(array &$params = []) : void
    {
        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        list($contents, $total) = $this->getItems($params);

        // No first page and no contents
        if ($params['page'] > 1 && $total < $params['epp'] * $params['page']) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
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

        $params['tags'] = $this->getTags($contents);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []) : void
    {
        $params['tags'] = $this->getTags($params['content']);
    }
}
