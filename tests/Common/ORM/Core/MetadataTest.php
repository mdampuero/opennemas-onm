<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->metadata = new Metadata([
            'name' => 'Foo',
            'mapping' => [
                'index' => [ [ 'columns' => [ 'id' ], 'primary' => true ] ]
            ]
        ]);
    }

    public function testGetCacheId()
    {
        $entity = new Entity([ 'id' => 1 ]);
        $this->assertEquals('foo-1', $this->metadata->getCacheId($entity));
    }

    public function testGetCachePrefix()
    {
        $this->assertEquals('foo-', $this->metadata->getCachePrefix());

        $this->metadata->cachePrefix = 'bar';
        $this->assertEquals('bar-', $this->metadata->getCachePrefix());
    }

    public function testGetCacheSeparator()
    {
        $this->assertEquals('-', $this->metadata->getCacheSeparator());

        $this->metadata->cacheSeparator = '_';
        $this->assertEquals('_', $this->metadata->getCacheSeparator());
    }

    public function testGetId()
    {
        $entity = new Entity([ 'id' => 1, 'foo' => 'bar' ]);
        $this->assertEquals([ 'id' => 1 ], $this->metadata->getId($entity));
        $this->assertEmpty($this->metadata->getId(new Entity()));
    }

    public function testGetIdKeys()
    {
        $this->metadata->mapping['index'] = [];
        $this->assertEmpty($this->metadata->getIdKeys());

        $this->metadata->mapping['index'] = [ [ 'name' => 'id' ] ];
        $this->assertEmpty($this->metadata->getIdKeys());

        $this->metadata->mapping['index'] = [
            [ 'name' => 'id', 'columns' => [ 'id' ], 'primary' => true ]
        ];

        $this->assertEquals([ 'id' ], $this->metadata->getIdKeys());
    }

    public function testGetMetaKeys()
    {
        $this->metadata->mapping['index'] = [
            [ 'name' => 'id', 'columns' => [ 'id' ], 'primary' => true ]
        ];

        $this->assertEquals([ 'id' => 'foo_id' ], $this->metadata->getMetaKeys());

        $this->metadata->mapping['metas'] = ['ids' => [ 'id' => 'bar' ] ];
        $this->assertEquals([ 'id' => 'bar' ], $this->metadata->getMetaKeys());
    }

    public function testGetMetaTable()
    {
        $this->assertEquals('foo_meta', $this->metadata->getMetaTable());

        $this->metadata->mapping['metas'] = ['table' => 'foo_table_meta' ];
        $this->assertEquals('foo_table_meta', $this->metadata->getMetaTable());
    }

    public function testGetTable()
    {
        $this->assertEquals('foo', $this->metadata->getTable());

        $this->metadata->mapping['table'] = 'foo_table';
        $this->assertEquals('foo_table', $this->metadata->getTable());
    }

    public function testHasMetas()
    {
        $this->assertFalse($this->metadata->hasMetas());

        $this->metadata->mapping['metas'] = ['table' => 'foo_table_meta' ];
        $this->assertTrue($this->metadata->hasMetas());
    }

    public function testNormalizeId()
    {
        $this->assertEquals([ 'id' => 1 ], $this->metadata->normalizeId(1));
        $this->assertEquals([ 'id' => 1 ], $this->metadata->normalizeId([ 'id' => 1 ]));
    }
}
