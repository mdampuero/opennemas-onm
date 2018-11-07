<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Validator\Constraints;

use Common\Core\Component\Validator\Constraints\BlackListWordsValidator;

/**
 * Defines test cases for BlackListWordsValidator class.
 */
class BlackListWordsValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->constraint = $this->getMockBuilder(
            'Common\Core\Component\Validator\Constraints\BlackListWords'
        )->getMock();

        $this->context = $this->getMockBuilder('Context')
            ->setMethods([
                'addViolation', 'buildViolation', 'setCode', 'setParameter'
            ])->getMock();

        $this->validator = new BlackListWordsValidator();
    }

    /**
     * Tests validate when the value to validate is empty.
     */
    public function testValidateWhenEmptyValue()
    {
        $this->assertEmpty($this->validator->validate(null, $this->constraint));
    }

    /**
     * Tests validate when the provided constraint is not valid.
     *
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateWhenNoValidConstraint()
    {
        $constraint = $this->getMockBuilder('Symfony\Component\Validator\Constraint')
            ->getMock();

        $this->validator->validate('quux', $constraint);
    }

    /**
     * Tests validate when the value to validate is not valid.
     *
     * @expectedException Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateWhenNoValidValue()
    {
        $this->validator->validate(
            json_decode(json_encode([ 'glork' => 'fred' ])),
            $this->constraint
        );
    }

    /**
     * Tests validate when the value matches the constraint.
     */
    public function testValidateWhenMatch()
    {
        $validator = $this->getMockBuilder('Common\Core\Component\Validator\Constraints\BlackListWordsValidator')
            ->setMethods([ 'match' ])
            ->getMock();

        $this->context->expects($this->once())->method('buildViolation')
            ->with('This value contains not allowed words.')
            ->willReturn($this->context);
        $this->context->expects($this->once())->method('setParameter')
            ->with('{{ value }}')->willReturn($this->context);
        $this->context->expects($this->once())->method('setCode')
            ->with('9f0865f1-8bee-4bc6-b936-15ef0f33ca7c')
            ->willReturn($this->context);

        $validator->expects($this->once())->method('match')
            ->willReturn(true);

        $property = new \ReflectionProperty($validator, 'context');
        $property->setAccessible(true);

        $property->setValue($validator, $this->context);

        $validator->validate('foo', $this->constraint);
    }

    /**
     * Tests validate when the value matches the constraint.
     */
    public function testValidateWhenNoMatch()
    {
        $validator = $this->getMockBuilder('Common\Core\Component\Validator\Constraints\BlackListWordsValidator')
            ->setMethods([ 'match' ])
            ->getMock();

        $validator->expects($this->once())->method('match')
            ->willReturn(false);

        $this->assertEmpty($validator->validate('foo', $this->constraint));
    }

    /**
     * Tests match when the provided blacklist is empty.
     */
    public function testMatchWhenEmptyBlackList()
    {
        $this->assertFalse($this->validator->match('frog', null));
        $this->assertFalse($this->validator->match('frog', []));
        $this->assertFalse($this->validator->match('frog', ''));
    }

    /**
     * Tests match when the value has invalid words included.
     */
    public function testMatchWhenInvalidWordsPresent()
    {
        $this->assertTrue($this->validator->match('frog bar foobar', [ 'bar' ]));
        $this->assertTrue($this->validator->match('mumble flob glork', "bar\nflob"));
        $this->assertTrue($this->validator->match('frog bar foobar', [ ' ', 'bar' ]));
    }

    /**
     * Tests match when the value has no invalid words included.
     */
    public function testMatchWhenNoInvalidWordsPresent()
    {
        $this->assertFalse($this->validator->match('frog garply foobar', [ 'bar' ]));
        $this->assertFalse($this->validator->match('cirugía', 'a'));
        $this->assertFalse($this->validator->match('grault,garply', [ '/^a/' ]));
        $this->assertTrue($this->validator->match('grault,garply', [ 'garply' ]));
        $this->assertTrue($this->validator->match('a', [ '/^a/' ]));
    }
}
