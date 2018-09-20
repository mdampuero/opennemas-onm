<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for the news agency module
 */
class NewsAgencyServerController extends Controller
{
    /**
     * Displays a form to create a new news-agency server.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_NEWS_AGENCY_CONFIG')")
     */
    public function createAction()
    {
        return $this->render('news_agency/config/new.tpl', [
            'authors'    => $this->getAuthors(),
            'categories' => $this->getCategories(),
            'server'     => [],
            'sync_from'  => $this->getSyncFrom()
        ]);
    }

    /**
     * Shows the list of downloaded newsfiles from Efe service
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('news_agency/config/list.tpl');
    }

    /**
     * Shows and handles the configuration form for Efe module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_NEWS_AGENCY_CONFIG')")
     */
    public function saveAction(Request $request)
    {
        $ds      = $this->get('orm.manager')->getDataSet('Settings');
        $servers = $ds->get('news_agency_config');

        if (!is_array($servers)) {
            $servers = [];
        }

        $latestServerId = count($servers) <= 0 ? 0 : max(array_keys($servers));

        $server = [
            'id'             => $latestServerId + 1,
            'name'           => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'url'            => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'username'       => $request->request->filter('username', '', FILTER_SANITIZE_STRING),
            'password'       => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
            'agency_string'  => $request->request->filter('agency_string', '', FILTER_SANITIZE_STRING),
            'external_link'  => $request->request->filter('external_link', '', FILTER_SANITIZE_STRING),
            'color'          => $request->request->filter('color', '#424E51', FILTER_SANITIZE_STRING),
            'sync_from'      => $request->request->filter('sync_from', '', FILTER_SANITIZE_STRING),
            'activated'      => $request->request->getDigits('activated', 0),
            'author'         => $request->request->getDigits('author', 0),
            'source'         => $request->request->getDigits('source', 0),
            'auto_import'    => $request->request->getDigits('auto_import', 0),
            'auto_import'    => $request->request->getDigits('auto_import', 0),
            'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
            'target_author'  => $request->request->filter('target_author', '', FILTER_SANITIZE_STRING),
            'import_related' => $request->request->filter('import_related', '', FILTER_SANITIZE_STRING),
            'filters'        => $request->request->get('filters', []),
        ];

        $servers[$server['id']] = $server;

        $ds->set('news_agency_config', $servers);

        $this->get('session')->getFlashBag()
            ->add('success', _('News agency server added.'));

        return $this->redirect($this->generateUrl(
            'backend_news_agency_server_show',
            [ 'id' => $server['id'] ]
        ));
    }

    /**
     * Shows the news agency information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_NEWS_AGENCY_CONFIG')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $servers = $this->get('orm.manager')->getDataSet('Settings')
            ->get('news_agency_config');

        if (empty($id) || !array_key_exists($id, $servers)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the news agency source with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('backend_news_agency_servers_list'));
        }

        return $this->render('news_agency/config/new.tpl', [
            'authors'    => $this->getAuthors(),
            'categories' => $this->getCategories(),
            'server'     => $servers[$id],
            'sync_from'  => $this->getSyncFrom()
        ]);
    }

    /**
     * Updates a news agency server.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_NEWS_AGENCY_CONFIG')")
     */
    public function updateAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $servers = $this->get('orm.manager')->getDataSet('Settings')
            ->get('news_agency_config');

        $server = [
            'id'             => $id,
            'name'           => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'url'            => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'username'       => $request->request->filter('username', '', FILTER_SANITIZE_STRING),
            'password'       => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
            'agency_string'  => $request->request->filter('agency_string', '', FILTER_SANITIZE_STRING),
            'external_link'  => $request->request->filter('external_link', '', FILTER_SANITIZE_STRING),
            'color'          => $request->request->filter('color', '#424E51', FILTER_SANITIZE_STRING),
            'sync_from'      => $request->request->filter('sync_from', '', FILTER_SANITIZE_STRING),
            'activated'      => $request->request->getDigits('activated', 0),
            'author'         => $request->request->getDigits('author', 0),
            'source'         => $request->request->getDigits('source', 0),
            'auto_import'    => $request->request->getDigits('auto_import', 0),
            'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
            'target_author'  => $request->request->filter('target_author', '', FILTER_SANITIZE_STRING),
            'import_related' => $request->request->filter('import_related', '', FILTER_SANITIZE_STRING),
            'filters'        => $request->request->get('filters', [])
        ];

        $servers[$id] = $server;

        $this->get('orm.manager')->getDataSet('Settings')
            ->set('news_agency_config', $servers);

        $this->get('session')->getFlashBag()
            ->add('success', _('News agency server updated.'));

        return $this->redirect($this->generateUrl(
            'backend_news_agency_server_show',
            [ 'id' => $id ]
        ));
    }

    /**
     * Returns the list of authors for the selector.
     *
     * @return array The list of authors.
     */
    protected function getAuthors()
    {
        $authors = [];
        $users   = $this->get('api.service.author')
            ->getList('order by name asc');

        foreach ($users['items'] as $user) {
            $authors[$user->id] = $user->name;
        }

        return $authors;
    }

    /**
     * Returns the list of categories for the selector.
     *
     * @return array The list of categories.
     */
    protected function getCategories()
    {
        $items = $this->get('orm.manager')->getRepository('Category')
            ->findBy('internal_category = 1 order by title asc');

        $categories = [];
        foreach ($items as $category) {
            $categories[$category->pk_content_category] = $this
                ->get('data.manager.filter')
                ->set($category->title)
                ->filter('localize')
                ->get();
        }

        return $categories;
    }

    /**
     * Returns the list of of hours for the selector.
     *
     * @return array The lisf of hours.
     */
    protected function getSyncFrom()
    {
        return [
            '3600'         => sprintf(_('%d hour'), '1'),
            '10800'         => sprintf(_('%d hours'), '3'),
            '21600'         => sprintf(_('%d hours'), '6'),
            '43200'         => sprintf(_('%d hours'), '12'),
            '86400'         => _('1 day'),
            '172800'        => sprintf(_('%d days'), '2'),
            '259200'        => sprintf(_('%d days'), '3'),
            '345600'        => sprintf(_('%d days'), '4'),
            '432000'        => sprintf(_('%d days'), '5'),
            '518400'        => sprintf(_('%d days'), '6'),
            '604800'        => sprintf(_('%d week'), '1'),
            '1209600'       => sprintf(_('%d weeks'), '2'),
            'no_limits'     => _('No limit'),
        ];
    }
}
