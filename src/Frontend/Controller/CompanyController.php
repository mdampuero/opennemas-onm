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
        $query = [ $date, $date, $params['epp'], $params['epp'] * ($params['page'] - 1) ];

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $oql = 'content_type_name = "company" and in_litter = 0 and content_status = 1 ' .
            'and (starttime is null or starttime < "%s") ' .
            'and (endtime is null or endtime >= "%s") ';

        if (!empty($params['title'])) {
            $oql .= 'and title ~ "%%' . $params['title'] . '%%" ';
        }

        if (!empty($params['sector'])) {
            $oql .= sprintf('and sector = "%s" ', $params['sector']);
        }

        $oql .= 'order by title asc limit %d offset %d';

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

        $params['sectors'] = [
            [ 'name' => 'automobiles', 'title' => _('Automobiles') ],
            [ 'name' => 'aeroespace', 'title' => _('Aerospace') ],
            [ 'name' => 'agriculture', 'title' => _('Agriculture') ],
            [ 'name' => 'breeding', 'title' => _('Breeding') ],
            [ 'name' => 'comerce', 'title' => _('Comerce') ],
            [ 'name' => 'construction', 'title' => _('Construction') ],
            [ 'name' => 'creative', 'title' => _('Creative') ],
            [ 'name' => 'culture', 'title' => _('Culture') ],
            [ 'name' => 'education', 'title' => _('Education') ],
            [ 'name' => 'electronic', 'title' => _('Electronic') ],
            [ 'name' => 'energy', 'title' => _('Energy') ],
            [ 'name' => 'entertainment', 'title' => _('Entertainment') ],
            [ 'name' => 'finance', 'title' => _('Finance') ],
            [ 'name' => 'fishing', 'title' => _('Fishing') ],
            [ 'name' => 'food', 'title' => _('Food') ],
            [ 'name' => 'healthcare', 'title' => _('Healthcare') ],
            [ 'name' => 'information_technology', 'title' => _('Information technology') ],
            [ 'name' => 'meat', 'title' => _('Meat') ],
            [ 'name' => 'mining', 'title' => _('Mining') ],
            [ 'name' => 'petroleum', 'title' => _('Petroleum') ],
            [ 'name' => 'pharmaceutical', 'title' => _('Pharmaceutical') ],
            [ 'name' => 'real_estate', 'title' => _('Real estate') ],
            [ 'name' => 'telecommunications', 'title' => _('Telecommunications') ],
            [ 'name' => 'tobacco', 'title' => _('Tobacco') ],
            [ 'name' => 'textile', 'title' => _('Textile') ],
            [ 'name' => 'transport', 'title' => _('Transport') ],
            [ 'name' => 'wood', 'title' => _('Wood') ],
            [ 'name' => 'other', 'title' => _('Other') ]
        ];

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
