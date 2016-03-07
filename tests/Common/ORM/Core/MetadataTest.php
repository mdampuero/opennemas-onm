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

use Common\ORM\Core\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->data     = [ 'name' => 'Foo', 'mapping' => [] ];
        $this->metadata = new Metadata($this->data);
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

    public function testGet()
    {
        $this->assertEmpty($this->metadata->baz);

        foreach ($this->data as $key => $value) {
            $this->assertEquals($value, $this->metadata->{$key});
        }
    }

    public function testGetData()
    {
        $this->assertEquals($this->data, $this->metadata->getData());
    }

    public function testIdKeys()
    {
        $this->assertFalse($this->metadata->getIdKeys());

        $this->metadata->mapping['index'] = [ [ 'name' => 'id' ] ];
        $this->assertFalse($this->metadata->getIdKeys());

        $this->metadata->mapping['index'] = [
            [ 'name' => 'PRIMARY', 'columns' => [ 'id' ] ]
        ];

        $this->assertEquals([ 'id' ], $this->metadata->getIdKeys());
    }

    public function testSet()
    {
        $this->metadata->qux = 'norf';

        $this->assertEquals('norf', $this->metadata->qux);
    }
}
