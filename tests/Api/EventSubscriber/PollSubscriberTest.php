<?php

namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\PollSubscriber;
use Common\Model\Entity\Content;

/**
 * Defines test cases for PollSubscriber class.
 */
class PollSubscriberTest extends \PHPUnit\Framework\TestCase
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

        $this->subscriber = new PollSubscriber($this->helper);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(PollSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onPollVote.
     */
    public function testOnPollVote()
    {
        $item = new Content();

        $this->event->expects($this->once())->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->once())->method('getArgument')
            ->with('item')->willReturn($item);

        $this->helper->expects($this->once())->method('deleteItem')->with($item);

        $this->subscriber->onPollVote($this->event);
    }
}
