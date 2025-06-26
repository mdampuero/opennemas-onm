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
use DateTime;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
        'list'             => 'articles',
        'taglist'          => 'articles',
        'typelist'         => 'articles',
        'categorylist'     => 'articles',
        'categorytaglist'  => 'articles',
        'categorytypelist' => 'articles',
        'show'             => 'articles'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'             => 'article_inner',
        'taglist'          => 'article_inner',
        'typelist'         => 'article_inner',
        'categorylist'     => 'article_inner',
        'categorytaglist'  => 'article_inner',
        'categorytypelist' => 'article_inner',
        'show'             => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'list'             => [ 1, 2, 5, 6, 7 ],
        'taglist'          => [ 1, 2, 5, 6, 7 ],
        'typelist'         => [ 1, 2, 5, 6, 7 ],
        'categorylist'     => [ 1, 2, 5, 6, 7 ],
        'categorytaglist'  => [ 1, 2, 5, 6, 7 ],
        'categorytypelist' => [ 1, 2, 5, 6, 7 ],
        'show'             => [ 1, 2, 5, 6, 7 ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $queries = [
        'list'             => [ 'page' ],
        'taglist'          => [ 'page', 'type', 'tag' ],
        'typelist'         => [ 'page', 'type' ],
        'categorylist'     => [ 'page', 'category_slug'],
        'categorytaglist'  => [ 'page', 'category_slug', 'type', 'tag' ],
        'categorytypelist' => [ 'page', 'category_slug', 'type' ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'list'             => 'frontend_events',
        'taglist'          => 'frontend_events_tag_list',
        'typelist'         => 'frontend_events_list',
        'categorylist'     => 'frontend_events_category',
        'categorytaglist'  => 'frontend_events_category_tag_list',
        'categorytypelist' => 'frontend_events_category_list',
        'show'             => 'frontend_event_show'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.event';

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list'             => 'event/list.tpl',
        'taglist'          => 'event/list.tpl',
        'typelist'         => 'event/list.tpl',
        'categorylist'     => 'event/list.tpl',
        'categorytaglist'  => 'event/list.tpl',
        'categorytypelist' => 'event/list.tpl',
        'show'             => 'event/item.tpl'
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.events';

    /**
     * {@inheritdoc}
     */
    public function categoryListAction(Request $request)
    {
        return parent::listAction($request);
    }

    /**
     * {@inheritdoc}
     */
    public function categoryTagListAction(Request $request)
    {
        return parent::listAction($request);
    }

    /**
     * {@inheritdoc}
     */
    public function categoryTypeListAction(Request $request)
    {
        return parent::listAction($request);
    }

    /**
     * {@inheritdoc}
     */
    public function tagListAction(Request $request)
    {
        return parent::listAction($request);
    }

    /**
     * {@inheritdoc}
     */
    public function typeListAction(Request $request)
    {
        return parent::listAction($request);
    }

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
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []): void
    {
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')) {
            throw new ResourceNotFoundException();
        }

        $action       = $this->get('core.globals')->getAction();
        $categorySlug = $params['category_slug'] ?? null;
        $tag          = $params['tag'] ?? null;
        $type         = $params['type'] ?? null;
        $settings     = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('event_settings', false);

        // Set dates
        $eventDate     = date('Y-m-d');
        $publishedDate = gmdate('Y-m-d H:i:s');

        // Flag for canonical url when duplicated list
        $changeCanonical = false;

        $baseSql = sprintf(
            'FROM contents '
            . 'inner join contentmeta as start_date_meta on contents.pk_content = start_date_meta.fk_content '
            . 'and start_date_meta.meta_name = "event_start_date" '
            . 'left join contentmeta as start_hour_meta on contents.pk_content = start_hour_meta.fk_content '
            . 'and start_hour_meta.meta_name = "event_start_hour" '
            . 'left join contentmeta as end_date_meta on contents.pk_content = end_date_meta.fk_content '
            . 'and end_date_meta.meta_name = "event_end_date" '
        );

        // Check for category at the start of the path
        if (!empty($categorySlug) && $category = $this->matchCategory($categorySlug)) {
            $baseSql .= sprintf(
                'join content_category on contents.pk_content = content_category.content_id '
                . 'and content_category.category_id = %d ',
                $category->id
            );
        }

        // Check for event type, category as type or tag on first url parameter
        if (!empty($type)) {
            if ($eventType = $this->get('core.helper.event')->getEventTypeBySlug($type)) {
                $baseSql .= sprintf(
                    'join contentmeta as event_type_meta on contents.pk_content = event_type_meta.fk_content '
                    . 'AND event_type_meta.meta_name = "event_type" AND event_type_meta.meta_value = "%s" ',
                    $eventType['id']
                );
            } elseif (empty($categorySlug) && $category = $this->matchCategory($type)) {
                $changeCanonical = true;

                $baseSql .= sprintf(
                    'join content_category on contents.pk_content = content_category.content_id '
                    . 'and content_category.category_id = %d ',
                    $category->id
                );
            } elseif (empty($tag) && $tagItem = $this->matchTag($type)) {
                $baseSql .= $this->buildTagJoin($tagItem);
            } else {
                throw new ResourceNotFoundException();
            }
        }

        // Check for tag when second parameter
        if (!empty($tag) && $tagItem = $this->matchTag($tag)) {
            $baseSql .= $this->buildTagJoin($tagItem);
        }

        $baseSql .= sprintf(
            'where content_type_name="event" and content_status=1 and in_litter=0 '
            . 'and (start_date_meta.meta_value >= "%s" '
            . 'or (start_date_meta.meta_value < "%s" and end_date_meta.meta_value >= "%s")) '
            . 'and (starttime is null or starttime < "%s") '
            . 'and (endtime is null or endtime > "%s") ',
            $eventDate,
            $eventDate,
            $eventDate,
            $publishedDate,
            $publishedDate
        );

        if ($settings["hide_current_events"] ?? false) {
            $months = empty(!$settings['hide_events_month'])
                ? $settings['hide_events_month']
                : 0;


            // Calculate the date to hide events
            $hideDate = (new DateTime($eventDate))->modify("-$months months")->format('Y-m-d');

            $baseSql .= sprintf(
                'and (start_date_meta.meta_value >= "%s") ',
                $hideDate
            );
        }

        $pagination = sprintf(
            'order by start_date_meta.meta_value asc, '
            . 'start_hour_meta.meta_value asc, '
            . 'contents.pk_content asc '
            . 'limit %d offset %d',
            $params['epp'],
            $params['epp'] * ($params['page'] - 1)
        );

        $sql = sprintf(
            'select * ' . $baseSql . ' %s',
            $pagination
        );

        $count = 'select count(*) as total ' . $baseSql;
        $total = $this->get('orm.manager')->getConnection('instance')->executeQuery($count)->fetchAll();
        $items = $this->get('api.service.content')->getListBySql($sql)['items'];

        // No first page and no contents
        if ($params['page'] > 1 && empty($items)) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);
            $params['x-cache-for'] = $expire;
        }

        // Set canonical for pages with same content but diferent uri
        if ($changeCanonical) {
            $canonicalAction = [
                'taglist'  => 'categorytypelist',
                'typelist' => 'categorylist'
            ];

            $canonicalParams = [
                'category_slug' => $params['type'] ?? null,
                'type'          => $params['tag'] ?? null
            ];

            $params['o_canonical'] = $this->getCanonicalUrl(
                $canonicalAction[$action],
                array_filter($canonicalParams)
            );
        }

        $params['tag']        = $tagItem->name ?? null;
        $params['x-tags']    .= ',event-frontpage';
        $params['o_category'] = $category ?? null;
        $params['contents']   = $items;
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $params['epp'],
            'page'        => $params['page'],
            'total'       => $total[0]['total'],
            'route'       => [
                'name' => $this->routes[$action],
                'params' => [
                    'type' => $type,
                    'tag'  => $tag,
                    'category_slug' => $category->name ?? null
                ]
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildTagJoin($tagItem)
    {
        return sprintf(
            'join contents_tags ct on contents.pk_content = ct.content_id '
            . 'and ct.tag_id in (%s) ',
            $tagItem->id
        );
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
        try {
            $category = $this->get('api.service.category')->getItemBySlug($slug);

            return !empty($category) ? $category : null;
        } catch (GetItemException $e) {
            return null;
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
        try {
            $oql = sprintf('slug = "%s"', $slug);

            $tag = $this->get('api.service.tag')->getList($oql)['items'];

            return !empty($tag) ? $tag[0] : null;
        } catch (GetListException $e) {
            return null;
        }
    }
}
