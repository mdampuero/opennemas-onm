<?php
/*
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Tests\Import;

use Framework\Import\ParserFactory;

class ParserFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->factory = new ParserFactory();

        $reflection = new \ReflectionClass(get_class($this->factory));

        $this->getParsers = $reflection->getMethod('getParsers');
        $this->getParsers->setAccessible(true);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetWithInvalidXML()
    {
        $this->factory->get(null);
    }

    public function testGetWithValidXML()
    {
        $xml = simplexml_load_string('<nitf></nitf>');

        $parser = $this->factory->get($xml);

        $this->assertInstanceOf('Framework\Import\Parser\Parser', $parser);
    }

    public function testGetParsersWithInvalidDirectory()
    {
        $parsers = $this->getParsers->invokeArgs($this->factory, [ null ]);

        $this->assertEmpty($parsers);
    }

    public function testGetParsersWithValidDirectory()
    {
        $directory = __DIR__ . '/../../../src/Framework/Import/Parser';

        $parsers = $this->getParsers->invokeArgs($this->factory, [ $directory ]);

        foreach ($parsers as $parser) {
            $parser = str_replace('\\', DS, $parser . '.php');

            $this->assertTrue(file_exists($parser));
        }
    }
}
