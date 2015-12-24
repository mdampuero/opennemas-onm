<?php

namespace Framework\Tests\ORM\Validator;

use Framework\ORM\Exception\InvalidSchemaException;
use Framework\ORM\Core\Entity;
use Framework\ORM\Entity\Client;
use Framework\ORM\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = new Entity([ 'foo' => 'bar', 'baz' => 'qux' ]);

        // Mock constructor only
        $this->validator = \Mockery::mock('\Framework\ORM\Validator\Validator')
            ->shouldDeferMissing();

        $reflection = new \ReflectionClass($this->validator);

        $this->properties['enum']       = $reflection->getProperty('enum');
        $this->properties['properties'] = $reflection->getProperty('properties');
        $this->properties['required']   = $reflection->getProperty('required');

        foreach ($this->properties as $property) {
            $property->setAccessible(true);
        }

        $this->properties['required']->setValue($this->validator, [ 'entity' => [ 'foo' ] ]);
        $this->properties['properties']->setValue($this->validator, [
            'entity' => [
                'foo' => 'string',
                'baz' => [ 'string' ]
            ],
            'extension' => []
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPath()
    {
        new Validator('foo/bar');
    }

    public function testConstructor()
    {
        new Validator(__DIR__ . '/../../../../src/Framework/Resources/config/orm/validation');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadRulesAlreadySet()
    {
        $this->validator->loadRules(__DIR__ . '/../../../../../src/Framework/Resources/config/orm/validation/extension.yml');
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidEntityException
     */
    public function testValidateInvalidRuleset()
    {
        $this->validator->validate(new Client([ 'corge' => 'flob' ]));
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidEntityException
     */
    public function testValidateInvalid()
    {
        $this->validator->validate(new Entity([ 'corge' => 'flob' ]));
    }

    public function testValidateValid()
    {
        $this->properties['required']->setValue($this->validator, [ 'entity' => [ 'foo' ] ]);
        $this->properties['properties']->setValue($this->validator, [
            'entity' => [
                'foo' => 'string',
                'baz' => [ 'string' ]
            ]
        ]);

        $this->validator->validate($this->entity);
    }

    public function testIsArray()
    {
        $this->assertTrue($this->validator->isArray([]));
        $this->assertFalse($this->validator->isArray('foo'));
    }

    public function testIsDouble()
    {
        $this->assertTrue($this->validator->isDouble(1.1));
        $this->assertFalse($this->validator->isDouble([]));
        $this->assertFalse($this->validator->isDouble('foo'));
    }

    public function testIsEnum()
    {
        $this->properties['enum']->setValue($this->validator, [ 'entity' => [ 'foo' => [ 'bar' ] ] ]);

        $this->assertTrue($this->validator->isEnum('bar', 'entity', 'foo'));
        $this->assertFalse($this->validator->isEnum('norf', 'entity', 'foo'));
    }

    public function testIsInteger()
    {
        $this->assertTrue($this->validator->isInteger(1));
        $this->assertFalse($this->validator->isInteger([]));
        $this->assertFalse($this->validator->isInteger('foo'));
    }

    public function testIsNumeric()
    {
        $this->assertTrue($this->validator->isNumeric(1));
        $this->assertTrue($this->validator->isNumeric(1.1));
        $this->assertFalse($this->validator->isNumeric([]));
        $this->assertFalse($this->validator->isNumeric('foo'));
    }

    public function testIsString()
    {
        $this->assertTrue($this->validator->isString('foo'));
        $this->assertFalse($this->validator->isString(1));
        $this->assertFalse($this->validator->isString([]));
    }

    public function testValidatePropertyInvalid()
    {
        $this->assertFalse($this->validator->validateProperty('entity', 'norf', 'glorp'));
        $this->assertFalse($this->validator->validateProperty('entity', 'foo', 1));
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidEntityException
     */
    public function testValidateDataInvalid()
    {
        $entity = new Entity([ 'glork' => 'glorp' ]);
        $this->validator->validateData('entity', $entity->getData());
    }

    public function testValidateDataValid()
    {
        $this->validator->validateData('entity', $this->entity->getData());
    }

    public function testValidateRequired()
    {
        $this->validator->validateRequired('entity', $this->entity->getData());
    }

    public function testValidateRequiredEmpty()
    {
        $this->properties['required']->setValue($this->validator, []);
        $this->validator->validateRequired('entity', $this->entity->getData());
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidEntityException
     */
    public function testValidateRequiredMissing()
    {
        $this->properties['required']->setValue($this->validator, [ 'entity' => [ 'norf' ] ]);
        $this->validator->validateRequired('entity', $this->entity->getData());
    }
}
