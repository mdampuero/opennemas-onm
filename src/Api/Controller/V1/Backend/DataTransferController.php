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
                'content_type_name',
                'fk_content_type',
                'title',
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
     *
     *  @Security("hasPermission('MASTER')")
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
         // TODO: Pass to helper fuction and allow filter nested arrays.
        $filtered       = $this->filterColumns($items, $includeColumns);

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
        $offset = 0;

        if (!$contentType || !$config) {
            return new JsonResponse(['error' => 'Invalid content type or config'], 400);
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

        $filtered = $this->filterColumns($items, $includeColumns);

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

        $us             = $this->container->get($config['config']['service']);
        $includeColumns = $config['includeColumns'] ?? [];

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
            }

            $filteredItem['content_status'] = 0;

            $us->createItem($filteredItem);
        }

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Filters specified columns in a given dataset of items, supporting nested fields with dot notation.
     *
     * @param array $items
     *   The full list of items to filter.
     * @param array $columns
     *   List of column keys to include or exclude (supports dot notation for nested fields).
     *
     * @return array
     *   The filtered dataset with selected columns.
     */
    protected function filterColumns(array $items, array $columns): array
    {
        if (empty($items)) {
            return [];
        }

        if (empty($columns)) {
            return $items;
        }

        return array_map(function ($item) use ($columns) {
            if (!is_array($item)) {
                return [];
            }

            $result = [];
            foreach ($columns as $column) {
                // Handle nested fields with dot notation
                if (strpos($column, '.') !== false) {
                    $value = $this->getNestedValue($item, $column);
                    if ($value !== null) {
                        $this->setNestedValue($result, $column, $value);
                    }
                } else {
                    if (array_key_exists($column, $item)) {
                        $result[$column] = $item[$column];
                    }
                }
            }

            return $result;
        }, $items);
    }

    /**
     * Gets a nested value from an array using dot notation.
     *
     * @param array $array
     * @param string $key
     * @return mixed|null
     */
    protected function getNestedValue(array $array, string $key)
    {
        $keys    = explode('.', $key);
        $current = $array;

        foreach ($keys as $k) {
            if (!is_array($current) || !array_key_exists($k, $current)) {
                return null;
            }
            $current = $current[$k];
        }

        return $current;
    }

    /**
     * Sets a nested value in an array using dot notation.
     *
     * @param array &$array
     * @param string $key
     * @param mixed $value
     */
    protected function setNestedValue(array &$array, string $key, $value): void
    {
        $keys    = explode('.', $key);
        $current = &$array;

        foreach ($keys as $k) {
            if (!is_array($current)) {
                $current = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }

    /**
     * Removes a nested field from an array using dot notation.
     *
     * @param array &$array
     * @param string $key
     */
    protected function removeNestedField(array &$array, string $key): void
    {
        $keys    = explode('.', $key);
        $lastKey = array_pop($keys);
        $current = &$array;

        foreach ($keys as $k) {
            if (!is_array($current) || !array_key_exists($k, $current)) {
                return;
            }
            $current = &$current[$k];
        }

        unset($current[$lastKey]);
    }
}
