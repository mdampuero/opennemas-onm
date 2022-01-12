<?php

namespace Common\Core\Component\Helper;

/**
* Returns specific properties of the obituary content.
*/
class ObituaryHelper extends ContentHelper
{
    /**
     * Returns the maps data for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content maps data.
     */
    public function getMaps($item = null) : ?string
    {
        $value = $this->getProperty($item, 'maps');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns the mortuary data for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content mortuary data.
     */
    public function getMortuary($item = null) : ?string
    {
        $value = $this->getProperty($item, 'mortuary');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns the website data for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content website data.
     */
    public function getWebsite($item = null) : ?string
    {
        $value = $this->getProperty($item, 'website');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns the website data for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content website data.
     */
    public function hasMaps($item = null) : ?bool
    {
        return !empty($this->getMaps($item));
    }

    /**
     * Returns the website data for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content website data.
     */
    public function hasMortuary($item = null) : ?bool
    {
        return !empty($this->getMortuary($item));
    }

    /**
     * Returns the website data for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content website data.
     */
    public function hasWebsite($item = null) : ?bool
    {
        return !empty($this->getWebsite($item));
    }
}
