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
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $date = date('Y-m-d H:i:s');

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $response = $this->get('api.service.content')->getListBySql(sprintf(
            'select * from contents '
            . 'inner join contentmeta '
            . 'on contents.pk_content = contentmeta.fk_content '
            . 'and contentmeta.meta_name = "event_start_date"'
            . 'where content_type_name="event" and content_status=1 and in_litter=0 '
            . 'and (starttime is null or starttime < "%s") '
            . 'and (endtime is null or endtime > "%s") '
            . 'order by meta_value desc limit %d offset %d',
            $date,
            $date,
            $params['epp'],
            $params['epp'] * ($params['page'] - 1)
        ));

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        $params['x-tags'] .= ',event-frontpage';

        $params['contents']   = $response['items'];
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'boundary'    => false,
            'epp'         => $params['epp'],
            'maxLinks'    => 5,
            'page'        => $params['page'],
            'total'       => $response['total'],
            'route'       => 'frontend_events',
        ]);

        $params['tags'] = $this->getTags($response['items']);
    }
}
