<?php

namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Displays an obituary or a list of obituaries.
 */
class ObituaryController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'    => 'obituary-frontpage',
        'show'    => 'obituary-inner',
        'showamp' => 'obituary-inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.obituaries';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'    => 'obituary_frontpage',
        'show'    => 'obituary_inner',
        'showamp' => 'amp_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'obituary_inner' => [ 7 ]
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list'    => [ 'page', 'category_slug' ],
        'showamp' => [ '_format' ],
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_obituaries'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.obituary';

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'    => 'obituary/obituary_frontpage.tpl',
        'show'    => 'obituary/obituary.tpl',
        'showamp' => 'amp/content.tpl',
    ];

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

        $response = $this->get($this->service)->getList(sprintf(
            'content_type_name="obituary" and content_status=1 and in_litter=0 '
            . 'and (starttime is null or starttime < "%s") '
            . 'and (endtime is null or endtime > "%s") '
            . 'order by starttime desc limit %d offset %d',
            $date,
            $date,
            $params['epp'],
            $params['epp'] * ($params['page'] - 1)
        ));

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $params = array_merge($params, [
            'obituaries'    => $response['items'],
            'total'         => $response['total'],
            'pagination'    => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $params['epp'],
                'page'        => $params['page'],
                'total'       => $response['total'],
                'route'       => [
                    'name'   => 'frontend_obituaries_frontpage'
                ]
            ])
        ]);
    }
}
