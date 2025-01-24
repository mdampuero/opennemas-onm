<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class AuthorController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list' => 'frontpages',
        'show' => 'frontpages'
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.tags';

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'list' => [ 7, 9 ],
        'show' => [ 7, 9 ]
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list' => [ 'page' ]
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_frontpage_authors'
    ];

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'    => 'user/frontpage_authors.tpl'
    ];

    /**
     * {@inheritdoc}
     */
    public function listAction(Request $request)
    {
        $this->checkSecurity('es.openhost.module.tagsIndex');

        return parent::listAction($request);
    }

    /**
     * {@inheritdoc}
     */
    public function showAction(Request $request)
    {
        return parent::showAction($request);
    }

    protected function getParameters($request, $item = null)
    {
        $action = $this->get('core.globals')->getAction();
        $params = parent::getParameters($request, $item[0]);

        unset($params['o_content']);
        unset($params['content']);

        return array_merge($params, [
            'author' => $item[0],
            'o_canonical' => $this->getCanonicalUrl($action, $params),
        ]);
    }


    protected function getItems($params)
    {
        $itemsPerPage = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);

        $oql = sprintf(
            'select SQL_CALC_FOUND_ROWS contents.fk_author as id, count(pk_content) as total from contents' .
            ' where contents.fk_author in (select users.id from users)' .
            ' and fk_content_type in (1, 4, 7, 9) and content_status = 1 and in_litter != 1' .
            ' group by contents.fk_author order by total desc',
        );

        $response = $this->get('api.service.author')->getListBySql($oql);

        $items = $response['items'];
        $total = count($items);

        $items = array_slice(
            $items,
            $itemsPerPage * ($params['page'] - 1),
            $itemsPerPage
        );
        return [
            $items,
            $total
        ];
    }

    protected function hydrateList(array &$params = []) : void
    {
        $itemsPerPage = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);

        if ($params['page'] <= 0 || $params['page'] > $this->getParameter('core.max_page')) {
            throw new ResourceNotFoundException();
        }

        try {
            list($items, $total) = $this->getItems($params);
        } catch (GetListException $e) {
            throw new ResourceNotFoundException();
        }

        $params['authors_contents'] = $items;
        $params['total']            = $total;
        $params['pagination']       = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => 'frontend_frontpage_authors',
        ]);
    }
}
