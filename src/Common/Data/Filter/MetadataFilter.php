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

class MetadataFilter extends Filter
{
    /**
     * Initializes the MetadataFilter.
     *
     * @param array $params The filter parameters.
     */
    public function __construct($params = [])
    {
        $this->utils = new \Onm\StringUtils;

        parent::__construct($params);
    }

    /**
     * Converts a string to a comma-separated string of tags.
     *
     * @param string $str The string to convert.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        return $this->utils->getTags($str);
    }
}
