<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Http;

use Common\Core\Component\Http\BotDetector;

/**
 * Defines test cases for BotDetector class.
 */
class BotDetectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->detector = new BotDetector();
    }

    /**
     * Tests isBot.
     */
    public function testIsBot()
    {
        $request          = $this->getMockBuilder('Request')->getMock();
        $headerBag        = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'get' ])
            ->getMock();
        $request->headers = $headerBag;

        $headerBag->expects($this->at(0))->method('get')->with('User-Agent')
            ->willReturn('Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, '
                . 'like Gecko) Chrome/41.0.2228.0 Safari/537.36');
        $headerBag->expects($this->at(1))->method('get')->with('User-Agent')
            ->willReturn('Mozilla/5.0 (iPhone; CPU iPhone OS 8_1 like Mac OS X) '
                . 'AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B411 '
                . 'Safari/600.1.4 (compatible; YandexBot/3.0; +http://yandex.com/bots)');
        $headerBag->expects($this->at(2))->method('get')->with('User-Agent')
            ->willReturn('Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)');

        $this->assertFalse($this->detector->isBot($request));
        $this->assertTrue($this->detector->isBot($request));
        $this->assertTrue($this->detector->isBot($request, 'bingbot'));
    }
}
