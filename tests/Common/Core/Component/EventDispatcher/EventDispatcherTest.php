<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\EventDispatcher;

use Common\Core\Component\EventDispatcher\EventDispatcher;

class EventDispatcherTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->sd = $this
            ->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch', 'foo' ])
            ->getMock();

        $this->dispatcher = new EventDispatcher($this->sd);
    }

    public function testDefaultCalls()
    {
        $this->sd->expects($this->once())->method('foo');

        $this->dispatcher->foo();
    }

    public function testDispatch()
    {
        $this->sd->expects($this->once())->method('dispatch');

        $this->dispatcher->dispatch('baz', [ 'quux' => 'norf' ]);
    }
}
