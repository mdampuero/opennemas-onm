<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    protected $availableDataTransfer = [
        'advertisement' => [
            'config' => [
                'service' => 'api.service.content',
                'limit'   => 1000,
                'query'   => [
                    'list' => 'content_type_name = "%s" order by starttime desc limit %d offset %d',
                    'single' => 'content_type_name = "%s" and pk_content in [%s]'
                ]
            ],
            'excludeColumns' => ['created_at', 'updated_at', 'categories', 'tags'],
            'allowImport' => true
        ],
        'event' => [
            'config' => [
                'service' => 'api.service.event',
                'limit'   => 1500,
                'query'   => 'content_type_name = "%s" order by starttime desc limit %d offset %d'
            ],
            'excludeColumns' => ['created_at', 'updated_at', 'categories', 'tags'],
            'allowImport' => false
        ],
        'widget' => [
            'config' => [
                'service' => 'api.service.widget',
                'limit'   => 500,
            ],
            'excludeColumns' => ['created_at', 'updated_at'],
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
        $data    = $service->getList($query);

        // Convertir a array y aplicar filtro de columnas
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

        // Streamed response with JSON output
        $response = new StreamedResponse(function () use ($exportData) {
            echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        });

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="export_' . $contentType . '_' . date('Ymd_His') . '.json"'
        );

        return $response;
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
        if (!$contentType || !isset($this->availableDataTransfers[$contentType])) {
            return new JsonResponse(
                ['error' => 'Unsupported export: ' . $contentType],
                400
            );
        }

        $config = $this->availableDataTransfers[$contentType]['config'];

        if (!in_array($type, ['json', 'csv'], true)) {
            return new JsonResponse(
                ['error' => 'Unsupported format: ' . $type],
                400
            );
        }

        $batchSize      = $config['limit'] ?? 1000;
        $offset         = 0;
        $excludeColumns = $this->availableDataTransfers[$contentType]['excludeColumns'] ?? [];
        $queryTemplate  = $config['query']['list'] ??
            'content_type_name = "%s" order by starttime desc limit %d offset %d';

        $service = $this->container->get($config['service']);

        $response = new StreamedResponse(function () use (
            $service,
            $contentType,
            $type,
            $queryTemplate,
            $batchSize,
            $offset,
            $excludeColumns
        ) {
            $output = fopen('php://output', 'w');

            if ($type === 'json') {
                $exportData = [
                    'metadata' => [
                        'content_type' => $contentType,
                        'export_date'  => date('Y-m-d H:i:s'),
                    ],
                    'items' => [],
                ];

                $query = sprintf($queryTemplate, $contentType, $batchSize, $offset);
                $data  = $service->getList($query);

                $items = array_map(function ($item) {
                    return (array) $item->getStored();
                }, $data['items']);

                $filtered = $this->filterColumns($items, $excludeColumns);

                $exportData['items'] = array_merge($exportData['items'], $filtered);

                fwrite($output, json_encode($exportData, JSON_PRETTY_PRINT));
            }

            fclose($output);
        });

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', sprintf(
            'attachment; filename="%s_export_%s.json"',
            $contentType,
            date('Y-m-d_H-i-s')
        ));
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
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
    protected function filterColumns(array $data, array $excludeColumns): array
    {
        return array_map(function ($item) use ($excludeColumns) {
            return array_diff_key($item, array_flip($excludeColumns));
        }, $data);
    }
}
