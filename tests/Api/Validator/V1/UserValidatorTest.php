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
class UserValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Security' . uniqid())
            ->setMethods([ 'hasPermission' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('core.security')->willReturn($this->security);

        $this->validator = new UserValidator($this->container);
    }

    /**
     * Tests validate when type changed and valid type provided.
     */
    public function testValidateWhenChanges()
    {
        $this->validator->validate(new Entity([ 'type' => 0 ]));
    }

    /**
     * Tests validate when there are no changes in type.
     */
    public function testValidateWhenNoChanges()
    {
        $item = new Entity([ 'type' => 0 ]);
        $item->refresh();

        $this->validator->validate($item);
    }

    /**
     * Tests validate when valid type provided.
     *
     * @expectedException Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenInvalidTypeForNonMasters()
    {
        $this->security->expects($this->once())->method('hasPermission')
            ->willReturn(false);

        $this->validator->validate(new Entity([ 'type' => 2 ]));
    }

    /**
     * Tests validate when valid type provided.
     *
     * @expectedException Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenInvalidTypeForMasters()
    {
        $this->validator->validate(new Entity([ 'type' => 3 ]));
    }

    /**
     * Tests validate when valid type only for master users provided.
     */
    public function testValidateWhenValidTypeForMaster()
    {
        $this->security->expects($this->once())->method('hasPermission')
            ->willReturn(true);

        $this->validator->validate(new Entity([ 'type' => 2 ]));
    }
}
