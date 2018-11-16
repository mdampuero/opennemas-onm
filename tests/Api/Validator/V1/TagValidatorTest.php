<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\Validator\V1;

use Api\Validator\V1\TagValidator;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for TagValidator class.
 */
class TagValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->coreValidator = $this->getMockBuilder('CoreValidator')
            ->setMethods([ 'validate' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('core.validator')->willReturn($this->coreValidator);

        $this->validator = new TagValidator($this->container);
    }

    /**
     * Tests validate when type changed and valid type provided.
     */
    public function testValidateWhenValid()
    {
        $this->coreValidator->expects($this->any())->method('validate')
            ->willReturn([]);

        $this->validator->validate(new Entity([ 'name' => 'flob' ]));
        $this->addToAssertionCount(1);
    }

    /**
     * Tests validate when there are no changes in type.
     *
     * @expectedException \Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenInvalid()
    {
        $this->coreValidator->expects($this->once())->method('validate')
            ->willReturn([ 'error1' => 'plugh fred' ]);

        $this->validator->validate(new Entity([ 'type' => 3 ]));
    }
}
