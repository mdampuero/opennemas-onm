<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Task\Component\Task;

use Common\Task\Component\Task\Task;

/**
 * Defines test cases for Task class.
 */
class TaskTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->task = new Task([ 'wobble' => 15451 ], 3);
    }

    /**
     * Tests getArgs.
     */
    public function testGetArgs()
    {
        $this->assertEquals([ 'wobble' => 15451 ], $this->task->getArgs());
    }

    /**
     * Tests getPriority.
     */
    public function testGetPriority()
    {
        $this->assertEquals(3, $this->task->getPriority());
    }
}
