<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Task\EventSubscriber\QueueSubscriber;

use Common\Task\EventSubscriber\QueueSubscriber;
use Common\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for QueueSubscriber class.
 */
class QueueSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->queue = $this->getMockBuilder('Common\Task\Component\Queue\Queue')
            ->disableOriginalConstructor()
            ->setMethods([ 'count', 'pop' ])
            ->getMock();

        $this->factory = $this->getMockBuilder('Common\Task\Component\Worker\WorkerFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->worker = $this->getMockBuilder('Common\Task\Component\Worker\ServiceTaskWorker')
            ->disableOriginalConstructor()
            ->setMethods([ 'execute' ])
            ->getMock();

        $this->subscriber = new QueueSubscriber($this->queue, $this->factory);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(QueueSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onKernelTerminate.
     */
    public function testOnKernelTerminate()
    {
        $task = new ServiceTask('foobar', 'flob');

        $this->queue->expects($this->at(0))->method('count')
            ->willReturn(1);
        $this->queue->expects($this->at(0))->method('count')
            ->willReturn(0);
        $this->queue->expects($this->once())->method('pop')
            ->willReturn($task);

        $this->factory->expects($this->once())->method('get')
            ->with($task)->willReturn($this->worker);

        $this->worker->expects($this->once())->method('execute')
            ->with($task);

        $this->subscriber->onKernelTerminate();
    }
}
