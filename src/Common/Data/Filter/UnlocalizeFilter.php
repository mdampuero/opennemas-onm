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

class UnlocalizeFilter extends LocalizeFilter
{
    /**
     * Filters a value and returns an array of values basing on locales.
     *
     * @param mixed $value The value to filter.
     *
     * @return mixed The value for locales.
     */
    public function filterValue($value)
    {
        // Locales from direct parameters
        $locales = $this->getParameter('locales', null, false);
        $locale  = $this->container->get('core.locale');

        // Locales from config
        if (empty($locales)) {
            $locales = !empty($locale->getAvailableLocales()) ?
                array_keys($locale->getAvailableLocales()) : [];
        }

        // Already unlocalized
        if (is_array($value)
            && (count(array_diff($locales, array_keys($value))) < count($locales)
            || in_array($locale->getLocale(), array_keys($value), true))
        ) {
            return $value;
        }

        return [ $locale->getLocale() => $value ];
    }
}
