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

use Api\Exception\GetItemException;
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
        'list'    => 'articles',
        'show'    => 'articles',
        'tagList' => 'articles'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'    => 'article_inner',
        'show'    => 'article_inner',
        'tagList' => 'article_inner'
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
        'list'    => 'frontend_events',
        'listSearch'    => 'frontend_events_list',
        'show'    => 'frontend_event_show'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.event';

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list'    => 'event/list.tpl',
        'taglist' => 'event/list.tpl',
        'show'    => 'event/item.tpl'
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
                    'event'
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
     * Retrieves a tag item based on the slug provided in the request and the current locale.
     *
     * @param Request $request The current HTTP request object, which contains the tag slug.
     *
     * @return array The tag item data.
     *
     * @throws ResourceNotFoundException If the tag item cannot be found or an error occurs while fetching the item.
     */
    protected function getItemTag(Request $request)
    {
        try {
            $locale = $this->container->get('core.locale')->getRequestLocale();
            $tag    = $request->get('tag');

            $item = $this->get('api.service.tag')->getItemBy(sprintf(
                'slug = "%s" and (locale = "%s" or locale is null)',
                $tag,
                $locale
            ));
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }


        return $item;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []): void
    {
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('event_settings', false);
        $date     = gmdate('Y-m-d H:i:s');

        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')) {
            throw new ResourceNotFoundException();
        }

        // Base de la consulta con INNER JOINs esenciales
        $oql = sprintf(
            'SELECT * FROM contents '
            . 'INNER JOIN contentmeta AS cm1 ON contents.pk_content = cm1.fk_content '
            . 'AND cm1.meta_name = "event_start_date" '
            . 'LEFT JOIN contentmeta AS cm2 ON contents.pk_content = cm2.fk_content '
            . 'AND cm2.meta_name = "event_end_date" '
        );

        if (!empty($params['category'])) {
            $category = $this->matchCategory($params['category']);

            if ($category instanceof \Common\Model\Entity\Category
            ) {
                $oql .= sprintf(
                    'JOIN content_category cc ON contents.pk_content = cc.content_id '
                    . 'AND cc.category_id = %d ',
                    $category->id
                );
            } elseif ($category instanceof \Common\Model\Entity\Tag) {
                $oql .= sprintf(
                    'JOIN contents_tags ct ON contents.pk_content = ct.content_id '
                    . 'AND ct.tag_id = %d ',
                    $category->id
                );
            }
        }

        if (!empty($params['tags'])) {
            $tags = $this->matchTag($params['tags']);

            $oql .= sprintf(
                'JOIN contents_tags ct ON contents.pk_content = ct.content_id '
                . 'AND ct.tag_id = %d ',
                $tags->id
            );
        }


        $oql .= sprintf(
            'WHERE content_type_name="event" AND content_status=1 AND in_litter=0 '
            . 'AND (cm1.meta_value >= "%s" OR (cm1.meta_value < "%s" AND cm2.meta_value >= "%s")) '
            . 'AND (starttime IS NULL OR starttime < "%s") '
            . 'AND (endtime IS NULL OR endtime > "%s") ',
            gmdate('Y-m-d'),
            gmdate('Y-m-d'),
            gmdate('Y-m-d'),
            $date,
            $date
        );

        if ($settings["hide_current_events"] ?? false) {
            $oql .= sprintf(
                'and (cm1.meta_value >= "%s") ',
                gmdate('Y-m-d'),
                gmdate('Y-m-d')
            );
        }

        $oql .= 'order by cm1.meta_value asc';

        $response = $this->get('api.service.content')->getListBySql($oql);

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

        $route = [
            'name' => 'frontend_events',
            'params' => [
                'category' => $params['category'] ?? null,
                'tag' => $params['tag'] ?? null
            ]
        ];

        $params['x-tags'] .= ',event-frontpage';

        $params['contents']   = $items;
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $params['epp'],
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => $route
        ]);

        $params['tags'] = $this->getTags($items);
    }

    protected function matchCategory(string $slug)
    {
        $coreInstance    = $this->container->get('core.instance');
        $categoryService = $this->get('api.service.category');

        $oql = $coreInstance->hasMultilanguage()
            ? sprintf('name regexp "(.+\"|^)%s(\".+|$)"', $slug)
            : sprintf('name = "%s"', $slug);

        try {
            $category = $categoryService->getItemBy($oql);

            return $category;
        } catch (GetItemException $e) {
            return $this->matchTag($slug);
        }
    }

    protected function matchTag(string $slug)
    {
        $oql = sprintf('slug = "%s"', $slug);

        try {
            $tags = $this->get('api.service.tag')->getItemBy($oql);

            return $tags;
        } catch (GetItemException $e) {
            return;
        }
    }

    /**
     * Handles the listing of tags and redirects if the request URI does not match the expected URI.
     * @param Request $request The current HTTP request object.
     * @return RedirectResponse|Response A redirect response if the URI is incorrect,
     * otherwise renders the tag listing template.
     * @throws SecurityException If the user does not have the required permissions.
     */
    public function tagListAction(Request $request)
    {
        $this->checkSecurity($this->extension);

        $action = $this->get('core.globals')->getAction();
        $params = $request->query->all();
        $item   = $this->getItemTag($request);

        $expected = $this->getExpectedUri($action, $params);

        if ($request->getRequestUri() !== $expected) {
            return new RedirectResponse($expected, 301);
        }

        $params           = $this->getParameters($request, $item);
        $params['x-tags'] = sprintf('%s,event-frontpage-tag', str_replace('event-', 'tag-', $params['x-tags']));

        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateListTag($params);
        }

        return $this->render($this->getTemplate($action), $params);
    }

    /**
     * Hydrates the list of tags by querying the database for content that matches specific criteria
     * and updates the given parameters array with the resulting content.
     *
     * @param array $params The parameters array, passed by reference, to be populated with content data.
     * @return void
     * @throws ResourceNotFoundException If no content is found that matches the query.
     */
    protected function hydrateListTag(array &$params = []) : void
    {
        $currentDate   = gmdate('Y-m-d');
        $fullDate      = gmdate('Y-m-d H:i:s');
        $eventSettings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('event_settings', false);

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
            AND t.slug = "%s"',
            $currentDate,
            $currentDate,
            $currentDate,
            $fullDate,
            $fullDate,
            $params['tag']
        );

        if ($eventSettings["hide_current_events"] ?? false) {
            $oql .= sprintf(
                ' AND (cm1.meta_value >= "%s")',
                gmdate('Y-m-d'),
                gmdate('Y-m-d')
            );
        }

        $oql .= ' ORDER BY cm1.meta_value ASC';

        $response = $this->get('api.service.content')->getListBySql($oql);
        $items    = $response['items'];

        if ($expire = $this->get('core.helper.content')->getCacheExpireDate()) {
            $this->setViewExpireDate($expire);
            $params['x-cache-for'] = $expire;
        }

        $params['contents'] = $items;

        // TODO: Remove this hack and update variable in templates
        $params['tag'] = $params['item']->name;
    }
}
