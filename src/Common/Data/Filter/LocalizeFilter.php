<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Filter;

class LocalizeFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter($items, $params = [])
    {
        if (empty($items) || empty($params)) {
            return;
        }

        if (is_array($items)) {
            foreach ($items as $item) {
                $this->filterItem($item, $params);
            }

            return;
        }

        $this->filterItem($items, $params);
    }

    /**
     * Filters an item.
     *
     * @param Object $item   Object to filter.
     * @param array  $params The list of parameters.
     *
     * @return Object Filtered object.
     */
    protected function filterItem($item, $params = [])
    {
        if ((!is_array($item)
            && !is_object($item))
            || !array_key_exists('keys', $params)
            || !array_key_exists('locale', $params)
        ) {
            return;
        }

        $locale  = $params['locale'];
        $default = array_key_exists('default', $params) ?
            $params['default'] : 'en_US';

        foreach ($params['keys'] as $key) {
            if (isset($item->{$key})) {
                $item->$key = $this->filterValue($item->$key, $locale, $default);
            }
        }
    }

    /**
     * Filters an array of values and returns a value basing on a locale.
     *
     * @param mixed  $value   The value to filter.
     * @param string $locale  The locale to base on.
     * @param string $default The default locale.
     *
     * @return mixed The value for the locale.
     */
    protected function filterValue($value, $locale, $default = null)
    {
        if (!is_array($value)) {
            return $value;
        }

        if (array_key_exists($locale, $value)) {
            return $value[$locale];
        }

        if (!empty($default) && array_key_exists($default, $value)) {
            return $value[$default];
        }

        return null;
    }
}
