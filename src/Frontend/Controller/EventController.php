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
use Doctrine\ORM\Query\Expr\Func;
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
        'show'    => 'articles'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'    => 'article_inner',
        'show'    => 'article_inner'
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
        'list' => [ 'page', 'type', 'tag' ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'list'    => 'frontend_events_list',
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
        $type     = isset($params['type']) ? $params['type'] : null;
        $tags     = isset($params['tag']) ? $params['tag'] : null;

        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')) {
            throw new ResourceNotFoundException();
        }

        // Base de la consulta con INNER JOINs esenciales
        $oql = sprintf(
            'select * from contents '
            . 'inner join contentmeta as cm1 on contents.pk_content = cm1.fk_content '
            . 'and cm1.meta_name = "event_start_date" '
            . 'left join contentmeta as cm2 on contents.pk_content = cm2.fk_content '
            . 'and cm2.meta_name = "event_end_date" '
        );

        if (!empty($type) || !empty($tags)) {
            if (!empty($type)) {
                if ($this->matchEventType($type)) {
                    $oql .= sprintf(
                        'inner join contentmeta as cm3 on contents.pk_content = cm3.fk_content '
                        . 'AND cm3.meta_name = "event_type" AND cm3.meta_value = "%s" ',
                        addslashes($type) // Protección contra SQL Injection
                    );
                } elseif ($this->matchCategory($type)) {
                    $oql .= $this->buildCategoryJoin($type);
                } elseif (empty($tags)) {
                    // Solo si no hay tags, se trata como una etiqueta.
                    $oql .= $this->buildTagJoin($type);
                }
            }
        }

        $oql .= sprintf(
            'where content_type_name="event" and content_status=1 and in_litter=0 '
            . 'and (cm1.meta_value >= "%s" or (cm1.meta_value < "%s" and cm2.meta_value >= "%s")) '
            . 'and (starttime is null or starttime < "%s") '
            . 'and (endtime is null or endtime > "%s") ',
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

        $routeType = $type ?? '';
        $routeTag  = $tags ?? '';

        $route = [
            'name' => 'frontend_events_list',
            'params' => [
                'type' => $routeType, // even-type, category or tag
                'tag'  => $routeTag // tag
            ]
        ];

        /// local.domain/event/lugo (category)/tag --> correcta
        /// local.domain/event/tag (tag)/lugo/ --> incorrecta (2 parametros) -> no tag
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

    /**
     * {@inheritdoc}
     */
    private function buildCategoryJoin($type)
    {
        return sprintf(
            'join content_category cc on contents.pk_content = cc.content_id '
            . 'and cc.category_id = %d ',
            $matchCategory->id
        );
    }

    protected function buildTagJoin($tags)
    {
        $tagsArray = explode(',', $tags);
        $tagIds    = array_filter(array_map(function ($tag) {
            $matchTags = $this->matchTag($tag);
            return $matchTags ? $matchTags->id : null;
        }, $tagsArray));

        if ($tagIds) {
            return sprintf(
                'join contents_tags ct on contents.pk_content = ct.content_id '
                . 'and ct.tag_id in (%s) ',
                implode(',', $tagIds)
            );
        }
        return '';
    }

    protected function matchEventType(string $slug): bool
    {
        $coreInstance = $this->container->get('core.instance');
        $eventService = $this->get('api.service.event');

        $oql = $coreInstance->hasMultilanguage()
            ? sprintf('event_type regexp "(.+\"|^)%s(\".+|$)"', $slug)
            : sprintf('event_type = "%s"', $slug);

        try {
            $event = $eventService->getList($oql);

            return !empty($event['items']);
        } catch (GetItemException $e) {
            return false;
        }
    }


    /**
     * Matches a category by its slug.
     *
     * This method checks if the category name matches the given slug, considering
     * whether multilanguage support is enabled. If no category is found, it falls back
     * to matching tags.
     *
     * @param string $slug The slug to match.
     *
     * @return mixed|null The matched category, or null if not found.
     */
    protected function matchCategory(string $slug)
    {
        $coreInstance    = $this->container->get('core.instance');
        $categoryService = $this->get('api.service.category');

        $oql = $coreInstance->hasMultilanguage()
            ? sprintf('name regexp "(.+\"|^)%s(\".+|$)"', $slug)
            : sprintf('name = "%s"', $slug);

        try {
            $category = $categoryService->getList($oql);

            return !empty($category['items']);
        } catch (GetItemException $e) {
            return false;
        }
    }

    /**
     * Matches a tag by its slug.
     *
     * This method attempts to find a tag based on the given slug.
     * If no tag is found, the method returns null.
     *
     * @param string $slug The slug to match.
     *
     * @return mixed|null The matched tags, or null if not found.
     */
    protected function matchTag(string $slug)
    {
        $oql = sprintf('slug in ["%s"]', $slug);

        try {
            $tags = $this->get('api.service.tag')->getList($oql);

            return !empty($tags['items']);
        } catch (GetItemException $e) {
            return false;
        }
    }

    protected function isValidResource($categoryParam, $tagsParam, $category, $tags)
    {
        if (empty($category) && empty($tags) && (!empty($categoryParam) || !empty($tagsParam))) {
            return false;
        }
        if (!empty($categoryParam) && !empty($tagsParam) && (empty($category) || empty($tags))) {
            return false;
        }
        return true;
    }
}
