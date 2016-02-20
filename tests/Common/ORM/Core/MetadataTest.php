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
        $this->data   = [ 'foo' => 'bar', 'parameters' => [] ];
        $this->validation = new Metadata($this->data);
    }

    public function testGet()
    {
        $this->assertEmpty($this->validation->baz);

        foreach ($this->data as $key => $value) {
            $this->assertEquals($value, $this->validation->{$key});
        }
    }

    public function testGetData()
    {
        $this->assertEquals($this->data, $this->validation->getData());
    }

    public function testSet()
    {
        $this->validation->qux = 'norf';

        $this->assertEquals('norf', $this->validation->qux);
    }
}
