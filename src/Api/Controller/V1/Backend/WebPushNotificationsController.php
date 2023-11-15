<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Core\Annotation\Security;

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
            'years' => $this->getItemYears(),
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
        if ($this->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')) {
            $demo_response = [
                'items' => [
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-01 08:00:00',
                        'title' => 'Lorem Ipsum Dolor',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '23854aer'
                    ],
                    [
                        'impressions' => 0,
                        'clicks' => 0,
                        'closed' => 0,
                        'send_date' => '2024-01-01 08:00:00',
                        'title' => 'Sit Amet Consectetur',
                        'status' => 0,
                        'image' => null,
                        'transaction_id' => '23872ser'
                    ],
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-01 08:00:00',
                        'title' => 'Adipiscing Elit',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '25879adr'
                    ],
                    [
                        'impressions' => 0,
                        'clicks' => 0,
                        'closed' => 0,
                        'send_date' => '2024-01-01 08:00:00',
                        'title' => 'Sed Do Eiusmod',
                        'status' => 0,
                        'image' => null,
                        'transaction_id' => '6879der'
                    ],
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-010 08:00:00',
                        'title' => 'Tempor Incididunt Ut Labore',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '23779aew'
                    ],
                    [
                        'impressions' => 0,
                        'clicks' => 0,
                        'closed' => 0,
                        'send_date' => '2024-01-01 08:00:00',
                        'title' => 'Ut Enim Ad Minim Veniam',
                        'status' => 0,
                        'image' => null,
                        'transaction_id' => '63879asr'
                    ],
                    [
                        'impressions' => 0,
                        'clicks' => 0,
                        'closed' => 0,
                        'send_date' => '2024-01-01 08:00:00',
                        'title' => 'Laboris Nisi Ut Aliquip',
                        'status' => 0,
                        'image' => null,
                        'transaction_id' => '43879ser'
                    ],
                    [
                        'impressions' => 6000,
                        'clicks' => 3000,
                        'closed' => 2500,
                        'send_date' => '2020-01-01 08:00:00',
                        'title' => 'Titulo 1',
                        'status' => 1,
                        'image' => null,
                        'transaction_id' => '63879aes'
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
                        'transaction_id' => '76879wes'
                    ],
                ]
            ];

            return [
                'items'      => $demo_response['items'],
                'total'      => 3,
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
        if ($this->get('core.security')->hasExtension('es.openhost.module.webpush_notifications')) {
            $demo_active_subscribers = [
                8500,
                8000,
                7500,
                7000,
                6500,
                5000,
                6500,
                6000,
                5500,
                5000,
                4500,
                4000,
                3000,
                2500,
                2000,
                1500,
                750,
                500,
                250,
                100
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
                'hours'                      => $hours,
                'webpush_active_subscribers' => $settings['webpush_active_subscribers']
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

        $webpush_restricted_hours = $request->request->get('webpush_restricted_hours');

        if (!is_array($webpush_restricted_hours)) {
            $webpush_restricted_hours = [];
        }

        foreach ($webpush_restricted_hours as &$hour) {
            $hour = $hour['text'];
        }
        $webpush_restricted_hours = array_unique($webpush_restricted_hours);
        sort($webpush_restricted_hours);

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
            'webpush_restricted_hours' => $webpush_restricted_hours,
        ];

        try {
            $this->get('orm.manager')->getDataSet('Settings')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Send Web Push notification.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse
     */
    public function sendNotificationAction(Request $request)
    {
        $webpushr    = $this->get('external.web_push.factory');
        $endpoint    = $webpushr->getEndpoint('notification');
        $itemId      = $request->request->all();
        $content     = $this->get('api.service.content')->getItem($itemId[0]);
        $contentPath = $this->get('core.helper.url_generator')->getUrl($content, ['_absolute' => true]);
        $image       = $this->get('core.helper.featured_media')->getFeaturedMedia($content, 'inner');
        $imagePath   = $this->get('core.helper.photo')->getPhotoPath($image, null, [], true);

        $favicoId = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('logo_favico');

        $favico = $this->get('core.helper.photo')->getPhotoPath(
            $this->get('api.service.content')->getItem($favicoId),
            null,
            [ 192, 192 ],
            true
        );

        $notification = $endpoint->sendNotification([
            'title'      => $content->title,
            'message'    => $content->description,
            'target_url' => $contentPath,
            'image'      => $imagePath,
            'icon'      => $favico,
        ]);
        return new JsonResponse($notification);
    }

    /**
     * Tries to connect to the server with the provided parameters.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function checkServerAction()
    {
        $msg      = $this->get('core.messenger');
        $webpushr = $this->get('external.web_push.factory');

        try {
            $endpoint = $webpushr->getEndpoint('subscriber');
            $endpoint-> getSubscribers();
            $msg->add(_('Server connection success!'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to connect to the server'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Retrieve all the data from the notification given.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getNotificationDataAction($id)
    {
        $msg      = $this->get('core.messenger');
        $webpushr = $this->get('external.web_push.factory');

        try {
            $endpoint         = $webpushr->getEndpoint('status');
            $notificationData = $endpoint->getStatus($id);
            $msg->add(_('Server connection success!'), 'success');
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error', 400);
        }

        return new JsonResponse($notificationData);
    }
}
