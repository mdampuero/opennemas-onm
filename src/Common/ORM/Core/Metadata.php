<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

use Framework\Component\Data\DataObject;
use Common\ORM\Core\Validation\Validable;

class Metadata extends DataObject implements Validable
{
    /**
     * Returns the cache prefix for the current entity.
     *
     * @return string The cache prefix.
     */
    public function getCachePrefix()
    {
        if (!empty($this->cachePrefix)) {
            return $this->cachePrefix . $this->getCacheSeparator();
        }

        return \underscore($this->name) . $this->getCacheSeparator();
    }

    /**
     * Returns the cache separator for the current entity.
     *
     * @return string The cache separator.
     */
    public function getCacheSeparator()
    {
        if (!empty($this->cacheSeparator)) {
            return $this->cacheSeparator;
        }

        return '-';
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return 'Metadata';
    }

    /**
     * Returns the key names for the current entity.
     *
     * @param type variable Description
     *
     * @return type Description
     */
    public function getIdKeys()
    {
        if (!array_key_exists('index', $this->mapping)
            || empty($this->mapping['index'])
        ) {
            return false;
        }

        foreach ($this->mapping['index'] as $index) {
            if (array_key_exists('primary', $index) && $index['primary']) {
                return $index['columns'];
            }
        }

        return false;
    }
}
