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

use Common\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for ServiceTask class.
 */
class ServiceTaskTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->task = new ServiceTask('bar', 'fred');
    }

    /**
     * Tests getAction.
     */
    public function testGetAction()
    {
        $this->assertEquals('fred', $this->task->getAction());
    }

    /**
     * Tests getService.
     */
    public function testGetService()
    {
        $this->assertEquals('bar', $this->task->getService());
    }
}
