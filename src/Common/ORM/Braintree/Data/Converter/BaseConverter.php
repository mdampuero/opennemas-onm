<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Braintree\Data\Converter;

use Common\ORM\Core\Data\Converter\Converter;
use Common\ORM\Core\Entity;

/**
 * The BaseConverter class converts entity data before and after persisting them
 * to braintree.
 */
class BaseConverter extends Converter
{
    /**
     * Convert entity data to valid braintree values.
     *
     * @param array $source The entity data.
     *
     * @return array The converted data.
     *
     * @throws \Exception
     */
    public function braintreefy($source)
    {
        if (empty($this->metadata->mapping)
            || !array_key_exists('braintree', $this->metadata->mapping)
        ) {
            throw new \Exception();
        }

        if ($source instanceof Entity) {
            $source = $source->getData();
        }

        $mapping = $this->metadata->mapping['braintree'];
        $data    = [];
        foreach ($mapping as $key => $map) {
            $from      = $this->metadata->properties[$key];
            $targetKey = $mapping[$key]['name'];
            $to        = \classify($map['type']);

            if (array_key_exists($key, $source)) {
                $data[$targetKey] =
                    $this->convertTo($from, $to, $source[$key]);
            }
        }

        return $data;
    }

    /**
     * Convert braintree values to valid entity values.
     *
     * @param array $source The data from braintree.
     *
     * @return array The converted data.
     */
    public function objectify($source)
    {
        if (empty($this->metadata->mapping)
            || !array_key_exists('braintree', $this->metadata->mapping)
        ) {
            return $source;
        }

        $mapping = $this->metadata->mapping['braintree'];
        $data    = [];
        foreach ($mapping as $key => $map) {
            $from      = $map['type'];
            $targetKey = $map['name'];
            $to        = $this->metadata->properties[$key];

            if (!empty($source->{$targetKey})) {
                $data[$key] =
                    $this->convertFrom($to, $from, $source->{$targetKey});
            }
        }

        return $data;
    }
}
