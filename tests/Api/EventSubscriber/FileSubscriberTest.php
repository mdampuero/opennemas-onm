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

use Api\EventSubscriber\FileSubscriber;
use Common\ORM\Entity\File;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for FileSubscriber class.
 */
class FileSubscriberTest extends \PHPUnit\Framework\TestCase
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
            ->setMethods([ 'deleteFiles' ])
            ->getMock();

        $this->subscriber = new FileSubscriber($this->vh);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(FileSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onFileDelete.
     */
    public function testOnFileDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\FileSubscriber')
            ->setConstructorArgs([ $this->vh ])
            ->setMethods([ 'onFileUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onFileUpdate');

        $subscriber->onFileDelete($this->event);
    }

    /**
     * Tests onFileUpdate.
     */
    public function testOnFileUpdate()
    {
        $item = new \Attachment();

        $this->event->expects($this->once())->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->once())->method('getArgument')
            ->with('item')->willReturn($item);


        $this->vh->expects($this->once())->method('deleteFiles')->with([ $item ]);

        $this->subscriber->onFileUpdate($this->event);
    }
}
