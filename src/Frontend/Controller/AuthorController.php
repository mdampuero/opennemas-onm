<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Doctrine\ORM\Query\Expr\Func;
use SebastianBergmann\Environment\Console;

/**
 * Class AuthorController
 *
 * Handles the display and management of authors and their associated content on the frontend.
 */
class AuthorController extends FrontendController
{
    /**
     * Cache configuration per action.
     *
     * @var array
     */
    protected $caches = [
        'list' => 'frontpages',
        'show' => 'frontpages'
    ];

    /**
     * Positions configuration per action.
     *
     * @var array
     */
    protected $positions = [
        'list' => [ 7, 9 ],
        'show' => [ 7, 9 ]
    ];

    /**
     * Valid query parameters for each action.
     *
     * @var array
     */
    protected $queries = [
        'list' => [ 'page' ],
        'show' => [ 'page', 'author_slug' ]
    ];

    /**
     * Route configuration per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_frontpage_authors',
        'show' => 'frontend_author_frontpage'
    ];

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'    => 'user/frontpage_authors.tpl',
        'show'    => 'user/author_frontpage.tpl'
    ];

    /**
     * Displays a list of authors.
     *
     * @param Request $request The HTTP request object.
     * @return mixed The response object from the parent class.
     */
    public function listAction(Request $request)
    {
        $action = $this->get('core.globals')->getAction();
        $params = $request->query->all();

        $expected = $this->getExpectedUri($action, $params);

        if (strpos($request->getRequestUri(), $expected) === false) {
            return new RedirectResponse($expected, 301);
        }

        $params = $this->getParameters($request);

        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateList($params);
        }

        return $this->render(
            $this->getTemplate($action),
            $params
        );
    }

    /**
     * Displays the content associated with a specific author.
     *
     * @param Request $request The HTTP request object.
     * @return RedirectResponse|mixed The rendered template or a redirect response.
     * @throws ResourceNotFoundException If the page or author cannot be found.
     */
    public function showAction(Request $request)
    {
        $action       = $this->get('core.globals')->getAction();
        $itemsPerPage = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);
        $item         = $this->getItem($request, $itemsPerPage);
        $params       = $request->query->all();
        $xtags        = [];
        $slug         = $request->query->filter('author_slug', '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $expected = $this->getExpectedUri($action, $params);

        if (strpos($request->getRequestUri(), $expected) === false) {
            return new RedirectResponse($expected, 301);
        }

        $params                   = $this->getParameters($request, $item);
        $params['item_id']        = $item[0]->id;
        $params['items_per_page'] = $itemsPerPage;
        $params['slug']           = $slug;

        $this->view->setConfig('articles');

        if (!$this->isCached($params)) {
            $this->hydrateShow($params);
        }

        $params['x-tags'] .= implode(',', array_unique($xtags));

        return $this->render(
            $this->getTemplate($action),
            $params
        );
    }

    protected function hydrateShow(array &$params = []) : void
    {
        $params['epp'] = $params['items_per_page'];

        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $service = $this->get('api.service.content');

        $response = $service->getList(
            sprintf(
                'fk_author = %d and fk_content_type in [1, 4, 7, 9] and content_status = 1 and in_litter = 0 ' .
                'and ((starttime is null or starttime <= "%s") and (endtime is null or endtime > "%s"))' .
                'limit %d offset %d',
                $params['item_id'],
                gmdate('Y-m-d H:i:s'),
                gmdate('Y-m-d H:i:s'),
                $params['epp'],
                $params['epp'] * ($params['page'] - 1)
            )
        );

        $contents = $response['items'];
        $total    = $response['total'];

        // No first page and no contents
        if ($params['page'] > 1 && empty($contents)) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        $params = array_merge($params, [
            'contents'   => $contents,
            'total'      => $total,
            'tag'        => $params['item'],
            'pagination' => $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $params['epp'],
                'maxLinks'    => 5,
                'page'        => $params['page'],
                'total'       => $total,
                'route'       => [
                    'name'   => 'frontend_author_frontpage',
                    'params' => [ 'author_slug' => $params['slug']]
                ]
            ])
        ]);
    }

    /**
     * Checks if the response for the current request is already cached basing
     * on all parameters provided.
     *
     * @param array $params The list of parameters.
     *
     * @return boolean True if the response is already cached. False otherwise.
     */
    protected function isCached($params)
    {
        return array_key_exists('cache_id', $params)
            && !empty($this->view->getCaching())
            && $this->view->isCached(
                $this->getTemplate($this->get('core.globals')->getAction()),
                $params['cache_id']
            );
    }

    /**
     * Retrieves the author item based on the provided slug.
     *
     * @param Request $request The HTTP request object.
     * @return array The retrieved author item.
     * @throws ResourceNotFoundException If the author cannot be found.
     */
    protected function getItem(Request $request)
    {
        $slug = $request->query->filter('author_slug', '', FILTER_SANITIZE_STRING);

        try {
            $item = $this->get('api.service.author')
                ->getItemBy("username = '$slug' or slug = '$slug'");
        } catch (GetItemException $e) {
            throw new ResourceNotFoundException();
        }

        return [ $item ];
    }

    /**
     * Retrieves and merges parameters for the specified action.
     *
     * @param Request $request The HTTP request object.
     * @param mixed|null $item The associated author item (if any).
     * @return array The merged parameters.
     */
    protected function getParameters($request, $item = null)
    {
        $action = $this->get('core.globals')->getAction();
        $params = parent::getParameters($request, $item[0]);

        unset($params['o_content']);
        unset($params['content']);

        $this->getAdvertisements();

        return array_merge($params, [
            'author' => $item[0],
            'authors' => $item,
            'o_canonical' => $this->getCanonicalUrl($action, $params),
        ]);
    }

    /**
     * Retrieves a list of author items with pagination.
     *
     * @param array $params The query parameters.
     * @param int $epp The number of items per page.
     * @return array An array containing the paginated items and total count.
     * @throws ResourceNotFoundException If the list cannot be retrieved.
     */
    protected function getItems($params, $epp)
    {
        $offset = ($params['page'] - 1) * $epp;

        $oql = sprintf(
            'SELECT SQL_CALC_FOUND_ROWS contents.fk_author as id, count(pk_content) as total_content
            FROM contents
            WHERE contents.fk_author IN (SELECT users.id FROM users)
            AND fk_content_type IN (1, 4, 7, 9)
            AND content_status = 1
            AND in_litter != 1
            GROUP BY contents.fk_author
            ORDER BY total_content DESC
            LIMIT %d OFFSET %d',
            $epp,
            $offset
        );

        $items = $this->get('dbal_connection')->fetchAll($oql);

        // Obtener el total de elementos sin paginación
        $totalQuery = 'SELECT FOUND_ROWS()';
        $total      = $this->get('dbal_connection')->fetchAssoc($totalQuery);
        $total      = array_pop($total);

        // Obtener información de los autores
        $authorIds = array_column($items, 'id');
        $response  = $this->get('api.service.author')->getListByIds($authorIds);

        $authors = $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', ['key' => 'id'])
            ->get();

        // Agregar total_contents a cada autor
        $items = array_map(function ($item) use ($authors) {
            if (isset($authors[$item['id']])) {
                $author                 = $authors[$item['id']];
                $author->total_contents = $item['total_content'];
                return $author;
            }
            return null;
        }, $items);

        $items = array_filter($items); // Eliminar autores nulos

        return [
            $items,
            $total
        ];
    }


    /**
     * Hydrates the list of authors for display on the frontpage.
     *
     * @param array $params Query parameters for fetching authors.
     * @return void
     * @throws ResourceNotFoundException If the authors or page are invalid.
     */
    protected function hydrateList(array &$params = []) : void
    {
        $itemsPerPage = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);

        if ($params['page'] <= 0 || $params['page'] > $this->getParameter('core.max_page')) {
            throw new ResourceNotFoundException();
        }

        try {
            list($items, $total) = $this->getItems($params, $itemsPerPage);
        } catch (GetListException $e) {
            throw new ResourceNotFoundException();
        }

        $params['authors_contents'] = $items;
        $params['total_contents'] = $total;
        $params['pagination']       = $this->get('paginator')->get([
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $params['page'],
            'total'       => $total,
            'route'       => 'frontend_frontpage_authors',
        ]);
    }

    /**
     * Retrieves and configures advertisements for the specified category and action.
     *
     * This method fetches advertisement positions based on the current action,
     * combines them with additional predefined positions, and retrieves the
     * corresponding advertisements from the repository. These advertisements
     * are then configured for rendering on the frontend.
     *
     * @param object|null $category The category object to filter advertisements by, or null for no filtering.
     * @param string|null $token An optional token for additional advertisement context (currently unused).
     * @return void
     */
    protected function getAdvertisements($category = null, $token = null)
    {
        $categoryId = empty($category) ? 0 : $category->id;
        $action     = $this->get('core.globals')->getAction();
        $group      = $this->getAdvertisementGroup($action);

        $positions = array_merge(
            $this->get('core.helper.advertisement')->getPositionsForGroup('all'),
            $this->get('core.helper.advertisement')->getPositionsForGroup($group),
            $this->get('core.helper.advertisement')->getPositionsForGroup('article_inner'),
            $this->getAdvertisementPositions($group)
        );

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $categoryId);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
    }
}
