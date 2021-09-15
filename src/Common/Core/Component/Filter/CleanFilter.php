<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Converter\ArrayConverter;
use Opennemas\Data\Filter\Filter;

class CleanFilter extends Filter
{
    /**
     * Cleans all the specified properties of a serialized array.
     *
     * @param string $array The serialized array.
     *
     * @return string|null The serialized array without the indicated properties.
     */
    public function filter($array)
    {
        $converter   = new ArrayConverter();
        $type        = $this->getParameter('type', 'String');
        $properties  = $this->getParameter('properties', []);
        $associative = $this->getParameter('associative', true);

        $array = $converter->{sprintf('from%s', $type)}($array);
        $array = $this->clean($array, $properties, $associative);

        return $converter->{sprintf('to%s', $type)}($array);
    }

    /**
     * Returns the array without the properties to clean.
     *
     * @param array $array       The array to clean.
     * @param array $properties  The properties to clean.
     * @param bool  $associative If the array is associative or not.
     *
     * @return array The cleaned array.
     */
    protected function clean($array, $properties, $associative)
    {
        if (!is_array($properties)) {
            $properties = [ $properties ];
        }

        if ($associative) {
            foreach (array_keys($array) as $key) {
                if (in_array($key, $properties)) {
                    unset($array[$key]);
                }
            }

            return $array;
        }

        return array_values(array_diff($array, $properties));
    }
}
