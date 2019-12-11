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

use Common\Task\Component\Worker\WorkerFactory;
use Common\Task\Component\Task\ServiceTask;
use Common\Task\Component\Task\Task;

/**
 * Defines test cases for WorkerFactory class.
 */
class WorkerFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\Component\DependecyInjection\ContainerInterface')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->factory = new WorkerFactory($this->container);
    }

    /**
     * Tests get when the provided task can be executed by a worker.
     */
    public function testGetForKnownTask()
    {
        $this->assertInstanceOf(
            'Common\Task\Component\Worker\ServiceTaskWorker',
            $this->factory->get(new ServiceTask('wibble', 'norf'))
        );
    }

    /**
     * Tests get when the provided task is unknown and can not be executed by
     * any worker.
     *
     * @expectedException Common\Task\Component\Exception\UnknownTaskException
     */
    public function testGetForUnknownTask()
    {
        $this->factory->get(new Task());
    }
}
