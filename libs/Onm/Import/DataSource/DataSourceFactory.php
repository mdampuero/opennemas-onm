<?php
/**
 * Implements the DataSourceFactory class
 *
 * This file is part of the Onm package.
 * (c) OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource;

/**
 * Initializes a Parser to proccess a XML file.
 */
class DataSourceFactory
{
    /**
     * Returns a parser to parse the given XML object.
     *
     * @param SimpleXMLObject $xml The XML to parse.
     *
     * @return Parser The parser.
     */
    public function get($xml)
    {
        $parsers = glob(__DIR__ . DS .'Parser' . DS . '*.php');

        // Exclude abstract Parser
        $parsers = array_filter($parsers, function ($parser) {
            return !preg_match('/Parser.php$/', $parser);
        });

        foreach ($parsers as $name) {
            $class = __NAMESPACE__ . "\\Parser\\" . basename($name, '.php');

            $parser = new $class($this);

            if ($parser->checkFormat($xml)) {
                return $parser;
            }
        }

        throw new \Exception(_('The XML could not be parsed'));
    }
}
