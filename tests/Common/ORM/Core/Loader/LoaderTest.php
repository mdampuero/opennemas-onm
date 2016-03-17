<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
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
        $this->loader = new Loader(__DIR__ . '/../../../../../src/Common/ORM/Resources/config/orm', 'dev');
    }

    public function testLoader()
    {
        $this->assertNotEmpty($this->loader->load());
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
