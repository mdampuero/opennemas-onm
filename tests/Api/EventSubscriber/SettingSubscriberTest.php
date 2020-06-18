<?php

namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\SettingSubscriber;

/**
 * Defines tests cases for SettingSubscriber class.
 */
class SettingSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->helper = $this->getMockBuilder('Api\Helper\Cache\CacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteDynamicCss', 'deleteInstance' ])
            ->getMock();

        $this->subscriber = new SettingSubscriber($this->helper);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray($this->subscriber->getSubscribedEvents());
    }

    /**
     * Tests onSettingUpdate.
     */
    public function testOnSettingUpdate()
    {
        $this->helper->expects($this->at(0))->method('deleteInstance')
            ->willReturn($this->helper);
        $this->helper->expects($this->at(1))->method('deleteDynamicCss');

        $this->subscriber->onSettingUpdate();
    }
}
