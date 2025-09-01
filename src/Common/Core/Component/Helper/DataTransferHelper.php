<?php
namespace Common\Core\Component\Helper;

/**
 * Helper class to retrieve tag data.
 */
class DataTransferHelper
{

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the DataTransfer Helper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Converts a path to a full URL.
     *
     * @param string $path The path to convert.
     *
     * @return string The full URL of the image.
     */
    protected function pathToImage($path)
    {
        if (empty($path)) {
            return '';
        }

        // Find content
        $cs      = $this->container->get('api.service.content');
        $content = $cs->getItem($path);

        if (empty($content)) {
            return '';
        }

        // If content is not empty, get the full path with photo helper
        $ih       = $this->container->get('core.helper.photo');
        $fullPath = $ih->getPhotoPath($content, null, [], true);

        return $fullPath ?? '';
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
    public function filterColumns(array $items, array $columns): array
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
     * Converts advertisement paths in a list of items to full URLs.
     *
     * @param array $items
     *   The list of items containing advertisements.
     *
     * @return array
     */
    public function convertAdvertisementPaths(array $items): array
    {
        foreach ($items as &$item) {
            if (empty($item['advertisements'])) {
                continue;
            }

            foreach ($item['advertisements'] as &$ad) {
                if (empty($ad['path']) || $ad['path'] === "0") {
                    continue;
                }

                $ad['path'] = $this->pathToImage($ad['path']);
            }
            unset($ad);
        }

        unset($item);

        return $items;
    }

    /**
     * Imports a photo from a given URL and creates a photo item.
     *
     * @param string $url The URL of the photo to import.
     * @param array $data Additional data to associate with the photo.
     * @return array The created photo item.
     * @throws \Exception If the file cannot be downloaded or processed.
     */
    public function importPhotoFromUrl(string $url, array $data = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $imageContent = curl_exec($ch);
        if (curl_errno($ch)) {
            $errorMsg = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Failed to download file from URL: $url. cURL error: $errorMsg");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode >= 400) {
            throw new \Exception("Failed to download file from URL: $url. HTTP status code: $httpCode");
        }

        $data['created']        = (new \DateTime())->format('Y-m-d H:i:s');
        $data['changed']        = (new \DateTime())->format('Y-m-d H:i:s');
        $data['description']    = '';
        $data['content_status'] = 1;

        $pathInfo  = pathinfo($url, PHP_URL_PATH);
        $extension = strtolower($pathInfo['extension'] ?? '');

        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $validExtensions)) {
            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageContent);

            $map = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ];

            $extension = $map[$mimeType] ?? 'bin';
        }

        $tmpFilePath = sys_get_temp_dir() . '/tmp_img_' . uniqid() . '.' . $extension;
        file_put_contents($tmpFilePath, $imageContent);

        $file = new \SplFileInfo($tmpFilePath);

        $photoService = $this->container->get('api.service.photo');
        $item         = $photoService->createItem($data, $file, true);

        return $item;
    }

    /**
     * Parses an OQL string into structured components (where, order, limit).
     *
     * @param string $oql
     *   The OQL string (e.g. 'content_type_name="advertisement" and in_litter="0"
     * order by created desc limit 10').
     *
     * @return array
     *   An array with keys: 'where', 'order', 'limit'.
     */
    public function parseOql(string $oql): array
    {
        $result = [
            'where' => null,
            'order' => null,
            'limit' => null,
        ];

        $oql = trim($oql);

        // Extract WHERE clause
        if (preg_match('/^(.*?)(\s+order by|\s+limit|$)/i', $oql, $m)) {
            $result['where'] = trim($m[1]);
        }

        // Extract ORDER BY
        if (preg_match('/order by (.*?)(\s+limit|$)/i', $oql, $m)) {
            $result['order'] = trim($m[1]);
        }

        // Extract LIMIT
        if (preg_match('/limit\s+(\d+)/i', $oql, $m)) {
            $result['limit'] = (int) $m[1];
        }

        return $result;
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
