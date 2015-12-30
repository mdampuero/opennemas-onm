<?php

namespace Framework\Tests\ORM\Core\Validator;

use Framework\ORM\Exception\InvalidSchemaException;
use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Validation;
use Framework\ORM\Entity\Client;
use Framework\ORM\Entity\Theme;
use Framework\ORM\Core\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new Client([ 'foo' => 'bar', 'baz' => 'qux' ]);

        $this->validations = [
            new Validation([
                'entity' => [
                    'name' => 'client',
                    'properties' => [
                        'foo'    => 'string',
                        'baz'    => [ 'string' ],
                        'garply' => 'enum',
                        'corge'  => 'integer',
                    ],
                    'enum' => [
                        'garply' => [ 'grault' ]
                    ]
                ]
            ]),
            new Validation([
                'entity' => [
                    'name' => 'extension'
                ]
            ]),
        ];


        // Mock constructor only
        $this->validator = new Validator($this->validations);

        $reflection = new \ReflectionClass($this->validator);

        $this->properties['enum']          = $reflection->getProperty('enum');
        $this->properties['properties']    = $reflection->getProperty('properties');
        $this->properties['required']      = $reflection->getProperty('required');
        $this->properties['rulesets']      = $reflection->getProperty('rulesets');
        $this->methods['isArray']          = $reflection->getMethod('isArray');
        $this->methods['isDouble']         = $reflection->getMethod('isDouble');
        $this->methods['isEnum']           = $reflection->getMethod('isEnum');
        $this->methods['isInteger']        = $reflection->getMethod('isInteger');
        $this->methods['isNumeric']        = $reflection->getMethod('isNumeric');
        $this->methods['isString']         = $reflection->getMethod('isString');
        $this->methods['loadValidation']   = $reflection->getMethod('loadValidation');
        $this->methods['validateProperty'] = $reflection->getMethod('validateProperty');
        $this->methods['validateData']     = $reflection->getMethod('validateData');
        $this->methods['validateRequired'] = $reflection->getMethod('validateRequired');

        foreach ($this->properties as $property) {
            $property->setAccessible(true);
        }

        foreach ($this->methods as $method) {
            $method->setAccessible(true);
        }
    }

    public function testConstructor()
    {
        new Validator(false);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadRulesAlreadySet()
    {
        $this->methods['loadValidation']->invokeArgs($this->validator, [ $this->validations[0] ]);
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidEntityException
     */
    public function testValidateInvalidRuleset()
    {
        $this->validator->validate(new Entity([ 'corge' => 'flob' ]));
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidEntityException
     */
    public function testValidateInvalid()
    {
        $this->validator->validate(new Client([ 'corge' => 'flob' ]));
    }

    public function testValidateValid()
    {
        $this->properties['required']->setValue($this->validator, [ 'client' => [ 'foo' ] ]);
        $this->properties['properties']->setValue($this->validator, [
            'client' => [
                'foo' => 'string',
                'baz' => [ 'string' ]
            ]
        ]);

        $this->validator->validate($this->client);
    }

    public function testIsArray()
    {
        $this->assertTrue($this->methods['isArray']->invokeArgs($this->validator, [ [] ]));
        $this->assertFalse($this->methods['isArray']->invokeArgs($this->validator, [ 'foo' ]));
    }

    public function testIsDouble()
    {
        $this->assertTrue($this->methods['isDouble']->invokeArgs($this->validator, [ 1.1 ]));
        $this->assertFalse($this->methods['isDouble']->invokeArgs($this->validator, [ [] ]));
        $this->assertFalse($this->methods['isDouble']->invokeArgs($this->validator, [ 'foo' ]));
    }

    public function testIsEnum()
    {
        $this->assertTrue($this->methods['isEnum']->invokeArgs($this->validator, [ 'grault', 'client', 'garply' ]));
        $this->assertFalse($this->methods['isEnum']->invokeArgs($this->validator, [ 'norf', 'client', 'foo' ]));
    }

    public function testIsInteger()
    {
        $this->assertTrue($this->methods['isInteger']->invokeArgs($this->validator, [ 1 ]));
        $this->assertFalse($this->methods['isInteger']->invokeArgs($this->validator, [ [] ]));
        $this->assertFalse($this->methods['isInteger']->invokeArgs($this->validator, [ 'foo' ]));
    }

    public function testIsNumeric()
    {
        $this->assertTrue($this->methods['isNumeric']->invokeArgs($this->validator, [ 1 ]));
        $this->assertTrue($this->methods['isNumeric']->invokeArgs($this->validator, [ 1.1 ]));
        $this->assertFalse($this->methods['isNumeric']->invokeArgs($this->validator, [ [] ]));
        $this->assertFalse($this->methods['isNumeric']->invokeArgs($this->validator, [ 'foo' ]));
    }

    public function testIsString()
    {
        $this->assertTrue($this->methods['isString']->invokeArgs($this->validator, [ 'foo' ]));
        $this->assertFalse($this->methods['isString']->invokeArgs($this->validator, [ 1 ]));
        $this->assertFalse($this->methods['isString']->invokeArgs($this->validator, [ [] ]));
    }

    public function testValidateProperty()
    {
        $this->assertFalse($this->methods['validateProperty']->invokeArgs($this->validator, [ 'client', 'foo', 1 ]));
        $this->assertTrue($this->methods['validateProperty']->invokeArgs($this->validator, [ 'client', 'woomble', 'wumble' ]));
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidEntityException
     */
    public function testValidateDataInvalid()
    {
        $entity = new Client([ 'foo' => 1 ]);
        $this->methods['validateData']->invokeArgs($this->validator, [ 'client', $entity->getData() ]);
    }

    public function testValidateDataValid()
    {
        $this->methods['validateData']->invokeArgs($this->validator, [ 'client', $this->client->getData() ]);
    }

    public function testValidateRequired()
    {
        $this->methods['validateRequired']->invokeArgs($this->validator, [ 'client', $this->client->getData() ]);
    }

    public function testValidateRequiredEmpty()
    {
        $this->properties['required']->setValue($this->validator, []);
        $this->methods['validateRequired']->invokeArgs($this->validator, [ 'client', $this->client->getData() ]);
    }

    /**
     * @expectedException \Framework\ORM\Exception\InvalidEntityException
     */
    public function testValidateRequiredMissing()
    {
        $this->properties['required']->setValue($this->validator, [ 'client' => [ 'norf' ] ]);
        $this->methods['validateRequired']->invokeArgs($this->validator, [ 'client', $this->client->getData() ]);
    }
}
