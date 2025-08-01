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
                'positon',
                'params',
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
                'fk_content_type',
                'content_type_name',
                'title',
                'description',
                'body',
                'content_status',
                'position',
                'frontpage',
                'in_litter',
                'in_home',
                'params',
                'class',
                'widget_type',
            ],
            'allowImport' => true
        ],
        'adstxt' => [
            'config' => [
                'service' => 'core.helper.advertisement',
                'limit'   => 1000,
            ],
            'allowImport' => true,
        ]
    ];

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
     */
    public function exportItemAction(Request $request)
    {
        $contentType = $request->query->get('contentType');
        $config      = $this->availableDataTransfers[$contentType]['config'] ?? null;
        $ids         = $request->query->get('ids');

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
            $config['query']['single'] ?? 'content_type_name = "%s" and pk_content in [%s]',
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
        $filtered       = $this->filterColumns($items, $includeColumns, true);

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
     */
    public function exportListAction($contentType)
    {
        $config = $this->availableDataTransfers[$contentType] ?? null;
        $limit  = $config['config']['limit'] ?? 1000;
        $method = $config['config']['method'] ?? 'getList';
        $offset = 0;

        if (!$contentType || !$config) {
            return new JsonResponse(['error' => 'Invalid content type or config'], 400);
        }

        if ($contentType === 'adstxt') {
            return $this->exportAdstxt($contentType, $config);
        }

        $service       = $this->container->get($config['config']['service']);
        $query         = $config['config']['query']['list'] ??
            'content_type_name = "%s" order by starttime desc limit %d offset %d';
        $queryTemplate = sprintf($query, $contentType, $limit, $offset);
        $data          = $service->$method($queryTemplate);

        $items = array_map(function ($item) {
            return (array) $item->getStored();
        }, $data['items']);

        $includeColumns = $config['includeColumns'] ?? [];

        $filtered = $this->filterColumns($items, $includeColumns, true);

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

        if ($contentType === 'adstxt') {
            return $this->importAdsTxt($contentType, $items);
        }

        $us             = $this->container->get($config['config']['service']);
        $includeColumns = $config['includeColumns'] ?? [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                return new JsonResponse(['error' => 'Invalid item format'], 400);
            }

            if (empty($includeColumns)) {
                $filteredItem = $item;
            } else {
                $filteredItem = array_intersect_key($item, array_flip($includeColumns));
            }

            if ($contentType === 'advertisement') {
                $devices = $filteredItem['params']['devices'];

                $devices = array_map(
                    function ($value) {
                        return (int) $value;
                    },
                    array_intersect_key($devices, array_flip(['desktop', 'phone', 'tablet']))
                ) + $devices;
            }

            $filteredItem['content_status'] = 0;

            $us->createItem($filteredItem);
        }

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Specialized export method for Ads.txt settings.
     *
     * @param string $contentType
     *   The content type being exported (must be 'adstxt').
     * @param array $config
     *   The configuration settings for adstxt export.
     *
     * @return Response
     *   A downloadable JSON response with Ads.txt metadata and items.
     */
    protected function exportAdstxt($contentType, $config)
    {
        $adstxtHelper = $this->container->get($config['config']['service']);
        $positions    = $adstxtHelper->getAdsTxtContentInstance();

        $exportData = [
            'metadata' => [
                'content_type' => $contentType,
                'export_date'  => date('Y-m-d H:i:s'),
            ],
            'items' => $positions,
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
     * Specialized import method for Ads.txt settings.
     *
     * @param string $contentType
     *   The content type being imported (must be 'adstxt').
     * @param array $items
     *   Array of Ads.txt entries to be saved.
     *
     * @return JsonResponse
     *   Response with success message or error.
     */
    protected function importAdsTxt($contentType, $items)
    {
        $msg = $this->get('core.messenger');

        if (!$contentType || empty($items)) {
            return new JsonResponse(['error' => 'Content type and items are required'], 400);
        }

        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        $settings = [
            'ads_txt' => $items
        ];

        $ds->set($settings);

        $msg->add(_('Settings saved.'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getcode());
    }

    /**
     * Filters specified columns in a given dataset of items.
     *
     * @param array $items
     *   The full list of items to filter.
     * @param array $columns
     *   List of column keys to include or exclude.
     * @param bool $include
     *   If true, only includes the specified columns. If false, excludes them.
     *
     * @return array
     *   The filtered dataset with selected columns.
     */
    protected function filterColumns(array $items, array $columns, bool $include = false): array
    {
        if (empty($items)) {
            return [];
        }

        if (empty($columns)) {
            return $items;
        }

        return array_map(function ($item) use ($columns, $include) {
            if (!is_array($item)) {
                return [];
            }

            if ($include) {
                return array_intersect_key($item, array_flip($columns));
            } else {
                return array_diff_key($item, array_flip($columns));
            }
        }, $items);
    }
}
