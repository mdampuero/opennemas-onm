<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Filter\Filter;

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
        // If array, already unlocalized
        if (is_array($value)) {
            return $value;
        }

        // Locales from direct parameters
        $locales = $this->getParameter('locales', null, false);
        $locale  = $this->container->get('core.locale');

        // Locales from config
        if (empty($locales)) {
            $locales = !empty($locale->getAvailableLocales('frontend')) ?
                array_keys($locale->getAvailableLocales('frontend')) : [];
        }

        return [ $locale->getLocale('frontend') => $value ];
    }
}
