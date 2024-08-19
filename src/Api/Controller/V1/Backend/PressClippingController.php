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
     * Retrieves a list of items.
     *
     * @param Request $request The request object.
     * @return JsonResponse The JSON response containing demo items.
     */
    public function getListAction(Request $request)
    {
        $listConnection = $this->get('dbal_connection');

        $list = $listConnection->fetchAll(
            "SELECT contentmeta.fk_content, contents.title, contentmeta.meta_name, contentmeta.meta_value
             FROM contentmeta
             JOIN contents ON contentmeta.fk_content = contents.pk_content
             WHERE contentmeta.meta_name IN ('pressclipping_sended', 'pressclipping_status')"
        );

        // Array to store results grouped by pk_content
        $groupedList = [];

        // Iterate over each row of $list
        foreach ($list as $item) {
            $pkContent = $item['fk_content'];

            // If pk_content is not already in $groupedList, initialize it as an empty array
            if (!isset($groupedList[$pkContent])) {
                $groupedList[$pkContent] = [
                    'title' => $item['title'],
                    'pressclipping_sended' => null,
                    'pressclipping_status' => null,
                ];
            }

            if ($item['meta_name'] == 'pressclipping_sended') {
                $groupedList[$pkContent]['pressclipping_sended'] = $item['meta_value'];
            }

            if ($item['meta_name'] == 'pressclipping_status') {
                $groupedList[$pkContent]['pressclipping_status'] = $item['meta_value'];
            }
        }

        // Convert associative array to indexed array
        $groupedList = array_values($groupedList);

        // Return JSON response with items data
        return new JsonResponse([
            'items' => $groupedList,
        ]);
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
    public function uploadAction(Request $request = null)
    {
        $data = $request->request->all();

        // Check if $data contains an array of articles.
        // If $data contains an array with at least one element and the first element is an array,
        // then assign $data to $article.
        if (isset($data[0]) && is_array($data[0])) {
            $article = $data;
        }

        // Get messenger service
        $msg = $this->get('core.messenger');

        try {
            // Get press clipping endpoint
            $pressClipping = $this->get('external.press_clipping.factory')
                ->getEndpoint('upload_info');

            // Upload article data
            $body = $pressClipping->uploadData($article);

            $pressClippingLogger = $this->get('dbal_connection');

            $pressClippingStatus = isset($body['errorCode']) ? 'Not sended' : 'Sended';

            $metas = [
                [
                    'fk_content' => $article[0]['articleID'],
                    'meta_name' => 'pressclipping_status',
                    'meta_value' => $pressClippingStatus
                ],
                [
                    'fk_content' => $article[0]['articleID'],
                    'meta_name' => 'pressclipping_sended',
                    'meta_value' => date('Y-m-d')
                ]
            ];

            $this->insertOrUpdateMetas($pressClippingLogger, $metas);

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
     * Inserts or updates meta data in the database.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param array $metas
     */
    protected function insertOrUpdateMetas($connection, array $metas)
    {
        foreach ($metas as $meta) {
            $connection->executeUpdate(
                "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
                . " VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?",
                [
                    $meta['fk_content'],
                    $meta['meta_name'],
                    $meta['meta_value'],
                    $meta['meta_value']
                ]
            );
        }
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
                'pressclipping_pubID',
                'pressclipping_apikey',
            ]);

        $pressclipping_service = [
            'service'   => $settings['pressclipping_service'],
            'pubID'   => $settings['pressclipping_pubID'],
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
        $pubID                 = $pressclipping_service['pubID'] ?? null;
        $apikey                = $pressclipping_service['apikey'] ?? null;

        $settings = [
            'pressclipping_service' => $service,
            'pressclipping_pubID'   => $pubID,
            'pressclipping_apikey'  => $apikey,
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
