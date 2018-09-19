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

use Common\Core\Component\EventDispatcher\Event;

class EventTest extends \PHPUnit\Framework\TestCase
{
    public function testGetResponse()
    {
        $event = new Event();

        $this->assertEmpty($event->getResponse());
    }

    public function testSetResponse()
    {
        $response = [ 'foo' => 'bar' ];
        $event    = new Event();

        $event->setResponse($response);

        $this->assertEquals($response, $event->getResponse());
    }
}
