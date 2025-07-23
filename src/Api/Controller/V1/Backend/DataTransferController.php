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
                'limit'   => 1000,
                'query'   => [
                    'list' => 'content_type_name = "%s" order by starttime desc',
                    'single' => 'content_type_name = "%s" and pk_content in [%s] order by starttime desc'
                ]
            ],
            'includeColumns' => [
                'title',
                'posicion',
                'params',
                'content_status',
                'advertisements',
            ],
            'allowImport' => true
        ],
        'widget' => [
            'config' => [
                'service' => 'api.service.widget',
                'limit'   => 500,
            ],
            'includeColumns' => [],
            'allowImport' => true
        ],
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

        $excludeColumns = $this->availableDataTransfers[$contentType]['excludeColumns'] ?? [];
        $filtered       = $this->filterColumns($items, $excludeColumns);

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
    public function exportListAction($contentType, $type)
    {
        $config = $this->availableDataTransfers[$contentType] ?? null;
        $limit  = $config['config']['limit'] ?? 1000;
        $method = $config['config']['method'] ?? 'getList';
        $offset = 0;

        if (!$contentType || !$config) {
            return new JsonResponse(['error' => 'Invalid content type or config'], 400);
        }

        if (!in_array($type, ['json', 'csv'])) {
            return new JsonResponse(['error' => 'Invalid export type'], 400);
        }

        $service       = $this->container->get($config['config']['service']);
        $query         = $config['config']['query']['list'] ??
            'content_type_name = "%s" order by starttime desc limit %d offset %d';
        $queryTemplate = sprintf($query, $contentType, $offset, $limit);
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

        if ($type === 'json') {
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
        $msg    = $this->get('core.messenger');
        $module = $request->query->get('module');

        if (!$module || !isset($this->availableImports[$module])) {
            return new JsonResponse(
                $msg->getMessages(),
                $msg->getCode()
            );
        }

        // TODO: Parse incoming data
        // TODO: Filter excluded columns and validate
        // TODO: Save to database

        return new JsonResponse(
            [
                'message' => 'Import logic not implemented yet'
            ]
        );
    }

    /**
     * Filters out excluded columns from a dataset.
     *
     * @param array $data
     * @param string[] $excludeColumns
     * @return array
     */
    protected function filterColumns(array $items, array $columns, bool $include = false): array
    {
        return array_map(function ($item) use ($columns, $include) {
            if ($include) {
                return array_intersect_key($item, array_flip($columns));
            } else {
                return array_diff_key($item, array_flip($columns));
            }
        }, $items);
    }
}
