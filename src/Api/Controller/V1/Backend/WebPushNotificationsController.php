<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class WebPushNotificationsController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.webpush_notifications';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'list'   => 'WEBPUSH_ADMIN',
    ];

    protected $module = 'webpush_notifications';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.webpush_notifications';

            /**
     * {@inheritdoc}
     */
    protected $helper = 'core.helper.webpush_notifications';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        $response = [
            'years'   => $this->getItemYears(),
            'service' => $this->get('orm.manager')
                ->getDataSet('Settings')
                ->get('webpush_service')
        ];

        if (empty($items)) {
            return $response;
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $photos = [];

        $ids = array_filter(array_map(function ($notification) {
            return $notification->image;
        }, $items), function ($photo) {
                return !empty($photo);
        });

        try {
            $photos = $this->get('api.service.content')->getListByIds($ids)['items'];
            $photos = $this->get('data.manager.filter')
                ->set($photos)
                ->filter('mapify', [ 'key' => 'pk_content' ])
                ->get();

            $photos = [ 'photos' => $this->get('api.service.content')->responsify($photos) ];
            return array_merge($response, $photos);
        } catch (GetItemException $e) {
        }
        $photos = [ 'photos' => $photos, ];

        return array_merge($response, $photos);
    }

        /**
     * Returns a list of items.
     *
     * @param Request $request The request object.
     *
     * @return array The list of items and all extra information.
     */
    public function getListAction(Request $request)
    {
        // Checks if it is a demo listing or a real one
        if (!$this->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')) {
            $demo_response = [
                'items' => [
                    [
                        'impressions' => 5476,
                        'clicks' => 1327,
                        'closed' => 3864,
                        'send_date' => '2001-12-03 21:30:12',
                        'title' => 'Lorem Ipsum Dolor',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '23854aer',
                        'send_count' => 6438
                    ],
                    [
                        'impressions' => 0,
                        'clicks' => 0,
                        'closed' => 0,
                        'send_date' => '2024-01-01 08:00:00',
                        'title' => 'Sit Amet Consectetur',
                        'status' => 0,
                        'image' => null,
                        'transaction_id' => '23872ser',
                        'send_count' => 0
                    ],
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-01 08:00:00',
                        'title' => 'Adipiscing Elit',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '25879adr',
                        'send_count' => 6235
                    ],
                    [
                        'impressions' => 0,
                        'clicks' => 0,
                        'closed' => 0,
                        'send_date' => '2024-01-01 08:00:00',
                        'title' => 'Sed Do Eiusmod',
                        'status' => 0,
                        'image' => null,
                        'transaction_id' => '6879der',
                        'send_count' => 0
                    ],
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-010 08:00:00',
                        'title' => 'Tempor Incididunt Ut Labore',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '23779aew',
                        'send_count' => 7422
                    ],
                    [
                        'impressions' => 0,
                        'clicks' => 0,
                        'closed' => 0,
                        'send_date' => '2024-01-01 08:00:00',
                        'title' => 'Ut Enim Ad Minim Veniam',
                        'status' => 0,
                        'image' => null,
                        'transaction_id' => '63879asr',
                        'send_count' => 0
                    ],
                    [
                        'impressions' => 0,
                        'clicks' => 0,
                        'closed' => 0,
                        'send_date' => '2024-01-01 08:00:00',
                        'title' => 'Laboris Nisi Ut Aliquip',
                        'status' => 0,
                        'image' => null,
                        'transaction_id' => '43879ser',
                        'send_count' => 0
                    ],
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-01 08:00:00',
                        'title' => 'Titulo 1',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '63879aes',
                        'send_count' => 6235
                    ],
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-01 08:00:00',
                        'title' => 'Ex Ea Commodo Consequat',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '53879adr'
                    ],
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-01 08:00:00',
                        'title' => 'Duis Aute Irure Dolor In Reprehenderit',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '76879wes',
                        'send_count' => 6235
                    ],
                ]
            ];

            return [
                'items'      => $demo_response['items'],
                'total'      => 10,
            ];
        } else {
            $this->checkSecurity($this->extension, $this->getActionPermission('list'));

            $us  = $this->get($this->service);
            $oql = $request->query->get('oql', '');

            $response = $us->getList($oql);
        }

        return [
            'items'      => $us->responsify($response['items']),
            'total'      => $response['total'],
            'extra'      => $this->getExtraData($response['items']),
            'o-filename' => $this->filename,
        ];
    }

    /**
     * Get the Web Push notifications configuration
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        // Checks if it is a demo listing or a real one
        if (!$this->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')) {
            $demo_active_subscribers = [
                8500,8000,7500,7000,6500,5000,6500,6000,5500,5000,4500,4000,3000,2500,2000,1500,750,500,250,100
            ];
            return new JsonResponse([
                'webpush_active_subscribers' => $demo_active_subscribers
            ]);
        } else {
            $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

            $settings = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get(['webpush_service',
                    'webpush_apikey',
                    'webpush_token',
                    'webpush_publickey',
                    'webpush_automatic',
                    'webpush_delay',
                    'webpush_restricted_hours',
                    'webpush_stop_collection',
                    'webpush_active_subscribers']);

            $webpush_service = [
                'service'   => $settings['webpush_service'],
                'apikey'    => $settings['webpush_apikey'],
                'token'     => $settings['webpush_token'],
                'publickey' => $settings['webpush_publickey']
            ];

            for ($i = 0; $i < 24; $i++) {
                $hours[] = sprintf("%02d:00", $i);
            }

            return new JsonResponse([
                'webpush_service'            => $webpush_service,
                'webpush_automatic'          => $settings['webpush_automatic'],
                'webpush_delay'              => $settings['webpush_delay'],
                'webpush_restricted_hours'   => $settings['webpush_restricted_hours'],
                'webpush_stop_collection'    => $settings['webpush_stop_collection'],
                'hours'                      => $hours,
                'webpush_active_subscribers' => $settings['webpush_active_subscribers'],
                'webpush_activated'          => $this->get('core.security')
                    ->hasExtension('es.openhost.module.webpush_notifications')
                    ? false
                    : true
            ]);
        }
    }

    /**
     * Saves configuration for Web Push notifications.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $this->checkSecurity($this->extension, 'WEBPUSH_ADMIN');

        $msg = $this->get('core.messenger');

        $restictedHours = $request->request->get('webpush_restricted_hours');

        if (!is_array($restictedHours)) {
            $restictedHours = [];
        }

        foreach ($restictedHours as &$hour) {
            $hour = $hour['text'];
        }
        $restictedHours = array_unique($restictedHours);
        sort($restictedHours);

        $webpush_service = $request->request->get('webpush_service');
        $service         = $webpush_service['service'] ?? null;
        $apikey          = $webpush_service['apikey'] ?? null;
        $token           = $webpush_service['token'] ?? null;
        $publickey       = $webpush_service['publickey'] ?? null;

        $settings = [
            'webpush_service'          => $service,
            'webpush_apikey'           => $apikey,
            'webpush_token'            => $token,
            'webpush_publickey'        => $publickey,
            'webpush_automatic'        => $request->request->get('webpush_automatic'),
            'webpush_delay'            => $request->request->get('webpush_delay'),
            'webpush_stop_collection'  => $request->request->get('webpush_stop_collection'),
            'webpush_restricted_hours' => $restictedHours,
        ];

        try {
            $this->get('orm.manager')->getDataSet('Settings')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (AccessDeniedException $e) {
            $msg->add(_('Webpush Module is not activated'), 'info');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Tries to connect to the server.
     *
     * @return JsonResponse The response object.
     */
    public function checkServerAction()
    {
        $msg = $this->get('core.messenger');
        try {
            $service  = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('webpush_service');
            $webpush  = $this->get(sprintf('external.web_push.factory.%s', $service));
            $endpoint = $webpush->getEndpoint('test_connection');
            $endpoint->testConnection();
            $msg->add(_('Server connection success!'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to connect to the server'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Remove webpush account data.
     *
     * @return JsonResponse The response object.
     */
    public function removeDataAction()
    {
        $msg = $this->get('core.messenger');
        try {
            $service       = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('webpush_service');
            $webpushHelper = $this->get(sprintf('core.helper.%s', $service));
            $webpushHelper->removeAccountData();
            $msg->add(_('Account data removed successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unexpected error'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
