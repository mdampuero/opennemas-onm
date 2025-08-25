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
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataTransferController extends ApiController
{

    /**
     * Defines available exportable modules, with format
     * and excluded columns.
     *
     * @var array<string, array{
     *  type: string,
     *  excludeColumns: string[]
     * }>
     */
    protected $availableDataTransfers = [
        'advertisement' => [
            'config' => [
                'service' => 'api.service.content',
                'limit'   => 1000
            ],
            'includeColumns' => [
                'content_type_name',
                'fk_content_type',
                'in_litter',
                'title',
                'position',
                'frontpage',
                'in_litter',
                'in_home',
                'params.sizes',
                'params.openx_zone_id',
                'params.googledfp_unit_id',
                'params.smart_format_id',
                'params.smart_page_id',
                'params.orientation',
                'params.devices',
                'content_status',
                'advertisements',
                'ads_positions'
            ],
            'allowImport' => true
        ],
        'widget' => [
            'config' => [
                'service' => 'api.service.widget',
                'limit'   => 500,
            ],
            'includeColumns' => [
                'content_type_name', 'fk_content_type', 'title', 'content_status',
                'position', 'frontpage', 'in_litter', 'in_home',
                'params.title', 'params.max_items', 'params.indetail', 'params.columns',
                'params.tiny', 'params.list', 'params.source_type', 'params.skip',
                'params.class', 'params.grid', 'params.days', 'params.related',
                'params.uIappearThreshold', 'params.scrollDownOffset', 'params.scrollThreshold',
                'params.targetContainer', 'params.append', 'params.pagetitle', 'params.pageSharrre',
                'params.articleTitle', 'params.articleBody', 'params.articleSharrre',
                'params.bgcolor', 'params.menu', 'params.borderColor', 'params.color',
                'params.date', 'params.oldest', 'params.items',
                'class', 'widget_type',
            ],
            'allowImport' => true
        ]
    ];

    /**
     * The DataTransferHelper.
     *
     * @var string
     */
    protected $helper = 'core.helper.datatransfer';

    /**
     * Exports a set of items by ID for a specific content type.
     *
     * @param Request $request
     *   Query parameters expected:
     *   - contentType (string): The content type to export.
     *   - ids (array|int[]): Array of IDs to export.
     *
     * @return StreamedResponse|JsonResponse
     *   A downloadable JSON file of exported data or an error response.
     *
     *  @Security("hasPermission('MASTER')")
     */
    public function exportItemAction(Request $request)
    {
        $contentType = $request->query->get('contentType');
        $config      = $this->availableDataTransfers[$contentType]['config'] ?? null;
        $ids         = $request->query->get('ids');
        $helper      = $this->container->get($this->helper);

        if (!$contentType) {
            return new JsonResponse(['error' => 'Content type is required'], 400);
        }

        if (!$ids) {
            return new JsonResponse(['error' => 'IDs are required'], 400);
        }

        if (!$config || !isset($config['service'])) {
            return new JsonResponse(['error' => 'Invalid export config for content type'], 400);
        }

        $idList = implode(',', array_map('intval', $ids));

        $query = sprintf(
            'content_type_name = "%s" and pk_content in [%s]',
            $contentType,
            $idList
        );

        $service = $this->container->get($config['service']);
        $method  = $config['method'] ?? 'getList';

        $data = $service->$method($query);

        $items = array_map(function ($item) {
            return (array) $item->getStored();
        }, $data['items']);

        $includeColumns = $this->availableDataTransfers[$contentType]['includeColumns'] ?? [];
        $filtered       = $helper->filterColumns($items, $includeColumns);
        $filtered       = $helper->convertAdvertisementPaths($filtered, $contentType);

        $exportData = [
            'metadata' => [
                'content_type' => $contentType,
                'export_date'  => date('Y-m-d H:i:s'),
                'items_count'  => count($filtered),
            ],
            'items' => $filtered,
        ];

        $json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => sprintf(
                'attachment; filename="export_%s_%s.json"',
                $contentType,
                date('Ymd_His')
            ),
        ]);
    }

    /**
     * Exports all items of a specific content type using default configuration.
     *
     * @param string $contentType
     *   The content type to export (must exist in $availableDataTransfers).
     *
     * @return Response|JsonResponse
     *   A downloadable JSON file of exported data or an error response.
     *
     *  @Security("hasPermission('MASTER')")
     */
    public function exportListAction($contentType)
    {
        $config = $this->availableDataTransfers[$contentType] ?? null;
        $limit  = $config['config']['limit'] ?? 1000;
        $method = $config['config']['method'] ?? 'getList';
        $helper = $this->container->get($this->helper);
        $offset = 0;

        if (!$contentType || !$config) {
            return new JsonResponse(['error' => 'Invalid content type or config'], 400);
        }

        $service       = $this->container->get($config['config']['service']);
        $query         = 'content_type_name = "%s" and in_litter = 0 order by starttime desc limit %d offset %d';
        $queryTemplate = sprintf($query, $contentType, $limit, $offset);
        $data          = $service->$method($queryTemplate);

        $items = array_map(function ($item) {
            return (array) $item->getStored();
        }, $data['items']);

        $includeColumns = $config['includeColumns'] ?? [];

        $filtered = $helper->filterColumns($items, $includeColumns);
        $filtered = $helper->convertAdvertisementPaths($filtered, $contentType);

        $exportData = [
            'metadata' => [
                'content_type' => $contentType,
                'export_date'  => date('Y-m-d H:i:s'),
                'items_count'  => count($filtered),
            ],
            'items' => $filtered,
        ];

        $json = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return new Response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => sprintf(
                'attachment; filename="export_%s_%s.json"',
                $contentType,
                date('Ymd_His')
            ),
        ]);
    }

    /**
     * Imports items for a given content type from a JSON request body.
     *
     * @param Request $request
     *   The HTTP request with JSON body containing:
     *   - metadata.content_type (string): The content type.
     *   - items (array): The data items to import.
     *
     * @return JsonResponse
     *   Response indicating success or failure with messages.
     *
     *  @Security("hasPermission('MASTER')")
     */
    public function importAction(Request $request)
    {
        $msg         = $this->get('core.messenger');
        $content     = json_decode($request->getContent(), true);
        $contentType = $content['metadata']['content_type'] ?? null;
        $items       = $content['items'] ?? [];
        $config      = $this->availableDataTransfers[$contentType] ?? null;

        if (!$contentType || empty($items)) {
            return new JsonResponse(['error' => 'Content type and items are required'], 400);
        }

        if (!$config || !$config['allowImport']) {
            return new JsonResponse(['error' => 'Import not allowed for this content type'], 403);
        }

        $us     = $this->container->get($config['config']['service']);
        $helper = $this->container->get($this->helper);

        foreach ($items as $item) {
            if (!is_array($item)) {
                return new JsonResponse(['error' => 'Invalid item format'], 400);
            }

            $filteredItem = $item;

            if ($contentType === 'advertisement') {
                $devices = $filteredItem['params']['devices'];

                $devices = array_map(
                    function ($value) {
                        return (int) $value;
                    },
                    array_intersect_key($devices, array_flip(['desktop', 'phone', 'tablet']))
                ) + $devices;

                if ($filteredItem['advertisements'][0]['path']) {
                    $image = $helper->importPhotoFromUrl(
                        $filteredItem['advertisements']['0']['path']
                    );

                    $filteredItem['advertisements'][0]['path'] = $image->pk_content;
                }
            }

            $filteredItem['content_status'] = 0;

            $us->createItem($filteredItem);
        }

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
