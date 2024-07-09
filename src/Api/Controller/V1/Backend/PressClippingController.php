<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PressClippingController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.pressclipping';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        // TODO: Falta especificar
    ];

    /**
     * {@inheritdoc}
     */
    protected $module = 'pressclipping';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.pressclipping';

    /**
     * {@inheritdoc}
     */
    protected $helper = 'core.helper.pressclipping';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData()
    {
        // TODO: Desarrollo futuro
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
            $pressclipping = $this->get('external.press_clipping.factory');
            $endpoint      = $pressclipping->getEndpoint('test_connection');

            $endpoint->testConnection();
            $msg->add(_('Server connection success!'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to connect to the server'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }


    public function saveConfigAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $pressclipping_service = $request->request->get('pressclipping_service');
        $service               = $pressclipping_service['service'] ?? null;
        $apikey                = $pressclipping_service['apikey'] ?? null;
        $token                 = $pressclipping_service['token'] ?? null;
        $publickey             = $pressclipping_service['publickey'] ?? null;

        $settings = [
            'pressclipping_service'          => $service,
            'pressclipping_apikey'           => $apikey,
            'pressclipping_token'            => $token,
            'pressclipping_publickey'        => $publickey,
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
}
