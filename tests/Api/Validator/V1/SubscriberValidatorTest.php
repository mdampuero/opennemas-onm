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

use Api\Validator\V1\SubscriberValidator;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for SubscriberValidator class.
 */
class SubscriberValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->validator = new SubscriberValidator($this->container);
    }

    /**
     * Tests validate when type changes and valid type provided.
     */
    public function testValidateChanges()
    {
        $this->validator->validate(new Entity([ 'type' => 1 ]));
        $this->validator->validate(new Entity([
            'type'  => 1,
            'email' => 'foobar@foo.fubar',
        ]));
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
    public function testValidateWhenInvalid()
    {
        $this->validator->validate(new Entity([ 'type' => 0 ]));
    }

    /**
     * Tests validate when valid type provided.
     */
    public function testValidateWhenValid()
    {
        $this->validator->validate(new Entity([ 'type' => 1 ]));
        $this->validator->validate(new Entity([ 'type' => 2 ]));
    }
}
