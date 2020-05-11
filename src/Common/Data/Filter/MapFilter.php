<?php

namespace Common\Data\Filter;

class MapFilter extends Filter
{
    /**
     * Initializes the MapFilter.
     *
     * @param ServiceContainer $container The service container.
     * @param string           $params    The filter parameters.
     */
    public function __construct($container, $params = [])
    {
        if (!array_key_exists('map', $params) || !is_array($params['map'])) {
            $message = 'MapFilter expects an argument of type array.';

            if (array_key_exists('map', $params)) {
                $message .= ' ' .  gettype($params['map']) . ' given.';
            }

            throw new \InvalidArgumentException($message);
        }

        parent::__construct($container, $params);
    }

    /**
     * Converts a string basing on a map.
     *
     * @param string $str The string to convert.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        $map = $this->getParameter('map');

        if (array_key_exists($str, $map)) {
            return $map[$str];
        }

        return false;
    }
}
