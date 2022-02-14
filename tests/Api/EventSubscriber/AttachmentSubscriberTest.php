<?php

namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\AttachmentSubscriber;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for AttachmentSubscriber class.
 */
class AttachmentSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Api\Helper\Cache\ContentCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteItem' ])
            ->getMock();

        $this->subscriber = new AttachmentSubscriber($this->helper);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(AttachmentSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onAttachmentDelete.
     */
    public function testOnAttachmentDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\AttachmentSubscriber')
            ->setConstructorArgs([ $this->helper ])
            ->setMethods([ 'onAttachmentUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onAttachmentUpdate');

        $subscriber->onAttachmentDelete($this->event);
    }

    /**
     * Tests onAttachmentUpdate.
     */
    public function testOnAttachmentUpdate()
    {
        $item = new Content([ 'content_type_name' => 'attachment' ]);

        $this->event->expects($this->once())->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->once())->method('getArgument')
            ->with('item')->willReturn($item);

        $this->helper->expects($this->at(0))->method('deleteItem')
            ->with($item)->willReturn($this->helper);

        $this->subscriber->onAttachmentUpdate($this->event);
    }
}
