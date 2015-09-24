<?php
/**
 * This file is part of the Onm package.
 *
 * (c) OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource\Parser;

use Onm\Import\DataSource\FormatInterface;
use Onm\Import\DataSource\Format\NewsMLG1Component\Video;
use Onm\Import\DataSource\Format\NewsMLG1Component\Photo;

/**
 * Generic Parser class.
 */
abstract class Parser
{
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
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
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
