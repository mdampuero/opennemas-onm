<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Serialize\Serializer;

use Common\Data\Serialize\Serializer\PhpSerializer;

/**
 * Defines test cases for PhpSerializer class.
 */
class PhpSerializerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests isSerialized with serailized and unserialized values.
     */
    public function testIsSerialized()
    {
        $this->assertFalse(PhpSerializer::isSerialized(null));
        $this->assertFalse(PhpSerializer::isSerialized([]));
        $this->assertFalse(PhpSerializer::isSerialized(1));
        $this->assertFalse(PhpSerializer::isSerialized(false));
        $this->assertFalse(PhpSerializer::isSerialized('wobble'));

        $this->assertTrue(PhpSerializer::isSerialized('N;'));
        $this->assertTrue(PhpSerializer::isSerialized('a:0:{}'));
        $this->assertTrue(PhpSerializer::isSerialized('i:0;'));
        $this->assertTrue(PhpSerializer::isSerialized('b:0;'));
        $this->assertTrue(PhpSerializer::isSerialized('s:6:"wobble";'));
    }

    /**
     * Tests serialize with serialized and unserialized values.
     */
    public function testSerialize()
    {
        $this->assertEquals('N;', PhpSerializer::serialize(null));
        $this->assertEquals('a:0:{}', PhpSerializer::serialize([]));
        $this->assertEquals('i:1;', PhpSerializer::serialize(1));
        $this->assertEquals('b:0;', PhpSerializer::serialize(false));
        $this->assertEquals('s:6:"wobble";', PhpSerializer::serialize('wobble'));

        $this->assertEquals('N;', PhpSerializer::serialize(serialize(null)));
        $this->assertEquals('a:0:{}', PhpSerializer::serialize(serialize([])));
        $this->assertEquals('i:1;', PhpSerializer::serialize(serialize(1)));
        $this->assertEquals('b:0;', PhpSerializer::serialize(serialize(false)));
        $this->assertEquals('s:6:"wobble";', PhpSerializer::serialize(serialize('wobble')));

        $this->assertEquals('N;', PhpSerializer::serialize(PhpSerializer::serialize(null)));
        $this->assertEquals('a:0:{}', PhpSerializer::serialize(PhpSerializer::serialize([])));
        $this->assertEquals('i:1;', PhpSerializer::serialize(PhpSerializer::serialize(1)));
        $this->assertEquals('b:0;', PhpSerializer::serialize(PhpSerializer::serialize(false)));
        $this->assertEquals('s:6:"wobble";', PhpSerializer::serialize(PhpSerializer::serialize('wobble')));
    }

    /**
     * Tests unserialize with serialized and unserialized values.
     */
    public function testUnserialize()
    {
        $this->assertEquals(null, PhpSerializer::unserialize(null));
        $this->assertEquals([], PhpSerializer::unserialize([]));
        $this->assertEquals(1, PhpSerializer::unserialize(1));
        $this->assertEquals(false, PhpSerializer::unserialize(false));
        $this->assertEquals('wobble', PhpSerializer::unserialize('wobble'));

        $this->assertEquals(null, PhpSerializer::unserialize('N;'));
        $this->assertEquals([], PhpSerializer::unserialize('a:0:{}'));
        $this->assertEquals(0, PhpSerializer::unserialize('i:0;'));
        $this->assertEquals(false, PhpSerializer::unserialize('b:0;'));
        $this->assertEquals('wobble', PhpSerializer::unserialize('s:6:"wobble";'));
    }
}
