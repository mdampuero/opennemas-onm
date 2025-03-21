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
use Api\Exception\GetListException;
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
        $ch       = $this->get('core.helper.content');

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

        if (!empty($type)) {
            if ($ch->matchEventType($type)) {
                $oql .= sprintf(
                    'join contentmeta as cm3 on contents.pk_content = cm3.fk_content '
                    . 'AND cm3.meta_name = "event_type" AND cm3.meta_value = "%s" ',
                    $type
                );
            } elseif ($this->matchCategory($type)) {
                $oql .= $this->buildCategoryJoin($type);
            } elseif (empty($tags)) {
                $oql .= $this->buildTagJoin($type);

                $tagsName = $this->matchTag($type)->name;
            }
        }

        if (!empty($tags)) {
            $oql .= $this->buildTagJoin($tags);

            $tagsName = $this->matchTag($tags)->name;
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

        $route = [
            'name' => 'frontend_events_list',
            'params' => [
                'type' => $type,
                'tag'  => $tags
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

        $params['tag'] = $tagsName ?? null;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildCategoryJoin($type)
    {
        $matchCategory = $this->matchCategory($type);

        return sprintf(
            'join content_category cc on contents.pk_content = cc.content_id '
            . 'and cc.category_id = %d ',
            $matchCategory->id
        );
    }

    /**
     * {@inheritdoc}
     */
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

            return !empty($category['items']) ? $category['items'][0] : null;
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
            $tags = $this->get('api.service.tag')->getItemBy($oql);

            return !empty($tags) ? $tags : null;
        } catch (GetItemException $e) {
            return false;
        }
    }
}
