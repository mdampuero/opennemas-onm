<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Schema;

use Common\ORM\Core\Schema\Dumper;
use Common\ORM\Core\Schema\Schema;
use Common\ORM\Core\Metadata;

/**
 * Defines test cases for Dumper class.
 */
class DumperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $schemas = [
            'Foobar' => new Schema([ 'name' => 'Foobar', 'entities' => [ 'Foo' ] ]),
            'Wibble' => new Schema([ 'name' => 'Wibble', 'entities' => [ 'Bar' ] ])
        ];

        $entities = [
            'Foo' => new Metadata([
                'name' => 'Foo',
                'properties' => 'foo',
                'mapping' => [
                    'database' => [
                        'table' => 'foo',
                        'columns' => [
                            'id'       => [ 'type' => 'integer', 'options' => [ 'autoincrement' => true ] ],
                            'title'    => [ 'type' => 'string' ],
                            'category' => [ 'type' => 'string' ],
                            'date'     => [ 'type' => 'datetimetz' ]
                        ],
                        'index' => [
                            [ 'name' => 'PRIMARY', 'primary' => true, 'columns' => [ 'id' ] ],
                            [ 'name' => 'title', 'unique' => true, 'columns' => [ 'title' ] ],
                            [ 'name' => 'category', 'columns' => [ 'category' ] ]
                        ]
                    ]
                ]
            ]),
            'Bar' => new Metadata([
                'name'    => 'Bar',
                'mapping' => []
            ])
        ];

        $this->dumper = new Dumper($schemas, $entities);
    }

    /**
     * Tests contructor.
     */
    public function testConstructWithoutArguments()
    {
        new Dumper();

        $this->addToAssertionCount(1);
    }

    /**
     * Tests discover.
     */
    public function testDiscover()
    {
        $database = 'gorp';

        $manager = $this->getMockBuilder('SchemaManager')
            ->setMethods([ 'createSchema' ])
            ->getMock();

        $conn = $this->getMockBuilder('Connection')
            ->setMethods([ 'selectDatabase', 'getSchemaManager' ])
            ->getMock();

        $manager->expects($this->once())->method('createSchema');
        $conn->expects($this->once())->method('selectDatabase')->with($database);
        $conn->expects($this->once())->method('getSchemaManager')->willReturn($manager);

        $this->dumper->discover($conn, $database);
    }

    /**
     * Tests dump for an undefined schema.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testDumpInvalidSchema()
    {
        $this->dumper->dump('foo');
    }

    /**
     * Tests dump for an schema without database mapping information.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testDumpNoDatabaseInformation()
    {
        $this->dumper->dump('Wibble');
    }

    /**
     * Tests dump for a defined schema.
     */
    public function testDump()
    {
        $this->assertInstanceOf('Doctrine\DBAL\Schema\Schema', $this->dumper->dump('Foobar'));
    }

    /**
     * Tests validate for a schema without table name.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateWithoutTableName()
    {
        $this->dumper->validate([]);
    }

    /**
     * Tests validate for a schema for a invalid table name.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateInvalidTableName()
    {
        $this->dumper->validate([ 'table' => 'foo#bar' ]);
    }

    /**
     * Tests validate for a valid table name.
     */
    public function testValidate()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateTable' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateTable');

        $dumper->validate([ 'table' => 'gorp' ]);
    }

    /**
     * Tests validateField for a field without type.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateFieldWithoutType()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateField');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'foo', [] ]);
    }

    /**
     * Tests validateField for a field with a valid type.
     */
    public function testValidateField()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateOptions', 'validateType' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateType');
        $dumper->expects($this->once())->method('validateOptions');

        $method = new \ReflectionMethod($dumper, 'validateField');
        $method->setAccessible(true);

        $method->invokeArgs($dumper, [ 'gorp', 'foo', [ 'type' => 'integer', 'options' => [ 'default' => 0 ] ] ]);
    }

    /**
     * Tests validateFields.
     */
    public function testValidateFields()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateField' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateField');

        $method = new \ReflectionMethod($dumper, 'validateFields');
        $method->setAccessible(true);

        $method->invokeArgs($dumper, [ 'gorp', [ 'quux' => [ 'type' => 'integer' ] ] ]);
    }

    /**
     * Tests validateIndex for an index without name.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateIndexWithoutName()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateIndex');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [ 'foo' ], [] ]);
    }

    /**
     * Tests validateIndex for an index without columns.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateIndexWithUnknownColumns()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateIndex');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [ 'foo' ], [ 'name' => 'bar', 'columns' => [ 'foo', 'bar' ] ] ]);
    }

    /**
     * Tests validateIndex for an index with invalid flags.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateIndexWithInvalidFlags()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateIndex');
        $method->setAccessible(true);

        $method->invokeArgs(
            $this->dumper,
            [ 'gorp', [ 'foo' ], [ 'name' => 'bar', 'columns' => [ 'foo' ], 'primary' => 'baz' ] ]
        );
    }

    /**
     * Tests validateIndex for a valid index.
     */
    public function testValidateIndexValid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateIndex');
        $method->setAccessible(true);

        $method->invokeArgs(
            $this->dumper,
            [ 'gorp', [ 'foo' ], [ 'name' => 'bar', 'columns' => [ 'foo' ], 'primary' => true ] ]
        );

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateIndex with an already validated index.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateIndexesDuplicated()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateIndex' ])
            ->getMock();

        $dumper->expects($this->exactly(2))->method('validateIndex');

        $method = new \ReflectionMethod($dumper, 'validateIndexes');
        $method->setAccessible(true);

        $method->invokeArgs($dumper, [ 'gorp', [ 'quux' ], [ [ 'name' => 'id' ], [ 'name' => 'id' ] ] ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateIndexes.
     */
    public function testValidateIndexesValid()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateIndex' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateIndex');

        $method = new \ReflectionMethod($dumper, 'validateIndexes');
        $method->setAccessible(true);

        $method->invokeArgs($dumper, [ 'gorp', [ 'quux' ], [ [ 'name' => 'PRIMARY' ] ] ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateOption for an invalid option.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateOptionInvalid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateOption');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'quux', 'length', true ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateOption for a valid option.
     */
    public function testValidateOptionValid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateOption');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'quux', 'length', 10 ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateOptions with unrecognized options.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateOptionsInvalid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateOptions');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'quux', [ 'foo' => 'bar' ] ]);
    }

    /**
     * Tests validateOptions with valid options.
     */
    public function testValidateOptionsValid()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateOption' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateOption');

        $method = new \ReflectionMethod($dumper, 'validateOptions');
        $method->setAccessible(true);

        $method->invokeArgs($dumper, [ 'gorp', 'quux',  [ 'length' => '10' ] ]);
    }

    /**
     * Tests validateTable for a table without columns.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateTableNoColumns()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateTable');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [] ]);
    }

    /**
     * Tests validateTable for a table without indexes.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateTableNoIndexes()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateTable');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [ 'columns' => [ 'foo' => 'integer' ] ] ]);
    }

    /**
     * Tests validateTable for a valid table.
     */
    public function testValidateTableValid()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateFields', 'validateIndexes' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateFields');
        $dumper->expects($this->once())->method('validateIndexes');

        $method = new \ReflectionMethod($dumper, 'validateTable');
        $method->setAccessible(true);

        $method->invokeArgs(
            $dumper,
            [ 'gorp', [ 'columns' => [ 'foo' => 'integer' ], 'index' => [ 'name' => 'PRIMARY' ] ] ]
        );
    }

    /**
     * Tests validateType with an invalid type.
     *
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateTypeInvalid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateType');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'garply', 'quux' ]);
    }

    /**
     * Tests validateType with a valid type.
     */
    public function testValidateTypeValid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateType');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'garply', 'integer' ]);

        $this->addToAssertionCount(1);
    }
}
