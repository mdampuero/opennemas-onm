<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Task\Component\Worker;

use Common\Task\Component\Task\ServiceTask;
use Common\Task\Component\Task\Task;
use Common\Task\Component\Worker\ServiceTaskWorker;

/**
 * Defines test cases for ServiceTaskWorker class.
 */
class ServiceTaskWorkerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependecyInjection\ContainerInterface')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->worker = new ServiceTaskWorker($this->container);
    }

    /**
     * Tests canExecute with executable and unexecutable tasks.
     */
    public function testCanExecute()
    {
        $this->assertFalse($this->worker->canExecute(new Task()));
        $this->assertTrue($this->worker->canExecute(new ServiceTask('bar', 'qux')));
    }

    /**
     * Tests canExecute with executable and unexecutable tasks.
     */
    public function testExecute()
    {
        $service = $this->getMockBuilder('Service')
            ->setMethods([ 'qux' ])
            ->getMock();

        $this->container->expects($this->once())->method('get')
            ->with('bar')->willReturn($service);

        $service->expects($this->once())->method('qux')
            ->with('frog', 27618);

        $this->worker->execute(new ServiceTask('bar', 'qux', [ 'frog', 27618 ]));
    }
}
