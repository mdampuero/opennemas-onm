<?php

namespace Tests\Common\ORM\Core\Schema;

use Common\ORM\Core\Schema\Dumper;
use Common\ORM\Core\Schema\Schema;
use Common\ORM\Core\Entity;

class DumperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $schemas  = [ 'Foobar' => new Schema([ 'name' => 'Foobar', 'entities' => [ 'Foo' ] ]) ];
        $entities = [ 'Foo' => new Entity([
            'name' => 'Foo',
            'properties' => 'foo',
            'mapping' => [
                'table' => 'foo',
                'columns' => [
                    'id'       => [ 'type' => 'integer', 'options' => [ 'autoincrement' => true ] ],
                    'title'    => [ 'type' => 'string' ],
                    'category' => [ 'type' => 'string' ]
                ],
                'index' => [
                    [ 'name' => 'PRIMARY', 'primary' => true, 'columns' => [ 'id' ] ],
                    [ 'name' => 'title', 'unique' => true, 'columns' => [ 'title' ] ],
                    [ 'name' => 'category', 'columns' => [ 'category' ] ]
                ]
            ]
        ])];

        $this->dumper = new Dumper($schemas, $entities);
    }

    public function testConstructWithoutArguments()
    {
        new Dumper();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDumpInvalidSchema()
    {
        $this->dumper->dump('foo');
    }

    public function testDump()
    {
        $this->assertInstanceOf('Doctrine\DBAL\Schema\Schema', $this->dumper->dump('Foobar'));
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateWithoutTableName()
    {
        $this->dumper->validate([]);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateInvalidTableName()
    {
        $this->dumper->validate([ 'table' => 'foo#bar' ]);
    }

    public function testValidate()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateTable' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateTable');

        $dumper->validate([ 'table' => 'gorp' ]);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateFieldWithoutType()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateField');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'foo', [] ]);
    }

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
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateIndexWithoutName()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateIndex');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [ 'foo' ], [] ]);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateIndexWithUnknownColumns()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateIndex');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [ 'foo' ], [ 'name' => 'bar', 'columns' => [ 'foo', 'bar' ] ] ]);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateIndexWithInvalidFlags()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateIndex');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [ 'foo' ], [ 'name' => 'bar', 'columns' => [ 'foo' ], 'primary' => 'baz' ] ]);
    }

    public function testValidateIndexValid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateIndex');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [ 'foo' ], [ 'name' => 'bar', 'columns' => [ 'foo' ], 'primary' => true ] ]);
    }

    /**
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
    }

    public function testValidateIndexesValid()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateIndex' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateIndex');

        $method = new \ReflectionMethod($dumper, 'validateIndexes');
        $method->setAccessible(true);

        $method->invokeArgs($dumper, [ 'gorp', [ 'quux' ], [ [ 'name' => 'PRIMARY' ] ] ]);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateOptionInvalid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateOption');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'quux', 'length', true ]);
    }

    public function testValidateOptionValid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateOption');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'quux', 'length', 10 ]);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateOptionsInvalid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateOptions');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'quux', [ 'foo' => 'bar' ] ]);
    }

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
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateTableNoColumns()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateTable');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [] ]);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateTableNoIndexes()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateTable');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', [ 'columns' => [ 'foo' => 'integer' ] ] ]);
    }

    public function testValidateTableValid()
    {
        $dumper = $this->getMockBuilder('Common\ORM\Core\Schema\Dumper')
            ->setMethods([ 'validateFields', 'validateIndexes' ])
            ->getMock();

        $dumper->expects($this->once())->method('validateFields');
        $dumper->expects($this->once())->method('validateIndexes');

        $method = new \ReflectionMethod($dumper, 'validateTable');
        $method->setAccessible(true);

        $method->invokeArgs($dumper, [ 'gorp', [ 'columns' => [ 'foo' => 'integer' ], 'index' => [ 'name' => 'PRIMARY' ] ] ]);
    }

    /**
     * @expectedException Common\ORM\Core\Exception\InvalidSchemaException
     */
    public function testValidateTypeInvalid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateType');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'garply', 'quux' ]);
    }

    public function testValidateTypeValid()
    {
        $method = new \ReflectionMethod($this->dumper, 'validateType');
        $method->setAccessible(true);

        $method->invokeArgs($this->dumper, [ 'gorp', 'garply', 'integer' ]);
    }
}
