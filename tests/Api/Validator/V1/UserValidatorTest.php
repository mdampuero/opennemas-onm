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
     */
    public function testValidateWhenInvalidTypeForNonMasters()
    {
        $this->validator->validate(new Entity([ 'type' => 0 ]));
        $this->validator->validate(new Entity([ 'type' => 2 ]));
    }

    /**
     * Tests validate when valid type provided.
     *
     * @expectedException Api\Exception\InvalidArgumentException
     */
    public function testValidateWhenInvalidTypeForMasters()
    {
        $this->validator->validate(new Entity([ 'type' => 1 ]));
    }
}
