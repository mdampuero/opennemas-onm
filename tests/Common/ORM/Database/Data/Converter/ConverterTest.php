<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Database\Data\Mapper;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Database\Data\Converter\Converter;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->metadata = new Metadata([
            'properties' => [
                'foo' => 'string',
                'bar' => 'integer',
                'baz' => 'array',
                'norf' => 'boolean',
                'wobble' => 'string'
            ],
            'mapping' => [
                'columns' => [
                    'foo' => [ 'type' => 'text' ],
                    'bar' => [ 'type' => 'integer' ],
                    'baz' => [ 'type' => 'array_json' ],
                    'norf' => [ 'type' => 'boolean' ],
                ],
                'hasMetas' => true
            ],
        ]);

        $this->converter = new Converter($this->metadata);

        $keys    = [ 'convert', 'convertFrom', 'convertTo' ];
        $this->methods = [];
        foreach ($keys as $method) {
            $this->methods[$method] = new \ReflectionMethod($this->converter, $method);
            $this->methods[$method]->setAccessible(true);
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testDatabasifyInvalid()
    {
        $converter = new Converter(new Metadata([]));
        $converter->databasify([]);
    }

    public function testDatabasifyValid()
    {
        $this->assertEquals(
            [
                [
                    'foo' => 'foobar',
                    'bar' => 1,
                    'baz' => '["garply","gorp"]',
                    'norf' => 0,
                ],
                [ 'wobble' => 'wubble' ]
            ],
            $this->converter->databasify([
                'foo' => 'foobar',
                'bar' => 1,
                'baz' => [ 'garply', 'gorp' ],
                'norf' => false,
                'wobble' => 'wubble'
            ])
        );
    }

    public function testObjectifyFromDatabase()
    {
        $this->assertEquals(
            [
                'foo' => 'foobar',
                'bar' => 1,
                'baz' => [ 'garply', 'gorp' ],
                'norf' => false
            ],
            $this->converter->objectify([
                'foo' => 'foobar',
                'bar' => 1,
                'baz' => '["garply","gorp"]',
                'norf' => 0
            ], true)
        );
    }

    public function testObjectifyFromRequest()
    {
        $this->assertEquals(
            [
                'foo' => 'foobar',
                'bar' => 1,
                'baz' => [ 'garply', 'gorp' ],
                'norf' => false
            ],
            $this->converter->objectify([
                'foo' => 'foobar',
                'bar' => '1',
                'baz' => [ 'garply', 'gorp' ],
                'norf' => '0'
            ])
        );
    }

    public function testObjectifyWithoutMetadata()
    {
        $converter = new Converter(new Metadata());
        $data      = [ 'foo' => 'bar' ];

        $this->assertEquals($data, $converter->objectify($data));
    }

    public function testResponsify()
    {
        $this->assertEquals(
            [
                'foo' => '2000-01-01 10:00:00',
                'bar' => [ 'gorp' => 'norf' ],
                'baz' => 1
            ],
            $this->converter->responsify([
                'foo' => new \DateTime('2000-01-01 10:00:00'),
                'bar' => new Entity([ 'gorp' => 'norf' ]),
                'baz' => true
            ])
        );
    }

    public function testConvertFrom()
    {
        $this->assertEquals(
            $this->methods['convert']->invokeArgs($this->converter, ['Array', 'fromSimpleArray', 'foo,bar' ]),
            $this->methods['convertFrom']->invokeArgs($this->converter, ['Array', 'SimpleArray', 'foo,bar' ])
        );
    }

    public function testConvertTo()
    {
        $this->assertEquals(
            $this->methods['convert']->invokeArgs($this->converter, ['Boolean', 'toInteger', false ]),
            $this->methods['convertTo']->invokeArgs($this->converter, ['Boolean', 'Integer', false ])
        );
    }
}
