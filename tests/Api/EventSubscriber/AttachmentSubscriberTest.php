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

use Api\EventSubscriber\AttachmentSubscriber;
use Common\ORM\Entity\Content;
use Common\ORM\Entity\Instance;

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

        $this->vh = $this->getMockBuilder('Common\Core\Component\Helper\VarnishHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteContents' ])
            ->getMock();

        $this->subscriber = new AttachmentSubscriber($this->vh);
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
            ->setConstructorArgs([ $this->vh ])
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


        $this->vh->expects($this->once())->method('deleteContents')->with([ $item ]);

        $this->subscriber->onAttachmentUpdate($this->event);
    }
}
