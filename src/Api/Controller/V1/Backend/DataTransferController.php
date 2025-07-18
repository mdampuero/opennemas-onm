<?php

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
            'type'           => 'json',
            'excludeColumns' => ['created_at', 'update_at'], // e.g., ['created_at', 'updated_at']
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
            'type'           => 'json',
            'config'         => [
                'limit' => 1000
            ],
            'excludeColumns' => [], // e.g., ['id']
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
    public function exportListAction(Request $request)
    {
        $msg    = $this->get('core.messenger');
        $module = $request->query->get('module');
        $format = $request->query->get('format');

        if (!$module || !isset($this->availableExports[$module])) {
            return new JsonResponse(
                $msg->getMessages(),
                $msg->getCode()
            );
        }

        $config = $this->availableExports[$module];
        $type   = !empty($format) ? $format : $config['type'];

        // TODO: Fetch data from repository based on module
        // TODO: Filter columns based on $config['excludeColumns']
        // TODO: Serialize data to the desired format (currently only JSON)
        // return new JsonResponse($filtered);

        return new JsonResponse(['message' => 'Export logic not implemented yet']);
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
