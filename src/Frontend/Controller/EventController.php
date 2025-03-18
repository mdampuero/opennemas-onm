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
        'list' => [ 'page' ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'list'    => 'frontend_events',
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
        $category = isset($params['category']) ? $params['category'] : null;
        $tags     = isset($params['tag']) ? $params['tag'] : null;

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

        // Check if the category is not empty.
        if (!empty($category)) {
            // Match the category using the provided slug.
            $matchCategory = $this->matchCategory($params['category']);

            // If it's a Category entity, join the content_category table.
            if ($matchCategory instanceof \Common\Model\Entity\Category) {
                $oql .= sprintf(
                    'JOIN content_category cc ON contents.pk_content = cc.content_id '
                    . 'AND cc.category_id = %d ',
                    $matchCategory->id
                );
            // If it's a Tag entity, join the contents_tags table.
            } elseif ($matchCategory instanceof \Common\Model\Entity\Tag) {
                $oql .= sprintf(
                    'JOIN contents_tags ct ON contents.pk_content = ct.content_id '
                    . 'AND ct.tag_id = %d ',
                    $matchCategory->id
                );
            }
        }

        // Check if tags are not empty.
        if (!empty($tags)) {
            // Match the tags using the provided slug.
            $matchTags = $this->matchTag($params['tags']);

            // Join the contents_tags table using the matched tag's ID.
            $oql .= sprintf(
                'JOIN contents_tags ct ON contents.pk_content = ct.content_id '
                . 'AND ct.tag_id IN [%d] ',
                $matchTags->id
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
            $category = $categoryService->getItemBy($oql);

            return $category;
        } catch (GetItemException $e) {
            return $this->matchTag($slug);
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

            return $tags;
        } catch (GetItemException $e) {
            return;
        }
    }
}
