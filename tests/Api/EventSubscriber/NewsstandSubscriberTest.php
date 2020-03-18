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

        $this->th = $this->getMockBuilder('Common\Core\Component\Helper\TemplateCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteNewsstands' ])
            ->getMock();

        $this->vh = $this->getMockBuilder('Common\Core\Component\Helper\VarnishHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteNewsstands' ])
            ->getMock();

        $this->subscriber = new NewsstandSubscriber($this->th, $this->vh);
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
            ->setConstructorArgs([ $this->th, $this->vh ])
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
            ->setConstructorArgs([ $this->th, $this->vh ])
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
        $item = new Content([ 'content_type_name' => 'attachment' ]);

        $this->event->expects($this->once())->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->once())->method('getArgument')
            ->with('item')->willReturn($item);


        $this->th->expects($this->once())->method('deleteNewsstands')->with([ $item ]);
        $this->vh->expects($this->once())->method('deleteNewsstands')->with([ $item ]);

        $this->subscriber->onNewsstandUpdate($this->event);
    }
}
