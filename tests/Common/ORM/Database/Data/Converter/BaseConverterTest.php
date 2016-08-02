<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Database\Data\Converter;

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

        $this->converter = new BaseConverter($this->metadata);
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
                    'quux' => null,
                ],
                [ 'wobble' => 'wubble' ],
                [
                    'foo'  => \PDO::PARAM_STR,
                    'bar'  => \PDO::PARAM_INT,
                    'baz'  => \PDO::PARAM_STR,
                    'norf' => \PDO::PARAM_BOOL,
                    'quux' => \PDO::PARAM_STR,
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
     * Tests databasify when valid metadata provided.
     */
    public function testDatabasifyValidWithArray()
    {
        $this->assertEquals(
            [
                [
                    [
                        'foo'  => 'foobar',
                        'bar'  => null,
                        'baz'  => null,
                        'norf' => null,
                        'quux' => null,
                    ],
                    [ 'wobble' => null ],
                    [
                        'foo'  => \PDO::PARAM_STR,
                        'bar'  => \PDO::PARAM_STR,
                        'baz'  => \PDO::PARAM_STR,
                        'norf' => \PDO::PARAM_STR,
                        'quux' => \PDO::PARAM_STR,
                    ]
                ],
                [
                    [
                        'foo'  => null,
                        'bar'  => 1,
                        'baz'  => null,
                        'norf' => null,
                        'quux' => null,
                    ],
                    [ 'wobble' => null ],
                    [
                        'foo'  => \PDO::PARAM_STR,
                        'bar'  => \PDO::PARAM_INT,
                        'baz'  => \PDO::PARAM_STR,
                        'norf' => \PDO::PARAM_STR,
                        'quux' => \PDO::PARAM_STR,
                    ]
                ]
            ],
            $this->converter->databasify([
                [ 'foo' => 'foobar' ],
                [ 'bar' => 1 ],
            ])
        );
    }

    /**
     * Tests objectify for data from database.
     */
    public function testObjectifyStrict()
    {
        $this->assertEquals(
            [
                'foo' => 'foobar',
                'bar' => 1,
                'baz' => [ 'garply', 'gorp' ],
                'norf' => false
            ],
            $this->converter->objectifyStrict([
                'foo' => 'foobar',
                'bar' => 1,
                'baz' => '["garply","gorp"]',
                'norf' => 0
            ], true)
        );
    }

    /**
     * Tests objectify for an array of data from database.
     */
    public function testObjectifyStrictWithArray()
    {
        $this->assertEquals(
            [
                [ 'foo' => 'foobar' ],
                [ 'bar' => 1 ],
            ],
            $this->converter->objectifyStrict([
                [ 'foo' => 'foobar' ],
                [ 'bar' => 1 ],
            ], true)
        );
    }
}
