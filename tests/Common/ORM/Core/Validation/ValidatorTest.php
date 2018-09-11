<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Validation;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Client;
use Common\ORM\Entity\Theme;
use Common\ORM\Core\Validation\Validator;

/**
 * Defines test cases for Validator class.
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->client = new Client([ 'foo' => 'bar', 'baz' => 'qux' ]);

        $this->validations = [
            new Metadata([
                'name' => 'client',
                'properties' => [
                    'foo'    => 'string',
                    'baz'    => [ 'string' ],
                    'garply' => 'enum',
                    'corge'  => 'entity::client',
                    'wobble'  => 'array::key=>thud:integer',
                ],
                'enum' => [
                    'garply' => [ 'grault' ]
                ]
            ]),
            new Metadata([ 'name' => 'extension', 'required' => [ 'foo' ] ]),
        ];

        // Mock constructor only
        $this->validator = new Validator($this->validations);

        $reflection = new \ReflectionClass($this->validator);

        $this->properties['enum']          = $reflection->getProperty('enum');
        $this->properties['properties']    = $reflection->getProperty('properties');
        $this->properties['required']      = $reflection->getProperty('required');
        $this->properties['rulesets']      = $reflection->getProperty('rulesets');
        $this->methods['isArray']          = $reflection->getMethod('isArray');
        $this->methods['isBoolean']        = $reflection->getMethod('isBoolean');
        $this->methods['isDateinterval']   = $reflection->getMethod('isDateinterval');
        $this->methods['isDatetime']       = $reflection->getMethod('isDatetime');
        $this->methods['isEntity']         = $reflection->getMethod('isEntity');
        $this->methods['isEnum']           = $reflection->getMethod('isEnum');
        $this->methods['isFloat']          = $reflection->getMethod('isFloat');
        $this->methods['isInteger']        = $reflection->getMethod('isInteger');
        $this->methods['isNull']           = $reflection->getMethod('isNull');
        $this->methods['isObject']         = $reflection->getMethod('isObject');
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

    /**
     * Tests constructor when empty validations provided.
     */
    public function testConstructor()
    {
        new Validator(false);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests loadValidation when the validation to load is already loaded.
     */
    public function testLoadRulesAlreadySet()
    {
        $this->methods['loadValidation']->invokeArgs($this->validator, [ $this->validations[0] ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validate when the ruleset for the entity isn't loaded.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidEntityException
     */
    public function testValidateInvalidRuleset()
    {
        $this->validator->validate(new Entity([ 'corge' => 'flob' ]));

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validate when the entity data are invalid.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidEntityException
     */
    public function testValidateInvalid()
    {
        $this->validator->validate(new Client([ 'corge' => 'flob' ]));

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validate for valid entity data.
     */
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

        $this->addToAssertionCount(1);
    }

    /**
     * Tests isArray with valid and invalid data.
     */
    public function testIsArray()
    {
        $this->assertTrue($this->methods['isArray']->invokeArgs($this->validator, [ [] ]));
        $this->assertFalse($this->methods['isArray']->invokeArgs($this->validator, [ 'foo' ]));
    }

    /**
     * Tests isBoolean with valid and invalid data.
     */
    public function testIsBoolean()
    {
        $this->assertTrue($this->methods['isBoolean']->invokeArgs($this->validator, [ true ]));
        $this->assertFalse($this->methods['isBoolean']->invokeArgs($this->validator, [ 'foo' ]));
    }

    /**
     * Tests isDateInterval with valid and invalid data.
     */
    public function testIsDateInterval()
    {
        $this->assertTrue($this->methods['isDateinterval']->invokeArgs($this->validator, [ new \DateInterval('P1D') ]));
        $this->assertFalse($this->methods['isDateinterval']->invokeArgs($this->validator, [ 1 ]));
        $this->assertFalse($this->methods['isDateinterval']->invokeArgs($this->validator, [ 'foo' ]));
    }

    /**
     * Tests isDateTime with valid and invalid data.
     */
    public function testIsDateTime()
    {
        $this->assertTrue($this->methods['isDatetime']->invokeArgs($this->validator, [ new \Datetime('now') ]));
        $this->assertFalse($this->methods['isDatetime']->invokeArgs($this->validator, [ 1 ]));
        $this->assertFalse($this->methods['isDatetime']->invokeArgs($this->validator, [ 'foo' ]));
    }

    /**
     * Tests isEntity with valid and invalid data.
     */
    public function testIsEntity()
    {
        $this->assertTrue($this->methods['isEntity']->invokeArgs($this->validator, [ new Entity([]) ]));
        $this->assertFalse($this->methods['isEntity']->invokeArgs($this->validator, [ 'norf' ]));
    }

    /**
     * Tests isEnum with valid and invalid data.
     */
    public function testIsEnum()
    {
        $this->assertTrue($this->methods['isEnum']->invokeArgs($this->validator, [ 'grault', 'client', 'garply' ]));
        $this->assertFalse($this->methods['isEnum']->invokeArgs($this->validator, [ 'norf', 'client', 'foo' ]));
    }

    /**
     * Tests isFloat with valid and invalid data.
     */
    public function testIsFloat()
    {
        $this->assertTrue($this->methods['isFloat']->invokeArgs($this->validator, [ 1.1 ]));
        $this->assertFalse($this->methods['isFloat']->invokeArgs($this->validator, [ [] ]));
        $this->assertFalse($this->methods['isFloat']->invokeArgs($this->validator, [ 'foo' ]));
    }

    /**
     * Tests isInteger with valid and invalid data.
     */
    public function testIsInteger()
    {
        $this->assertTrue($this->methods['isInteger']->invokeArgs($this->validator, [ 1 ]));
        $this->assertFalse($this->methods['isInteger']->invokeArgs($this->validator, [ [] ]));
        $this->assertFalse($this->methods['isInteger']->invokeArgs($this->validator, [ 'foo' ]));
    }

    /**
     * Tests isNull with valid and invalid data.
     */
    public function testIsNull()
    {
        $this->assertTrue($this->methods['isNull']->invokeArgs($this->validator, [ 'foo', 'bar', null ]));
        $this->assertTrue($this->methods['isNull']->invokeArgs($this->validator, [ 'extension', 'foo', null ]));
        $this->assertFalse($this->methods['isNull']->invokeArgs($this->validator, [ 'extension', 'foo', 1 ]));
        $this->assertFalse($this->methods['isNull']->invokeArgs($this->validator, [ 'foo', 'bar', [] ]));
    }

    /**
     * Tests isObject with valid and invalid data.
     */
    public function testIsObject()
    {
        $this->assertTrue($this->methods['isObject']->invokeArgs($this->validator, [ $this->client ]));
        $this->assertFalse($this->methods['isObject']->invokeArgs($this->validator, [ 1 ]));
        $this->assertFalse($this->methods['isObject']->invokeArgs($this->validator, [ 'foo' ]));
    }

    /**
     * Tests isString with valid and invalid data.
     */
    public function testIsString()
    {
        $this->assertTrue($this->methods['isString']->invokeArgs($this->validator, [ 'foo' ]));
        $this->assertFalse($this->methods['isString']->invokeArgs($this->validator, [ 1 ]));
        $this->assertFalse($this->methods['isString']->invokeArgs($this->validator, [ [] ]));
    }

    /**
     * Tests validateProperty  with valid and invalid data.
     */
    public function testValidateProperty()
    {
        $this->assertFalse($this->methods['validateProperty']->invokeArgs($this->validator, [ 'client', 'foo', 1 ]));
        $this->assertTrue($this->methods['validateProperty']->invokeArgs(
            $this->validator,
            [ 'client', 'woomble', 'wumble' ]
        ));
        $this->assertFalse($this->methods['validateProperty']->invokeArgs(
            $this->validator,
            [ 'client', 'foo', 1 ]
        ));
        $this->assertTrue($this->methods['validateProperty']->invokeArgs(
            $this->validator,
            [ 'client', 'wobble', [ 1 ] ]
        ));
    }

    /**
     * Tests validateData with invalid data.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidEntityException
     */
    public function testValidateDataInvalid()
    {
        $entity = new Client([ 'foo' => 1 ]);
        $this->methods['validateData']->invokeArgs($this->validator, [ 'client', $entity->getData() ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateData with valid data.
     */
    public function testValidateDataValid()
    {
        $this->methods['validateData']->invokeArgs($this->validator, [ 'client', $this->client->getData() ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateRequired with valid data.
     */
    public function testValidateRequired()
    {
        $this->methods['validateRequired']->invokeArgs($this->validator, [ 'client', $this->client->getData() ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateRequired with no required data.
     */
    public function testValidateRequiredEmpty()
    {
        $this->properties['required']->setValue($this->validator, []);
        $this->methods['validateRequired']->invokeArgs($this->validator, [ 'client', $this->client->getData() ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validateRequired with missing required data.
     *
     * @expectedException \Common\ORM\Core\Exception\InvalidEntityException
     */
    public function testValidateRequiredMissing()
    {
        $this->properties['required']->setValue($this->validator, [ 'client' => [ 'norf' ] ]);
        $this->methods['validateRequired']->invokeArgs($this->validator, [ 'client', $this->client->getData() ]);

        $this->addToAssertionCount(1);
    }
}
