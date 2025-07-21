<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use GuzzleHttp\Psr7\Request;
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
    protected $availableExports = [
        'advertisement' => [
            'config'         => [
                'service' => 'api.service.content',
                'limit'   => 5
            ],
            'excludeColumns' => ['created_at', 'update_at'],
        ],
    ];

    /**
     * Defines available importable modules, with format
     * and excluded columns.
     *
     * @var array<string, array{
     *  type: string,
     *  excludeColumns: string[]
     * }>
     */
    protected $availableImports = [
        'advertisement' => [
            'config'         => [
                'limit' => 1000
            ],
            'excludeColumns' => [],
        ],
    ];

    /**
     * Handles the export logic for a given module.
     *
     * Expected query parameters:
     * - module: The module to export (must be in $availableExports)
     * - format: (optional) Format override (default is the configured one)
     *
     * @return JsonResponse Response Object
     */
    public function exportListAction($contentType, $type)
    {
        if (!$contentType || !isset($this->availableExports[$contentType])) {
            return new JsonResponse(
                ['error' => 'Unsupported export: ' . $contentType],
                400
            );
        }

        $config = $this->availableExports[$contentType];

        if ($type !== 'json' && $type !== 'csv') {
            return new JsonResponse(
                ['error' => 'Unsupported format: ' . $type],
                400
            );
        }

        $response = new StreamedResponse(function () use ($contentType, $config, $type) {
            $batchSize = $config['config']['limit'];
            $offset    = 0;

            $service = $this->get($config['config']['service']);

            $output = fopen('php://output', 'w');

            if ($type === 'json') {
                $exportData = [
                    'metadata' => [
                        'content_type' => $contentType,
                        'export_date'  => date('Y-m-d H:i:s'),
                    ],
                    'items' => [],
                ];

                do {
                    $query = sprintf(
                        'content_type_name = "%s" order by starttime desc limit %d offset %d',
                        $contentType,
                        $batchSize,
                        $offset
                    );

                    $data = $service->getList($query);

                    foreach ($data['items'] as $index => $item) {
                        $itemData              = (array) $item->getStored();
                        $exportData['items'][] = $itemData;
                    }

                    $offset += $batchSize;
                } while (count($data['items']) === $batchSize);

                fwrite($output, json_encode($exportData, JSON_PRETTY_PRINT));
            }

            fclose($output);
        });

        // Configurar headers
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

        $config = $this->availableImports[$module];

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
