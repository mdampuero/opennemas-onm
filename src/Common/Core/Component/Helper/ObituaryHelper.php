<?php

namespace Common\Core\Component\Helper;

/**
* Returns specific properties of the obituary content.
*/
class ObituaryHelper extends ContentHelper
{
    /**
     * Returns the date data for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content date data.
     */
    public function getDate($item = null) : ?string
    {
        $value = $this->getProperty($item, 'date');

        return !empty($value) ? htmlentities($value) : null;
    }

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
     * Returns true if the content has date.
     *
     * @param Content $item The item to get property from.
     *
     * @return boolean True if the content has date, false otherwise.
     */
    public function hasDate($item = null) : ?bool
    {
        return !empty($this->getDate($item));
    }

    /**
     * Returns true if the content has maps.
     *
     * @param Content $item The item to get property from.
     *
     * @return boolean True if the content has maps, false otherwise.
     */
    public function hasMaps($item = null) : ?bool
    {
        return !empty($this->getMaps($item));
    }

    /**
     * Returns true if the content has mortuary.
     *
     * @param Content $item The item to get property from.
     *
     * @return boolean True if the content has mortuary, false otherwise.
     */
    public function hasMortuary($item = null) : ?bool
    {
        return !empty($this->getMortuary($item));
    }

    /**
     * Returns true if the content has website.
     *
     * @param Content $item The item to get property from.
     *
     * @return boolean True if the content has website, false otherwise.
     */
    public function hasWebsite($item = null) : ?bool
    {
        return !empty($this->getWebsite($item));
    }
}
