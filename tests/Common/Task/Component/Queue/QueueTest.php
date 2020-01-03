<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Task\Component\Queue;

use Common\Task\Component\Queue\Queue;
use Common\Task\Component\Task\Task;

/**
 * Defines test cases for Queue class.
 */
class QueueTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->queue = new Queue();
    }

    /**
     * Tests count, push and pop methods for the queue.
     */
    public function testQueue()
    {
        $this->assertEquals($this->queue, $this->queue->push(new Task([ 'xyzzy' ], 3)));
        $this->assertEquals($this->queue, $this->queue->push(new Task([ 'foobar' ], 6)));

        $this->assertEquals(2, $this->queue->count());

        $task = $this->queue->pop();

        $this->assertEquals(3, $task->getPriority());
        $this->assertEquals(1, $this->queue->count());
    }
}
