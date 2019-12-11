<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Factory;

use Common\NewsAgency\Component\Parser\Parser;
use Symfony\Component\Finder\Finder;

/**
 * The ParserFactory class returns a parser to parse an XML file basing on the
 * XML content.
 */
class ParserFactory
{
    /**
     * The Finder component.
     *
     * @var Finder
     */
    protected $finder;

    /**
     * The list of available parsers.
     *
     * @var array
     */
    protected $parsers;

    /**
     * Initializes the ParserFactory.
     */
    public function __construct()
    {
        $this->finder  = new Finder();
        $this->parsers = $this->getParsers();
    }

    /**
     * Returns a parser to parse the given XML object.
     *
     * @param SimpleXMLObject $xml    The XML to parse.
     * @param Parser          $parent The parent parser when factory is invoked
     *                                from another parser.
     *
     * @return Parser The parser.
     */
    public function get(\SimpleXMLElement $xml, Parser $parent = null)
    {
        $bag = empty($parent) ? [] : $parent->getBag();

        foreach ($this->parsers as $class) {
            $parser = new $class($this, $bag);

            if ($parser->checkFormat($xml)) {
                return $parser;
            }
        }

        throw new \InvalidArgumentException('The XML could not be parsed');
    }

    /**
     * Gets a list of available parsers with its relative path.
     *
     * @return array The list of available parsers.
     */
    protected function getParsers() : array
    {
        $directory = realpath(__DIR__ . '/../Parser');
        $parsers   = [];

        $files = $this->finder->in($directory)
            ->name('*.php')
            ->notName('Parser.php')
            ->files();

        foreach ($files as $file) {
            $parsers[] = sprintf(
                '\Common\NewsAgency\Component\Parser\%s',
                str_replace(
                    [ $directory . '/', '.php', '/' ],
                    [ '', '', '\\'],
                    $file->getRealPath()
                )
            );
        }

        uasort($parsers, function ($a, $b) {
            return strlen($a) >= strlen($b) ? -1 : 1;
        });

        return $parsers;
    }
}
