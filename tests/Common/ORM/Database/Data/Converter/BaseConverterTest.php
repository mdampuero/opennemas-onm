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
class BaseConverterTest extends \PHPUnit\Framework\TestCase
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
                'glorp'  => 'array::a=>b:integer',
                'mumble' => 'datetime',
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
                    'hasMetas' => true,
                    'relations' => [
                        'glorp' => [
                            'table' => 'glorp_table',
                            'ids'   => [ 'foo' => 'foo_id' ]
                        ]
                    ]
                ]
            ],
        ]);

        $this->converter = new BaseConverter($this->metadata);
    }

    /**
     * Tests databasify when empty values provided.
     */
    public function testDatabasifyEmpty()
    {
        $this->assertEquals([ [], [], [], [] ], $this->converter->databasify([]));
    }
    /**
     * Tests databasify when empty metadata provided.
     *
     * @expectedException \Exception
     */
    public function testDatabasifyInvalid()
    {
        $converter = new BaseConverter(new Metadata([]));
        $converter->databasify([ 'foo' => 'glorp' ]);
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
                [ 'wobble' => 'wubble', 'mumble' => null ],
                [
                    'foo'  => \PDO::PARAM_STR,
                    'bar'  => \PDO::PARAM_INT,
                    'baz'  => \PDO::PARAM_STR,
                    'norf' => \PDO::PARAM_BOOL,
                    'quux' => \PDO::PARAM_STR,
                ],
                [
                    'glorp' => [
                        [ 'a' => 1 ]
                    ]
                ]
            ],
            $this->converter->databasify([
                'foo'    => 'foobar',
                'bar'    => 1,
                'baz'    => [ 'garply', 'gorp' ],
                'norf'   => false,
                'wobble' => 'wubble',
                'glorp'  => [ [ 'a' => 1 ] ]
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
                    [ 'wobble' => null, 'mumble' => null ],
                    [
                        'foo'  => \PDO::PARAM_STR,
                        'bar'  => \PDO::PARAM_STR,
                        'baz'  => \PDO::PARAM_STR,
                        'norf' => \PDO::PARAM_STR,
                        'quux' => \PDO::PARAM_STR,
                    ],
                    [
                        'glorp' => [
                            [ 'a' => 1 ]
                        ]
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
                    [ 'wobble' => null, 'mumble' => null ],
                    [
                        'foo'  => \PDO::PARAM_STR,
                        'bar'  => \PDO::PARAM_INT,
                        'baz'  => \PDO::PARAM_STR,
                        'norf' => \PDO::PARAM_STR,
                        'quux' => \PDO::PARAM_STR,
                    ],
                    [
                        'glorp' => [
                            [ 'a' => 2 ]
                        ]
                    ]
                ],
            ],
            $this->converter->databasify([
                [ 'foo' => 'foobar', 'glorp' => [ [ 'a' => 1 ] ] ],
                [ 'bar' => 1, 'glorp' => [ [ 'a' => 2 ] ] ],
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
            $this->converter->objectifyStrict([
                'foo'    => 'foobar',
                'bar'    => 1,
                'baz'    => '["garply","gorp"]',
                'norf'   => 0,
                'mumble' => \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    '2010-10-01 10:10:10',
                    new \DateTimeZone('UTC')
                )
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

    /**
     * Tests objectify when no mapping information.
     */
    public function testObjectifyStrictWhenNoMapping()
    {
        $data = [
            'foo' => 'foobar',
            'bar' => 1,
            'baz' => '["garply","gorp"]',
            'norf' => 0
        ];

        $this->metadata->mapping = null;

        $this->assertEquals($data, $this->converter->objectifyStrict($data, true));
    }
}
