<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\FreshBooks\Data\Converter;

use Common\ORM\Core\Data\Converter\Converter;
use Common\ORM\Core\Entity;
use Symfony\Component\Intl\Intl;

/**
 * The BaseConverter class converts entity data before and after persisting them
 * to FreshBooks.
 */
class BaseConverter extends Converter
{
    /**
     * Convert entity data to valid FreshBooks values.
     *
     * @param array $source The entity data.
     *
     * @return array The converted data.
     *
     * @throws \Exception
     */
    public function freshbooksfy($source)
    {
        if (empty($this->metadata->mapping)
            || !array_key_exists('freshbooks', $this->metadata->mapping)
        ) {
            throw new \Exception();
        }

        if ($source instanceof Entity) {
            $source = $source->getData();
        }

        $source  = $this->normalize($source);
        $mapping = $this->metadata->mapping['freshbooks'];
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
     * Convert FreshBooks values to valid entity values.
     *
     * @param array $source The data from FreshBooks.
     *
     * @return array The converted data.
     */
    public function objectify($source)
    {
        if (empty($this->metadata->mapping)
            || !array_key_exists('freshbooks', $this->metadata->mapping)
        ) {
            return $source;
        }

        $mapping = $this->metadata->mapping['freshbooks'];
        $data    = [];
        foreach ($mapping as $key => $map) {
            $from      = $map['type'];
            $targetKey = $map['name'];
            $to        = $this->metadata->properties[$key];

            if (array_key_exists($targetKey, $source)) {
                $data[$key] =
                    $this->convertFrom($to, $from, $source[$targetKey]);
            }
        }

        return $this->unNormalize($data);
    }

    /**
     * Normalizes the data for Freshbooks.
     *
     * @param array $data The entity data.
     *
     * @return array The normalized data.
     */
    protected function normalize($data)
    {
        $data = $this->normalizeCountry($data);
        $data = $this->normalizeLines($data);
        $data = $this->normalizeType($data);

        return $data;
    }

    /**
     * Normalizes the country for Freshbooks.
     *
     * @param array $data The entity data.
     *
     * @return array The normalized data.
     */
    protected function normalizeCountry($data)
    {
        if (array_key_exists('country', $data) && !empty($data['country'])) {
            $countries = Intl::getRegionBundle()->getCountryNames('en');

            if (array_key_exists($data['country'], $countries)) {
                $data['country'] = $countries[$data['country']];
            }
        }

        return $data;
    }

    /**
     * Normalizes the lines for Freshbooks.
     *
     * @param array $data The entity data.
     *
     * @return array The normalized data.
     */
    protected function normalizeLines($data)
    {
        if (array_key_exists('lines', $data)) {
            foreach ($data['lines'] as &$line) {
                unset($line['uuid']);
                unset($line['order']);
            }

            $data['lines'] = [ 'line' => [ $data['lines'] ] ];
        }

        return $data;
    }

    /**
     * Normalizes the payment method for Freshbooks.
     *
     * @param array $data The entity data.
     *
     * @return array The normalized data.
     */
    protected function normalizeType($data)
    {
        if (!array_key_exists('type', $data) || empty($data['type'])) {
            return $data;
        }

        $data['type'] = $data['type'] === 'CreditCard' ?
            'Credit Card' : 'PayPal';

        return $data;
    }

    /**
     * Unnormalizes the data for Freshbooks.
     *
     * @param array $data The entity data.
     *
     * @return array The normalized data.
     */
    protected function unNormalize($data)
    {
        $data = $this->unNormalizeCountry($data);
        $data = $this->unNormalizeLines($data);
        $data = $this->unNormalizeType($data);

        return $data;
    }
    /**
     * Unnormalizes the country data for Freshbooks.
     *
     * @param array $data The entity data.
     *
     * @return array The unnormalized data.
     */
    protected function unNormalizeCountry($data)
    {
        if (!array_key_exists('country', $data) || empty($data['country'])) {
            return $data;
        }

        $countries = Intl::getRegionBundle()->getCountryNames('en');

        foreach ($countries as $key => $country) {
            if ($data['country'] === $country) {
                $data['country'] = $key;

                return $data;
            }
        }

        return $data;
    }

    /**
     * Unnormalizes the lines for Freshbooks.
     *
     * @param array $data The entity data.
     *
     * @return array The normalized data.
     */
    protected function unNormalizeLines($data)
    {
        if (array_key_exists('lines', $data)
            && !empty($data['lines'])
            && array_key_exists('line', $data['lines'])
        ) {
            $lines = [];
            foreach ($data['lines']['line'] as $line) {
                $lines[] = $line;
            }

            $data['lines'] = $lines;
        }

        return $data;
    }

    /**
     * Unnormalizes the payment method for Freshbooks.
     *
     * @param array $data The entity data.
     *
     * @return array The normalized data.
     */
    protected function unNormalizeType($data)
    {
        if (!array_key_exists('type', $data) || empty($data['type'])) {
            return $data;
        }

        $data['type'] = $data['type'] === 'Credit Card' ?
            'CreditCard' : 'PayPalAccount';

        return $data;
    }
}
