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
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for the news agency module
 */
class NewsAgencyServerController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $this->syncFrom = array(
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
        );

        ini_set('memory_limit', '128M');
        ini_set('set_time_limit', '0');

        // Check if module is configured, if not redirect to configuration form
        if (is_null($this->get('setting_repository')->get('news_agency_config'))) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please provide your source server configuration to start to use your Importer module')
            );
        }
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
    public function createAction(Request $request)
    {
        $servers = $this->get('setting_repository')->get('news_agency_config');

        if (!is_array($servers)) {
            $servers = [];
        }

        if (count($servers) <= 0) {
            $latestServerId = 0;
        } else {
            $latestServerId = max(array_keys($servers));
        }

        $server = array(
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
            'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
            'target_author'  => $request->request->filter('target_author', '', FILTER_SANITIZE_STRING),
            'import_related' => $request->request->filter('import_related', '', FILTER_SANITIZE_STRING),
            'filters'        => $request->request->get('filters', []),
        );

        $servers[$server['id']] = $server;

        $this->get('setting_repository')->set('news_agency_config', $servers);

        $this->get('session')->getFlashBag()->add(
            'success',
            _('News agency server added.')
        );

        return $this->redirect(
            $this->generateUrl(
                'backend_news_agency_server_show',
                array('id' => $server['id'])
            )
        );
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
        $servers = $this->get('setting_repository')->get('news_agency_config');
        $items   = $this->get('category_repository')->findBy(
            ['internal_category' => [ [ 'value' => 1  ]] ],
            []
        );

        $categories = [];
        foreach ($items as $category) {
            $categories[$category->id] = $this->get('data.manager.filter')
                ->set($category->title)->filter('localize')->get();
        }

        asort($categories);

        $id     = $request->query->getDigits('id');
        $server = [];

        if (!empty($id)) {
            $server = $servers[$id];
        }

        $authors = [];
        $users   = $this->get('api.service.author')
            ->getList('order by name asc');

        foreach ($users['items'] as $user) {
            $authors[$user->id] = $user->name;
        }

        return $this->render('news_agency/config/new.tpl', [
            'authors'    => $authors,
            'categories' => $categories,
            'server'     => $server,
            'sync_from'  => $this->syncFrom
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
        $servers = $this->get('setting_repository')->get('news_agency_config');

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

        $this->get('setting_repository')->set('news_agency_config', $servers);

        $this->get('session')->getFlashBag()->add(
            'success',
            _('News agency server updated.')
        );

        return $this->redirect(
            $this->generateUrl(
                'backend_news_agency_server_show',
                [ 'id' => $id ]
            )
        );
    }
}
