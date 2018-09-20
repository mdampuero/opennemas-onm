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

use Common\Core\Component\Locale\Locale;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Data\Converter\Converter;

/**
 * Defines test cases for Converter class.
 */
class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->locale = new Locale([ 'en_US' ], 'path/to/foo');
        $this->locale->setLocale('en_US');

        $this->metadata = new Metadata([
            'translate'  => [ 'baz' ],
            'properties' => [
                'foo'    => 'string',
                'bar'    => 'integer',
                'baz'    => 'array',
                'norf'   => 'boolean',
                'wobble' => 'string',
                'mumble' => 'datetime',
                'quux'   => 'integer',
            ],
            'mapping' => [
                'database' => [
                    'columns' => [
                        'foo'  => [ 'type' => 'text' ],
                        'bar'  => [ 'type' => 'integer' ],
                        'baz'  => [ 'type' => 'array_json' ],
                        'norf' => [ 'type' => 'boolean' ],
                        'quux' => [ 'type' => 'integer', 'options' => [ 'default' => null ] ],
                    ],
                    'hasMetas' => true
                ]
            ],
        ]);

        $this->converter = new Converter($this->metadata, $this->locale);

        $keys = [ 'convert', 'convertFrom', 'convertTo', 'translate' ];

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
                'foo'    => 'foobar',
                'bar'    => 1,
                'baz'    => [ 'garply', 'gorp' ],
                'norf'   => false,
                'mumble' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2010-10-01 10:10:10',
                    new \DateTimeZone('UTC')
                )
            ],
            $this->converter->objectify([
                'foo'    => 'foobar',
                'bar'    => '1',
                'baz'    => serialize([ 'garply', 'gorp' ]),
                'norf'   => '0',
                'mumble' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2010-10-01 10:10:10',
                    new \DateTimeZone('UTC')
                )
            ])
        );
    }

    /**
     * Tests objectify with an array of enitty data.
     */
    public function testObjectifyWithArray()
    {
        $this->assertEquals(
            [
                [ 'foo' => 'fred' ],
                [ 'norf' => true ],
            ],
            $this->converter->objectify([
                [ 'foo' => 'fred' ],
                [ 'norf' => 1 ]
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
                'baz' => 'wubble'
            ],
            $this->converter->responsify([
                'foo' => new \DateTime('2000-01-01 10:00:00'),
                'bar' => new Entity([ 'gorp' => 'norf' ]),
                'baz' => [ 'en' => 'wubble' ]
            ], true)
        );

        $this->assertEquals(
            [
                'foo' => '2000-01-01 10:00:00',
                'bar' => [ 'gorp' => 'norf' ],
                'baz' => [ 'en' => 'wubble' ]
            ],
            $this->converter->responsify([
                'foo' => new \DateTime('2000-01-01 10:00:00'),
                'bar' => new Entity([ 'gorp' => 'norf' ]),
                'baz' => [ 'en' => 'wubble' ]
            ])
        );

        $this->assertEquals(
            [
                'foo'               => '2000-01-01 10:00:00',
                'bar'               => [ 'gorp' => 'norf' ],
                'baz'               => [ 'en' => 'wubble' ],
                'not_in_meta_field' => null
            ],
            $this->converter->responsify([
                'foo' => new \DateTime('2000-01-01 10:00:00'),
                'bar' => new Entity([ 'gorp' => 'norf' ]),
                'baz' => [ 'en' => 'wubble' ],
                'not_in_meta_field' => null
            ])
        );

        $this->assertEquals(
            [
                'foo' => '2000-01-01 10:00:00',
                'bar' => [ 'gorp' => 'norf' ],
                'baz' => []
            ],
            $this->converter->responsify([
                'foo' => new \DateTime('2000-01-01 10:00:00'),
                'bar' => new Entity([ 'gorp' => 'norf' ]),
                'baz' => null
            ])
        );
    }

    /**
     * Tests responsify with an array of enitty data.
     */
    public function testResponsifyWithArray()
    {
        $this->assertEquals(
            [
                [ 'foo' => '2000-01-01 10:00:00' ],
                [ 'baz' => true ],
            ],
            $this->converter->responsify([
                new Entity([ 'foo' => new \DateTime('2000-01-01 10:00:00') ]),
                [ 'baz' => true ]
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

    /**
     * Tests convertTo
     */
    public function testTranslate()
    {
        $this->assertEmpty($this->methods['translate']->invokeArgs($this->converter, [ 'baz', null ]));

        $this->assertEquals(
            [ 'corge' ],
            $this->methods['translate']->invokeArgs($this->converter, [ 'foo', [ 'corge' ] ])
        );

        $this->assertEquals(
            'wobble',
            $this->methods['translate']->invokeArgs($this->converter, [ 'baz', 'wobble' ])
        );

        $this->assertEquals(
            'wobble',
            $this->methods['translate']->invokeArgs($this->converter, ['baz', [ 'en' => 'wobble' ] ])
        );

        $this->assertEquals(
            'grault',
            $this->methods['translate']->invokeArgs($this->converter, ['baz', [ 'es' => 'grault' ] ])
        );
    }
}
