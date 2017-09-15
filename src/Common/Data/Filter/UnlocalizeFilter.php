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

use Common\Data\Filter\LocalizeFilter;

/**
 * Defines test cases for UnlocalizeFilter class.
 */
class UnlocalizeFilter extends LocalizeFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($items)
    {
        if (empty($items) || empty($this->getParameter('keys'))) {
            return $items;
        }

        if (is_array($items)) {
            foreach ($items as $item) {
                $this->filterItem($item);
            }

            return $items;
        }

        $this->filterItem($items);

        return $items;
    }

    /**
     * Filters a value and returns an array of values basing on locales.
     *
     * @param mixed $value The value to filter.
     *
     * @return mixed The value for locales.
     */
    public function filterValue($value)
    {
        $locales = $this->getParameter('locales');
        $locale  = $this->getParameter('locale');

        if (empty($locales) || empty($locale)) {
            return $value;
        }

        // Already unlocalized
        if (is_array($value)
            && count(array_diff($locales, array_keys($value))) < count($locales)
        ) {
            return $value;
        }

        return array_fill_keys([ $locale ], $value);
    }
}
