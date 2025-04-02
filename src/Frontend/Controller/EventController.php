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
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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
        'taglist' => 'articles',
        'typelist' => 'articles',
        'show' => 'articles'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list' => 'article_inner',
        'taglist' => 'article_inner',
        'typelist' => 'article_inner',
        'show' => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'list' => [ 1, 2, 5, 6, 7 ],
        'taglist' => [ 1, 2, 5, 6, 7 ],
        'typelist' => [ 1, 2, 5, 6, 7 ],
        'show' => [ 1, 2, 5, 6, 7 ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $queries = [
        'list' => [ 'page' ],
        'taglist'  => [ 'page', 'type', 'tag' ],
        'typelist' => [ 'page', 'type' ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'list'      => 'frontend_events',
        'taglist'  => 'frontend_events_tag_list',
        'typelist' => 'frontend_events_list',
        'show'      => 'frontend_event_show'
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
        'taglist'  => 'event/list.tpl',
        'typelist' => 'event/list.tpl',
        'show' => 'event/item.tpl'
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.events';

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
        $date     = gmdate('Y-m-d H:i:s');
        $type     = $params['type'] ?? null;
        $tag      = $params['tag'] ?? null;
        $ch       = $this->get('core.helper.event');
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('event_settings', false);

        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')) {
            throw new ResourceNotFoundException();
        }

        $baseOql = sprintf(
            'FROM contents'
            . ' inner join contentmeta as cm1 on contents.pk_content = cm1.fk_content '
            . ' and cm1.meta_name = "event_start_date" '
            . ' left join contentmeta as cm2 on contents.pk_content = cm2.fk_content '
            . ' and cm2.meta_name = "event_end_date" '
        );

        // Check for event type, category or tag on first url parameter
        if (!empty($type)) {
            if ($ch->matchType($type)) {
                $baseOql .= sprintf(
                    'join contentmeta as cm3 on contents.pk_content = cm3.fk_content '
                    . 'AND cm3.meta_name = "event_type" AND cm3.meta_value = "%s" ',
                    $type
                );
            } elseif ($category = $this->matchCategory($type)) {
                $baseOql .= sprintf(
                    'join content_category cc on contents.pk_content = cc.content_id '
                    . 'and cc.category_id = %d ',
                    $category->id
                );
            } elseif (empty($tag) && $tagItem = $this->matchTag($type)) {
                $baseOql .= $this->buildTagJoin($tagItem);
            } else {
                throw new ResourceNotFoundException();
            }
        }

        // Check for tag when second parameter
        if (!empty($tag) && $tagItem = $this->matchTag($tag)) {
            $baseOql .= $this->buildTagJoin($tagItem);
        }

        $baseOql .= sprintf(
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
            $baseOql .= sprintf(
                'and (cm1.meta_value >= "%s") ',
                gmdate('Y-m-d'),
                gmdate('Y-m-d')
            );
        }

        $pagination = sprintf(
            'order by cm1.meta_value asc limit %d offset %d',
            $params['epp'],
            $params['epp'] * ($params['page'] - 1)
        );

        $dataOql = sprintf(
            'SELECT * ' . $baseOql . ' %s',
            $pagination
        );

        $count = 'SELECT COUNT(*) AS total ' . $baseOql . ' order by cm1.meta_value asc';
        $total = $this->get('orm.manager')->getConnection('instance')->executeQuery($count)->fetchAll();
        $items = $this->get('api.service.content')->getListBySql($dataOql)['items'];

        // No first page and no contents
        if ($params['page'] > 1 && empty($items)) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);
            $params['x-cache-for'] = $expire;
        }

        $params['tag']        = $tagItem->name ?? null;
        $params['x-tags']    .= ',event-frontpage';
        $params['contents']   = $items;
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $params['epp'],
            'page'        => $params['page'],
            'total'       => $total[0]['total'],
            'route'       => [
                'name' => 'frontend_events_list',
                'params' => [ 'type' => $type, 'tag'  => $tag ]
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
