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

use Common\ORM\Core\Data\Mapper\L10nStringDataMapper;

/**
 * Defines test cases for L10nStringDataMapper class.
 */
class L10nStringDataMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->mapper = new L10nStringDataMapper();
    }

    /**
     * Tests fromArray.
     */
    public function testFromArray()
    {
        $this->assertNull($this->mapper->fromArray(null));
        $this->assertNull($this->mapper->fromArray([]));

        $this->assertEquals('grault', $this->mapper->fromArray('grault'));
    }

    /**
     * Tests fromString for serialized and regular values.
     */
    public function testFromString()
    {
        $this->assertNull($this->mapper->fromString(''));
        $this->assertNull($this->mapper->fromString('a:0:{}'));
        $this->assertEquals('thud', $this->mapper->fromString('thud'));
        $this->assertEquals([
            'es' => 'wibble',
            'gl' => 'bar',
        ], $this->mapper->fromString('a:2:{s:2:"es";s:6:"wibble";s:2:"gl";s:3:"bar";}'));
    }

    /**
     * Tests toArray for serialized and regular values.
     */
    public function testToArray()
    {
        $this->assertEquals('thud', $this->mapper->toArray('thud'));
        $this->assertEquals([
                'es' => 'wibble',
                'gl' => 'bar',
            ], $this->mapper->toArray('a:2:{s:2:"es";s:6:"wibble";s:2:"gl";s:3:"bar";}'));
    }

    /**
     * Tests toString for serialized and regular values.
     */
    public function testToString()
    {
        $this->assertNull($this->mapper->toString(''));
        $this->assertNull($this->mapper->toString([]));
        $this->assertEquals('thud', $this->mapper->toString('thud'));
        $this->assertEquals(
            'a:2:{s:2:"es";s:6:"wibble";s:2:"gl";s:3:"bar";}',
            $this->mapper->toString([
                'es' => 'wibble',
                'gl' => 'bar',
            ])
        );
    }
}
