<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\FormFieldHelper;

/**
 * Defines test cases for FormFieldHelper class.
 */
class FormFieldHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Common\Core\Component\Security\Security')
            ->setMethods([ 'hasExtension' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->FormFieldHelper = new FormFieldHelper($this->container);
    }

    /**
     * Returns a mocked service based on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.security':
                return $this->security;

            default:
                return null;
        }
    }

    /**
     * Tests filterFields when empty name.
     */
    public function testfilterFieldsEmptyName()
    {
        $this->assertEquals([], $this->FormFieldHelper->filterFields(''));
    }

    /**
     * Tests filterFields when valid name.
     */
    public function testfilterFieldsValidName()
    {
        $this->assertEquals([
            [
                'name' => 'subscriptions',
                'title' => _('Lists'),
                'module' => false,
                'available' => [ 'subscriber' ]
            ]
        ], $this->FormFieldHelper->filterFields('subscriber'));
    }
}
