<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Database\Data\Mapper;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Database\Data\Converter\BaseConverter;

/**
 * Defines test cases for BaseConverter class.
 */
class BaseConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
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

        $this->converter = new BaseConverter($this->metadata);

        $keys    = [ 'convert', 'convertFrom', 'convertTo' ];
        $this->methods = [];
        foreach ($keys as $method) {
            $this->methods[$method] = new \ReflectionMethod($this->converter, $method);
            $this->methods[$method]->setAccessible(true);
        }
    }

    /**
     * Tests databasify when empty metadata provided.
     *
     * @expectedException \Exception
     */
    public function testDatabasifyInvalid()
    {
        $converter = new BaseConverter(new Metadata([]));
        $converter->databasify([]);
    }

    /**
     * Tests databasify when valid metadata provided.
     */
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
                [ 'wobble' => 'wubble' ],
                [
                    'foo'  => \PDO::PARAM_STR,
                    'bar'  => \PDO::PARAM_INT,
                    'baz'  => \PDO::PARAM_STR,
                    'norf' => \PDO::PARAM_BOOL,
                ]
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

    /**
     * Tests objectify for data from database.
     */
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

    /**
     * Tests objectify for data from a request.
     */
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

    /**
     * Tests objectify when empty metadata provided.
     */
    public function testObjectifyWithoutMetadata()
    {
        $converter = new BaseConverter(new Metadata());
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
