<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class NewsAgencyServerController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'NEWS_AGENCY_IMPORTER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_news_agency_server_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'check'       => 'IMPORT_NEWS_AGENCY_CONFIG',
        'create'      => 'IMPORT_NEWS_AGENCY_CONFIG',
        'delete'      => 'IMPORT_NEWS_AGENCY_CONFIG',
        'empty'       => 'MASTER',
        'list'        => 'IMPORT_NEWS_AGENCY_CONFIG',
        'patch'       => 'IMPORT_NEWS_AGENCY_CONFIG',
        'save'        => 'IMPORT_NEWS_AGENCY_CONFIG',
        'show'        => 'IMPORT_NEWS_AGENCY_CONFIG',
        'synchronize' => 'MASTER',
        'update'      => 'IMPORT_NEWS_AGENCY_CONFIG',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.news_agency.server';

    /**
     * Tries to connect to the server with the provided parameters.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function checkItemAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('check'));

        $server = [
            'password'  => $request->query->get('password'),
            'url'       => $request->query->get('url'),
            'username'  => $request->query->get('username'),
        ];

        $msg = $this->get('core.messenger');
        $sf  = $this->get('news_agency.factory.server');

        try {
            $sf->get($server);
            $msg->add(_('Server connection success!'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to connect to the server'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Tries to connect to the server with the provided parameters.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function emptyItemAction($id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('empty'));

        $msg = $this->get('core.messenger');

        try {
            $this->get($this->service)->emptyItem($id);
            $msg->add(_('Running cleanup in background'), 'info');
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error', $e->getCode());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Tries to connect to the server with the provided parameters.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function synchronizeItemAction($id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('synchronize'));

        $msg = $this->get('core.messenger');

        try {
            $this->get($this->service)->synchronizeItem($id);
            $msg->add(_('Running syncronization in background'), 'info');
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error', $e->getCode());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        return [
            'stats'     => $this->get('news_agency.service.synchronizer')
                ->getServerStats(),
            'sync_from' => $this->getSyncFrom()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getItemId($item)
    {
        return $item['id'];
    }

    /**
     * Returns the list of hours.
     *
     * @return array The list of hours.
     */
    protected function getSyncFrom()
    {
        return [
            '3600'      => sprintf(_('%d hour'), '1'),
            '10800'     => sprintf(_('%d hours'), '3'),
            '21600'     => sprintf(_('%d hours'), '6'),
            '43200'     => sprintf(_('%d hours'), '12'),
            '86400'     => _('1 day'),
            '172800'    => sprintf(_('%d days'), '2'),
            '259200'    => sprintf(_('%d days'), '3'),
            '345600'    => sprintf(_('%d days'), '4'),
            '432000'    => sprintf(_('%d days'), '5'),
            '518400'    => sprintf(_('%d days'), '6'),
            '604800'    => sprintf(_('%d week'), '1'),
            '1209600'   => sprintf(_('%d weeks'), '2'),
            'no_limits' => _('No limit'),
        ];
    }
}
