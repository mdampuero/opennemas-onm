<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PressClippingController extends ApiController
{
    /**
     * Override extension property.
     *
     * @var string
     */
    protected $extension = 'es.openhost.module.pressclipping';

    /**
     * Override permissions property.
     *
     * @var array
     */
    protected $permissions = [
        // TODO: Specify permissions
    ];

    /**
     * Override module property.
     *
     * @var string
     */
    protected $module = 'pressclipping';

    /**
     * Override service property.
     *
     * @var string
     */
    protected $service = 'api.service.pressclipping';

    /**
     * Override helper property.
     *
     * @var string
     */
    protected $helper = 'core.helper.pressclipping';

    /**
     * Retrieves a list of demo items.
     *
     * @param Request $request The request object.
     * @return JsonResponse The JSON response containing demo items.
     */
    public function getListAction(Request $request)
    {
        // Generate demo response data
        $demo_response = $this->generateDemoResponse();

        // Return JSON response with demo data
        return new JsonResponse([
            'items' => $demo_response['items'],
            'total' => count($demo_response['items']),
        ]);
    }

    /**
     * Generates demo response data.
     *
     * @return array Demo response data.
     */
    private function generateDemoResponse(): array
    {
        return [
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
    }

    /**
     * Checks the server connection status.
     *
     * @return JsonResponse The JSON response containing server connection status.
     */
    public function checkServerAction()
    {
        // Get messenger service
        $msg = $this->get('core.messenger');

        try {
            $pressClipping = $this->get('external.press_clipping.factory')
                ->getEndpoint('test_connection');
            $body          = $pressClipping->testConnection();

            if (isset($body['errorCode'], $body['errorMessage'])) {
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

    /**
     * Uploads an article to the server.
     *
     * @return JsonResponse The JSON response containing upload status.
     */
    public function uploadAction()
    {
        // Example article data
        $article = [
            [
                "publicationID" => "12345",
                "title" => "Example Article",
                "subtitle" => "An example subtitle",
                "author" => "John Doe",
                "pubDate" => "2024-07-11 10:30:00",
                "body" => "<p>This is the content of the article.</p>",
                "category" => "Technology",
                "image" => "https://example.com/image.jpg",
                "articleID" => "67890",
                "articleURL" => "https://example.com/article"
            ]
        ];

        // Get messenger service
        $msg = $this->get('core.messenger');

        try {
            // Get press clipping endpoint
            $pressClipping = $this->get('external.press_clipping.factory')
                ->getEndpoint('upload_info');

            // Upload article data
            $body = $pressClipping->uploadData($article);

            // Handle response based on presence of error codes
            if (isset($body['errorCode'], $body['errorMessage'])) {
                $msg->add(
                    sprintf(
                        _('Error %d: %s'),
                        $body['errorCode'],
                        $body['errorMessage']
                    ),
                    'error',
                    400
                );
            } else {
                $msg->add(_('Article uploaded successfully'), 'success');
            }
        } catch (\Exception $e) {
            // Handle connection failure
            $msg->add(_('Unable to connect to the server'), 'error', 400);
        }

        // Return JSON response with messages
        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Retrieves configuration settings.
     *
     * @return JsonResponse The JSON response containing configuration settings.
     */
    public function getConfigAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get(['pressclipping_service',
                'pressclipping_apikey']);

        $pressclipping_service = [
            'service'   => $settings['pressclipping_service'],
            'apikey'    => $settings['pressclipping_apikey']
        ];

        return new JsonResponse([
            'pressclipping_service'      => $pressclipping_service
        ]);
    }

    /**
     * Saves configuration settings.
     *
     * @param Request $request The request object containing configuration data.
     * @return JsonResponse The JSON response containing the result of saving settings.
     */
    public function saveConfigAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $pressclipping_service = $request->request->get('pressclipping_service');
        $service               = $pressclipping_service['service'] ?? null;
        $apikey                = $pressclipping_service['apikey'] ?? null;

        $settings = [
            'pressclipping_service'          => $service,
            'pressclipping_apikey'           => $apikey
        ];

        try {
            $this->get('orm.manager')->getDataSet('Settings')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (AccessDeniedException $e) {
            $msg->add(_('PressClipping Module is not activated'), 'info');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
