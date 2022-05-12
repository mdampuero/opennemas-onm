<?php

namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CompanyController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list' => 'company-frontpage',
        'show' => 'company-inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list' => 'company_frontpage',
        'show' => 'company_inner'
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
        'list' => [ 'page', 'sector', 'title' ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $routes = [
        'list' => 'frontend_companies',
        'show' => 'frontend_company_show'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.company';

    /**
     * {@inheritdoc}
     */
    protected $templates = [
        'list' => 'company/list.tpl',
        'show' => 'company/item.tpl'
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.companies';

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $date  = date('Y-m-d H:i:s');
        $query = [ $date, $date ];

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $oql = 'content_type_name = "company" and in_litter = 0 and content_status = 1 ' .
            'and (starttime is null or starttime < "%s") ' .
            'and (endtime is null or endtime >= "%s") ';

        foreach ([ 'sector', 'title' ] as $param) {
            if (!empty($params[$param])) {
                $oql .= 'and ' . $param . ' = "%s" ';
                array_push($query, $params[$param]);
            }
        }

        $query = array_merge($query, [ $params['epp'], $params['epp'] * ($params['page'] - 1) ]);

        $oql .= 'order by title desc limit %d offset %d';

        $response = $this->get('api.service.content')->getList(sprintf($oql, ...$query));

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        $params['contents']   = $response['items'];
        $params['pagination'] = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $params['epp'],
            'page'        => $params['page'],
            'total'       => $response['total'],
            'route'       => 'frontend_companies'
        ]);

        $params['tags'] = $this->getTags($response['items']);
    }
}
