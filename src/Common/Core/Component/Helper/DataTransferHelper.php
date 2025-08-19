<?php

namespace Common\Core\Component\Helper;

/**
 * Helper class to retrieve tag data.
 */
class DataTransferHelper
{
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
