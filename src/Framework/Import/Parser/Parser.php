<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser;

/**
 * Generic Parser class.
 */
abstract class Parser
{
    /**
     * The array of common parsed values.
     *
     * @var array
     */
    protected $bag;

    /**
     * The DataSourceFactory object.
     *
     * @var DataSourceFactory
     */
    protected $factory;

    /**
     * The list of priorities.
     *
     * @var array
     */
    protected $priorities = [
        '10' => 1,
        '20' => 2,
        '25' => 3,
        '30' => 4,
        // From Pandora
        'U'  => 4,
        'R'  => 3,
        'B'  => 2
    ];

    /**
     * Initializes the Parser object.
     *
     * @param DataSourceFactory $factory The DataSourceFactory object.
     * @param array             $bag     The current bag.
     */
    public function __construct($factory, $bag = [])
    {
        $this->factory = $factory;
        $this->bag     = $bag;
    }

    /**
     * Returns an empty value for properties that can not be parsed.
     *
     * @param string $method The method name.
     * @param array  $args   The array of arguments.
     *
     * @return mixed An empty string or array.
     */
    public function __call($method, $args)
    {
        return '';
    }

    /**
     * Returns the bag of the current parser.
     *
     * @return array The parser bag.
     */
    public function getBag()
    {
        return $this->bag;
    }

    /**
     * Returns a property from bag.
     *
     * @param string $property The property name.
     *
     * @return mixed The property value.
     */
    public function getFromBag($property)
    {
        if (array_key_exists($property, $this->bag)) {
            return $this->bag[$property];
        }

        return '';
    }

    /**
     * Sets the bag for the current parsed.
     *
     * @param array $bag The parser bag.
     */
    public function setBag($bag)
    {
        $this->bag = $bag;
    }

    /**
     * Checks if the given data can be parsed.
     *
     * @param object $data The data to check.
     *
     * @return boolean True if the given data can be parsed. Otherwise, returns
     *                 false.
     */
    abstract public function checkFormat($data);

    /**
     * Parses the data and returns an array of elements to import.
     *
     * @param SimpleXMLObject $data The data to import.
     *
     * @return array An array of elements to import.
     */
    abstract public function parse($data);
}
