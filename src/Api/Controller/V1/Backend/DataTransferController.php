<?php

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
     * Handles the export of items for a given content type.
     *
     * Expected query parameters:
     * - contentType: The type of content to export (e.g., 'advertisement', 'event')
     * - ids: Comma-separated list of IDs to export
     *
     * @return StreamedResponse|JsonResponse
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
     * Handles the export logic for a given module.
     *
     * Expected query parameters:
     * - module: The module to export (must be in $availableDataTransfers)
     * - format: (optional) Format override (default is the configured one)
     *
     * @return JsonResponse Response Object
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
     * Handles the import logic for a given module.
     *
     * Expected:
     * - module: passed in query or route
     * - JSON body with data to import
     *
     * @return JsonResponse Response Object
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
     * Filters columns from a dataset.
     *
     * @param array $items Array of items to filter
     * @param array $columns Columns to include or exclude
     * @param bool $include If true, includes only specified columns. If false, excludes specified columns.
     * @return array Filtered items
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
