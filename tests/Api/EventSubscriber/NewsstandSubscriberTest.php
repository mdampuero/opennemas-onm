<?php

namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\NewsstandSubscriber;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for NewsstandSubscriber class.
 */
class NewsstandSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Api\Helper\Cache\NewsstandCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteFile', 'deleteItem', 'deleteList' ])
            ->getMock();

        $this->subscriber = new NewsstandSubscriber($this->helper);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(NewsstandSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onNewsstandDelete.
     */
    public function testOnNewsstandDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\NewsstandSubscriber')
            ->setConstructorArgs([ $this->helper ])
            ->setMethods([ 'onNewsstandUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onNewsstandUpdate');

        $subscriber->onNewsstandDelete($this->event);
    }

    /**
     * Tests onNewsstandPatch.
     */
    public function testOnNewsstandPatch()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\NewsstandSubscriber')
            ->setConstructorArgs([ $this->helper ])
            ->setMethods([ 'onNewsstandUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onNewsstandUpdate');

        $subscriber->onNewsstandPatch($this->event);
    }

    /**
     * Tests onNewsstandUpdate.
     */
    public function testOnNewsstandUpdate()
    {
        $item = new Content([ 'content_type_name' => 'kiosko' ]);

        $this->event->expects($this->once())->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->once())->method('getArgument')
            ->with('item')->willReturn($item);

        $this->helper->expects($this->at(0))->method('deleteItem')
            ->with($item)->willReturn($this->helper);
        $this->helper->expects($this->at(1))->method('deleteFile')->with($item);
        $this->helper->expects($this->at(2))->method('deleteList');

        $this->subscriber->onNewsstandUpdate($this->event);
    }

    /**
     * Tests onNewsstandUpdate when contents updated but it is not a newsstand.
     */
    public function testOnNewsstandUpdateWhenNoNewsstands()
    {
        $item = new Content([ 'content_type_name' => 'flob' ]);

        $this->event->expects($this->once())->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->once())->method('getArgument')
            ->with('item')->willReturn($item);

        $this->subscriber->onNewsstandUpdate($this->event);
    }
}
