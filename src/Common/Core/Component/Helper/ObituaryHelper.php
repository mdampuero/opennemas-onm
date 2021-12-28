<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Helper;

use Api\Exception\GetItemException;
use Symfony\Component\DependencyInjection\Container;

/**
* Perform searches in Database related with one content
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
}
