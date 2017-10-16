<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\MultiValueDataMapper;

/**
 * Defines test cases for MultiValueDataMapper class.
 */
class MultiValueDataMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->mapper = new MultiValueDataMapper();
    }

    /**
     * Test toString with multiple values.
     */
    public function testFromString()
    {
        $this->assertEquals('flob bar xyzzy', $this->mapper->fromString('flob bar xyzzy'));
        $this->assertEquals(1, $this->mapper->fromString(1));
        $this->assertEquals(
            [ 'xyzzy', 'flob' ],
            $this->mapper->fromString('a:2:{i:0;s:5:"xyzzy";i:1;s:4:"flob";}')
        );
        $this->assertEquals(
            [ 'bar'   => 'xyzzy', 'corge' => 'flob' ],
            $this->mapper->fromString('a:2:{s:3:"bar";s:5:"xyzzy";s:5:"corge";s:4:"flob";}')
        );
    }

    /**
     * Test toString with multiple values.
     */
    public function testToString()
    {
        $this->assertEquals('flob bar xyzzy', $this->mapper->toString('flob bar xyzzy'));
        $this->assertEquals(1, $this->mapper->toString(1));
        $this->assertEquals('a:2:{i:0;s:5:"xyzzy";i:1;s:4:"flob";}', $this->mapper->toString([
            'xyzzy', 'flob'
        ]));
        $this->assertEquals('a:2:{s:3:"bar";s:5:"xyzzy";s:5:"corge";s:4:"flob";}', $this->mapper->toString([
            'bar'   => 'xyzzy',
            'corge' => 'flob'
        ]));
    }

    /**
     * Tests is serialized with valid and invalid values.
     */
    public function testIsSerialized()
    {
        $method = new \ReflectionMethod($this->mapper, 'isSerialized');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->mapper, [ null ]));
        $this->assertFalse($method->invokeArgs($this->mapper, [ 123 ]));
        $this->assertFalse($method->invokeArgs($this->mapper, [ false ]));
        $this->assertFalse($method->invokeArgs($this->mapper, [ 'frog' ]));
        $this->assertFalse($method->invokeArgs($this->mapper, [ [] ]));

        $this->assertTrue($method->invokeArgs($this->mapper, [ 'N;' ]));
        $this->assertTrue($method->invokeArgs($this->mapper, [ 'i:123;' ]));
        $this->assertTrue($method->invokeArgs($this->mapper, [ 'b:0;' ]));
        $this->assertTrue($method->invokeArgs($this->mapper, [ 's:4:"frog";' ]));
        $this->assertTrue($method->invokeArgs($this->mapper, [ 'a:0:{};' ]));
        $this->assertTrue($method->invokeArgs($this->mapper, [ 'a:1:{i:0;s:5:"plugh";}' ]));
    }
}
