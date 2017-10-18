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
    public function filter($items)
    {
        if (empty($items)) {
            return $items;
        }

        // Filter simple values
        if (empty($this->getParameter('keys'))) {
            return $this->filterValue($items);
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
     * Filters an item.
     *
     * @param Object $item   Object to filter.
     * @param array  $params The list of parameters.
     *
     * @return Object Filtered object.
     */
    protected function filterItem($item)
    {
        if (!is_object($item)) {
            return;
        }

        foreach ($this->getParameter('keys') as $key) {
            if (isset($item->{$key})) {
                $item->{$key} = $this->filterValue($item->{$key});
            }
        }
    }

    /**
     * Filters an array of values and returns a value basing on a locale.
     *
     * @param mixed $value The value to filter.
     *
     * @return mixed The value for the locale.
     */
    protected function filterValue($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        // Locale from direct parameters
        $default = $this->getParameter('default');
        $locale  = $this->getParameter('locale', null, false);

        // Locale from request
        if (empty($locale)) {
            $locale = $this->container->get('core.locale')
                ->getRequestLocale('frontend');
        }

        if (empty($default)) {
            $default = $this->container->get('core.locale')
                ->getLocale('frontend');
        }

        if (!empty($locale) && array_key_exists($locale, $value)) {
            return $value[$locale];
        }

        if (!empty($default) && array_key_exists($default, $value)) {
            return $value[$default];
        }

        return null;
    }
}
