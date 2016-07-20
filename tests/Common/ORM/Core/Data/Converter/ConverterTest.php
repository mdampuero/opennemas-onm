<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Data\Converter;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Data\Converter\Converter;

/**
 * Defines test cases for Converter class.
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->metadata = new Metadata([
            'properties' => [
                'foo'    => 'string',
                'bar'    => 'integer',
                'baz'    => 'array',
                'norf'   => 'boolean',
                'wobble' => 'string',
                'quux'   => 'integer',
            ],
            'mapping' => [
                'database' => [
                    'columns' => [
                        'foo' => [ 'type' => 'text' ],
                        'bar' => [ 'type' => 'integer' ],
                        'baz' => [ 'type' => 'array_json' ],
                        'norf' => [ 'type' => 'boolean' ],
                        'quux' => [ 'type' => 'integer', 'options' => [ 'default' => null ] ],
                    ],
                    'hasMetas' => true
                ]
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
     * Tests objectify for data from a request.
     */
    public function testObjectify()
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

    /**
     * Tests objectify when empty metadata provided.
     */
    public function testObjectifyWithoutMetadata()
    {
        $converter = new Converter(new Metadata());
        $data      = [ 'foo' => 'bar' ];

        $this->assertEquals($data, $converter->objectify($data));
    }

    /**
     * Tests responsify for data from an Entity.
     */
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

    /**
     * Tests convertFrom.
     */
    public function testConvertFrom()
    {
        $this->assertEquals(
            $this->methods['convert']->invokeArgs($this->converter, ['Array', 'fromSimpleArray', 'foo,bar' ]),
            $this->methods['convertFrom']->invokeArgs($this->converter, ['Array', 'SimpleArray', 'foo,bar' ])
        );
    }

    /**
     * Tests convertTo
     */
    public function testConvertTo()
    {
        $this->assertEquals(
            $this->methods['convert']->invokeArgs($this->converter, ['Boolean', 'toInteger', false ]),
            $this->methods['convertTo']->invokeArgs($this->converter, ['Boolean', 'Integer', false ])
        );
    }
}
