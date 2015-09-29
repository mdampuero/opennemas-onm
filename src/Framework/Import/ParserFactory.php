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
namespace Framework\Import;

/**
 * Initializes a Parser to proccess a XML file.
 */
class ParserFactory
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
        $parsers = $this->getParsers(__DIR__ . DS . 'Parser');

        foreach ($parsers as $name) {
            $class = __NAMESPACE__ . '\\Parser\\' . $name;

            $parser = new $class($this);

            if ($parser->checkFormat($xml)) {
                return $parser;
            }
        }

        throw new \Exception(_('The XML could not be parsed'));
    }

    /**
     * Gets a list of available parsers with its relative path.
     *
     * @param string $directory The directory where search parsers.
     *
     * @return array The list of available parsers.
     */
    private function getParsers($directory)
    {
        if (empty($directory)) {
            return [];
        }

        $path      = __DIR__ . DS . 'Parser' . DS;
        $namespace = str_replace([ $path , DS ], [ '', '\\' ], $directory);

        if (!empty($namespace)) {
            $namespace .= '\\';
        }

        $files = scandir($directory);

        $parsers = [];
        foreach ($files as $file) {
            if ($file !== '..' && $file !== '.' && $file !== 'Parser.php') {
                if (!is_file($directory . DS . $file)) {
                    $parsers = array_merge(
                        $parsers,
                        $this->getParsers($directory . DS . $file)
                    );
                } else {
                    $parsers[] = ltrim($namespace, '\\')
                        . basename($file, '.php');
                }
            }
        }

        uasort($parsers, function ($a, $b) {
            return strlen($a) >= strlen($b) ? -1 : 1;
        });

        return $parsers;
    }
}
