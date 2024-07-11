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

    public function getListAction(Request $request)
    {
        $demo_response = [
            "items" => [
                [
                    "send_date" => "2020-01-01 08:00:00",
                    "title" => "Duis Aute Irure Dolor In Reprehenderit",
                    "status" => 1
                ],
                [
                    "send_date" => "2021-02-15 09:30:00",
                    "title" => "Lorem Ipsum Dolor Sit Amet",
                    "status" => 1
                ],
                [
                    "send_date" => "2022-03-20 14:00:00",
                    "title" => "Consectetur Adipiscing Elit",
                    "status" => 0
                ],
                [
                    "send_date" => "2023-04-10 11:45:00",
                    "title" => "Sed Do Eiusmod Tempor",
                    "status" => 1
                ],
                [
                    "send_date" => "2024-05-05 16:20:00",
                    "title" => "Incididunt Ut Labore Et Dolore",
                    "status" => 0
                ],
                [
                    "send_date" => "2025-06-30 08:00:00",
                    "title" => "Magna Aliqua Ut Enim",
                    "status" => 1
                ],
                [
                    "send_date" => "2026-07-25 13:10:00",
                    "title" => "Ad Minim Veniam",
                    "status" => 0
                ],
                [
                    "send_date" => "2027-08-15 07:30:00",
                    "title" => "Quis Nostrud Exercitation",
                    "status" => 1
                ],
                [
                    "send_date" => "2028-09-10 10:00:00",
                    "title" => "Ullamco Laboris Nisi",
                    "status" => 0
                ],
                [
                    "send_date" => "2029-10-05 15:45:00",
                    "title" => "Ut Aliquip Ex Ea Commodo",
                    "status" => 1
                ]
            ]
        ];

        return new JsonResponse([
            'items' => $demo_response['items'],
            'total' => 10,
        ]);
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
            $body          = $endpoint->testConnection();

            if (isset($body['errorCode']) && isset($body['errorMessage'])) {
                if ($body['errorCode'] === '004') {
                    $msg->add(_('Server connection success'), 'success');
                } else {
                    $msg->add(_('Unable to connect to the server'), 'error', 400);
                }
            } else {
                $msg->add(_('Server connection success'), 'success');
            }
        } catch (\Exception $e) {
            $msg->add(_('Unable to connect to the server'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    public function getConfigAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['pressclipping_service',
                'pressclipping_apikey',
                'pressclipping_token',
                'pressclipping_publickey']);

        $pressclipping_service = [
            'service'   => $settings['pressclipping_service'],
            'apikey'    => $settings['pressclipping_apikey'],
            'token'     => $settings['pressclipping_token'],
            'publickey' => $settings['pressclipping_publickey']
        ];

        return new JsonResponse([
            'pressclipping_service'      => $pressclipping_service
        ]);
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
