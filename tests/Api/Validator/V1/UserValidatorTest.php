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

use Api\Validator\V1\UserValidator;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for UserValidator class.
 */
class UserValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->validator = new UserValidator($this->container);
    }

    /**
     * Tests validate when type changed and valid type provided.
     */
    public function testValidateWhenValid()
    {
        $this->validator->validate(new Entity([]));
        $this->validator->validate(new Entity([ 'type' => 0 ]));

        $this->addToAssertionCount(1);
    }

    /**
     * Tests validate when there are no changes in type.
     *
     * @expectedException Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenInvalid()
    {
        $this->validator->validate(new Entity([ 'type' => 3 ]));
    }
}
