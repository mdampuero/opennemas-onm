<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Loader;

use Common\ORM\Core\Loader\Loader;
use Common\ORM\Core\Metadata;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Cache')
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->cm = $this->getMockBuilder('Common\Cache\Core\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->parser = $this->getMockBuilder('Symfony\Component\Yaml\Parser')
            ->setMethods([ 'parse' ])
            ->getMock();

        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods([ 'getIterator', 'files', 'in', 'name' ])
            ->getMock();

        $this->file = $this->getMockBuilder('File')
            ->setMethods([ 'getRealPath' ])
            ->getMock();

        $this->cm->expects($this->any())->method('getConnection')->willReturn($this->cache);

        $this->loader = new Loader(
            __DIR__,
            'dev',
            ['connection' => [], 'entity' => [], 'schema' => []],
            $this->cm
        );
    }

    public function testLoad()
    {
        $this->cache->expects($this->once())->method('get')->willReturn([]);
        $this->cache->expects($this->once())->method('set')->willReturn([]);

        $this->file->expects($this->any())->method('getRealPath')->willReturn(__FILE__);

        $this->finder->expects($this->any())->method('files')->willReturn($this->finder);
        $this->finder->expects($this->any())->method('in')->willReturn($this->finder);
        $this->finder->expects($this->any())->method('name')->willReturn($this->finder);
        $this->finder->expects($this->any())->method('getIterator')
            ->willReturn(new \ArrayIterator(array_fill(0, 6, $this->file)));

        $this->parser->expects($this->at(0))->method('parse')
            ->willReturn([ 'entity' => [ 'name' => 'wobble' ] ]);
        $this->parser->expects($this->at(1))->method('parse')
            ->willReturn([ 'entity' => [ 'name' => 'fubar', 'parent' => 'wobble' ] ]);
        $this->parser->expects($this->at(2))->method('parse')
            ->willReturn([ 'connection' => [ 'name' => 'baz'] ]);
        $this->parser->expects($this->at(3))->method('parse')
        ->willReturn([ 'schema' => [ 'name' => 'baz'] ]);
        $this->parser->expects($this->at(4))->method('parse')->willReturn([ 'foo' => [] ]);
        $this->parser->expects($this->at(5))->method('parse')->willReturn([]);

        $property = new \ReflectionProperty($this->loader, 'parser');
        $property->setAccessible(true);
        $property->setValue($this->loader, $this->parser);

        $property = new \ReflectionProperty($this->loader, 'finder');
        $property->setAccessible(true);
        $property->setValue($this->loader, $this->finder);

        $config = $this->loader->load();

        $this->assertNotEmpty($config);
        $this->assertEquals(1, count($config['connection']));
        $this->assertEquals(2, count($config['metadata']));
        $this->assertEquals(1, count($config['schema']));
    }

    public function testLoadWithCache()
    {
        $config = [ 'entity' => [ new Metadata([ 'name' => 'foo' ]) ] ];

        $this->cache->expects($this->any())->method('get')->willReturn($config);

        $this->loader = new Loader(
            __DIR__,
            'dev',
            ['connection' => [], 'entity' => [], 'schema' => []],
            $this->cm
        );

        $this->assertEquals($config, $this->loader->load());
    }

    public function testMergeItems()
    {
        $item   = new Metadata([ 'mapping' => [ 'table' => 'foo' ] ]);
        $parent = new Metadata([ 'mapping' => [ 'table' => 'bar' ] ]);

        $method = new \ReflectionMethod($this->loader, 'mergeItems');
        $method->setAccessible(true);

        $method->invokeArgs($this->loader, [ $item, $parent ]);

        $this->assertEquals('foo', $item->mapping['table']);
    }

    public function testMergeValues()
    {
        $method = new \ReflectionMethod($this->loader, 'mergeValues');
        $method->setAccessible(true);

        $this->assertEquals('foo', $method->invokeArgs($this->loader, [ 'key', 'foo', false ]));
        $this->assertEquals('foo', $method->invokeArgs($this->loader, [ 'key', false, 'foo' ]));
        $this->assertEquals('bar', $method->invokeArgs($this->loader, [ 'key', 'foo', 'bar' ]));
        $this->assertEquals(
            [ 'foo' => 'integer', 'bar' => 'string'],
            $method->invokeArgs(
                $this->loader,
                [ 'properties', [ 'foo' => 'integer' ], [ 'bar' => 'string' ] ]
            )
        );

        $this->assertEquals(
            [ 'table' => 'bar', 'norf' => [ 'glork', 'glorp' ] ],
            $method->invokeArgs(
                $this->loader,
                [
                    'mapping',
                    [ 'table' => 'foo', 'norf' => [ 'glork' ] ],
                    [ 'table' => 'bar', 'norf' => [ 'glorp' ] ]
                ]
            )
        );

        $this->assertEquals(
            [ 'table' => 'foo', 'norf' => [ 'glork' ] ],
            $method->invokeArgs(
                $this->loader,
                [
                    'mapping',
                    [ 'table' => 'foo', 'norf' => [ 'glork' ] ],
                    []
                ]
            )
        );

    }
}
