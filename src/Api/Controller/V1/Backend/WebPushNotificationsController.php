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
        return [
            'years'            => $this->getItemYears()
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
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['webpush_service', 'webpush_apikey', 'webpush_token', 'webpush_automatic', 'webpush_delay']);

        $webpush_service = [
            'service' => $settings['webpush_service'],
            'apikey'  => $settings['webpush_apikey'],
            'token'   => $settings['webpush_token']
        ];

        return new JsonResponse([
            'webpush_service'   => $webpush_service,
            'webpush_automatic' => $settings['webpush_automatic'],
            'webpush_delay'     => $settings['webpush_delay'],
        ]);
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

        $webpush_service = $request->request->get('webpush_service');
        $service         = $webpush_service['service'] ?? null;
        $apikey          = $webpush_service['apikey'] ?? null;
        $token           = $webpush_service['token'] ?? null;

        $settings = [
            'webpush_service'   => $service,
            'webpush_apikey'    => $apikey,
            'webpush_token'     => $token,
            'webpush_automatic' => $request->request->get('webpush_automatic'),
            'webpush_delay'     => $request->request->get('webpush_delay'),
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
}
