<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Cache\Local;

use Common\Cache\Local\Local;
use Common\ORM\Core\Entity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Local class.
 */
class LocalTest extends KernelTestCase
{
    public function setUp()
    {
        $this->local = new Local();
    }

    /**
     * Tests set and get with single values.
     */
    public function testWithSingleValues()
    {
        $this->local->set('foo', 'bar', 60);
        $this->assertEquals('bar', $this->local->get('foo'));
        $this->local->delete('foo');
        $this->assertEmpty($this->local->get('foo'));

        $object = json_decode(json_encode(['foo' => 'bar']));

        $this->local->set('flob', $object);
        $this->assertEquals($object, $this->local->get('flob'));
        $this->local->delete('flob');
        $this->assertEmpty($this->local->get('flob'));
    }

    /**
     * Tests set and get with multiple keys and values.
     */
    public function testWithMultipleValues()
    {
        $this->local->set([ 'foo' => 'bar', 'fred' => 'wibble' ]);

        $this->assertEquals(['foo' => 'bar', 'fred' => 'wibble' ], $this->local->get([ 'foo', 'fred' ]));

        $this->assertEquals(['foo' => 'bar' ], $this->local->get([ 'foo', 'garply' ]));

        $this->assertEquals('bar', $this->local->get('foo'));
        $this->assertEquals('wibble', $this->local->get('fred'));
        $this->local->delete([ 'foo', 'fred' ]);
        $this->assertEmpty($this->local->get([ 'foo', 'fred' ]));
    }
}
