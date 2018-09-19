<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Exception;

use Common\Core\Component\Exception\BotDetectedException;

/**
 * Defines test cases for BotDetectedException class.
 */
class BotDetectedExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests methods when the exception is empty.
     */
    public function testExceptionWithNoParameters()
    {
        $exception = new BotDetectedException();

        $this->assertEmpty($exception->getMessage());
        $this->assertEmpty($exception->getRoute());
    }

    /**
     * Tests methods when the exception is empty.
     */
    public function testExceptionWithParameters()
    {
        $exception = new BotDetectedException('corge_route', 'glork garply wubble');

        $this->assertEquals('glork garply wubble', $exception->getMessage());
        $this->assertEquals('corge_route', $exception->getRoute());
    }
}
