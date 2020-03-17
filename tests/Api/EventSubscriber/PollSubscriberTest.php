<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\PollSubscriber;
use Common\ORM\Entity\Content;

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

        $this->vh = $this->getMockBuilder('Common\Core\Component\Helper\VarnishHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteContents' ])
            ->getMock();

        $this->subscriber = new PollSubscriber($this->vh);
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


        $this->vh->expects($this->once())->method('deleteContents')->with([ $item ]);

        $this->subscriber->onPollVote($this->event);
    }
}
